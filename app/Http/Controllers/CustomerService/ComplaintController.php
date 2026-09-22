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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Complaint::with([
            'consumer',
            'division',
            'category',
            'technicians',
            'customerService',
            'verifier',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
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

                    ->orWhereHas(
                        'consumer',
                        function ($consumerQuery) use ($search) {

                            $consumerQuery
                                ->where(
                                    'first_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'last_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'consumer_no',
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
                    )

                    ->orWhereHas(
                        'category',
                        function ($categoryQuery) use ($search) {

                            $categoryQuery
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
                    );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Division Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('division_id')) {

            $query->where(
                'division_id',
                $request->division_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Complaints
        |--------------------------------------------------------------------------
        */

        $complaints = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Dashboard Statistics
        |--------------------------------------------------------------------------
        */

        $totalComplaints =
            Complaint::count();

        $pendingComplaints =
            Complaint::where(
                'status',
                'Pending'
            )->count();

        $inProgressComplaints =
            Complaint::where(
                'status',
                'In Progress'
            )->count();

        $completedComplaints =
            Complaint::where(
                'status',
                'Completed'
            )->count();


        /*
        |--------------------------------------------------------------------------
        | Divisions For Filters
        |--------------------------------------------------------------------------
        */

        $divisions = Division::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();


        return view(
            'customer-service.complaints.index',
            compact(
                'complaints',
                'totalComplaints',
                'pendingComplaints',
                'inProgressComplaints',
                'completedComplaints',
                'divisions'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        /*
        |--------------------------------------------------------------------------
        | Consumers
        |--------------------------------------------------------------------------
        */

        $consumers = Consumer::query()
            ->where('is_active', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Divisions + Complaint Types
        |--------------------------------------------------------------------------
        */

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


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(StoreComplaintRequest $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validated Data
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validated();


        /*
        |--------------------------------------------------------------------------
        | Verify Division
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
        | Verify Complaint Type Belongs To Division
        |--------------------------------------------------------------------------
        */

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
                $division
            ) {

                /*
                |--------------------------------------------------------------------------
                | Photo
                |--------------------------------------------------------------------------
                */

                $photoPath = null;

                if ($request->hasFile('photo')) {

                    $photoPath = $request
                        ->file('photo')
                        ->store(
                            'complaints',
                            'public'
                        );
                }


                /*
                |--------------------------------------------------------------------------
                | Determine Complainant
                |--------------------------------------------------------------------------
                */

                $complainantType =
                    $validated['complainant_type'];

                $consumerId = null;

                $complainantName = null;

                $complainantPhone = null;


                /*
                |--------------------------------------------------------------------------
                | Registered Consumer
                |--------------------------------------------------------------------------
                */

                if (
                    $complainantType ===
                    'registered'
                ) {

                    $consumerId =
                        $validated['consumer_id'];
                }


                /*
                |--------------------------------------------------------------------------
                | Walk-in Complainant
                |--------------------------------------------------------------------------
                */

                if (
                    $complainantType ===
                    'walk_in'
                ) {

                    $complainantName =
                        $validated['complainant_name'];

                    $complainantPhone =
                        $validated['complainant_phone'] ?? null;
                }


                /*
                |--------------------------------------------------------------------------
                | Create Complaint
                |--------------------------------------------------------------------------
                */

                Complaint::create([

                    /*
                    |--------------------------------------------------------------------------
                    | Complaint Number
                    |--------------------------------------------------------------------------
                    */

                    'complaint_no' =>
                    Complaint::generateComplaintNo(),


                    /*
                    |--------------------------------------------------------------------------
                    | Complainant
                    |--------------------------------------------------------------------------
                    */

                    'consumer_id' =>
                    $consumerId,

                    'complainant_name' =>
                    $complainantName,

                    'complainant_phone' =>
                    $complainantPhone,


                    /*
                    |--------------------------------------------------------------------------
                    | Classification
                    |--------------------------------------------------------------------------
                    */

                    'division_id' =>
                    $division->id,

                    'complaint_category_id' =>
                    $validated['complaint_category_id'],


                    /*
                    |--------------------------------------------------------------------------
                    | Workflow
                    |--------------------------------------------------------------------------
                    */

                    'status' =>
                    'Pending',


                    /*
                    |--------------------------------------------------------------------------
                    | Customer Service
                    |--------------------------------------------------------------------------
                    */

                    'customer_service_id' =>
                    auth()->id(),


                    /*
                    |--------------------------------------------------------------------------
                    | Complaint Information
                    |--------------------------------------------------------------------------
                    */

                    'description' =>
                    $validated['description'],


                    /*
                    |--------------------------------------------------------------------------
                    | Location
                    |--------------------------------------------------------------------------
                    */

                    'address' =>
                    $validated['address'],

                    'landmark' =>
                    $validated['landmark']
                        ?? null,

                    /*
                    | Coordinates remain stored in the database.
                    | They are hidden from the Customer Service UI.
                    */

                    'latitude' =>
                    $validated['latitude']
                        ?? null,

                    'longitude' =>
                    $validated['longitude']
                        ?? null,


                    /*
                    |--------------------------------------------------------------------------
                    | Evidence
                    |--------------------------------------------------------------------------
                    */

                    'photo' =>
                    $photoPath,
                ]);
            }
        );


        return redirect()
            ->route(
                'customer-service.complaints.index'
            )
            ->with(
                'success',
                'Complaint submitted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(
        Complaint $complaint
    ) {
        $complaint->load([
            'consumer',
            'division',
            'category',
            'technicians',
            'customerService',
            'verifier',
            'maintenanceReport',
        ]);


        return view(
            'customer-service.complaints.show',
            compact('complaint')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(
        Complaint $complaint
    ) {
        /*
        |--------------------------------------------------------------------------
        | Consumers
        |--------------------------------------------------------------------------
        */

        $consumers = Consumer::query()
            ->where('is_active', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Divisions + Complaint Types
        |--------------------------------------------------------------------------
        */

        $divisions = Division::query()
            ->where('is_active', true)
            ->with([
                'complaintTypes' =>
                function ($query) {

                    $query
                        ->where(
                            'is_active',
                            true
                        )
                        ->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Complaint Relationships
        |--------------------------------------------------------------------------
        */

        $complaint->load([
            'consumer',
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


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateComplaintRequest $request,
        Complaint $complaint
    ) {
        /*
        |--------------------------------------------------------------------------
        | Validated Data
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validated();


        /*
        |--------------------------------------------------------------------------
        | Verify Division
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
        | Verify Complaint Type Belongs To Division
        |--------------------------------------------------------------------------
        */

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

                /*
                |--------------------------------------------------------------------------
                | Determine Complainant
                |--------------------------------------------------------------------------
                */

                $complainantType =
                    $validated['complainant_type'];

                $consumerId = null;

                $complainantName = null;

                $complainantPhone = null;


                /*
                |--------------------------------------------------------------------------
                | Registered Consumer
                |--------------------------------------------------------------------------
                */

                if (
                    $complainantType ===
                    'registered'
                ) {

                    $consumerId =
                        $validated['consumer_id'];
                }


                /*
                |--------------------------------------------------------------------------
                | Walk-in Complainant
                |--------------------------------------------------------------------------
                */

                if (
                    $complainantType ===
                    'walk_in'
                ) {

                    $complainantName =
                        $validated['complainant_name'];

                    $complainantPhone =
                        $validated['complainant_phone'] ?? null;
                }


                /*
                |--------------------------------------------------------------------------
                | Complaint Data
                |--------------------------------------------------------------------------
                */

                $data = [

                    /*
                    |--------------------------------------------------------------------------
                    | Complainant
                    |--------------------------------------------------------------------------
                    */

                    'consumer_id' =>
                    $consumerId,

                    'complainant_name' =>
                    $complainantName,

                    'complainant_phone' =>
                    $complainantPhone,


                    /*
                    |--------------------------------------------------------------------------
                    | Classification
                    |--------------------------------------------------------------------------
                    */

                    'division_id' =>
                    $division->id,

                    'complaint_category_id' =>
                    $validated['complaint_category_id'],


                    /*
                    |--------------------------------------------------------------------------
                    | Complaint Information
                    |--------------------------------------------------------------------------
                    */

                    'description' =>
                    $validated['description'],


                    /*
                    |--------------------------------------------------------------------------
                    | Location
                    |--------------------------------------------------------------------------
                    */

                    'address' =>
                    $validated['address'],

                    'landmark' =>
                    $validated['landmark']
                        ?? null,

                    'latitude' =>
                    $validated['latitude']
                        ?? null,

                    'longitude' =>
                    $validated['longitude']
                        ?? null,
                ];


                /*
                |--------------------------------------------------------------------------
                | Replace Photo
                |--------------------------------------------------------------------------
                */

                if ($request->hasFile('photo')) {

                    if (
                        $complaint->photo &&
                        Storage::disk('public')
                        ->exists(
                            $complaint->photo
                        )
                    ) {

                        Storage::disk('public')
                            ->delete(
                                $complaint->photo
                            );
                    }


                    $data['photo'] =
                        $request
                        ->file('photo')
                        ->store(
                            'complaints',
                            'public'
                        );
                }


                /*
                |--------------------------------------------------------------------------
                | Update
                |--------------------------------------------------------------------------
                */

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
                'Complaint information updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Complaint $complaint
    ) {
        $complaint->delete();


        return redirect()
            ->route(
                'customer-service.complaints.index'
            )
            ->with(
                'success',
                'Complaint deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFY
    |--------------------------------------------------------------------------
    */

    public function verify(
        VerifyComplaintRequest $request,
        Complaint $complaint
    ) {
        /*
        |--------------------------------------------------------------------------
        | Only Pending Complaints
        |--------------------------------------------------------------------------
        */

        if (
            $complaint->status !==
            'Pending'
        ) {

            return back()->with(
                'error',
                'Only pending complaints can be verified.'
            );
        }


        DB::transaction(
            function () use (
                $request,
                $complaint
            ) {

                $complaint->update([

                    'status' =>
                    'Verified',

                    'verified_by' =>
                    auth()->id(),

                    'verified_at' =>
                    now(),

                    'verification_reason' =>
                    $request
                        ->verification_reason,
                ]);
            }
        );


        return redirect()
            ->route(
                'customer-service.complaints.show',
                $complaint
            )
            ->with(
                'success',
                'Complaint verified successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */

    public function reject(
        RejectComplaintRequest $request,
        Complaint $complaint
    ) {
        /*
        |--------------------------------------------------------------------------
        | Only Pending Complaints
        |--------------------------------------------------------------------------
        */

        if (
            $complaint->status !==
            'Pending'
        ) {

            return back()->with(
                'error',
                'Only pending complaints can be rejected.'
            );
        }


        DB::transaction(
            function () use (
                $request,
                $complaint
            ) {

                $complaint->update([

                    'status' =>
                    'Rejected',

                    'verified_by' =>
                    auth()->id(),

                    'verified_at' =>
                    now(),

                    'verification_reason' =>
                    $request
                        ->verification_reason,
                ]);
            }
        );


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
}
