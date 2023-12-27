<?php

namespace Database\Seeders;

use App\Models\AbsentDevice;
use Illuminate\Database\Seeder;

class AbsentDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AbsentDevice::create([
            'absent_device_id' => 'AD1',
            'publish_topic' => 'mcc/jakarta/bank/meeting/door/door-2/pub',
            'subscribe_topic' => 'mcc/jakarta/bank/meeting/door/door-2/sub',
            'branch' => 'jakarta',
            'building' => 'bank',
            'room' => 'meeting',
        ]);
    }
}
