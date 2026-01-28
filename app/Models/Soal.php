<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $fillable = [
        'bank_soal_id',
        'soal',
        'gambar',
        'tipe_soal',
        'multiple_choice',
        'matching',
    ];

    protected $casts = [
        'multiple_choice' => 'array',
        'matching' => 'array',
    ];


    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class);
    }
}
