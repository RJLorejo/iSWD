<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [

            ['position_name' => 'System Administrator'],

            ['position_name' => 'Maintenance Manager'],

            ['position_name' => 'Maintenance Supervisor'],

            ['position_name' => 'Maintenance Technician'],

            ['position_name' => 'Customer Service Representative'],

        ];

        foreach ($positions as $position) {

            Position::updateOrCreate(

                [
                    'position_name' => $position['position_name']
                ],

                $position

            );
        }
    }
}
