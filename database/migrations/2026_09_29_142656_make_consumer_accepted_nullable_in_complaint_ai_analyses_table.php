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

                $table->boolean('consumer_accepted')
                    ->nullable()
                    ->default(null)
                    ->change();
            }
        );
    }

    public function down(): void
    {

        \DB::table('complaint_ai_analyses')
            ->whereNull('consumer_accepted')
            ->update([
                'consumer_accepted' => false,
            ]);

        Schema::table(
            'complaint_ai_analyses',
            function (Blueprint $table) {

                $table->boolean('consumer_accepted')
                    ->default(false)
                    ->nullable(false)
                    ->change();
            }
        );
    }
};
