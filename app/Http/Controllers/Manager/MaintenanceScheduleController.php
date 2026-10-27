<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceScheduleHistory;
use App\Models\MaintenanceWorkOrder;
use App\Models\TechnicianDuty;
use App\Models\User;
use App\Notifications\MaintenanceScheduleFinalized;
use App\Services\AuditLogger;
use App\Services\BatchMaintenancePlanningService;
use App\Services\MaintenancePlanningQueueService;
use App\Services\MaintenanceSchedulingService;
use App\Services\MaintenanceWorkOrderValidationService;
use App\Services\TechnicianAvailabilityService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class MaintenanceScheduleController extends Controller
{
    public function index(
        Request $request,
        MaintenancePlanningQueueService $planning
    ) {
        // Existing availability response for the Draft Reschedule modal.
        if ($request->has('availability_date')) {
            $targetDate = $request->validate([
                'availability_date' => ['required', 'date_format:Y-m-d'],
            ])['availability_date'];

            app(MaintenanceWorkOrderValidationService::class)
                ->validateWorkingDate($targetDate, 'availability_date');

            $duties = app(TechnicianAvailabilityService::class)
                ->availableOn($targetDate);

            $availableIds = $duties
                ->pluck('technician_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $dutyByTechnician = TechnicianDuty::query()
                ->whereDate('duty_date', $targetDate)
                ->get()
                ->keyBy('technician_id');

            $roster = User::role('Maintenance Technician')
                ->where('is_active', true)
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get()
                ->map(function ($technician) use (
                    $availableIds,
                    $dutyByTechnician
                ) {
                    $duty = $dutyByTechnician->get($technician->id);

                    $available = in_array(
                        (int) $technician->id,
                        $availableIds,
                        true
                    );

                    return [
                        'id' => (int) $technician->id,
                        'name' => $technician->full_name
                            ?: 'Technician #' . $technician->id,
                        'available' => $available,
                        'duty_status' => $available
                            ? 'Available'
                            : 'Unavailable',
                        'reason' => !$duty
                            ? 'No availability record'
                            : 'Marked unavailable',
                    ];
                })
                ->values();

            return response()->json([
                'date' => $targetDate,

                'technicians' => $duties
                    ->unique('technician_id')
                    ->map(function ($duty) {
                        return [
                            'id' => (int) $duty->technician_id,
                            'name' => $duty->technician?->full_name
                                ?? 'Technician #' . $duty->technician_id,
                            'duty_status' => $duty->status,
                        ];
                    })
                    ->values(),

                'all_technicians' => $roster,
            ]);
        }

        $timezone = 'Asia/Manila';

        $today = CarbonImmutable::now($timezone)->startOfDay();

        $nextWorkingDay = $today->addDay();

        while ($nextWorkingDay->isWeekend()) {
            $nextWorkingDay = $nextWorkingDay->addDay();
        }

        $date = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ])['date'] ?? $nextWorkingDay->toDateString();

        $relations = [
            'workOrders.complaint.category',
            'workOrders.complaint.division',
            'workOrders.complaint.technicians',
            'workOrders.complaint.aiAnalysis',
            'workOrders.technicians',
            'creator',
            'finalizer',
        ];

        $todaySchedule = MaintenanceSchedule::with($relations)
            ->whereDate('schedule_date', $today->toDateString())
            ->first();

        $schedule = MaintenanceSchedule::with($relations)
            ->whereDate('schedule_date', $date)
            ->first();

        $planningComplaints = $planning->unassigned()
            ->with([
                'division',
                'commercialResolution',
                'category',
                'aiAnalysis',
            ])
            ->limit(100)
            ->get();

        $assignedUnscheduled = Complaint::query()
            ->with([
                'division',
                'category',
                'technicians',
                'aiAnalysis',
            ])
            ->where('status', 'Assigned')
            ->whereHas('technicians')
            ->whereDoesntHave('workOrders', function ($query) {
                $query->whereIn('status', [
                    'Scheduled',
                    'In Progress',
                    'Pending Workload',
                ]);
            })
            ->orderBy('created_at')
            ->limit(100)
            ->get();

        $pendingWorkOrders = MaintenanceWorkOrder::query()
            ->with([
                'complaint.category',
                'complaint.technicians',
                'complaint.aiAnalysis',
                'schedule',
                'technicians',
            ])
            ->where('status', 'Pending Workload')
            ->whereHas('complaint', function ($query) {
                $query->whereNotIn('status', [
                    'Completed',
                    'Closed',
                ]);
            })
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('maintenance_work_orders as follow_up')
                    ->whereColumn(
                        'follow_up.complaint_id',
                        'maintenance_work_orders.complaint_id'
                    )
                    ->whereColumn(
                        'follow_up.id',
                        '>',
                        'maintenance_work_orders.id'
                    )
                    ->where('follow_up.status', 'Completed');
            })
            ->orderBy('pending_recorded_at')
            ->limit(100)
            ->get();

        $overdueWorkOrders = MaintenanceWorkOrder::query()
            ->with([
                'complaint.category',
                'complaint.technicians',
                'complaint.aiAnalysis',
                'schedule',
                'technicians',
            ])
            ->where('status', 'Scheduled')
            ->where('approval_status', 'Approved')
            ->whereHas('schedule', function ($query) use ($today) {
                $query
                    ->where('status', 'Finalized')
                    ->whereDate(
                        'schedule_date',
                        '<',
                        $today->toDateString()
                    );
            })
            ->whereNull('started_at')
            ->orderBy('id')
            ->limit(100)
            ->get();

        $availableDuties = app(TechnicianAvailabilityService::class)
            ->availableOn($date);

        $scheduledWorkOrders = MaintenanceWorkOrder::query()
            ->with([
                'complaint.aiAnalysis',
                'technicians',
            ])
            ->whereHas('schedule', function ($query) use ($date) {
                $query->whereDate('schedule_date', $date);
            })
            ->whereIn('status', [
                'Scheduled',
                'In Progress',
                'Pending Workload',
            ])
            ->orderBy('id')
            ->get();

        $plumberWorkloads = $availableDuties->mapWithKeys(
            function ($duty) use ($scheduledWorkOrders) {
                $workOrders = $scheduledWorkOrders->filter(
                    fn ($order) => $order->technicians
                        ->contains('id', $duty->technician_id)
                );

                return [
                    $duty->technician_id => [
                        'count' => $workOrders->count(),
                        'work_orders' => $workOrders->values(),
                    ],
                ];
            }
        );

        return view(
            'maintenance-manager.schedules.index',
            compact(
                'date',
                'today',
                'nextWorkingDay',
                'schedule',
                'availableDuties',
                'scheduledWorkOrders',
                'plumberWorkloads',
                'todaySchedule',
                'planningComplaints',
                'assignedUnscheduled',
                'pendingWorkOrders',
                'overdueWorkOrders'
            )
        );
    }

    /**
     * Retained for existing single-complaint routes.
     */
    public function store(
        Request $request,
        MaintenanceSchedulingService $service
    ) {
        $validated = $request->validate([
            'complaint_id' => [
                'required',
                'integer',
                'exists:complaints,id',
            ],
            'schedule_date' => [
                'required',
                'date_format:Y-m-d',
            ],
            'late_override' => ['nullable', 'boolean'],
            'late_addition_reason' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $complaintId = (int) $validated['complaint_id'];
        $scheduleDate = $validated['schedule_date'];

        $workOrder = $service->scheduleComplaint(
            $complaintId,
            $scheduleDate,
            (int) $request->user()->id,
            $request->boolean('late_override'),
            $validated['late_addition_reason'] ?? null
        );

        // Audit logging must not change the scheduling result.
        AuditLogger::log(
            action: 'Work Order Scheduled',
            module: 'Maintenance Scheduling',
            description: "Scheduled complaint #{$complaintId} for {$scheduleDate}.",
            subject: $workOrder instanceof Model ? $workOrder : null,
            oldValues: null,
            newValues: [
                'complaint_id' => $complaintId,
                'schedule_date' => $scheduleDate,
            ]
        );

        return redirect()
            ->route('maintenance-manager.schedules.index', [
                'date' => $scheduleDate,
            ])
            ->with(
                'success',
                'Complaint added to the maintenance schedule.'
            );
    }

    public function finalize(
        Request $request,
        MaintenanceSchedule $schedule,
        \App\Services\MaintenanceScheduleFinalizationService $validator
    ) {
        $scheduleDate = null;
        $notificationRecipients = [];

        DB::transaction(function () use (
            $request,
            $schedule,
            $validator,
            &$scheduleDate,
            &$notificationRecipients
        ) {
            $lockedSchedule = MaintenanceSchedule::query()
                ->lockForUpdate()
                ->findOrFail($schedule->id);

            $validator->validate($lockedSchedule);

            $scheduleDate = $lockedSchedule->schedule_date
                ->format('Y-m-d');

            $now = now('Asia/Manila');
            $managerId = (int) $request->user()->id;

            $workOrders = $lockedSchedule->workOrders()
                ->where('status', 'Scheduled')
                ->lockForUpdate()
                ->get();

            foreach ($workOrders as $workOrder) {
                $technicianIds = $workOrder->technicians()
                    ->pluck('users.id')
                    ->map(fn ($id) => (int) $id)
                    ->all();

                $previousApproval = $workOrder->approval_status;

                $workOrder->update([
                    'approval_status' => 'Approved',
                    'approved_by' => $managerId,
                    'approved_at' => $now,
                ]);

                MaintenanceScheduleHistory::create([
                    'maintenance_work_order_id' => $workOrder->id,
                    'performed_by' => $managerId,
                    'action' => 'Schedule Finalized',
                    'old_values' => [
                        'schedule_status' => 'Draft',
                        'approval_status' => $previousApproval,
                    ],
                    'new_values' => [
                        'schedule_status' => 'Finalized',
                        'approval_status' => 'Approved',
                        'finalized_at' => $now->toDateTimeString(),
                    ],
                    'reason' =>
                        'Manager approved the daily maintenance workload.',
                ]);

                if (!empty($technicianIds)) {
                    $notificationRecipients[$workOrder->id] =
                        $technicianIds;
                }
            }

            $lockedSchedule->update([
                'status' => 'Finalized',
                'finalized_by' => $managerId,
                'finalized_at' => $now,
            ]);
        }, 3);

        AuditLogger::log(
            action: 'Schedule Finalized',
            module: 'Maintenance Scheduling',
            description: "Finalized the maintenance schedule for {$scheduleDate}.",
            subject: $schedule,
            oldValues: [
                'status' => 'Draft',
            ],
            newValues: [
                'status' => 'Finalized',
                'schedule_date' => $scheduleDate,
                'work_orders_approved' => count($notificationRecipients),
            ]
        );

        // Notifications remain after the transaction commits.
        foreach ($notificationRecipients as $workOrderId => $technicianIds) {
            $this->notifyScheduleTechnicians(
                (int) $workOrderId,
                $technicianIds,
                'finalized'
            );
        }

        return redirect()
            ->route('maintenance-manager.schedules.index', [
                'date' => $scheduleDate,
            ])
            ->with(
                'success',
                'Daily maintenance workload finalized successfully.'
            );
    }

    /**
     * Approve new Draft Additions on an already-finalized date.
     * Previously approved work orders remain unchanged.
     */
    public function finalizeAdditions(
        Request $request,
        MaintenanceSchedule $schedule,
        MaintenanceWorkOrderValidationService $validation
    ) {
        $scheduleDate = null;
        $approvedCount = 0;
        $notificationRecipients = [];

        DB::transaction(function () use (
            $request,
            $schedule,
            $validation,
            &$scheduleDate,
            &$approvedCount,
            &$notificationRecipients
        ) {
            $lockedSchedule = MaintenanceSchedule::query()
                ->lockForUpdate()
                ->findOrFail($schedule->id);

            if ($lockedSchedule->status !== 'Finalized') {
                throw ValidationException::withMessages([
                    'schedule' =>
                        'Only finalized schedules can approve additional workload.',
                ]);
            }

            $scheduleDate = $lockedSchedule->schedule_date
                ->format('Y-m-d');

            $validation->validateWorkingDate($scheduleDate);

            $draftOrders = $lockedSchedule->workOrders()
                ->where('approval_status', 'Draft')
                ->lockForUpdate()
                ->get();

            if ($draftOrders->isEmpty()) {
                throw ValidationException::withMessages([
                    'schedule' =>
                        'There are no Draft Additions awaiting approval.',
                ]);
            }

            $managerId = (int) $request->user()->id;
            $now = now('Asia/Manila');

            foreach ($draftOrders as $order) {
                if (
                    $order->status !== 'Scheduled' ||
                    $order->started_at !== null
                ) {
                    throw ValidationException::withMessages([
                        'schedule' =>
                            'All Draft Additions must be Scheduled and not started before approval.',
                    ]);
                }

                $teamIds = $validation->teamIds($order);

                if (empty($teamIds)) {
                    throw ValidationException::withMessages([
                        'schedule' =>
                            'Every Draft Addition must have at least one assigned plumber.',
                    ]);
                }

                $duties = $validation->lockDuties(
                    $scheduleDate,
                    $teamIds
                );

                foreach ($teamIds as $technicianId) {
                    $validation->validateTechnicianForDate(
                        $technicianId,
                        $scheduleDate,
                        'schedule',
                        $duties
                    );
                }

                $order->update([
                    'approval_status' => 'Approved',
                    'approved_by' => $managerId,
                    'approved_at' => $now,
                ]);

                MaintenanceScheduleHistory::create([
                    'maintenance_work_order_id' => $order->id,
                    'performed_by' => $managerId,
                    'action' => 'Addition Approved',
                    'old_values' => [
                        'approval_status' => 'Draft',
                        'schedule_status' => 'Finalized',
                    ],
                    'new_values' => [
                        'approval_status' => 'Approved',
                        'schedule_status' => 'Finalized',
                        'approved_at' => $now->toDateTimeString(),
                        'technician_ids' => $teamIds,
                    ],
                    'reason' =>
                        'Manager approved an additional work order on the finalized daily schedule.',
                ]);

                $notificationRecipients[$order->id] = array_map(
                    'intval',
                    $teamIds
                );

                $approvedCount++;
            }
        }, 3);

        AuditLogger::log(
            action: 'Draft Additions Approved',
            module: 'Maintenance Scheduling',
            description: "Approved {$approvedCount} Draft Addition(s) for {$scheduleDate}.",
            subject: $schedule,
            oldValues: [
                'approval_status' => 'Draft',
            ],
            newValues: [
                'approval_status' => 'Approved',
                'schedule_date' => $scheduleDate,
                'approved_work_orders' => $approvedCount,
            ]
        );

        foreach ($notificationRecipients as $workOrderId => $technicianIds) {
            $this->notifyScheduleTechnicians(
                (int) $workOrderId,
                $technicianIds,
                'addition'
            );
        }

        return redirect()
            ->route('maintenance-manager.schedules.index', [
                'date' => $scheduleDate,
            ])
            ->with(
                'success',
                "{$approvedCount} additional work order(s) approved successfully."
            );
    }

    /**
     * Move a Draft Scheduled work order to another working day.
     */
    public function reschedule(
        Request $request,
        MaintenanceWorkOrder $workOrder,
        MaintenanceWorkOrderValidationService $validation
    ) {
        $validated = $request->validate([
            'schedule_date' => [
                'required',
                'date_format:Y-m-d',
            ],
            'reason' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
            'technician_ids' => [
                'sometimes',
                'array',
                'min:1',
            ],
            'technician_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:users,id',
            ],
        ]);

        $date = $validated['schedule_date'];

        $validation->validateWorkingDate($date);

        $oldDate = null;
        $oldTeamIds = [];
        $finalTeamIds = [];
        $oldScheduleStatus = null;

        $workOrderId = (int) $workOrder->id;

        $destinationStatus = DB::transaction(function () use (
            $request,
            $workOrder,
            $validated,
            $date,
            $validation,
            &$oldDate,
            &$oldTeamIds,
            &$finalTeamIds,
            &$oldScheduleStatus
        ) {
            $order = MaintenanceWorkOrder::query()
                ->lockForUpdate()
                ->findOrFail($workOrder->id);

            if ($order->approval_status !== 'Draft') {
                throw ValidationException::withMessages([
                    'work_order' =>
                        'Only unapproved Draft work orders can be rescheduled.',
                ]);
            }

            if ($order->status !== 'Scheduled') {
                throw ValidationException::withMessages([
                    'work_order' =>
                        'Only Draft Scheduled work orders can be rescheduled. Use Pending Workload Follow-up Planning for unfinished maintenance.',
                ]);
            }

            $oldSchedule = MaintenanceSchedule::query()
                ->lockForUpdate()
                ->find($order->maintenance_schedule_id);

            if (!$oldSchedule) {
                throw ValidationException::withMessages([
                    'work_order' =>
                        'The original maintenance schedule was not found.',
                ]);
            }

            $oldDate = $oldSchedule->schedule_date->format('Y-m-d');
            $oldScheduleStatus = $oldSchedule->status;

            if (!in_array(
                $oldSchedule->status,
                ['Draft', 'Finalized'],
                true
            )) {
                throw ValidationException::withMessages([
                    'work_order' =>
                        'The original schedule has an unsupported status.',
                ]);
            }

            if (
                $order->started_at !== null ||
                $order->completed_at !== null
            ) {
                throw ValidationException::withMessages([
                    'work_order' =>
                        'A work order that has started or completed cannot be rescheduled as Draft.',
                ]);
            }

            if ($oldDate === $date) {
                throw ValidationException::withMessages([
                    'schedule_date' =>
                        'Choose a different maintenance date.',
                ]);
            }

            $oldTeamIds = $validation->teamIds($order);

            $teamIds = array_key_exists(
                'technician_ids',
                $validated
            )
                ? array_map('intval', $validated['technician_ids'])
                : $oldTeamIds;

            $teamIds = array_values(array_unique($teamIds));
            sort($teamIds);

            if (empty($teamIds)) {
                throw ValidationException::withMessages([
                    'technician_ids' =>
                        'Assign at least one plumber before rescheduling.',
                ]);
            }

            $duties = $validation->lockDuties($date, $teamIds);

            foreach ($teamIds as $technicianId) {
                $validation->validateTechnicianForDate(
                    $technicianId,
                    $date,
                    'technician_ids',
                    $duties
                );
            }

            $newSchedule = MaintenanceSchedule::query()
                ->whereDate('schedule_date', $date)
                ->lockForUpdate()
                ->first();

            if (
                $newSchedule &&
                !in_array(
                    $newSchedule->status,
                    ['Draft', 'Finalized'],
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'schedule_date' =>
                        'The destination schedule has an unsupported status.',
                ]);
            }

            if (!$newSchedule) {
                $newSchedule = MaintenanceSchedule::create([
                    'schedule_date' => $date,
                    'status' => 'Draft',
                    'created_by' => $request->user()->id,
                ]);
            }

            $oldValues = [
                'schedule_date' => $oldDate,
                'status' => $order->status,
                'approval_status' => $order->approval_status,
                'schedule_status' => $oldSchedule->status,
                'technician_ids' => $oldTeamIds,
                'pending_reason' => $order->pending_reason,
                'planned_start_time' => $order->planned_start_time,
                'planned_end_time' => $order->planned_end_time,
            ];

            $order->update([
                'maintenance_schedule_id' => $newSchedule->id,
                'status' => 'Scheduled',
                'approval_status' => 'Draft',
                'approved_by' => null,
                'approved_at' => null,
                'planned_start_time' => null,
                'planned_end_time' => null,
            ]);

            $order->technicians()->sync(
                collect($teamIds)
                    ->mapWithKeys(fn ($id) => [
                        $id => ['assignment_role' => 'Member'],
                    ])
                    ->all()
            );

            $complaint = Complaint::query()
                ->lockForUpdate()
                ->findOrFail($order->complaint_id);

            $now = now('Asia/Manila');

            // Preserve existing complaint-level technician assignments.
            $existingTechnicianIds = DB::table('complaint_technicians')
                ->where('complaint_id', $complaint->id)
                ->pluck('technician_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            foreach ($teamIds as $technicianId) {
                if (!in_array(
                    $technicianId,
                    $existingTechnicianIds,
                    true
                )) {
                    DB::table('complaint_technicians')->insert([
                        'complaint_id' => $complaint->id,
                        'technician_id' => $technicianId,
                        'status' => 'Assigned',
                        'assigned_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            if (!in_array(
                $complaint->status,
                ['Assigned', 'In Progress'],
                true
            )) {
                throw ValidationException::withMessages([
                    'work_order' =>
                        'This complaint is not eligible for Draft rescheduling.',
                ]);
            }

            MaintenanceScheduleHistory::create([
                'maintenance_work_order_id' => $order->id,
                'performed_by' => $request->user()->id,
                'action' => 'Rescheduled',
                'old_values' => $oldValues,
                'new_values' => [
                    'schedule_date' => $date,
                    'status' => 'Scheduled',
                    'technician_ids' => $teamIds,
                    'schedule_status' => $newSchedule->status,
                    'approval_status' => 'Draft',
                    'planned_start_time' => null,
                    'planned_end_time' => null,
                ],
                'reason' => $validated['reason'],
            ]);

            $finalTeamIds = $teamIds;

            return $newSchedule->status;
        }, 3);

        // Audit only after the reschedule transaction has committed.
        AuditLogger::log(
            action: 'Work Order Rescheduled',
            module: 'Maintenance Scheduling',
            description: "Rescheduled work order #{$workOrderId} from {$oldDate} to {$date}.",
            subject: $workOrder,
            oldValues: [
                'schedule_date' => $oldDate,
                'schedule_status' => $oldScheduleStatus,
                'technician_ids' => $oldTeamIds,
            ],
            newValues: [
                'schedule_date' => $date,
                'schedule_status' => $destinationStatus,
                'technician_ids' => $finalTeamIds,
                'reason' => $validated['reason'],
            ]
        );

        return redirect()
            ->route('maintenance-manager.schedules.index', [
                'date' => $date,
            ])
            ->with(
                'success',
                $destinationStatus === 'Finalized'
                    ? 'Work order moved as a Draft Addition. Finalize the new addition before technician dispatch.'
                    : 'Workload moved to the selected working day. Review and finalize the draft schedule.'
            );
    }

    /**
     * Replace one unavailable plumber on an approved, unstarted work order.
     * This is an audited amendment, not a new approval or rescheduling.
     */
    public function replacePlumber(
        Request $request,
        MaintenanceWorkOrder $workOrder,
        MaintenanceWorkOrderValidationService $validation
    ) {
        $data = $request->validate([
            'old_technician_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'new_technician_id' => [
                'required',
                'integer',
                'different:old_technician_id',
                'exists:users,id',
            ],
            'reason' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
        ]);

        $replacementTechnicianId = (int) $data['new_technician_id'];

        $auditData = [];

        $scheduleDate = DB::transaction(function () use (
            $request,
            $workOrder,
            $validation,
            $data,
            $replacementTechnicianId,
            &$auditData
        ) {
            $order = MaintenanceWorkOrder::query()
                ->lockForUpdate()
                ->findOrFail($workOrder->id);

            $schedule = MaintenanceSchedule::query()
                ->lockForUpdate()
                ->findOrFail($order->maintenance_schedule_id);

            if (
                $schedule->status !== 'Finalized' ||
                $order->approval_status !== 'Approved'
            ) {
                throw ValidationException::withMessages([
                    'work_order' =>
                        'Only approved work orders on finalized schedules can be amended.',
                ]);
            }

            if (
                $order->status !== 'Scheduled' ||
                $order->started_at !== null ||
                $order->completed_at !== null
            ) {
                throw ValidationException::withMessages([
                    'work_order' =>
                        'This work order has already started or changed status. Use the handover process instead.',
                ]);
            }

            $date = $schedule->schedule_date->format('Y-m-d');

            $validation->validateWorkingDate($date);

            $oldId = (int) $data['old_technician_id'];
            $newId = $replacementTechnicianId;

            $oldTeam = $validation->teamIds($order);
            sort($oldTeam);

            if (!in_array($oldId, $oldTeam, true)) {
                throw ValidationException::withMessages([
                    'old_technician_id' =>
                        'The plumber is no longer assigned to this work order.',
                ]);
            }

            if (in_array($newId, $oldTeam, true)) {
                throw ValidationException::withMessages([
                    'new_technician_id' =>
                        'The replacement is already on this work-order team.',
                ]);
            }

            $duties = $validation->lockDuties($date, [$newId]);

            $validation->validateTechnicianForDate(
                $newId,
                $date,
                'new_technician_id',
                $duties
            );

            // Preserve the roles of all unaffected team members.
            $teamRoles = DB::table('maintenance_work_order_technicians')
                ->where('maintenance_work_order_id', $order->id)
                ->lockForUpdate()
                ->pluck('assignment_role', 'technician_id')
                ->all();

            $oldRole = $teamRoles[$oldId] ?? 'Member';

            unset($teamRoles[$oldId]);

            $teamRoles[$newId] = $oldRole;

            $sync = [];

            foreach ($teamRoles as $id => $role) {
                $sync[(int) $id] = [
                    'assignment_role' => $role,
                ];
            }

            $order->technicians()->sync($sync);

            $newTeam = array_map(
                'intval',
                array_keys($teamRoles)
            );

            sort($newTeam);

            // Preserve complaint-level assignment history.
            $existingComplaintAssignment = DB::table('complaint_technicians')
                ->where('complaint_id', $order->complaint_id)
                ->where('technician_id', $newId)
                ->exists();

            if (!$existingComplaintAssignment) {
                $now = now('Asia/Manila');

                DB::table('complaint_technicians')->insert([
                    'complaint_id' => $order->complaint_id,
                    'technician_id' => $newId,
                    'status' => 'Assigned',
                    'assigned_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            MaintenanceScheduleHistory::create([
                'maintenance_work_order_id' => $order->id,
                'performed_by' => $request->user()->id,
                'action' => 'Plumber Replaced',
                'old_values' => [
                    'schedule_date' => $date,
                    'technician_ids' => $oldTeam,
                    'replaced_technician_id' => $oldId,
                    'approval_status' => 'Approved',
                ],
                'new_values' => [
                    'schedule_date' => $date,
                    'technician_ids' => $newTeam,
                    'replacement_technician_id' => $newId,
                    'approval_status' => 'Approved',
                ],
                'reason' => $data['reason'],
            ]);

            // Collect audit details inside the transaction, but write the
            // audit entry only after the transaction successfully commits.
            $auditData = [
                'date' => $date,
                'old_team' => $oldTeam,
                'new_team' => $newTeam,
                'old_technician_id' => $oldId,
                'new_technician_id' => $newId,
                'reason' => $data['reason'],
            ];

            return $date;
        }, 3);

        AuditLogger::log(
            action: 'Technician Replaced',
            module: 'Maintenance Scheduling',
            description: "Replaced technician #{$auditData['old_technician_id']} with technician #{$auditData['new_technician_id']} on work order #{$workOrder->id}.",
            subject: $workOrder,
            oldValues: [
                'schedule_date' => $auditData['date'],
                'technician_ids' => $auditData['old_team'],
                'replaced_technician_id' =>
                    $auditData['old_technician_id'],
            ],
            newValues: [
                'schedule_date' => $auditData['date'],
                'technician_ids' => $auditData['new_team'],
                'replacement_technician_id' =>
                    $auditData['new_technician_id'],
                'reason' => $auditData['reason'],
            ]
        );

        // Preserve the existing notification flow.
        $this->notifyScheduleTechnicians(
            (int) $workOrder->id,
            [$replacementTechnicianId],
            'replacement'
        );

        return redirect()
            ->route('maintenance-manager.schedules.index', [
                'date' => $scheduleDate,
            ])
            ->with(
                'success',
                'Plumber replaced successfully. The finalized schedule and work-order approval remain unchanged.'
            );
    }

    public function batchStore(
        Request $request,
        BatchMaintenancePlanningService $service
    ) {
        $jobs = collect($request->input('jobs', []))
            ->map(function ($job) {
                if (!is_array($job)) {
                    return $job;
                }

                if (
                    isset($job['technician_ids']) &&
                    is_array($job['technician_ids'])
                ) {
                    $job['technician_ids'] = array_values(
                        array_unique($job['technician_ids'])
                    );
                }

                return $job;
            })
            ->all();

        $request->merge(['jobs' => $jobs]);

        $validated = $request->validate([
            'schedule_date' => [
                'required',
                'date_format:Y-m-d',
            ],
            'jobs' => [
                'required',
                'array',
                'min:1',
                'max:50',
            ],
            'jobs.*.complaint_id' => [
                'required',
                'integer',
                'distinct',
                'exists:complaints,id',
            ],
            'jobs.*.technician_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'jobs.*.technician_ids.*' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'jobs.*.override_reason' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'schedule_date.required' =>
                'Please select a working date.',
            'schedule_date.date_format' =>
                'The selected working date is invalid.',
            'jobs.required' =>
                'Please select at least one complaint.',
            'jobs.min' =>
                'Please select at least one complaint to continue.',
            'jobs.max' =>
                'You can plan up to 50 complaints at a time.',
            'jobs.*.complaint_id.required' =>
                'A selected complaint is missing. Please select it again.',
            'jobs.*.complaint_id.distinct' =>
                'A complaint has been selected more than once. Please review your selections.',
            'jobs.*.complaint_id.exists' =>
                'One of the selected complaints is no longer available. Refresh the page and try again.',
            'jobs.*.technician_ids.required' =>
                'Please assign at least one plumber to every selected complaint.',
            'jobs.*.technician_ids.min' =>
                'Please assign at least one plumber to every selected complaint.',
            'jobs.*.technician_ids.*.required' =>
                'A plumber selection is incomplete. Please review the assigned team.',
            'jobs.*.technician_ids.*.integer' =>
                'One of the plumber selections is invalid. Please select the team again.',
            'jobs.*.technician_ids.*.exists' =>
                'One of the selected plumbers is no longer available. Refresh the page and try again.',
            'jobs.*.override_reason.max' =>
                'The scheduling explanation must not exceed 2,000 characters.',
        ]);

        $schedule = $service->create(
            $validated,
            (int) $request->user()->id
        );

        AuditLogger::log(
            action: 'Batch Work Orders Scheduled',
            module: 'Maintenance Scheduling',
            description: 'Created a batch maintenance plan for '
                . count($validated['jobs'])
                . ' complaint(s) on '
                . $validated['schedule_date']
                . '.',
            subject: $schedule instanceof Model ? $schedule : null,
            oldValues: null,
            newValues: [
                'schedule_date' => $validated['schedule_date'],
                'schedule_status' => $schedule->status ?? null,
                'complaint_ids' => array_map(
                    'intval',
                    array_column($validated['jobs'], 'complaint_id')
                ),
                'job_count' => count($validated['jobs']),
            ]
        );

        return redirect()
            ->route('maintenance-manager.schedules.index', [
                'date' => $schedule->schedule_date->format('Y-m-d'),
            ])
            ->with(
                'success',
                $schedule->status === 'Finalized'
                    ? 'Additional workload saved as Draft Additions. Manager approval is required before technician dispatch.'
                    : 'Daily workload saved in Draft. The manager may review and finalize it.'
            );
    }

    /**
     * Queue schedule-related email notifications after a successful
     * transaction.
     *
     * Supported types: finalized, addition, replacement.
     */
    private function notifyScheduleTechnicians(
        int $workOrderId,
        array $technicianIds,
        string $notificationType
    ): void {
        try {
            $order = MaintenanceWorkOrder::query()
                ->with([
                    'complaint.division',
                    'complaint.category',
                    'complaint.aiAnalysis',
                    'schedule',
                ])
                ->find($workOrderId);

            if (!$order || !$order->complaint) {
                Log::warning(
                    'Schedule notification skipped: work order or complaint not found.',
                    [
                        'work_order_id' => $workOrderId,
                        'notification_type' => $notificationType,
                    ]
                );

                return;
            }

            $technicianIds = array_values(array_unique(array_map(
                'intval',
                $technicianIds
            )));

            if (empty($technicianIds)) {
                return;
            }

            $technicians = User::query()
                ->whereIn('id', $technicianIds)
                ->get();

            foreach ($technicians as $technician) {
                if (empty($technician->email)) {
                    Log::warning(
                        'Schedule notification skipped: technician has no email address.',
                        [
                            'work_order_id' => $workOrderId,
                            'technician_id' => $technician->id,
                            'notification_type' => $notificationType,
                        ]
                    );

                    continue;
                }

                try {
                    $technician->notify(
                        new MaintenanceScheduleFinalized(
                            $order,
                            $notificationType
                        )
                    );
                } catch (Throwable $exception) {
                    Log::error(
                        'Failed to queue maintenance schedule notification.',
                        [
                            'work_order_id' => $workOrderId,
                            'technician_id' => $technician->id,
                            'notification_type' => $notificationType,
                            'error' => $exception->getMessage(),
                        ]
                    );
                }
            }
        } catch (Throwable $exception) {
            Log::error(
                'Failed to prepare maintenance schedule notifications.',
                [
                    'work_order_id' => $workOrderId,
                    'notification_type' => $notificationType,
                    'error' => $exception->getMessage(),
                ]
            );
        }
    }
}
