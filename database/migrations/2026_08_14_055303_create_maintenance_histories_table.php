<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_histories', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Related Complaint
            |--------------------------------------------------------------------------
            */
            $table->foreignId('complaint_id')
                ->constrained('complaints')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | User who performed the action
            |--------------------------------------------------------------------------
            */
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Event Information
            |--------------------------------------------------------------------------
            */
            $table->string('event_type', 100);

            $table->string('title');

            $table->text('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status Tracking
            |--------------------------------------------------------------------------
            */
            $table->string('old_status')->nullable();

            $table->string('new_status')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Additional Information
            |--------------------------------------------------------------------------
            */
            $table->json('metadata')->nullable();

            $table->timestamp('event_at');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */
            $table->index([
                'complaint_id',
                'event_at',
            ]);

            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_histories');
    }
};
