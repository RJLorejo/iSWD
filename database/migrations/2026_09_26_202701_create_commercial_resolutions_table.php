<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commercial_resolutions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('complaint_id')
                ->unique()
                ->constrained('complaints')
                ->cascadeOnDelete();

            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('started_at')->nullable();

            $table->text('findings')->nullable();

            $table->text('action_taken')->nullable();

            $table->text('resolution_remarks')->nullable();

            $table->text('consumer_response')->nullable();

            $table->dateTime('completed_at')->nullable();

            $table->dateTime('response_communicated_at')->nullable();

            $table->string('response_method')->nullable();

            $table->timestamps();

            $table->index('started_at');
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commercial_resolutions');
    }
};
