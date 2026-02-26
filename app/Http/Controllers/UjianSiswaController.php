<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use App\Models\Kelase;
use App\Models\Nilai;
use App\Models\Peserta;
use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UjianSiswaController extends Controller
{

    // VIEW INDEX
    public function form()
    {
        return view('ujian.index', [
            'kelas' => Kelase::all(),
        ]);
    }

    // VIEW CEK
    public function cek(Request $request)
    {
        // ambil ujian berdasarkan kode dan kelas
        $ujian = Ujian::where('kode_ujian', $request->kode_ujian)
            ->whereHas('kelase', function ($q) use ($request) {
                $q->where('kelases.id', $request->kelase_id);
            })
            ->first();

        // jika tidak ditemukan
        if (!$ujian) {
            return back()
                ->withInput()
                ->with('error', 'Kode ujian tidak ditemukan atau tidak sesuai dengan kelas.');
        }

        $ujian = Ujian::where('kode_ujian', $request->kode_ujian)
            ->where('is_active', true)
            ->first();

        if (!$ujian) {
            return back()->with('error', 'Kode ujian tidak valid / belum aktif');
        }

        $peserta = \DB::table('pesertas')
            ->where('nomor_absen', $request->nomor_absen)
            ->where('kelase_id', $request->kelase_id)
            ->first();

        // B. Jika peserta ditemukan, cek apakah ID tersebut sudah ada di tabel nilais
        if ($peserta) {
            $sudahAdaNilai = \DB::table('nilais')
                ->where('peserta_id', $peserta->id)
                ->exists();

            if ($sudahAdaNilai) {
                return back()
                    ->withInput()
                    ->with('error', 'Akses Ditolak! Anda sudah menyelesaikan ujian ini dan nilai sudah tersimpan.');
            }
        }

        // ambil kelas yang dipilih
        $kelasDipilih = $ujian->kelase()
            ->where('kelases.id', $request->kelase_id)
            ->first();

        return view('ujian.review', [
            'data' => $request->all(),
            'ujian' => $ujian,
            'kelasDipilih' => $kelasDipilih,
        ]);
    }

    // FUNGSI 1: Proses pendaftaran peserta (Hanya dijalankan sekali saat klik 'Mulai')
    public function mulai(Request $request)
    {
        // Cek apakah peserta sudah ada, jika belum buat baru
        $peserta = Peserta::firstOrCreate(
            [
                'nomor_absen' => $request->nomor_absen,
                'ujian_id' => $request->ujian_id,
                'kelase_id' => $request->kelase_id,
            ],
            [
                'nama' => $request->nama,
                'started_at' => now(), // Waktu dikunci di sini
                'is_locked' => false,
            ]
        );

        // Kunci urutan soal jika belum ada
        if (!$peserta->list_soal) {
            $soalIds = Soal::where('bank_soal_id', $request->ujian_id)->pluck('id')->toArray();
            shuffle($soalIds); // Acak murni satu kali
            $peserta->update(['list_soal' => implode(',', $soalIds)]);
        }

        session(['peserta_id' => $peserta->id]);

        // Redirect ke halaman GET agar saat di-refresh waktu tidak reset
        return redirect()->route('ujian.soal', ['id' => $peserta->id]);
    }

    public function soal($id)
    {
        // PROTEKSI: Cek apakah ID di URL sama dengan ID di Session login
        // Cek apakah peserta_id di session cocok dengan ID di URL
        if (session('peserta_id') != $id) {
            // Alihkan ke halaman 404 Standard Laravel
            abort(404);
        }

        // Ambil data peserta beserta relasi ujian dan jawaban yang sudah ada
        $peserta = Peserta::with(['ujian', 'jawaban'])->findOrFail($id);

        $startTime = $peserta->started_at ?? now();
        $waktuSelesai = $startTime->addMinutes($peserta->ujian->durasi_menit)->timestamp;

        // CEK AMAN: Jika list_soal kosong (karena peserta lama atau gagal generate), buatkan sekarang
        if (empty($peserta->list_soal)) {
            $soalIds = Soal::where('bank_soal_id', $peserta->ujian->bank_soal_id)
                ->pluck('id')
                ->toArray();
            shuffle($soalIds);
            $peserta->list_soal = implode(',', $soalIds);
            $peserta->save(); // Simpan ke database agar refresh berikutnya aman
        }

        // Ambil string ID soal
        $urutanIds = explode(',', $peserta->list_soal);

        // Ambil data soal sesuai urutan yang tersimpan
        $soals = Soal::whereIn('id', $urutanIds)
            ->orderByRaw("FIELD(id, " . $peserta->list_soal . ")")
            ->get();

        // Logika Matching (Kunci urutan kanan pakai ID soal agar tidak berubah)
        $soals->each(function ($soal) use ($peserta) {
            if ($soal->tipe_soal === 'matching') {
                $soal->kananList = collect($soal->matching)
                    ->pluck('kanan')->unique()
                    ->shuffle($soal->id) // Seed pakai ID soal bersifat permanen
                    ->values()->toArray();
            }
        });

        // 3. Ambil jawaban yang sudah tersimpan di DB (untuk ditampilkan kembali saat refresh)
        // Kita buat format: [soal_id => jawaban_json]
        $jawabanTerarsip = $peserta->jawaban->pluck('jawaban', 'soal_id')
            ->map(function ($item) {
                return json_decode($item, true);
            })->toArray();


        return view('ujian.soal', compact('peserta', 'soals', 'waktuSelesai', 'jawabanTerarsip'));
    }

    public function autosave(Request $request)
    {
        // PROTEKSI: Pastikan peserta_id yang dikirim lewat AJAX sama dengan session
        if (session('peserta_id') != $request->peserta_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Simpan atau update jawaban
            // Kita gunakan updateOrInsert agar ringan
            DB::table('jawabans')->updateOrInsert(
                [
                    'peserta_id' => $request->peserta_id,
                    'soal_id'    => $request->soal_id,
                ],
                [
                    'jawaban'    => json_encode($request->jawaban),
                    'skor'       => 0, // Skor biarkan 0 dulu, dihitung saat submit akhir atau nanti
                    'updated_at' => now(),
                ]
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }


    // SUBMIT JAWABAN
    public function submit(Request $request)
    {
        // 1. Validasi awal: Mencegah Double Submit
        $sudahAdaNilai = Nilai::where('peserta_id', $request->peserta_id)->exists();
        if ($sudahAdaNilai) {
            return redirect()->route('ujian.selesai');
        }

        // 2. Mulai Transaksi Database
        DB::beginTransaction();

        try {
            $totalSkor = 0;
            $dataJawaban = [];
            $jawabanInput = $request->jawaban ?? [];

            // 3. Optimasi: Ambil semua soal yang dijawab dalam satu query
            $daftarSoalId = array_keys($jawabanInput);
            $semuaSoal = Soal::whereIn('id', $daftarSoalId)->get()->keyBy('id');

            foreach ($jawabanInput as $soal_id => $jawabanSiswa) {
                $soal = $semuaSoal->get($soal_id);

                if (!$soal) continue;

                // Hitung skor menggunakan logic yang sudah Anda punya
                $skor = $this->cekJawaban($soal, $jawabanSiswa);
                $totalSkor += $skor;

                // Tampung data ke array (untuk Bulk Insert)
                $dataJawaban[] = [
                    'peserta_id' => $request->peserta_id,
                    'soal_id'    => $soal_id,
                    'jawaban'    => json_encode($jawabanSiswa),
                    'skor'       => $skor,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // 4. Eksekusi simpan massal (Jauh lebih cepat daripada loop create)
            if (!empty($dataJawaban)) {
                Jawaban::insert($dataJawaban);
            }

            // 5. Simpan Nilai Akhir
            Nilai::create([
                'peserta_id' => $request->peserta_id,
                'total_skor' => $totalSkor,
            ]);

            DB::commit();
            return redirect()->route('ujian.selesai');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal submit ujian: " . $e->getMessage());

            return back()->with('error', 'Terjadi gangguan koneksi, silakan coba submit kembali.');
        }
    }

    // UJIAN SELESAI
    public function selesai()
    {
        return view('ujian.selesai');
    }

    // CEK JAWABAN
    protected function cekJawaban(Soal $soal, $jawaban)
    {
        $skor = 0;

        if ($soal->tipe_soal === 'multiple_choice') {
            foreach ($soal->multiple_choice ?? [] as $opsi) {
                if (
                    in_array($opsi['opsi'], $jawaban ?? []) &&
                    ($opsi['skor'] ?? 0) > 0
                ) {
                    $skor += $opsi['skor'];
                }
            }
        }

        if ($soal->tipe_soal === 'matching') {
            foreach ($soal->matching ?? [] as $i => $match) {
                if (
                    isset($jawaban[$i]) &&
                    $jawaban[$i] === $match['kanan']
                ) {
                    $skor += $match['matching_skor'] ?? 0;
                }
            }
        }

        return $skor;
    }

    // function finish
    public function finish($id)
    {
        // 1. Validasi Kepemilikan (Sama seperti fungsi soal)
        if (session('peserta_id') != $id) {
            return redirect()->route('ujian.index');
        }

        $peserta = Peserta::with(['ujian', 'jawaban.soal'])->findOrFail($id);

        // 2. Hitung Skor Otomatis (Jika soalnya Multiple Choice/Matching)
        $totalSkor = 0;
        foreach ($peserta->jawaban as $jawab) {
            $skorSoal = 0;
            $soal = $jawab->soal;
            $jawabanSiswa = json_decode($jawab->jawaban, true);

            if ($soal->tipe_soal == 'multiple_choice') {
                // Cek apakah jawaban string sama
                if ($soal->jawaban_benar == $jawabanSiswa) {
                    $skorSoal = $soal->skor_poin; // Misal 2 atau 5 poin
                }
            } elseif ($soal->tipe_soal == 'matching') {
                // Hitung poin per baris yang benar
                $benar = 0;
                foreach ($soal->matching as $index => $item) {
                    if (isset($jawabanSiswa[$index]) && $jawabanSiswa[$index] == $item['kanan']) {
                        $benar++;
                    }
                }
                // Poin proporsional: (jumlah benar / total baris) * poin soal
                $totalBaris = count($soal->matching);
                $skorSoal = ($totalBaris > 0) ? ($benar / $totalBaris) * $soal->skor_poin : 0;
            }

            // Update skor di tabel jawabans
            $jawab->update(['skor' => $skorSoal]);
            $totalSkor += $skorSoal;
        }

        // 3. Update Status Peserta (Kunci agar tidak bisa masuk lagi)
        $peserta->update([
            'skor_total' => $totalSkor,
            'finished_at' => now(),
            'is_status' => 'selesai' // Gunakan flag ini untuk proteksi tambahan
        ]);

        // 4. Hapus Session (Agar tidak bisa tembak URL lagi)
        session()->forget('peserta_id');

        return view('ujian.finish', compact('peserta', 'totalSkor'));
    }

    // LOCK KEAMANAN
    public function lock(Request $request)
    {
        Peserta::where('id', $request->peserta_id)->update([
            'is_locked' => true,
            'tab_violation' => DB::raw('tab_violation + 1'),
        ]);

        return response()->json(['locked' => true]);
    }

    // UNLOCK KEAMANAN
    public function unlock(Request $request)
    {
        // Gunakan find saja agar tidak melempar error 404 berantakan jika ID bermasalah
        $peserta = Peserta::with('ujian')->find($request->peserta_id);

        if (!$peserta) {
            return response()->json(['success' => false, 'message' => 'Peserta tidak ditemukan'], 404);
        }

        // 1. Gunakan trim() untuk membuang spasi tak sengaja di awal/akhir
        // 2. Gunakan (string) casting agar perbandingan kode '001' vs 1 tetap valid
        $inputCode = trim((string) $request->code);
        $dbCode = trim((string) $peserta->ujian->unlock_code);

        if ($inputCode === $dbCode) {
            $peserta->update(['is_locked' => false]);

            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kode salah'
        ], 403);
    }
}
