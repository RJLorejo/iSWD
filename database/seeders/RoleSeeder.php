<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'Administrator']);
        Role::firstOrCreate(['name' => 'Maintenance Manager']);
        Role::firstOrCreate(['name' => 'Customer Service']);
        Role::firstOrCreate(['name' => 'Maintenance Technician']);
        Role::firstOrCreate(['name' => 'Consumer']);
    }
}
