<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the Maintenance Technician dashboard.
     */
    public function index()
    {
        $technician = Auth::user();

        $assignedComplaints = $technician->assignedComplaints();

        /*
        |--------------------------------------------------------------------------
        | STATISTICS
        |--------------------------------------------------------------------------
        */

        $assignedCount = (clone $assignedComplaints)
            ->where('complaints.status', 'Assigned')
            ->count();

        $inProgressCount = (clone $assignedComplaints)
            ->where('complaints.status', 'In Progress')
            ->count();

        $completedCount = (clone $assignedComplaints)
            ->where('complaints.status', 'Completed')
            ->count();

        $urgentCount = (clone $assignedComplaints)
            ->whereIn('complaints.priority', [
                'High',
                'Critical',
            ])
            ->whereIn('complaints.status', [
                'Assigned',
                'In Progress',
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | TOTAL ASSIGNED
        |--------------------------------------------------------------------------
        */

        $totalCount = (clone $assignedComplaints)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | ACTIVE WORK
        |--------------------------------------------------------------------------
        */

        $activeCount = (clone $assignedComplaints)
            ->whereIn('complaints.status', [
                'Assigned',
                'In Progress',
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | CURRENT WORK
        |--------------------------------------------------------------------------
        |
        | Highest priority complaints appear first.
        |
        */

        $currentComplaints = (clone $assignedComplaints)
            ->with([
                'consumer',
                'category',
                'technicians',
                'verifier',
            ])
            ->whereIn('complaints.status', [
                'Assigned',
                'In Progress',
            ])
            ->orderByRaw("
                CASE
                    WHEN complaints.priority = 'Critical' THEN 1
                    WHEN complaints.priority = 'High' THEN 2
                    WHEN complaints.priority = 'Medium' THEN 3
                    ELSE 4
                END
            ")
            ->latest('complaints.updated_at')
            ->take(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RECENTLY COMPLETED
        |--------------------------------------------------------------------------
        */

        $recentCompleted = (clone $assignedComplaints)
            ->with([
                'consumer',
                'category',
                'technicians',
            ])
            ->where('complaints.status', 'Completed')
            ->whereNotNull('complaints.completed_at')
            ->latest('complaints.completed_at')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RECENT ACTIVITY
        |--------------------------------------------------------------------------
        |
        | This shows the technician's latest assigned complaints regardless
        | of current status.
        |
        */

        $recentActivity = (clone $assignedComplaints)
            ->with([
                'consumer',
                'category',
                'technicians',
            ])
            ->latest('complaints.updated_at')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'technician.dashboard',
            compact(
                'assignedCount',
                'inProgressCount',
                'completedCount',
                'urgentCount',
                'totalCount',
                'activeCount',
                'currentComplaints',
                'recentCompleted',
                'recentActivity'
            )
        );
    }
}
