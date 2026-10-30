
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

        if (!Schema::hasColumn('consumers', 'account_number')) {
            Schema::table('consumers', function (Blueprint $table) {
                $table->string('account_number', 50)
                    ->nullable()
                    ->after('id');
            });
        }

        // Add a unique index only if one does not already exist.
        $indexes = collect(Schema::getIndexes('consumers'));

        $hasAccountNumberUniqueIndex = $indexes->contains(
            fn ($index) =>
                $index['unique'] &&
                $index['columns'] === ['account_number']
        );

        if (!$hasAccountNumberUniqueIndex) {
            Schema::table('consumers', function (Blueprint $table) {
                $table->unique('account_number');
            });
        }
    }

    public function down(): void
    {
        // Intentionally preserve account_number and its data.
        // This migration must not remove consumer account information.
    }
};
