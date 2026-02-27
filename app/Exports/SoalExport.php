<?php

namespace App\Exports;

use App\Models\Soal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SoalExport implements FromCollection, WithHeadings
{
    protected int $bankSoalId;

    public function __construct(int $bankSoalId)
    {
        $this->bankSoalId = $bankSoalId;
    }

    public function headings(): array
    {
        return [
            'soal',
            'tipe_soal',
            'opsi',
            'jawaban',
            'kiri',
            'kanan',
            'skor',
        ];
    }

    public function collection()
    {
        $rows = collect();

        $soals = Soal::where('bank_soal_id', $this->bankSoalId)->get();

        foreach ($soals as $soal) {

            // Baris utama (judul soal)
            $rows->push([
                'soal' => $soal->soal,
                'tipe_soal' => $soal->tipe_soal,
                'opsi' => '',
                'jawaban' => '',
                'kiri' => '',
                'kanan' => '',
                'skor' => '',
            ]);

            // MULTIPLE CHOICE
            if ($soal->tipe_soal === 'multiple_choice') {
                foreach ($soal->multiple_choice ?? [] as $mc) {
                    $rows->push([
                        'soal' => '',
                        'tipe_soal' => '',
                        'opsi' => $mc['opsi'] ?? '',
                        'jawaban' => $mc['jawaban'] ?? '',
                        'kiri' => '',
                        'kanan' => '',
                        'skor' => $mc['skor'] ?? 0,
                    ]);
                }
            }

            // MATCHING
            if ($soal->tipe_soal === 'matching') {
                foreach ($soal->matching ?? [] as $match) {
                    $rows->push([
                        'soal' => '',
                        'tipe_soal' => '',
                        'opsi' => '',
                        'jawaban' => '',
                        'kiri' => $match['kiri'] ?? '',
                        'kanan' => $match['kanan'] ?? '',
                        'skor' => $match['matching_skor'] ?? 0,

                    ]);
                }
            }
        }

        return $rows;
    }
}
