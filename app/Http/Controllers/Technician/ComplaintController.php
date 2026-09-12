<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Http\Requests\Technician\CompleteComplaintRequest;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{
    /**
     * Display complaints assigned to the logged-in technician.
     */
    public function index(Request $request)
    {
        $technicianId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | BASE QUERY
        |--------------------------------------------------------------------------
        | complaint_technicians is now the source of truth for assignment.
        */
        $query = Complaint::with([
            'consumer',
            'category',
            'customerService',
            'verifier',
            'technicians',
            'maintenanceReport',
        ])
            ->withCount('technicians')
            ->whereHas('technicians', function ($q) use ($technicianId) {
                $q->where('users.id', $technicianId);
            })
            ->whereIn('status', [
                'Assigned',
                'In Progress',
                'Completed',
            ]);

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('complaint_no', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('landmark', 'like', "%{$search}%")

                    ->orWhereHas('consumer', function ($consumer) use ($search) {
                        $consumer
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('account_number', 'like', "%{$search}%");
                    })

                    ->orWhereHas('technicians', function ($technician) use ($search) {
                        $technician
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS FILTER
        |--------------------------------------------------------------------------
        */
        if ($request->filled('status')) {
            $allowedStatuses = [
                'Assigned',
                'In Progress',
                'Completed',
            ];

            if (in_array($request->status, $allowedStatuses, true)) {
                $query->where('status', $request->status);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PRIORITY FILTER
        |--------------------------------------------------------------------------
        */
        if ($request->filled('priority')) {
            $allowedPriorities = [
                'Low',
                'Medium',
                'High',
                'Critical',
            ];

            if (in_array($request->priority, $allowedPriorities, true)) {
                $query->where('priority', $request->priority);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ORDERING
        |--------------------------------------------------------------------------
        | Active work appears first, followed by priority.
        */
        $query
            ->orderByRaw("
                CASE
                    WHEN status = 'In Progress' THEN 1
                    WHEN status = 'Assigned' THEN 2
                    WHEN status = 'Completed' THEN 3
                    ELSE 4
                END
            ")
            ->orderByRaw("
                CASE
                    WHEN priority = 'Critical' THEN 1
                    WHEN priority = 'High' THEN 2
                    WHEN priority = 'Medium' THEN 3
                    WHEN priority = 'Low' THEN 4
                    ELSE 5
                END
            ")
            ->latest('updated_at');

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */
        $complaints = $query
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | STATISTICS
        |--------------------------------------------------------------------------
        */
        $assignedCount = $this->assignedComplaintsQuery($technicianId)
            ->where('status', 'Assigned')
            ->count();

        $inProgressCount = $this->assignedComplaintsQuery($technicianId)
            ->where('status', 'In Progress')
            ->count();

        $completedCount = $this->assignedComplaintsQuery($technicianId)
            ->where('status', 'Completed')
            ->count();

        $urgentCount = $this->assignedComplaintsQuery($technicianId)
            ->whereIn('priority', ['High', 'Critical'])
            ->whereIn('status', ['Assigned', 'In Progress'])
            ->count();

        $totalCount = $this->assignedComplaintsQuery($technicianId)
            ->count();

        $activeCount = $this->assignedComplaintsQuery($technicianId)
            ->whereIn('status', ['Assigned', 'In Progress'])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */
        return view(
            'technician.complaints.index',
            compact(
                'complaints',
                'assignedCount',
                'inProgressCount',
                'completedCount',
                'urgentCount',
                'totalCount',
                'activeCount'
            )
        );
    }


    /**
     * Display a complaint assigned to the technician.
     */
    public function show(Complaint $complaint)
    {
        $complaint->load([
            'consumer',
            'category',
            'customerService',
            'verifier',
            'technicians',
            'maintenanceReport',
        ]);

        $isAssignedToMe = $complaint->technicians()
            ->where('users.id', auth()->id())
            ->exists();

        abort_unless($isAssignedToMe, 403);

        return view(
            'technician.complaints.show',
            compact('complaint')
        );
    }


    /**
     * Start maintenance work.
     */
    public function start(Complaint $complaint)
    {
        $this->ensureAssignedToMe($complaint);

        if ($complaint->status !== 'Assigned') {
            return back()->with(
                'error',
                'Only assigned complaints can be started.'
            );
        }

        DB::transaction(function () use ($complaint) {

            $complaint->update([
                'status' => 'In Progress',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update this technician's assignment record
            |--------------------------------------------------------------------------
            */
            $complaint->technicians()->updateExistingPivot(
                Auth::id(),
                [
                    'status' => 'In Progress',
                    'started_at' => now(),
                ]
            );
        });

        return back()->with(
            'success',
            'Maintenance work has been started successfully.'
        );
    }


    /**
     * Complete maintenance work.
     *
     * This action should only be available after the technician
     * has completed the required maintenance report.
     */
    public function complete(
        CompleteComplaintRequest $request,
        Complaint $complaint
    ) {
        $this->ensureAssignedToMe($complaint);

        if ($complaint->status !== 'In Progress') {
            return back()->with(
                'error',
                'Only complaints currently in progress can be completed.'
            );
        }

        DB::transaction(function () use ($complaint) {

            /*
            |--------------------------------------------------------------------------
            | Mark this technician's assignment as completed
            |--------------------------------------------------------------------------
            */
            $complaint->technicians()->updateExistingPivot(
                Auth::id(),
                [
                    'status' => 'Completed',
                    'completed_at' => now(),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Complaint becomes Completed
            |--------------------------------------------------------------------------
            |
            | Manager can now review/validate the maintenance report.
            |
            */
            $complaint->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);
        });

        return redirect()
            ->route(
                'technician.complaints.show',
                $complaint
            )
            ->with(
                'success',
                'Maintenance work has been marked as completed and is now ready for manager review.'
            );
    }


    /**
     * Make sure the logged-in technician is assigned to the complaint.
     */
    private function ensureAssignedToMe(Complaint $complaint): void
    {
        abort_unless(
            $complaint->technicians()
                ->where('users.id', Auth::id())
                ->exists(),
            403
        );
    }


    /**
     * Reusable assigned-complaint query.
     */
    private function assignedComplaintsQuery(int $technicianId)
    {
        return Complaint::whereHas(
            'technicians',
            function ($q) use ($technicianId) {
                $q->where('users.id', $technicianId);
            }
        );
    }
}
