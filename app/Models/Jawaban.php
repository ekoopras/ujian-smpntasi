<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $fillable = [
        'peserta_id',
        'soal_id',
        'jawaban',
        'skor'
    ];
}
