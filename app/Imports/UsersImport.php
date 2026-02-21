<?php

namespace App\Imports;

use App\Models\Mapel;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {

            foreach ($rows as $row) {

                $user = User::firstOrCreate(
                    ['email' => $row['email']],
                    [
                        'name'     => $row['name'],
                        'password' => Hash::make($row['password']),
                        'role'     => 'guru',
                    ]
                );

                $mapelNames = explode(',', $row['mapel_ids']);
                $mapelIds = [];

                foreach ($mapelNames as $name) {

                    $mapel = Mapel::where('mapel', trim($name))->first();

                    if ($mapel) {
                        $mapelIds[] = $mapel->id;
                    }
                }

                $user->mapel()->sync($mapelIds);
            }
        });
    }
}
