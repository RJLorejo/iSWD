<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consumers', function (Blueprint $table) {
            $table->string('sex')->nullable()->after('suffix');

            $table->dropColumn('meter_number');
        });
    }

    public function down(): void
    {
        Schema::table('consumers', function (Blueprint $table) {
            $table->string('meter_number')->nullable()->after('account_number');

            $table->dropColumn('sex');
        });
    }
};
