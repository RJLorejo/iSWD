<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Http\Requests\Technician\StoreMaintenanceReportRequest;
use App\Models\Complaint;
use App\Models\MaintenanceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\MaintenanceWorkOrder;
use App\Models\MaintenanceScheduleHistory;
use Illuminate\Validation\ValidationException;


class MaintenanceReportController extends Controller
{

    public function index(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $technicianId = (int) Auth::id();

        $from = $request->filled('from')
            ? Carbon::parse($request->from, 'Asia/Manila')->startOfDay()
            : now('Asia/Manila')->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to, 'Asia/Manila')->endOfDay()
            : now('Asia/Manila')->endOfDay();

        /*
    |--------------------------------------------------------------------------
    | Assigned Maintenance Work Orders
    |--------------------------------------------------------------------------
    |
    | Count individual maintenance visits rather than complaint records.
    | Include only work orders assigned to the logged-in technician.
    |
    */

        $baseWorkOrders = MaintenanceWorkOrder::query()
            ->whereHas('technicians', function ($query) use ($technicianId) {
                $query->where('users.id', $technicianId);
            });

        /*
    |--------------------------------------------------------------------------
    | Current Work Order Status
    |--------------------------------------------------------------------------
    */

        // A Draft Addition is not an approved scheduled visit.
        $draftCount = (clone $baseWorkOrders)
            ->where('maintenance_work_orders.status', 'Scheduled')
            ->where('maintenance_work_orders.approval_status', 'Draft')
            ->count();

        $assignedCount = (clone $baseWorkOrders)
            ->where('maintenance_work_orders.status', 'Scheduled')
            ->where('maintenance_work_orders.approval_status', 'Approved')
            ->whereHas('schedule', function ($query) {
                $query->where('status', 'Finalized');
            })
            ->count();

        $inProgressCount = (clone $baseWorkOrders)
            ->where('maintenance_work_orders.status', 'In Progress')
            ->count();

        $pendingWorkloadCount = (clone $baseWorkOrders)
            ->where('maintenance_work_orders.status', 'Pending Workload')
            ->whereDoesntHave('complaint', function ($complaintQuery) {
                $complaintQuery->whereIn('status', ['Completed', 'Closed']);
            })
            ->count();

        $activeCount = $assignedCount + $inProgressCount;

        /*
    |--------------------------------------------------------------------------
    | Completed Maintenance Visits
    |--------------------------------------------------------------------------
    |
    | Count completed work orders using their completion dates.
    | Complaint closure does not create another completed visit.
    |
    */

        $completedWorkOrders = (clone $baseWorkOrders)
            ->with([
                'complaint.consumer',
                'complaint.consumer.address',
                'complaint.category',
                'complaint.division',
                'complaint.technicians',
                'complaint.maintenanceReport',
                'complaint.maintenanceReport.technician',
                'complaint.aiAnalysis',
                'complaint.commercialResolution',
                'schedule',
            ])
            ->where('maintenance_work_orders.status', 'Completed')
            ->whereNotNull('maintenance_work_orders.completed_at')
            ->whereBetween(
                'maintenance_work_orders.completed_at',
                [$from, $to]
            )
            ->orderByDesc('maintenance_work_orders.completed_at')
            ->orderByDesc('maintenance_work_orders.id')
            ->get();

        $completedCount = $completedWorkOrders->count();

        // Keep exact completed visit timestamps for the Accomplished Work table.
        $completedVisitDates = $completedWorkOrders
            ->groupBy('complaint_id')
            ->map(fn ($orders) => $orders->first()->completed_at);

        /*
    |--------------------------------------------------------------------------
    | Closed Complaints
    |--------------------------------------------------------------------------
    |
    | This is a separate complaint-level metric.
    | It must not be added to completed work-order totals.
    |
    */

        $closedCount = Complaint::query()
            ->where('complaints.status', 'Closed')
            ->whereHas('maintenanceWorkOrders', function ($query) use ($technicianId) {
                $query->whereHas('technicians', function ($technicianQuery) use ($technicianId) {
                    $technicianQuery->where('users.id', $technicianId);
                });
            })
            ->whereNotNull('complaints.completed_at')
            ->whereBetween('complaints.completed_at', [$from, $to])
            ->count();

        $accomplishedCount = $completedCount;

        /*
    |--------------------------------------------------------------------------
    | Completion Rate
    |--------------------------------------------------------------------------
    |
    | Work orders completed in the selected period compared with
    | completed visits plus the current assigned/in-progress workload.
    | Pending workload is included only when unresolved.
    |
    */

        $totalAssigned = $assignedCount
            + $inProgressCount
            + $pendingWorkloadCount
            + $completedCount;

        $completionRate = $totalAssigned > 0
            ? round(($completedCount / $totalAssigned) * 100, 1)
            : 0;

        /*
    |--------------------------------------------------------------------------
    | Average Maintenance Completion Time
    |--------------------------------------------------------------------------
    |
    | Use actual maintenance start and completion timestamps,
    | not the date when the consumer originally filed a complaint.
    |
    */

        $totalMinutes = 0;
        $validRecords = 0;

        foreach ($completedWorkOrders as $workOrder) {

            if (!$workOrder->started_at || !$workOrder->completed_at) {
                continue;
            }

            $minutes = $workOrder->started_at->diffInMinutes(
                $workOrder->completed_at,
                false
            );

            if ($minutes < 0) {
                continue;
            }

            $totalMinutes += $minutes;
            $validRecords++;
        }

        $averageCompletionMinutes = $validRecords > 0
            ? round($totalMinutes / $validRecords, 1)
            : null;

        $averageCompletionHours = $averageCompletionMinutes !== null
            ? round($averageCompletionMinutes / 60, 2)
            : null;

        /*
    |--------------------------------------------------------------------------
    | Current Complaints
    |--------------------------------------------------------------------------
    |
    | Preserve the existing Blade structure, which loops through
    | Complaint models rather than MaintenanceWorkOrder models.
    |
    | A complaint is displayed once, even if it has multiple visits.
    |
    */

