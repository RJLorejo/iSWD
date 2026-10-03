<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commercial_resolutions', function (Blueprint $table) {
            $table->dropColumn([
                'action_taken',
                'consumer_response',
                'response_communicated_at',
                'response_method',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('commercial_resolutions', function (Blueprint $table) {
            $table->text('action_taken')->nullable();
            $table->text('consumer_response')->nullable();
            $table->dateTime('response_communicated_at')->nullable();
            $table->string('response_method')->nullable();
        });
    }
};
