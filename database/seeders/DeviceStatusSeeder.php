<?php

namespace Database\Seeders;

use App\Models\DeviceStatus;
use Illuminate\Database\Seeder;

class DeviceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $device_status = [
            [
                'device_id' => 1,
                'status_type_id' => 1,
                'device_log_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'device_id' => 2,
                'status_type_id' => 1,
                'device_log_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ];

        DeviceStatus::insert($device_status);
    }
}
