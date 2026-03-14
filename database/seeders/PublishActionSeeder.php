<?php

namespace Database\Seeders;

use App\Models\PublishAction;
use Illuminate\Database\Seeder;

class PublishActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $publish_actions = [
            [
                'device_id' => 1,
                'label' => 'Open Door',
                'value' => 'open',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'device_id' => 1,
                'label' => 'Close Door',
                'value' => 'close',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'device_id' => 2,
                'label' => 'Open Door',
                'value' => 'open',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'device_id' => 2,
                'label' => 'Close Door',
                'value' => 'close',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        PublishAction::insert($publish_actions);
    }
}
