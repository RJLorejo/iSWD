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

class MaintenanceReportController extends Controller
{
    public function index(Request $request)
    {
        $technicianId = Auth::id();

        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $baseQuery = Complaint::query()
            ->whereHas('technicians', function ($query) use ($technicianId) {
                $query->where('users.id', $technicianId);
            });

        $totalAssigned = (clone $baseQuery)
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $assignedCount = (clone $baseQuery)
            ->where('status', 'Assigned')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $inProgressCount = (clone $baseQuery)
            ->where('status', 'In Progress')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $completedCount = (clone $baseQuery)
            ->where('status', 'Completed')
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$from, $to])
            ->count();

        $closedCount = (clone $baseQuery)
            ->where('status', 'Closed')
            ->whereBetween('updated_at', [$from, $to])
            ->count();

        $urgentCount = (clone $baseQuery)
            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])
            ->whereHas('aiAnalysis', function ($query) {
                $query->whereRaw(
                    'UPPER(urgency_level) = ?',
                    ['HIGH']
                );
            })
            ->count();

        $activeCount = $assignedCount + $inProgressCount;

        $completionRate = $totalAssigned > 0
            ? round(($completedCount / $totalAssigned) * 100, 1)
            : 0;

        $completedForAverage = (clone $baseQuery)
            ->where('status', 'Completed')
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$from, $to])
            ->get([
                'id',
                'created_at',
                'completed_at',
            ]);

        $averageCompletionHours = 0;

        if ($completedForAverage->isNotEmpty()) {
            $totalHours = $completedForAverage->sum(
                function ($complaint) {
                    if (
                        !$complaint->created_at ||
                        !$complaint->completed_at
                    ) {
                        return 0;
                    }

                    return $complaint->created_at
                        ->diffInMinutes($complaint->completed_at) / 60;
                }
            );

            $averageCompletionHours = round(
                $totalHours / $completedForAverage->count(),
                1
            );
        }

        $urgencyBreakdown = collect([
            'High',
            'Moderate',
            'Low',
        ])->mapWithKeys(
            function ($urgency) use (
                $baseQuery,
                $from,
                $to
            ) {
                return [
                    $urgency => (clone $baseQuery)
                        ->whereBetween(
                            'created_at',
                            [$from, $to]
                        )
                        ->whereHas(
                            'aiAnalysis',
                            function ($query) use ($urgency) {
                                $query->whereRaw(
                                    'UPPER(urgency_level) = ?',
                                    [strtoupper($urgency)]
                                );
                            }
                        )
                        ->count(),
                ];
            }
        );

        $notAssessedCount = (clone $baseQuery)
            ->whereBetween('created_at', [$from, $to])
            ->where(function ($query) {
                $query
                    ->whereDoesntHave('aiAnalysis')
                    ->orWhereHas(
                        'aiAnalysis',
                        function ($aiQuery) {
                            $aiQuery
                                ->whereNull('urgency_level')
                                ->orWhere(
                                    'urgency_level',
                                    ''
                                );
                        }
                    );
            })
            ->count();

        $urgencyBreakdown->put(
            'Not Assessed',
            $notAssessedCount
        );

        $statusBreakdown = collect([
            'Assigned' => $assignedCount,
            'In Progress' => $inProgressCount,
            'Completed' => $completedCount,
            'Closed' => $closedCount,
        ]);

        $currentComplaints = (clone $baseQuery)
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
            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])
            ->get()
            ->sortBy(function ($complaint) {
                $urgencyOrder = match (strtoupper(
                    trim(
                        $complaint->aiAnalysis?->urgency_level ?? ''
                    )
                )) {
                    'HIGH' => 1,
                    'MODERATE' => 2,
                    'LOW' => 3,
                    default => 4,
                };

                $statusOrder = match ($complaint->status) {
                    'In Progress' => 1,
                    'Assigned' => 2,
                    default => 3,
                };

                return sprintf(
                    '%d-%d-%020d',
                    $urgencyOrder,
                    $statusOrder,
                    PHP_INT_MAX -
                        ($complaint->updated_at?->timestamp ?? 0)
                );
            })
            ->values();

        $completedComplaintsList = (clone $baseQuery)
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
            ->where('status', 'Completed')
            ->whereNotNull('completed_at')
            ->whereBetween(
                'completed_at',
                [$from, $to]
            )
            ->latest('completed_at')
            ->get();

        return view(
            'technician.reports.maintenance',
            compact(
                'from',
                'to',
                'totalAssigned',
                'assignedCount',
                'inProgressCount',
                'completedCount',
                'closedCount',
                'urgentCount',
                'activeCount',
                'completionRate',
                'averageCompletionHours',
                'urgencyBreakdown',
                'notAssessedCount',
                'statusBreakdown',
                'currentComplaints',
                'completedComplaintsList'
            )
        );
    }

    public function start(Complaint $complaint)
    {
        $this->authorizeTechnician($complaint);

        if ($complaint->status !== 'Assigned') {
            return back()->with(
                'error',
                'This maintenance work cannot be started from its current status.'
            );
        }

        DB::transaction(function () use ($complaint) {
            $startedAt = now();

            $complaint->update([
                'status' => 'In Progress',
            ]);

            $report = MaintenanceReport::firstOrCreate(
                [
                    'complaint_id' => $complaint->id,
                ],
                [
                    'technician_id' => Auth::id(),
                    'started_at' => $startedAt,
                    'review_status' => 'Draft',
                ]
            );

            if (!$report->started_at) {
                $report->update([
                    'started_at' => $startedAt,
                ]);
            }

            DB::table('complaint_technicians')
                ->where('complaint_id', $complaint->id)
                ->update([
                    'status' => 'In Progress',
                    'started_at' => $startedAt,
                    'updated_at' => $startedAt,
                ]);
        });

        return redirect()
            ->route(
                'technician.maintenance-reports.create',
                $complaint
            )
            ->with(
                'success',
                'Maintenance started for the assigned team. Complete the accomplishment report after performing the work.'
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

        $complaint->loadMissing(
            'maintenanceReport'
        );

        $report = $complaint->maintenanceReport;

        if (
            $report &&
            $report->review_status === 'Approved'
        ) {
            return back()->with(
                'error',
                'This accomplishment report has already been approved and cannot be modified.'
            );
        }

        if (
            $report &&
            $report->review_status === 'Pending Review'
        ) {
            return back()->with(
                'error',
                'This accomplishment report has already been submitted and is waiting for management review.'
            );
        }

        if (
            !in_array(
                $complaint->status,
                [
                    'In Progress',
                    'Completed',
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
                    'Maintenance must be started before submitting an accomplishment report.'
                );
        }

        $validated = $request->validated();

        DB::transaction(
            function () use (
                $request,
                $complaint,
                &$report,
                $validated
            ) {
                if (!$report) {
                    $report = MaintenanceReport::create([
                        'complaint_id' => $complaint->id,
                        'technician_id' => Auth::id(),
                        'started_at' => now(),
                        'review_status' => 'Draft',
                    ]);
                }

                if (
                    $request->hasFile(
                        'before_photo'
                    )
                ) {
                    if ($report->before_photo) {
                        Storage::disk('public')
                            ->delete(
                                $report->before_photo
                            );
                    }

                    $report->before_photo =
                        $request
                        ->file('before_photo')
                        ->store(
                            'maintenance-reports/before',
                            'public'
                        );
                }

                if (
                    $request->hasFile(
                        'after_photo'
                    )
                ) {
                    if ($report->after_photo) {
                        Storage::disk('public')
                            ->delete(
                                $report->after_photo
                            );
                    }

                    $report->after_photo =
                        $request
                        ->file('after_photo')
                        ->store(
                            'maintenance-reports/after',
                            'public'
                        );
                }

                $isResubmission =
                    $report->review_status ===
                    'Returned';

                $submittedAt = now();

                $report->diagnosis =
                    $validated['diagnosis'];

                $report->root_cause =
                    $validated['root_cause'];

                $report->materials_parts =
                    $validated['materials_parts']
                    ?? null;

                $report->technician_notes =
                    $validated['technician_notes']
                    ?? null;

                if (!$report->started_at) {
                    $report->started_at =
                        $submittedAt;
                }

                $report->submitted_at =
                    $submittedAt;

                $report->technician_id = Auth::id();

                if ($isResubmission) {
                    $report->resubmitted_at =
                        $submittedAt;

                    $report->revision_number =
                        ($report->revision_number ?? 0)
                        + 1;
                }

                $report->review_status =
                    'Pending Review';

                $report->reviewed_by = null;
                $report->reviewed_at = null;
                $report->review_remarks = null;

                $report->save();

                $complaint->update([
                    'status' => 'Completed',
                    'completed_at' => $submittedAt,
                ]);

                DB::table('complaint_technicians')
                    ->where('complaint_id', $complaint->id)
                    ->update([
                        'status' => 'Completed',
                        'completed_at' => $submittedAt,
                        'updated_at' => $submittedAt,
                    ]);
            }
        );

        return redirect()
            ->route(
                'technician.maintenance-reports.show',
                $complaint
            )
            ->with(
                'success',
                'Maintenance accomplished successfully. The accomplishment report has been submitted for manager review.'
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
            !in_array(
                $report->review_status,
                [
                    'Pending Review',
                    'Returned',
                    'Approved',
                ],
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

        return view(
            'technician.maintenance.show',
            compact('complaint')
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
                in_array(
                    $report->review_status,
                    [
                        'Pending Review',
                        'Returned',
                        'Approved',
                    ],
                    true
                ),
            404
        );

        return view(
            'technician.maintenance.print',
            compact('complaint')
        );
    }

    public function print(Request $request)
    {
        $technicianId = Auth::id();

        $from = $request->filled('from')
            ? Carbon::parse(
                $request->from
            )->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse(
                $request->to
            )->endOfDay()
            : now()->endOfDay();

        $complaints = Complaint::query()
            ->with([
                'consumer',
                'consumer.address',
                'category',
                'division',
                'technicians',
                'maintenanceReport',
                'aiAnalysis',
            ])
            ->whereHas(
                'technicians',
                function ($query) use (
                    $technicianId
                ) {
                    $query->where(
                        'users.id',
                        $technicianId
                    );
                }
            )
            ->where('status', 'Completed')
            ->whereNotNull('completed_at')
            ->whereHas(
                'maintenanceReport',
                function ($query) {
                    $query->whereIn(
                        'review_status',
                        [
                            'Pending Review',
                            'Returned',
                            'Approved',
                        ]
                    );
                }
            )
            ->whereBetween(
                'completed_at',
                [$from, $to]
            )
            ->latest('completed_at')
            ->get();

        $total = $complaints->count();

        $averageCompletionHours = 0;

        if ($total > 0) {
            $totalHours = $complaints->sum(
                function ($complaint) {
                    if (
                        !$complaint->created_at ||
                        !$complaint->completed_at
                    ) {
                        return 0;
                    }

                    return $complaint
                        ->created_at
                        ->diffInMinutes(
                            $complaint->completed_at
                        ) / 60;
                }
            );

            $averageCompletionHours = round(
                $totalHours / $total,
                1
            );
        }

        return view(
            'technician.reports.maintenance-print',
            compact(
                'from',
                'to',
                'complaints',
                'total',
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
}
