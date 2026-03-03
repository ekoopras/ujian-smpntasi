<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NilaiExport implements FromCollection, WithHeadings
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
        return $this->query
            ->join('pesertas', 'nilais.peserta_id', '=', 'pesertas.id') // Join ke tabel peserta
            ->orderBy('pesertas.nomor_absen', 'asc') // Urutkan berdasarkan absen
            ->select('nilais.*') // Pastikan hanya mengambil kolom dari tabel nilai
            ->with(['peserta.kelase', 'ujian.mapel'])
            ->get()
            ->map(function ($nilai) {
                return [
                    'Nama'       => $nilai->peserta->nama,
                    'NomorAbsen' => $nilai->peserta->nomor_absen,
                    'Kelas'      => $nilai->peserta->kelase->kelas ?? '-',
                    'Mapel'      => $nilai->ujian->mapel->mapel ?? '-',
                    'Nilai'      => $nilai->total_skor,
                ];
            });
    }

    public function headings(): array
    {
        return ['Nama', 'Nomor Absen', 'Kelas', 'Mapel', 'Nilai'];
    }
}
