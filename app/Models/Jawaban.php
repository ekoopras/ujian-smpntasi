<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $fillable = [
        'peserta_id',
        'soal_id',
        'ujian_id',
        'jawaban',
        'skor'
    ];

    // app/Models/Jawaban.php
    public function soal()
    {
        // Pastikan nama modelnya 'Soal' (sesuai nama tabel soals)
        return $this->belongsTo(Soal::class, 'soal_id');
    }
}
