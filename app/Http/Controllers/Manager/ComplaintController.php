<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\AssignComplaintRequest;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Division;
use App\Services\AI\AIService;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {

        $query = Complaint::query()
            ->with([
                'consumer',
                'division',
                'category',
                'customerService',
                'verifier',
                'technicians',
                'aiAnalysis',
            ])
            ->whereIn('status', [
                'Verified',
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ]);


        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
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
                        function ($consumer) use ($search) {

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
                                );
                        }
                    )

                    ->orWhereHas(
                        'division',
                        function ($division) use ($search) {

                            $division->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        }
                    )

                    ->orWhereHas(
                        'category',
                        function ($category) use ($search) {

                            $category->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
            });
        }

        $allowedStatuses = [
            'For Assignment',
            'Verified',
            'Assigned',
            'In Progress',
            'Completed',
            'Closed',
        ];


        if (
            $request->filled('status') &&
            in_array(
                $request->status,
                $allowedStatuses,
                true
            )
        ) {

            if ($request->status === 'For Assignment') {


                $query
                    ->where('status', 'Verified')
                    ->whereDoesntHave('technicians')
                    ->whereHas(
                        'division',
                        function ($divisionQuery) {

                            $divisionQuery->where(
                                'name',
                                'Engineering'
                            );
                        }
                    );
            } else {

                $query->where(
                    'status',
                    $request->status
                );
            }
        }


        if ($request->filled('urgency')) {

            $urgency = $request->urgency;

            if (
                in_array(
                    $urgency,
                    ['High', 'Moderate', 'Low'],
                    true
                )
            ) {

                $query->whereHas(
                    'aiAnalysis',
                    function ($aiQuery) use ($urgency) {

                        $aiQuery->where(
                            'urgency_level',
                            $urgency
                        );
                    }
                );
            } elseif ($urgency === 'not_assessed') {

                $query->where(
                    function ($urgencyQuery) {

                        $urgencyQuery
                            ->whereDoesntHave('aiAnalysis')
                            ->orWhereHas(
                                'aiAnalysis',
                                function ($aiQuery) {

                                    $aiQuery
                                        ->whereNull('urgency_level')
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


        $verifiedCount = Complaint::query()
            ->where('status', 'Verified')
            ->whereDoesntHave('technicians')
            ->whereHas('division', function ($query) {

                $query->whereRaw(
                    'LOWER(TRIM(name)) LIKE ?',
                    ['%engineering%']
                );
            })
            ->count();


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
                'closedCount',
            )
        );
    }

    /**
     * Print filtered complaint management report.
     */
    public function printReport(Request $request)
    {

        $query = Complaint::query()
            ->with([
                'consumer',
                'division',
                'category',
                'customerService',
                'verifier',
                'technicians',
                'aiAnalysis',
            ])
            ->whereIn('status', [
                'Verified',
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ]);


        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
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
                        function ($consumer) use ($search) {

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
                                );
                        }
                    );
            });
        }

        if ($request->filled('division_id')) {

            $query->where(
                'division_id',
                $request->division_id
            );
        }


        $allowedStatuses = [
            'For Assignment',
            'Verified',
            'Assigned',
            'In Progress',
            'Completed',
            'Closed',
        ];


        if (
            $request->filled('status') &&
            in_array(
                $request->status,
                $allowedStatuses,
                true
            )
        ) {

            if ($request->status === 'For Assignment') {

                $query
                    ->where('status', 'Verified')
                    ->whereDoesntHave('technicians')
                    ->whereHas(
                        'division',
                        function ($divisionQuery) {

                            $divisionQuery->where(
                                'name',
                                'Engineering'
                            );
                        }
                    );
            } else {

                $query->where(
                    'status',
                    $request->status
                );
            }
        }

        if ($request->filled('urgency')) {

            $urgency = $request->urgency;

            if (
                in_array(
                    $urgency,
                    ['High', 'Moderate', 'Low'],
                    true
                )
            ) {

                $query->whereHas(
                    'aiAnalysis',
                    function ($aiQuery) use ($urgency) {

                        $aiQuery->where(
                            'urgency_level',
                            $urgency
                        );
                    }
                );
            } elseif ($urgency === 'not_assessed') {

                $query->where(
                    function ($urgencyQuery) {

                        $urgencyQuery
                            ->whereDoesntHave('aiAnalysis')
                            ->orWhereHas(
                                'aiAnalysis',
                                function ($aiQuery) {

                                    $aiQuery
                                        ->whereNull('urgency_level')
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


        $selectedDivision = null;

        if ($request->filled('division_id')) {

            $selectedDivision = Division::find(
                $request->division_id
            );
        }


        return view(
            'maintenance-manager.complaints.print-report',
            compact(
                'complaints',
                'selectedDivision'
            )
        );
    }

    /**
     * Display Engineering complaints waiting for plumber assignment.
     *
     * Queue rules:
     * - Complaint must be Verified
     * - Complaint must belong to an Engineering division
     * - Complaint must not have assigned technicians yet
     */
    public function forAssignment(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | Base Queue
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | Do not use an exact division name such as:
    |
    |     where('name', 'Engineering')
    |
    | because the actual division name may be:
    | Engineering Division, Engineering Operation, etc.
    |
    */

        $query = Complaint::query()
            ->with([
                'consumer',
                'division',
                'category',
                'customerService',
                'verifier',
                'technicians',
                'aiAnalysis',
            ])
            ->where('status', 'Verified')
            ->whereDoesntHave('technicians')
            ->whereHas('division', function ($query) {

                $query->whereRaw(
                    'LOWER(TRIM(name)) LIKE ?',
                    ['%engineering%']
                );
            });


        /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($query) use ($search) {

                $query
                    ->where(
                        'complaint_no',
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

                    ->orWhereHas(
                        'consumer',
                        function ($consumer) use ($search) {

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
                        'category',
                        function ($category) use ($search) {

                            $category
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
            });
        }


        /*
    |--------------------------------------------------------------------------
    | Urgency Filter
    |--------------------------------------------------------------------------
    */

        if ($request->filled('urgency')) {

            if (
                in_array(
                    $request->urgency,
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
                    function ($aiQuery) use ($request) {

                        $aiQuery->where(
                            'urgency_level',
                            $request->urgency
                        );
                    }
                );
            }


            /*
        |--------------------------------------------------------------------------
        | Not Assessed
        |--------------------------------------------------------------------------
        */ elseif (
                $request->urgency === 'not_assessed'
            ) {

                $query->where(
                    function ($urgencyQuery) {

                        $urgencyQuery
                            ->whereDoesntHave(
                                'aiAnalysis'
                            )

                            ->orWhereHas(
                                'aiAnalysis',
                                function ($aiQuery) {

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


        /*
    |--------------------------------------------------------------------------
    | Date From
    |--------------------------------------------------------------------------
    |
    | This queue is based on verification, so use verified_at.
    |
    */

        if ($request->filled('date_from')) {

            $query->whereDate(
                'verified_at',
                '>=',
                $request->date_from
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Date To
    |--------------------------------------------------------------------------
    */

        if ($request->filled('date_to')) {

            $query->whereDate(
                'verified_at',
                '<=',
                $request->date_to
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Statistics Base Query
    |--------------------------------------------------------------------------
    |
    | These counts must use the exact same queue rules.
    |
    */

        $baseQueue = Complaint::query()
            ->where('status', 'Verified')
            ->whereDoesntHave('technicians')
            ->whereHas('division', function ($query) {

                $query->whereRaw(
                    'LOWER(TRIM(name)) LIKE ?',
                    ['%engineering%']
                );
            });


        /*
    |--------------------------------------------------------------------------
    | Total For Assignment
    |--------------------------------------------------------------------------
    */

        $totalForAssignment = (clone $baseQueue)
            ->count();


        /*
    |--------------------------------------------------------------------------
    | High Urgency
    |--------------------------------------------------------------------------
    */

        $highUrgencyCount = (clone $baseQueue)

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


        /*
    |--------------------------------------------------------------------------
    | Moderate Urgency
    |--------------------------------------------------------------------------
    */

        $moderateUrgencyCount = (clone $baseQueue)

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


        /*
    |--------------------------------------------------------------------------
    | Low Urgency
    |--------------------------------------------------------------------------
    */

        $lowUrgencyCount = (clone $baseQueue)

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

            ->select(
                'complaints.*'
            )

            ->orderByRaw("
            CASE complaint_ai_analyses.urgency_level

                WHEN 'High' THEN 1

                WHEN 'Moderate' THEN 2

                WHEN 'Low' THEN 3

                ELSE 4

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
            'maintenanceReport',
            'aiAnalysis',
        ]);

        $divisionName = strtolower(
            trim((string) $complaint->division?->name)
        );

        $isEngineering = str_contains(
            $divisionName,
            'engineering'
        );

        /*
        |--------------------------------------------------------------------------
        | Engineering-Only Plumber Assignment
        |--------------------------------------------------------------------------
        |
        | All complaints remain visible to the Maintenance Manager.
        | Commercial complaints can be reviewed, including their registered
        | service location, but only Engineering complaints can be assigned
        | to the maintenance/plumber team.
        |
        */

        $technicians = collect();

        $plumberRecommendations = [
            'has_recommendations' => false,
            'count' => 0,
            'recommendations' => [],
            'human_confirmation_required' => true,
        ];

        $plumberRecommendationError = null;

        if ($isEngineering) {
            $technicians = User::role(
                'Maintenance Technician'
            )
                ->where('is_active', true)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get();

            if (
                $complaint->status === 'Verified' &&
                $complaint->technicians->isEmpty()
            ) {
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
        }

        return view(
            'maintenance-manager.complaints.show',
            compact(
                'complaint',
                'technicians',
                'plumberRecommendations',
                'plumberRecommendationError',
                'isEngineering'
            )
        );
    }


    public function assign(

        AssignComplaintRequest $request,

        Complaint $complaint

    ) {

        /*

    |--------------------------------------------------------------------------

    | Workflow Validation

    |--------------------------------------------------------------------------

    */



        /*
        |--------------------------------------------------------------------------
        | Engineering-Only Assignment Protection
        |--------------------------------------------------------------------------
        */

        $complaint->loadMissing('division');

        $divisionName = strtolower(
            trim((string) $complaint->division?->name)
        );

        $isEngineering = str_contains(
            $divisionName,
            'engineering'
        );

        if (!$isEngineering) {
            return redirect()
                ->route(
                    'maintenance-manager.complaints.show',
                    $complaint
                )
                ->with(
                    'error',
                    'Only Engineering complaints can be assigned to maintenance technicians.'
                );
        }

        if ($complaint->status !== 'Verified') {

            return back()->with(

                'error',

                'Only verified complaints can be assigned to maintenance technicians.'

            );
        }



        /*

    |--------------------------------------------------------------------------

    | Get Selected Technicians

    |--------------------------------------------------------------------------

    */



        $technicianIds = collect($request->input('technician_ids', []))

            ->map(fn($id) => (int) $id)

            ->filter()

            ->unique()

            ->values();



        if ($technicianIds->isEmpty()) {

            return back()->with(

                'error',

                'Please select at least one maintenance technician.'

            );
        }



        /*

    |--------------------------------------------------------------------------

    | Verify Selected Users

    |--------------------------------------------------------------------------

    */



        $technicians = User::role('Maintenance Technician')

            ->where('is_active', true)

            ->whereIn('id', $technicianIds)

            ->orderBy('first_name')

            ->orderBy('last_name')

            ->get();



        if ($technicians->count() !== $technicianIds->count()) {

            return back()->with(

                'error',

                'One or more selected technicians are invalid or inactive.'

            );
        }



        /*

    |--------------------------------------------------------------------------

    | Assign Maintenance Team

    |--------------------------------------------------------------------------

    */



        DB::transaction(function () use (

            $complaint,

            $technicians

        ) {



            $assignments = [];



            foreach ($technicians->values() as $index => $technician) {



                $assignments[$technician->id] = [



                    // Individual technician assignment status

                    'status' => 'Assigned',



                    'assigned_at' => now(),



                    'started_at' => null,



                    'completed_at' => null,

                ];
            }



            /*

        |--------------------------------------------------------------------------

        | Sync Technician Team

        |--------------------------------------------------------------------------

        |

        | This creates/updates the complaint_technicians records.

        |

        */



            $complaint->technicians()->sync($assignments);



            /*

        |--------------------------------------------------------------------------

        | Update Complaint Workflow

        |--------------------------------------------------------------------------

        |

        | The actual technician assignment is stored in

        | complaint_technicians, NOT complaints.assigned_to.

        |

        */



            $complaint->update([

                'technicians' => null,

                'status' => 'Assigned',

            ]);
        });



        return redirect()

            ->route(

                'maintenance-manager.complaints.show',

                $complaint

            )

            ->with(

                'success',

                $technicians->count()

                    . ' maintenance technician(s) successfully assigned to this complaint.'

            );
    }



    /*\*

     \* Generate a printable report for a single complaint.

     */

    public function report(Complaint $complaint)

    {

        $complaint->load([

            'consumer',

            'category',

            'customerService',

            'verifier',

            'technicians',

            'maintenanceReport',

        ]);



        return view(

            'maintenance-manager.complaints.report',

            compact('complaint')

        );
    }



    private function getPlumberRecommendations(

        Complaint $complaint,

        AIService $aiService

    ): array {

        $plumbers = User::role(

            'Maintenance Technician'

        )

            ->where(

                'is_active',

                true

            )

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

                function (User $plumber) {

                    return $this

                        ->buildPlumberPayload(

                            $plumber

                        );
                }

            )

            ->values()

            ->all();



        $complaintPayload = [

            'id' => (int) $complaint->id,



            'complaint_no' =>

            (string) $complaint->complaint_no,



            'latitude' =>

            $complaint->latitude !== null

                ? (float) $complaint->latitude

                : null,



            'longitude' =>

            $complaint->longitude !== null

                ? (float) $complaint->longitude

                : null,

        ];





        /*
        |--------------------------------------------------------------------------
        | Dynamic AI Recommendation Limit
        |--------------------------------------------------------------------------
        |
        | If every active plumber has zero active workload, show all.
        | Otherwise, return only the Top 6 AI recommendations.
        |
        | This does not limit manual team size.
        |
        */

        $allPlumbersAreFree =
            collect($plumberPayloads)
            ->every(
                fn(array $plumber) =>
                (int) ($plumber['active_workload'] ?? 0) === 0
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





    private function buildPlumberPayload(

        User $plumber

    ): array {

        $activeAssignments =

            Complaint::query()

            ->whereHas(

                'technicians',

                function ($query) use (

                    $plumber

                ) {

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

            ->get([

                'id',

                'complaint_no',

                'status',

                'latitude',

                'longitude',

            ]);



        return [

            'id' =>

            (int) $plumber->id,



            'name' =>

            (string) $plumber->full_name,



            'active_workload' =>

            $activeAssignments->count(),



            'active_assignments' =>

            $activeAssignments

                ->map(

                    function (

                        Complaint $assignment

                    ) {

                        return [

                            'complaint_id' =>

                            (int) $assignment->id,



                            'complaint_no' =>

                            (string) $assignment

                                ->complaint_no,



                            'status' =>

                            (string) $assignment

                                ->status,



                            'latitude' =>

                            $assignment->latitude

                                !== null

                                ? (float) $assignment

                                    ->latitude

                                : null,



                            'longitude' =>

                            $assignment->longitude

                                !== null

                                ? (float) $assignment

                                    ->longitude

                                : null,

                        ];
                    }

                )

                ->values()

                ->all(),

        ];
    }
}
