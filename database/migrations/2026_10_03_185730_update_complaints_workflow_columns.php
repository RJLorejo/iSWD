<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE complaints
            MODIFY status ENUM(
                'Pending',
                'Verified',
                'CS Processing',
                'For Maintenance',
                'Assigned',
                'In Progress',
                'Accomplished',
                'Completed',
                'Closed',
                'Rejected'
            )
            NOT NULL DEFAULT 'Pending'
        ");

        DB::statement("
            UPDATE complaints
            SET status = 'Completed'
            WHERE status = 'Accomplished'
        ");

        DB::statement("
            ALTER TABLE complaints
            MODIFY status ENUM(
                'Pending',
                'Verified',
                'CS Processing',
                'For Maintenance',
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
                'Rejected'
            )
            NOT NULL DEFAULT 'Pending'
        ");

        DB::statement("
            ALTER TABLE complaints
            DROP COLUMN priority
        ");

        DB::statement("
            ALTER TABLE commercial_resolutions
            ADD initial_processing_completed_at DATETIME NULL AFTER resolution_remarks,
            ADD forwarded_to_maintenance_at DATETIME NULL AFTER initial_processing_completed_at,
            ADD forwarded_by BIGINT UNSIGNED NULL AFTER forwarded_to_maintenance_at,
            ADD CONSTRAINT commercial_resolutions_forwarded_by_foreign
                FOREIGN KEY (forwarded_by)
                REFERENCES users(id)
                ON DELETE SET NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE commercial_resolutions
            DROP FOREIGN KEY commercial_resolutions_forwarded_by_foreign,
            DROP COLUMN forwarded_by,
            DROP COLUMN forwarded_to_maintenance_at,
            DROP COLUMN initial_processing_completed_at
        ");

        DB::statement("
            ALTER TABLE complaints
            ADD priority ENUM(
                'Low',
                'Medium',
                'High',
                'Critical'
            )
            NOT NULL DEFAULT 'Medium'
            AFTER customer_service_id
        ");

        DB::statement("
            ALTER TABLE complaints
            MODIFY status ENUM(
                'Pending',
                'Verified',
                'Assigned',
                'In Progress',
                'Accomplished',
                'Completed',
                'Closed',
                'Rejected'
            )
            NOT NULL DEFAULT 'Pending'
        ");
    }
};
