<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement_reads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('consumer_id')
                ->constrained('consumers')
                ->cascadeOnDelete();

            $table->foreignId('service_announcement_id')
                ->constrained('service_announcements')
                ->cascadeOnDelete();

            $table->timestamp('read_at');

            $table->timestamps();

            $table->unique([
                'consumer_id',
                'service_announcement_id',
            ]);

            $table->index([
                'consumer_id',
                'read_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_reads');
    }
};
