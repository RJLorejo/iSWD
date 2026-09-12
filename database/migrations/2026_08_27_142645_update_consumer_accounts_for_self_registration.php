<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CONSUMERS
        |--------------------------------------------------------------------------
        */

        // Remove old fields that are no longer part of the consumer account.
        Schema::table('consumers', function (Blueprint $table) {

            $table->dropColumn([
                'consumer_no',
                'sex',
                'birth_date',
            ]);
        });

        // Consumer verification.
        Schema::table('consumers', function (Blueprint $table) {

            $table->enum('verification_status', [
                'Pending Verification',
                'Verified',
                'Rejected',
                'Suspended',
            ])
                ->default('Pending Verification')
                ->after('email');

            $table->timestamp('verified_at')
                ->nullable()
                ->after('verification_status');

            $table->foreignId('verified_by')
                ->nullable()
                ->after('verified_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->text('verification_reason')
                ->nullable()
                ->after('verified_by');

            $table->timestamp('email_verified_at')
                ->nullable()
                ->after('verification_reason');

            $table->timestamp('phone_verified_at')
                ->nullable()
                ->after('email_verified_at');

            $table->enum('registration_source', [
                'Self Registration',
                'Customer Service',
                'Imported',
            ])
                ->default('Self Registration')
                ->after('phone_verified_at');
        });


        /*
        |--------------------------------------------------------------------------
        | CONSUMER ADDRESSES
        |--------------------------------------------------------------------------
        */

        if (Schema::hasColumn('consumer_addresses', 'zip_code')) {
            Schema::table('consumer_addresses', function (Blueprint $table) {
                $table->dropColumn('zip_code');
            });
        }

        if (Schema::hasColumn('consumer_addresses', 'landmark')) {
            Schema::table('consumer_addresses', function (Blueprint $table) {
                $table->dropColumn('landmark');
            });
        }
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CONSUMER ADDRESSES
        |--------------------------------------------------------------------------
        */

        if (!Schema::hasColumn('consumer_addresses', 'zip_code')) {
            Schema::table('consumer_addresses', function (Blueprint $table) {
                $table->string('zip_code')
                    ->nullable()
                    ->after('province');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | CONSUMERS
        |--------------------------------------------------------------------------
        */

        Schema::table('consumers', function (Blueprint $table) {

            $table->dropForeign([
                'verified_by',
            ]);

            $table->dropColumn([
                'verification_status',
                'verified_at',
                'verified_by',
                'verification_reason',
                'email_verified_at',
                'phone_verified_at',
                'registration_source',
            ]);

            $table->enum('sex', [
                'Male',
                'Female',
            ])->nullable();

            $table->date('birth_date')
                ->nullable();

            $table->string('consumer_no')
                ->nullable();
        });
    }
};
