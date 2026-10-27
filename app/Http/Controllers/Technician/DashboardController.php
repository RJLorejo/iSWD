<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\MaintenanceWorkOrder;
use App\Models\MaintenanceReport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $technicianId = (int) Auth::id();
        $today = Carbon::now('Asia/Manila');
        $todayDate = $today->toDateString();

        $baseOrders = MaintenanceWorkOrder::query()
            ->whereHas('technicians', function ($query) use ($technicianId) {
                $query->where('users.id', $technicianId);
            });

        /*
        |--------------------------------------------------------------------------
        | Active Complaint Constraint
        |--------------------------------------------------------------------------
        */

        $activeComplaint = function ($query) {
            $query->whereNotIn('complaints.status', [
                'Completed',
                'Closed',
                'Rejected',
            ]);
        };

        /*
        |--------------------------------------------------------------------------
        | Work Order Statistics
        |--------------------------------------------------------------------------
        */

        $assignedCount = (clone $baseOrders)
            ->where('maintenance_work_orders.status', 'Scheduled')
            ->whereHas('complaint', $activeComplaint)
            ->count();

        $inProgressCount = (clone $baseOrders)
            ->where('maintenance_work_orders.status', 'In Progress')
            ->whereHas('complaint', $activeComplaint)
            ->count();

        $pendingWorkloadCount = (clone $baseOrders)
            ->where('maintenance_work_orders.status', 'Pending Workload')
            ->whereHas('complaint', $activeComplaint)
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('maintenance_work_orders as later_visits')
                    ->whereColumn(
                        'later_visits.complaint_id',
                        'maintenance_work_orders.complaint_id'
                    )
                    ->whereColumn(
                        'later_visits.visit_number',
                        '>',
                        'maintenance_work_orders.visit_number'
                    )
                    ->where('later_visits.status', 'Completed');
            })
            ->count();

        $historicalPendingCount = (clone $baseOrders)
            ->where('maintenance_work_orders.status', 'Pending Workload')
            ->count();

        $completedCount = (clone $baseOrders)
            ->where('maintenance_work_orders.status', 'Completed')
            ->count();

        $activeCount = $assignedCount + $inProgressCount;

        $totalCount = $activeCount
            + $pendingWorkloadCount
            + $completedCount;

        $completionRate = $totalCount > 0
            ? round(($completedCount / $totalCount) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Today's Scheduled Visits
        |--------------------------------------------------------------------------
        */

        $todayScheduledCount = (clone $baseOrders)
            ->where('maintenance_work_orders.status', 'Scheduled')
            ->whereHas('complaint', $activeComplaint)
            ->whereHas('schedule', function ($query) use ($todayDate) {
                $query->where('status', 'Finalized')
                    ->whereDate('schedule_date', $todayDate);
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Overdue Scheduled Visits
        |--------------------------------------------------------------------------
        */

        $overdueCount = (clone $baseOrders)
            ->where('maintenance_work_orders.status', 'Scheduled')
            ->whereHas('complaint', $activeComplaint)
            ->whereHas('schedule', function ($query) use ($todayDate) {
                $query->where('status', 'Finalized')
                    ->whereDate('schedule_date', '<', $todayDate);
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | AI High Urgency
        |--------------------------------------------------------------------------
        | Count distinct complaints, not repeated visits.
        */


        /*
        |--------------------------------------------------------------------------
        | Verified High Urgency
        |--------------------------------------------------------------------------
        | Count distinct complaints, not repeated visits.
        | Use the urgency finalized by Customer Service.
        */

        $urgentCount = Complaint::query()
            ->whereIn('complaints.status', ['Assigned', 'In Progress'])
            ->whereHas('maintenanceWorkOrders', function ($query) use ($technicianId) {
                $query->whereIn('maintenance_work_orders.status', [
                    'Scheduled',
                    'In Progress',
                ])
                ->whereHas('technicians', function ($team) use ($technicianId) {
                    $team->where('users.id', $technicianId);
                });
            })
            ->whereHas('aiAnalysis', function (Builder $query) {
                $query->whereRaw(
                    'UPPER(TRIM(verified_urgency_level)) = ?',
                    ['HIGH']
                );
            })
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Six-Month Accomplishment Trend
        |--------------------------------------------------------------------------
        */

        $trendStart = $today->copy()->startOfMonth()->subMonths(5);
        $trendEnd = $today->copy()->endOfMonth();

        $monthlyResults = (clone $baseOrders)
            ->where('maintenance_work_orders.status', 'Completed')
            ->whereNotNull('maintenance_work_orders.completed_at')
            ->whereBetween('maintenance_work_orders.completed_at', [
                $trendStart,
                $trendEnd,
            ])
            ->selectRaw(
                "DATE_FORMAT(maintenance_work_orders.completed_at, '%Y-%m') AS month_key"
            )
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $trendLabels = [];
        $trendValues = [];

        for ($i = 0; $i < 6; $i++) {
            $month = $trendStart->copy()->addMonths($i);

            $trendLabels[] = $month->format('M Y');
            $trendValues[] = (int) (
                $monthlyResults[$month->format('Y-m')] ?? 0
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Active Maintenance Queue
        |--------------------------------------------------------------------------
        | High urgency first within each status group.
        */

        $currentWorkOrders = (clone $baseOrders)
            ->with([
                'complaint.consumer',
                'complaint.consumer.address',
                'complaint.category',
                'complaint.division',
                'complaint.aiAnalysis',
                'schedule',
            ])
            ->whereIn('maintenance_work_orders.status', [
                'Scheduled',
                'In Progress',
            ])
            ->whereHas('complaint', $activeComplaint)
            ->orderByRaw("
                CASE maintenance_work_orders.status
                    WHEN 'In Progress' THEN 1
                    WHEN 'Scheduled' THEN 2
                    ELSE 3
                END
            ")
            ->orderByRaw("
    CASE WHEN EXISTS (
        SELECT 1
        FROM complaints
        INNER JOIN complaint_ai_analyses
            ON complaint_ai_analyses.complaint_id = complaints.id
        WHERE complaints.id = maintenance_work_orders.complaint_id
        AND UPPER(TRIM(complaint_ai_analyses.verified_urgency_level)) = 'HIGH'
    ) THEN 0 ELSE 1 END
")
            ->orderBy('maintenance_work_orders.id')
            ->limit(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recently Accomplished Visits
        |--------------------------------------------------------------------------
        */

        $recentCompletedOrders = (clone $baseOrders)
            ->with([
                'complaint.consumer',
                'complaint.category',
                'complaint.aiAnalysis',
                'complaint.maintenanceReport',
            ])
            ->where('maintenance_work_orders.status', 'Completed')
            ->whereNotNull('maintenance_work_orders.completed_at')
            ->orderByDesc('maintenance_work_orders.completed_at')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Work Order Updates
        |--------------------------------------------------------------------------
        */

        $recentActivityOrders = (clone $baseOrders)
            ->with([
                'complaint.category',
                'complaint.consumer',
            ])
            ->orderByDesc('maintenance_work_orders.updated_at')
            ->limit(5)
            ->get();

        $statusLabels = [
            'Scheduled',
            'In Progress',
            'Pending Workload',
            'Completed',
        ];

        $statusValues = [
            $assignedCount,
            $inProgressCount,
            $pendingWorkloadCount,
            $completedCount,
        ];

        /*
        |--------------------------------------------------------------------------
        | My Submitted Accomplishment Reports
        |--------------------------------------------------------------------------
        | These are report records, not maintenance work-order visits.
        | Only the original submitting technician's reports are counted.
        */

        $myReports = MaintenanceReport::query()
            ->where('technician_id', $technicianId)
            ->whereNotNull('submitted_at');

        $reportTotalCount = (clone $myReports)->count();
        $reportPendingCount = (clone $myReports)
            ->where('review_status', 'Pending Review')->count();
        $reportApprovedCount = (clone $myReports)
            ->where('review_status', 'Approved')->count();
        $reportReturnedCount = (clone $myReports)
            ->where('review_status', 'Returned')->count();

        $recentSubmittedReports = (clone $myReports)
            ->with(['complaint.category'])
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return view('technician.dashboard', compact(
            'assignedCount',
            'inProgressCount',
            'pendingWorkloadCount',
            'historicalPendingCount',
            'completedCount',
            'activeCount',
            'totalCount',
            'urgentCount',
            'completionRate',
            'todayScheduledCount',
            'overdueCount',
            'trendLabels',
            'trendValues',
            'statusLabels',
            'statusValues',
            'currentWorkOrders',
            'recentCompletedOrders',
            'recentActivityOrders',
            'reportTotalCount',
            'reportPendingCount',
            'reportApprovedCount',
            'reportReturnedCount',
            'recentSubmittedReports'
        ));
    }
}
