<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    /**
     * Display all complaints to Admin in read-only mode.
     */
    public function index(Request $request)
    {
        $query = Complaint::query()
            ->with([
                'consumer.address',
                'division',
                'category',
                'technicians',
                'customerService',
                'verifier',
                'aiAnalysis',
                'commercialResolution',
                'maintenanceReport',
            ]);

        $this->applyComplaintIndexFilters($query, $request);

        $complaints = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $totalComplaints = Complaint::query()->count();

        $pendingCount = Complaint::query()
            ->where('status', 'Pending')
            ->count();

        $activeCount = Complaint::query()
            ->whereIn('status', [
                'Assigned',
                'In Progress',
                'For Maintenance',
                'CS Processing',
            ])
            ->count();

        return view(
            'admin.complaints.index',
            compact(
                'complaints',
                'totalComplaints',
                'pendingCount',
                'activeCount'
            )
        );
    }

    /**
     * Display the full complaint details without mutation actions.
     */
    public function show(Complaint $complaint)
    {
        $complaint->load([
            'consumer.address',
            'division',
            'category',
            'photos',
            'technicians',
            'customerService',
            'verifier',
            'maintenanceReport',
            'commercialResolution.processor',
            'commercialResolution.forwarder',
            'aiAnalysis.predictedCategory',
            'aiAnalysis.consumerCategory',
            'aiAnalysis.verifiedCategory',
            'aiAnalysis.verifier',
        ]);

        $workOrders = \App\Models\MaintenanceWorkOrder::query()
            ->with(['schedule', 'technicians'])
            ->where('complaint_id', $complaint->id)
            ->orderBy('visit_number')
            ->orderBy('id')
            ->get();

        return view(
            'admin.complaints.show',
            compact('complaint', 'workOrders')
        );
    }

    /**
     * Apply the same search and filtering options as CS.
     */
    private function applyComplaintIndexFilters(
        Builder $query,
        Request $request
    ): void {
        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $normalizedSearch = strtolower($search);

            $workflowSearchStatus = match ($normalizedSearch) {
                'pending', 'submitted' => 'Pending',
                'verified' => 'Verified',
                'cs processing',
                'customer service processing',
                'under initial processing',
                'initial processing' => 'CS Processing',
                'for maintenance',
                'forwarded to maintenance',
                'maintenance' => 'For Maintenance',
                'assigned',
                'plumber assigned',
                'assigned for maintenance' => 'Assigned',
                'in progress',
                'maintenance in progress' => 'In Progress',
                'completed',
                'accomplished',
                'maintenance completed' => 'Completed',
                'closed',
                'request closed' => 'Closed',
                'rejected' => 'Rejected',
                default => null,
            };

            $isInitialProcessingCompletedSearch = in_array(
                $normalizedSearch,
                [
                    'initial processing completed',
                    'processing completed',
                    'initial complete',
                ],
                true
            );

            $query->where(function (Builder $query) use (
                $search,
                $workflowSearchStatus,
                $isInitialProcessingCompletedSearch
            ) {
                $query
                    ->where('complaint_no', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('landmark', 'like', "%{$search}%")
                    ->orWhere('complainant_name', 'like', "%{$search}%")
                    ->orWhere('complainant_phone', 'like', "%{$search}%")
                    ->orWhereHas('consumer', function (Builder $consumer) use ($search) {
                        $consumer
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('account_number', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function (Builder $category) use ($search) {
                        $category
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('division', function (Builder $division) use ($search) {
                        $division->where('name', 'like', "%{$search}%");
                    });

                if ($workflowSearchStatus !== null) {
                    $query->orWhere('status', $workflowSearchStatus);
                }

                if ($isInitialProcessingCompletedSearch) {
                    $query->orWhereHas(
                        'commercialResolution',
                        function (Builder $resolutionQuery) {
                            $resolutionQuery->whereNotNull(
                                'initial_processing_completed_at'
                            );
                        }
                    );
                }
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');

            $allowedStatuses = [
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

            if ($status === 'Initial Processing Completed') {
                $query->whereHas(
                    'commercialResolution',
                    function (Builder $resolutionQuery) {
                        $resolutionQuery->whereNotNull(
                            'initial_processing_completed_at'
                        );
                    }
                );
            } elseif (in_array($status, $allowedStatuses, true)) {
                $query->where('status', $status);
            }
        }

        if ($request->filled('urgency')) {
            $urgency = $request->input('urgency');

            if (in_array($urgency, ['High', 'Moderate', 'Low'], true)) {
                $query->whereHas('aiAnalysis', function (Builder $aiQuery) use ($urgency) {
                    $aiQuery->where(function (Builder $finalQuery) use ($urgency) {
                        $finalQuery->where('verified_urgency_level', $urgency)
                            ->orWhere(function (Builder $fallbackQuery) use ($urgency) {
                                $fallbackQuery
                                    ->whereNull('verified_urgency_level')
                                    ->where('urgency_level', $urgency);
                            });
                    });
                });
            } elseif ($urgency === 'not_assessed') {
                $query->where(function (Builder $urgencyQuery) {
                    $urgencyQuery->whereDoesntHave('aiAnalysis')
                        ->orWhereHas('aiAnalysis', function (Builder $aiQuery) {
                            $aiQuery
                                ->whereNull('verified_urgency_level')
                                ->whereNull('urgency_level');
                        });
                });
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->input('date_from')
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->input('date_to')
            );
        }
    }

    public function report(Request $request)
    {
        $query = \App\Models\Complaint::query()
            ->with([
                'consumer.user',
                'category',
                'division',
                'aiAnalysis',
            ]);

        // Search
        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('complaint_no', 'like', "%{$search}%")
                    ->orWhere('complainant_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('consumer', function ($consumerQuery) use ($search) {
                        $consumerQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('account_number', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where('full_name', 'like', "%{$search}%");
                            });
                    })
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('division', function ($divisionQuery) use ($search) {
                        $divisionQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Urgency filtering: verified CS urgency takes precedence over AI urgency.
        if ($request->filled('urgency')) {
            $urgency = $request->input('urgency');

            $query->whereHas('aiAnalysis', function ($analysisQuery) use ($urgency) {
                if ($urgency === 'not_assessed') {
                    $analysisQuery->where(function ($q) {
                        $q->whereNull('verified_urgency_level')
                            ->whereNull('urgency_level');
                    });
                } else {
                    $analysisQuery->where(function ($q) use ($urgency) {
                        $q->where('verified_urgency_level', $urgency)
                            ->orWhere(function ($fallbackQuery) use ($urgency) {
                                $fallbackQuery->whereNull('verified_urgency_level')
                                    ->where('urgency_level', $urgency);
                            });
                    });
                }
            });

            // Include complaints without an AI analysis in "Not assessed".
            if ($urgency === 'not_assessed') {
                $query->orWhereDoesntHave('aiAnalysis');

                // Keep all other active filters applied to the no-analysis branch.
                // The grouped query below is rebuilt to avoid bypassing search/status/date.
                $query = \App\Models\Complaint::query()
                    ->with([
                        'consumer.user',
                        'category',
                        'division',
                        'aiAnalysis',
                    ])
                    ->where(function ($outer) use ($request) {
                        $outer->whereDoesntHave('aiAnalysis')
                            ->orWhereHas('aiAnalysis', function ($analysisQuery) {
                                $analysisQuery->whereNull('verified_urgency_level')
                                    ->whereNull('urgency_level');
                            });
                    });

                if ($request->filled('search')) {
                    $search = trim($request->input('search'));

                    $query->where(function ($q) use ($search) {
                        $q->where('complaint_no', 'like', "%{$search}%")
                            ->orWhere('complainant_name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhereHas('consumer', function ($consumerQuery) use ($search) {
                                $consumerQuery->where('full_name', 'like', "%{$search}%")
                                    ->orWhere('account_number', 'like', "%{$search}%")
                                    ->orWhereHas('user', function ($userQuery) use ($search) {
                                        $userQuery->where('full_name', 'like', "%{$search}%");
                                    });
                            })
                            ->orWhereHas('category', function ($categoryQuery) use ($search) {
                                $categoryQuery->where('name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('division', function ($divisionQuery) use ($search) {
                                $divisionQuery->where('name', 'like', "%{$search}%");
                            });
                    });
                }

                if ($request->filled('status')) {
                    $query->where('status', $request->input('status'));
                }

                if ($request->filled('date_from')) {
                    $query->whereDate('created_at', '>=', $request->input('date_from'));
                }

                if ($request->filled('date_to')) {
                    $query->whereDate('created_at', '<=', $request->input('date_to'));
                }
            }
        }

        $complaints = $query
            ->orderByDesc('created_at')
            ->get();

        return view('admin.complaints.report', compact('complaints'));
    }
}
