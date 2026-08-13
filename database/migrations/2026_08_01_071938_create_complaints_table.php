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
        Schema::create('complaints', function (Blueprint $table) {

            $table->id();

            $table->string('complaint_no')->unique();

            $table->foreignId('consumer_id')->nullable()->constrained();

            $table->foreignId('complaint_category_id')
                ->constrained();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users');

            $table->foreignId('customer_service_id')
                ->nullable()
                ->constrained('users');

            $table->enum('priority', [
                'Low',
                'Medium',
                'High',
                'Critical'
            ])->default('Medium');

            $table->enum('status', [

                'Pending',

                'Verified',

                'Assigned',

                'In Progress',

                'Completed',

                'Closed',

                'Rejected'

            ])->default('Pending');

            $table->string('subject');

            $table->longText('description');

            $table->string('address');

            $table->string('landmark')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();

            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('photo')->nullable();

            $table->timestamp('verified_at')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
