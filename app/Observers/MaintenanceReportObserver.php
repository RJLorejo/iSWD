<?php

namespace App\Observers;

use App\Models\MaintenanceHistory;
use App\Models\MaintenanceReport;

class MaintenanceReportObserver
{
    /**
     * Report created.
     */
    public function created(
        MaintenanceReport $report
    ): void {

        $complaint = $report->complaint;

        if (!$complaint) {
            return;
        }

        MaintenanceHistory::record(

            $complaint,

            'maintenance_report_created',

            'Maintenance Report Created',

            'A maintenance report draft was created.',

            $complaint->status,

            $complaint->status,

            $report->technician_id
        );

        if ($report->started_at) {

            MaintenanceHistory::record(

                $complaint,

                'maintenance_started',

                'Maintenance Started',

                'The technician started the maintenance activity.',

                $complaint->status,

                $complaint->status,

                $report->technician_id,

                [
                    'started_at' =>
                    $report->started_at,
                ]
            );
        }
    }

    /**
     * Report updated.
     */
    public function updated(
        MaintenanceReport $report
    ): void {

        $complaint = $report->complaint;

        if (!$complaint) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Report submitted
        |--------------------------------------------------------------------------
        */

        if (
            $report->wasChanged('submitted_at') &&
            $report->submitted_at
        ) {

            MaintenanceHistory::record(

                $complaint,

                'maintenance_report_submitted',

                'Maintenance Report Submitted',

                'The technician submitted the completed maintenance report.',

                $complaint->status,

                $complaint->status,

                $report->technician_id,

                [
                    'submitted_at' =>
                    $report->submitted_at,
                ]
            );
        }
    }
}
