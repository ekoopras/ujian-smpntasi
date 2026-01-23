<?php

namespace App\Http\Controllers;

use App\Models\Jawaban;
use App\Models\Kelase;
use App\Models\Nilai;
use App\Models\Peserta;
use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Http\Request;

class UjianSiswaController extends Controller
{
    public function index()
    {
        return view('ujian.index', [
            'kelas' => Kelase::all()
        ]);
    }

    public function start(Request $request)
    {
        $ujian = Ujian::where('kode_ujian', $request->token)->firstOrFail();

        $peserta = Peserta::create([
            'nama' => $request->nama,
            'nis' => $request->nis,
            'kelase_id' => $request->kelase_id,
            'ujian_id' => $ujian->id,
        ]);

        $soals = Soal::where('bank_soal_id', $ujian->bank_soal_id)->get();

        return view('ujian.soal', compact('ujian', 'soals', 'peserta'));
    }

    public function submit(Request $request)
    {
        $total = 0;

        foreach ($request->jawaban as $soalId => $jawaban) {
            $soal = Soal::find($soalId);
            $skor = $soal->{'skor_' . $jawaban};

            Jawaban::create([
                'peserta_id' => $request->peserta_id,
                'soal_id' => $soalId,
                'jawaban' => $jawaban,
                'skor' => $skor,
            ]);

            $total += $skor;
        }

        Nilai::create([
            'peserta_id' => $request->peserta_id,
            'total_skor' => $total,
        ]);

        return redirect('/ujian')->with('success', 'Ujian selesai');
    }
}
