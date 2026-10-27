<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_schedule_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('maintenance_work_order_id')
                ->constrained('maintenance_work_orders')
                ->restrictOnDelete();

            $table->foreignId('performed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action', 60);

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->text('reason')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(
                ['maintenance_work_order_id', 'created_at'],
                'maintenance_schedule_histories_order_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedule_histories');
    }
};
