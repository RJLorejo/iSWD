<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $technicianId = Auth::id();

        $query = Complaint::query()
            ->with([
                'consumer',
                'division',
                'category',
                'customerService',
                'verifier',
                'technicians',
                'maintenanceReport',
            ])
            ->withCount('technicians')
            ->whereHas('technicians', function ($query) use ($technicianId) {
                $query->where('users.id', $technicianId);
            })
            ->whereIn('status', [
                'Assigned',
                'In Progress',
                'Accomplished',
                'Completed',
            ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($query) use ($search) {
                $query
                    ->where('complaint_no', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('landmark', 'like', "%{$search}%")
                    ->orWhereHas('consumer', function ($consumer) use ($search) {
                        $consumer
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('account_number', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($category) use ($search) {
                        $category
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('division', function ($division) use ($search) {
                        $division->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $allowedStatuses = [
                'Assigned',
                'In Progress',
                'Accomplished',
                'Completed',
            ];

            if (in_array($request->status, $allowedStatuses, true)) {
                $query->where('status', $request->status);
            }
        }

        $query
            ->orderByRaw("
                CASE
                    WHEN status = 'In Progress' THEN 1
                    WHEN status = 'Assigned' THEN 2
                    WHEN status = 'Accomplished' THEN 3
                    WHEN status = 'Completed' THEN 4
                    ELSE 5
                END
            ")
            ->latest('updated_at');

        $complaints = $query
            ->paginate(10)
            ->withQueryString();

        $assignedCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->where('status', 'Assigned')
            ->count();

        $inProgressCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->where('status', 'In Progress')
            ->count();

        $accomplishedCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->where('status', 'Accomplished')
            ->count();

        $completedCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->where('status', 'Completed')
            ->count();

        $urgentCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->whereIn('priority', [
                'High',
                'Critical',
            ])
            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])
            ->count();

        $activeCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])
            ->count();

        $totalCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->whereIn('status', [
                'Assigned',
                'In Progress',
                'Accomplished',
                'Completed',
            ])
            ->count();

        return view(
            'technician.complaints.index',
            compact(
                'complaints',
                'assignedCount',
                'inProgressCount',
                'accomplishedCount',
                'completedCount',
                'urgentCount',
                'activeCount',
                'totalCount'
            )
        );
    }

    public function show(Complaint $complaint)
    {
        $isAssignedToMe = $complaint
            ->technicians()
            ->where('users.id', Auth::id())
            ->exists();

        abort_unless($isAssignedToMe, 403);

        $complaint->load([
            'consumer',
            'division',
            'category',
            'customerService',
            'verifier',
            'technicians',
            'maintenanceReport',
        ]);

        return view(
            'technician.complaints.show',
            compact('complaint')
        );
    }

    private function assignedComplaintsQuery(int $technicianId)
    {
        return Complaint::query()
            ->whereHas('technicians', function ($query) use ($technicianId) {
                $query->where('users.id', $technicianId);
            });
    }
}
