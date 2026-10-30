<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create the base table only if it is missing.
        if (!Schema::hasTable('maintenance_reports')) {
            Schema::create('maintenance_reports', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Intentionally do not drop this table.
        // It may contain production maintenance records.
    }
};