<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name'=> "Admin",
            'email'=> "admin@admin.com",
            'password'=> bcrypt('password'),
            'role'=> "admin",
        ]);

        DB::table('users')->insert([
            'name'=> "User",
            'email'=> "observer@observer.com",
            'password'=> bcrypt('password'),
            'role'=> "observer",
        ]);
    }
}
