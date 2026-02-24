<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $fillable = [
        'nama',
        'nomor_absen',
        'kelase_id',
        'ujian_id'
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function nilai()
    {
        return $this->hasOne(Nilai::class);
    }

    public function kelase()
    {
        return $this->belongsTo(Kelase::class);
    }
}
