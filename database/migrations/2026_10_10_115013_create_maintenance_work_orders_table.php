<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_work_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('maintenance_schedule_id')
                ->nullable()
                ->constrained('maintenance_schedules')
                ->restrictOnDelete();

            $table->foreignId('complaint_id')
                ->constrained('complaints')
                ->restrictOnDelete();

            // Supports repeat visits without duplicate complaints
            $table->unsignedInteger('visit_number')->default(1);

            $table->enum('dispatch_type', [
                'Regular',
                'Emergency',
            ])->default('Regular');

            $table->enum('status', [
                'Scheduled',
                'In Progress',
                'Completed',
                'Pending Workload',
                'Rescheduled',
                'Cancelled',
            ])->default('Scheduled');

            $table->time('planned_start_time')->nullable();
            $table->time('planned_end_time')->nullable();

            $table->foreignId('scheduled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Required by application validation when unfinished
            $table->text('pending_reason')->nullable();

            $table->timestamp('pending_recorded_at')->nullable();

            $table->foreignId('pending_recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Required by application validation for late additions
            $table->boolean('is_late_addition')->default(false);
            $table->text('late_addition_reason')->nullable();

            $table->text('manager_remarks')->nullable();

            $table->timestamps();

            $table->unique(
                ['complaint_id', 'visit_number'],
                'maintenance_work_orders_complaint_visit_unique'
            );

            $table->index(
                ['maintenance_schedule_id', 'status'],
                'maintenance_work_orders_schedule_status_index'
            );

            $table->index(
                ['dispatch_type', 'status'],
                'maintenance_work_orders_dispatch_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_work_orders');
    }
};
