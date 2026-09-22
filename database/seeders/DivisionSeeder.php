<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        Division::updateOrCreate(
            ['name' => 'Engineering Operation'],
            [
                'description' => 'Handles engineering-related water service concerns and field operations.',
                'is_active' => true,
            ]
        );

        Division::updateOrCreate(
            ['name' => 'Commercial Services'],
            [
                'description' => 'Handles commercial and consumer account-related concerns.',
                'is_active' => true,
            ]
        );
    }
}
