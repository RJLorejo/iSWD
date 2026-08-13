<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MaintenanceReportController extends Controller
{
    /**
     * Technician Maintenance Report
     */
    public function index(Request $request)
    {
        $technicianId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        */

        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $baseQuery = Complaint::where(
            'assigned_to',
            $technicianId
        );

        /*
        |--------------------------------------------------------------------------
        | Overall Statistics
        |--------------------------------------------------------------------------
        */

        $totalAssigned = (clone $baseQuery)
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $assignedCount = (clone $baseQuery)
            ->where('status', 'Assigned')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $inProgressCount = (clone $baseQuery)
            ->where('status', 'In Progress')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $completedCount = (clone $baseQuery)
            ->where('status', 'Completed')
            ->whereBetween('completed_at', [$from, $to])
            ->count();

        $closedCount = (clone $baseQuery)
            ->where('status', 'Closed')
            ->whereBetween('updated_at', [$from, $to])
            ->count();

        $urgentCount = (clone $baseQuery)
            ->whereIn('priority', ['High', 'Critical'])
            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Completion Rate
        |--------------------------------------------------------------------------
        */

        $completionRate = $totalAssigned > 0
            ? round(($completedCount / $totalAssigned) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Average Completion Time
        |--------------------------------------------------------------------------
        */

        $completedComplaints = (clone $baseQuery)
            ->where('status', 'Completed')
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$from, $to])
            ->get([
                'id',
                'created_at',
                'completed_at',
            ]);

        $averageCompletionHours = 0;

        if ($completedComplaints->count() > 0) {
            $totalHours = $completedComplaints->sum(function ($complaint) {
                return $complaint->created_at
                    ->diffInMinutes($complaint->completed_at) / 60;
            });

            $averageCompletionHours = round(
                $totalHours / $completedComplaints->count(),
                1
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Priority Breakdown
        |--------------------------------------------------------------------------
        */

        $priorityBreakdown = collect([
            'Low',
            'Medium',
            'High',
            'Critical',
        ])->mapWithKeys(function ($priority) use (
            $baseQuery,
            $from,
            $to
        ) {
            return [
                $priority => (clone $baseQuery)
                    ->where('priority', $priority)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Status Breakdown
        |--------------------------------------------------------------------------
        */

        $statusBreakdown = collect([
            'Assigned',
            'In Progress',
            'Completed',
            'Closed',
            'Rejected',
        ])->mapWithKeys(function ($status) use (
            $baseQuery,
            $from,
            $to
        ) {
            return [
                $status => (clone $baseQuery)
                    ->where('status', $status)
                    ->whereBetween(
                        $status === 'Completed'
                            ? 'completed_at'
                            : 'updated_at',
                        [$from, $to]
                    )
                    ->count(),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Completed Maintenance
        |--------------------------------------------------------------------------
        */

        $completedComplaintsList = (clone $baseQuery)
            ->with([
                'consumer',
                'category',
            ])
            ->where('status', 'Completed')
            ->whereBetween(
                'completed_at',
                [$from, $to]
            )
            ->latest('completed_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Current Maintenance
        |--------------------------------------------------------------------------
        */

        $currentComplaints = (clone $baseQuery)
            ->with([
                'consumer',
                'category',
            ])
            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])
            ->latest('updated_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'technician.reports.maintenance',
            compact(
                'from',
                'to',
                'totalAssigned',
                'assignedCount',
                'inProgressCount',
                'completedCount',
                'closedCount',
                'urgentCount',
                'completionRate',
                'averageCompletionHours',
                'priorityBreakdown',
                'statusBreakdown',
                'completedComplaintsList',
                'currentComplaints'
            )
        );
    }

    /**
     * Printable maintenance report.
     */
    public function print(Request $request)
    {
        $technicianId = Auth::id();

        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $complaints = Complaint::with([
            'consumer',
            'category',
        ])
            ->where('assigned_to', $technicianId)
            ->where('status', 'Completed')
            ->whereBetween(
                'completed_at',
                [$from, $to]
            )
            ->latest('completed_at')
            ->get();

        $total = $complaints->count();

        $averageCompletionHours = 0;

        if ($total > 0) {
            $totalHours = $complaints->sum(function ($complaint) {
                return $complaint->created_at
                    ->diffInMinutes($complaint->completed_at) / 60;
            });

            $averageCompletionHours = round(
                $totalHours / $total,
                1
            );
        }

        return view(
            'technician.reports.maintenance-print',
            compact(
                'from',
                'to',
                'complaints',
                'total',
                'averageCompletionHours'
            )
        );
    }
}
