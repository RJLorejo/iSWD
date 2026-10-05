<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $technicianId = Auth::id();

        $assignedCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->where('complaints.status', 'Assigned')
            ->count();

        $inProgressCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->where('complaints.status', 'In Progress')
            ->count();

        $urgentCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->whereIn('complaints.status', [
                'Assigned',
                'In Progress',
            ])
            ->whereHas('aiAnalysis', function (Builder $query) {
                $query->whereRaw(
                    'UPPER(urgency_level) = ?',
                    ['HIGH']
                );
            })
            ->count();

        $activeCount = $assignedCount + $inProgressCount;

        $completedCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->where('complaints.status', 'Completed')
            ->count();

        $totalCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->whereIn('complaints.status', [
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ])
            ->count();

        $currentComplaints = $this
            ->assignedComplaintsQuery($technicianId)
            ->with([
                'consumer.address',
                'division',
                'category',
                'technicians',
                'aiAnalysis',
                'commercialResolution',
            ])
            ->whereIn('complaints.status', [
                'Assigned',
                'In Progress',
            ])
            ->orderByRaw("
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM complaint_ai_analyses
                        WHERE complaint_ai_analyses.complaint_id = complaints.id
                        AND UPPER(complaint_ai_analyses.urgency_level) = 'HIGH'
                    ) THEN 1

                    WHEN EXISTS (
                        SELECT 1
                        FROM complaint_ai_analyses
                        WHERE complaint_ai_analyses.complaint_id = complaints.id
                        AND UPPER(complaint_ai_analyses.urgency_level) = 'MODERATE'
                    ) THEN 2

                    WHEN EXISTS (
                        SELECT 1
                        FROM complaint_ai_analyses
                        WHERE complaint_ai_analyses.complaint_id = complaints.id
                        AND UPPER(complaint_ai_analyses.urgency_level) = 'LOW'
                    ) THEN 3

                    ELSE 4
                END
            ")
            ->orderByRaw("
                CASE
                    WHEN complaints.status = 'In Progress' THEN 1
                    WHEN complaints.status = 'Assigned' THEN 2
                    ELSE 3
                END
            ")
            ->latest('complaints.updated_at')
            ->take(6)
            ->get();

        $recentCompleted = $this
            ->assignedComplaintsQuery($technicianId)
            ->with([
                'consumer',
                'division',
                'category',
                'technicians',
                'aiAnalysis',
                'maintenanceReport',
            ])
            ->where('complaints.status', 'Completed')
            ->latest('complaints.completed_at')
            ->latest('complaints.updated_at')
            ->take(5)
            ->get();

        $recentActivity = $this
            ->assignedComplaintsQuery($technicianId)
            ->with([
                'consumer',
                'division',
                'category',
                'technicians',
                'aiAnalysis',
            ])
            ->whereIn('complaints.status', [
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ])
            ->latest('complaints.updated_at')
            ->take(5)
            ->get();

        return view(
            'technician.dashboard',
            compact(
                'assignedCount',
                'inProgressCount',
                'urgentCount',
                'activeCount',
                'completedCount',
                'totalCount',
                'currentComplaints',
                'recentCompleted',
                'recentActivity'
            )
        );
    }

    private function assignedComplaintsQuery(
        int $technicianId
    ): Builder {
        return Complaint::query()
            ->whereHas(
                'technicians',
                function (Builder $query) use ($technicianId) {
                    $query->where(
                        'users.id',
                        $technicianId
                    );
                }
            );
    }
}
