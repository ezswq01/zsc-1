<?php

namespace Database\Seeders;

use App\Models\SubscribeExpression;
use Illuminate\Database\Seeder;

class SubscribeExpressionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subscribe_expressions = [
            [
                'device_id' => 1,
                'status_type_id' => 1,
                'expression' => "{{value}} == 'request'",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'device_id' => 2,
                'status_type_id' => 1,
                'expression' => "{{value}} == 'request'",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ];

        SubscribeExpression::insert($subscribe_expressions);
    }
}
