<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('maintenance_work_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maintenance_work_order_id');
            $table->unsignedBigInteger('technician_id');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->string('end_reason', 30)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('maintenance_work_order_id', 'mws_order_fk')
                ->references('id')->on('maintenance_work_orders')->cascadeOnDelete();
            $table->foreign('technician_id', 'mws_technician_fk')
                ->references('id')->on('users')->restrictOnDelete();
            $table->index(['maintenance_work_order_id', 'technician_id', 'ended_at'], 'mws_active_lookup');
        });
    }
    public function down(): void {
        Schema::dropIfExists('maintenance_work_sessions');
    }
};
