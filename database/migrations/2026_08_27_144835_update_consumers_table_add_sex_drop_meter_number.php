
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('consumers')) {
            return;
        }

        // Add sex only if it does not already exist.
        if (!Schema::hasColumn('consumers', 'sex')) {
            Schema::table('consumers', function (Blueprint $table) {
                $table->string('sex')->nullable()->after('suffix');
            });
        }

        // Drop meter_number only if it exists.
        if (Schema::hasColumn('consumers', 'meter_number')) {
            Schema::table('consumers', function (Blueprint $table) {
                $table->dropColumn('meter_number');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('consumers')) {
            return;
        }

        // Restore meter_number only if it does not exist.
        if (!Schema::hasColumn('consumers', 'meter_number')) {
            Schema::table('consumers', function (Blueprint $table) {
                $table->string('meter_number')
                    ->nullable()
                    ->after('account_number');
            });
        }

        // Remove sex only if it exists.
        if (Schema::hasColumn('consumers', 'sex')) {
            Schema::table('consumers', function (Blueprint $table) {
                $table->dropColumn('sex');
            });
        }
    }
};
