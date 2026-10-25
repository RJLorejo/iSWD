<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consumer\StoreConsumerComplaintRequest;
use App\Http\Requests\Consumer\UpdateConsumerComplaintRequest;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\Division;
use App\Services\AI\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ComplaintController extends Controller
{
    /**
     * Get authenticated consumer.
     */
    private function consumer()
    {
        $consumer = Auth::user()->consumer;

        abort_unless(
            $consumer,
            403,
            'Consumer profile not found.'
        );

        return $consumer;
    }

    /**
     * Display consumer complaints.
     */
    /**
     * Display consumer complaints.
     */
    public function index(Request $request)
    {
        $consumer = $this->consumer();


        /*
    |--------------------------------------------------------------------------
    | Request Filters
    |--------------------------------------------------------------------------
    */

        $search = trim(
            (string) $request->input(
                'search',
                ''
            )
        );


        $status = trim(
            (string) $request->input(
                'status',
                ''
            )
        );


        $group = trim(
            (string) $request->input(
                'group',
                ''
            )
        );


        /*
    |--------------------------------------------------------------------------
    | Allowed Individual Statuses
    |--------------------------------------------------------------------------
    */

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


        /*
    |--------------------------------------------------------------------------
    | Dashboard Status Groups
    |--------------------------------------------------------------------------
    */

        $statusGroups = [

            'pending' => [
                'Pending',
                'Verified',
            ],

            'active' => [
                'CS Processing',
                'For Maintenance',
                'Assigned',
                'In Progress',
            ],

            'completed' => [
                'Completed',
                'Closed',
            ],

        ];


        /*
    |--------------------------------------------------------------------------
    | Complaint Query
    |--------------------------------------------------------------------------
    */

        $complaints = Complaint::query()

            ->with([
                'division',
                'category',
                'technicians',
            ])

            ->where(
                'consumer_id',
                $consumer->id
            )


            /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

            ->when(
                $search !== '',
                function ($query) use ($search) {

                    $query->where(
                        function ($searchQuery) use ($search) {

                            $searchQuery

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

                                ->orWhereHas(
                                    'category',
                                    function ($categoryQuery) use ($search) {

                                        $categoryQuery->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                )

                                ->orWhereHas(
                                    'division',
                                    function ($divisionQuery) use ($search) {

                                        $divisionQuery->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                );
                        }
                    );
                }
            )


            /*
        |--------------------------------------------------------------------------
        | Dashboard Group Filter
        |--------------------------------------------------------------------------
        */

            ->when(
                isset(
                    $statusGroups[$group]
                ),
                function ($query) use (
                    $statusGroups,
                    $group
                ) {

                    $query->whereIn(
                        'status',
                        $statusGroups[$group]
                    );
                }
            )


            /*
        |--------------------------------------------------------------------------
        | Individual Status Filter
        |--------------------------------------------------------------------------
        */

            ->when(
                !isset(
                    $statusGroups[$group]
                )
                    &&
                    in_array(
                        $status,
                        $allowedStatuses,
                        true
                    ),
                function ($query) use ($status) {

                    $query->where(
                        'status',
                        $status
                    );
                }
            )


            /*
        |--------------------------------------------------------------------------
        | Latest First
        |--------------------------------------------------------------------------
        */

            ->orderByDesc('created_at')
            ->orderByDesc('id')

            ->paginate(10)

            ->withQueryString();


        /*
    |--------------------------------------------------------------------------
    | Complaints View
    |--------------------------------------------------------------------------
    */

        return view(
            'consumer.complaints.index',
            compact(
                'complaints',
                'search',
                'status',
                'group',
                'allowedStatuses'
            )
        );
    }

    /**
     * Show complaint form.
     */
    public function create()
    {
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
            'consumer.complaints.create',
            compact('divisions')
        );
    }

    /**
     * Store consumer complaint.
     */
    public function store(StoreConsumerComplaintRequest $request)
    {
        $consumer = $this->consumer();

        $validated = $request->validated();

        $division = Division::query()
            ->where('id', $validated['division_id'])
            ->where('is_active', true)
            ->firstOrFail();

        ComplaintCategory::query()
            ->where('id', $validated['complaint_category_id'])
            ->where('division_id', $division->id)
            ->where('is_active', true)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Consumer Information
        |--------------------------------------------------------------------------
        */

        $validated['consumer_id'] = $consumer->id;
        $validated['complainant_name'] = $consumer->full_name;
        $validated['complainant_phone'] = $consumer->phone;

        /*
        |--------------------------------------------------------------------------
        | Complaint Number
        |--------------------------------------------------------------------------
        */

        $validated['complaint_no'] =
            Complaint::generateComplaintNo();

        /*
        |--------------------------------------------------------------------------
        | Initial Status
        |--------------------------------------------------------------------------
        */

        $validated['status'] = 'Pending';

        /*
        |--------------------------------------------------------------------------
        | Commercial Service Location
        |--------------------------------------------------------------------------
        |
        | Commercial concerns use the registered consumer account address.
        |
        */

        if ($division->name === 'Commercial Services') {
            $validated['address'] = null;
            $validated['landmark'] = null;
            $validated['latitude'] = null;
            $validated['longitude'] = null;
        }


        unset(
            $validated['photos'],
            $validated['remove_photos']
        );

        /*
|--------------------------------------------------------------------------
| Validate Complaint Description With AI
|--------------------------------------------------------------------------
|
| This must run BEFORE Complaint::create().
| Invalid descriptions must never be saved.
|
*/

        $preflightAnalysis = null;

        try {
            $aiService = app(AIService::class);

            $preflightAnalysis = $aiService->analyzeComplaint(
                $validated['description']
            );
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'description' =>
                    'We could not validate your description because the AI service is unavailable. Please try again shortly.',
                ]);
        }

        if (
            data_get($preflightAnalysis, 'valid_complaint') !== true
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'description' => data_get(
                        $preflightAnalysis,
                        'message',
                        'Please describe a valid water-service concern.'
                    ),
                ]);
        }

        $complaint = Complaint::create($validated);

        /*
        |--------------------------------------------------------------------------
        | Store Supporting Photos
        |--------------------------------------------------------------------------
        */

        foreach ($request->file('photos', []) as $photo) {
            $path = $photo->store(
                'complaints',
                'public'
            );

            $complaint->photos()->create([
                'photo' => $path,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | AI Analysis
        |--------------------------------------------------------------------------
        */

        $analysisToken = $request->input(
            'ai_analysis_token'
        );

        $analysis = $preflightAnalysis;
        $predictedCategoryId = null;
        $analyzedAt = now();
        $consumerUsedAiAssistance = false;

        /*
        |--------------------------------------------------------------------------
        | Use Trusted AI Analysis From Session
        |--------------------------------------------------------------------------
        */

        if ($analysisToken) {
            $sessionKey =
                "complaint_ai_analysis.{$analysisToken}";

            $storedAiAnalysis =
                $request->session()->pull(
                    $sessionKey
                );

            if ($storedAiAnalysis) {
                $sameConsumer =
                    (int) data_get(
                        $storedAiAnalysis,
                        'consumer_id'
                    )
                    ===
                    (int) $consumer->id;

                $currentDescriptionHash = hash(
                    'sha256',
                    trim($validated['description'])
                );

                $storedDescriptionHash =
                    (string) data_get(
                        $storedAiAnalysis,
                        'description_hash',
                        ''
                    );

                $sameDescription =
                    $storedDescriptionHash !== ''
                    &&
                    hash_equals(
                        $storedDescriptionHash,
                        $currentDescriptionHash
                    );

                if (
                    $sameConsumer
                    &&
                    $sameDescription
                ) {
                    $analysis = data_get(
                        $storedAiAnalysis,
                        'analysis',
                        []
                    );

                    $predictedCategoryId =
                        data_get(
                            $storedAiAnalysis,
                            'predicted_category_id'
                        );

                    $analyzedAt =
                        data_get(
                            $storedAiAnalysis,
                            'analyzed_at'
                        )
                        ?? now();

                    $consumerUsedAiAssistance = true;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Server-Side AI Analysis Fallback
        |--------------------------------------------------------------------------
        */

        if (
            empty($analysis)
        ) {
            try {
                $aiService = app(
                    AIService::class
                );

                $analysis =
                    $aiService->analyzeComplaint(
                        $validated['description']
                    );

                $predictedTypeForMatching = trim(
                    (string) (
                        data_get(
                            $analysis,
                            'classification.complaint_type'
                        )
                        ?? ''
                    )
                );

                if (
                    $predictedTypeForMatching !== ''
                ) {
                    $matchedCategory =
                        ComplaintCategory::query()
                        ->where(
                            'is_active',
                            true
                        )
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
                                    $predictedTypeForMatching
                                ),
                            ]
                        )
                        ->first();

                    $predictedCategoryId =
                        $matchedCategory?->id;
                }

                $analyzedAt = now();
            } catch (Throwable $exception) {
                report($exception);

                $analysis = null;
                $predictedCategoryId = null;
            }
        }


        /*
|--------------------------------------------------------------------------
| Match Predicted Type To Complaint Category
|--------------------------------------------------------------------------
*/

        if (
            is_array($analysis)
            && !empty($analysis)
            && $predictedCategoryId === null
        ) {
            $predictedTypeForMatching = trim(
                (string) data_get(
                    $analysis,
                    'classification.complaint_type',
                    ''
                )
            );

            if ($predictedTypeForMatching !== '') {
                $matchedCategory = ComplaintCategory::query()
                    ->where('is_active', true)
                    ->whereHas('division', function ($query) {
                        $query->where('is_active', true);
                    })
                    ->whereRaw(
                        'LOWER(TRIM(name)) = ?',
                        [
                            strtolower($predictedTypeForMatching),
                        ]
                    )
                    ->first();

                $predictedCategoryId = $matchedCategory?->id;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Save AI Analysis
        |--------------------------------------------------------------------------
        */

        if (
            is_array($analysis)
            &&
            !empty($analysis)
        ) {
            $predictedType =
                data_get(
                    $analysis,
                    'classification.complaint_type'
                );

            $confidence =
                data_get(
                    $analysis,
                    'classification.confidence'
                );

            $confidenceLevel =
                data_get(
                    $analysis,
                    'classification.confidence_level'
                );

            $confidenceGap =
                data_get(
                    $analysis,
                    'classification.confidence_gap'
                );

            $ambiguous =
                (bool) data_get(
                    $analysis,
                    'classification.ambiguous',
                    false
                );

            $consumerAccepted = null;

            if (
                $consumerUsedAiAssistance
                &&
                $predictedCategoryId !== null
            ) {
                $consumerAccepted =
                    (int) $predictedCategoryId
                    ===
                    (int) $validated['complaint_category_id'];
            }

            $complaint->aiAnalysis()->create([
                'predicted_category_id' =>
                $predictedCategoryId,

                'predicted_type' =>
                $predictedType,

                'confidence' =>
                $confidence,

                'confidence_level' =>
                $confidenceLevel,

                'confidence_gap' =>
                $confidenceGap,

                'ambiguous' =>
                $ambiguous,

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
                    'review.reasons',
                    []
                ),

                'verification_questions' =>
                data_get(
                    $analysis,
                    'review.verification_questions',
                    []
                ),

                'consumer_accepted' =>
                $consumerAccepted,

                'consumer_category_id' =>
                $validated['complaint_category_id'],

                'final_category_id' =>
                $validated['complaint_category_id'],

                'raw_analysis' =>
                $analysis,

                'analyzed_at' =>
                $analyzedAt,
            ]);
        }

        return redirect()
            ->route(
                'consumer.complaints.show',
                $complaint
            )
            ->with(
                'success',
                'Your concern has been submitted successfully. Customer Service will review it shortly.'
            );
    }

    /**
     * Show complaint edit form.
     *
     * Consumers can only edit Pending complaints.
     */
    public function edit(Complaint $complaint)
    {
        $consumer = $this->consumer();

        abort_unless(
            (int) $complaint->consumer_id === (int) $consumer->id,
            403
        );

        if ($complaint->status !== 'Pending') {
            return redirect()
                ->route(
                    'consumer.complaints.show',
                    $complaint
                )
                ->with(
                    'error',
                    'This complaint can no longer be edited because Customer Service has already started processing it.'
                );
        }

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
            'division',
            'category',
            'photos',
        ]);

        return view(
            'consumer.complaints.edit',
            compact(
                'complaint',
                'divisions'
            )
        );
    }

    /**
     * Update a pending consumer complaint.
     */
    public function update(
        UpdateConsumerComplaintRequest $request,
        Complaint $complaint
    ) {
        $consumer = $this->consumer();

        abort_unless(
            (int) $complaint->consumer_id === (int) $consumer->id,
            403
        );

        if ($complaint->status !== 'Pending') {
            return redirect()
                ->route(
                    'consumer.complaints.show',
                    $complaint
                )
                ->with(
                    'error',
                    'This complaint can no longer be edited because it is already being processed.'
                );
        }

        $validated = $request->validated();

        $division = Division::query()
            ->where('id', $validated['division_id'])
            ->where('is_active', true)
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
        | Commercial Service Location
        |--------------------------------------------------------------------------
        */

        if ($division->name === 'Commercial Services') {
            $validated['address'] = null;
            $validated['landmark'] = null;
            $validated['latitude'] = null;
            $validated['longitude'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Final Photo Count
        |--------------------------------------------------------------------------
        */

        $complaint->load('photos');

        $removePhotoIds = collect(
            $request->input('remove_photos', [])
        )
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $existingPhotoCount =
            $complaint->photos->count();

        $removedPhotoCount =
            $complaint->photos
            ->whereIn(
                'id',
                $removePhotoIds->all()
            )
            ->count();

        $newPhotoCount =
            count(
                $request->file(
                    'photos',
                    []
                )
            );

        $finalPhotoCount =
            $existingPhotoCount
            - $removedPhotoCount
            + $newPhotoCount;

        if ($finalPhotoCount < 1) {
            return back()
                ->withInput()
                ->withErrors([
                    'photos' =>
                    'At least one supporting photo is required.',
                ]);
        }

        if ($finalPhotoCount > 5) {
            return back()
                ->withInput()
                ->withErrors([
                    'photos' =>
                    'You may keep or upload a maximum of 5 supporting photos.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Remove Photo Inputs From Complaint Data
        |--------------------------------------------------------------------------
        */

        unset(
            $validated['photos'],
            $validated['remove_photos']
        );


        /*
|--------------------------------------------------------------------------
| Reanalyze Changed Complaint Description Before Saving
|--------------------------------------------------------------------------
*/

        $descriptionChanged = trim(
            (string) $complaint->description
        ) !== trim(
            (string) $validated['description']
        );

        $freshAnalysis = null;
        $freshPredictedCategoryId = null;
        $freshAnalyzedAt = null;

        if ($descriptionChanged) {
            try {
                $aiService = app(AIService::class);

                $freshAnalysis = $aiService->analyzeComplaint(
                    $validated['description']
                );

                if (
                    data_get($freshAnalysis, 'valid_complaint') !== true
                ) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'description' => data_get(
                                $freshAnalysis,
                                'message',
                                'Please describe a valid water-service concern.'
                            ),
                        ]);
                }

                $predictedType = trim(
                    (string) data_get(
                        $freshAnalysis,
                        'classification.complaint_type',
                        ''
                    )
                );

                if ($predictedType !== '') {
                    $matchedCategory = ComplaintCategory::query()
                        ->where('is_active', true)
                        ->whereHas('division', function ($query) {
                            $query->where('is_active', true);
                        })
                        ->whereRaw(
                            'LOWER(TRIM(name)) = ?',
                            [strtolower($predictedType)]
                        )
                        ->first();

                    $freshPredictedCategoryId = $matchedCategory?->id;
                }

                $freshAnalyzedAt = now();
            } catch (Throwable $exception) {
                report($exception);

                return back()
                    ->withInput()
                    ->withErrors([
                        'description' =>
                        'We could not reanalyze your updated description because the AI service is unavailable. Your complaint was not updated. Please try again shortly.',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Update Complaint
        |--------------------------------------------------------------------------
        */

        $complaint->update($validated);

        /*
        |--------------------------------------------------------------------------
        | Delete Removed Existing Photos
        |--------------------------------------------------------------------------
        */

        if ($removePhotoIds->isNotEmpty()) {
            $photosToRemove =
                $complaint->photos()
                ->whereIn(
                    'id',
                    $removePhotoIds->all()
                )
                ->get();

            foreach ($photosToRemove as $photo) {
                if ($photo->photo) {
                    Storage::disk('public')
                        ->delete($photo->photo);
                }

                $photo->delete();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Store New Supporting Photos
        |--------------------------------------------------------------------------
        */

        foreach ($request->file('photos', []) as $photo) {
            $path = $photo->store(
                'complaints',
                'public'
            );

            $complaint->photos()->create([
                'photo' => $path,
            ]);
        }


        /*
|--------------------------------------------------------------------------
| Update AI Analysis For Changed Description
|--------------------------------------------------------------------------
|
| Always use the fresh server-side result when the description changes.
| Do not depend on the optional browser AI analysis token.
|
*/

        if (
            $descriptionChanged
            && is_array($freshAnalysis)
            && !empty($freshAnalysis)
        ) {
            $predictedType = data_get(
                $freshAnalysis,
                'classification.complaint_type'
            );

            $confidence = data_get(
                $freshAnalysis,
                'classification.confidence'
            );

            $confidenceLevel = data_get(
                $freshAnalysis,
                'classification.confidence_level'
            );

            $confidenceGap = data_get(
                $freshAnalysis,
                'classification.confidence_gap'
            );

            $ambiguous = (bool) data_get(
                $freshAnalysis,
                'classification.ambiguous',
                false
            );

            $consumerAccepted = null;

            if ($freshPredictedCategoryId !== null) {
                $consumerAccepted =
                    (int) $freshPredictedCategoryId
                    === (int) $validated['complaint_category_id'];
            }

            $aiAnalysisData = [
                'predicted_category_id' =>
                $freshPredictedCategoryId,

                'predicted_type' =>
                $predictedType,

                'confidence' =>
                $confidence,

                'confidence_level' =>
                $confidenceLevel,

                'confidence_gap' =>
                $confidenceGap,

                'ambiguous' =>
                $ambiguous,

                'urgency_level' => data_get(
                    $freshAnalysis,
                    'urgency.level'
                ),

                'urgency_score' => data_get(
                    $freshAnalysis,
                    'urgency.score'
                ),

                'signals' => data_get(
                    $freshAnalysis,
                    'operational_analysis.signals',
                    []
                ),

                'evidence' => data_get(
                    $freshAnalysis,
                    'operational_analysis.evidence',
                    []
                ),

                'review_reasons' => data_get(
                    $freshAnalysis,
                    'review.reasons',
                    []
                ),

                'verification_questions' => data_get(
                    $freshAnalysis,
                    'review.verification_questions',
                    []
                ),

                'consumer_accepted' =>
                $consumerAccepted,

                'consumer_category_id' =>
                $validated['complaint_category_id'],

                'final_category_id' =>
                $validated['complaint_category_id'],

                'raw_analysis' =>
                $freshAnalysis,

                'analyzed_at' =>
                $freshAnalyzedAt,
            ];

            // Reload the relationship so we use the current database record.
            $existingAnalysis = $complaint->aiAnalysis()->first();

            if ($existingAnalysis) {
                $existingAnalysis->update($aiAnalysisData);
            } else {
                $complaint->aiAnalysis()->create($aiAnalysisData);
            }
        }


        return redirect()
            ->route(
                'consumer.complaints.show',
                $complaint
            )
            ->with(
                'success',
                'Your complaint has been updated successfully.'
            );
    }

    /**
     * Analyze complaint description using AI.
     */
    public function analyze(
        Request $request,
        AIService $aiService
    ): JsonResponse {
        $consumer = $this->consumer();

        $validated = $request->validate([
            'description' => [
                'required',
                'string',
                'min:1',
                'max:5000',
            ],
        ]);

        try {
            /*
            |--------------------------------------------------------------------------
            | Analyze Complaint
            |--------------------------------------------------------------------------
            */

            $analysis = $aiService->analyzeComplaint(
                $validated['description']
            );


            if (
                data_get($analysis, 'valid_complaint') !== true
            ) {
                return response()->json([
                    'success' => false,
                    'valid_complaint' => false,
                    'status' => data_get(
                        $analysis,
                        'status',
                        'invalid_input'
                    ),
                    'message' => data_get(
                        $analysis,
                        'message',
                        'Please describe a valid water-service concern.'
                    ),
                    'matched_terms' => data_get(
                        $analysis,
                        'matched_terms',
                        []
                    ),
                ], 422);
            }


            /*
|--------------------------------------------------------------------------
| Reject Invalid Complaint Descriptions
|--------------------------------------------------------------------------
*/

            if (
                data_get($analysis, 'valid_complaint') !== true
            ) {
                return response()->json([
                    'success' => false,
                    'valid_complaint' => false,
                    'status' => data_get(
                        $analysis,
                        'status',
                        'invalid_input'
                    ),
                    'message' => data_get(
                        $analysis,
                        'message',
                        'Please describe a valid water-service concern.'
                    ),
                    'matched_terms' => data_get(
                        $analysis,
                        'matched_terms',
                        []
                    ),
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Get Predicted Complaint Type
            |--------------------------------------------------------------------------
            */

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

            $category = null;

            if ($predictedType !== '') {
                $category = ComplaintCategory::query()
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
            | Generate Secure AI Analysis Token
            |--------------------------------------------------------------------------
            |
            | The browser receives only this random token.
            | The actual AI analysis stays inside the Laravel session.
            |
            */

            $analysisToken = (string) Str::uuid();

            /*
            |--------------------------------------------------------------------------
            | Store Trusted Analysis In Session
            |--------------------------------------------------------------------------
            */

            $request->session()->put(
                "complaint_ai_analysis.{$analysisToken}",
                [
                    'consumer_id' =>
                    $consumer->id,

                    'description_hash' =>
                    hash(
                        'sha256',
                        trim(
                            $validated['description']
                        )
                    ),

                    'predicted_category_id' =>
                    $category?->id,

                    'analysis' =>
                    $analysis,

                    'analyzed_at' =>
                    now()->toISOString(),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Return Result To Consumer
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'success' => true,

                'analysis_token' =>
                $analysisToken,

                'analysis' =>
                $analysis,

                'matched_category' =>
                $category
                    ? [
                        'id' =>
                        (int) $category->id,

                        'name' =>
                        $category->name,

                        'division_id' =>
                        (int) $category->division_id,

                        'division_name' =>
                        $category
                            ->division
                            ?->name,
                    ]
                    : null,

                'human_confirmation_required' =>
                true,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(
                [
                    'success' => false,

                    'message' =>
                    'AI analysis is temporarily unavailable. You can continue filling out the complaint manually.',
                ],
                503
            );
        }
    }

    /**
     * Display complaint details.
     */
    public function show(Complaint $complaint)
    {
        $consumer = Auth::user()->consumer;

        abort_unless(
            $consumer,
            403
        );

        abort_unless(
            (int) $complaint->consumer_id
                ===
                (int) $consumer->id,
            403
        );

        $complaint->load([
            'consumer',
            'division',
            'category',
            'photos',
            'technicians',
            'customerService',
            'verifier',
            'maintenanceReport',
            'feedback',
            'commercialResolution',
            'aiAnalysis',
        ]);

        return view(
            'consumer.complaints.show',
            compact('complaint')
        );
    }

    /**
     * Return a change signature for the authenticated consumer's complaints.
     * Used by the complaints index auto-refresh.
     */
    public function refreshIndexState(): JsonResponse
    {
        $consumer = $this->consumer();

        $complaints = Complaint::query()
            ->where('consumer_id', $consumer->id)
            ->orderBy('id')
            ->get([
                'id',
                'status',
                'updated_at',
            ]);

        $signatureData = $complaints->map(function ($complaint) {
            return [
                'id' => $complaint->id,
                'status' => $complaint->status,
                'updated_at' => $complaint->updated_at?->toISOString(),
            ];
        });

        return response()->json([
            'signature' => hash(
                'sha256',
                $signatureData->toJson()
            ),
        ]);
    }

    /**
     * Return a change signature for one complaint owned by the consumer.
     * Used by the complaint details auto-refresh.
     */

    public function refreshShowState(Complaint $complaint): JsonResponse
    {
        $consumer = $this->consumer();

        abort_unless(
            (int) $complaint->consumer_id === (int) $consumer->id,
            404
        );

        $complaint->refresh();

        $complaint->load([
            'maintenanceReport',
            'feedback',
            'commercialResolution',
            'technicians',
        ]);

        $resolution = $complaint->commercialResolution;
        $report = $complaint->maintenanceReport;
        $feedback = $complaint->feedback;

        $signatureData = [
            'id' => $complaint->id,
            'status' => $complaint->status,
            'updated_at' => $complaint->updated_at?->toISOString(),
            'verified_at' => $complaint->verified_at?->toISOString(),
            'completed_at' => $complaint->completed_at?->toISOString(),
            'closed_at' => $complaint->closed_at?->toISOString(),

            'commercial_resolution' => [
                'updated_at' => $resolution?->updated_at?->toISOString(),
                'started_at' => $resolution?->started_at?->toISOString(),
                'initial_processing_completed_at' =>
                $resolution?->initial_processing_completed_at?->toISOString(),
                'forwarded_to_maintenance_at' =>
                $resolution?->forwarded_to_maintenance_at?->toISOString(),
                'completed_at' => $resolution?->completed_at?->toISOString(),
            ],

            'maintenance_report' => [
                'updated_at' => $report?->updated_at?->toISOString(),
                'status' => $report?->status,
            ],

            'feedback' => [
                'updated_at' => $feedback?->updated_at?->toISOString(),
            ],

            'technicians' => $complaint->technicians
                ->map(function ($technician) {
                    return [
                        'id' => $technician->id,
                        'assigned_at' => $technician->pivot?->assigned_at,
                        'started_at' => $technician->pivot?->started_at,
                        'completed_at' => $technician->pivot?->completed_at,
                        'status' => $technician->pivot?->status,
                    ];
                })
                ->sortBy('id')
                ->values()
                ->all(),
        ];

        return response()
            ->json([
                'signature' => hash(
                    'sha256',
                    json_encode(
                        $signatureData,
                        JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
                    )
                ),
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}