        $currentComplaints = Complaint::query()
            ->with([
                'consumer',
                'consumer.address',
                'category',
                'division',
                'technicians',
                'maintenanceReport',
                'aiAnalysis',
                'commercialResolution',
            ])
            ->whereIn('complaints.status', ['Assigned', 'In Progress'])
            ->whereHas('maintenanceWorkOrders', function ($query) use ($technicianId) {
                $query
                    ->whereIn('maintenance_work_orders.status', [
                        'Scheduled',
                        'In Progress',
                    ])
                    ->whereHas('technicians', function ($technicianQuery) use ($technicianId) {
                        $technicianQuery->where('users.id', $technicianId);
                    });
            })
            ->orderByRaw("
            CASE complaints.status
                WHEN 'In Progress' THEN 1
                WHEN 'Assigned' THEN 2
                ELSE 3
            END
        ")
            ->orderByDesc('complaints.updated_at')
            ->orderByDesc('complaints.id')
            ->get();

        // The current-work table must show the latest work-order approval,
        // rather than relying only on the complaint's Assigned status.
        $currentWorkOrders = (clone $baseWorkOrders)
            ->with('schedule')
            ->whereIn('maintenance_work_orders.status', ['Scheduled', 'In Progress'])
            ->orderByDesc('visit_number')
            ->orderByDesc('id')
            ->get()
            ->unique('complaint_id')
            ->keyBy('complaint_id');


        /*
        |--------------------------------------------------------------------------
        | Customer Service Verified Urgency Breakdown
        |--------------------------------------------------------------------------
        |
        | Use the urgency finalized by Customer Service rather than
        | the initial AI-generated urgency level.
        |
        */

        $highUrgencyCount = $currentComplaints
            ->filter(
                fn ($complaint) =>
                strtoupper(trim(
                    (string) ($complaint->aiAnalysis?->verified_urgency_level ?? '')
                )) === 'HIGH'
            )
            ->count();

        $moderateUrgencyCount = $currentComplaints
            ->filter(
                fn ($complaint) =>
                strtoupper(trim(
                    (string) ($complaint->aiAnalysis?->verified_urgency_level ?? '')
                )) === 'MODERATE'
            )
            ->count();

        $lowUrgencyCount = $currentComplaints
            ->filter(
                fn ($complaint) =>
                strtoupper(trim(
                    (string) ($complaint->aiAnalysis?->verified_urgency_level ?? '')
                )) === 'LOW'
            )
            ->count();

        $notAssessedCount = $currentComplaints
            ->filter(
                fn ($complaint) =>
                !in_array(
                    strtoupper(trim(
                        (string) ($complaint->aiAnalysis?->verified_urgency_level ?? '')
                    )),
                    ['HIGH', 'MODERATE', 'LOW'],
                    true
                )
            )
            ->count();

        $urgentCount = $highUrgencyCount;

        $urgencyBreakdown = collect([
            'High' => $highUrgencyCount,
            'Moderate' => $moderateUrgencyCount,
            'Low' => $lowUrgencyCount,
            'Not Assessed' => $notAssessedCount,
        ]);


        /*
    |--------------------------------------------------------------------------
    | Status Breakdown
    |--------------------------------------------------------------------------
    */

        $statusBreakdown = collect([
            'Draft' => $draftCount,
            'Scheduled' => $assignedCount,
            'In Progress' => $inProgressCount,
            'Pending Workload' => $pendingWorkloadCount,
            'Completed' => $completedCount,
        ]);

        /*
    |--------------------------------------------------------------------------
    | Accomplished Complaint List
    |--------------------------------------------------------------------------
    |
    | The existing Blade expects Complaint objects.
    | Show each complaint once even when multiple work orders exist.
    |
    */

        $completedComplaintIds = $completedWorkOrders
            ->pluck('complaint_id')
            ->unique()
            ->values();

        $completedComplaintsList = Complaint::query()
            ->with([
                'consumer',
                'consumer.address',
                'category',
                'division',
                'technicians',
                'maintenanceReport',
                'maintenanceReport.technician',
                'aiAnalysis',
                'commercialResolution',
            ])
            ->whereIn('complaints.id', $completedComplaintIds)
            ->orderByDesc('complaints.completed_at')
            ->orderByDesc('complaints.id')
            ->get();

        // Participation is separate from completed-visit ownership. An outgoing
        // plumber can have a Handed Over record without being counted as the
        // technician who completed the work order.
        $participationHistory = \App\Models\MaintenanceWorkOrderParticipation::query()
            ->with(['workOrder.complaint', 'workOrder.schedule'])
            ->where('technician_id', $technicianId)
            ->where(function ($query) use ($from, $to) {
                $query->whereBetween('started_at', [$from, $to])
                    ->orWhereBetween('ended_at', [$from, $to]);
            })
            ->orderByDesc('started_at')
            ->limit(100)
            ->get();

        return view('technician.reports.maintenance', compact(
            'from',
            'to',
            'totalAssigned',
            'assignedCount',
            'draftCount',
            'inProgressCount',
            'pendingWorkloadCount',
            'completedCount',
            'closedCount',
            'accomplishedCount',
            'urgentCount',
            'activeCount',
            'completionRate',
            'averageCompletionHours',
            'urgencyBreakdown',
            'notAssessedCount',
            'statusBreakdown',
            'currentComplaints',
            'currentWorkOrders',
            'participationHistory',
            'completedComplaintsList',
            'completedVisitDates',
            'averageCompletionHours',
            'averageCompletionMinutes'
        ));
    }


    /**
     * Searchable history of accomplishment reports submitted by this technician.
     * This is report-level, not work-order-level: one row per report.
     */
    public function history(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:all,Pending Review,Approved,Returned'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $technicianId = (int) Auth::id();
        $base = MaintenanceReport::query()
            ->where('technician_id', $technicianId)
            ->whereNotNull('submitted_at')
            ->whereIn('review_status', ['Pending Review', 'Approved', 'Returned']);

        $statistics = [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('review_status', 'Pending Review')->count(),
            'approved' => (clone $base)->where('review_status', 'Approved')->count(),
            'returned' => (clone $base)->where('review_status', 'Returned')->count(),
        ];

        $reportsQuery = (clone $base)->with([
            'complaint.category',
            'complaint.division',
            'complaint.consumer',
        ]);

        if (filled($validated['search'] ?? null)) {
            $term = trim($validated['search']);
            $reportsQuery->whereHas('complaint', function ($query) use ($term) {
                $query->where(function ($subQuery) use ($term) {
                    $subQuery->where('complaint_no', 'like', '%' . $term . '%')
                        ->orWhereHas('category', function ($categoryQuery) use ($term) {
                            $categoryQuery->where('name', 'like', '%' . $term . '%');
                        });
                });
            });
        }

        if (filled($validated['status'] ?? null) && $validated['status'] !== 'all') {
            $reportsQuery->where('review_status', $validated['status']);
        }
        if (filled($validated['from'] ?? null)) {
            $reportsQuery->whereDate('submitted_at', '>=', $validated['from']);
        }
        if (filled($validated['to'] ?? null)) {
            $reportsQuery->whereDate('submitted_at', '<=', $validated['to']);
        }

        $reports = $reportsQuery
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('technician.reports.history', compact('reports', 'statistics'));
    }

