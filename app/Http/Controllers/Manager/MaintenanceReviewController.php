<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceReport;
use App\Notifications\ComplaintCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaintenanceReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceReport::with([
            'complaint.consumer',
            'complaint.category',
            'complaint.division',
            'technician',
        ]);

        $status = $request->get(
            'status',
            'Pending Review'
        );

        if ($status !== 'All') {
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

        $pendingCount = MaintenanceReport::where(
            'review_status',
            'Pending Review'
        )->count();

        $returnedCount = MaintenanceReport::where(
            'review_status',
            'Returned'
        )->count();

        $approvedCount = MaintenanceReport::where(
            'review_status',
            'Approved'
        )->count();

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

    public function show(MaintenanceReport $maintenanceReport)
    {
        $maintenanceReport->load([
            'complaint.consumer',
            'complaint.category',
            'complaint.division',
            'complaint.technicians',
            'technician',
            'reviewer',
        ]);

        return view(
            'maintenance-manager.maintenance-reviews.show',
            compact('maintenanceReport')
        );
    }

    public function approve(
        Request $request,
        MaintenanceReport $maintenanceReport
    ) {
        $request->validate([
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
                'This accomplishment report cannot be approved because it has not been submitted yet.'
            );
        }

        if ($maintenanceReport->review_status === 'Approved') {
            return back()->with(
                'error',
                'This accomplishment report has already been approved.'
            );
        }

        if ($maintenanceReport->review_status !== 'Pending Review') {
            return back()->with(
                'error',
                'Only accomplishment reports pending review can be approved.'
            );
        }

        if (!$maintenanceReport->complaint) {
            return back()->with(
                'error',
                'The complaint associated with this accomplishment report could not be found.'
            );
        }

        if ($maintenanceReport->complaint->status !== 'Accomplished') {
            return back()->with(
                'error',
                'Only accomplished service work can be approved.'
            );
        }

        DB::transaction(function () use (
            $maintenanceReport,
            $request
        ) {
            $maintenanceReport->update([
                'review_status' => 'Approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_remarks' => $request->review_remarks,
            ]);

            $maintenanceReport->complaint->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);
        });

        $complaint = $maintenanceReport->complaint;

        $complaint->load([
            'consumer.user',
            'category',
            'division',
        ]);

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
                    'Engineering complaint completion email failed.',
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
                    'Service accomplishment approved successfully. The complaint is now completed and the consumer has been notified by email.'
                );
        }

        return redirect()
            ->route(
                'maintenance-manager.maintenance-reviews.show',
                $maintenanceReport
            )
            ->with(
                'success',
                'Service accomplishment approved successfully. The complaint is now completed.'
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

        $maintenanceReport->load('complaint');

        if (!$maintenanceReport->submitted_at) {
            return back()->with(
                'error',
                'This accomplishment report cannot be returned because it has not been submitted yet.'
            );
        }

        if ($maintenanceReport->review_status === 'Approved') {
            return back()->with(
                'error',
                'An approved accomplishment report cannot be returned.'
            );
        }

        if ($maintenanceReport->review_status !== 'Pending Review') {
            return back()->with(
                'error',
                'Only accomplishment reports pending review can be returned for correction.'
            );
        }

        DB::transaction(function () use (
            $maintenanceReport,
            $validated
        ) {
            $maintenanceReport->update([
                'review_status' => 'Returned',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_remarks' => $validated['review_remarks'],
            ]);

            if (
                $maintenanceReport->complaint &&
                $maintenanceReport->complaint->status !== 'Accomplished'
            ) {
                $maintenanceReport->complaint->update([
                    'status' => 'Accomplished',
                    'completed_at' => null,
                ]);
            }
        });

        return redirect()
            ->route(
                'maintenance-manager.maintenance-reviews.index'
            )
            ->with(
                'success',
                'Accomplishment report returned to the plumber for correction.'
            );
    }
}
