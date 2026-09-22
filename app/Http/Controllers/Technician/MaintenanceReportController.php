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
    /*
    |--------------------------------------------------------------------------
    | Maintenance Reports
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $technicianId = Auth::id();

        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        | A technician belongs to complaints through the pivot table.
        */

        $baseQuery = Complaint::query()
            ->whereHas('technicians', function ($query) use ($technicianId) {
                $query->where('users.id', $technicianId);
            });

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

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
            ->whereBetween('completed_at', [$from, $to])
            ->count();

        $closedCount = (clone $baseQuery)
            ->where('status', 'Closed')
            ->whereBetween('updated_at', [$from, $to])
            ->count();

        $urgentCount = (clone $baseQuery)
            ->whereIn('priority', ['High', 'Critical'])
            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Completion Rate
        |--------------------------------------------------------------------------
        */

        $completionRate = $totalAssigned > 0
            ? round(($completedCount / $totalAssigned) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Average Completion Time
        |--------------------------------------------------------------------------
        */

        $completedComplaints = (clone $baseQuery)
            ->where('status', 'Completed')
            ->whereNotNull('completed_at')
            ->whereBetween(
                'completed_at',
                [$from, $to]
            )
            ->get([
                'id',
                'created_at',
                'completed_at',
            ]);

        $averageCompletionHours = 0;

        if ($completedComplaints->isNotEmpty()) {
            $totalHours = $completedComplaints->sum(function ($complaint) {
                return $complaint->created_at
                    ->diffInMinutes($complaint->completed_at) / 60;
            });

            $averageCompletionHours = round(
                $totalHours / $completedComplaints->count(),
                1
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Priority Breakdown
        |--------------------------------------------------------------------------
        */

        $priorityBreakdown = collect([
            'Low',
            'Medium',
            'High',
            'Critical',
        ])->mapWithKeys(function ($priority) use (
            $baseQuery,
            $from,
            $to
        ) {
            return [
                $priority => (clone $baseQuery)
                    ->where('priority', $priority)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Status Breakdown
        |--------------------------------------------------------------------------
        */

        $statusBreakdown = collect([
            'Assigned',
            'In Progress',
            'Completed',
            'Closed',
            'Rejected',
        ])->mapWithKeys(function ($status) use (
            $baseQuery,
            $from,
            $to
        ) {
            $dateColumn = $status === 'Completed'
                ? 'completed_at'
                : 'updated_at';

            return [
                $status => (clone $baseQuery)
                    ->where('status', $status)
                    ->whereBetween($dateColumn, [$from, $to])
                    ->count(),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Completed Maintenance
        |--------------------------------------------------------------------------
        */

        $completedComplaintsList = (clone $baseQuery)
            ->with([
                'consumer',
                'category',
                'technicians',
                'maintenanceReport',
            ])
            ->where('status', 'Completed')
            ->whereBetween('completed_at', [$from, $to])
            ->latest('completed_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Current Maintenance
        |--------------------------------------------------------------------------
        */

        $currentComplaints = (clone $baseQuery)
            ->with([
                'consumer',
                'category',
                'technicians',
                'maintenanceReport',
            ])
            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])
            ->orderByRaw("
                CASE
                    WHEN priority = 'Critical' THEN 1
                    WHEN priority = 'High' THEN 2
                    WHEN priority = 'Medium' THEN 3
                    ELSE 4
                END
            ")
            ->latest('updated_at')
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
                'completionRate',
                'averageCompletionHours',
                'priorityBreakdown',
                'statusBreakdown',
                'completedComplaintsList',
                'currentComplaints'
            )
        );
    }
    /*
|--------------------------------------------------------------------------
| Start Maintenance
|--------------------------------------------------------------------------
*/

    public function start(Complaint $complaint)
    {
        $this->authorizeTechnician($complaint);

        if ($complaint->status !== 'Assigned') {
            return back()->with(
                'error',
                'This service cannot be started from the current status.'
            );
        }

        DB::transaction(function () use ($complaint) {
            $complaint->update([
                'status' => 'In Progress',
            ]);

            MaintenanceReport::firstOrCreate(
                [
                    'complaint_id' => $complaint->id,
                ],
                [
                    'technician_id' => Auth::id(),
                    'started_at' => now(),
                    'review_status' => 'Draft',
                ]
            );

            $complaint->technicians()->updateExistingPivot(
                Auth::id(),
                [
                    'status' => 'In Progress',
                    'started_at' => now(),
                ]
            );
        });

        return redirect()
            ->route(
                'technician.maintenance-reports.create',
                $complaint
            )
            ->with(
                'success',
                'Service started. Complete the Service Accomplishment Report after performing the work.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Create / Edit Maintenance Report
    |--------------------------------------------------------------------------
    */
    /*
|--------------------------------------------------------------------------
| Create / Edit Maintenance Report
|--------------------------------------------------------------------------
*/

    public function create(Complaint $complaint)
    {
        $this->authorizeTechnician($complaint);

        $complaint->load([
            'consumer',
            'category',
            'technicians',
            'maintenanceReport.technician',
        ]);

        $report = $complaint->maintenanceReport;

        /*
    |--------------------------------------------------------------------------
    | APPROVED
    |--------------------------------------------------------------------------
    */

        if ($report && $report->review_status === 'Approved') {
            return redirect()
                ->route(
                    'technician.maintenance-reports.show',
                    $complaint
                );
        }

        /*
    |--------------------------------------------------------------------------
    | PENDING REVIEW
    |--------------------------------------------------------------------------
    |
    | Report has already been submitted.
    |
    */

        if ($report && $report->review_status === 'Pending Review') {
            return redirect()
                ->route(
                    'technician.maintenance-reports.show',
                    $complaint
                )
                ->with(
                    'info',
                    'This maintenance report has already been submitted and is awaiting manager validation.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | DRAFT
    |--------------------------------------------------------------------------
    |
    | Technician can continue editing the report.
    |
    */

        if ($report && $report->review_status === 'Draft') {
            return view(
                'technician.maintenance.report',
                compact(
                    'complaint',
                    'report'
                )
            );
        }

        /*
    |--------------------------------------------------------------------------
    | RETURNED
    |--------------------------------------------------------------------------
    |
    | Manager returned the report for correction.
    |
    */

        if ($report && $report->review_status === 'Returned') {
            return view(
                'technician.maintenance.report',
                compact(
                    'complaint',
                    'report'
                )
            );
        }

        /*
    |--------------------------------------------------------------------------
    | NEW REPORT
    |--------------------------------------------------------------------------
    */

        if (! in_array(
            $complaint->status,
            ['Assigned', 'In Progress'],
            true
        )) {
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

    /*
    |--------------------------------------------------------------------------
    | Submit Maintenance Report
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreMaintenanceReportRequest $request,
        Complaint $complaint
    ) {
        $this->authorizeTechnician($complaint);

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
                    'Accomplished',
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
                    'Service must be started before submitting an accomplishment report.'
                );
        }

        $validated = $request->validated();

        DB::transaction(function () use (
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

            if ($request->hasFile('before_photo')) {
                if ($report->before_photo) {
                    Storage::disk('public')
                        ->delete($report->before_photo);
                }

                $report->before_photo = $request
                    ->file('before_photo')
                    ->store(
                        'maintenance-reports/before',
                        'public'
                    );
            }

            if ($request->hasFile('after_photo')) {
                if ($report->after_photo) {
                    Storage::disk('public')
                        ->delete($report->after_photo);
                }

                $report->after_photo = $request
                    ->file('after_photo')
                    ->store(
                        'maintenance-reports/after',
                        'public'
                    );
            }

            $isResubmission =
                $report->review_status === 'Returned';

            $report->diagnosis =
                $validated['diagnosis'];

            $report->root_cause =
                $validated['root_cause'];



            $report->parts_replaced =
                $validated['parts_replaced'] ?? null;


            $report->technician_notes =
                $validated['technician_notes'] ?? null;

            $report->completion_remarks =
                $validated['completion_remarks'];

            if (!$report->started_at) {
                $report->started_at = now();
            }

            $report->submitted_at = now();

            if ($isResubmission) {
                $report->resubmitted_at = now();

                $report->revision_number =
                    ($report->revision_number ?? 0) + 1;
            }

            $report->review_status =
                'Pending Review';

            $report->reviewed_by = null;
            $report->reviewed_at = null;
            $report->review_remarks = null;

            $report->save();

            $complaint->update([
                'status' => 'Accomplished',
                'completed_at' => null,
            ]);

            $complaint
                ->technicians()
                ->updateExistingPivot(
                    Auth::id(),
                    [
                        'status' => 'Accomplished',
                        'completed_at' => now(),
                    ]
                );
        });

        return redirect()
            ->route(
                'technician.maintenance-reports.show',
                $complaint
            )
            ->with(
                'success',
                'Service accomplished successfully. The accomplishment report has been submitted for management review.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Show Individual Report
    |--------------------------------------------------------------------------
    */

    public function show(Complaint $complaint)
    {
        $this->authorizeTechnician($complaint);

        $complaint->load([
            'consumer',
            'category',
            'technicians',
            'maintenanceReport.technician',
        ]);

        if (!$complaint->maintenanceReport) {

            return redirect()
                ->route(
                    'technician.maintenance-reports.create',
                    $complaint
                )
                ->with(
                    'error',
                    'No maintenance report has been submitted yet.'
                );
        }

        return view(
            'technician.maintenance.show',
            compact('complaint')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Print Individual Report
    |--------------------------------------------------------------------------
    */

    public function printReport(Complaint $complaint)
    {
        $this->authorizeTechnician($complaint);

        $complaint->load([
            'consumer',
            'category',
            'technicians',
            'maintenanceReport.technician',
        ]);

        abort_unless(
            $complaint->maintenanceReport,
            404
        );

        return view(
            'technician.maintenance.print',
            compact('complaint')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Printable Maintenance Summary
    |--------------------------------------------------------------------------
    */

    public function print(Request $request)
    {
        $technicianId = Auth::id();

        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $complaints = Complaint::with([
            'consumer',
            'category',
            'technicians',
            'maintenanceReport',
        ])
            ->whereHas('technicians', function ($query) use ($technicianId) {
                $query->where('users.id', $technicianId);
            })
            ->where('status', 'Completed')
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
                    return $complaint->created_at
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


    /*
    |--------------------------------------------------------------------------
    | Technician Authorization
    |--------------------------------------------------------------------------
    */

    private function authorizeTechnician(
        Complaint $complaint
    ): void {

        abort_unless(
            $complaint->technicians()
                ->where('users.id', Auth::id())
                ->exists(),
            403,
            'You are not authorized to manage this maintenance report.'
        );
    }
}
