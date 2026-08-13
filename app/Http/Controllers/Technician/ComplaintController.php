<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
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
        */

        $query = Complaint::with([
            'consumer',
            'category',
            'customerService',
            'verifier',
            'technician',
        ])
            ->where('assigned_to', $technicianId)
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
                    ->orWhere('landmark', 'like', "%{$search}%");

                $q->orWhereHas('consumer', function ($consumer) use ($search) {

                    $consumer
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('consumer_no', 'like', "%{$search}%");
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

                $query->where(
                    'status',
                    $request->status
                );
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

                $query->where(
                    'priority',
                    $request->priority
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ORDERING
        |--------------------------------------------------------------------------
        */

        $query->orderByRaw("
            CASE
                WHEN status = 'In Progress' THEN 1
                WHEN status = 'Assigned' THEN 2
                WHEN status = 'Completed' THEN 3
                ELSE 4
            END
        ");

        $query->orderByRaw("
            CASE
                WHEN priority = 'Critical' THEN 1
                WHEN priority = 'High' THEN 2
                WHEN priority = 'Medium' THEN 3
                WHEN priority = 'Low' THEN 4
                ELSE 5
            END
        ");

        $query->latest('updated_at');

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

        $assignedCount = Complaint::where(
            'assigned_to',
            $technicianId
        )
            ->where('status', 'Assigned')
            ->count();

        $inProgressCount = Complaint::where(
            'assigned_to',
            $technicianId
        )
            ->where('status', 'In Progress')
            ->count();

        $completedCount = Complaint::where(
            'assigned_to',
            $technicianId
        )
            ->where('status', 'Completed')
            ->count();

        $urgentCount = Complaint::where(
            'assigned_to',
            $technicianId
        )
            ->whereIn('priority', [
                'High',
                'Critical',
            ])
            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])
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
                'urgentCount'
            )
        );
    }


    /**
     * Display a complaint assigned to the technician.
     */
    public function show(Complaint $complaint)
    {
        /*
        |--------------------------------------------------------------------------
        | SECURITY
        |--------------------------------------------------------------------------
        */

        abort_unless(
            (int) $complaint->assigned_to === (int) Auth::id(),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | LOAD RELATIONSHIPS
        |--------------------------------------------------------------------------
        */

        $complaint->load([
            'consumer',
            'category',
            'customerService',
            'verifier',
            'technician',
        ]);

        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

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
        /*
        |--------------------------------------------------------------------------
        | SECURITY
        |--------------------------------------------------------------------------
        */

        abort_unless(
            (int) $complaint->assigned_to === (int) Auth::id(),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if ($complaint->status !== 'Assigned') {

            return back()->with(
                'error',
                'Only assigned complaints can be started.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        $complaint->update([
            'status' => 'In Progress',
        ]);

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            'Maintenance work has been started successfully.'
        );
    }


    /**
     * Complete maintenance work.
     */
    public function complete(Complaint $complaint)
    {
        /*
        |--------------------------------------------------------------------------
        | SECURITY
        |--------------------------------------------------------------------------
        */

        abort_unless(
            (int) $complaint->assigned_to === (int) Auth::id(),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if ($complaint->status !== 'In Progress') {

            return back()->with(
                'error',
                'Only complaints currently in progress can be completed.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($complaint) {

            $complaint->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'technician.complaints.show',
                $complaint
            )
            ->with(
                'success',
                'Maintenance work has been marked as completed.'
            );
    }
}
