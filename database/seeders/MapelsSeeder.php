<?php

namespace Database\Seeders;

use App\Models\Mapel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MapelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['mapel' => 'IPA', 'slug' => 'ipa'],
            ['mapel' => 'BIN', 'slug' => 'bin'],
        ];
        foreach ($data as $item) {
            Mapel::create($item);
        }
    }
}
