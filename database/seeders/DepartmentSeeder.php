<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [

            [
                'department_code' => 'ADMIN',
                'department_name' => 'Administration',
                'description' => 'System Administration',
                'is_active' => true,
            ],

            [
                'department_code' => 'MAINT',
                'department_name' => 'Maintenance Department',
                'description' => 'Maintenance Operations',
                'is_active' => true,
            ],

            [
                'department_code' => 'FIN',
                'department_name' => 'Finance',
                'description' => 'Finance Department',
                'is_active' => true,
            ],

            [
                'department_code' => 'CS',
                'department_name' => 'Customer Service',
                'description' => 'Customer Service Department',
                'is_active' => true,
            ],

        ];

        foreach ($departments as $department) {

            Department::updateOrCreate(

                [
                    'department_code' => $department['department_code']
                ],

                $department

            );
        }
    }
}
