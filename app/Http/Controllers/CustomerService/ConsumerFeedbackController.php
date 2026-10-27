<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Models\ComplaintFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsumerFeedbackController extends Controller
{

    public function index(Request $request)
    {
        $feedback = $this->filteredFeedbackQuery($request)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalFeedback = ComplaintFeedback::count();

        $averageRating = ComplaintFeedback::avg('overall_rating');

        $newCount = ComplaintFeedback::where('handling_status', 'New')
            ->count();

        $reviewedCount = ComplaintFeedback::whereIn('handling_status', [
            'Reviewed',
            'Follow-up Required',
            'Follow-up In Progress',
            'Resolved',
        ])->count();

        return view(
            'customer-service.feedback.index',
            compact(
                'feedback',
                'totalFeedback',
                'averageRating',
                'newCount',
                'reviewedCount'
            )
        );
    }


    /** Print every record matching the same filters used by the index page. */
    public function print(Request $request)
    {
        $feedback = $this->filteredFeedbackQuery($request)
            ->latest()
            ->get();

        // Keep these summary figures consistent with the dashboard cards.
        $averageRating = ComplaintFeedback::avg('overall_rating');
        $newCount = ComplaintFeedback::where('handling_status', 'New')->count();
        $needsAttentionCount = ComplaintFeedback::whereIn('handling_status', [
            'Follow-up Required',
            'Follow-up In Progress',
        ])->count();

        return view(
            'customer-service.feedback.print',
            compact('feedback', 'averageRating', 'newCount', 'needsAttentionCount')
        );
    }

    /** Build the common filtered query for the list and print report. */
    private function filteredFeedbackQuery(Request $request)
    {
        $query = ComplaintFeedback::query()->with([
            'consumer',
            'complaint.division',
            'complaint.category',
            'reviewer',
        ]);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($query) use ($search) {
                $query
                    ->whereHas('complaint', function ($complaintQuery) use ($search) {
                        $complaintQuery
                            ->where('complaint_no', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    })
                    ->orWhereHas('consumer', function ($consumerQuery) use ($search) {
                        $consumerQuery
                            ->where('account_number', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('complaint.category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('complaint.division', function ($divisionQuery) use ($search) {
                        $divisionQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('resolution_status')) {
            $query->where('resolution_status', $request->input('resolution_status'));
        }

        if ($request->filled('handling_status')) {
            $query->where('handling_status', $request->input('handling_status'));
        }

        if ($request->filled('rating')) {
            $query->where('overall_rating', (int) $request->input('rating'));
        }

        // Keep support for existing bookmarked URLs that still contain division_id.
        if ($request->filled('division_id')) {
            $divisionId = (int) $request->input('division_id');
            $query->whereHas('complaint', function ($complaintQuery) use ($divisionId) {
                $complaintQuery->where('division_id', $divisionId);
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        return $query;
    }

    public function show(ComplaintFeedback $feedback)
    {
        $feedback->load([
            'consumer.address',
            'complaint.division',
            'complaint.category',
            'complaint.technicians',
            'complaint.maintenanceReport',
            'reviewer',
        ]);

        return view('customer-service.feedback.show', compact('feedback'));
    }


    public function markReviewed(ComplaintFeedback $feedback)
    {
        if ($feedback->handling_status !== 'New') {
            return back()->with(
                'warning',
                'This feedback has already been reviewed or processed.'
            );
        }

        $feedback->update([
            'handling_status' => 'Reviewed',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now('Asia/Manila'),
        ]);

        return back()->with(
            'success',
            'Feedback marked as reviewed. You can now reply to the consumer.'
        );
    }

    public function requireFollowUp(Request $request, ComplaintFeedback $feedback)
    {
        $validated = $request->validate([
            'follow_up_notes' => ['required', 'string', 'max:2000'],
        ]);

        if (!in_array($feedback->handling_status, ['New', 'Reviewed'], true)) {
            return back()->with(
                'warning',
                'This feedback cannot be moved to follow-up from its current status.'
            );
        }

        DB::transaction(function () use ($feedback, $validated) {
            $feedback->update([
                'handling_status' => 'Follow-up Required',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => $feedback->reviewed_at ?? now(),
                'follow_up_notes' => trim($validated['follow_up_notes']),
                'follow_up_at' => null,
                'resolved_at' => null,
            ]);
        });

        return back()->with('success', 'Feedback has been marked for follow-up.');
    }

    public function startFollowUp(Request $request, ComplaintFeedback $feedback)
    {
        $validated = $request->validate([
            'follow_up_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($feedback->handling_status !== 'Follow-up Required') {
            return back()->with('warning', 'Only feedback requiring follow-up can be started.');
        }

        $notes = $feedback->follow_up_notes;

        if (!empty($validated['follow_up_notes'])) {
            $additionalNotes = trim($validated['follow_up_notes']);
            $notes = $notes ? $notes . "\n\n" . $additionalNotes : $additionalNotes;
        }

        $feedback->update([
            'handling_status' => 'Follow-up In Progress',
            'follow_up_notes' => $notes,
            'follow_up_at' => now(),
        ]);

        return back()->with('success', 'Follow-up has been started.');
    }




    public function resolve(Request $request, ComplaintFeedback $feedback)
    {
        $validated = $request->validate([
            'resolution_message' => ['required', 'string', 'max:2000'],
        ]);

        $message = trim($validated['resolution_message']);

        if ($message === '') {
            return back()
                ->withErrors([
                    'resolution_message' => 'Please enter a reply before sending.',
                ])
                ->withInput();
        }

        $allowedStatuses = [
            'Reviewed',
            'Follow-up Required',
            'Follow-up In Progress',
            'Resolved',
        ];

        if (!in_array($feedback->handling_status, $allowedStatuses, true)) {
            return back()->with(
                'warning',
                'Please mark this feedback as reviewed before sending a reply.'
            );
        }

        $feedback->update([
            'resolution_message' => $message,
            'resolution_sent_at' => now('Asia/Manila'),
        ]);

        return back()->with(
            'success',
            'Your reply has been sent to the consumer.'
        );
    }





    public function sidebarNotifications()
    {
        /*
    |--------------------------------------------------------------------------
    | 1. Complaints
    |--------------------------------------------------------------------------
    | Count complaints awaiting initial processing.
    */

        $complaintsCount = \App\Models\Complaint::query()
            ->where('status', 'Pending')
            ->count();

        /*
    |--------------------------------------------------------------------------
    | 2. Complaint Verification
    |--------------------------------------------------------------------------
    | The verification controller only displays Pending complaints.
    */

        $verificationCount = \App\Models\Complaint::query()
            ->where('status', 'Pending')
            ->count();

        /*
    |--------------------------------------------------------------------------
    | 3. Commercial Services
    |--------------------------------------------------------------------------
    | Count verified Commercial Division complaints that are still
    | being processed by Commercial Services.
    |
    | Exclude For Maintenance because those complaints have been
    | forwarded to Maintenance. Also exclude terminal statuses.
    */

        $commercialServicesCount = \App\Models\Complaint::query()
            ->whereNotNull('verified_at')
            ->whereNotIn('status', [
                'Pending',
                'Rejected',
                'For Maintenance',
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ])
            ->whereHas('division', function ($query) {
                $query->whereRaw(
                    'LOWER(name) LIKE ?',
                    ['%commercial%']
                );
            })
            ->count();

        /*
    |--------------------------------------------------------------------------
    | 4. Final Closures
    |--------------------------------------------------------------------------
    | Completed complaints with a maintenance report.
    | This matches the FinalClosureController base query.
    */

        $finalClosuresCount = \App\Models\Complaint::query()
            ->where('status', 'Completed')
            ->whereHas('maintenanceReport')
            ->count();

        /*
    |--------------------------------------------------------------------------
    | 5. Pending Workloads
    |--------------------------------------------------------------------------
    | Match the MaintenanceMonitoringController base query:
    | pending workload, recorded reason, active complaint, and no
    | later completed follow-up work order.
    */

        $pendingWorkloadsCount = \App\Models\MaintenanceWorkOrder::query()
            ->where('status', 'Pending Workload')
            ->whereNotNull('pending_recorded_at')
            ->whereNotNull('pending_reason')
            ->whereHas('complaint', function ($query) {
                $query->whereNotIn('status', [
                    'Completed',
                    'Closed',
                ]);
            })
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('maintenance_work_orders as resolved_follow_up')
                    ->whereColumn(
                        'resolved_follow_up.complaint_id',
                        'maintenance_work_orders.complaint_id'
                    )
                    ->whereColumn(
                        'resolved_follow_up.id',
                        '>',
                        'maintenance_work_orders.id'
                    )
                    ->where(
                        'resolved_follow_up.status',
                        'Completed'
                    );
            })
            ->count();

        /*
    |--------------------------------------------------------------------------
    | 6. Missed Schedules
    |--------------------------------------------------------------------------
    | Scheduled work orders that have not started, with a finalized
    | schedule date before today's date in the Asia/Manila timezone.
    */

        $today = \Carbon\CarbonImmutable::now('Asia/Manila')
            ->toDateString();

        $missedSchedulesCount = \App\Models\MaintenanceWorkOrder::query()
            ->where('status', 'Scheduled')
            ->whereNull('started_at')
            ->whereHas('schedule', function ($query) use ($today) {
                $query->whereDate('schedule_date', '<', $today)
                    ->where('status', 'Finalized');
            })
            ->count();

        /*
    |--------------------------------------------------------------------------
    | 7. Feedback
    |--------------------------------------------------------------------------
    | Preserve the existing working New feedback notification.
    */

        $newFeedbackCount = \App\Models\ComplaintFeedback::query()
            ->where('handling_status', 'New')
            ->count();

        /*
    |--------------------------------------------------------------------------
    | JSON Response
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'complaints' => $complaintsCount,
            'verification' => $verificationCount,
            'commercial_services' => $commercialServicesCount,
            'final_closures' => $finalClosuresCount,
            'pending_workloads' => $pendingWorkloadsCount,
            'missed_schedules' => $missedSchedulesCount,
            'feedback' => $newFeedbackCount,
        ]);
    }
}
