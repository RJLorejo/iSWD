<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('technician_duties', function (Blueprint $table) {
            $table->unique(
                ['technician_id', 'duty_date'],
                'technician_duty_date_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('technician_duties', function (Blueprint $table) {
            $table->dropUnique('technician_duty_date_unique');
        });
    }
};
