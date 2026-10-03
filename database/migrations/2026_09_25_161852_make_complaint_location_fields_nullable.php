<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('address', 500)
                ->nullable()
                ->change();

            $table->string('landmark', 255)
                ->nullable()
                ->change();

            $table->decimal('latitude', 10, 7)
                ->nullable()
                ->change();

            $table->decimal('longitude', 10, 7)
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('address', 500)
                ->nullable(false)
                ->change();

            $table->string('landmark', 255)
                ->nullable()
                ->change();

            $table->decimal('latitude', 10, 7)
                ->nullable()
                ->change();

            $table->decimal('longitude', 10, 7)
                ->nullable()
                ->change();
        });
    }
};
