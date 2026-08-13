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
        Schema::table('complaints', function (Blueprint $table) {

            if (!Schema::hasColumn('complaints', 'complainant_name')) {
                $table->string('complainant_name')
                    ->nullable()
                    ->after('consumer_id');
            }

            if (!Schema::hasColumn('complaints', 'complainant_phone')) {
                $table->string('complainant_phone')
                    ->nullable()
                    ->after('complainant_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {

            if (Schema::hasColumn('complaints', 'complainant_phone')) {
                $table->dropColumn('complainant_phone');
            }

            if (Schema::hasColumn('complaints', 'complainant_name')) {
                $table->dropColumn('complainant_name');
            }
        });
    }
};
