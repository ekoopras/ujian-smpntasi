<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $fillable = [
        'ujian_id',
        'soal',
        'gambar',
        'a',
        'skor_a',
        'b',
        'skor_b',
        'c',
        'skor_c',
        'd',
        'skor_d',
    ];

    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class);
    }
}
