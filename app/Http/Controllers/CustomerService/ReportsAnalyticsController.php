<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintFeedback;
use App\Models\Division;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReportsAnalyticsController extends Controller
{
    private const STATUSES = [
        'Pending',
        'Verified',
        'CS Processing',
        'For Maintenance',
        'Assigned',
        'In Progress',
        'Completed',
        'Closed',
        'Rejected',
    ];

    private const PERIODS = [
        'day',
        'week',
        'month',
        'year',
        'custom',
    ];

    private const TREND_INTERVALS = [
        'month',
        'year',
    ];

    public function index(Request $request)
    {
        return view(
            'customer-service.reports.index',
            $this->buildReport($request)
        );
    }

    public function print(Request $request)
    {
        return view(
            'customer-service.reports.print',
            $this->buildReport($request, true)
        );
    }

    private function buildReport(
        Request $request,
        bool $printing = false
    ): array {
        // ---------------------------------------------------------
        // VALIDATE REPORT FILTERS
        // ---------------------------------------------------------
        $filters = $request->validate([
            'period' => [
                'nullable',
                Rule::in(self::PERIODS),
            ],
            'trend_interval' => [
                'nullable',
                Rule::in(self::TREND_INTERVALS),
            ],
            'trend_date_from' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'trend_date_to' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'date_from' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'date_to' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'division_id' => [
                'nullable',
                'integer',
                Rule::exists('divisions', 'id'),
            ],
            'status' => [
                'nullable',
                Rule::in(self::STATUSES),
            ],
        ]);

        // ---------------------------------------------------------
        // VALIDATE DATE RANGE ORDER
        // ---------------------------------------------------------
        foreach (
            [
                [
                    'date_from',
                    'date_to',
                    'Invalid report date range.',
                ],
                [
                    'trend_date_from',
                    'trend_date_to',
                    'Invalid complaint trend date range.',
                ],
            ] as [$fromKey, $toKey, $message]
        ) {
            if (
                !empty($filters[$fromKey])
                && !empty($filters[$toKey])
                && $filters[$fromKey] > $filters[$toKey]
            ) {
                throw ValidationException::withMessages([
                    $toKey => $message,
                ]);
            }
        }

        // ---------------------------------------------------------
        // REPORT PERIOD AND TIMEZONE
        // ---------------------------------------------------------
        $period = $filters['period'] ?? 'month';
        $trendInterval = $filters['trend_interval'] ?? 'month';

        $timezone = 'Asia/Manila';

        $today = CarbonImmutable::now($timezone);

        [$start, $end] = match ($period) {
            'day' => [
                $today->startOfDay(),
                $today->endOfDay(),
            ],

            'week' => [
                $today->startOfWeek(),
                $today->endOfWeek(),
            ],

            'year' => [
                $today->startOfYear(),
                $today->endOfYear(),
            ],

            'custom' => [
                !empty($filters['date_from'])
                    ? CarbonImmutable::parse(
                        $filters['date_from'],
                        $timezone
                    )->startOfDay()
                    : (
                        !empty($filters['date_to'])
                            ? CarbonImmutable::parse(
                                $filters['date_to'],
                                $timezone
                            )->startOfDay()
                            : $today->startOfMonth()
                    ),

                !empty($filters['date_to'])
                    ? CarbonImmutable::parse(
                        $filters['date_to'],
                        $timezone
                    )->endOfDay()
                    : $today->endOfDay(),
            ],

            default => [
                $today->startOfMonth(),
                $today->endOfMonth(),
            ],
        };

        if ($start->greaterThan($end)) {
            abort(422, 'Invalid report date range.');
        }

        // ---------------------------------------------------------
        // TREND DATE RANGE
        // ---------------------------------------------------------
        $trendStart = !empty($filters['trend_date_from'])
            ? CarbonImmutable::parse(
                $filters['trend_date_from'],
                $timezone
            )->startOfDay()
            : (
                !empty($filters['trend_date_to'])
                    ? (
                        $trendInterval === 'year'
                            ? CarbonImmutable::parse(
                                $filters['trend_date_to'],
                                $timezone
                            )->subYears(4)->startOfYear()
                            : CarbonImmutable::parse(
                                $filters['trend_date_to'],
                                $timezone
                            )->subMonths(11)->startOfMonth()
                    )
                    : (
                        $trendInterval === 'year'
                            ? $today->subYears(4)->startOfYear()
                            : $today->subMonths(11)->startOfMonth()
                    )
            );

        $trendEnd = !empty($filters['trend_date_to'])
            ? CarbonImmutable::parse(
                $filters['trend_date_to'],
                $timezone
            )->endOfDay()
            : (
                $trendInterval === 'year'
                    ? $today->endOfYear()
                    : $today->endOfMonth()
            );

        if ($trendStart->greaterThan($trendEnd)) {
            abort(422, 'Invalid complaint trend date range.');
        }

        // ---------------------------------------------------------
        // CONVERT TIMEZONE FOR DATABASE QUERIES
        // ---------------------------------------------------------
        // Assumes database timestamps use config('app.timezone').
        // Confirm this setting matches how timestamps are stored.
        $storageTimezone = config('app.timezone', 'UTC');

        $toDatabaseTimestamp = fn (CarbonImmutable $date) => $date
            ->setTimezone($storageTimezone)
            ->format('Y-m-d H:i:s');

        $startDb = $toDatabaseTimestamp($start);
        $endDb = $toDatabaseTimestamp($end);
        $trendStartDb = $toDatabaseTimestamp($trendStart);
        $trendEndDb = $toDatabaseTimestamp($trendEnd);

        // ---------------------------------------------------------
        // BASE COMPLAINT QUERIES
        // ---------------------------------------------------------
        $complaintQuery = Complaint::query()
            ->whereBetween('complaints.created_at', [
                $startDb,
                $endDb,
            ]);

        $trendQuery = Complaint::query()
            ->whereBetween('complaints.created_at', [
                $trendStartDb,
                $trendEndDb,
            ]);

        // Apply the same division and status filters to both queries.
        if (!empty($filters['division_id'])) {
            $complaintQuery->where(
                'complaints.division_id',
                $filters['division_id']
            );

            $trendQuery->where(
                'complaints.division_id',
                $filters['division_id']
            );
        }

        if (!empty($filters['status'])) {
            $complaintQuery->where(
                'complaints.status',
                $filters['status']
            );

            $trendQuery->where(
                'complaints.status',
                $filters['status']
            );
        }

        // ---------------------------------------------------------
        // GENERAL COMPLAINT STATISTICS
        // ---------------------------------------------------------
        $total = (clone $complaintQuery)->count();

        $statusCounts = (clone $complaintQuery)
            ->select('complaints.status')
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('complaints.status')
            ->pluck('total', 'status');

        $activeStatuses = [
            'Verified',
            'CS Processing',
            'For Maintenance',
            'Assigned',
            'In Progress',
        ];

        $activeCount = collect($activeStatuses)
            ->sum(
                fn ($status) => (int) ($statusCounts[$status] ?? 0)
            );

        $completedCount =
            (int) ($statusCounts['Completed'] ?? 0)
            + (int) ($statusCounts['Closed'] ?? 0);

        $resolutionRate = $total > 0
            ? round(($completedCount / $total) * 100, 1)
            : 0;

        // ---------------------------------------------------------
        // COMPLAINTS BY DIVISION
        // ---------------------------------------------------------
        $divisionStats = (clone $complaintQuery)
            ->leftJoin(
                'divisions',
                'complaints.division_id',
                '=',
                'divisions.id'
            )
            ->selectRaw(
                "COALESCE(divisions.name, 'Unassigned') AS name"
            )
            ->selectRaw('COUNT(*) AS total')
            ->groupBy(
                'divisions.id',
                'divisions.name'
            )
            ->orderByDesc('total')
            ->get();

        // ---------------------------------------------------------
        // COMPLAINTS BY CATEGORY
        // ---------------------------------------------------------
        $categoryStats = (clone $complaintQuery)
            ->leftJoin(
                'complaint_categories',
                'complaints.complaint_category_id',
                '=',
                'complaint_categories.id'
            )
            ->selectRaw(
                "COALESCE(complaint_categories.name, 'Uncategorized') AS name"
            )
            ->selectRaw('COUNT(*) AS total')
            ->groupBy(
                'complaint_categories.id',
                'complaint_categories.name'
            )
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // ---------------------------------------------------------
        // AI-SUGGESTED URGENCY DISTRIBUTION
        // ---------------------------------------------------------
        // Shows the urgency suggested by the AI.
        // Complaints without a usable AI urgency are Not Assessed.
        $urgencyStats = (clone $complaintQuery)
            ->leftJoin(
                'complaint_ai_analyses',
                'complaints.id',
                '=',
                'complaint_ai_analyses.complaint_id'
            )
            ->selectRaw("
                COALESCE(
                    NULLIF(
                        TRIM(complaint_ai_analyses.urgency_level),
                        ''
                    ),
                    'Not Assessed'
                ) AS name
            ")
            ->selectRaw(
                'COUNT(DISTINCT complaints.id) AS total'
            )
            ->groupBy('name')
            ->orderByDesc('total')
            ->get();

        // ---------------------------------------------------------
        // CUSTOMER SERVICE-VERIFIED URGENCY DISTRIBUTION
        // ---------------------------------------------------------
        // Separates missing analysis, unavailable AI urgency,
        // pending verification, and verified urgency levels.
        $verifiedUrgencyStats = (clone $complaintQuery)
            ->leftJoin(
                'complaint_ai_analyses',
                'complaints.id',
                '=',
                'complaint_ai_analyses.complaint_id'
            )
            ->selectRaw("
                CASE
                    WHEN complaint_ai_analyses.id IS NULL
                        THEN 'No AI Analysis'

                    WHEN complaint_ai_analyses.urgency_level IS NULL
                        OR TRIM(complaint_ai_analyses.urgency_level) = ''
                        THEN 'AI Urgency Unavailable'

                    WHEN complaint_ai_analyses.verified_urgency_level IS NULL
                        OR TRIM(complaint_ai_analyses.verified_urgency_level) = ''
                        THEN 'Pending Verification'

                    ELSE TRIM(
                        complaint_ai_analyses.verified_urgency_level
                    )
                END AS name
            ")
            ->selectRaw(
                'COUNT(DISTINCT complaints.id) AS total'
            )
            ->groupBy('name')
            ->orderByDesc('total')
            ->get();

        // ---------------------------------------------------------
        // URGENCY VERIFICATION SUMMARY
        // ---------------------------------------------------------
        $urgencySummary = (clone $complaintQuery)
            ->leftJoin(
                'complaint_ai_analyses',
                'complaints.id',
                '=',
                'complaint_ai_analyses.complaint_id'
            )
            ->selectRaw(
                'COUNT(DISTINCT complaints.id) AS total'
            )

            // Complaints with a non-empty AI-suggested urgency.
            ->selectRaw("
                COUNT(DISTINCT CASE
                    WHEN complaint_ai_analyses.urgency_level IS NOT NULL
                        AND TRIM(complaint_ai_analyses.urgency_level) <> ''
                    THEN complaints.id
                END) AS ai_assessed
            ")

            // Verified counts only include complaints with an AI urgency.
            ->selectRaw("
                COUNT(DISTINCT CASE
                    WHEN complaint_ai_analyses.urgency_level IS NOT NULL
                        AND TRIM(complaint_ai_analyses.urgency_level) <> ''
                        AND complaint_ai_analyses.verified_urgency_level IS NOT NULL
                        AND TRIM(complaint_ai_analyses.verified_urgency_level) <> ''
                    THEN complaints.id
                END) AS verified
            ")

            // Pending means the AI suggested an urgency,
            // but Customer Service has not verified it yet.
            ->selectRaw("
                COUNT(DISTINCT CASE
                    WHEN complaint_ai_analyses.urgency_level IS NOT NULL
                        AND TRIM(complaint_ai_analyses.urgency_level) <> ''
                        AND (
                            complaint_ai_analyses.verified_urgency_level IS NULL
                            OR TRIM(complaint_ai_analyses.verified_urgency_level) = ''
                        )
                    THEN complaints.id
                END) AS pending_verification
            ")

            // Counts complaints where verified urgency differs from AI.
            ->selectRaw("
                COUNT(DISTINCT CASE
                    WHEN complaint_ai_analyses.urgency_level IS NOT NULL
                        AND TRIM(complaint_ai_analyses.urgency_level) <> ''
                        AND complaint_ai_analyses.verified_urgency_level IS NOT NULL
                        AND TRIM(complaint_ai_analyses.verified_urgency_level) <> ''
                        AND UPPER(TRIM(complaint_ai_analyses.urgency_level))
                            <> UPPER(TRIM(complaint_ai_analyses.verified_urgency_level))
                    THEN complaints.id
                END) AS urgency_changed
            ")
            ->first();

        // ---------------------------------------------------------
        // URGENCY VERIFICATION RATE
        // ---------------------------------------------------------
        $aiAssessedCount = (int) ($urgencySummary->ai_assessed ?? 0);
        $verifiedCount = (int) ($urgencySummary->verified ?? 0);

        $urgencyVerificationRate = $aiAssessedCount > 0
            ? round(($verifiedCount / $aiAssessedCount) * 100, 1)
            : 0;

        // ---------------------------------------------------------
        // CUSTOMER FEEDBACK
        // ---------------------------------------------------------
        $feedbackQuery = ComplaintFeedback::query()
            ->whereBetween('created_at', [
                $startDb,
                $endDb,
            ]);

        if (!empty($filters['division_id'])) {
            $feedbackQuery->whereHas(
                'complaint',
                fn ($query) => $query->where(
                    'division_id',
                    $filters['division_id']
                )
            );
        }

        if (!empty($filters['status'])) {
            $feedbackQuery->whereHas(
                'complaint',
                fn ($query) => $query->where(
                    'status',
                    $filters['status']
                )
            );
        }

        $feedbackTotal = (clone $feedbackQuery)->count();

        $averageRating = round(
            (float) (clone $feedbackQuery)->avg('overall_rating'),
            2
        );

        $attentionCount = (clone $feedbackQuery)
            ->whereIn('handling_status', [
                'Follow-up Required',
                'Follow-up In Progress',
            ])
            ->count();

        $ratingStats = (clone $feedbackQuery)
            ->whereNotNull('overall_rating')
            ->select('overall_rating')
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('overall_rating')
            ->pluck('total', 'overall_rating');

        // ---------------------------------------------------------
        // COMPLAINT TREND
        // ---------------------------------------------------------
        $trend = $this->buildTrend(
            $trendQuery,
            $trendStart,
            $trendEnd,
            $storageTimezone,
            $trendInterval
        );

        // ---------------------------------------------------------
        // COMPLAINT RECORDS
        // ---------------------------------------------------------
        $recordsQuery = (clone $complaintQuery)
            ->with([
                'division',
                'category',
                'aiAnalysis',
            ])
            ->orderByDesc('complaints.created_at')
            ->orderByDesc('complaints.id');

        $records = $printing
            ? $recordsQuery->get()
            : $recordsQuery
                ->paginate(15)
                ->withQueryString();

        // ---------------------------------------------------------
        // DIVISION FILTER OPTIONS
        // ---------------------------------------------------------
        $divisions = Division::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        // ---------------------------------------------------------
        // RETURN DATA TO DASHBOARD AND PRINT VIEWS
        // ---------------------------------------------------------
        return compact(
            'filters',
            'period',
            'trendInterval',
            'trendStart',
            'trendEnd',
            'start',
            'end',
            'total',
            'statusCounts',
            'activeCount',
            'completedCount',
            'resolutionRate',
            'divisionStats',
            'categoryStats',
            'urgencyStats',
            'verifiedUrgencyStats',
            'urgencySummary',
            'urgencyVerificationRate',
            'feedbackTotal',
            'averageRating',
            'attentionCount',
            'ratingStats',
            'trend',
            'records',
            'divisions',
        );
    }

    // -------------------------------------------------------------
    // BUILD COMPLAINT TREND DATA
    // -------------------------------------------------------------
    private function buildTrend(
        $query,
        CarbonImmutable $start,
        CarbonImmutable $end,
        string $storageTimezone,
        string $interval
    ): array {
        $timestamps = (clone $query)
            ->reorder()
            ->pluck('complaints.created_at');

        $counts = [];

        foreach ($timestamps as $timestamp) {
            $date = CarbonImmutable::parse(
                $timestamp,
                $storageTimezone
            )->setTimezone('Asia/Manila');

            $key = match ($interval) {
                'year' => $date->format('Y'),
                default => $date->format('Y-m'),
            };

            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        $cursor = match ($interval) {
            'year' => $start->startOfYear(),
            default => $start->startOfMonth(),
        };

        $points = [];

        while ($cursor->lessThanOrEqualTo($end)) {
            $key = match ($interval) {
                'year' => $cursor->format('Y'),
                default => $cursor->format('Y-m'),
            };

            $points[] = [
                'label' => match ($interval) {
                    'year' => $cursor->format('Y'),
                    default => $cursor->format('M Y'),
                },
                'count' => $counts[$key] ?? 0,
            ];

            $cursor = match ($interval) {
                'year' => $cursor->addYear(),
                default => $cursor->addMonth(),
            };
        }

        return $points;
    }
}
