<?php

namespace Database\Seeders;

use App\Models\AbsentLastLog;
use App\Models\AbsentLog;
use App\Models\AbsentReceivedLog;
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
                'value' => 'request',
                'type' => 'subscribe',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'device_id' => 2,
                'value' => 'request',
                'type' => 'subscribe',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        DeviceLog::insert($device_logs);

        $absent_logs = [
            [
                'absent_device_id' => 1,
                'value' => 'observer',
                'status' => 'Request Open',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $absent_last_logs = [
            [
                'absent_log_id' => 1,
                'absent_device_id' => 1,
                'value' => 'observer',
                'status' => 'Request Open',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $absent_received_logs = [
            [
                'absent_log_id' => 1,
                'absent_device_id' => 1,
                'value' => 'observer',
                'status' => 'Request Open',
                'marked_as_read' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        AbsentLog::insert($absent_logs);
        AbsentLastLog::insert($absent_last_logs);
        AbsentReceivedLog::insert($absent_received_logs);
    }
}
