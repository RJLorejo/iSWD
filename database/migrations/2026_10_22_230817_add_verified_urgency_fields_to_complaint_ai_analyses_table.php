
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaint_ai_analyses', function (Blueprint $table) {
            $table->string('verified_urgency_level', 30)
                ->nullable()
                ->after('urgency_score');

            $table->foreignId('urgency_verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('urgency_verified_at')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('complaint_ai_analyses', function (Blueprint $table) {
            $table->dropForeign(['urgency_verified_by']);

            $table->dropColumn([
                'verified_urgency_level',
                'urgency_verified_by',
                'urgency_verified_at',
            ]);
        });
    }
};
