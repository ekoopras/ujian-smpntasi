<?php

namespace Database\Seeders;

use App\Models\Kelase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kelas' => 'Kelas 7A', 'slug' => 'kelas-7a'],
            ['kelas' => 'Kelas 7B', 'slug' => 'kelas-7b'],
            ['kelas' => 'Kelas 8A', 'slug' => 'kelas-8a'],
            ['kelas' => 'Kelas 8B', 'slug' => 'kelas-8b'],
        ];
        foreach ($data as $item) {
            Kelase::create($item);
        }
    }
}
