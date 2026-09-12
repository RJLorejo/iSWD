<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('technician_duties', function (Blueprint $table) {

            $table->id();

            $table->foreignId('technician_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('duty_date');

            $table->time('start_time')->nullable();

            $table->time('end_time')->nullable();

            $table->string('assigned_area')->nullable();

            $table->string('duty_type')->default('Maintenance');

            $table->enum('status', [
                'Scheduled',
                'On Duty',
                'Completed',
                'Off Duty'
            ])->default('Scheduled');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_duties');
    }
};
