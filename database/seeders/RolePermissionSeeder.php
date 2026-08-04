<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [

            'Administrator',

            'Maintenance Manager',

            'Maintenance Supervisor',

            'Maintenance Technician',

            'Consumer'

        ];

        foreach ($roles as $role) {

            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web'
            ]);

        }
    }
}
