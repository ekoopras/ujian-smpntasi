<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $fillable = [
        'nama',
        'nomor_absen',
        'kelase_id',
        'ujian_id',
        'started_at',
        'list_soal',
        'is_locked',
        'tab_violation',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'is_locked' => 'boolean',
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
    public function jawaban()
    {
        return $this->hasMany(Jawaban::class, 'peserta_id');
    }
}
