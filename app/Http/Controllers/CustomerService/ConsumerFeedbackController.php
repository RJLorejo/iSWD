<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Models\ComplaintFeedback;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ConsumerFeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = ComplaintFeedback::query()
            ->with([
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
                    });
            });
        }

        if ($request->filled('resolution_status')) {
            $query->where(
                'resolution_status',
                $request->input('resolution_status')
            );
        }

        if ($request->filled('handling_status')) {
            $query->where(
                'handling_status',
                $request->input('handling_status')
            );
        }

        if ($request->filled('rating')) {
            $query->where(
                'overall_rating',
                (int) $request->input('rating')
            );
        }

        if ($request->filled('division_id')) {
            $divisionId = (int) $request->input('division_id');

            $query->whereHas(
                'complaint',
                function ($complaintQuery) use ($divisionId) {
                    $complaintQuery->where(
                        'division_id',
                        $divisionId
                    );
                }
            );
        }

        if ($request->filled('from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->input('from')
            );
        }

        if ($request->filled('to')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->input('to')
            );
        }

        if ($request->boolean('needs_attention')) {
            $query->whereIn('handling_status', [
                'Follow-up Required',
                'Follow-up In Progress',
            ]);
        }

        $feedback = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalFeedback = ComplaintFeedback::count();

        $averageRating = ComplaintFeedback::avg(
            'overall_rating'
        );

        $newCount = ComplaintFeedback::where(
            'handling_status',
            'New'
        )->count();

        $needsAttentionCount = ComplaintFeedback::whereIn(
            'handling_status',
            [
                'Follow-up Required',
                'Follow-up In Progress',
            ]
        )->count();

        $resolvedCount = ComplaintFeedback::where(
            'handling_status',
            'Resolved'
        )->count();

        $divisions = Division::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'customer-service.feedback.index',
            compact(
                'feedback',
                'totalFeedback',
                'averageRating',
                'newCount',
                'needsAttentionCount',
                'resolvedCount',
                'divisions'
            )
        );
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

        return view(
            'customer-service.feedback.show',
            compact('feedback')
        );
    }

    public function markReviewed(ComplaintFeedback $feedback)
    {
        if ($feedback->handling_status !== 'New') {
            return back()->with(
                'warning',
                'This feedback has already been processed.'
            );
        }

        $feedback->update([
            'handling_status' => 'Reviewed',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with(
            'success',
            'Feedback marked as reviewed.'
        );
    }

    public function requireFollowUp(
        Request $request,
        ComplaintFeedback $feedback
    ) {
        $validated = $request->validate([
            'follow_up_notes' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        if (!in_array(
            $feedback->handling_status,
            ['New', 'Reviewed'],
            true
        )) {
            return back()->with(
                'warning',
                'This feedback cannot be moved to follow-up from its current status.'
            );
        }

        DB::transaction(function () use (
            $feedback,
            $validated
        ) {
            $feedback->update([
                'handling_status' => 'Follow-up Required',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => $feedback->reviewed_at ?? now(),
                'follow_up_notes' => trim(
                    $validated['follow_up_notes']
                ),
                'follow_up_at' => null,
                'resolved_at' => null,
            ]);
        });

        return back()->with(
            'success',
            'Feedback has been marked for follow-up.'
        );
    }

    public function startFollowUp(
        Request $request,
        ComplaintFeedback $feedback
    ) {
        $validated = $request->validate([
            'follow_up_notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        if ($feedback->handling_status !== 'Follow-up Required') {
            return back()->with(
                'warning',
                'Only feedback requiring follow-up can be started.'
            );
        }

        $notes = $feedback->follow_up_notes;

        if (!empty($validated['follow_up_notes'])) {
            $additionalNotes = trim(
                $validated['follow_up_notes']
            );

            $notes = $notes
                ? $notes . "\n\n" . $additionalNotes
                : $additionalNotes;
        }

        $feedback->update([
            'handling_status' => 'Follow-up In Progress',
            'follow_up_notes' => $notes,
            'follow_up_at' => now(),
        ]);

        return back()->with(
            'success',
            'Follow-up has been started.'
        );
    }

    public function resolve(
        Request $request,
        ComplaintFeedback $feedback
    ) {
        $validated = $request->validate([
            'follow_up_notes' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        if (!in_array(
            $feedback->handling_status,
            [
                'Follow-up Required',
                'Follow-up In Progress',
            ],
            true
        )) {
            return back()->with(
                'warning',
                'This feedback is not currently under follow-up.'
            );
        }

        $resolutionNotes = trim(
            $validated['follow_up_notes']
        );

        $notes = $feedback->follow_up_notes
            ? $feedback->follow_up_notes .
                "\n\nResolution:\n" .
                $resolutionNotes
            : "Resolution:\n" . $resolutionNotes;

        $feedback->update([
            'handling_status' => 'Resolved',
            'follow_up_notes' => $notes,
            'follow_up_at' => $feedback->follow_up_at ?? now(),
            'resolved_at' => now(),
        ]);

        return back()->with(
            'success',
            'Feedback follow-up has been resolved.'
        );
    }
}
