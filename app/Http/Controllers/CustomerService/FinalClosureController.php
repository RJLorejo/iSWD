<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class FinalClosureController extends Controller
{
    /**
     * Display accomplished complaints awaiting
     * official Customer Service closure.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'review_status' => [
                'nullable',
                'in:all,ready,pending,returned',
            ],
            'date_from' => ['nullable', 'date'],
            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        |
        | Completed = Accomplished, but not officially Closed.
        | Includes Engineering and Commercial complaints
        | that were forwarded to Maintenance.
        |
        */

        $baseQuery = Complaint::query()
            ->where('complaints.status', 'Completed')
            ->whereHas('maintenanceReport');

        /*
        |--------------------------------------------------------------------------
        | Summary Statistics
        |--------------------------------------------------------------------------
        */

        $pendingClosureCount = (clone $baseQuery)
            ->count();

        $readyToCloseCount = (clone $baseQuery)
            ->whereHas(
                'maintenanceReport',
                function (Builder $query) {
                    $query
                        ->whereNotNull('submitted_at')
                        ->where('review_status', 'Approved');
                }
            )
            ->count();

        $awaitingReviewCount = (clone $baseQuery)
            ->whereHas(
                'maintenanceReport',
                function (Builder $query) {
                    $query->where(function (Builder $review) {
                        $review
                            ->whereNull('submitted_at')
                            ->orWhereNull('review_status')
                            ->orWhereIn(
                                'review_status',
                                ['Draft', 'Pending Review']
                            );
                    });
                }
            )
            ->count();

        $returnedCount = (clone $baseQuery)
            ->whereHas(
                'maintenanceReport',
                function (Builder $query) {
                    $query->where(
                        'review_status',
                        'Returned'
                    );
                }
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Searchable Listing
        |--------------------------------------------------------------------------
        */

        $query = (clone $baseQuery)
            ->with([
                'consumer',
                'division',
                'category',
                'maintenanceReport',
                'commercialResolution',
            ]);

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);

            $query->where(
                function (Builder $builder) use ($search) {
                    $builder
                        ->where(
                            'complaints.complaint_no',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'complaints.complainant_name',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'complaints.description',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhereHas(
                            'consumer',
                            function (Builder $consumer) use ($search) {
                                $consumer
                                    ->where(
                                        'account_number',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'first_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'last_name',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Manager Review Filters
        |--------------------------------------------------------------------------
        */

        $reviewStatus = $validated['review_status'] ?? 'all';

        if ($reviewStatus === 'ready') {
            $query->whereHas(
                'maintenanceReport',
                function (Builder $report) {
                    $report
                        ->whereNotNull('submitted_at')
                        ->where('review_status', 'Approved');
                }
            );
        }

        if ($reviewStatus === 'pending') {
            $query->whereHas(
                'maintenanceReport',
                function (Builder $report) {
                    $report->where(
                        function (Builder $review) {
                            $review
                                ->whereNull('submitted_at')
                                ->orWhereNull('review_status')
                                ->orWhereIn(
                                    'review_status',
                                    ['Draft', 'Pending Review']
                                );
                        }
                    );
                }
            );
        }

        if ($reviewStatus === 'returned') {
            $query->whereHas(
                'maintenanceReport',
                function (Builder $report) {
                    $report->where(
                        'review_status',
                        'Returned'
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date Filters
        |--------------------------------------------------------------------------
        |
        | Filter using complaint accomplishment date.
        |
        */

        if (!empty($validated['date_from'])) {
            $query->whereDate(
                'complaints.completed_at',
                '>=',
                $validated['date_from']
            );
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate(
                'complaints.completed_at',
                '<=',
                $validated['date_to']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Results
        |--------------------------------------------------------------------------
        */

        $complaints = $query
            ->orderByDesc('complaints.completed_at')
            ->orderByDesc('complaints.id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'customer-service.final-closures.index',
            compact(
                'complaints',
                'pendingClosureCount',
                'readyToCloseCount',
                'awaitingReviewCount',
                'returnedCount',
                'reviewStatus'
            )
        );
    }

    /**
     * Print all accomplished complaints matching the selected filters.
     */
    public function print(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'review_status' => [
                'nullable',
                'in:all,ready,pending,returned',
            ],
            'date_from' => ['nullable', 'date'],
            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],
        ]);

        $query = Complaint::query()
            ->where('complaints.status', 'Completed')
            ->whereHas('maintenanceReport')
            ->with([
                'consumer',
                'division',
                'category',
                'maintenanceReport',
                'commercialResolution',
            ]);

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);

            $query->where(function (Builder $builder) use ($search) {
                $builder
                    ->where('complaints.complaint_no', 'like', "%{$search}%")
                    ->orWhere('complaints.complainant_name', 'like', "%{$search}%")
                    ->orWhere('complaints.description', 'like', "%{$search}%")
                    ->orWhereHas('consumer', function (Builder $consumer) use ($search) {
                        $consumer
                            ->where('account_number', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        $reviewStatus = $validated['review_status'] ?? 'all';

        if ($reviewStatus === 'ready') {
            $query->whereHas('maintenanceReport', function (Builder $report) {
                $report
                    ->whereNotNull('submitted_at')
                    ->where('review_status', 'Approved');
            });
        } elseif ($reviewStatus === 'pending') {
            $query->whereHas('maintenanceReport', function (Builder $report) {
                $report->where(function (Builder $review) {
                    $review
                        ->whereNull('submitted_at')
                        ->orWhereNull('review_status')
                        ->orWhereIn('review_status', ['Draft', 'Pending Review']);
                });
            });
        } elseif ($reviewStatus === 'returned') {
            $query->whereHas('maintenanceReport', function (Builder $report) {
                $report->where('review_status', 'Returned');
            });
        }

        if (!empty($validated['date_from'])) {
            $query->whereDate(
                'complaints.completed_at',
                '>=',
                $validated['date_from']
            );
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate(
                'complaints.completed_at',
                '<=',
                $validated['date_to']
            );
        }

        $complaints = $query
            ->orderByDesc('complaints.completed_at')
            ->orderByDesc('complaints.id')
            ->get();

        return view('customer-service.final-closures.print', [
            'complaints' => $complaints,
            'reviewStatus' => $reviewStatus,
            'filters' => $validated,
        ]);
    }
}
