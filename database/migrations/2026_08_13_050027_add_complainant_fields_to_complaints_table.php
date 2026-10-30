
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('complaints')) {
            return;
        }

        Schema::table('complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('complaints', 'complainant_name')) {
                $table->string('complainant_name')
                    ->nullable()
                    ->after('consumer_id');
            }

            if (!Schema::hasColumn('complaints', 'complainant_phone')) {
                $table->string('complainant_phone')
                    ->nullable()
                    ->after('complainant_name');
            }
        });
    }

    public function down(): void
    {
        // Intentionally left empty.
        // The earlier walk-in complainant migration owns these columns.
    }
};
