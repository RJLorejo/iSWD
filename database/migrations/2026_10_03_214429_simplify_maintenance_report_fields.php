<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->longText('materials_parts')
                ->nullable()
                ->after('root_cause');
        });

        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropColumn([
                'work_performed',
                'repair_procedure',
                'materials_used',
                'parts_replaced',
                'tools_used',
                'completion_remarks',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->longText('work_performed')->nullable();
            $table->longText('repair_procedure')->nullable();
            $table->text('materials_used')->nullable();
            $table->text('parts_replaced')->nullable();
            $table->text('tools_used')->nullable();
            $table->longText('completion_remarks')->nullable();

            $table->dropColumn('materials_parts');
        });
    }
};
