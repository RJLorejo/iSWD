<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consumer\StoreComplaintFeedbackRequest;
use App\Models\Complaint;
use App\Models\ComplaintFeedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComplaintFeedbackController extends Controller
{
    public function create(Complaint $complaint)
    {
        $consumer = Auth::user()->consumer;

        abort_unless($consumer, 403);

        abort_unless(
            (int) $complaint->consumer_id === (int) $consumer->id,
            403
        );

        abort_unless(
            in_array($complaint->status, ['Accomplished', 'Completed', 'Closed'], true),
            403
        );

        $complaint->load([
            'division',
            'category',
            'technicians',
            'feedback',
        ]);

        if ($complaint->feedback) {
            return redirect()
                ->route('consumer.complaints.show', $complaint)
                ->with('warning', 'Feedback has already been submitted for this complaint.');
        }

        return view(
            'consumer.complaints.feedback.create',
            compact('complaint')
        );
    }

    public function store(
        StoreComplaintFeedbackRequest $request,
        Complaint $complaint
    ) {
        $consumer = Auth::user()->consumer;

        abort_unless($consumer, 403);

        abort_unless(
            (int) $complaint->consumer_id === (int) $consumer->id,
            403
        );

        abort_unless(
            in_array($complaint->status, ['Accomplished', 'Completed', 'Closed'], true),
            403
        );

        if ($complaint->feedback()->exists()) {
            return redirect()
                ->route('consumer.complaints.show', $complaint)
                ->with('warning', 'Feedback has already been submitted for this complaint.');
        }

        DB::transaction(function () use (
            $request,
            $complaint,
            $consumer
        ) {
            ComplaintFeedback::create([
                'complaint_id' => $complaint->id,
                'consumer_id' => $consumer->id,

                'overall_rating' =>
                    $request->integer('overall_rating'),

                'service_quality_rating' =>
                    $request->integer('service_quality_rating'),

                'response_time_rating' =>
                    $request->integer('response_time_rating'),

                'personnel_courtesy_rating' =>
                    $request->integer('personnel_courtesy_rating'),

                'resolution_status' =>
                    $request->input('resolution_status'),

                'comments' =>
                    $request->filled('comments')
                        ? trim($request->input('comments'))
                        : null,
            ]);
        });

        return redirect()
            ->route('consumer.complaints.show', $complaint)
            ->with(
                'success',
                'Thank you. Your feedback has been submitted successfully.'
            );
    }
}
