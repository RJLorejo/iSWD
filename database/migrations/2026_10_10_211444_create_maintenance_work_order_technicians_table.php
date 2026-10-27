<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_work_order_technicians', function (Blueprint $table) {
            $table->id();

            // Maintenance work order
            $table->unsignedBigInteger('maintenance_work_order_id');

            $table->foreign(
                'maintenance_work_order_id',
                'mwot_work_order_fk'
            )
                ->references('id')
                ->on('maintenance_work_orders')
                ->cascadeOnDelete();

            // Assigned plumber
            $table->unsignedBigInteger('technician_id');

            $table->foreign(
                'technician_id',
                'mwot_technician_fk'
            )
                ->references('id')
                ->on('users')
                ->restrictOnDelete();

            // Team role
            $table->string('assignment_role', 50)
                ->default('Member');

            $table->timestamps();

            // Prevent duplicate plumber assignment to one work order
            $table->unique(
                ['maintenance_work_order_id', 'technician_id'],
                'mwot_work_order_technician_unique'
            );

            // Speed up plumber workload searches
            $table->index(
                'technician_id',
                'mwot_technician_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_work_order_technicians');
    }
};
