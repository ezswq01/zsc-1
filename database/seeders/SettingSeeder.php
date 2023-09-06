<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'app_name' => 'MCC IOT',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('status_type_widgets')->insert([
            'setting_id' => 1,
            'status_type_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('status_type_widgets')->insert([
            'setting_id' => 1,
            'status_type_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
