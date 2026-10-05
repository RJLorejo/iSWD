<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\AssignComplaintRequest;
use App\Models\Complaint;
use App\Models\User;
use App\Notifications\PlumberComplaintAssigned;
use App\Services\AI\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
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
            ]);

        $this->applySearch($query, $request);

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
            ->where('status', 'Assigned')
            ->count();

        $inProgressCount = Complaint::query()
            ->where('status', 'In Progress')
            ->count();

        $completedCount = Complaint::query()
            ->where('status', 'Completed')
            ->count();

        $closedCount = Complaint::query()
            ->where('status', 'Closed')
            ->count();

        return view(
            'maintenance-manager.complaints.index',
            compact(
                'complaints',
                'verifiedCount',
                'assignedCount',
                'inProgressCount',
                'completedCount',
                'closedCount'
            )
        );
    }

    public function printReport(Request $request)
    {
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
            ]);

        $this->applySearch($query, $request);

        if ($request->filled('division_id')) {
            $query->where(
                'division_id',
                $request->division_id
            );
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

        return view(
            'maintenance-manager.complaints.print-report',
            compact('complaints')
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
                                        'status',
                                        'Verified'
                                    )
                                    ->whereDate(
                                        'verified_at',
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
                                        'status',
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
                                        'status',
                                        'Verified'
                                    )
                                    ->whereDate(
                                        'verified_at',
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
                                        'status',
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

        $highUrgencyCount =
            (clone $baseQueue)
            ->whereHas(
                'aiAnalysis',
                function ($query) {
                    $query->where(
                        'urgency_level',
                        'High'
                    );
                }
            )
            ->count();

        $moderateUrgencyCount =
            (clone $baseQueue)
            ->whereHas(
                'aiAnalysis',
                function ($query) {
                    $query->where(
                        'urgency_level',
                        'Moderate'
                    );
                }
            )
            ->count();

        $lowUrgencyCount =
            (clone $baseQueue)
            ->whereHas(
                'aiAnalysis',
                function ($query) {
                    $query->where(
                        'urgency_level',
                        'Low'
                    );
                }
            )
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
                CASE complaint_ai_analyses.urgency_level
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
        Complaint $complaint,
        AIService $aiService
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
            'aiAnalysis',
            'commercialResolution.processor',
            'commercialResolution.forwarder',
        ]);

        $isEngineering =
            $this->isEngineering($complaint);

        $canAssign = (
            (
                $isEngineering &&
                $complaint->status === 'Verified'
            ) ||
            $complaint->status === 'For Maintenance'
        ) && $complaint->technicians->isEmpty();

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
                'canAssign'
            )
        );
    }

    public function assign(
        AssignComplaintRequest $request,
        Complaint $complaint
    ) {
        $complaint->loadMissing([
            'division',
            'technicians',
            'commercialResolution',
        ]);

        $isEngineering =
            $this->isEngineering($complaint);

        $isDirectEngineeringAssignment =
            $isEngineering &&
            $complaint->status === 'Verified';

        $isForwardedMaintenanceRequest =
            $complaint->status === 'For Maintenance';

        if (
            !$isDirectEngineeringAssignment &&
            !$isForwardedMaintenanceRequest
        ) {
            return redirect()
                ->route(
                    'maintenance-manager.complaints.show',
                    $complaint
                )
                ->with(
                    'error',
                    'This complaint or service request is not currently available for maintenance assignment.'
                );
        }

        if ($complaint->technicians->isNotEmpty()) {
            return back()->with(
                'error',
                'A maintenance team has already been assigned to this complaint or service request.'
            );
        }

        if (
            $isForwardedMaintenanceRequest &&
            !$complaint
                ->commercialResolution
                ?->forwarded_to_maintenance_at
        ) {
            return back()->with(
                'error',
                'This service request does not have a valid Customer Service maintenance handoff.'
            );
        }

        $technicianIds = collect(
            $request->input(
                'technician_ids',
                []
            )
        )
            ->map(
                fn($id) => (int) $id
            )
            ->filter()
            ->unique()
            ->values();

        if ($technicianIds->isEmpty()) {
            return back()->with(
                'error',
                'Please select at least one maintenance technician.'
            );
        }

        $technicians =
            User::role('Maintenance Technician')
            ->where('is_active', true)
            ->whereIn(
                'id',
                $technicianIds
            )
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        if (
            $technicians->count() !==
            $technicianIds->count()
        ) {
            return back()->with(
                'error',
                'One or more selected technicians are invalid or inactive.'
            );
        }

        DB::transaction(
            function () use (
                $complaint,
                $technicians
            ) {
                $assignments = [];

                $assignedAt = now();

                foreach ($technicians as $technician) {
                    $assignments[$technician->id] = [
                        'status' => 'Assigned',
                        'assigned_at' => $assignedAt,
                        'started_at' => null,
                        'completed_at' => null,
                    ];
                }

                $complaint
                    ->technicians()
                    ->sync($assignments);

                $complaint->update([
                    'status' => 'Assigned',
                    'completed_at' => null,
                ]);
            }
        );

        $complaint->refresh();

        $complaint->loadMissing([
            'division',
            'category',
            'aiAnalysis',
        ]);

        $emailsSent = 0;
        $emailsFailed = 0;

        foreach ($technicians as $technician) {

            if (!$technician->email) {
                continue;
            }

            try {

                $technician->notify(
                    new PlumberComplaintAssigned(
                        $complaint
                    )
                );

                $emailsSent++;
            } catch (Throwable $exception) {

                $emailsFailed++;

                Log::error(
                    'Plumber assignment email failed.',
                    [
                        'complaint_id' =>
                        $complaint->id,

                        'complaint_no' =>
                        $complaint->complaint_no,

                        'technician_id' =>
                        $technician->id,

                        'email' =>
                        $technician->email,

                        'error' =>
                        $exception->getMessage(),
                    ]
                );
            }
        }

        $message =
            $technicians->count() .
            ' maintenance technician(s) successfully assigned.';

        if ($emailsSent > 0) {
            $message .=
                ' ' .
                $emailsSent .
                ' plumber email notification(s) sent.';
        }

        if ($emailsFailed > 0) {
            $message .=
                ' ' .
                $emailsFailed .
                ' email notification(s) could not be sent.';
        }

        return redirect()
            ->route(
                'maintenance-manager.complaints.show',
                $complaint
            )
            ->with(
                'success',
                $message
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

    private function forAssignmentBaseQuery()
    {
        return Complaint::query()
            ->whereDoesntHave('technicians')
            ->where(
                function ($query) {
                    $query
                        ->where(
                            function (
                                $engineeringQuery
                            ) {
                                $engineeringQuery
                                    ->where(
                                        'status',
                                        'Verified'
                                    )
                                    ->whereHas(
                                        'division',
                                        function (
                                            $divisionQuery
                                        ) {
                                            $divisionQuery
                                                ->whereRaw(
                                                    'LOWER(TRIM(name)) LIKE ?',
                                                    ['%engineering%']
                                                );
                                        }
                                    );
                            }
                        )
                        ->orWhere(
                            function (
                                $forwardedQuery
                            ) {
                                $forwardedQuery
                                    ->where(
                                        'status',
                                        'For Maintenance'
                                    )
                                    ->whereHas(
                                        'commercialResolution',
                                        function (
                                            $resolutionQuery
                                        ) {
                                            $resolutionQuery
                                                ->whereNotNull(
                                                    'forwarded_to_maintenance_at'
                                                );
                                        }
                                    );
                            }
                        );
                }
            );
    }

    private function applyForAssignmentScope(
        $query
    ): void {
        $query
            ->whereDoesntHave('technicians')
            ->where(
                function ($scopeQuery) {
                    $scopeQuery
                        ->where(
                            function (
                                $engineeringQuery
                            ) {
                                $engineeringQuery
                                    ->where(
                                        'status',
                                        'Verified'
                                    )
                                    ->whereHas(
                                        'division',
                                        function (
                                            $divisionQuery
                                        ) {
                                            $divisionQuery
                                                ->whereRaw(
                                                    'LOWER(TRIM(name)) LIKE ?',
                                                    ['%engineering%']
                                                );
                                        }
                                    );
                            }
                        )
                        ->orWhere(
                            function (
                                $forwardedQuery
                            ) {
                                $forwardedQuery
                                    ->where(
                                        'status',
                                        'For Maintenance'
                                    )
                                    ->whereHas(
                                        'commercialResolution',
                                        function (
                                            $resolutionQuery
                                        ) {
                                            $resolutionQuery
                                                ->whereNotNull(
                                                    'forwarded_to_maintenance_at'
                                                );
                                        }
                                    );
                            }
                        );
                }
            );
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

        $urgency = $request->urgency;

        if (
            in_array(
                $urgency,
                [
                    'High',
                    'Moderate',
                    'Low',
                ],
                true
            )
        ) {
            $query->whereHas(
                'aiAnalysis',
                function (
                    $aiQuery
                ) use ($urgency) {
                    $aiQuery->where(
                        'urgency_level',
                        $urgency
                    );
                }
            );

            return;
        }

        if ($urgency === 'not_assessed') {
            $query->where(
                function ($urgencyQuery) {
                    $urgencyQuery
                        ->whereDoesntHave(
                            'aiAnalysis'
                        )
                        ->orWhereHas(
                            'aiAnalysis',
                            function (
                                $aiQuery
                            ) {
                                $aiQuery
                                    ->whereNull(
                                        'urgency_level'
                                    )
                                    ->orWhere(
                                        'urgency_level',
                                        ''
                                    );
                            }
                        );
                }
            );
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
