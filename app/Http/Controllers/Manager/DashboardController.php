<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Complaint Statistics
        |--------------------------------------------------------------------------
        */

        $verifiedComplaints = Complaint::where(
            'status',
            'Verified'
        )->count();

        $assignedComplaints = Complaint::where(
            'status',
            'Assigned'
        )->count();

        $inProgressComplaints = Complaint::where(
            'status',
            'In Progress'
        )->count();

        $completedComplaints = Complaint::where(
            'status',
            'Completed'
        )->count();

        $criticalComplaints = Complaint::where(
            'priority',
            'Critical'
        )
            ->whereNotIn('status', ['Completed', 'Closed'])
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Technician Statistics
        |--------------------------------------------------------------------------
        */

        $technicianCount = User::role(
            'Maintenance Technician'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Recent Verified Complaints
        |--------------------------------------------------------------------------
        */

        $recentVerifiedComplaints = Complaint::with([
            'consumer',
            'category',
        ])
            ->where('status', 'Verified')
            ->latest()
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Assigned / Active Complaints
        |--------------------------------------------------------------------------
        */

        $activeComplaints = Complaint::with([
            'consumer',
            'category',
            'technician',
        ])
            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])
            ->latest()
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Technician Workload
        |--------------------------------------------------------------------------
        */

        $technicians = User::role(
            'Maintenance Technician'
        )
            ->withCount([
                'assignedComplaints as active_complaints_count' => function ($query) {
                    $query->whereIn('status', [
                        'Assigned',
                        'In Progress',
                    ]);
                },
            ])
            ->orderByDesc('active_complaints_count')
            ->take(5)
            ->get();


        return view(
            'maintenance-manager.dashboard',
            compact(
                'verifiedComplaints',
                'assignedComplaints',
                'inProgressComplaints',
                'completedComplaints',
                'criticalComplaints',
                'technicianCount',
                'recentVerifiedComplaints',
                'activeComplaints',
                'technicians'
            )
        );
    }
}
