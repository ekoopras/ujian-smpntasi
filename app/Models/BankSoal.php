<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankSoal extends Model
{
    protected $fillable = [
        'mapel_id',
        'kelas',
        'semester',
    ];

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function soals()
    {
        return $this->hasMany(Soal::class);
    }

    public function ujians()
    {
        return $this->belongsToMany(Ujian::class);
    }
}
