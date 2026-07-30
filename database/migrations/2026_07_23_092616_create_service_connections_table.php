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
        Schema::create('service_connections', function (Blueprint $table) {

            $table->id();

            $table->foreignId('consumer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('account_number')->unique();

            $table->string('service_connection_number')->unique();

            $table->string('meter_number')->unique();

            $table->enum('status', [
                'Active',
                'Disconnected',
                'Pending'
            ])->default('Pending');

            $table->date('installation_date')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();

            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_connections');
    }
};
