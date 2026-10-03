<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaint_feedback', function (Blueprint $table) {
            $table->enum('handling_status', [
                'New',
                'Reviewed',
                'Follow-up Required',
                'Follow-up In Progress',
                'Resolved',
            ])->default('New')->after('comments');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->after('handling_status')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable()
                ->after('reviewed_by');

            $table->text('follow_up_notes')
                ->nullable()
                ->after('reviewed_at');

            $table->timestamp('follow_up_at')
                ->nullable()
                ->after('follow_up_notes');

            $table->timestamp('resolved_at')
                ->nullable()
                ->after('follow_up_at');
        });
    }

    public function down(): void
    {
        Schema::table('complaint_feedback', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);

            $table->dropColumn([
                'handling_status',
                'reviewed_by',
                'reviewed_at',
                'follow_up_notes',
                'follow_up_at',
                'resolved_at',
            ]);
        });
    }
};
