<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\UpdateComplaintRequest;
use App\Http\Requests\VerifyComplaintRequest;
use App\Http\Requests\RejectComplaintRequest;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\Consumer;
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
            'category',
            'technician',
            'customerService',
            'verifier',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'complaint_no',
                    'like',
                    "%{$search}%"
                )

                    ->orWhere(
                        'subject',
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

                    // Walk-in complainant
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

                    // Registered consumer
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
                                    'last_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'consumer_no',
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

                    // Category
                    ->orWhereHas(
                        'category',
                        function ($category) use ($search) {

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
        | Priority Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('priority')) {

            $query->where(
                'priority',
                $request->priority
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

        $totalComplaints = Complaint::count();

        $pendingComplaints = Complaint::where(
            'status',
            'Pending'
        )->count();

        $inProgressComplaints = Complaint::where(
            'status',
            'In Progress'
        )->count();

        $completedComplaints = Complaint::where(
            'status',
            'Completed'
        )->count();

        $criticalComplaints = Complaint::where(
            'priority',
            'Critical'
        )->count();


        return view(
            'customer-service.complaints.index',
            compact(
                'complaints',
                'totalComplaints',
                'pendingComplaints',
                'inProgressComplaints',
                'completedComplaints',
                'criticalComplaints'
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
        $consumers = Consumer::query()
            ->where('is_active', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $categories = ComplaintCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'customer-service.complaints.create',
            compact(
                'consumers',
                'categories'
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
        DB::transaction(function () use ($request) {

            $photoPath = null;

            /*
        |--------------------------------------------------------------------------
        | Upload Photo
        |--------------------------------------------------------------------------
        */

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

            $complainantType = $request->input(
                'complainant_type'
            );

            $consumerId = null;

            $complainantName = null;

            $complainantPhone = null;


            /*
        |--------------------------------------------------------------------------
        | Registered Consumer
        |--------------------------------------------------------------------------
        */

            if ($complainantType === 'registered') {

                $consumerId = $request->input(
                    'consumer_id'
                );
            }


            /*
        |--------------------------------------------------------------------------
        | Walk-in Complainant
        |--------------------------------------------------------------------------
        */

            if ($complainantType === 'walk_in') {

                $complainantName =
                    $request->input(
                        'complainant_name'
                    );

                $complainantPhone =
                    $request->input(
                        'complainant_phone'
                    );
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

                'complaint_category_id' =>
                $request->complaint_category_id,

                'priority' =>
                $request->priority,

                /*
            |--------------------------------------------------------------------------
            | Workflow
            |--------------------------------------------------------------------------
            */

                'status' =>
                'Pending',

                'assigned_to' =>
                null,

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

                'subject' =>
                $request->subject,

                'description' =>
                $request->description,

                /*
            |--------------------------------------------------------------------------
            | Location
            |--------------------------------------------------------------------------
            */

                'address' =>
                $request->address,

                'landmark' =>
                $request->landmark,

                'latitude' =>
                $request->latitude,

                'longitude' =>
                $request->longitude,

                /*
            |--------------------------------------------------------------------------
            | Evidence
            |--------------------------------------------------------------------------
            */

                'photo' =>
                $photoPath,
            ]);
        });


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

    public function show(Complaint $complaint)
    {
        $complaint->load([
            'consumer',
            'category',
            'technician',
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

    public function edit(Complaint $complaint)
    {
        $consumers = Consumer::query()
            ->where('is_active', true)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $categories = ComplaintCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $complaint->load([
            'consumer',
            'category',
            'technician',
            'customerService',
            'verifier',
        ]);

        return view(
            'customer-service.complaints.edit',
            compact(
                'complaint',
                'consumers',
                'categories'
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

        DB::transaction(function () use (
            $request,
            $complaint
        ) {

            /*
        |--------------------------------------------------------------------------
        | Determine Complainant
        |--------------------------------------------------------------------------
        */

            $complainantType =
                $request->input('complainant_type');

            $consumerId = null;

            $complainantName = null;

            $complainantPhone = null;


            /*
        |--------------------------------------------------------------------------
        | Registered Consumer
        |--------------------------------------------------------------------------
        */

            if ($complainantType === 'registered') {

                $consumerId =
                    $request->input('consumer_id');
            }


            /*
        |--------------------------------------------------------------------------
        | Walk-in Complainant
        |--------------------------------------------------------------------------
        */

            if ($complainantType === 'walk_in') {

                $complainantName =
                    $request->input(
                        'complainant_name'
                    );

                $complainantPhone =
                    $request->input(
                        'complainant_phone'
                    );
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

                'complaint_category_id' =>
                $request->complaint_category_id,

                'priority' =>
                $request->priority,

                /*
            |--------------------------------------------------------------------------
            | Complaint Information
            |--------------------------------------------------------------------------
            */

                'subject' =>
                $request->subject,

                'description' =>
                $request->description,

                /*
            |--------------------------------------------------------------------------
            | Location
            |--------------------------------------------------------------------------
            */

                'address' =>
                $request->address,

                'landmark' =>
                $request->landmark,

                'latitude' =>
                $request->latitude,

                'longitude' =>
                $request->longitude,
            ];

            if ($request->hasFile('photo')) {

                if (
                    $complaint->photo &&
                    Storage::disk('public')->exists(
                        $complaint->photo
                    )
                ) {

                    Storage::disk('public')->delete(
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


            $complaint->update($data);
        });


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

    public function destroy(Complaint $complaint)
    {
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

        if ($complaint->status !== 'Pending') {

            return back()->with(
                'error',
                'Only pending complaints can be verified.'
            );
        }


        DB::transaction(function () use (
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
                $request->verification_reason,
            ]);
        });


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

        if ($complaint->status !== 'Pending') {

            return back()->with(
                'error',
                'Only pending complaints can be rejected.'
            );
        }


        DB::transaction(function () use (
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
                $request->verification_reason,
            ]);
        });


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
