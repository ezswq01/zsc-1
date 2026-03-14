<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class LocationPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Safe to re-run — uses firstOrCreate so no duplicates.
     */
    public function run(): void
    {
        $permissions = [
            'locations-create',
            'locations-read',
            'locations-update',
            'locations-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        $this->command->info('Location permissions seeded successfully.');
    }
}
