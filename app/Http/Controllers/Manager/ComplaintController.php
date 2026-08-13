<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignComplaintRequest;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{
    /**
     * Display complaints waiting for technician assignment.
     */
    public function index(Request $request)
    {
        $query = Complaint::with([
            'consumer',
            'category',
            'customerService',
            'verifier',
            'technician',
        ])
            ->whereIn('status', [
                'Verified',
                'Assigned',
                'In Progress',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('complaint_no', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");

                $q->orWhereHas('consumer', function ($consumer) use ($search) {

                    $consumer
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('consumer_no', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Priority Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('priority')) {

            $query->where(
                'priority',
                $request->priority
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Complaints
        |--------------------------------------------------------------------------
        */

        $complaints = $query
            ->latest('verified_at')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $verifiedCount = Complaint::where(
            'status',
            'Verified'
        )->count();

        $assignedCount = Complaint::where(
            'status',
            'Assigned'
        )->count();

        $inProgressCount = Complaint::where(
            'status',
            'In Progress'
        )->count();

        $criticalCount = Complaint::where(
            'status',
            'Verified'
        )
            ->where(
                'priority',
                'Critical'
            )
            ->count();

        return view(
            'maintenance-manager.complaints.index',
            compact(
                'complaints',
                'verifiedCount',
                'assignedCount',
                'inProgressCount',
                'criticalCount'
            )
        );
    }


    public function show(Complaint $complaint)
    {
        $complaint->load([
            'consumer',
            'category',
            'customerService',
            'verifier',
            'technician',
        ]);

        $technicians = User::role('Maintenance Technician')
            ->where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view(
            'maintenance-manager.complaints.show',
            compact(
                'complaint',
                'technicians'
            )
        );
    }

    /**
     * Assign complaint to a Maintenance Technician.
     */
    public function assign(
        AssignComplaintRequest $request,
        Complaint $complaint
    ) {

        /*
        |--------------------------------------------------------------------------
        | Only Verified Complaints Can Be Assigned
        |--------------------------------------------------------------------------
        */

        if ($complaint->status !== 'Verified') {

            return back()->with(
                'error',
                'Only verified complaints can be assigned.'
            );
        }

        DB::transaction(function () use (
            $request,
            $complaint
        ) {

            $complaint->update([

                'assigned_to' =>
                $request->technician_id,

                'status' =>
                'Assigned',

            ]);
        });

        return redirect()
            ->route(
                'maintenance-manager.complaints.show',
                $complaint
            )
            ->with(
                'success',
                'Complaint assigned to the Maintenance Technician successfully.'
            );
    }
}
