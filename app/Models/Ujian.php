<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Ujian extends Model
{

    protected $table = 'ujians';

    protected $fillable = [
        'mapel_id',
        'bank_soal_id',
        'kode_ujian',
        'durasi_menit',
        'unlock_code',
    ];

    // public function kelase()
    // {
    //     return $this->belongsTo(Kelase::class);
    // }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class);
    }

    public function pesertas()
    {
        return $this->hasMany(Peserta::class);
    }

    public function kelase()
    {
        return $this->belongsToMany(Kelase::class, 'kelas_ujian', 'ujian_id', 'kelase_id');
    }


    // GENERATE KODE UJIAN
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ujian) {
            if (empty($ujian->kode_ujian)) {
                $ujian->kode_ujian = strtoupper(Str::random(6));
            }
        });
    }
}
