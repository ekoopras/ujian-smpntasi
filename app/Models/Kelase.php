<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelase extends Model
{
    protected $table = 'kelases';

    // ✅ Daftar kolom yang bisa diisi massal (fillable)
    protected $fillable = [
        'kelas',
        'slug',
    ];
}