    /** Print all matching submitted reports, without pagination. */
    public function printHistory(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:all,Pending Review,Approved,Returned'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $query = MaintenanceReport::query()
            ->where('technician_id', (int) Auth::id())
            ->whereNotNull('submitted_at')
            ->whereIn('review_status', ['Pending Review', 'Approved', 'Returned'])
            ->with(['complaint.category', 'complaint.division', 'complaint.consumer']);

        if (filled($validated['search'] ?? null)) {
            $term = trim($validated['search']);
            $query->whereHas('complaint', function ($complaintQuery) use ($term) {
                $complaintQuery->where(function ($nested) use ($term) {
                    $nested->where('complaint_no', 'like', '%' . $term . '%')
                        ->orWhereHas('category', function ($categoryQuery) use ($term) {
                            $categoryQuery->where('name', 'like', '%' . $term . '%');
                        });
                });
            });
        }

        if (filled($validated['status'] ?? null) && $validated['status'] !== 'all') {
            $query->where('review_status', $validated['status']);
        }
        if (filled($validated['from'] ?? null)) {
            $query->whereDate('submitted_at', '>=', $validated['from']);
        }
        if (filled($validated['to'] ?? null)) {
            $query->whereDate('submitted_at', '<=', $validated['to']);
        }

        $reports = $query->orderByDesc('submitted_at')->orderByDesc('id')->get();
        $counts = [
            'total' => $reports->count(),
            'pending' => $reports->where('review_status', 'Pending Review')->count(),
            'approved' => $reports->where('review_status', 'Approved')->count(),
            'returned' => $reports->where('review_status', 'Returned')->count(),
        ];

        return view('technician.reports.history-print', compact('reports', 'counts', 'validated'));
    }

    public function startWorkOrder(
        \App\Models\Complaint $complaint,
        \App\Models\MaintenanceWorkOrder $workOrder
    ) {
        $technicianId = (int) auth()->id();

        DB::transaction(function () use (
            $complaint,
            $workOrder,
            $technicianId
        ) {
            $lockedComplaint = \App\Models\Complaint::query()
                ->lockForUpdate()
                ->findOrFail($complaint->id);

            $lockedOrder = \App\Models\MaintenanceWorkOrder::query()
                ->lockForUpdate()
                ->findOrFail($workOrder->id);

            if ((int) $lockedOrder->complaint_id !== (int) $lockedComplaint->id) {
                abort(404);
            }


            if (
                !in_array(
                    $lockedComplaint->status,
                    ['Assigned', 'In Progress'],
                    true
                ) ||
                $lockedOrder->status !== 'Scheduled'
            ) {


                throw ValidationException::withMessages([
                    'maintenance' =>
                    'This work order is not available to start.',
                ]);
            }

            /*
|--------------------------------------------------------------------------
| Prevent Starting an Outdated Work Order
|--------------------------------------------------------------------------
|
| A newer Scheduled or In Progress visit takes priority.
| Older work orders must not be started after a newer
| maintenance visit has been created.
|
*/

            $hasNewerActiveVisit = MaintenanceWorkOrder::query()
                ->where('complaint_id', $lockedComplaint->id)
                ->where(function ($query) use ($lockedOrder) {
                    $query
                        ->where('visit_number', '>', $lockedOrder->visit_number)
                        ->orWhere(function ($sameVisit) use ($lockedOrder) {
                            $sameVisit
                                ->where('visit_number', $lockedOrder->visit_number)
                                ->where('id', '>', $lockedOrder->id);
                        });
                })
                ->whereIn('status', [
                    'Scheduled',
                    'In Progress',
                    'Completed',
                    'Pending Workload',
                ])
                ->exists();

            if ($hasNewerActiveVisit) {
                throw ValidationException::withMessages([
                    'maintenance' =>
                    'A newer maintenance visit already exists. '
                        . 'This older work order can no longer be started.',
                ]);
            }


            if ($lockedComplaint->status === 'In Progress') {
                $previousOrder = MaintenanceWorkOrder::query()
                    ->where('complaint_id', $lockedComplaint->id)
                    ->where('id', '!=', $lockedOrder->id)
                    ->where('visit_number', '<', $lockedOrder->visit_number)
                    ->orderByDesc('visit_number')
                    ->orderByDesc('id')
                    ->first();

                if (
                    !$previousOrder ||
                    $previousOrder->status !== 'Pending Workload' ||
                    !filled($previousOrder->pending_reason) ||
                    !$previousOrder->pending_recorded_at
                ) {
                    throw ValidationException::withMessages([
                        'maintenance' =>
                        'The previous maintenance visit must have a recorded Pending Workload reason before starting a follow-up.',
                    ]);
                }
            }

            $isAssigned = DB::table('maintenance_work_order_technicians')
                ->where('maintenance_work_order_id', $lockedOrder->id)
                ->where('technician_id', $technicianId)
                ->exists();

            abort_unless($isAssigned, 403);
            $this->assertCurrentTeamMember($lockedOrder, $technicianId);
            $this->assertLatestExecutableVisit($lockedOrder);

            if ($lockedOrder->approval_status !== 'Approved') {
                throw ValidationException::withMessages([
                    'maintenance' => 'This work order is awaiting Maintenance Manager approval.',
                ]);
            }

            $schedule = \App\Models\MaintenanceSchedule::query()
                ->lockForUpdate()
                ->find($lockedOrder->maintenance_schedule_id);

            if (!$schedule || $schedule->status !== 'Finalized') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'maintenance' => 'The daily maintenance schedule has not been finalized.',
                ]);
            }

            $today = now('Asia/Manila')->toDateString();

            $scheduledDate = \Carbon\CarbonImmutable::parse(
                $schedule->schedule_date,
                'Asia/Manila'
            )->toDateString();

