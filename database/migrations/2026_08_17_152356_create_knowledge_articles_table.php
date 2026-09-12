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
        Schema::create('knowledge_articles', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Article Identification
            |--------------------------------------------------------------------------
            */

            $table->string('article_no')
                ->unique();

            $table->string('title');

            $table->string('slug')
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | Classification
            |--------------------------------------------------------------------------
            */

            $table->string('category')
                ->nullable();

            $table->string('equipment_type')
                ->nullable();

            $table->string('problem_type')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Knowledge Content
            |--------------------------------------------------------------------------
            */

            $table->longText('problem_description')
                ->nullable();

            $table->longText('diagnosis')
                ->nullable();

            $table->longText('root_cause')
                ->nullable();

            $table->longText('solution')
                ->nullable();

            $table->longText('repair_procedure')
                ->nullable();

            $table->text('materials_used')
                ->nullable();

            $table->text('parts_replaced')
                ->nullable();

            $table->text('tools_used')
                ->nullable();

            $table->longText('technician_notes')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Source Maintenance Report
            |--------------------------------------------------------------------------
            */

            $table->foreignId('maintenance_report_id')
                ->nullable()
                ->constrained('maintenance_reports')
                ->nullOnDelete();

            $table->foreignId('complaint_id')
                ->nullable()
                ->constrained('complaints')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Creation / Approval
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Repository Status
            |--------------------------------------------------------------------------
            |
            | Draft
            | Pending Review
            | Published
            | Archived
            |
            */

            $table->string('status')
                ->default('Draft');

            /*
            |--------------------------------------------------------------------------
            | AI / Search Preparation
            |--------------------------------------------------------------------------
            */

            $table->longText('searchable_text')
                ->nullable();

            $table->unsignedInteger('view_count')
                ->default(0);

            $table->timestamp('published_at')
                ->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('category');
            $table->index('equipment_type');
            $table->index('problem_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_articles');
    }
};
