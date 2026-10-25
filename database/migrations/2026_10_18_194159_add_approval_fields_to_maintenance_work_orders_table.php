
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_work_orders', function (Blueprint $table) {
            $table->enum('approval_status', [
                'Draft',
                'Approved',
            ])->default('Draft');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->index(
                ['maintenance_schedule_id', 'approval_status'],
                'work_orders_schedule_approval_index'
            );
        });

        // Preserve existing finalized assignments.
        DB::table('maintenance_work_orders')
            ->whereIn(
                'maintenance_schedule_id',
                DB::table('maintenance_schedules')
                    ->select('id')
                    ->where('status', 'Finalized')
            )
            ->update([
                'approval_status' => 'Approved',
            ]);
    }

    public function down(): void
    {
        Schema::table('maintenance_work_orders', function (Blueprint $table) {
            $table->dropIndex('work_orders_schedule_approval_index');

            $table->dropForeign(['approved_by']);

            $table->dropColumn([
                'approval_status',
                'approved_by',
                'approved_at',
            ]);
        });
    }
};
