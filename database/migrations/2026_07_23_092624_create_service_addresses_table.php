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
        Schema::create('service_addresses', function (Blueprint $table) {

            $table->id();

            $table->foreignId('service_connection_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('house_no')->nullable();

            $table->string('street')->nullable();

            $table->string('purok')->nullable();

            $table->string('barangay');

            $table->string('city')->default('Sagay City');

            $table->string('province')->default('Negros Occidental');

            $table->string('zip_code')->nullable();

            $table->string('landmark')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_addresses');
    }
};
