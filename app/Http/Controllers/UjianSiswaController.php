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

class UjianSiswaController extends Controller
{

    //view
    public function form()
    {
        return view('ujian.index', [
            'kelas' => Kelase::all(),
        ]);
    }

    //cek-peserta-ujian
    public function cek(Request $request)
    {
        $ujian = Ujian::where('kode_ujian', $request->kode_ujian)
            ->where('kelase_id', $request->kelase_id)
            ->firstOrFail();

        // 🔎 Cek apakah siswa sudah pernah ikut ujian ini
        $sudahIkut = Peserta::where('nis', $request->nis)
            ->where('ujian_id', $ujian->id)
            ->exists();

        if ($sudahIkut) {
            return redirect()->route('ujian.sudah')
                ->with('error', 'Anda sudah mengikuti ujian mapel ini.');
        }

        return view('ujian.review', [
            'data' => $request->all(),
            'ujian' => $ujian,
        ]);
    }


    //mulai ujian
    public function mulai(Request $request)
    {
        $peserta = Peserta::create([
            'nama' => $request->nama,
            'nis' => $request->nis,
            'kelase_id' => $request->kelase_id,
            'ujian_id' => $request->ujian_id,
            'started_at' => now(),
            'is_locked' => false,
        ]);


        // 🔥 ACak urutan SOAL SAJA
        $soals = Soal::where('bank_soal_id', $peserta->ujian->bank_soal_id)
            ->get()
            ->shuffle($peserta->id);

        // 🎯 khusus MATCHING (kolom kanan diacak)
        $soals->each(function ($soal) use ($peserta) {
            if ($soal->tipe_soal === 'matching') {
                $soal->kananList = collect($soal->matching)
                    ->pluck('kanan')
                    ->unique()
                    ->shuffle($peserta->id)
                    ->values()
                    ->toArray();
            }
        });

        $durasiMenit = $peserta->ujian->durasi_menit;

        return view('ujian.soal', compact('peserta', 'soals', 'durasiMenit'));
    }


    //submit jawaban
    public function submit(Request $request)
    {
        if (Nilai::where('peserta_id', $request->peserta_id)->exists()) {
            return redirect('/ujian/selesai');
        }

        $total = 0;

        foreach ($request->jawaban ?? [] as $soal_id => $jawaban) {

            $soal = Soal::find($soal_id);
            if (!$soal) {
                continue;
            }

            $skor = $this->cekJawaban($soal, $jawaban);

            Jawaban::create([
                'peserta_id' => $request->peserta_id,
                'soal_id' => $soal_id,
                'jawaban' => json_encode($jawaban),
                'skor' => $skor,
            ]);

            $total += $skor;
        }

        Nilai::create([
            'peserta_id' => $request->peserta_id,
            'total_skor' => $total,
        ]);

        return redirect('/ujian-selesai');
    }

    //cek jawaban
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

    //lock keamanan
    public function lock(Request $request)
    {
        Peserta::where('id', $request->peserta_id)->update([
            'is_locked' => true,
            'tab_violation' => DB::raw('tab_violation + 1'),
        ]);

        return response()->json(['locked' => true]);
    }

    //unlock keamanan
    public function unlock(Request $request)
    {
        $peserta = Peserta::findOrFail($request->peserta_id);

        if ($request->code === $peserta->ujian->unlock_code) {
            $peserta->update(['is_locked' => false]);

            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kode salah'
        ], 403);
    }
}
