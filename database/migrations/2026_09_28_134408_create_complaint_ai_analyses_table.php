<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'complaint_ai_analyses',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Complaint
                |--------------------------------------------------------------------------
                */

                $table->foreignId('complaint_id')
                    ->unique()
                    ->constrained('complaints')
                    ->cascadeOnDelete();


                /*
                |--------------------------------------------------------------------------
                | AI Classification
                |--------------------------------------------------------------------------
                */

                $table->foreignId('predicted_category_id')
                    ->nullable()
                    ->constrained('complaint_categories')
                    ->nullOnDelete();

                $table->string('predicted_type')
                    ->nullable();

                $table->decimal(
                    'confidence',
                    8,
                    6
                )->nullable();

                $table->string(
                    'confidence_level',
                    30
                )->nullable();

                $table->decimal(
                    'confidence_gap',
                    8,
                    6
                )->nullable();

                $table->boolean('ambiguous')
                    ->default(false);


                /*
                |--------------------------------------------------------------------------
                | AI Urgency
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'urgency_level',
                    30
                )->nullable();

                $table->integer('urgency_score')
                    ->nullable();


                /*
                |--------------------------------------------------------------------------
                | Operational Analysis
                |--------------------------------------------------------------------------
                */

                $table->json('signals')
                    ->nullable();

                $table->json('evidence')
                    ->nullable();


                /*
                |--------------------------------------------------------------------------
                | AI Review
                |--------------------------------------------------------------------------
                */

                $table->json('review_reasons')
                    ->nullable();

                $table->json('verification_questions')
                    ->nullable();


                /*
                |--------------------------------------------------------------------------
                | Human Decision / Audit
                |--------------------------------------------------------------------------
                */

                $table->boolean('consumer_accepted')
                    ->default(false);

                $table->foreignId('final_category_id')
                    ->nullable()
                    ->constrained('complaint_categories')
                    ->nullOnDelete();


                /*
                |--------------------------------------------------------------------------
                | Complete Original AI Response
                |--------------------------------------------------------------------------
                */

                $table->json('raw_analysis')
                    ->nullable();

                $table->timestamp('analyzed_at')
                    ->nullable();

                $table->timestamps();
            }
        );
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'complaint_ai_analyses'
        );
    }
};
