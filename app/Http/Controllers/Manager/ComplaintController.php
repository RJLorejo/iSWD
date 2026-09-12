<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\AssignComplaintRequest;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{

    public function index(Request $request)
    {

        $query = Complaint::with([
            'consumer',
            'category',
            'customerService',
            'verifier',
            'technicians',
        ])->whereIn('status', [
            'Verified',
            'Assigned',
            'In Progress',
            'Completed',
            'Closed',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Search Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('complaint_no', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('consumer', function ($consumer) use ($search) {
                        $consumer->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('account_number', 'like', "%{$search}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        $allowedStatuses = [
            'For Assignment',
            'Verified',
            'Assigned',
            'In Progress',
            'Completed',
            'Closed',
        ];

        if (
            $request->filled('status') &&
            in_array($request->status, $allowedStatuses, true)
        ) {
            if ($request->status === 'For Assignment') {
                $query->where('status', 'Verified')
                    ->whereDoesntHave('technicians');
            } else {
                $query->where('status', $request->status);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        |
        | Filters complaints according to their creation date.
        |
        */

        if ($request->filled('date_from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Paginated Complaints
        |--------------------------------------------------------------------------
        */

        $complaints = $query
            ->latest('verified_at')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Status Statistics
        |--------------------------------------------------------------------------
        |
        | Existing statistics are preserved.
        |
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

        $completedCount = Complaint::where(
            'status',
            'Completed'
        )->count();

        $closedCount = Complaint::where(
            'status',
            'Closed'
        )->count();

        return view(
            'maintenance-manager.complaints.index',
            compact(
                'complaints',
                'verifiedCount',
                'assignedCount',
                'inProgressCount',
                'completedCount',
                'closedCount'
            )
        );
    }

    /**
     * Display complaint details.
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
     * Assign complaint to multiple Maintenance Technicians.
     */
    public function assign(
        AssignComplaintRequest $request,
        Complaint $complaint
    ) {
        /*
    |--------------------------------------------------------------------------
    | Workflow Validation
    |--------------------------------------------------------------------------
    */

        if ($complaint->status !== 'Verified') {
            return back()->with(
                'error',
                'Only verified complaints can be assigned to maintenance technicians.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Get Selected Technicians
    |--------------------------------------------------------------------------
    */

        $technicianIds = collect($request->input('technician_ids', []))
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($technicianIds->isEmpty()) {
            return back()->with(
                'error',
                'Please select at least one maintenance technician.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Verify Selected Users
    |--------------------------------------------------------------------------
    */

        $technicians = User::role('Maintenance Technician')
            ->where('is_active', true)
            ->whereIn('id', $technicianIds)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        if ($technicians->count() !== $technicianIds->count()) {
            return back()->with(
                'error',
                'One or more selected technicians are invalid or inactive.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Assign Maintenance Team
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use (
            $complaint,
            $technicians
        ) {

            $assignments = [];

            foreach ($technicians->values() as $index => $technician) {

                $assignments[$technician->id] = [

                    // Individual technician assignment status
                    'status' => 'Assigned',

                    'assigned_at' => now(),

                    'started_at' => null,

                    'completed_at' => null,
                ];
            }

            /*
        |--------------------------------------------------------------------------
        | Sync Technician Team
        |--------------------------------------------------------------------------
        |
        | This creates/updates the complaint_technicians records.
        |
        */

            $complaint->technicians()->sync($assignments);

            /*
        |--------------------------------------------------------------------------
        | Update Complaint Workflow
        |--------------------------------------------------------------------------
        |
        | The actual technician assignment is stored in
        | complaint_technicians, NOT complaints.assigned_to.
        |
        */

            $complaint->update([
                'technicians' => null,
                'status' => 'Assigned',
            ]);
        });

        return redirect()
            ->route(
                'maintenance-manager.complaints.show',
                $complaint
            )
            ->with(
                'success',
                $technicians->count()
                    . ' maintenance technician(s) successfully assigned to this complaint.'
            );
    }

    /**
     * Generate a printable report for a single complaint.
     */
    public function report(Complaint $complaint)
    {
        $complaint->load([
            'consumer',
            'category',
            'customerService',
            'verifier',
            'technicians',
            'maintenanceReport',
        ]);

        return view(
            'maintenance-manager.complaints.report',
            compact('complaint')
        );
    }
}
