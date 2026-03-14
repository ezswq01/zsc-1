<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StatusType;

class StatusTypeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define which status types belong to which category
        $categories = [
            'critical' => [
                'ALARM BUZZER ACTIVE ALERT',
                'HIGH VIBRATION ALERT',
                'MOTION SENSOR ALERT'
            ],
            'warning' => [
                'BAD CAMERA IMAGE',
                'DISABLED BUZZER WARNING',
                'BUZZER FAILURE WARNING',
                'CAMERA FAILURE WARNING',
                'DOOR OPEN WARNING'
            ],
            'info' => [
                'DOOR OPEN ALERT (NON OPS)',
                'MACHINE MOVEMENT ALERT',
                'PEER LOW BATTERY WARNING',
                'HIGH TEMPERATURE ALERT',
                'POWER DOWN WARNING'
            ]
        ];

        foreach ($categories as $category => $types) {
            StatusType::whereIn('name', $types)->update(['category' => $category]);
        }

        $this->command->info('Status Types have been successfully categorized!');
    }
}