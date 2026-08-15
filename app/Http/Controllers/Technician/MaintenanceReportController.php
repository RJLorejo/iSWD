<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
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
    | Maintenance Report Dashboard / Reports
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
        */

        $baseQuery = Complaint::where(
            'assigned_to',
            $technicianId
        );

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
            ? round(
                ($completedCount / $totalAssigned) * 100,
                1
            )
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

        if ($completedComplaints->count() > 0) {

            $totalHours = $completedComplaints->sum(
                function ($complaint) {

                    return $complaint->created_at
                        ->diffInMinutes(
                            $complaint->completed_at
                        ) / 60;
                }
            );

            $averageCompletionHours = round(
                $totalHours /
                    $completedComplaints->count(),
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
        ])->mapWithKeys(
            function ($priority) use (
                $baseQuery,
                $from,
                $to
            ) {

                return [
                    $priority => (clone $baseQuery)
                        ->where(
                            'priority',
                            $priority
                        )
                        ->whereBetween(
                            'created_at',
                            [$from, $to]
                        )
                        ->count(),
                ];
            }
        );

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
        ])->mapWithKeys(
            function ($status) use (
                $baseQuery,
                $from,
                $to
            ) {

                $dateColumn = $status === 'Completed'
                    ? 'completed_at'
                    : 'updated_at';

                return [
                    $status => (clone $baseQuery)
                        ->where(
                            'status',
                            $status
                        )
                        ->whereBetween(
                            $dateColumn,
                            [$from, $to]
                        )
                        ->count(),
                ];
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Completed Maintenance
        |--------------------------------------------------------------------------
        */

        $completedComplaintsList = (clone $baseQuery)
            ->with([
                'consumer',
                'category',
                'maintenanceReport',
            ])
            ->where(
                'status',
                'Completed'
            )
            ->whereBetween(
                'completed_at',
                [$from, $to]
            )
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
                'maintenanceReport',
            ])
            ->whereIn(
                'status',
                [
                    'Assigned',
                    'In Progress',
                ]
            )
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
                'This maintenance cannot be started from the current status.'
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
                'Maintenance started. Complete the maintenance report below.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Create Maintenance Report
    |--------------------------------------------------------------------------
    */

    public function create(Complaint $complaint)
    {
        $this->authorizeTechnician($complaint);

        if (!in_array(
            $complaint->status,
            [
                'Assigned',
                'In Progress',
            ]
        )) {

            return redirect()
                ->route(
                    'technician.complaints.show',
                    $complaint
                )
                ->with(
                    'error',
                    'This complaint is no longer available for maintenance reporting.'
                );
        }

        $complaint->load([
            'consumer',
            'category',
            'technician',
            'maintenanceReport',
        ]);

        return view(
            'technician.maintenance.report',
            compact('complaint')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Submit Maintenance Report
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Complaint $complaint
    ) {

        $this->authorizeTechnician($complaint);

        if ($complaint->status !== 'In Progress') {

            return redirect()
                ->route(
                    'technician.complaints.show',
                    $complaint
                )
                ->with(
                    'error',
                    'Maintenance must be started before submitting a report.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'diagnosis' => [
                'required',
                'string',
                'max:5000',
            ],

            'root_cause' => [
                'required',
                'string',
                'max:5000',
            ],

            'work_performed' => [
                'required',
                'string',
                'max:10000',
            ],

            'repair_procedure' => [
                'required',
                'string',
                'max:10000',
            ],

            'materials_used' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'parts_replaced' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'tools_used' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'technician_notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'completion_remarks' => [
                'required',
                'string',
                'max:10000',
            ],

            'before_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'after_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Save Report
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $request,
            $complaint,
            $validated
        ) {

            $report = MaintenanceReport::firstOrNew([
                'complaint_id' => $complaint->id,
            ]);

            $report->technician_id = Auth::id();

            $report->diagnosis =
                $validated['diagnosis'];

            $report->root_cause =
                $validated['root_cause'];

            $report->work_performed =
                $validated['work_performed'];

            $report->repair_procedure =
                $validated['repair_procedure'];

            $report->materials_used =
                $validated['materials_used'] ?? null;

            $report->parts_replaced =
                $validated['parts_replaced'] ?? null;

            $report->tools_used =
                $validated['tools_used'] ?? null;

            $report->technician_notes =
                $validated['technician_notes'] ?? null;

            $report->completion_remarks =
                $validated['completion_remarks'];

            /*
            |--------------------------------------------------------------------------
            | Before Photo
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('before_photo')) {

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

            /*
            |--------------------------------------------------------------------------
            | After Photo
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('after_photo')) {

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

            $report->submitted_at = now();

            $report->save();

            /*
            |--------------------------------------------------------------------------
            | Complete Complaint
            |--------------------------------------------------------------------------
            */

            $complaint->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);
        });

        return redirect()
            ->route(
                'technician.maintenance-reports.show',
                $complaint
            )
            ->with(
                'success',
                'Maintenance report submitted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Show Individual Maintenance Report
    |--------------------------------------------------------------------------
    */

    public function show(Complaint $complaint)
    {
        $this->authorizeTechnician($complaint);

        $complaint->load([
            'consumer',
            'category',
            'technician',
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
    | Print Individual Maintenance Report
    |--------------------------------------------------------------------------
    */

    public function printReport(Complaint $complaint)
    {
        $this->authorizeTechnician($complaint);

        $complaint->load([
            'consumer',
            'category',
            'technician',
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
            'maintenanceReport',
        ])
            ->where(
                'assigned_to',
                $technicianId
            )
            ->where(
                'status',
                'Completed'
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

                    return $complaint->created_at
                        ->diffInMinutes(
                            $complaint->completed_at
                        ) / 60;
                }
            );

            $averageCompletionHours =
                round(
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
            (int) $complaint->assigned_to ===
                (int) Auth::id(),
            403,
            'You are not authorized to manage this maintenance report.'
        );
    }
}
