<?php

namespace Database\Seeders;

use App\Models\DeviceLog;
use Illuminate\Database\Seeder;

class DeviceLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $device_logs = [
            [
                'device_id' => 1,
                'value' => 'open',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        DeviceLog::insert($device_logs);
    }
}
