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
        Schema::create('service_announcements', function (Blueprint $table) {
            $table->id();

            $table->string('title');

            $table->string('type')->default('General Announcement');

            $table->text('content');

            $table->string('affected_barangay')->nullable();

            $table->dateTime('start_at')->nullable();

            $table->dateTime('end_at')->nullable();

            $table->enum('status', [
                'Draft',
                'Published',
                'Archived',
            ])->default('Draft');

            $table->foreignId('published_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'status',
                'start_at',
                'end_at',
            ]);

            $table->index('affected_barangay');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_announcements');
    }
};
