<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_feedback', function (Blueprint $table) {
            $table->id();

            $table->foreignId('complaint_id')
                ->constrained('complaints')
                ->cascadeOnDelete();

            $table->foreignId('consumer_id')
                ->constrained('consumers')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('overall_rating');
            $table->unsignedTinyInteger('service_quality_rating');
            $table->unsignedTinyInteger('response_time_rating');
            $table->unsignedTinyInteger('personnel_courtesy_rating');

            $table->enum('resolution_status', [
                'Resolved',
                'Partially Resolved',
                'Not Resolved',
            ]);

            $table->text('comments')->nullable();

            $table->timestamps();

            $table->unique('complaint_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_feedback');
    }
};
