<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(

            [
                'email' => 'admin@swd.gov.ph',
            ],

            [
                'employee_id' => 'SWD-ADMIN-001',

                'first_name' => 'System',

                'middle_name' => null,

                'last_name' => 'Administrator',

                'suffix' => null,

                'phone' => '09123456789',

                'department_id' => 1,

                'position_id' => 1,

                'password' => Hash::make('Admin@12345'),

                'is_active' => true,
            ]
        );

        $admin->assignRole('Administrator');
    }
}
