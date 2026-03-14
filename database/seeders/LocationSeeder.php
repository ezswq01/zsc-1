<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['code' => 'as'],
            ['code' => 'demo_unit_1'],
            ['code' => 'demo_unit1'],
            ['code' => 'dev_1'],
            ['code' => 'dev_2'],
            ['code' => 'dev_3'],
            ['code' => 'dev_4'],
            ['code' => 'wsid_b1120874'],
        ];

        foreach ($locations as $location) {
            Location::updateOrCreate(
                ['code' => $location['code']],
                [
                    'company_name' => null,
                    'name'         => null,
                    'address'      => null,
                    'city'         => null,
                    'coordinate'   => null,
                    'is_active'    => true,
                    'last_updated_at' => now(),
                    'last_updated_by' => null,
                ]
            );
        }
    }
}
