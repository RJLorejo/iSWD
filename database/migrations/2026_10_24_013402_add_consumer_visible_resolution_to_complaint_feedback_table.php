<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaint_feedback', function (Blueprint $table) {
            if (!Schema::hasColumn('complaint_feedback', 'resolution_message')) {
                $table->text('resolution_message')->nullable();
            }

            if (!Schema::hasColumn('complaint_feedback', 'resolution_sent_at')) {
                $table->timestamp('resolution_sent_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('complaint_feedback', function (Blueprint $table) {
            if (Schema::hasColumn('complaint_feedback', 'resolution_sent_at')) {
                $table->dropColumn('resolution_sent_at');
            }

            if (Schema::hasColumn('complaint_feedback', 'resolution_message')) {
                $table->dropColumn('resolution_message');
            }
        });
    }
};