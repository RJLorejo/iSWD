<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('maintenance_work_orders', function (Blueprint $table) {
            $table->text('missed_reason')->nullable();
            $table->foreignId('missed_reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('missed_reported_at')->nullable();
            $table->foreignId('missed_confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('missed_confirmed_at')->nullable();
            $table->foreignId('rescheduled_from_work_order_id')->nullable()->constrained('maintenance_work_orders')->restrictOnDelete();
            $table->unique('rescheduled_from_work_order_id', 'wo_rescheduled_from_unique');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_work_orders', function (Blueprint $table) {
            $table->dropUnique('wo_rescheduled_from_unique');
            $table->dropForeign(['rescheduled_from_work_order_id']);
            $table->dropForeign(['missed_reported_by']);
            $table->dropForeign(['missed_confirmed_by']);
            $table->dropColumn(['missed_reason','missed_reported_by','missed_reported_at','missed_confirmed_by','missed_confirmed_at','rescheduled_from_work_order_id']);
        });
    }
};
