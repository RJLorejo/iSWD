
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_work_order_participations', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Maintenance Work Order
            |--------------------------------------------------------------------------
            */

            $table->foreignId('maintenance_work_order_id');

            $table->foreign(
                'maintenance_work_order_id',
                'mwop_work_order_fk'
            )
                ->references('id')
                ->on('maintenance_work_orders')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Participating Technician
            |--------------------------------------------------------------------------
            */

            $table->foreignId('technician_id');

            $table->foreign(
                'technician_id',
                'mwop_technician_fk'
            )
                ->references('id')
                ->on('users')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Participation Status
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'Active',
                'Handed Over',
                'Pending Workload',
                'Completed',
            ])->default('Active');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            $table->text('contribution_notes')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Recorded By
            |--------------------------------------------------------------------------
            */

            $table->foreignId('recorded_by')->nullable();

            $table->foreign(
                'recorded_by',
                'mwop_recorded_by_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->unique(
                ['maintenance_work_order_id', 'technician_id'],
                'mwop_work_technician_unique'
            );

            $table->index(
                ['technician_id', 'status'],
                'mwop_technician_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_work_order_participations');
    }
};
