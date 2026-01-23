<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class NilaiExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->get()->map(function ($nilai) {
            return [
                'Nama'  => $nilai->peserta->nama,
                'NIS'   => $nilai->peserta->nis,
                'Kelas' => $nilai->peserta->kelase->kelas,
                'Mapel' => $nilai->peserta->ujian->mapel->mapel,
                'Nilai' => $nilai->total_skor,
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama', 'NIS', 'Kelas', 'Mapel', 'Nilai'];
    }
}
