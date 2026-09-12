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
        Schema::table('complaint_categories', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Complaint Workflow Type
            |--------------------------------------------------------------------------
            |
            | Determines which department/process should handle the complaint.
            |
            */

            $table->enum('category_type', [
                'Maintenance',
                'Billing',
                'Account',
                'General',
            ])
                ->default('General')
                ->after('description');

            /*
            |--------------------------------------------------------------------------
            | Maintenance Requirement
            |--------------------------------------------------------------------------
            |
            | Determines whether the complaint can proceed to the
            | Maintenance Manager / Technician workflow.
            |
            */

            $table->boolean('requires_maintenance')
                ->default(false)
                ->after('category_type');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaint_categories', function (Blueprint $table) {

            $table->dropColumn([
                'category_type',
                'requires_maintenance',
            ]);
        });
    }
};
