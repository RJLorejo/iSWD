<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;

use App\Http\Requests\CustomerService\StoreComplaintRequest;

use App\Http\Requests\CustomerService\UpdateComplaintRequest;

use App\Http\Requests\CustomerService\VerifyComplaintRequest;

use App\Http\Requests\CustomerService\RejectComplaintRequest;

use App\Models\Complaint;

use App\Models\ComplaintCategory;

use App\Models\Consumer;

use App\Models\Division;

use App\Models\CommercialResolution;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Log;

use App\Services\AI\AIService;

use Throwable;

use Illuminate\Database\Eloquent\Builder;

class ComplaintController extends Controller

{

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

            ]);

        $this->applyComplaintIndexFilters(

            $query,

            $request

        );

        $complaints = $query

            ->orderByDesc('created_at')

            ->orderByDesc('id')

            ->paginate(10)

            ->withQueryString();

        $totalComplaints = Complaint::query()

            ->count();

        $pendingCount = Complaint::query()

            ->where(

                'status',

                'Pending'

            )

            ->count();

        $activeCount = Complaint::query()

            ->whereIn(

                'status',

                [

                    'Assigned',

                    'In Progress',

                ]

            )

            ->count();

        return view(

            'customer-service.complaints.index',

            compact(

                'complaints',

                'totalComplaints',

                'pendingCount',

                'activeCount',

            )

        );
    }

    /**

     * Print the currently filtered Customer Service complaint list.

     */

    public function printReport(Request $request)

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

            ]);

        /*

    |--------------------------------------------------------------------------

    | Same Filters As Index

    |--------------------------------------------------------------------------

    */

        $this->applyComplaintIndexFilters(

            $query,

            $request

        );

        /*

    |--------------------------------------------------------------------------

    | Results

    |--------------------------------------------------------------------------

    */

        $complaints = $query

            ->orderByDesc('created_at')

            ->orderByDesc('id')

            ->get();

        return view(

            'customer-service.complaints.print-report',

            compact('complaints')

        );
    }

    public function create()

    {

        $consumers = Consumer::query()

            ->with('address')

            ->where('is_active', true)

            ->orderBy('last_name')

            ->orderBy('first_name')

            ->get();

        $divisions = Division::query()

            ->where('is_active', true)

            ->with([

                'complaintTypes' => function ($query) {

                    $query

                        ->where('is_active', true)

                        ->orderBy('name');
                },

            ])

            ->orderBy('name')

            ->get();

        return view(

            'customer-service.complaints.create',

            compact(

                'consumers',

                'divisions'

            )

        );
    }

    public function store(

        StoreComplaintRequest $request,

        AIService $aiService

    ) {

        $validated = $request->validated();

        $division = Division::query()

            ->where(

                'id',

                $validated['division_id']

            )

            ->where(

                'is_active',

                true

            )

            ->firstOrFail();

        ComplaintCategory::query()

            ->where(

                'id',

                $validated['complaint_category_id']

            )

            ->where(

                'division_id',

                $division->id

            )

            ->where(

                'is_active',

                true

            )

            ->firstOrFail();

        /*

        |--------------------------------------------------------------------------

        | Create Complaint

        |--------------------------------------------------------------------------

        */

        $complaint = DB::transaction(

            function () use (

                $request,

                $validated,

                $division

            ) {

                $photoPath = null;

                if ($request->hasFile('photo')) {

                    $photoPath = $request

                        ->file('photo')

                        ->store(

                            'complaints',

                            'public'

                        );
                }

                $divisionName = strtolower(

                    trim((string) $division->name)

                );

                $isCommercial = str_contains(

                    $divisionName,

                    'commercial'

                );

                $hasLinkedConsumer =

                    !empty($validated['consumer_id']);

                if ($isCommercial) {

                    $address = $hasLinkedConsumer

                        ? null

                        : ($validated['address'] ?? null);

                    $landmark = null;

                    $latitude = null;

                    $longitude = null;
                } else {

                    $address =

                        $validated['address'] ?? null;

                    $landmark =

                        $validated['landmark'] ?? null;

                    $latitude =

                        $validated['latitude'] ?? null;

                    $longitude =

                        $validated['longitude'] ?? null;
                }

                return Complaint::create([

                    'complaint_no' =>

                    Complaint::generateComplaintNo(),

                    'consumer_id' =>

                    $validated['consumer_id']

                        ?? null,

                    'complainant_name' =>

                    $validated['complainant_name'],

                    'complainant_phone' =>

                    $validated['complainant_phone']

                        ?? null,

                    'division_id' =>

                    $division->id,

                    'complaint_category_id' =>

                    $validated['complaint_category_id'],

                    'status' =>

                    'Pending',

                    'customer_service_id' =>

                    auth()->id(),

                    'description' =>

                    $validated['description'],

                    'address' =>

                    $address,

                    'landmark' =>

                    $landmark,

                    'latitude' =>

                    $latitude,

                    'longitude' =>

                    $longitude,

                    'photo' =>

                    $photoPath,

                ]);
            }

        );

        /*

        |--------------------------------------------------------------------------

        | Automatic AI Analysis

        |--------------------------------------------------------------------------

        |

        | Customer Service-created complaints do not use the consumer-facing

        | "Analyze My Concern" button. We therefore analyze the description

        | automatically after the complaint has been safely created.

        |

        | This gives every new complaint an urgency assessment regardless of

        | whether it came from the Consumer Portal or Customer Service.

        |

        | AI failure must NEVER prevent complaint submission.

        |

        */

        try {

            $analysis = $aiService->analyzeComplaint(

                $validated['description']

            );

            $predictedType = trim(

                (string) (

                    data_get(

                        $analysis,

                        'classification.complaint_type'

                    )

                    ??

                    data_get(

                        $analysis,

                        'complaint_type'

                    )

                    ??

                    ''

                )

            );

            /*

            |--------------------------------------------------------------------------

            | Match AI Prediction To Real Complaint Category

            |--------------------------------------------------------------------------

            */

            $predictedCategory = null;

            if ($predictedType !== '') {

                $predictedCategory =

                    ComplaintCategory::query()

                    ->with('division')

                    ->where('is_active', true)

                    ->whereHas(

                        'division',

                        function ($query) {

                            $query->where(

                                'is_active',

                                true

                            );
                        }

                    )

                    ->whereRaw(

                        'LOWER(TRIM(name)) = ?',

                        [

                            strtolower(

                                $predictedType

                            ),

                        ]

                    )

                    ->first();
            }

            /*

            |--------------------------------------------------------------------------

            | Save AI Audit + Urgency

            |--------------------------------------------------------------------------

            |

            | consumer_accepted = NULL is intentional here.

            |

            | It means the complaint was analyzed automatically and the person

            | submitting it did not interact with a consumer-facing AI

            | recommendation.

            |

            */

            $complaint->aiAnalysis()->create([

                'predicted_category_id' =>

                $predictedCategory?->id,

                'predicted_type' =>

                $predictedType !== ''

                    ? $predictedType

                    : null,

                'confidence' =>

                data_get(

                    $analysis,

                    'classification.confidence'

                ),

                'confidence_level' =>

                data_get(

                    $analysis,

                    'classification.confidence_level'

                ),

                'confidence_gap' =>

                data_get(

                    $analysis,

                    'classification.confidence_gap'

                ),

                'ambiguous' =>

                (bool) data_get(

                    $analysis,

                    'classification.ambiguous',

                    false

                ),

                'urgency_level' =>

                data_get(

                    $analysis,

                    'urgency.level'

                ),

                'urgency_score' =>

                data_get(

                    $analysis,

                    'urgency.score'

                ),

                'signals' =>

                data_get(

                    $analysis,

                    'operational_analysis.signals',

                    []

                ),

                'evidence' =>

                data_get(

                    $analysis,

                    'operational_analysis.evidence',

                    []

                ),

                'review_reasons' =>

                data_get(

                    $analysis,

                    'review\.reasons',

                    []

                ),

                'verification_questions' =>

                data_get(

                    $analysis,

                    'review\.verification_questions',

                    []

                ),

                'consumer_accepted' =>

                null,

                'consumer_category_id' =>

                $validated['complaint_category_id'],

                'verified_category_id' =>

                null,

                'final_category_id' =>

                $validated['complaint_category_id'],

                'raw_analysis' =>

                $analysis,

                'analyzed_at' =>

                now(),

            ]);
        } catch (Throwable $exception) {

            /*

            |--------------------------------------------------------------------------

            | Fail Gracefully

            |--------------------------------------------------------------------------

            |

            | The complaint is already valid and saved. If FastAPI is offline

            | or analysis fails, keep the complaint and show "Not Assessed"

            | until analysis can be performed later.

            |

            */

            Log::warning(

                'Automatic AI analysis failed for Customer Service complaint.',

                [

                    'complaint_id' =>

                    $complaint->id,

                    'complaint_no' =>

                    $complaint->complaint_no,

                    'error' =>

                    $exception->getMessage(),

                ]

            );
        }

        return redirect()

            ->route(

                'customer-service.complaints.index'

            )

            ->with(

                'success',

                'Complaint submitted successfully.'

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

        $divisions = Division::query()

            ->where('is_active', true)

            ->with([

                'complaintTypes' => function ($query) {

                    $query

                        ->where('is_active', true)

                        ->orderBy('name');
                },

            ])

            ->orderBy('name')

            ->get();

        $similarComplaints = [

            'has_possible_related_complaints' => false,

            'count' => 0,

            'matches' => [],

        ];

        $similarComplaintError = null;

        try {

            $similarComplaints =

                $this->getSimilarComplaints(

                    $complaint,

                    $aiService

                );
        } catch (Throwable $exception) {

            report($exception);

            $similarComplaintError =

                'Related complaint analysis is temporarily unavailable.';
        }

        return view(

            'customer-service.complaints.show',

            compact(

                'complaint',

                'divisions',

                'similarComplaints',

                'similarComplaintError'

            )

        );
    }

    public function edit(Complaint $complaint)

    {

        $this->ensureCustomerServiceCanEdit(

            $complaint

        );

        $consumers = Consumer::query()

            ->with('address')

            ->where('is_active', true)

            ->orderBy('last_name')

            ->orderBy('first_name')

            ->get();

        $divisions = Division::query()

            ->where('is_active', true)

            ->with([

                'complaintTypes' => function ($query) {

                    $query

                        ->where('is_active', true)

                        ->orderBy('name');
                },

            ])

            ->orderBy('name')

            ->get();

        $complaint->load([

            'consumer.address',

            'division',

            'category',

            'technicians',

            'customerService',

            'verifier',

        ]);

        return view(

            'customer-service.complaints.edit',

            compact(

                'complaint',

                'consumers',

                'divisions'

            )

        );
    }

    public function update(

        UpdateComplaintRequest $request,

        Complaint $complaint

    ) {

        $this->ensureCustomerServiceCanEdit(

            $complaint

        );

        $validated = $request->validated();

        $division = Division::query()

            ->where(

                'id',

                $validated['division_id']

            )

            ->where(

                'is_active',

                true

            )

            ->firstOrFail();

        ComplaintCategory::query()

            ->where(

                'id',

                $validated['complaint_category_id']

            )

            ->where(

                'division_id',

                $division->id

            )

            ->where(

                'is_active',

                true

            )

            ->firstOrFail();

        DB::transaction(

            function () use (

                $request,

                $validated,

                $complaint,

                $division

            ) {

                $divisionName = strtolower(

                    trim((string) $division->name)

                );

                $isCommercial = str_contains(

                    $divisionName,

                    'commercial'

                );

                $hasLinkedConsumer = !empty($validated['consumer_id']);

                if ($isCommercial) {

                    $address = $hasLinkedConsumer

                        ? null

                        : ($validated['address'] ?? null);

                    $landmark = null;

                    $latitude = null;

                    $longitude = null;
                } else {

                    $address =

                        $validated['address'] ?? null;

                    $landmark =

                        $validated['landmark'] ?? null;

                    $latitude =

                        $validated['latitude'] ?? null;

                    $longitude =

                        $validated['longitude'] ?? null;
                }

                $data = [

                    'consumer_id' =>

                    $validated['consumer_id']

                        ?? null,

                    'complainant_name' =>

                    $validated['complainant_name'],

                    'complainant_phone' =>

                    $validated['complainant_phone']

                        ?? null,

                    'division_id' =>

                    $division->id,

                    'complaint_category_id' =>

                    $validated['complaint_category_id'],

                    'description' =>

                    $validated['description'],

                    'address' =>

                    $address,

                    'landmark' =>

                    $landmark,

                    'latitude' =>

                    $latitude,

                    'longitude' =>

                    $longitude,

                ];

                if ($request->hasFile('photo')) {

                    if (

                        $complaint->photo &&

                        Storage::disk('public')

                        ->exists($complaint->photo)

                    ) {

                        Storage::disk('public')

                            ->delete($complaint->photo);
                    }

                    $data['photo'] = $request

                        ->file('photo')

                        ->store(

                            'complaints',

                            'public'

                        );
                }

                $complaint->update($data);
            }

        );

        return redirect()

            ->route(

                'customer-service.complaints.show',

                $complaint

            )

            ->with(

                'success',

                'Complaint updated successfully.'

            );
    }

    public function destroy(Complaint $complaint)

    {

        if (

            $complaint->status !== 'Pending' ||

            (int) $complaint->customer_service_id !==

            (int) auth()->id()

        ) {

            abort(

                403,

                'You are not allowed to delete this complaint.'

            );
        }

        DB::transaction(

            function () use ($complaint) {

                if (

                    $complaint->photo &&

                    Storage::disk('public')

                    ->exists($complaint->photo)

                ) {

                    Storage::disk('public')

                        ->delete($complaint->photo);
                }

                $complaint->delete();
            }

        );

        return redirect()

            ->route(

                'customer-service.complaints.index'

            )

            ->with(

                'success',

                'Complaint deleted successfully.'

            );
    }

    public function verify(

        VerifyComplaintRequest $request,

        Complaint $complaint

    ) {

        if ($complaint->status !== 'Pending') {

            return back()->with(

                'error',

                'Only pending complaints can be verified.'

            );
        }

        $validated = $request->validated();

        /*

    |--------------------------------------------------------------------------

    | Validate Selected Division

    |--------------------------------------------------------------------------

    */

        $division = Division::query()

            ->where(

                'id',

                $validated['division_id']

            )

            ->where(

                'is_active',

                true

            )

            ->firstOrFail();

        /*

    |--------------------------------------------------------------------------

    | Validate Selected Complaint Type

    |--------------------------------------------------------------------------

    |

    | This prevents a complaint type from another division from being

    | submitted manually.

    |

    */

        $category = ComplaintCategory::query()

            ->where(

                'id',

                $validated['complaint_category_id']

            )

            ->where(

                'division_id',

                $division->id

            )

            ->where(

                'is_active',

                true

            )

            ->firstOrFail();

        /*

    |--------------------------------------------------------------------------

    | Verify Complaint

    |--------------------------------------------------------------------------

    */

        DB::transaction(

            function () use (

                $complaint,

                $division,

                $category,

                $validated

            ) {

                $verifiedAt = now();

                $verifiedBy = auth()->id();

                /*

            |--------------------------------------------------------------------------

            | Save Customer Service Decision

            |--------------------------------------------------------------------------

            |

            | complaint_category_id now becomes the operational category that

            | the rest of the system will use after CS verification.

            |

            */

                $complaint->update([

                    'division_id' =>

                    $division->id,

                    'complaint_category_id' =>

                    $category->id,

                    'status' =>

                    'Verified',

                    'verified_by' =>

                    $verifiedBy,

                    'verified_at' =>

                    $verifiedAt,

                    'verification_reason' =>

                    $validated['verification_reason'] ?? null,

                ]);

                /*

            |--------------------------------------------------------------------------

            | Save Human-Verified Ground Truth

            |--------------------------------------------------------------------------

            |

            | Only update this if the complaint actually has an AI analysis.

            |

            | We NEVER overwrite:

            | - predicted_category_id

            | - consumer_category_id

            | - raw_analysis

            |

            */

                if ($complaint->aiAnalysis) {

                    $complaint->aiAnalysis->update([

                        'verified_category_id' =>

                        $category->id,

                        'verified_by' =>

                        $verifiedBy,

                        'verified_at' =>

                        $verifiedAt,

                    ]);
                }
            }

        );

        /*

    |--------------------------------------------------------------------------

    | Refresh Division

    |--------------------------------------------------------------------------

    */

        $complaint->refresh();

        $complaint->load('division');

        /*

    |--------------------------------------------------------------------------

    | Preserve Existing Routing Behavior

    |--------------------------------------------------------------------------

    */

        $divisionName = strtolower(

            trim(

                (string) $complaint

                    ->division?->name

            )

        );

        $isCommercial = str_contains(

            $divisionName,

            'commercial'

        );

        return redirect()

            ->route(

                'customer-service.complaints.show',

                $complaint

            )

            ->with(

                'success',

                $isCommercial

                    ? 'Complaint verified successfully and is ready for Customer Service processing.'

                    : 'Complaint verified successfully and is ready for Engineering maintenance management.'

            );
    }

    public function reject(

        RejectComplaintRequest $request,

        Complaint $complaint

    ) {

        if ($complaint->status !== 'Pending') {

            return back()->with(

                'error',

                'Only pending complaints can be rejected.'

            );
        }

        $complaint->update([

            'status' =>

            'Rejected',

            'verified_by' =>

            auth()->id(),

            'verified_at' =>

            now(),

            'verification_reason' =>

            $request->validated()['verification_reason'],

        ]);

        return redirect()

            ->route(

                'customer-service.complaints.show',

                $complaint

            )

            ->with(

                'success',

                'Complaint rejected successfully.'

            );
    }

    public function startCommercialProcessing(Complaint $complaint)
    {
        if (!$this->isCommercialComplaint($complaint)) {
            return back()->with(
                'error',
                'Only Commercial Services complaints can use this action.'
            );
        }

        if ($complaint->status !== 'Verified') {
            return back()->with(
                'error',
                'Only verified Commercial Services complaints can begin initial processing.'
            );
        }

        DB::transaction(function () use ($complaint) {
            $resolution = CommercialResolution::firstOrCreate(
                ['complaint_id' => $complaint->id],
                [
                    'processed_by' => auth()->id(),
                    'started_at' => now(),
                ]
            );

            $updates = [];

            if (!$resolution->processed_by) {
                $updates['processed_by'] = auth()->id();
            }

            if (!$resolution->started_at) {
                $updates['started_at'] = now();
            }

            if (!empty($updates)) {
                $resolution->update($updates);
            }

            $complaint->update([
                'status' => 'CS Processing',
                'completed_at' => null,
            ]);
        });

        return redirect()
            ->route('customer-service.complaints.show', $complaint)
            ->with(
                'success',
                'Initial processing started successfully.'
            );
    }

    public function saveCommercialResolution(
        Request $request,
        Complaint $complaint
    ) {
        if (!$this->isCommercialComplaint($complaint)) {
            return back()->with(
                'error',
                'Only Commercial Services complaints can have initial processing findings.'
            );
        }

        if ($complaint->status !== 'CS Processing') {
            return back()->with(
                'error',
                'The request must be under initial processing before findings can be updated.'
            );
        }

        $validated = $request->validate([
            'findings' => ['nullable', 'string', 'max:5000'],
            'resolution_remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        $resolution = CommercialResolution::firstOrCreate(
            ['complaint_id' => $complaint->id],
            [
                'processed_by' => auth()->id(),
                'started_at' => now(),
            ]
        );

        $resolution->update([
            'findings' => $validated['findings'] ?? null,
            'resolution_remarks' => $validated['resolution_remarks'] ?? null,
        ]);

        return redirect()
            ->route('customer-service.complaints.show', $complaint)
            ->with(
                'success',
                'Findings and resolution/recommendation saved successfully.'
            );
    }

    public function completeCommercialComplaint(
        Request $request,
        Complaint $complaint
    ) {
        if (!$this->isCommercialComplaint($complaint)) {
            return back()->with(
                'error',
                'Only Commercial Services complaints can use this action.'
            );
        }

        if ($complaint->status !== 'CS Processing') {
            return back()->with(
                'error',
                'Only requests under initial processing can be completed.'
            );
        }

        $validated = $request->validate([
            'findings' => ['required', 'string', 'max:5000'],
            'resolution_remarks' => ['required', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($validated, $complaint) {
            $resolution = CommercialResolution::firstOrCreate(
                ['complaint_id' => $complaint->id],
                [
                    'processed_by' => auth()->id(),
                    'started_at' => now(),
                ]
            );

            $resolution->update([
                'findings' => $validated['findings'],
                'resolution_remarks' => $validated['resolution_remarks'],
                'initial_processing_completed_at' => now(),
            ]);
        });

        return redirect()
            ->route('customer-service.complaints.show', $complaint)
            ->with(
                'success',
                'Initial processing completed. You can now close the request or forward it to Maintenance.'
            );
    }

    public function forwardCommercialToMaintenance(Complaint $complaint)
    {
        if (!$this->isCommercialComplaint($complaint)) {
            return back()->with(
                'error',
                'Only Commercial Services complaints can use this action.'
            );
        }

        if ($complaint->status !== 'CS Processing') {
            return back()->with(
                'error',
                'Only requests under initial processing can be forwarded to Maintenance.'
            );
        }

        $complaint->loadMissing('commercialResolution');

        $resolution = $complaint->commercialResolution;

        if (!$resolution || !$resolution->initial_processing_completed_at) {
            return back()->with(
                'error',
                'Complete the initial processing and save the required findings and resolution/recommendation before forwarding this request.'
            );
        }

        if (
            blank($resolution->findings) ||
            blank($resolution->resolution_remarks)
        ) {
            return back()->with(
                'error',
                'Findings and resolution/recommendation are required before forwarding this request.'
            );
        }

        DB::transaction(function () use ($complaint, $resolution) {
            $resolution->update([
                'forwarded_to_maintenance_at' => now(),
                'forwarded_by' => auth()->id(),
                'completed_at' => now(),
            ]);

            $complaint->update([
                'status' => 'For Maintenance',
                'completed_at' => null,
            ]);
        });

        return redirect()
            ->route('customer-service.complaints.show', $complaint)
            ->with(
                'success',
                'Request forwarded to Maintenance successfully and is ready for plumber assignment.'
            );
    }

    public function closeCommercialComplaint(Complaint $complaint)
    {
        if (!$this->isCommercialComplaint($complaint)) {
            return back()->with(
                'error',
                'Only Commercial Services complaints can use this action.'
            );
        }

        if ($complaint->status !== 'CS Processing') {
            return back()->with(
                'error',
                'Only requests under initial processing can be closed by Customer Service.'
            );
        }

        $complaint->loadMissing('commercialResolution');

        $resolution = $complaint->commercialResolution;

        if (!$resolution || !$resolution->initial_processing_completed_at) {
            return back()->with(
                'error',
                'Complete the initial processing before closing this request.'
            );
        }

        if (
            blank($resolution->findings) ||
            blank($resolution->resolution_remarks)
        ) {
            return back()->with(
                'error',
                'Findings and resolution/recommendation are required before closing this request.'
            );
        }

        DB::transaction(function () use ($complaint, $resolution) {
            $resolution->update([
                'completed_at' => now(),
            ]);

            $complaint->update([
                'status' => 'Closed',
                'completed_at' => now(),
            ]);
        });

        return redirect()
            ->route('customer-service.complaints.show', $complaint)
            ->with(
                'success',
                'Request closed successfully. No maintenance assignment is required.'
            );
    }

    private function isCommercialComplaint(

        Complaint $complaint

    ): bool {

        $complaint->loadMissing('division');

        $divisionName = strtolower(

            trim(

                (string) $complaint->division?->name

            )

        );

        return str_contains(

            $divisionName,

            'commercial'

        );
    }

    private function ensureCustomerServiceCanEdit(

        Complaint $complaint

    ): void {

        if ($complaint->status !== 'Pending') {

            abort(

                403,

                'Only pending complaints can be edited.'

            );
        }

        if (

            !$complaint->customer_service_id ||

            (int) $complaint->customer_service_id !==

            (int) auth()->id()

        ) {

            abort(

                403,

                'Only the Customer Service employee who created this complaint can edit it.'

            );
        }
    }

    private function getSimilarComplaints(

        Complaint $complaint,

        AIService $aiService

    ): array {

        if (empty($complaint->description)) {
            return [
                'has_possible_related_complaints' => false,
                'count' => 0,
                'matches' => [],
            ];
        }

        $candidateComplaints =

            Complaint::query()

            ->with([

                'consumer.address',

                'division',

            ])

            ->where(

                'id',

                '!=',

                $complaint->id

            )

            ->whereNotIn(

                'status',

                [

                    'Closed',

                    'Rejected',

                ]

            )

            ->whereBetween(

                'created_at',

                [

                    $complaint

                        ->created_at

                        ->copy()

                        ->subDays(7),

                    $complaint

                        ->created_at

                        ->copy()

                        ->addDays(7),

                ]

            )

            ->orderByDesc(

                'created_at'

            )

            ->limit(50)

            ->get([

                'id',

                'complaint_no',

                'consumer_id',

                'description',

                'division_id',

                'complaint_category_id',

                'status',

                'latitude',

                'longitude',

                'created_at',

            ]);

        if (

            $candidateComplaints->isEmpty()

        ) {

            return [

                'has_possible_related_complaints' => false,

                'count' => 0,

                'matches' => [],

            ];
        }

        $complaintPayload =

            $this->buildSimilarityPayload(

                $complaint,

                0

            );

        $candidatePayloads =

            $candidateComplaints

            ->map(

                function (

                    Complaint $candidate

                ) use ($complaint) {

                    $hoursDifference =

                        abs(

                            $complaint

                                ->created_at

                                ->diffInMinutes(

                                    $candidate

                                        ->created_at,

                                    false

                                )

                        ) / 60;

                    return $this

                        ->buildSimilarityPayload(

                            $candidate,

                            $hoursDifference

                        );
                }

            )

            ->values()

            ->all();

        return $aiService

            ->findSimilarComplaints(

                $complaintPayload,

                $candidatePayloads,

                5

            );
    }

    private function buildSimilarityPayload(
        Complaint $complaint,
        float $hoursDifference
    ): array {
        $coordinates =
            $this->resolveComplaintCoordinates(
                $complaint
            );

        return [
            'id' =>
            (int) $complaint->id,

            'complaint_no' =>
            (string) $complaint->complaint_no,

            'consumer_id' =>
            $complaint->consumer_id !== null
                ? (int) $complaint->consumer_id
                : null,

            'description' =>
            (string) $complaint->description,

            'division_id' =>
            $complaint->division_id !== null
                ? (int) $complaint->division_id
                : null,

            'complaint_category_id' =>
            $complaint->complaint_category_id !== null
                ? (int) $complaint->complaint_category_id
                : null,

            'status' =>
            $complaint->status !== null
                ? (string) $complaint->status
                : null,

            'latitude' =>
            $coordinates['latitude'],

            'longitude' =>
            $coordinates['longitude'],

            'hours_difference' =>
            round(
                $hoursDifference,
                2
            ),
        ];
    }

    private function resolveComplaintCoordinates(

        Complaint $complaint

    ): array {

        $complaint->loadMissing([

            'division',

            'consumer.address',

        ]);

        $divisionName = strtolower(

            trim(

                (string) $complaint

                    ->division?->name

            )

        );

        $isCommercial = str_contains(

            $divisionName,

            'commercial'

        );

        if ($isCommercial) {

            $consumerAddress =

                $complaint

                ->consumer?->address;

            if (

                $consumerAddress &&

                $consumerAddress->latitude !== null &&

                $consumerAddress->longitude !== null

            ) {

                return [

                    'latitude' =>

                    (float) $consumerAddress->latitude,

                    'longitude' =>

                    (float) $consumerAddress->longitude,

                ];
            }

            return [

                'latitude' => null,

                'longitude' => null,

            ];
        }

        return [

            'latitude' =>

            $complaint->latitude !== null

                ? (float) $complaint->latitude

                : null,

            'longitude' =>

            $complaint->longitude !== null

                ? (float) $complaint->longitude

                : null,

        ];
    }

    /**

     * Apply Customer Service complaint list filters.

     */

    private function applyComplaintIndexFilters(

        Builder $query,

        Request $request

    ): void {

        if ($request->filled('search')) {

            $search = trim($request->search);

            $normalizedSearch = strtolower($search);

            $workflowSearchStatus = match ($normalizedSearch) {
                'pending',
                'submitted' => 'Pending',

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
                        'landmark',
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
                        function (Builder $consumer) use ($search) {
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
                        function (Builder $category) use ($search) {
                            $category
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'code',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    )
                    ->orWhereHas(
                        'division',
                        function (Builder $division) use ($search) {
                            $division->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );

                if ($workflowSearchStatus !== null) {
                    $query->orWhere(
                        'status',
                        $workflowSearchStatus
                    );
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

            $status = $request->status;

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
            } elseif (
                in_array(
                    $status,
                    $allowedStatuses,
                    true
                )
            ) {

                $query->where(
                    'status',
                    $status
                );
            }
        }
        /*

    |--------------------------------------------------------------------------

    | AI Urgency

    |--------------------------------------------------------------------------

    */

        if ($request->filled('urgency')) {

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

                    function (Builder $aiQuery) use ($urgency) {

                        $aiQuery->where(

                            'urgency_level',

                            $urgency

                        );
                    }

                );
            } elseif (

                $urgency === 'not_assessed'

            ) {

                $query->where(

                    function (Builder $urgencyQuery) {

                        $urgencyQuery

                            ->whereDoesntHave(

                                'aiAnalysis'

                            )

                            ->orWhereHas(

                                'aiAnalysis',

                                function (Builder $aiQuery) {

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

        if ($request->filled('date_from')) {

            $query->whereDate(

                'created_at',

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

                'created_at',

                '<=',

                $request->date_to

            );
        }
    }
}
