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
            DeviceTypeSeeder::class,
            StatusTypeSeeder::class,
            DeviceSeeder::class,
            PublishActionSeeder::class,
            SubscribeExpressionSeeder::class,
            DeviceLogSeeder::class,
            DeviceStatusSeeder::class
        ]);
    }
}
