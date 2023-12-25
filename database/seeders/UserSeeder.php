<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name' => "Admin",
            'email' => "admin@admin.com",
            'user_code' => "admin",
            'password' => bcrypt('password'),
        ]);
        $admin->syncRoles(['admin']);

        $observer = User::create([
            'name' => "User",
            'email' => "observer@observer.com",
            'user_code' => "observer",
            'password' => bcrypt('password'),
        ]);
        $observer->syncRoles(['observer']);
    }
}
