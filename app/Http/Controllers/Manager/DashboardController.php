<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | MAINTENANCE CASE STATISTICS
        |--------------------------------------------------------------------------
        */

        // Verified complaints
        $verifiedComplaints = Complaint::where(
            'complaints.status',
            'Verified'
        )->count();


        // Verified complaints that do not yet have a technician
        $unassignedCases = Complaint::where(
            'complaints.status',
            'Verified'
        )
            ->whereDoesntHave('technicians')
            ->count();


        // Assigned complaints
        $assignedCases = Complaint::where(
            'complaints.status',
            'Assigned'
        )->count();


        // Complaints currently being worked on
        $inProgressComplaints = Complaint::where(
            'complaints.status',
            'In Progress'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | REPORTS FOR REVIEW
        |--------------------------------------------------------------------------
        |
        | TEMPORARY VALUE FOR THE CURRENT PROTOTYPE.
        |
        | Replace this with the actual MaintenanceReport query
        | once the report workflow is finalized.
        |
        */

        $reportsForReview = 2;


        /*
        |--------------------------------------------------------------------------
        | COMPLETED TODAY
        |--------------------------------------------------------------------------
        */

        $completedToday = Complaint::where(
            'complaints.status',
            'Completed'
        )
            ->whereDate(
                'complaints.updated_at',
                Carbon::today()
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | TECHNICIAN COUNT
        |--------------------------------------------------------------------------
        */

        $technicianCount = User::role(
            'Maintenance Technician'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | RECENT VERIFIED COMPLAINTS
        |--------------------------------------------------------------------------
        */

        $recentVerifiedComplaints = Complaint::with([
            'consumer',
            'category',
        ])
            ->where(
                'complaints.status',
                'Verified'
            )
            ->latest('complaints.created_at')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | ACTIVE COMPLAINTS
        |--------------------------------------------------------------------------
        |
        | A complaint may have multiple assigned technicians.
        |
        */

        $activeComplaints = Complaint::with([
            'consumer',
            'category',
            'technicians',
        ])
            ->whereIn(
                'complaints.status',
                [
                    'Assigned',
                    'In Progress',
                ]
            )
            ->latest('complaints.created_at')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TECHNICIAN WORKLOAD
        |--------------------------------------------------------------------------
        |
        | Because one complaint can have multiple technicians,
        | assignedComplaints must use the complaint_technicians
        | pivot relationship.
        |
        */

        $technicians = User::role(
            'Maintenance Technician'
        )
            ->withCount([
                'assignedComplaints as active_complaints_count' => function ($query) {
                    $query->whereIn(
                        'complaints.status',
                        [
                            'Assigned',
                            'In Progress',
                        ]
                    );
                },
            ])
            ->orderByDesc('active_complaints_count')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | CASE STATUS CHART
        |--------------------------------------------------------------------------
        */

        $caseStatusChart = [
            'labels' => [
                'Verified',
                'Unassigned',
                'Assigned',
                'In Progress',
                'For Review',
                'Completed',
            ],

            'data' => [
                $verifiedComplaints,
                $unassignedCases,
                $assignedCases,
                $inProgressComplaints,
                $reportsForReview,
                $completedToday,
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | RETURN DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view(
            'maintenance-manager.dashboard',
            compact(
                'verifiedComplaints',
                'unassignedCases',
                'assignedCases',
                'inProgressComplaints',
                'reportsForReview',
                'completedToday',
                'technicianCount',
                'recentVerifiedComplaints',
                'activeComplaints',
                'technicians',
                'caseStatusChart'
            )
        );
    }
}
