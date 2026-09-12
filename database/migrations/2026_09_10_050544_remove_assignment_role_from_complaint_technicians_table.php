<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaint_technicians', function (Blueprint $table) {
            $table->dropColumn('assignment_role');
        });
    }

    public function down(): void
    {
        Schema::table('complaint_technicians', function (Blueprint $table) {
            $table->string('assignment_role', 50)
                ->nullable();
        });
    }
};