            if ($scheduledDate !== $today) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'maintenance' => $scheduledDate > $today
                        ? "This work order is scheduled for {$scheduledDate}. It cannot be started early."
                        : 'The scheduled date has passed. Contact the Maintenance Manager for rescheduling.',
                ]);
            }
            $previousComplaintStatus = $lockedComplaint->status;
            $startedAt = now('Asia/Manila');

            $lockedOrder->update([
                'status' => 'In Progress',
                'started_at' => $startedAt,
            ]);

            // Record actual participation by the plumber who started this visit.
            \App\Models\MaintenanceWorkOrderParticipation::query()->firstOrCreate(
                [
                    'maintenance_work_order_id' => $lockedOrder->id,
                    'technician_id' => $technicianId,
                ],
                [
                    'status' => 'Active',
                    'started_at' => $startedAt,
                    'recorded_by' => $technicianId,
                ]
            );

            DB::table('maintenance_work_sessions')->insert([
                'maintenance_work_order_id' => $lockedOrder->id,
                'technician_id' => $technicianId,
                'started_at' => $startedAt,
                'created_at' => $startedAt,
                'updated_at' => $startedAt,
            ]);

            $lockedComplaint->update([
                'status' => 'In Progress',
            ]);

            DB::table('complaint_technicians')
                ->where('complaint_id', $lockedComplaint->id)
                ->where('technician_id', $technicianId)
                ->where('status', 'Assigned')
                ->update([
                    'status' => 'In Progress',
                    'started_at' => $startedAt,
                    'updated_at' => $startedAt,
                ]);



            \App\Models\MaintenanceScheduleHistory::create([
                'maintenance_work_order_id' => $lockedOrder->id,
                'performed_by' => $technicianId,
                'action' => 'Maintenance Started',
                'old_values' => [
                    'work_order_status' => 'Scheduled',
                    'complaint_status' => $previousComplaintStatus,
                ],
                'new_values' => [
                    'work_order_status' => 'In Progress',
                    'complaint_status' => 'In Progress',
                    'started_at' => $startedAt->toDateTimeString(),
                ],
                'reason' => 'Assigned technician started maintenance on the finalized scheduled date.',
            ]);
        }, 3);

        return redirect()
            ->route('technician.maintenance-reports.create', $complaint)
            ->with('success', 'Maintenance started successfully.');
    }




    public function start(
        \App\Models\Complaint $complaint
    ) {
        $technicianId = (int) auth()->id();

        $workOrders = MaintenanceWorkOrder::query()
            ->where('complaint_id', $complaint->id)
            ->where('status', 'Scheduled')
            ->whereHas('technicians', function ($query) use ($technicianId) {
                $query->where('users.id', $technicianId);
            })
            ->whereHas('schedule', function ($query) {
                $query
                    ->where('status', 'Finalized')
                    ->whereDate(
                        'schedule_date',
                        now('Asia/Manila')->toDateString()
                    );
            })
            ->orderByDesc('visit_number')
            ->orderByDesc('id')
            ->limit(2)
            ->get();

        if ($workOrders->count() !== 1) {
            throw ValidationException::withMessages([
                'maintenance' => $workOrders->isEmpty()
                    ? 'No authorized work order is available to start today.'
                    : 'Multiple work orders are available. Open the specific work order to start maintenance.',
            ]);
        }

        return $this->startWorkOrder(
            $complaint,
            $workOrders->first()
        );
    }



    public function create(Complaint $complaint)
    {
        $this->authorizeTechnician($complaint);

        $complaint->load([
            'consumer',
            'consumer.address',
            'category',
            'division',
            'technicians',
            'maintenanceReport.technician',
            'aiAnalysis',
            'commercialResolution',
        ]);

        $report = $complaint->maintenanceReport;

        if (
            $report &&
            $report->review_status === 'Approved'
        ) {
            return redirect()->route(
                'technician.maintenance-reports.show',
                $complaint
            );
        }

        if (
            $report &&
            $report->review_status === 'Pending Review'
        ) {
            return redirect()
                ->route(
                    'technician.maintenance-reports.show',
                    $complaint
                )
                ->with(
                    'info',
                    'This accomplishment report has already been submitted and is awaiting manager review.'
                );
        }
        $this->ensureAccomplishmentAllowed($complaint);

        if (
            $report &&
            in_array(
                $report->review_status,
                [
                    'Draft',
                    'Returned',
                ],
                true
            )
        ) {
            return view(
                'technician.maintenance.report',
                compact(
                    'complaint',
                    'report'
                )
            );
        }

        if (
            !in_array(
                $complaint->status,
                [
                    'Assigned',
                    'In Progress',
                ],
                true
            )
        ) {
            return redirect()
                ->route(
                    'technician.complaints.show',
                    $complaint
                )
                ->with(
                    'error',
                    'This complaint is not currently available for maintenance reporting.'
                );
        }

        return view(
            'technician.maintenance.report',
            compact(
                'complaint',
                'report'
            )
        );
    }


    public function store(
        StoreMaintenanceReportRequest $request,
        Complaint $complaint
    ) {
        $this->authorizeTechnician($complaint);
        $this->ensureAccomplishmentAllowed($complaint);

        $validated = $request->validated();
        $technicianId = (int) Auth::id();

        $newPhotos = [];
        $oldPhotos = [];

        try {
            DB::transaction(function () use (
                $request,
                $complaint,
                $validated,
                $technicianId,
                &$newPhotos,
                &$oldPhotos
            ) {
                /*
                |--------------------------------------------------------------------------
                | Lock Complaint and Existing Report
                |--------------------------------------------------------------------------
                */

                $lockedComplaint = Complaint::query()
                    ->lockForUpdate()
                    ->findOrFail($complaint->id);

                $report = MaintenanceReport::query()
                    ->where('complaint_id', $lockedComplaint->id)
                    ->lockForUpdate()
                    ->first();

                if (
                    $report &&
                    in_array(
                        $report->review_status,
                        ['Approved', 'Pending Review'],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'maintenance' =>
                        'This report has already been submitted or approved.',
                    ]);
                }

                $isResubmission =
                    $report?->review_status === 'Returned';


                if ($isResubmission) {
                    if (
                        !$report->technician_id ||
                        (int) $report->technician_id !== $technicianId
                    ) {
                        abort(
                            403,
                            'Only the original reporting technician can correct this returned accomplishment report.'
                        );
                    }
                }


                $submittedAt = now('Asia/Manila');

                $workOrder = null;

                if (!$isResubmission) {
                    if ($lockedComplaint->status !== 'In Progress') {
                        throw ValidationException::withMessages([
                            'maintenance' =>
                            'The complaint must be In Progress before report submission.',
                        ]);
                    }

                    $workOrder = MaintenanceWorkOrder::query()
                        ->where('complaint_id', $lockedComplaint->id)
                        ->where('status', 'In Progress')
                        ->whereHas(
                            'technicians',
                            function ($query) use ($technicianId) {
                                $query->where(
                                    'users.id',
                                    $technicianId
                                );
                            }
                        )
                        ->orderByDesc('visit_number')
                        ->orderByDesc('id')
                        ->lockForUpdate()
                        ->first();

                    if (!$workOrder) {
                        throw ValidationException::withMessages([
                            'maintenance' =>
                            'No active work order is assigned to you. The complaint cannot be completed.',
                        ]);
                    }

                    $this->assertCurrentTeamMember($workOrder, $technicianId);
                    $this->assertLatestExecutableVisit($workOrder);

                    if ($workOrder->approval_status !== 'Approved') {
                        throw ValidationException::withMessages([
                            'maintenance' => 'This work order has not been approved.',
                        ]);
                    }

                    $schedule = $workOrder->schedule;

                    if (
                        !$schedule ||
                        $schedule->status !== 'Finalized'
                    ) {
                        throw ValidationException::withMessages([
                            'maintenance' =>
                            'The active work order must have a finalized schedule.',
                        ]);
                    }

                    $scheduledDate =
                        \Carbon\CarbonImmutable::parse(
                            $schedule->schedule_date,
                            'Asia/Manila'
                        )->toDateString();

                    if (
                        $scheduledDate !==
                        $submittedAt->toDateString()
                    ) {
                        throw ValidationException::withMessages([
                            'maintenance' =>
                            'The work order can only be completed on its scheduled date.',
                        ]);
                    }

                    if (!$workOrder->started_at) {
                        throw ValidationException::withMessages([
                            'maintenance' =>
                            'The work order has no recorded maintenance start.',
                        ]);
                    }
                } elseif (
                    !in_array(
                        $lockedComplaint->status,
                        ['Completed', 'Closed'],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'maintenance' =>
                        'A returned accomplishment report can only be corrected after completion.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Create Report If Missing
                |--------------------------------------------------------------------------
                */

                if (!$report) {
                    $report = MaintenanceReport::create([
                        'complaint_id' => $lockedComplaint->id,
                        'technician_id' => $technicianId,
                        'started_at' => $workOrder->started_at,
                        'review_status' => 'Draft',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Handle Before and After Photos
                |--------------------------------------------------------------------------
                */

                foreach (
                    ['before_photo', 'after_photo']
                    as $photoField
                ) {
                    if (!$request->hasFile($photoField)) {
                        continue;
                    }

                    $folder = $photoField === 'before_photo'
                        ? 'maintenance-reports/before'
                        : 'maintenance-reports/after';

                    $newPath = $request
                        ->file($photoField)
                        ->store($folder, 'public');

                    $newPhotos[] = $newPath;

                    if ($report->{$photoField}) {
                        $oldPhotos[] =
                            $report->{$photoField};
                    }

                    $report->{$photoField} = $newPath;
                }

                /*
                |--------------------------------------------------------------------------
                | Save Accomplishment Report
                |--------------------------------------------------------------------------
                */

                $report->diagnosis =
                    $validated['diagnosis'];

                $report->root_cause =
                    $validated['root_cause'];

                $report->materials_parts =
                    $validated['materials_parts'] ?? null;

                $report->technician_notes =
                    $validated['technician_notes'] ?? null;

                if (!$isResubmission) {
                    $report->started_at = $workOrder->started_at;
                }

                if (!$isResubmission) {
                    $report->submitted_at = $submittedAt;
                }
                if (!$isResubmission) {
                    $report->technician_id = $technicianId;
                }

                /*
                |--------------------------------------------------------------------------
                | Returned Report Resubmission
                |--------------------------------------------------------------------------
                */

                if ($isResubmission) {
                    $report->resubmitted_at = $submittedAt;

                    $report->revision_number =
                        ((int) ($report->revision_number ?? 0))
                        + 1;
                }

                $report->review_status = 'Pending Review';

                $report->reviewed_by = null;
                $report->reviewed_at = null;
                $report->review_remarks = null;

                $report->save();

                /*
                |--------------------------------------------------------------------------
                | Complete Only the Active Work Order
                |--------------------------------------------------------------------------
                |
                | Do not modify an older Pending Workload visit.
                | Do not complete a work order again during resubmission.
                |
                */

                if (!$isResubmission) {
                    $lockedComplaint->update([
                        'status' => 'Completed',
                        'completed_at' => $submittedAt,
                    ]);

                    DB::table('maintenance_work_sessions')
                        ->where('maintenance_work_order_id', $workOrder->id)
                        ->whereNull('ended_at')
                        ->update([
                            'ended_at' => $submittedAt,
                            'end_reason' => 'Completed',
                            'updated_at' => $submittedAt,
                        ]);

                    $workOrder->update([
                        'status' => 'Completed',
                        'completed_at' => $submittedAt,
                    ]);

                    // Only recorded participants receive credit. The outgoing
                    // plumber retains Handed Over status; the reporting plumber
                    // is credited for completing this visit.
                    $reportingParticipation = \App\Models\MaintenanceWorkOrderParticipation::query()
                        ->firstOrCreate(
                            [
                                'maintenance_work_order_id' => $workOrder->id,
                                'technician_id' => $technicianId,
                            ],
                            [
                                'status' => 'Active',
                                // A replacement who only submits the report is
                                // not credited with the original technician's hours.
                                'started_at' => $submittedAt,
                                'recorded_by' => $technicianId,
                            ]
                        );
                    $reportingParticipation->update([
                        'status' => 'Completed',
                        'ended_at' => $submittedAt,
                        'recorded_by' => $technicianId,
                    ]);

                    \App\Models\MaintenanceWorkOrderParticipation::query()
                        ->where('maintenance_work_order_id', $workOrder->id)
                        ->where('status', 'Active')
                        ->where('technician_id', '!=', $technicianId)
                        ->update([
                            'status' => 'Completed',
                            'ended_at' => $submittedAt,
                            'updated_at' => $submittedAt,
                        ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Work Order Audit History
                    |--------------------------------------------------------------------------
                    */

                    MaintenanceScheduleHistory::create([
                        'maintenance_work_order_id' =>
                        $workOrder->id,

                        'performed_by' =>
                        $technicianId,

                        'action' =>
                        'Work Completed',

                        'old_values' => [
                            'status' => 'In Progress',
                        ],

                        'new_values' => [
                            'status' => 'Completed',
                            'completed_at' =>
                            $submittedAt->toDateTimeString(),
                        ],

                        'reason' =>
                        'Technician submitted the maintenance accomplishment report.',
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Update Current Visit Technicians
                    |--------------------------------------------------------------------------
                    |
                    | Do not update all complaint technicians.
                    | Only update technicians belonging to this work order.
                    |
                    */

                    $teamIds = DB::table(
                        'maintenance_work_order_technicians'
                    )
                        ->where(
                            'maintenance_work_order_id',
                            $workOrder->id
                        )
                        ->pluck('technician_id')
                        ->all();

                    DB::table('complaint_technicians')
                        ->where(
                            'complaint_id',
                            $lockedComplaint->id
                        )
                        ->whereIn(
                            'technician_id',
                            $teamIds
                        )
                        ->whereIn(
                            'status',
                            ['Assigned', 'In Progress']
                        )
                        ->update([
                            'status' => 'Completed',
                            'completed_at' => $submittedAt,
                            'updated_at' => $submittedAt,
                        ]);
                }
            }, 3);
        } catch (\Throwable $exception) {
            /*
             * Database rollback cannot automatically
             * remove uploaded files.
             */
            foreach ($newPhotos as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        /*
        |--------------------------------------------------------------------------
        | Delete Replaced Photos After Successful Transaction
        |--------------------------------------------------------------------------
        */

        foreach ($oldPhotos as $path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()
            ->route(
                'technician.maintenance-reports.show',
                $complaint
            )
            ->with(
                'success',
                'The accomplishment report has been submitted for manager review.'
            );
    }



    public function show(Complaint $complaint)
    {
        $this->authorizeTechnician($complaint);

        $complaint->load([
            'consumer',
            'consumer.address',
            'category',
            'division',
            'technicians',
            'maintenanceReport.technician',
            'aiAnalysis',
            'commercialResolution',
        ]);

        $report = $complaint->maintenanceReport;

        if (!$report) {
            return redirect()
                ->route(
                    'technician.maintenance-reports.create',
                    $complaint
                )
                ->with(
                    'error',
                    'No maintenance report is available yet.'
                );
        }

        if (
            !$report->submitted_at ||
            !in_array(
                $report->review_status,
                ['Pending Review', 'Returned', 'Approved'],
                true
            )
        ) {
            return redirect()
                ->route(
                    'technician.maintenance-reports.create',
                    $complaint
                )
                ->with(
                    'info',
                    'The accomplishment report has not been submitted yet.'
                );
        }

        $completedWorkOrder = $this->completedVisitForReport($complaint);

        return view(
            'technician.maintenance.show',
            compact('complaint', 'completedWorkOrder')
        );
    }

    public function printReport(
        Complaint $complaint
    ) {
        $this->authorizeTechnician($complaint);

        $complaint->load([
            'consumer',
            'consumer.address',
            'category',
            'division',
            'technicians',
            'maintenanceReport.technician',
            'aiAnalysis',
        ]);

        $report = $complaint->maintenanceReport;

        abort_unless(
            $report &&
                $report->submitted_at &&
                in_array(
                    $report->review_status,
                    ['Pending Review', 'Returned', 'Approved'],
                    true
                ),
            404
        );

        $completedWorkOrder = $this->completedVisitForReport($complaint);

        return view(
            'technician.maintenance.print',
            compact('complaint', 'completedWorkOrder')
        );
    }


    public function print(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $technicianId = (int) Auth::id();

        $from = $request->filled('from')
            ? Carbon::parse($request->from, 'Asia/Manila')->startOfDay()
            : now('Asia/Manila')->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to, 'Asia/Manila')->endOfDay()
            : now('Asia/Manila')->endOfDay();

        /*
    |--------------------------------------------------------------------------
    | Completed Maintenance Work Orders
    |--------------------------------------------------------------------------
    |
    | Match the Work Summary dashboard.
    | Count completed visits, not completed complaint records.
    |
    */

        $completedWorkOrders = MaintenanceWorkOrder::query()
            ->with([
                'complaint.consumer',
                'complaint.consumer.address',
                'complaint.category',
                'complaint.aiAnalysis',
                'schedule',
            ])
            ->whereHas('technicians', function ($query) use ($technicianId) {
                $query->where('users.id', $technicianId);
            })
            ->where('maintenance_work_orders.status', 'Completed')
            ->whereNotNull('maintenance_work_orders.completed_at')
            ->whereBetween(
                'maintenance_work_orders.completed_at',
                [$from, $to]
            )
            ->orderByDesc('maintenance_work_orders.completed_at')
            ->orderByDesc('maintenance_work_orders.id')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Accomplished Visits
    |--------------------------------------------------------------------------
    */

        $total = $completedWorkOrders->count();

        /*
    |--------------------------------------------------------------------------
    | Actual Average Maintenance Duration
    |--------------------------------------------------------------------------
    |
    | Use work-order started_at and completed_at.
    | Calculate using seconds for accurate short durations.
    |
    */
        $totalSeconds = 0;
        $validRecords = 0;

        foreach ($completedWorkOrders as $workOrder) {
            if (!$workOrder->started_at || !$workOrder->completed_at) {
                continue;
            }

            $seconds = $workOrder->started_at->diffInSeconds(
                $workOrder->completed_at,
                false
            );

            if ($seconds < 0) {
                continue;
            }

            $totalSeconds += $seconds;
            $validRecords++;
        }

        $averageCompletionMinutes = $validRecords > 0
            ? round(($totalSeconds / $validRecords) / 60, 1)
            : null;

        $averageCompletionHours = $validRecords > 0
            ? round(($totalSeconds / $validRecords) / 3600, 2)
            : null;

        /*
    |--------------------------------------------------------------------------
    | Return Print View
    |--------------------------------------------------------------------------
    */

        return view(
            'technician.reports.maintenance-print',
            compact(
                'from',
                'to',
                'completedWorkOrders',
                'total',
                'averageCompletionMinutes',
                'averageCompletionHours'
            )
        );
    }

    private function authorizeTechnician(
        Complaint $complaint
    ): void {
        abort_unless(
            $complaint
                ->technicians()
                ->where(
                    'users.id',
                    Auth::id()
                )
                ->exists(),
            403,
            'You are not authorized to manage this maintenance report.'
        );
    }



    public function resumeWorkOrder(Complaint $complaint, MaintenanceWorkOrder $workOrder)
    {
        $technicianId = (int) auth()->id();

        DB::transaction(function () use ($complaint, $workOrder, $technicianId) {
            $lockedComplaint = Complaint::query()->lockForUpdate()->findOrFail($complaint->id);
            $order = MaintenanceWorkOrder::query()->lockForUpdate()->findOrFail($workOrder->id);
            abort_unless((int) $order->complaint_id === (int) $lockedComplaint->id, 404);

            $assigned = DB::table('maintenance_work_order_technicians')
                ->where('maintenance_work_order_id', $order->id)
                ->where('technician_id', $technicianId)->exists();
            abort_unless($assigned, 403, 'You are no longer assigned to this work order.');
            $this->assertCurrentTeamMember($order, $technicianId);
            $this->assertLatestExecutableVisit($order);

            if ($order->status !== 'Pending Workload' ||
                $order->approval_status !== 'Approved' ||
                !filled($order->pending_reason) ||
                !in_array($lockedComplaint->status, ['Assigned', 'In Progress'], true)) {
                throw ValidationException::withMessages([
                    'maintenance' => 'This work order is not eligible for same-day resumption.',
                ]);
            }

            $schedule = \App\Models\MaintenanceSchedule::query()
                ->lockForUpdate()->find($order->maintenance_schedule_id);
            $today = now('Asia/Manila')->toDateString();
            if (!$schedule || $schedule->status !== 'Finalized' ||
                \Carbon\CarbonImmutable::parse($schedule->schedule_date, 'Asia/Manila')->toDateString() !== $today) {
                throw ValidationException::withMessages([
                    'maintenance' => 'The scheduled day has passed or the schedule is not finalized. Contact the manager.',
                ]);
            }

            $newerVisit = MaintenanceWorkOrder::query()
                ->where('complaint_id', $order->complaint_id)
                ->where(function ($query) use ($order) {
                    $query->where('visit_number', '>', $order->visit_number)
                        ->orWhere(fn ($q) => $q->where('visit_number', $order->visit_number)
                            ->where('id', '>', $order->id));
                })->whereIn('status', ['Scheduled', 'In Progress', 'Completed', 'Pending Workload'])
                ->exists();
            if ($newerVisit) {
                throw ValidationException::withMessages([
                    'maintenance' => 'A newer visit already exists. Manager review is required.',
                ]);
            }

            $hasReport = MaintenanceReport::query()
                ->where('complaint_id', $order->complaint_id)
                ->whereIn('review_status', ['Pending Review', 'Returned', 'Approved'])->exists();
            if ($hasReport) {
                throw ValidationException::withMessages([
                    'maintenance' => 'A report has already been submitted for this complaint.',
                ]);
            }

            $at = now('Asia/Manila');
            $previousStatus = $lockedComplaint->status;
            $wasStarted = (bool) $order->started_at;
            $order->status = 'In Progress';
            if (!$wasStarted) $order->started_at = $at;
            $order->save();

            $lockedComplaint->status = 'In Progress';
            $lockedComplaint->save();

            DB::table('maintenance_work_sessions')->insert([
                'maintenance_work_order_id' => $order->id,
                'technician_id' => $technicianId,
                'started_at' => $at,
                'created_at' => $at,
                'updated_at' => $at,
            ]);

            $participation = \App\Models\MaintenanceWorkOrderParticipation::query()
                ->firstOrCreate(
                    ['maintenance_work_order_id' => $order->id, 'technician_id' => $technicianId],
                    ['status' => 'Active', 'started_at' => $at, 'recorded_by' => $technicianId]
                );
            $participation->update([
                'status' => 'Active',
                'ended_at' => null,
                'recorded_by' => $technicianId,
            ]);

            DB::table('complaint_technicians')
                ->where('complaint_id', $lockedComplaint->id)
                ->where('technician_id', $technicianId)
                ->where('status', 'Assigned')
                ->update(['status' => 'In Progress', 'started_at' => $at, 'updated_at' => $at]);

            MaintenanceScheduleHistory::create([
                'maintenance_work_order_id' => $order->id,
                'performed_by' => $technicianId,
                'action' => 'Maintenance Resumed',
                'old_values' => ['status' => 'Pending Workload', 'complaint_status' => $previousStatus],
                'new_values' => ['status' => 'In Progress', 'complaint_status' => 'In Progress',
                    'resumed_at' => $at->toDateTimeString(), 'first_start' => !$wasStarted],
                'reason' => 'Assigned technician resumed work on the original approved scheduled date.',
            ]);
        }, 3);

        return redirect()->route('technician.complaints.show', $complaint)
            ->with('success', 'Maintenance resumed. You can continue the same visit.');
    }

    public function markPendingWorkload(
        Request $request,
        Complaint $complaint,
        MaintenanceWorkOrder $workOrder
    ) {
        $validated = $request->validate([
            'pending_reason' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
        ]);

        $technicianId = (int) auth()->id();

        DB::transaction(function () use (
            $complaint,
            $workOrder,
            $technicianId,
            $validated
        ) {
            $lockedComplaint = Complaint::query()
                ->lockForUpdate()
                ->findOrFail($complaint->id);

            $lockedOrder = MaintenanceWorkOrder::query()
                ->lockForUpdate()
                ->findOrFail($workOrder->id);

            if (
                (int) $lockedOrder->complaint_id !==
                (int) $lockedComplaint->id
            ) {
                abort(404);
            }

            /*
        |--------------------------------------------------------------------------
        | Dedicated Team Authorization
        |--------------------------------------------------------------------------
        */

            $isAssigned = DB::table('maintenance_work_order_technicians')
                ->where('maintenance_work_order_id', $lockedOrder->id)
                ->where('technician_id', $technicianId)
                ->exists();

            abort_unless(
                $isAssigned,
                403,
                'You are not assigned to this maintenance work order.'
            );
            $this->assertCurrentTeamMember($lockedOrder, $technicianId);
            $this->assertLatestExecutableVisit($lockedOrder);

            if ($lockedOrder->approval_status !== 'Approved') {
                throw ValidationException::withMessages([
                    'pending_reason' => 'This work order is awaiting Maintenance Manager approval.',
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Validate Active Maintenance
        |--------------------------------------------------------------------------
        */

            if (
                !in_array($lockedComplaint->status, ['Assigned', 'In Progress'], true) ||
                !in_array($lockedOrder->status, ['Scheduled', 'In Progress'], true)
            ) {
                throw ValidationException::withMessages([
                    'pending_reason' =>
                    'Only assigned or in-progress maintenance can be marked Pending Workload.',
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Prevent Updating an Outdated Visit
        |--------------------------------------------------------------------------
        */

            $hasNewerVisit = MaintenanceWorkOrder::query()
                ->where('complaint_id', $lockedComplaint->id)
                ->where(function ($query) use ($lockedOrder) {
                    $query
                        ->where('visit_number', '>', $lockedOrder->visit_number)
                        ->orWhere(function ($sameVisit) use ($lockedOrder) {
                            $sameVisit
                                ->where('visit_number', $lockedOrder->visit_number)
                                ->where('id', '>', $lockedOrder->id);
                        });
                })
                ->whereIn('status', [
                    'Scheduled',
                    'In Progress',
                    'Completed',
                    'Pending Workload',
                ])
                ->exists();

            if ($hasNewerVisit) {
                throw ValidationException::withMessages([
                    'pending_reason' =>
                    'A newer maintenance visit already exists. This older work order requires manager review.',
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Validate Finalized Schedule
        |--------------------------------------------------------------------------
        */

            $schedule = \App\Models\MaintenanceSchedule::query()
                ->lockForUpdate()
                ->find($lockedOrder->maintenance_schedule_id);

            if (!$schedule || $schedule->status !== 'Finalized') {
                throw ValidationException::withMessages([
                    'pending_reason' =>
                    'This work order does not belong to a finalized maintenance schedule.',
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Prevent Recording Pending Workload Before Scheduled Date
        |--------------------------------------------------------------------------
        */

            $today = now('Asia/Manila')->toDateString();

            $scheduledDate = \Carbon\CarbonImmutable::parse(
                $schedule->schedule_date,
                'Asia/Manila'
            )->toDateString();

            if ($scheduledDate !== $today) {
                throw ValidationException::withMessages([
                    'pending_reason' =>
                    'Pending Workload can only be recorded on the scheduled maintenance date.',
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Prevent Pending Workload After Report Submission
        |--------------------------------------------------------------------------
        */

            $reportSubmitted = MaintenanceReport::query()
                ->where('complaint_id', $lockedComplaint->id)
                ->whereIn('review_status', [
                    'Pending Review',
                    'Approved',
                    'Returned',
                ])
                ->exists();

            if ($reportSubmitted) {
                throw ValidationException::withMessages([
                    'pending_reason' =>
                    'An accomplishment report already exists for management review.',
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Record Pending Workload
        |--------------------------------------------------------------------------
        */

            $recordedAt = now('Asia/Manila');
            $reason = trim($validated['pending_reason']);

            DB::table('maintenance_work_sessions')
                ->where('maintenance_work_order_id', $lockedOrder->id)
                ->whereNull('ended_at')
                ->update([
                    'ended_at' => $recordedAt,
                    'end_reason' => 'Pending Workload',
                    'notes' => $reason,
                    'updated_at' => $recordedAt,
                ]);

            $lockedOrder->update([
                'status' => 'Pending Workload',
                'pending_reason' => $reason,
                'pending_recorded_at' => $recordedAt,
                'pending_recorded_by' => $technicianId,
            ]);

            // Pending Workload preserves the original visit and its participants.
            \App\Models\MaintenanceWorkOrderParticipation::query()->updateOrCreate(
                [
                    'maintenance_work_order_id' => $lockedOrder->id,
                    'technician_id' => $technicianId,
                ],
                [
                    'status' => 'Pending Workload',
                    'started_at' => $lockedOrder->started_at,
                    'ended_at' => $recordedAt,
                    'recorded_by' => $technicianId,
                ]
            );
            \App\Models\MaintenanceWorkOrderParticipation::query()
                ->where('maintenance_work_order_id', $lockedOrder->id)
                ->where('status', 'Active')
                ->where('technician_id', '!=', $technicianId)
                ->update([
                    'status' => 'Pending Workload',
                    'ended_at' => $recordedAt,
                    'updated_at' => $recordedAt,
                ]);

            /*
        |--------------------------------------------------------------------------
        | Preserve Complaint Status
        |--------------------------------------------------------------------------
        |
        | The complaint remains In Progress.
        | No completion date or accomplishment is recorded.
        |
        */

            /*
        |--------------------------------------------------------------------------
        | Maintenance Audit History
        |--------------------------------------------------------------------------
        */

            MaintenanceScheduleHistory::create([
                'maintenance_work_order_id' => $lockedOrder->id,
                'performed_by' => $technicianId,
                'action' => 'Pending Workload',
                'old_values' => [
                    'status' => $lockedOrder->started_at ? 'In Progress' : 'Scheduled',
                    'complaint_status' => $lockedComplaint->status,
                ],
                'new_values' => [
                    'status' => 'Pending Workload',
                    'complaint_status' => 'In Progress',
                    'pending_recorded_at' => $recordedAt->toDateTimeString(),
                ],
                'reason' => $reason,
            ]);
        }, 3);

        return redirect()
            ->route('technician.complaints.show', $complaint)
            ->with(
                'success',
                'Pending Workload recorded. You may resume this approved visit today; otherwise the manager can plan a follow-up.'
            );
    }


    private function ensureAccomplishmentAllowed(
        Complaint $complaint
    ): void {
        $technicianId = (int) Auth::id();



        $report = MaintenanceReport::query()
            ->where('complaint_id', $complaint->id)
            ->first();

        if ($report?->review_status === 'Returned') {
            abort_unless(
                (int) $report->technician_id === $technicianId,
                403,
                'Only the original reporting technician can correct this returned report.'
            );

            if (!in_array(
                $complaint->status,
                ['Completed', 'Closed'],
                true
            )) {
                throw ValidationException::withMessages([
                    'maintenance' =>
                    'A returned accomplishment report can only be corrected after maintenance completion.',
                ]);
            }

            return;
        }

        /*
    |--------------------------------------------------------------------------
    | Active Work Order Authorization
    |--------------------------------------------------------------------------
    */

        $order = MaintenanceWorkOrder::query()
            ->where('complaint_id', $complaint->id)
            ->where('status', 'In Progress')
            ->whereHas('technicians', function ($query) use ($technicianId) {
                $query->where('users.id', $technicianId);
            })
            ->orderByDesc('visit_number')
            ->orderByDesc('id')
            ->first();

        if (!$order) {
            throw ValidationException::withMessages([
                'maintenance' =>
                'No authorized active work order is available. Record Pending Workload if maintenance is unfinished, or contact the Maintenance Manager.',
            ]);
        }

        $this->assertCurrentTeamMember($order, $technicianId);
        $this->assertLatestExecutableVisit($order);

        if ($order->approval_status !== 'Approved') {
            throw ValidationException::withMessages([
                'maintenance' => 'This work order is awaiting Maintenance Manager approval.',
            ]);
        }

        if (!$order->started_at) {
            throw ValidationException::withMessages([
                'maintenance' =>
                'The active work order has no recorded maintenance start.',
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Finalized Schedule Validation
    |--------------------------------------------------------------------------
    */

        $schedule = $order->schedule;

        if (!$schedule || $schedule->status !== 'Finalized') {
            throw ValidationException::withMessages([
                'maintenance' =>
                'The maintenance schedule must be finalized by the manager.',
            ]);
        }

        $today = now('Asia/Manila')->toDateString();

        $scheduleDate = \Carbon\CarbonImmutable::parse(
            $schedule->schedule_date,
            'Asia/Manila'
        )->toDateString();

        if ($scheduleDate !== $today) {
            throw ValidationException::withMessages([
                'maintenance' =>
                'This work order is not scheduled for today. Contact the Maintenance Manager for rescheduling.',
            ]);
        }
    }

    /** Verify the technician remains authorized after a manager handover. */
    private function assertCurrentTeamMember(MaintenanceWorkOrder $order, int $technicianId): void
    {
        $assigned = DB::table('maintenance_work_order_technicians')
            ->where('maintenance_work_order_id', $order->id)
            ->where('technician_id', $technicianId)
            ->exists();

        abort_unless($assigned, 403, 'This visit has been assigned to another plumber.');

        $participation = \App\Models\MaintenanceWorkOrderParticipation::query()
            ->where('maintenance_work_order_id', $order->id)
            ->where('technician_id', $technicianId)
            ->first();

        abort_if(
            $participation && in_array($participation->status, ['Handed Over', 'Replaced'], true),
            403,
            'Your participation in this visit has ended following a manager handover.'
        );
    }

    /** Older visits remain readable but are not executable after a follow-up exists. */
    private function assertLatestExecutableVisit(MaintenanceWorkOrder $order): void
    {
        $newer = MaintenanceWorkOrder::query()
            ->where('complaint_id', $order->complaint_id)
            ->where(function ($query) use ($order) {
                $query->where('visit_number', '>', $order->visit_number)
                    ->orWhere(function ($sameVisit) use ($order) {
                        $sameVisit->where('visit_number', $order->visit_number)
                            ->where('id', '>', $order->id);
                    });
            })
            ->whereNotIn('status', ['Cancelled', 'Rescheduled'])
            ->exists();

        if ($newer) {
            throw ValidationException::withMessages([
                'maintenance' => 'A newer maintenance visit exists. The earlier visit is history only.',
            ]);
        }
    }

    private function completedVisitForReport(Complaint $complaint): ?MaintenanceWorkOrder
    {
        return MaintenanceWorkOrder::query()
            ->with(['schedule', 'technicians'])
            ->where('complaint_id', $complaint->id)
            ->where('status', 'Completed')
            ->whereNotNull('completed_at')
            ->orderByDesc('visit_number')
            ->orderByDesc('id')
            ->first();
    }
}
