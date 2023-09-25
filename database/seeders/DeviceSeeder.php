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
                'publish_topic' => 'mcc/bandung/bank/teller/door/door-1/pub',
                'subscribe_topic' => 'mcc/bandung/bank/teller/door/door-1/sub',
                'branch' => 'bandung',
                'building' => 'bank',
                'room' => 'teller',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'device_id' => 'LBBES1',
                'device_type_id' => 2,
                'publish_topic' => 'mcc/jakarta/bank/server/door/door-2/pub',
                'subscribe_topic' => 'mcc/jakarta/bank/server/door/door-2/sub',
                'branch' => 'jakarta',
                'building' => 'bank',
                'room' => 'server',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        Device::insert($devices);
    }
}
