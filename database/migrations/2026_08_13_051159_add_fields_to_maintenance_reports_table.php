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
        Schema::table('maintenance_reports', function (Blueprint $table) {

            $table->foreignId('complaint_id')
                ->unique()
                ->constrained('complaints')
                ->cascadeOnDelete();

            $table->foreignId('technician_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('diagnosis')
                ->nullable();

            $table->text('root_cause')
                ->nullable();

            $table->longText('work_performed')
                ->nullable();

            $table->longText('repair_procedure')
                ->nullable();

            $table->text('materials_used')
                ->nullable();

            $table->text('parts_replaced')
                ->nullable();

            $table->text('tools_used')
                ->nullable();

            $table->longText('technician_notes')
                ->nullable();

            $table->longText('completion_remarks')
                ->nullable();

            $table->string('before_photo')
                ->nullable();

            $table->string('after_photo')
                ->nullable();

            $table->timestamp('started_at')
                ->nullable();

            $table->timestamp('submitted_at')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {

            $table->dropForeign([
                'complaint_id',
            ]);

            $table->dropForeign([
                'technician_id',
            ]);

            $table->dropColumn([
                'complaint_id',
                'technician_id',
                'diagnosis',
                'root_cause',
                'work_performed',
                'repair_procedure',
                'materials_used',
                'parts_replaced',
                'tools_used',
                'technician_notes',
                'completion_remarks',
                'before_photo',
                'after_photo',
                'started_at',
                'submitted_at',
            ]);
        });
    }
};
