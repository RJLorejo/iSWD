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

        /*
        |--------------------------------------------------------------------------
        | Remove Photo Inputs From Complaint Data
        |--------------------------------------------------------------------------
        |
        | Photos are now stored in complaint_photos, not complaints.photo.
        |
        */

        unset(
            $validated['photos'],
            $validated['remove_photos']
        );

        /*
        |--------------------------------------------------------------------------
        | Create Complaint
        |--------------------------------------------------------------------------
        */

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

        $analysis = null;
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
        | Update AI Analysis
        |--------------------------------------------------------------------------
        */

        $analysisToken = $request->input(
            'ai_analysis_token'
        );

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

                        if ($predictedCategoryId !== null) {
                            $consumerAccepted =
                                (int) $predictedCategoryId
                                ===
                                (int) $validated['complaint_category_id'];
                        }

                        $aiAnalysisData = [
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
                            $consumerAccepted,

                            'consumer_category_id' =>
                            $validated['complaint_category_id'],

                            'final_category_id' =>
                            $validated['complaint_category_id'],

                            'raw_analysis' =>
                            $analysis,

                            'analyzed_at' =>
                            $analyzedAt,
                        ];

                        if ($complaint->aiAnalysis) {
                            $complaint
                                ->aiAnalysis
                                ->update(
                                    $aiAnalysisData
                                );
                        } else {
                            $complaint
                                ->aiAnalysis()
                                ->create(
                                    $aiAnalysisData
                                );
                        }
                    }
                }
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
                'min:10',
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
        ]);

        return view(
            'consumer.complaints.show',
            compact('complaint')
        );
    }
}
