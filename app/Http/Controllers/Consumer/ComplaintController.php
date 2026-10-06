<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consumer\StoreConsumerComplaintRequest;
use App\Http\Requests\Consumer\UpdateConsumerComplaintRequest;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\Division;
use Illuminate\Support\Str;
use App\Services\AI\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
    public function index()
    {
        $consumer = $this->consumer();

        $complaints = Complaint::with([
            'division',
            'category',
            'technicians',
        ])
            ->where('consumer_id', $consumer->id)
            ->latest()
            ->paginate(10);

        return view(
            'consumer.complaints.index',
            compact('complaints')
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

        if ($division->name === 'Commercial Services') {
            $validated['address'] = null;
            $validated['landmark'] = null;
            $validated['latitude'] = null;
            $validated['longitude'] = null;
        }


        if ($request->hasFile('photo')) {
            $validated['photo'] = $request
                ->file('photo')
                ->store('complaints', 'public');
        }



        $complaint = Complaint::create($validated);

        $analysisToken = $request->input(
            'ai_analysis_token'
        );

        $analysis = null;

        $predictedCategoryId = null;

        $analyzedAt = now();

        $consumerUsedAiAssistance = false;


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


                $sameDescription =
                    hash_equals(
                        (string) data_get(
                            $storedAiAnalysis,
                            'description_hash',
                            ''
                        ),
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
            $complaint->consumer_id === $consumer->id,
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
            $complaint->consumer_id === $consumer->id,
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

        if ($division->name === 'Commercial Services') {
            $validated['address'] = null;
            $validated['landmark'] = null;
            $validated['latitude'] = null;
            $validated['longitude'] = null;
        }

        if ($request->hasFile('photo')) {

            if ($complaint->photo) {
                Storage::disk('public')
                    ->delete($complaint->photo);
            }

            $validated['photo'] = $request
                ->file('photo')
                ->store(
                    'complaints',
                    'public'
                );
        }

        $complaint->update($validated);

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

                        if (
                            $predictedCategoryId !== null
                        ) {

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
        |
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

                    /*
                | Consumer that requested the analysis
                */

                    'consumer_id' => $consumer->id,


                    /*
                | Original description
                |
                | We save a hash so that if the consumer changes
                | the description after analyzing, we will not
                | attach an outdated AI analysis.
                */

                    'description_hash' => hash(
                        'sha256',
                        trim($validated['description'])
                    ),


                    /*
                | Real matched database category
                */

                    'predicted_category_id' =>
                    $category?->id,


                    /*
                | Complete trusted AI response
                */

                    'analysis' => $analysis,


                    /*
                | Time analysis was performed
                */

                    'analyzed_at' => now()->toISOString(),
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

        abort_unless($consumer, 403);

        abort_unless(
            (int) $complaint->consumer_id === (int) $consumer->id,
            403
        );

        $complaint->load([
            'consumer',
            'division',
            'category',
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
