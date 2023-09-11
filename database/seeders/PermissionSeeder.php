<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arr = [];

        // system
        array_push(
            $arr,
            [
                'guard_name' => 'web',
                'name' => 'admin-access',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'systems-control',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'contents-control',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // permissions
        array_push(
            $arr,
            [
                'guard_name' => 'web',
                'name' => 'permissions-create',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'permissions-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'permissions-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'permissions-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
        );

        // roles
        array_push(
            $arr,
            [
                'guard_name' => 'web',
                'name' => 'roles-create',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'roles-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'roles-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'roles-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
        );

        // users
        array_push(
            $arr,
            [
                'guard_name' => 'web',
                'name' => 'users-create',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'users-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'users-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'users-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
        );

        // status types
        array_push(
            $arr,
            [
                'guard_name' => 'web',
                'name' => 'status-types-create',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'status-types-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'status-types-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'status-types-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
        );

        // device types
        array_push(
            $arr,
            [
                'guard_name' => 'web',
                'name' => 'device-types-create',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'device-types-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'device-types-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'device-types-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
        );

        // devices
        array_push(
            $arr,
            [
                'guard_name' => 'web',
                'name' => 'devices-create',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'devices-read',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'devices-update',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'guard_name' => 'web',
                'name' => 'devices-delete',
                'created_at' => now(),
                'updated_at' => now()
            ],
        );

        // device logs
        array_push(
            $arr,
            [
                'guard_name' => 'web',
                'name' => 'device-logs-read',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        DB::table('permissions')->insert($arr);
    }
}
