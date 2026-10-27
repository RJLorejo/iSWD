<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplaintFeedback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ConsumerFeedbackController extends Controller
{
    /**
     * Display the Admin feedback monitoring page.
     */
    public function index(Request $request)
    {
        $query = $this->filteredFeedbackQuery($request);

        $feedback = (clone $query)
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        // Summary statistics cover all feedback, not just the current filters.
        $allFeedback = ComplaintFeedback::query();

        $totalFeedback = (clone $allFeedback)->count();

        $newCount = (clone $allFeedback)
            ->where('handling_status', 'New')
            ->count();

        $needsAttentionCount = (clone $allFeedback)
            ->whereIn('handling_status', [
                'Follow-up Required',
                'Follow-up In Progress',
            ])
            ->count();

        $resolvedCount = (clone $allFeedback)
            ->where('resolution_status', 'Resolved')
            ->count();

        $averageRating = (clone $allFeedback)
            ->whereNotNull('overall_rating')
            ->avg('overall_rating');

        return view('admin.consumer-feedback.index', compact(
            'feedback',
            'totalFeedback',
            'newCount',
            'needsAttentionCount',
            'resolvedCount',
            'averageRating'
        ));
    }

    /**
     * Print the feedback report using the current filters.
     */
    public function print(Request $request)
    {
        $feedback = $this->filteredFeedbackQuery($request)
            ->latest('created_at')
            ->get();

        $totalFeedback = $feedback->count();

        $newCount = $feedback
            ->where('handling_status', 'New')
            ->count();

        $needsAttentionCount = $feedback
            ->whereIn('handling_status', [
                'Follow-up Required',
                'Follow-up In Progress',
            ])
            ->count();

        $resolvedCount = $feedback
            ->where('resolution_status', 'Resolved')
            ->count();

        $averageRating = $feedback
            ->whereNotNull('overall_rating')
            ->avg('overall_rating');

        return view('admin.consumer-feedback.print', compact(
            'feedback',
            'totalFeedback',
            'newCount',
            'needsAttentionCount',
            'resolvedCount',
            'averageRating'
        ));
    }

    /**
     * Display feedback details without management actions.
     */
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

        return view('admin.consumer-feedback.show', compact('feedback'));
    }

    /**
     * Build the shared query used by the index and print report.
     */
    private function filteredFeedbackQuery(Request $request): Builder
    {
        $query = ComplaintFeedback::query()
            ->with([
                'consumer',
                'complaint.division',
                'complaint.category',
                'reviewer',
            ]);

        // General search: complaint, consumer, service category, or division.
        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->whereHas('consumer', function (Builder $consumer) use ($search) {
                        $consumer->where(function (Builder $nameQuery) use ($search) {
                            $nameQuery
                                ->where('first_name', 'like', "%{$search}%")
                                ->orWhere('middle_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('account_number', 'like', "%{$search}%");
                        });
                    })
                    ->orWhereHas('complaint', function (Builder $complaint) use ($search) {
                        $complaint->where(function (Builder $complaintQuery) use ($search) {
                            $complaintQuery
                                ->where('complaint_no', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%");
                        });
                    })
                    ->orWhereHas('complaint.division', function (Builder $division) use ($search) {
                        $division->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('complaint.category', function (Builder $category) use ($search) {
                        $category->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Consumer's reported resolution.
        if ($request->filled('resolution_status')) {
            $query->where(
                'resolution_status',
                $request->input('resolution_status')
            );
        }

        // Internal handling status.
        if ($request->filled('handling_status')) {
            $query->where(
                'handling_status',
                $request->input('handling_status')
            );
        }

        // Rating filter.
        if ($request->filled('rating')) {
            $query->where(
                'overall_rating',
                $request->input('rating')
            );
        }

        // Date range uses the feedback submission date.
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        return $query;
    }
}
