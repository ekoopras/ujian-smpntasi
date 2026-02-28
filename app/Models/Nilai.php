<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $fillable = [
        'peserta_id',
        'total_skor',
        'ujian_id',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }
}
