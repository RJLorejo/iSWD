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
        Schema::create('consumer_addresses', function (Blueprint $table) {

            $table->id();

            $table->foreignId('consumer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('house_no')->nullable();

            $table->string('street')->nullable();

            $table->string('purok')->nullable();

            $table->string('barangay');

            $table->string('municipality')->default('Sagay');

            $table->string('province')->default('Negros Occidental');

            $table->string('zip_code')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumer_addresses');
    }
};
