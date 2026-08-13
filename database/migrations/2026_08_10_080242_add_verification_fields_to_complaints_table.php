<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {


            if (!Schema::hasColumn('complaints', 'verified_by')) {

                $table->foreignId('verified_by')
                    ->nullable()
                    ->after('status')
                    ->constrained('users')
                    ->nullOnDelete();

            }

            if (!Schema::hasColumn('complaints', 'verified_at')) {

                $table->timestamp('verified_at')
                    ->nullable()
                    ->after('verified_by');

            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {

            if (Schema::hasColumn('complaints', 'verified_by')) {
                $table->dropForeign(['verified_by']);
                $table->dropColumn('verified_by');
            }

            if (Schema::hasColumn('complaints', 'verified_at')) {
                $table->dropColumn('verified_at');
            }

        });
    }
};
