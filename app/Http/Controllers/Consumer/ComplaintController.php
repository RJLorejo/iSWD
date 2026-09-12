<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consumer\StoreConsumerComplaintRequest;
use App\Http\Requests\Consumer\UpdateConsumerComplaintRequest;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
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
        $categories = ComplaintCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'consumer.complaints.create',
            compact('categories')
        );
    }


    /**
     * Store consumer complaint.
     */
    public function store(StoreConsumerComplaintRequest $request)
    {
        $consumer = $this->consumer();

        $validated = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Verify Category
        |--------------------------------------------------------------------------
        */

        ComplaintCategory::query()
            ->where('id', $validated['complaint_category_id'])
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
        | Photo
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request
                ->file('photo')
                ->store('complaints', 'public');
        }


        /*
        |--------------------------------------------------------------------------
        | Create Complaint
        |--------------------------------------------------------------------------
        */

        $complaint = Complaint::create($validated);


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $complaint->consumer_id === $consumer->id,
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Only Pending Complaints Can Be Edited
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $categories = ComplaintCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();


        return view(
            'consumer.complaints.edit',
            compact(
                'complaint',
                'categories'
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

        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $complaint->consumer_id === $consumer->id,
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Only Pending Complaints Can Be Updated
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | Verify Active Category
        |--------------------------------------------------------------------------
        */

        ComplaintCategory::query()
            ->where('id', $validated['complaint_category_id'])
            ->where('is_active', true)
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Replace Photo If New Photo Was Uploaded
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('photo')) {

            if ($complaint->photo) {
                Storage::disk('public')
                    ->delete($complaint->photo);
            }

            $validated['photo'] = $request
                ->file('photo')
                ->store('complaints', 'public');
        }


        /*
        |--------------------------------------------------------------------------
        | Update Complaint
        |--------------------------------------------------------------------------
        */

        $complaint->update($validated);


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
     * Display complaint details.
     */
    public function show(Complaint $complaint)
    {
        $consumer = $this->consumer();

        /*
        |--------------------------------------------------------------------------
        | Security
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $complaint->consumer_id === $consumer->id,
            403
        );


        /*
        |--------------------------------------------------------------------------
        | Load Complaint Information
        |--------------------------------------------------------------------------
        |
        | technicians = canonical multi-technician assignment
        |
        */

        $complaint->load([
            'category',
            'technicians',
            'verifier',
            'maintenanceReport',
        ]);


        return view(
            'consumer.complaints.show',
            compact('complaint')
        );
    }
}
