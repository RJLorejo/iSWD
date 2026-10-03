<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'complaint_ai_analyses',
            function (Blueprint $table) {

                /*
                |--------------------------------------------------------------------------
                | Consumer Submitted Classification
                |--------------------------------------------------------------------------
                */

                $table->foreignId(
                    'consumer_category_id'
                )
                    ->nullable()
                    ->after('consumer_accepted')
                    ->constrained(
                        'complaint_categories'
                    )
                    ->nullOnDelete();


                /*
                |--------------------------------------------------------------------------
                | Customer Service Verified Classification
                |--------------------------------------------------------------------------
                */

                $table->foreignId(
                    'verified_category_id'
                )
                    ->nullable()
                    ->after('consumer_category_id')
                    ->constrained(
                        'complaint_categories'
                    )
                    ->nullOnDelete();


                /*
                |--------------------------------------------------------------------------
                | Verification Information
                |--------------------------------------------------------------------------
                */

                $table->foreignId(
                    'verified_by'
                )
                    ->nullable()
                    ->after('verified_category_id')
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamp(
                    'verified_at'
                )
                    ->nullable()
                    ->after('verified_by');
            }
        );
    }


    public function down(): void
    {
        Schema::table(
            'complaint_ai_analyses',
            function (Blueprint $table) {

                $table->dropForeign([
                    'consumer_category_id'
                ]);

                $table->dropForeign([
                    'verified_category_id'
                ]);

                $table->dropForeign([
                    'verified_by'
                ]);

                $table->dropColumn([
                    'consumer_category_id',
                    'verified_category_id',
                    'verified_by',
                    'verified_at',
                ]);
            }
        );
    }
};
