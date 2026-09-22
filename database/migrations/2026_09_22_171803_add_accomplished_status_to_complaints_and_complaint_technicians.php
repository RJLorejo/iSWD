<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE complaints
            MODIFY COLUMN status ENUM(
                'Pending',
                'Verified',
                'Assigned',
                'In Progress',
                'Accomplished',
                'Completed',
                'Closed',
                'Rejected'
            ) NOT NULL DEFAULT 'Pending'
        ");

        DB::statement("
            ALTER TABLE complaint_technicians
            MODIFY COLUMN status ENUM(
                'Assigned',
                'In Progress',
                'Accomplished',
                'Completed',
                'Cancelled'
            ) NOT NULL DEFAULT 'Assigned'
        ");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE complaints
            SET status = 'Completed'
            WHERE status = 'Accomplished'
        ");

        DB::statement("
            UPDATE complaint_technicians
            SET status = 'Completed'
            WHERE status = 'Accomplished'
        ");

        DB::statement("
            ALTER TABLE complaints
            MODIFY COLUMN status ENUM(
                'Pending',
                'Verified',
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
                'Rejected'
            ) NOT NULL DEFAULT 'Pending'
        ");

        DB::statement("
            ALTER TABLE complaint_technicians
            MODIFY COLUMN status ENUM(
                'Assigned',
                'In Progress',
                'Completed',
                'Cancelled'
            ) NOT NULL DEFAULT 'Assigned'
        ");
    }
};
