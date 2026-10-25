<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\AssignComplaintRequest;
use App\Models\Complaint;
use App\Models\User;
use App\Services\AI\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Services\MaintenanceSchedulingService;
use Carbon\CarbonImmutable;
use App\Services\MaintenancePlanningQueueService;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'string', 'in:For Assignment,Verified,For Maintenance,Assigned,In Progress,Completed,Closed'],
            'urgency' => ['nullable', 'string', 'in:High,Moderate,Low,not_assessed'],
            'complaint_type_id' => ['nullable', 'integer', 'exists:complaint_categories,id'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ]);

        $query = Complaint::query()
            ->with([
                'consumer.address',
                'division',
                'category',
                'customerService',
                'verifier',
                'technicians',
                'aiAnalysis',
                'commercialResolution',
            ])
            ->whereIn('status', [
                'Verified',
                'For Maintenance',
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ])
            ->where(fn($scope) => $this->applyMaintenanceOnlyScope($scope));

        $this->applySearch($query, $request);

        if ($request->filled('complaint_type_id')) {
            $query->where('complaints.complaint_category_id', $request->integer('complaint_type_id'));
        }

        $allowedStatuses = [
            'For Assignment',
            'Verified',
            'For Maintenance',
            'Assigned',
            'In Progress',
            'Completed',
            'Closed',
        ];

        if (
            $request->filled('status') &&
            in_array($request->status, $allowedStatuses, true)
        ) {
            if ($request->status === 'For Assignment') {
                $this->applyForAssignmentScope($query);
            } else {
                $query->where('status', $request->status);
            }
        }

        $this->applyUrgencyFilter($query, $request);

        if ($request->filled('date_from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        $complaints = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $verifiedCount =
            $this->forAssignmentBaseQuery()->count();

        $assignedCount = Complaint::query()
            ->where('complaints.status', 'Assigned')
            ->where(fn($scope) => $this->applyMaintenanceOnlyScope($scope))
            ->count();

        $inProgressCount = Complaint::query()
            ->where('complaints.status', 'In Progress')
            ->where(fn($scope) => $this->applyMaintenanceOnlyScope($scope))
            ->count();

        $completedCount = Complaint::query()
            ->where('complaints.status', 'Completed')
            ->where(fn($scope) => $this->applyMaintenanceOnlyScope($scope))
            ->count();

        $closedCount = Complaint::query()
            ->where('complaints.status', 'Closed')
            ->where(fn($scope) => $this->applyMaintenanceOnlyScope($scope))
            ->count();

        $complaintTypes = \App\Models\ComplaintCategory::query()->orderBy('name')->get(['id', 'name']);

        return view(
            'maintenance-manager.complaints.index',
            compact(
                'complaints',
                'verifiedCount',
                'assignedCount',
                'inProgressCount',
                'completedCount',
                'closedCount',
                'complaintTypes'
            )
        );
    }

    public function printReport(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'string', 'in:For Assignment,Verified,For Maintenance,Assigned,In Progress,Completed,Closed'],
            'urgency' => ['nullable', 'string', 'in:High,Moderate,Low,not_assessed'],
            'complaint_type_id' => ['nullable', 'integer', 'exists:complaint_categories,id'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ]);

        $query = Complaint::query()
            ->with([
                'consumer.address',
                'division',
                'category',
                'customerService',
                'verifier',
                'technicians',
                'aiAnalysis',
                'commercialResolution',
            ])
            ->whereIn('status', [
                'Verified',
                'For Maintenance',
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ])
            ->where(fn($scope) => $this->applyMaintenanceOnlyScope($scope));

        $this->applySearch($query, $request);

        if ($request->filled('complaint_type_id')) {
            $query->where('complaints.complaint_category_id', $request->integer('complaint_type_id'));
        }

        $allowedStatuses = [
            'For Assignment',
            'Verified',
            'For Maintenance',
            'Assigned',
            'In Progress',
            'Completed',
            'Closed',
        ];

        if (
            $request->filled('status') &&
            in_array($request->status, $allowedStatuses, true)
        ) {
            if ($request->status === 'For Assignment') {
                $this->applyForAssignmentScope($query);
            } else {
                $query->where(
                    'status',
                    $request->status
                );
            }
        }

        $this->applyUrgencyFilter($query, $request);

        if ($request->filled('date_from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        $complaints = $query
            ->latest()
            ->get();

        $selectedComplaintType = $request->filled('complaint_type_id')
            ? \App\Models\ComplaintCategory::find($request->integer('complaint_type_id'))
            : null;

        return view(
            'maintenance-manager.complaints.print-report',
            compact('complaints', 'selectedComplaintType')
        );
    }

    public function forAssignment(Request $request)
    {
        $query = $this->forAssignmentBaseQuery()
            ->with([
                'consumer.address',
                'division',
                'category',
                'customerService',
                'verifier',
                'technicians',
                'aiAnalysis',
                'commercialResolution.processor',
                'commercialResolution.forwarder',
            ]);

        $this->applySearch($query, $request);
        $this->applyUrgencyFilter($query, $request);

        if ($request->filled('date_from')) {
            $query->where(
                function ($dateQuery) use ($request) {
                    $dateQuery
                        ->where(
                            function (
                                $engineeringQuery
                            ) use ($request) {
                                $engineeringQuery
                                    ->where(
                                        'complaints.status',
                                        'Verified'
                                    )
                                    ->whereDate(
                                        'complaints.verified_at',
                                        '>=',
                                        $request->date_from
                                    );
                            }
                        )
                        ->orWhere(
                            function (
                                $forwardedQuery
                            ) use ($request) {
                                $forwardedQuery
                                    ->where(
                                        'complaints.status',
                                        'For Maintenance'
                                    )
                                    ->whereHas(
                                        'commercialResolution',
                                        function (
                                            $resolutionQuery
                                        ) use ($request) {
                                            $resolutionQuery
                                                ->whereDate(
                                                    'forwarded_to_maintenance_at',
                                                    '>=',
                                                    $request->date_from
                                                );
                                        }
                                    );
                            }
                        );
                }
            );
        }

        if ($request->filled('date_to')) {
            $query->where(
                function ($dateQuery) use ($request) {
                    $dateQuery
                        ->where(
                            function (
                                $engineeringQuery
                            ) use ($request) {
                                $engineeringQuery
                                    ->where(
                                        'complaints.status',
                                        'Verified'
                                    )
                                    ->whereDate(
                                        'complaints.verified_at',
                                        '<=',
                                        $request->date_to
                                    );
                            }
                        )
                        ->orWhere(
                            function (
                                $forwardedQuery
                            ) use ($request) {
                                $forwardedQuery
                                    ->where(
                                        'complaints.status',
                                        'For Maintenance'
                                    )
                                    ->whereHas(
                                        'commercialResolution',
                                        function (
                                            $resolutionQuery
                                        ) use ($request) {
                                            $resolutionQuery
                                                ->whereDate(
                                                    'forwarded_to_maintenance_at',
                                                    '<=',
                                                    $request->date_to
                                                );
                                        }
                                    );
                            }
                        );
                }
            );
        }

        $baseQueue =
            $this->forAssignmentBaseQuery();

        $totalForAssignment =
            (clone $baseQueue)->count();


        $highUrgencyCount = (clone $baseQueue)
            ->whereHas('aiAnalysis', function ($query) {
                $query->where(function ($urgency) {
                    $urgency
                        ->where('verified_urgency_level', 'High')
                        ->orWhere(function ($fallback) {
                            $fallback
                                ->where(function ($verified) {
                                    $verified
                                        ->whereNull('verified_urgency_level')
                                        ->orWhere('verified_urgency_level', '');
                                })
                                ->where('urgency_level', 'High');
                        });
                });
            })
            ->count();

        $moderateUrgencyCount = (clone $baseQueue)
            ->whereHas('aiAnalysis', function ($query) {
                $query->where(function ($urgency) {
                    $urgency
                        ->where('verified_urgency_level', 'Moderate')
                        ->orWhere(function ($fallback) {
                            $fallback
                                ->where(function ($verified) {
                                    $verified
                                        ->whereNull('verified_urgency_level')
                                        ->orWhere('verified_urgency_level', '');
                                })
                                ->where('urgency_level', 'Moderate');
                        });
                });
            })
            ->count();

        $lowUrgencyCount = (clone $baseQueue)
            ->whereHas('aiAnalysis', function ($query) {
                $query->where(function ($urgency) {
                    $urgency
                        ->where('verified_urgency_level', 'Low')
                        ->orWhere(function ($fallback) {
                            $fallback
                                ->where(function ($verified) {
                                    $verified
                                        ->whereNull('verified_urgency_level')
                                        ->orWhere('verified_urgency_level', '');
                                })
                                ->where('urgency_level', 'Low');
                        });
                });
            })
            ->count();


        $complaints = $query
            ->leftJoin(
                'complaint_ai_analyses',
                'complaints.id',
                '=',
                'complaint_ai_analyses.complaint_id'
            )
            ->select('complaints.*')

            ->orderByRaw("
    CASE COALESCE(
        NULLIF(complaint_ai_analyses.verified_urgency_level, ''),
        NULLIF(complaint_ai_analyses.urgency_level, '')
    )
        WHEN 'High' THEN 1
        WHEN 'Moderate' THEN 2
        WHEN 'Low' THEN 3
        ELSE 4
    END
")

            ->orderByRaw("
                CASE
                    WHEN complaints.status = 'For Maintenance'
                        THEN 1
                    ELSE 2
                END
            ")
            ->orderBy(
                'complaints.verified_at',
                'asc'
            )
            ->paginate(10)
            ->withQueryString();

        return view(
            'maintenance-manager.complaints.for-assignment',
            compact(
                'complaints',
                'totalForAssignment',
                'highUrgencyCount',
                'moderateUrgencyCount',
                'lowUrgencyCount'
            )
        );
    }

    public function show(
        Request $request,
        Complaint $complaint,
        AIService $aiService
    ) {
        /*
    |--------------------------------------------------------------------------
    | Context-aware return navigation
    |--------------------------------------------------------------------------
    */

        $source = $request->query('source', 'complaints');

        $backUrl = route('maintenance-manager.complaints.index');
        $backLabel = 'Back';

        if ($source === 'for-assignment') {

            $validated = $request->validate([
                'page' => ['nullable', 'integer', 'min:1'],
                'search' => ['nullable', 'string', 'max:255'],
                'urgency' => ['nullable', 'string', 'max:100'],
                'date_from' => ['nullable', 'date_format:Y-m-d'],
                'date_to' => ['nullable', 'date_format:Y-m-d'],
            ]);

            $backUrl = route(
                'maintenance-manager.complaints.for-assignment',
                $validated
            );

            $backLabel = 'Back to For Assignment';
        } elseif ($source === 'schedule') {

            $validated = $request->validate([
                'date' => ['nullable', 'date_format:Y-m-d'],
                'section' => [
                    'nullable',
                    'in:today,scheduled,ready,backlog',
                ],
            ]);

            $parameters = [];

            if (!empty($validated['date'])) {
                $parameters['date'] = $validated['date'];
            }

            $section = $validated['section'] ?? 'scheduled';

            $backUrl = route(
                'maintenance-manager.schedules.index',
                $parameters
            ) . '#' . $section;

            $backLabel = 'Back to Daily Maintenance Planning';
        } else {

            $validated = $request->validate([
                'page' => ['nullable', 'integer', 'min:1'],
                'search' => ['nullable', 'string', 'max:255'],
                'status' => ['nullable', 'string', 'max:100'],
                'urgency' => ['nullable', 'string', 'max:100'],
                'date_from' => ['nullable', 'date_format:Y-m-d'],
                'date_to' => ['nullable', 'date_format:Y-m-d'],
            ]);

            $backUrl = route(
                'maintenance-manager.complaints.index',
                $validated
            );
        }


        $complaint->load([
            'consumer.address',
            'division',
            'category',
            'photos',
            'customerService',
            'verifier',
            'technicians',
            'maintenanceReport.technician',
            'maintenanceReport.reviewer',
            'aiAnalysis',
            'commercialResolution.processor',
            'commercialResolution.forwarder',
        ]);

        $workOrders = \App\Models\MaintenanceWorkOrder::query()
            ->with([
                'schedule',
                'technicians',
            ])
            ->where('complaint_id', $complaint->id)
            ->orderBy('visit_number')
            ->orderBy('id')
            ->get();

        $workOrderIds = $workOrders->pluck('id');

        $plumberReplacementHistories = \App\Models\MaintenanceScheduleHistory::query()
            ->with('performer')
            ->whereIn('maintenance_work_order_id', $workOrderIds)
            ->where('action', 'Plumber Replaced')
            ->orderBy('created_at')
            ->get()
            ->groupBy('maintenance_work_order_id');


        $isEngineering =
            $this->isEngineering($complaint);


        $canAssign = app(MaintenancePlanningQueueService::class)
            ->unassigned()
            ->whereKey($complaint->id)
            ->exists();


        $technicians = collect();

        $plumberRecommendations = [
            'has_recommendations' => false,
            'count' => 0,
            'recommendations' => [],
            'human_confirmation_required' => true,
        ];

        $plumberRecommendationError = null;

        if ($canAssign) {
            $technicians =
                User::role('Maintenance Technician')
                ->with('serviceArea')
                ->where('is_active', true)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();

            try {
                $plumberRecommendations =
                    $this->getPlumberRecommendations(
                        $complaint,
                        $aiService
                    );
            } catch (Throwable $exception) {
                report($exception);

                $plumberRecommendationError =
                    'Plumber recommendation analysis is temporarily unavailable.';
            }
        }

        return view(
            'maintenance-manager.complaints.show',
            compact(
                'complaint',
                'technicians',
                'plumberRecommendations',
                'plumberRecommendationError',
                'isEngineering',
                'canAssign',
                'backUrl',
                'backLabel',
                'workOrders',
                'plumberReplacementHistories'
            )
        );
    }

    public function assign(
        AssignComplaintRequest $request,
        Complaint $complaint
    ) {
        return redirect()
            ->route('maintenance-manager.schedules.index')
            ->with(
                'error',
                'Technician assignment has moved to Daily Maintenance Planning. '
                    . 'Please select the complaint and its work date there.'
            );
    }

    public function report(
        Complaint $complaint
    ) {
        $complaint->load([
            'consumer.address',
            'division',
            'category',
            'customerService',
            'verifier',
            'technicians',
            'maintenanceReport.technician',
            'maintenanceReport.reviewer',
            'commercialResolution.processor',
            'commercialResolution.forwarder',
            'aiAnalysis',
        ]);

        return view(
            'maintenance-manager.complaints.report',
            compact('complaint')
        );
    }


    /**
     * Manager complaint records include Engineering cases and Commercial
     * cases explicitly forwarded to Maintenance by Customer Service.
     * A commercial complaint that remains within Commercial Services is
     * excluded, regardless of its status.
     */
    private function applyMaintenanceOnlyScope($query): void
    {
        $query->where(function ($eligible) {
            $eligible->whereHas('division', function ($division) {
                $division->whereRaw('LOWER(name) LIKE ?', ['%engineering%']);
            })->orWhere(function ($forwarded) {
                $forwarded->whereHas('division', function ($division) {
                    $division->whereRaw('LOWER(name) LIKE ?', ['%commercial%']);
                })->whereHas('commercialResolution', function ($resolution) {
                    $resolution->whereNotNull('forwarded_to_maintenance_at');
                });
            });
        });
    }

    private function forAssignmentBaseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return app(MaintenancePlanningQueueService::class)
            ->unassigned();
    }


    private function applyForAssignmentScope($query): void
    {
        $eligibleIds = $this->forAssignmentBaseQuery()
            ->select('complaints.id');

        $query->whereIn('complaints.id', $eligibleIds);
    }

    private function applySearch(
        $query,
        Request $request
    ): void {
        if (!$request->filled('search')) {
            return;
        }

        $search = trim(
            $request->search
        );

        $query->where(
            function ($query) use ($search) {
                $query
                    ->where(
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
                    ->orWhere(
                        'complainant_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'complainant_phone',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'consumer',
                        function (
                            $consumer
                        ) use ($search) {
                            $consumer
                                ->where(
                                    'first_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'middle_name',
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
                                )
                                ->orWhere(
                                    'phone',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    )
                    ->orWhereHas(
                        'division',
                        function (
                            $division
                        ) use ($search) {
                            $division->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        }
                    )
                    ->orWhereHas(
                        'category',
                        function (
                            $category
                        ) use ($search) {
                            $category->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
            }
        );
    }


    private function applyUrgencyFilter(
        $query,
        Request $request
    ): void {
        if (!$request->filled('urgency')) {
            return;
        }

        $urgency = $request->input('urgency');

        if (in_array($urgency, ['High', 'Moderate', 'Low'], true)) {
            $query->whereHas('aiAnalysis', function ($aiQuery) use ($urgency) {
                $aiQuery->where(function ($effectiveUrgency) use ($urgency) {
                    // Use the urgency confirmed by Customer Service first.
                    $effectiveUrgency->where(
                        'verified_urgency_level',
                        $urgency
                    )

                        // Otherwise, fall back to the original AI prediction.
                        ->orWhere(function ($aiFallback) use ($urgency) {
                            $aiFallback
                                ->where(function ($verified) {
                                    $verified
                                        ->whereNull('verified_urgency_level')
                                        ->orWhere('verified_urgency_level', '');
                                })
                                ->where('urgency_level', $urgency);
                        });
                });
            });

            return;
        }

        if ($urgency === 'not_assessed') {
            $query->where(function ($scope) {
                $scope
                    ->whereDoesntHave('aiAnalysis')
                    ->orWhereHas('aiAnalysis', function ($aiQuery) {
                        $aiQuery->where(function ($effectiveUrgency) {
                            // Not assessed only when both values are empty.
                            $effectiveUrgency
                                ->where(function ($verified) {
                                    $verified
                                        ->whereNull('verified_urgency_level')
                                        ->orWhere('verified_urgency_level', '');
                                })
                                ->where(function ($ai) {
                                    $ai
                                        ->whereNull('urgency_level')
                                        ->orWhere('urgency_level', '');
                                });
                        });
                    });
            });
        }
    }


    private function isEngineering(
        Complaint $complaint
    ): bool {
        $complaint->loadMissing(
            'division'
        );

        $divisionName = strtolower(
            trim(
                (string)
                $complaint->division?->name
            )
        );

        return str_contains(
            $divisionName,
            'engineering'
        );
    }

    private function getPlumberRecommendations(
        Complaint $complaint,
        AIService $aiService
    ): array {
        $plumbers =
            User::role('Maintenance Technician')
            ->with('serviceArea')
            ->where('is_active', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        if ($plumbers->isEmpty()) {
            return [
                'has_recommendations' => false,
                'count' => 0,
                'recommendations' => [],
                'human_confirmation_required' => true,
            ];
        }

        $plumberPayloads = $plumbers
            ->map(
                fn(User $plumber) =>
                $this->buildPlumberPayload(
                    $plumber
                )
            )
            ->values()
            ->all();

        [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ] = $this->resolveComplaintLocation(
            $complaint
        );

        $complaintPayload = [
            'id' =>
            (int) $complaint->id,

            'complaint_no' =>
            (string)
            $complaint->complaint_no,

            'complaint_type' =>
            $complaint
                ->category
                ?->name,

            'division' =>
            $complaint
                ->division
                ?->name,

            'latitude' =>
            $latitude,

            'longitude' =>
            $longitude,
        ];

        $allPlumbersAreFree =
            collect($plumberPayloads)
            ->every(
                fn(array $plumber) =>
                (int) (
                    $plumber['active_workload'] ?? 0
                ) === 0
            );

        $recommendationLimit =
            $allPlumbersAreFree
            ? count($plumberPayloads)
            : min(
                6,
                count($plumberPayloads)
            );

        return $aiService
            ->recommendPlumbers(
                $complaintPayload,
                $plumberPayloads,
                $recommendationLimit
            );
    }

    private function resolveComplaintLocation(
        Complaint $complaint
    ): array {
        $complaint->loadMissing([
            'division',
            'consumer.address',
        ]);

        if ($this->isEngineering($complaint)) {
            return [
                'latitude' =>
                $complaint->latitude !== null
                    ? (float)
                    $complaint->latitude
                    : null,

                'longitude' =>
                $complaint->longitude !== null
                    ? (float)
                    $complaint->longitude
                    : null,
            ];
        }

        return [
            'latitude' =>
            $complaint
                ->consumer
                ?->address
                ?->latitude !== null
                ? (float)
                $complaint
                    ->consumer
                    ?->address
                    ?->latitude
                : null,

            'longitude' =>
            $complaint
                ->consumer
                ?->address
                ?->longitude !== null
                ? (float)
                $complaint
                    ->consumer
                    ?->address
                    ?->longitude
                : null,
        ];
    }

    private function buildPlumberPayload(
        User $plumber
    ): array {
        $activeAssignments =
            Complaint::query()
            ->with([
                'division',
                'category',
                'consumer.address',
            ])
            ->whereHas(
                'technicians',
                function (
                    $query
                ) use ($plumber) {
                    $query->where(
                        'users.id',
                        $plumber->id
                    );
                }
            )
            ->whereIn(
                'status',
                [
                    'Assigned',
                    'In Progress',
                ]
            )
            ->get();

        $recentAssignments =
            Complaint::query()
            ->with([
                'division',
                'category',
                'consumer.address',
            ])
            ->whereHas(
                'technicians',
                function (
                    $query
                ) use ($plumber) {
                    $query->where(
                        'users.id',
                        $plumber->id
                    );
                }
            )
            ->whereIn(
                'status',
                [
                    'Completed',
                    'Closed',
                ]
            )
            ->whereNotNull(
                'completed_at'
            )
            ->where(
                'completed_at',
                '>=',
                now()->subDays(14)
            )
            ->orderByDesc(
                'completed_at'
            )
            ->get();

        return [
            'id' =>
            (int) $plumber->id,

            'name' =>
            (string)
            $plumber->full_name,

            'service_area' =>
            $plumber->serviceArea
                ? [
                    'id' =>
                    (int)
                    $plumber
                        ->serviceArea
                        ->id,

                    'name' =>
                    (string)
                    $plumber
                        ->serviceArea
                        ->name,
                ]
                : null,

            'active_workload' =>
            $activeAssignments
                ->count(),

            'active_assignments' =>
            $activeAssignments
                ->map(
                    function (
                        Complaint $assignment
                    ) {
                        $location =
                            $this
                            ->resolveComplaintLocation(
                                $assignment
                            );

                        return [
                            'complaint_id' =>
                            (int)
                            $assignment->id,

                            'complaint_no' =>
                            (string)
                            $assignment
                                ->complaint_no,

                            'complaint_type' =>
                            $assignment
                                ->category
                                ?->name,

                            'status' =>
                            (string)
                            $assignment
                                ->status,

                            'latitude' =>
                            $location['latitude'],

                            'longitude' =>
                            $location['longitude'],
                        ];
                    }
                )
                ->values()
                ->all(),

            'recent_assignments' =>
            $recentAssignments
                ->map(
                    function (
                        Complaint $assignment
                    ) {
                        $location =
                            $this
                            ->resolveComplaintLocation(
                                $assignment
                            );

                        return [
                            'complaint_id' =>
                            (int)
                            $assignment->id,

                            'complaint_no' =>
                            (string)
                            $assignment
                                ->complaint_no,

                            'complaint_type' =>
                            $assignment
                                ->category
                                ?->name,

                            'latitude' =>
                            $location['latitude'],

                            'longitude' =>
                            $location['longitude'],

                            'completed_at' =>
                            $assignment
                                ->completed_at
                                ?->toIso8601String(),
                        ];
                    }
                )
                ->values()
                ->all(),
        ];
    }
}
