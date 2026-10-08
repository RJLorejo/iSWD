<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_otps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('channel', 20);

            $table->string('purpose', 50);

            $table->string('destination');

            $table->string('code_hash');

            $table->timestamp('expires_at');

            $table->unsignedTinyInteger('attempts')
                ->default(0);

            $table->timestamp('verified_at')
                ->nullable();

            $table->timestamp('last_sent_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'destination',
                'channel',
                'purpose',
            ]);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('verification_otps');
    }
};
