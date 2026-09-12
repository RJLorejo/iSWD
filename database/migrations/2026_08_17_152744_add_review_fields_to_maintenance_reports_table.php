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

            $table->string('review_status')
                ->default('Pending Review')
                ->after('submitted_at');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->after('review_status')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable()
                ->after('reviewed_by');

            $table->longText('review_remarks')
                ->nullable()
                ->after('reviewed_at');

            $table->unsignedInteger('revision_number')
                ->default(0)
                ->after('review_remarks');

            $table->timestamp('resubmitted_at')
                ->nullable()
                ->after('revision_number');

            $table->index('review_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {

            $table->dropForeign([
                'reviewed_by',
            ]);

            $table->dropIndex([
                'maintenance_reports_review_status_index',
            ]);

            $table->dropColumn([
                'review_status',
                'reviewed_by',
                'reviewed_at',
                'review_remarks',
                'revision_number',
                'resubmitted_at',
            ]);
        });
    }
};
