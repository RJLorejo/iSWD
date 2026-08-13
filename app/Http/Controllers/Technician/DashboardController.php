<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display technician dashboard.
     */
    public function index()
    {
        $technicianId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | STATISTICS
        |--------------------------------------------------------------------------
        */

        $assignedCount = Complaint::where('assigned_to', $technicianId)
            ->where('status', 'Assigned')
            ->count();

        $inProgressCount = Complaint::where('assigned_to', $technicianId)
            ->where('status', 'In Progress')
            ->count();

        $completedCount = Complaint::where('assigned_to', $technicianId)
            ->where('status', 'Completed')
            ->count();

        $urgentCount = Complaint::where('assigned_to', $technicianId)
            ->whereIn('priority', ['High', 'Critical'])
            ->whereIn('status', ['Assigned', 'In Progress'])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | TOTAL ASSIGNED
        |--------------------------------------------------------------------------
        */

        $totalCount = Complaint::where('assigned_to', $technicianId)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | ACTIVE WORK
        |--------------------------------------------------------------------------
        */

        $activeCount = Complaint::where('assigned_to', $technicianId)
            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | CURRENT COMPLAINTS
        |--------------------------------------------------------------------------
        */

        $currentComplaints = Complaint::with([
            'consumer',
            'category',
            'technician',
            'verifier',
        ])
            ->where('assigned_to', $technicianId)
            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])
            ->orderByRaw("
                CASE
                    WHEN priority = 'Critical' THEN 1
                    WHEN priority = 'High' THEN 2
                    WHEN priority = 'Medium' THEN 3
                    ELSE 4
                END
            ")
            ->latest('updated_at')
            ->take(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RECENTLY COMPLETED
        |--------------------------------------------------------------------------
        */

        $recentCompleted = Complaint::with([
            'consumer',
            'category',
            'technician',
        ])
            ->where('assigned_to', $technicianId)
            ->where('status', 'Completed')
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RECENTLY UPDATED
        |--------------------------------------------------------------------------
        */

        $recentActivity = Complaint::with([
            'consumer',
            'category',
        ])
            ->where('assigned_to', $technicianId)
            ->latest('updated_at')
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
