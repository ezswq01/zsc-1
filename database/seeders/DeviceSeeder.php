<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $devices = [
            [
                'device_id' => 'LBBD1',
                'device_type_id' => 1,
                'publish_topic' => 'mcc/bandung/door-1',
                'subscribe_topic' => 'mcc/bandung/door-1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'device_id' => 'LBBES1',
                'device_type_id' => 2,
                'publish_topic' => 'mcc/jakarta/door-2',
                'subscribe_topic' => 'mcc/jakarta/door-2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        Device::insert($devices);
    }
}
