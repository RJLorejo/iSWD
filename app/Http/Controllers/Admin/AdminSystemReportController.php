<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintAiAnalysis;
use App\Models\ComplaintFeedback;
use App\Models\Consumer;
use App\Models\MaintenanceWorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminSystemReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->validateFilters($request);

        $from = $filters['date_from']
            ? Carbon::parse($filters['date_from'])->startOfDay()
            : null;

        $to = $filters['date_to']
            ? Carbon::parse($filters['date_to'])->endOfDay()
            : null;

        $consumerQuery = $this->dateQuery(
            Consumer::query(),
            'created_at',
            $from,
            $to
        );

        $complaintQuery = $this->dateQuery(
            Complaint::query(),
            'created_at',
            $from,
            $to
        );

        $feedbackQuery = $this->dateQuery(
            ComplaintFeedback::query(),
            'created_at',
            $from,
            $to
        );

        $aiQuery = $this->dateQuery(
            ComplaintAiAnalysis::query(),
            'analyzed_at',
            $from,
            $to
        );

        $workOrderQuery = $this->dateQuery(
            MaintenanceWorkOrder::query(),
            'created_at',
            $from,
            $to
        );

        /*
        |--------------------------------------------------------------------------
        | Consumer Summary
        |--------------------------------------------------------------------------
        */

        $consumerStats = [
            'total' => (clone $consumerQuery)->count(),
            'active' => (clone $consumerQuery)
                ->where('is_active', true)
                ->count(),
            'inactive' => (clone $consumerQuery)
                ->where('is_active', false)
                ->count(),
            'verified' => (clone $consumerQuery)
                ->where('verification_status', 'Verified')
                ->count(),
            'pending' => (clone $consumerQuery)
                ->where('verification_status', 'Pending')
                ->count(),
            'rejected' => (clone $consumerQuery)
                ->where('verification_status', 'Rejected')
                ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Complaint Summary
        |--------------------------------------------------------------------------
        */

        $complaintStats = [
            'total' => (clone $complaintQuery)->count(),
            'pending' => (clone $complaintQuery)
                ->where('status', 'Pending')
                ->count(),
            'verified' => (clone $complaintQuery)
                ->where('status', 'Verified')
                ->count(),
            'assigned' => (clone $complaintQuery)
                ->where('status', 'Assigned')
                ->count(),
            'in_progress' => (clone $complaintQuery)
                ->where('status', 'In Progress')
                ->count(),
            'completed' => (clone $complaintQuery)
                ->where('status', 'Completed')
                ->count(),
            'closed' => (clone $complaintQuery)
                ->where('status', 'Closed')
                ->count(),
            'rejected' => (clone $complaintQuery)
                ->where('status', 'Rejected')
                ->count(),
        ];



        $complaintsByCategory = (clone $complaintQuery)
            ->leftJoin(
                'complaint_categories',
                'complaints.complaint_category_id',
                '=',
                'complaint_categories.id'
            )
            ->where(function ($query) use ($from, $to) {
                // The date conditions are applied below only when needed.
                // Existing date filters on the cloned query are handled separately.
            })
            ->selectRaw(
                "COALESCE(complaint_categories.name, 'Uncategorized') as category_name,
         COUNT(complaints.id) as total"
            )
            ->groupBy('complaint_categories.name')
            ->orderByDesc('total')
            ->get();



        /*
        |--------------------------------------------------------------------------
        | AI Analysis Summary
        |--------------------------------------------------------------------------
        */

        $aiStats = [
            'total' => (clone $aiQuery)->count(),

            'high' => (clone $aiQuery)
                ->whereRaw('LOWER(urgency_level) = ?', ['high'])
                ->count(),

            'medium' => (clone $aiQuery)
                ->whereRaw('LOWER(urgency_level) = ?', ['medium'])
                ->count(),

            'low' => (clone $aiQuery)
                ->whereRaw('LOWER(urgency_level) = ?', ['low'])
                ->count(),

            'ambiguous' => (clone $aiQuery)
                ->where('ambiguous', true)
                ->count(),

            'average_confidence' => round(
                (clone $aiQuery)->whereNotNull('confidence')
                    ->avg('confidence') ?? 0,
                4
            ),

            'average_urgency_score' => round(
                (clone $aiQuery)->whereNotNull('urgency_score')
                    ->avg('urgency_score') ?? 0,
                2
            ),
        ];

        $aiUrgencyBreakdown = (clone $aiQuery)
            ->selectRaw(
                "COALESCE(urgency_level, 'Not classified') as urgency,
                 COUNT(*) as total"
            )
            ->groupBy('urgency_level')
            ->orderByDesc('total')
            ->get();

        $aiConfidenceBreakdown = (clone $aiQuery)
            ->selectRaw(
                "COALESCE(confidence_level, 'Not specified') as confidence_label,
                 COUNT(*) as total"
            )
            ->groupBy('confidence_level')
            ->orderByDesc('total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Consumer Feedback Summary
        |--------------------------------------------------------------------------
        */

        $feedbackStats = [
            'total' => (clone $feedbackQuery)->count(),

            'average_overall' => round(
                (clone $feedbackQuery)->avg('overall_rating') ?? 0,
                2
            ),

            'average_service_quality' => round(
                (clone $feedbackQuery)->avg('service_quality_rating') ?? 0,
                2
            ),

            'average_response_time' => round(
                (clone $feedbackQuery)->avg('response_time_rating') ?? 0,
                2
            ),

            'average_personnel_courtesy' => round(
                (clone $feedbackQuery)->avg('personnel_courtesy_rating') ?? 0,
                2
            ),

            'resolved' => (clone $feedbackQuery)
                ->where('resolution_status', 'Resolved')
                ->count(),

            'partially_resolved' => (clone $feedbackQuery)
                ->where('resolution_status', 'Partially Resolved')
                ->count(),

            'not_resolved' => (clone $feedbackQuery)
                ->where('resolution_status', 'Not Resolved')
                ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Maintenance Work-Order Summary
        |--------------------------------------------------------------------------
        */

        $workOrderStats = [
            'total' => (clone $workOrderQuery)->count(),

            'completed' => (clone $workOrderQuery)
                ->where('status', 'Completed')
                ->count(),

            'in_progress' => (clone $workOrderQuery)
                ->where('status', 'In Progress')
                ->count(),

            'scheduled' => (clone $workOrderQuery)
                ->where('status', 'Scheduled')
                ->count(),

            'pending_workload' => (clone $workOrderQuery)
                ->where('status', 'Pending Workload')
                ->count(),

            'rescheduled' => (clone $workOrderQuery)
                ->where('status', 'Rescheduled')
                ->count(),

            'cancelled' => (clone $workOrderQuery)
                ->where('status', 'Cancelled')
                ->count(),

            'emergency' => (clone $workOrderQuery)
                ->where('dispatch_type', 'Emergency')
                ->count(),

            'regular' => (clone $workOrderQuery)
                ->where('dispatch_type', 'Regular')
                ->count(),

            'late_additions' => (clone $workOrderQuery)
                ->where('is_late_addition', true)
                ->count(),

            'approved' => (clone $workOrderQuery)
                ->where('approval_status', 'Approved')
                ->count(),
        ];

        $workOrders = (clone $workOrderQuery)
            ->with([
                'complaint',
                'schedule',
                'technicians',
            ])
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Monthly Trend
        |--------------------------------------------------------------------------
        */

        $trendStart = $from
            ? $from->copy()->startOfMonth()
            : now()->subMonths(5)->startOfMonth();

        $trendEnd = $to
            ? $to->copy()->startOfMonth()
            : now()->startOfMonth();

        $trendStart = $trendStart->copy()->startOfMonth();
        $trendEnd = $trendEnd->copy()->startOfMonth();

        if ($trendEnd->lt($trendStart)) {
            $trendEnd = $trendStart->copy();
        }

        // Limit the chart to 24 months to keep the report responsive.
        if ($trendStart->diffInMonths($trendEnd) > 23) {
            $trendStart = $trendEnd->copy()->subMonths(23);
        }

        $monthlyTrend = collect();
        $cursor = $trendStart->copy();

        while ($cursor->lte($trendEnd)) {
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd = $cursor->copy()->endOfMonth();

            $effectiveStart = $from && $from->gt($monthStart)
                ? $from->copy()
                : $monthStart->copy();

            $effectiveEnd = $to && $to->lt($monthEnd)
                ? $to->copy()
                : $monthEnd->copy();

            $monthlyTrend->push([
                'month' => $cursor->format('M Y'),

                'complaints' => Complaint::query()
                    ->whereBetween('created_at', [
                        $effectiveStart,
                        $effectiveEnd,
                    ])
                    ->count(),

                'completed_work_orders' => MaintenanceWorkOrder::query()
                    ->where('status', 'Completed')
                    ->whereBetween('completed_at', [
                        $effectiveStart,
                        $effectiveEnd,
                    ])
                    ->count(),
            ]);

            $cursor->addMonth();
        }

        /*
        |--------------------------------------------------------------------------
        | Latest Feedback
        |--------------------------------------------------------------------------
        */

        $latestFeedback = (clone $feedbackQuery)
            ->with(['complaint', 'consumer'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact(
            'filters',
            'consumerStats',
            'complaintStats',
            'complaintsByCategory',
            'aiStats',
            'aiUrgencyBreakdown',
            'aiConfidenceBreakdown',
            'feedbackStats',
            'latestFeedback',
            'workOrderStats',
            'workOrders',
            'monthlyTrend'
        ));
    }

    public function print(Request $request)
    {
        $filters = $this->validateFilters($request);

        $from = $filters['date_from']
            ? Carbon::parse($filters['date_from'])->startOfDay()
            : null;

        $to = $filters['date_to']
            ? Carbon::parse($filters['date_to'])->endOfDay()
            : null;

        $consumerQuery = $this->dateQuery(
            Consumer::query(),
            'created_at',
            $from,
            $to
        );

        $complaintQuery = $this->dateQuery(
            Complaint::query(),
            'created_at',
            $from,
            $to
        );

        $feedbackQuery = $this->dateQuery(
            ComplaintFeedback::query(),
            'created_at',
            $from,
            $to
        );

        $aiQuery = $this->dateQuery(
            ComplaintAiAnalysis::query(),
            'analyzed_at',
            $from,
            $to
        );

        $workOrderQuery = $this->dateQuery(
            MaintenanceWorkOrder::query(),
            'created_at',
            $from,
            $to
        );

        $consumerStats = [
            'total' => (clone $consumerQuery)->count(),
            'active' => (clone $consumerQuery)->where('is_active', true)->count(),
            'inactive' => (clone $consumerQuery)->where('is_active', false)->count(),
            'verified' => (clone $consumerQuery)->where('verification_status', 'Verified')->count(),
            'pending' => (clone $consumerQuery)->where('verification_status', 'Pending')->count(),
            'rejected' => (clone $consumerQuery)->where('verification_status', 'Rejected')->count(),
        ];

        $complaintStats = [
            'total' => (clone $complaintQuery)->count(),
            'pending' => (clone $complaintQuery)->where('status', 'Pending')->count(),
            'verified' => (clone $complaintQuery)->where('status', 'Verified')->count(),
            'assigned' => (clone $complaintQuery)->where('status', 'Assigned')->count(),
            'in_progress' => (clone $complaintQuery)->where('status', 'In Progress')->count(),
            'completed' => (clone $complaintQuery)->where('status', 'Completed')->count(),
            'closed' => (clone $complaintQuery)->where('status', 'Closed')->count(),
            'rejected' => (clone $complaintQuery)->where('status', 'Rejected')->count(),
        ];

        $aiStats = [
            'total' => (clone $aiQuery)->count(),
            'high' => (clone $aiQuery)->whereRaw('LOWER(urgency_level) = ?', ['high'])->count(),
            'medium' => (clone $aiQuery)->whereRaw('LOWER(urgency_level) = ?', ['medium'])->count(),
            'low' => (clone $aiQuery)->whereRaw('LOWER(urgency_level) = ?', ['low'])->count(),
            'ambiguous' => (clone $aiQuery)->where('ambiguous', true)->count(),
            'average_confidence' => round(
                (clone $aiQuery)->whereNotNull('confidence')->avg('confidence') ?? 0,
                4
            ),
            'average_urgency_score' => round(
                (clone $aiQuery)->whereNotNull('urgency_score')->avg('urgency_score') ?? 0,
                2
            ),
        ];

        $feedbackStats = [
            'total' => (clone $feedbackQuery)->count(),
            'average_overall' => round((clone $feedbackQuery)->avg('overall_rating') ?? 0, 2),
            'average_service_quality' => round((clone $feedbackQuery)->avg('service_quality_rating') ?? 0, 2),
            'average_response_time' => round((clone $feedbackQuery)->avg('response_time_rating') ?? 0, 2),
            'average_personnel_courtesy' => round((clone $feedbackQuery)->avg('personnel_courtesy_rating') ?? 0, 2),
            'resolved' => (clone $feedbackQuery)->where('resolution_status', 'Resolved')->count(),
            'partially_resolved' => (clone $feedbackQuery)->where('resolution_status', 'Partially Resolved')->count(),
            'not_resolved' => (clone $feedbackQuery)->where('resolution_status', 'Not Resolved')->count(),
        ];

        $workOrderStats = [
            'total' => (clone $workOrderQuery)->count(),
            'completed' => (clone $workOrderQuery)->where('status', 'Completed')->count(),
            'in_progress' => (clone $workOrderQuery)->where('status', 'In Progress')->count(),
            'scheduled' => (clone $workOrderQuery)->where('status', 'Scheduled')->count(),
            'pending_workload' => (clone $workOrderQuery)->where('status', 'Pending Workload')->count(),
            'rescheduled' => (clone $workOrderQuery)->where('status', 'Rescheduled')->count(),
            'cancelled' => (clone $workOrderQuery)->where('status', 'Cancelled')->count(),
            'emergency' => (clone $workOrderQuery)->where('dispatch_type', 'Emergency')->count(),
            'regular' => (clone $workOrderQuery)->where('dispatch_type', 'Regular')->count(),
            'late_additions' => (clone $workOrderQuery)->where('is_late_addition', true)->count(),
            'approved' => (clone $workOrderQuery)->where('approval_status', 'Approved')->count(),
        ];

        $complaintsByCategory = (clone $complaintQuery)
            ->leftJoin(
                'complaint_categories',
                'complaints.complaint_category_id',
                '=',
                'complaint_categories.id'
            )
            ->selectRaw(
                "COALESCE(complaint_categories.name, 'Uncategorized') as category_name,
                 COUNT(complaints.id) as total"
            )
            ->groupBy('complaint_categories.name')
            ->orderByDesc('total')
            ->get();

        $aiUrgencyBreakdown = (clone $aiQuery)
            ->selectRaw(
                "COALESCE(urgency_level, 'Not classified') as urgency, COUNT(*) as total"
            )
            ->groupBy('urgency_level')
            ->orderByDesc('total')
            ->get();

        $workOrders = (clone $workOrderQuery)
            ->with(['complaint', 'schedule', 'technicians'])
            ->latest('created_at')
            ->get();

        $latestFeedback = (clone $feedbackQuery)
            ->with(['complaint', 'consumer'])
            ->latest()
            ->limit(100)
            ->get();

        return view('admin.reports.print', compact(
            'filters',
            'consumerStats',
            'complaintStats',
            'complaintsByCategory',
            'aiStats',
            'aiUrgencyBreakdown',
            'feedbackStats',
            'latestFeedback',
            'workOrderStats',
            'workOrders'
        ));
    }

    private function validateFilters(Request $request): array
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        return [
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
        ];
    }


    private function dateQuery($query, string $column, ?Carbon $from, ?Carbon $to)
    {
        // Qualify created_at when querying complaints so joins cannot
        // make the date column ambiguous.
        if ($query->getModel() instanceof Complaint && $column === 'created_at') {
            $column = 'complaints.created_at';
        }

        if ($from) {
            $query->where($column, '>=', $from);
        }

        if ($to) {
            $query->where($column, '<=', $to);
        }

        return $query;
    }
}
