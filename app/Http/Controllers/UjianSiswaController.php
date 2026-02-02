<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use App\Models\Kelase;
use App\Models\Nilai;
use App\Models\Peserta;
use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UjianSiswaController extends Controller
{

    public function form()
    {
        return view('ujian.index', [
            'kelas' => Kelase::all(),
        ]);
    }

    public function cek(Request $request)
    {
        $ujian = Ujian::where('kode_ujian', $request->kode_ujian)
            ->where('kelase_id', $request->kelase_id)
            ->firstOrFail();

        return view('ujian.review', [
            'data' => $request->all(),
            'ujian' => $ujian,
        ]);
    }

    public function mulai(Request $request)
    {
        $peserta = Peserta::create([
            'nama' => $request->nama,
            'nis' => $request->nis,
            'kelase_id' => $request->kelase_id,
            'ujian_id' => $request->ujian_id,
        ]);

        $soals = Soal::where('bank_soal_id', $peserta->ujian->bank_soal_id)->get();

        // ambil durasi ujian (menit)
        $durasiMenit = $peserta->ujian->durasi_menit;

        return view('ujian.soal', compact('peserta', 'soals', 'durasiMenit'));
    }

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

        return redirect('/ujian/selesai');
    }


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


    // protected function cekJawaban(Soal $soal, $jawaban)
    // {
    //     $skor = 0;

    //     // ===== MULTIPLE CHOICE (BERDASARKAN NILAI OPSI) =====
    //     if ($soal->tipe_soal === 'multiple_choice') {

    //         if (!is_string($jawaban)) {
    //             return 0;
    //         }

    //         foreach ($soal->multiple_choice as $opsi) {
    //             if ($opsi['opsi'] === $jawaban) {
    //                 return (int) ($opsi['nilai'] ?? 0);
    //             }
    //         }

    //         return 0;
    //     }

    //     // ===== MATCHING =====
    //     if ($soal->tipe_soal === 'matching') {

    //         if (!is_array($jawaban)) {
    //             return 0;
    //         }

    //         foreach ($soal->matching as $i => $match) {
    //             if (
    //                 isset($jawaban[$i]) &&
    //                 $jawaban[$i] === $match['kanan']
    //             ) {
    //                 $skor += $match['matching_skor'] ?? 0;
    //             }
    //         }
    //     }

    //     return $skor;
    // }
}
