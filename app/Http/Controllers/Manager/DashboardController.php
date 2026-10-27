<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\MaintenanceReport;
use App\Models\MaintenanceWorkOrder;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = CarbonImmutable::now('Asia/Manila')->toDateString();
        /*
|--------------------------------------------------------------------------
| Complaint Summary
|--------------------------------------------------------------------------
*/

        $eligibleComplaints = Complaint::query()
            ->where('complaints.status', 'Verified')
            ->whereHas('division', function ($query) {
                $query->whereIn('name', [
                    'Engineering',
                    'Customer Service',
                ]);
            });

        $verifiedComplaints = (clone $eligibleComplaints)->count();

        $unassignedCases = (clone $eligibleComplaints)
            ->whereDoesntHave('technicians')
            ->count();

        $assignedCases = Complaint::query()
            ->where('status', 'Assigned')
            ->count();

        $inProgressComplaints = Complaint::query()
            ->where('status', 'In Progress')
            ->count();

        $reportsForReview = MaintenanceReport::query()
            ->where('review_status', 'Pending Review')
            ->count();

        $completedToday = Complaint::query()
            ->where('status', 'Completed')
            ->whereDate('completed_at', $today)
            ->count();

        $technicianCount = User::role('Maintenance Technician')
            ->where('is_active', true)
            ->count();

        /*
|--------------------------------------------------------------------------
| New & Verified Complaints
|--------------------------------------------------------------------------
| Display only verified Engineering and Customer Service complaints
| that have no technician assignment.
*/

        $recentVerifiedComplaints = (clone $eligibleComplaints)
            ->whereDoesntHave('technicians')
            ->with([
                'consumer',
                'category',
                'aiAnalysis',
                'division',
            ])
            ->latest('complaints.created_at')
            ->take(5)
            ->get();
        /*
        |--------------------------------------------------------------------------
        | Scheduling Workflow Counts
        |--------------------------------------------------------------------------
        | These supplement the existing cards and can also be used by charts.
        | A Draft Addition belongs to a finalized schedule but is not yet
        | approved for technician dispatch.
        */

        $draftWorkOrders = MaintenanceWorkOrder::query()
            ->where('status', 'Scheduled')
            ->where('approval_status', 'Draft')
            ->count();

        $pendingWorkloadCount = MaintenanceWorkOrder::query()
            ->where('status', 'Pending Workload')
            ->count();

        $approvedScheduledCount = MaintenanceWorkOrder::query()
            ->where('status', 'Scheduled')
            ->where('approval_status', 'Approved')
            ->count();

        $activeWorkOrderCount = MaintenanceWorkOrder::query()
            ->whereIn('status', ['In Progress'])
            ->count();



        /*
        |--------------------------------------------------------------------------
        | Active Maintenance
        |--------------------------------------------------------------------------
        | Exclude complaints that have already completed or closed.
        */

        $activeComplaints = Complaint::query()
            ->with([
                'consumer',
                'category',
                'technicians',
                'aiAnalysis',
                'workOrders.schedule',
                'workOrders.technicians',
            ])
            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])
            ->whereDoesntHave('workOrders', function ($query) {
                $query->where('status', 'Completed');
            })
            ->latest('created_at')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Technician Workload
        |--------------------------------------------------------------------------
        | Count active work orders assigned to each technician rather than
        | relying solely on complaint-level assignments. This reflects the
        | actual maintenance workload, including multiple jobs per day.
        */

        /*
|--------------------------------------------------------------------------
| Technician Workload
|--------------------------------------------------------------------------
| Count active maintenance work orders directly from the database.
| This avoids depending on an undefined User relationship.
*/

        $technicians = User::role('Maintenance Technician')
            ->where('is_active', true)
            ->get();

        $technicianIds = $technicians->pluck('id');

        $workloadCounts = collect();

        if ($technicianIds->isNotEmpty()) {
            $workloadCounts = DB::table('maintenance_work_order_technicians as mwot')
                ->join(
                    'maintenance_work_orders as mwo',
                    'mwo.id',
                    '=',
                    'mwot.maintenance_work_order_id'
                )
                ->whereIn('mwot.technician_id', $technicianIds)
                ->whereIn('mwo.status', [
                    'Scheduled',
                    'In Progress',
                    'Pending Workload',
                ])
                ->select(
                    'mwot.technician_id',
                    DB::raw('COUNT(DISTINCT mwo.id) as active_complaints_count')
                )
                ->groupBy('mwot.technician_id')
                ->pluck('active_complaints_count', 'mwot.technician_id');
        }

        $technicians = $technicians
            ->each(function ($technician) use ($workloadCounts) {
                $technician->active_complaints_count = (int) (
                    $workloadCounts[$technician->id] ?? 0
                );
            })
            ->sortByDesc('active_complaints_count')
            ->take(5)
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Case Status Chart Data
        |--------------------------------------------------------------------------
        | Preserve the original chart variable for existing JavaScript.
        */

        $caseStatusChart = [
            'labels' => [
                'Verified',
                'Unassigned',
                'Assigned',
                'In Progress',
                'For Review',
                'Completed Today',
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
        | Current Scheduling Chart Data
        |--------------------------------------------------------------------------
        | These values reflect the new work-order approval workflow.
        */

        $maintenanceWorkflowChart = [
            'labels' => [
                'Draft / Draft Additions',
                'Approved / Scheduled',
                'In Progress',
                'Pending Workload',
                'Completed',
            ],
            'data' => [
                $draftWorkOrders,
                $approvedScheduledCount,
                $activeWorkOrderCount,
                $pendingWorkloadCount,
                MaintenanceWorkOrder::query()
                    ->where('status', 'Completed')
                    ->count(),
            ],
        ];

        return view('maintenance-manager.dashboard', compact(
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
            'caseStatusChart',
            'maintenanceWorkflowChart',
            'draftWorkOrders',
            'pendingWorkloadCount',
            'approvedScheduledCount'
        ));
    }
}
