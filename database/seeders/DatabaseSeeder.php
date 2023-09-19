<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ProvinceSeeder::class,
            RegencySeeder::class,
            SubDistrictSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            DeviceTypeSeeder::class,
            StatusTypeSeeder::class,
            SettingSeeder::class,
            DeviceSeeder::class,
            PublishActionSeeder::class,
            SubscribeExpressionSeeder::class,
            DeviceLogSeeder::class,
            DeviceStatusSeeder::class
        ]);
    }
}
