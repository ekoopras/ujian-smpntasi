<?php

namespace App\Imports;

use App\Models\Soal;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MultipleChoiceImport implements ToCollection, WithHeadingRow
{
    protected int $bankSoalId;

    public function __construct(int $bankSoalId)
    {
        $this->bankSoalId = $bankSoalId;
    }

    public function collection(Collection $rows)
    {
        $currentSoal = null;
        $mc = [];
        $matching = [];

        foreach ($rows as $row) {

            // BARIS SOAL BARU
            if (! empty($row['soal'])) {

                // simpan soal sebelumnya
                if ($currentSoal) {
                    $this->saveSoal($currentSoal, $mc, $matching);
                }

                // reset state
                $currentSoal = [
                    'soal' => $row['soal'],
                    'tipe_soal' => $row['tipe_soal'],
                ];

                $mc = [];
                $matching = [];
            }

            // ===== MULTIPLE CHOICE =====
            if (
                ($currentSoal['tipe_soal'] ?? null) === 'multiple_choice'
                && ! empty($row['opsi'])
            ) {
                $mc[] = [
                    'opsi' => strtoupper($row['opsi']),
                    'jawaban' => $row['jawaban'],
                    'skor' => (int) $row['skor'],
                ];
            }

            // ===== MATCHING =====
            if (
                ($currentSoal['tipe_soal'] ?? null) === 'matching'
                && ! empty($row['kiri'])
            ) {
                $matching[] = [
                    'kiri' => $row['kiri'],
                    'kanan' => $row['kanan'],
                    'matching_skor' => (int) $row['skor'],
                ];
            }
        }

        // simpan soal terakhir
        if ($currentSoal) {
            $this->saveSoal($currentSoal, $mc, $matching);
        }
    }

    protected function saveSoal(array $soal, array $mc, array $matching): void
    {
        Soal::create([
            'bank_soal_id' => $this->bankSoalId,
            'soal' => $soal['soal'],
            'tipe_soal' => $soal['tipe_soal'],
            'multiple_choice' => $soal['tipe_soal'] === 'multiple_choice' ? $mc : [],
            'matching' => $soal['tipe_soal'] === 'matching' ? $matching : [],
        ]);
    }
}
