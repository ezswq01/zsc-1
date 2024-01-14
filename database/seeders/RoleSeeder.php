<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create role
        $admin = Role::create(['name' => 'admin']);
        $observer = Role::create(['name' => 'observer']);
        $employee = Role::create(['name' => 'employee']);

        // sync permissions
        $admin->syncPermissions(['admin-access']);

        $observer->syncPermissions([
            'users-create',
            'users-read',
            'device-logs-read'
        ]);
    }
}
