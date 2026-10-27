<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceReport;
use App\Notifications\ComplaintCompleted;
use App\Notifications\PlumberReportReturned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaintenanceReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceReport::query()
            ->with([
                'complaint.consumer',
                'complaint.category',
                'complaint.division',
                'complaint.aiAnalysis',
                'technician',
                'reviewer',
            ])
            ->whereNotNull('submitted_at');

        $status = $request->get(
            'status',
            'Pending Review'
        );

        if (
            in_array(
                $status,
                [
                    'Pending Review',
                    'Returned',
                    'Approved',
                ],
                true
            )
        ) {
            $query->where(
                'review_status',
                $status
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->whereHas(
                'complaint',
                function ($q) use ($search) {
                    $q->where(
                        'complaint_no',
                        'like',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'description',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'address',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhereHas(
                            'category',
                            function ($categoryQuery) use ($search) {
                                $categoryQuery->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                );
                            }
                        )
                        ->orWhereHas(
                            'consumer',
                            function ($consumerQuery) use ($search) {
                                $consumerQuery
                                    ->where(
                                        'first_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'last_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'account_number',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                }
            );
        }

        if ($request->filled('from')) {
            $query->whereDate(
                'submitted_at',
                '>=',
                $request->from
            );
        }

        if ($request->filled('to')) {
            $query->whereDate(
                'submitted_at',
                '<=',
                $request->to
            );
        }

        $reports = $query
            ->latest('submitted_at')
            ->paginate(15)
            ->withQueryString();

        $pendingCount = MaintenanceReport::query()
            ->whereNotNull('submitted_at')
            ->where(
                'review_status',
                'Pending Review'
            )
            ->count();

        $returnedCount = MaintenanceReport::query()
            ->whereNotNull('submitted_at')
            ->where(
                'review_status',
                'Returned'
            )
            ->count();

        $approvedCount = MaintenanceReport::query()
            ->whereNotNull('submitted_at')
            ->where(
                'review_status',
                'Approved'
            )
            ->count();

        return view(
            'maintenance-manager.maintenance-reviews.index',
            compact(
                'reports',
                'pendingCount',
                'returnedCount',
                'approvedCount'
            )
        );
    }


    /** Print the full filtered register of submitted maintenance reports. */
    public function printReport(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:All,Pending Review,Returned,Approved'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $status = $filters['status'] ?? 'Pending Review';
        $query = MaintenanceReport::query()
            ->with([
                'complaint.consumer',
                'complaint.category',
                'complaint.division',
                'complaint.aiAnalysis',
                'technician',
                'reviewer',
            ])
            ->whereNotNull('submitted_at');

        if ($status !== 'All') {
            $query->where('review_status', $status);
        }

        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $query->whereHas('complaint', function ($complaintQuery) use ($search) {
                $complaintQuery->where(function ($q) use ($search) {
                    $q->where('complaint_no', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhereHas('category', fn ($category) =>
                            $category->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('consumer', function ($consumer) use ($search) {
                            $consumer->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('account_number', 'like', "%{$search}%");
                        });
                });
            });
        }

        if (!empty($filters['from'])) {
            $query->whereDate('submitted_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->whereDate('submitted_at', '<=', $filters['to']);
        }

        $reports = $query->orderByDesc('submitted_at')->get();
        $counts = [
            'total' => $reports->count(),
            'pending' => $reports->where('review_status', 'Pending Review')->count(),
            'returned' => $reports->where('review_status', 'Returned')->count(),
            'approved' => $reports->where('review_status', 'Approved')->count(),
        ];

        return view('maintenance-manager.maintenance-reviews.print-report', compact(
            'reports', 'counts', 'filters', 'status'
        ));
    }

    public function show(
        MaintenanceReport $maintenanceReport
    ) {
        $maintenanceReport->load([
            'complaint.consumer',
            'complaint.consumer.address',
            'complaint.category',
            'complaint.division',
            'complaint.aiAnalysis',
            'complaint.technicians',
            'complaint.commercialResolution.processor',
            'complaint.commercialResolution.forwarder',
            'technician',
            'reviewer',
        ]);

        if (!$maintenanceReport->submitted_at) {
            return redirect()
                ->route(
                    'maintenance-manager.maintenance-reviews.index'
                )
                ->with(
                    'error',
                    'This accomplishment report has not been submitted yet.'
                );
        }

        return view(
            'maintenance-manager.maintenance-reviews.show',
            compact('maintenanceReport')
        );
    }

    public function approve(
        Request $request,
        MaintenanceReport $maintenanceReport
    ) {
        $validated = $request->validate([
            'review_remarks' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ]);

        $maintenanceReport->load('complaint');

        if (!$maintenanceReport->submitted_at) {
            return back()->with(
                'error',
                'This accomplishment report has not been submitted yet.'
            );
        }

        if (!$maintenanceReport->complaint) {
            return back()->with(
                'error',
                'The complaint associated with this accomplishment report could not be found.'
            );
        }

        if (
            $maintenanceReport->review_status ===
            'Approved'
        ) {
            return back()->with(
                'error',
                'This accomplishment report has already been approved.'
            );
        }

        if (
            $maintenanceReport->review_status !==
            'Pending Review'
        ) {
            return back()->with(
                'error',
                'Only accomplishment reports pending review can be approved.'
            );
        }

        if (
            $maintenanceReport->complaint->status !==
            'Completed'
        ) {
            return back()->with(
                'error',
                'Only accomplished maintenance work can be approved.'
            );
        }

        DB::transaction(
            function () use (
                $maintenanceReport,
                $validated
            ) {
                $maintenanceReport->update([
                    'review_status' => 'Approved',
                    'reviewed_by' => Auth::id(),
                    'reviewed_at' => now(),
                    'review_remarks' =>
                    $validated['review_remarks']
                        ?? null,
                ]);

                /*
                 * Keep the complaint as Completed.
                 * In SWD staff UI this is displayed as Accomplished.
                 *
                 * Approval and closure are separate actions.
                 */
            }
        );

        return redirect()
            ->route(
                'maintenance-manager.maintenance-reviews.show',
                $maintenanceReport
            )
            ->with(
                'success',
                'Accomplishment report approved. You may now finalize and close the complaint.'
            );
    }

    public function returnForCorrection(
        Request $request,
        MaintenanceReport $maintenanceReport
    ) {
        $validated = $request->validate([
            'review_remarks' => [
                'required',
                'string',
                'max:10000',
            ],
        ], [
            'review_remarks.required' =>
            'Please provide the corrections required.',
        ]);

        $result = DB::transaction(function () use (
            $maintenanceReport,
            $validated
        ) {
            // Lock the report so two requests cannot update it simultaneously.
            $report = MaintenanceReport::query()
                ->lockForUpdate()
                ->findOrFail($maintenanceReport->id);

            if (!$report->submitted_at) {
                return 'not_submitted';
            }

            // Repeated submissions are handled without modifying the report.
            if ($report->review_status === 'Returned') {
                return 'already_returned';
            }

            if ($report->review_status !== 'Pending Review') {
                return 'invalid_status';
            }

            $complaint = $report->complaint;

            if (!$complaint || $complaint->status !== 'Completed') {
                return 'invalid_complaint';
            }

            $report->update([
                'review_status' => 'Returned',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_remarks' => $validated['review_remarks'],
            ]);

            return 'returned';
        });

        if ($result === 'not_submitted') {
            return redirect()
                ->route('maintenance-manager.maintenance-reviews.index')
                ->with('error', 'This accomplishment report has not been submitted yet.');
        }

        if ($result === 'already_returned') {
            return redirect()
                ->route('maintenance-manager.maintenance-reviews.show', $maintenanceReport)
                ->with('success', 'This accomplishment report has already been returned for correction. No duplicate action was performed.');
        }

        if ($result !== 'returned') {
            return redirect()
                ->route('maintenance-manager.maintenance-reviews.show', $maintenanceReport)
                ->with('error', 'This accomplishment report is no longer eligible for return.');
        }

        $maintenanceReport->refresh();

        $maintenanceReport->load([
            'complaint.technicians',
        ]);

        $technicians = $maintenanceReport->complaint?->technicians
            ?? collect();

        foreach ($technicians->unique('id') as $technician) {
            if (!$technician->email) {
                continue;
            }

            try {
                $technician->notify(
                    new PlumberReportReturned($maintenanceReport)
                );
            } catch (\Throwable $exception) {
                Log::error('Failed to queue returned report notification.', [
                    'maintenance_report_id' => $maintenanceReport->id,
                    'technician_id' => $technician->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
        return redirect()
            ->route('maintenance-manager.maintenance-reviews.show', $maintenanceReport)
            ->with(
                'success',
                'Accomplishment report returned for correction successfully. The review decision has been saved.'
            );
    }

    public function close(
        MaintenanceReport $maintenanceReport
    ) {
        $maintenanceReport->load([
            'complaint.consumer.user',
            'complaint.category',
            'complaint.division',
        ]);

        $complaint = $maintenanceReport->complaint;

        if (!$maintenanceReport->submitted_at) {
            return back()->with(
                'error',
                'This accomplishment report has not been submitted yet.'
            );
        }

        if (!$complaint) {
            return back()->with(
                'error',
                'The complaint associated with this accomplishment report could not be found.'
            );
        }

        if (
            $maintenanceReport->review_status !==
            'Approved'
        ) {
            return back()->with(
                'error',
                'The accomplishment report must be approved before the complaint can be closed.'
            );
        }

        if ($complaint->status === 'Closed') {
            return back()->with(
                'error',
                'This complaint has already been closed.'
            );
        }

        if ($complaint->status !== 'Completed') {
            return back()->with(
                'error',
                'Only accomplished maintenance work can be finalized and closed.'
            );
        }

        DB::transaction(
            function () use ($complaint) {
                $complaint->update([
                    'status' => 'Closed',
                ]);
            }
        );

        $complaint->refresh();

        $emailNotificationSent = false;

        $user = $complaint->consumer?->user;

        if ($user?->email) {
            try {
                $user->notify(
                    new ComplaintCompleted(
                        $complaint
                    )
                );

                $emailNotificationSent = true;
            } catch (\Throwable $exception) {
                Log::error(
                    'Maintenance complaint closure email failed.',
                    [
                        'complaint_id' =>
                        $complaint->id,

                        'complaint_no' =>
                        $complaint->complaint_no,

                        'user_id' =>
                        $user->id,

                        'email' =>
                        $user->email,

                        'error' =>
                        $exception->getMessage(),
                    ]
                );
            }
        }

        if ($emailNotificationSent) {
            return redirect()
                ->route(
                    'maintenance-manager.maintenance-reviews.show',
                    $maintenanceReport
                )
                ->with(
                    'success',
                    'Complaint finalized and closed successfully. The consumer has been notified by email.'
                );
        }

        return redirect()
            ->route(
                'maintenance-manager.maintenance-reviews.show',
                $maintenanceReport
            )
            ->with(
                'success',
                'Complaint finalized and closed successfully.'
            );
    }
}
