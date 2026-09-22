Yesterday 11:03 PM
Pasted text(20260917-150702).txt
Document
its ok now, so can we adjust or modify the existing map in reverse geocoding? 
@extends('consumer.layouts.app')

@section('title', 'My Complaints')

@section('content')

    <div class="space-y-6">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>

                <h1 class="text-3xl font-bold text-slate-900">
                    My Complaints
                </h1>

                <p class="mt-1 text-slate-500">
                    View and track your water service complaints.
                </p>

            </div>

            <a href="{{ route('consumer.complaints.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-sky-700 text-white font-semibold hover:bg-sky-800">
                <i class="fas fa-plus"></i>
                Submit Complaint
            </a>

        </div>


        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">

            @forelse($complaints as $complaint)
                <a href="{{ route('consumer.complaints.show', $complaint) }}"
                    class="block p-6 border-b last:border-b-0 hover:bg-slate-50 transition">

                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

                        <div class="flex items-start gap-4">

                            <div
                                class="w-12 h-12 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">
                                <i class="fas fa-droplet"></i>
                            </div>

                            <div>

                                <p class="font-bold text-slate-900">
                                    {{ $complaint->complaint_no }}
                                </p>

                                <h2 class="mt-1 font-semibold text-slate-800">
                                    {{ $complaint->subject }}
                                </h2>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ optional($complaint->category)->category_name ?? 'Water Service Concern' }}
                                </p>

                                <p class="mt-2 text-xs text-slate-400">
                                    Submitted {{ $complaint->created_at->format('M d, Y h:i A') }}
                                </p>

                            </div>

                        </div>


                        <div class="flex items-center gap-4">

                            @php
                                $statusClasses = [
                                    'Pending' => 'bg-amber-100 text-amber-700',
                                    'Verified' => 'bg-blue-100 text-blue-700',
                                    'Assigned' => 'bg-indigo-100 text-indigo-700',
                                    'In Progress' => 'bg-sky-100 text-sky-700',
                                    'Completed' => 'bg-emerald-100 text-emerald-700',
                                    'Closed' => 'bg-slate-100 text-slate-700',
                                    'Rejected' => 'bg-red-100 text-red-700',
                                ];
                            @endphp

                            <span
                                class="inline-flex px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusClasses[$complaint->status] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $complaint->status }}
                            </span>

                            <i class="fas fa-chevron-right text-slate-400"></i>

                        </div>

                    </div>

                </a>

            @empty

                <div class="p-16 text-center">

                    <div class="mx-auto w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
                        <i class="fas fa-file-circle-question text-2xl"></i>
                    </div>

                    <h2 class="mt-5 text-xl font-bold text-slate-900">
                        No complaints found
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        You have not submitted any water service complaints yet.
                    </p>

                    <a href="{{ route('consumer.complaints.create') }}"
                        class="inline-flex items-center gap-2 mt-6 px-5 py-3 rounded-xl bg-sky-700 text-white font-semibold hover:bg-sky-800">
                        <i class="fas fa-plus"></i>
                        Submit Your First Complaint
                    </a>

                </div>
            @endforelse

        </div>


        <div>
            {{ $complaints->links() }}
        </div>

    </div>

@endsection
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


Exactly — your client’s question is valid, and it actually gives us a good reason to redesign the location section.

1. Can reverse geocoding identify the purok/PRK or street?

Sometimes, but not reliably.

Reverse geocoding can return components such as:

house/building number
street
road
barangay
city/municipality
province
country
postal code

But Purok/PRK is a problem. Purok is often a local administrative/community designation that isn't consistently included in OpenStreetMap's address data. So we shouldn't promise that clicking the map will automatically produce:

Purok 3, Rizal Street, Brgy. Poblacion, Sagay City

It might instead return only something like:

Poblacion, Sagay City, Negros Occidental, Philippines

or even just a nearby road/barangay.

Your current address field explicitly asks the consumer for house number, street/purok, and barangay, which is actually useful information that reverse geocoding may not know.

2. And yes — your client's second point is important

They asked:

If the consumer already manually enters their address, what's the purpose of putting a pin on the map?

The answer is:

The address and the map serve two different purposes.

Address = human-readable location

For example:

Purok 3, Rizal Street, Brgy. Poblacion, Sagay City

This is useful for:

staff reading the complaint
contacting the consumer
directions
reports
official records
Map pin = geographic coordinates

The map gives:

Latitude: 10.xxxxxx
Longitude: 123.xxxxxx

This is useful for:

seeing the complaint geographically
locating complaints on a GIS dashboard
identifying clusters of complaints
finding nearby complaints
helping field personnel locate the reported problem
future AI-assisted assignment based on proximity
comparing complaints within the same area

So the map isn't a replacement for the address.

3. But I would change your current UI

Right now your form essentially says:

Enter Address
Enter Landmark
Pin the problem location

That can make the consumer think:

"Why do I have to do both?"

Your current implementation indeed has a separate address input and optional map pin.

I would change it to a location verification workflow.

Better design
WHERE IS THE CONCERN?

Address
┌─────────────────────────────────────────────┐
│ Purok 3, Rizal Street, Brgy. Poblacion     │
│ Sagay City                                  │
└─────────────────────────────────────────────┘

                 OR

📍 Locate this address on the map

┌─────────────────────────────────────────────┐
│                                             │
│              📍                             │
│                                             │
│                 MAP                         │
│                                             │
└─────────────────────────────────────────────┘

[ Use my current location ]

✓ Location pinned
  Poblacion, Sagay City

Drag the pin to the exact location of the concern.

The key is that the map becomes confirmation/precision, not another tedious form field.

4. Even better: make the map help fill the address

This is where reverse geocoding becomes useful.

Suppose the consumer says:

"May tagas sa amon tubo sa Purok 3."

They enter:

Purok 3, Brgy. Poblacion

Then they click the map around their house.

The system can obtain:

Latitude: 10.XXXXXX
Longitude: 123.XXXXXX

Detected:
Poblacion, Sagay City, Negros Occidental

The consumer can then verify the address:

Address
┌───────────────────────────────────────┐
│ Purok 3, Brgy. Poblacion, Sagay City │
└───────────────────────────────────────┘

📍 Map location confirmed

So the consumer supplies the local information that maps may not know, while the map supplies the precise geographic coordinate.

5. What I would recommend for your iSWD system

I would keep both, but give them clearly different purposes.

Feature	Purpose
Address	Human-readable location, including Purok/Street/Barangay
Landmark	Helps field personnel identify the location
Map pin	Exact geographic position
Latitude/Longitude	GIS, nearby complaint detection, mapping
Reverse geocoding	Helps identify/verify barangay, road, city, etc.
Use my location	Quickly places the pin using GPS
And importantly:

Don't make the consumer enter latitude/longitude.

They shouldn't even need to know those values exist.

6. One change I strongly recommend

Instead of:

Pin the problem location — Optional but recommended

I'd change it to:

Confirm the location on the map

And explain:

Your address tells us where the concern is. The map pin helps us identify the exact geographic location for field personnel and mapping.

That directly answers your client's concern.

You could also make the map recommended rather than technically optional for complaints that may require field work.

For example:

📍 Help us locate the concern

Enter your address above, then place the pin
on the map as close as possible to where the
problem occurred.

[ Use my location ]

        MAP

✓ Location selected
The important distinction for your capstone

This also makes your GIS feature much easier to defend during your panel:

The address provides descriptive location information, while the map pin provides geographic coordinates that allow the system to visualize complaints spatially, identify nearby or potentially related complaints, and support field-assignment decisions based on proximity.

That is a much stronger justification than saying "we have a map because reverse geocoding gives an address."

And yes, we can modify your existing code to work exactly this way, including reverse geocoding and a "Detected Location" display while still allowing the consumer to specify their Purok/PRK and street manually.

ok goods now provide a full code of create and edit blade

Yes. Below is a complete replacement for both your consumer complaint create.blade.php and edit.blade.php.

This version keeps your client's requested distinction:

Address → consumer enters Purok/PRK, street, barangay, etc.
Map pin → identifies the geographic location.
Reverse geocoding → shows a readable location detected from the pin.
Use detected address → optional button to copy the detected map address into the address field.
Use my location → GPS places the pin.
Clicking the map or dragging the marker updates the coordinates and detected location.
Existing coordinates are restored when editing.
The consumer can still manually correct the address.
Latitude/longitude remain hidden.

Your existing form already has the address, landmark, map, and hidden coordinate fields, so we're modifying that structure rather than changing your whole complaint workflow.

1. resources/views/consumer/complaints/create.blade.php

Replace your current create.blade.php with this:

@extends('consumer.layouts.app')

@section('title', 'Report a Concern')

@section('content')

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="mb-8">

            <div class="flex items-center gap-3 mb-3">

                <div
                    class="w-12 h-12 rounded-2xl
                    bg-sky-100 text-sky-700
                    flex items-center justify-center
                    text-2xl">

                    <i class="fas fa-exclamation-triangle"></i>

                </div>

                <div>

                    <h1 class="text-2xl font-bold text-slate-800">
                        Report a Concern
                    </h1>

                    <p class="text-sm text-slate-500">
                        Tell Sagay Water District about your concern.
                    </p>

                </div>

            </div>


            <div class="bg-sky-50 border border-sky-100 rounded-2xl p-4">

                <p class="text-sm text-sky-800">

                    <strong>Need help?</strong>
                    Select the category that best describes your concern.
                    You do not need to determine the priority.

                </p>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- VALIDATION ERRORS --}}
        {{-- ========================================================= --}}

        @if ($errors->any())

            <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-5">

                <div class="font-semibold text-red-700 mb-2">
                    Please check the following:
                </div>

                <ul class="list-disc ml-5 text-sm text-red-600 space-y-1">

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- FORM --}}
        {{-- ========================================================= --}}

        <form
            method="POST"
            action="{{ route('consumer.complaints.store') }}"
            enctype="multipart/form-data"
            class="space-y-8">

            @csrf


            {{-- ===================================================== --}}
            {{-- CATEGORY --}}
            {{-- ===================================================== --}}

            <section
                class="bg-white rounded-3xl shadow-sm
                border border-slate-200 p-6 md:p-8">

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-800">
                        What is your concern?
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Select the option that best describes your concern.
                        You do not need to determine the priority.
                    </p>

                </div>


                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    @foreach ($categories as $category)

                        <label class="relative cursor-pointer block">

                            <input
                                type="radio"
                                name="complaint_category_id"
                                value="{{ $category->id }}"
                                class="peer sr-only"
                                {{ old('complaint_category_id') == $category->id ? 'checked' : '' }}
                                required>


                            <div
                                class="
                                    h-full
                                    rounded-2xl
                                    border-2
                                    border-slate-200
                                    bg-white
                                    p-5
                                    transition-all
                                    duration-200

                                    hover:border-sky-300
                                    hover:bg-sky-50/50

                                    peer-checked:border-sky-600
                                    peer-checked:bg-sky-50
                                    peer-checked:shadow-md
                                ">

                                <div class="flex items-start justify-between gap-4">

                                    <div class="flex-1">

                                        <h3
                                            class="
                                                font-semibold
                                                text-slate-800
                                                peer-checked:text-sky-700
                                            ">

                                            {{ $category->name }}

                                        </h3>


                                        @if ($category->description)

                                            <p class="text-sm text-slate-500 mt-2">

                                                {{ $category->description }}

                                            </p>

                                        @endif

                                    </div>


                                    {{-- CUSTOM RADIO --}}

                                    <div
                                        class="
                                            flex-shrink-0
                                            w-6 h-6
                                            rounded-full
                                            border-2
                                            border-slate-300
                                            flex items-center justify-center
                                            transition
                                            peer-checked:border-sky-600
                                        ">

                                        <div
                                            class="
                                                w-3 h-3
                                                rounded-full
                                                bg-sky-600
                                                scale-0
                                                transition-transform
                                                peer-checked:scale-100
                                            ">
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </label>

                    @endforeach

                </div>


                {{-- CATEGORY HELP --}}

                <div
                    id="categoryHelp"
                    class="
                        hidden
                        mt-6
                        rounded-2xl
                        bg-sky-50
                        border
                        border-sky-100
                        p-5
                    ">

                    <div class="flex gap-3">

                        <div
                            class="
                                w-10 h-10
                                flex-shrink-0
                                rounded-xl
                                bg-white
                                flex items-center justify-center
                                text-xl
                                shadow-sm
                            ">

                            <i class="fas fa-info-circle text-sky-600"></i>

                        </div>

                        <div>

                            <h3
                                id="categoryHelpTitle"
                                class="font-semibold text-sky-800">
                            </h3>

                            <p
                                id="categoryHelpText"
                                class="text-sm text-sky-700 mt-1 leading-6">
                            </p>

                        </div>

                    </div>

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- COMPLAINT DETAILS --}}
            {{-- ===================================================== --}}

            <section
                class="bg-white rounded-3xl shadow-sm
                border border-slate-200 p-6 md:p-8">

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-800">
                        Tell us about the problem
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Use simple words. You do not need technical terms.
                    </p>

                </div>


                {{-- SUBJECT --}}

                <div class="mb-6">

                    <label
                        for="subject"
                        class="block text-sm font-semibold text-slate-700 mb-2">

                        Short description
                        <span class="text-red-500">*</span>

                    </label>

                    <input
                        id="subject"
                        name="subject"
                        type="text"
                        value="{{ old('subject') }}"
                        required
                        maxlength="255"
                        placeholder="Example: No water in our house"
                        class="
                            w-full
                            rounded-xl
                            border-slate-300
                            px-4 py-3
                            focus:border-sky-500
                            focus:ring-sky-500
                        ">

                </div>


                {{-- DESCRIPTION --}}

                <div>

                    <label
                        for="description"
                        class="block text-sm font-semibold text-slate-700 mb-2">

                        What happened?
                        <span class="text-red-500">*</span>

                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        required
                        maxlength="5000"
                        placeholder="Example: We have had no water since this morning."
                        class="
                            w-full
                            rounded-xl
                            border-slate-300
                            px-4 py-3
                            focus:border-sky-500
                            focus:ring-sky-500
                        ">{{ old('description') }}</textarea>

                    <p class="text-xs text-slate-400 mt-2">
                        Please avoid passwords, payment details, or other sensitive information.
                    </p>

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- LOCATION --}}
            {{-- ===================================================== --}}

            <section
                class="bg-white rounded-3xl shadow-sm
                border border-slate-200 p-6 md:p-8">

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-800">
                        Where is the concern?
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Enter the address and use the map to identify the geographic location.
                    </p>

                </div>


                {{-- ================================================= --}}
                {{-- ADDRESS --}}
                {{-- ================================================= --}}

                <div class="mb-6">

                    <label
                        for="address"
                        class="block text-sm font-semibold text-slate-700 mb-2">

                        Address
                        <span class="text-red-500">*</span>

                    </label>

                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        required
                        maxlength="500"
                        placeholder="House number, street/purok, barangay"
                        class="
                            w-full
                            rounded-xl
                            border-slate-300
                            px-4 py-3
                            focus:border-sky-500
                            focus:ring-sky-500
                        ">{{ old('address') }}</textarea>

                    <p class="text-xs text-slate-500 mt-2">
                        Include your Purok/PRK, street, and barangay when available.
                    </p>

                </div>


                {{-- ================================================= --}}
                {{-- LANDMARK --}}
                {{-- ================================================= --}}

                <div class="mb-6">

                    <label
                        for="landmark"
                        class="block text-sm font-semibold text-slate-700 mb-2">

                        Nearby landmark

                        <span class="text-slate-400 font-normal">
                            (Optional)
                        </span>

                    </label>

                    <input
                        id="landmark"
                        name="landmark"
                        type="text"
                        value="{{ old('landmark') }}"
                        maxlength="255"
                        placeholder="Example: Near barangay hall"
                        class="
                            w-full
                            rounded-xl
                            border-slate-300
                            px-4 py-3
                            focus:border-sky-500
                            focus:ring-sky-500
                        ">

                </div>


                {{-- ================================================= --}}
                {{-- MAP --}}
                {{-- ================================================= --}}

                <div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">

                        <div>

                            <label
                                class="block text-sm font-semibold text-slate-700">

                                Confirm location on the map

                            </label>

                            <p class="text-xs text-slate-500 mt-1">
                                The address identifies the place. The map pin records its geographic location.
                            </p>

                        </div>


                        <button
                            type="button"
                            id="locateMe"
                            class="
                                inline-flex
                                items-center
                                justify-center
                                gap-2
                                text-sm
                                font-semibold
                                text-sky-600
                                hover:text-sky-800
                                whitespace-nowrap
                            ">

                            <i class="fas fa-location-crosshairs"></i>

                            Use my location

                        </button>

                    </div>


                    {{-- MAP --}}

                    <div
                        id="complaintMap"
                        class="
                            w-full
                            h-80
                            rounded-2xl
                            border border-slate-300
                            overflow-hidden
                            bg-slate-100
                        ">
                    </div>


                    {{-- MAP INSTRUCTION --}}

                    <p class="text-xs text-slate-500 mt-3">

                        Click the map or drag the marker to the location
                        where the concern occurred.

                    </p>


                    {{-- DETECTED LOCATION --}}

                    <div
                        id="detectedLocationBox"
                        class="
                            hidden
                            mt-4
                            rounded-2xl
                            border
                            border-sky-100
                            bg-sky-50
                            p-4
                        ">

                        <div class="flex items-start gap-3">

                            <div
                                class="
                                    w-10 h-10
                                    rounded-xl
                                    bg-white
                                    text-sky-600
                                    flex
                                    items-center
                                    justify-center
                                    flex-shrink-0
                                ">

                                <i class="fas fa-map-pin"></i>

                            </div>


                            <div class="flex-1 min-w-0">

                                <p class="text-xs font-semibold uppercase tracking-wide text-sky-600">

                                    Detected map location

                                </p>

                                <p
                                    id="detectedLocation"
                                    class="mt-1 text-sm font-medium text-slate-700 break-words">

                                    —

                                </p>

                                <p
                                    id="reverseGeocodeStatus"
                                    class="mt-1 text-xs text-slate-500">

                                    Location selected.

                                </p>

                            </div>

                        </div>


                        <button
                            type="button"
                            id="useDetectedAddress"
                            class="
                                hidden
                                mt-3
                                text-sm
                                font-semibold
                                text-sky-700
                                hover:text-sky-900
                            ">

                            <i class="fas fa-arrow-down mr-1"></i>

                            Use detected location as address

                        </button>

                    </div>


                    {{-- LOCATION COORDINATES --}}

                    <input
                        type="hidden"
                        id="latitude"
                        name="latitude"
                        value="{{ old('latitude') }}">


                    <input
                        type="hidden"
                        id="longitude"
                        name="longitude"
                        value="{{ old('longitude') }}">

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- PHOTO --}}
            {{-- ===================================================== --}}

            <section
                class="bg-white rounded-3xl shadow-sm
                border border-slate-200 p-6 md:p-8">

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-800">
                        Add a photo
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        A photo can help staff understand the problem.
                    </p>

                </div>


                <input
                    type="file"
                    name="photo"
                    accept="image/jpeg,image/png,image/webp"
                    class="
                        w-full
                        rounded-xl
                        border
                        border-slate-300
                        px-4 py-3
                        text-sm
                    ">


                <p class="text-xs text-slate-400 mt-2">
                    JPG, PNG, or WEBP. Maximum 5 MB.
                </p>

            </section>


            {{-- ===================================================== --}}
            {{-- SUBMIT --}}
            {{-- ===================================================== --}}

            <div
                class="
                    bg-white
                    rounded-3xl
                    border border-slate-200
                    shadow-sm
                    p-6 md:p-8
                ">

                <div class="flex items-start gap-4">

                    <div
                        class="
                            w-11 h-11
                            rounded-xl
                            bg-sky-100
                            flex
                            items-center
                            justify-center
                            text-xl
                            flex-shrink-0
                        ">

                        <i class="fas fa-exclamation-triangle text-sky-600"></i>

                    </div>


                    <div>

                        <h3 class="font-bold text-slate-800">
                            Before you submit
                        </h3>

                        <p class="text-sm text-slate-500 mt-1 leading-6">
                            Please make sure the information you provided is
                            correct. Customer Service will review your concern
                            before it is processed.
                        </p>

                    </div>

                </div>


                <div class="mt-6 space-y-3 text-sm text-slate-600">

                    <div class="flex gap-3">

                        <span>✓</span>

                        <span>
                            Your complaint will be recorded in the system.
                        </span>

                    </div>


                    <div class="flex gap-3">

                        <span>✓</span>

                        <span>
                            Customer Service will review your concern.
                        </span>

                    </div>


                    <div class="flex gap-3">

                        <span>✓</span>

                        <span>
                            You can monitor the status of your complaint
                            from your Consumer Portal.
                        </span>

                    </div>

                </div>


                <div
                    class="
                        mt-8
                        flex
                        flex-col
                        sm:flex-row
                        justify-end
                        gap-3
                    ">

                    <a
                        href="{{ route('consumer.complaints.index') }}"
                        class="
                            px-6 py-3
                            rounded-xl
                            border border-slate-300
                            text-slate-700
                            text-center
                            hover:bg-slate-50
                            transition
                        ">

                        Cancel

                    </a>


                    <button
                        type="submit"
                        class="
                            px-7 py-3
                            rounded-xl
                            bg-sky-700
                            text-white
                            font-semibold
                            hover:bg-sky-800
                            transition
                            shadow-lg
                            shadow-sky-700/20
                        ">

                        Submit Concern

                    </button>

                </div>

            </div>

        </form>

    </div>


    {{-- ============================================================= --}}
    {{-- LEAFLET + REVERSE GEOCODING --}}
    {{-- ============================================================= --}}

    @push('scripts')

        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        />

        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
        </script>


        <script>

            document.addEventListener('DOMContentLoaded', function () {

                /*
                |--------------------------------------------------------------------------
                | DEFAULT LOCATION
                |--------------------------------------------------------------------------
                |
                | Sagay City approximate center.
                |
                */

                const defaultLat = 10.9447;
                const defaultLng = 123.4247;


                /*
                |--------------------------------------------------------------------------
                | ELEMENTS
                |--------------------------------------------------------------------------
                */

                const latitudeInput =
                    document.getElementById('latitude');

                const longitudeInput =
                    document.getElementById('longitude');

                const addressInput =
                    document.getElementById('address');

                const detectedLocationBox =
                    document.getElementById('detectedLocationBox');

                const detectedLocation =
                    document.getElementById('detectedLocation');

                const reverseGeocodeStatus =
                    document.getElementById('reverseGeocodeStatus');

                const useDetectedAddress =
                    document.getElementById('useDetectedAddress');


                /*
                |--------------------------------------------------------------------------
                | SAVED LOCATION
                |--------------------------------------------------------------------------
                */

                const savedLat =
                    parseFloat(latitudeInput.value);

                const savedLng =
                    parseFloat(longitudeInput.value);


                const startLat =
                    !isNaN(savedLat)
                        ? savedLat
                        : defaultLat;


                const startLng =
                    !isNaN(savedLng)
                        ? savedLng
                        : defaultLng;


                /*
                |--------------------------------------------------------------------------
                | MAP
                |--------------------------------------------------------------------------
                */

                const map =
                    L.map('complaintMap')
                        .setView(
                            [startLat, startLng],
                            14
                        );


                /*
                |--------------------------------------------------------------------------
                | OPENSTREETMAP
                |--------------------------------------------------------------------------
                */

                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        maxZoom: 19,
                        attribution:
                            '&copy; OpenStreetMap contributors'
                    }
                ).addTo(map);


                /*
                |--------------------------------------------------------------------------
                | MARKER
                |--------------------------------------------------------------------------
                */

                let marker = null;


                /*
                |--------------------------------------------------------------------------
                | LAST DETECTED ADDRESS
                |--------------------------------------------------------------------------
                */

                let lastDetectedAddress = '';


                /*
                |--------------------------------------------------------------------------
                | REVERSE GEOCODING
                |--------------------------------------------------------------------------
                */

                async function reverseGeocode(lat, lng) {

                    detectedLocationBox.classList.remove('hidden');

                    detectedLocation.textContent =
                        'Detecting location...';

                    reverseGeocodeStatus.textContent =
                        'Please wait while the map location is being identified.';

                    useDetectedAddress.classList.add('hidden');


                    try {

                        const url =
                            'https://nominatim.openstreetmap.org/reverse' +
                            '?format=jsonv2' +
                            '&lat=' + encodeURIComponent(lat) +
                            '&lon=' + encodeURIComponent(lng) +
                            '&zoom=18' +
                            '&addressdetails=1' +
                            '&accept-language=en';


                        const response =
                            await fetch(url, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });


                        if (!response.ok) {
                            throw new Error(
                                'Reverse geocoding request failed.'
                            );
                        }


                        const data =
                            await response.json();


                        if (!data || !data.display_name) {

                            throw new Error(
                                'No readable address was found.'
                            );

                        }


                        lastDetectedAddress =
                            data.display_name;


                        detectedLocation.textContent =
                            lastDetectedAddress;


                        reverseGeocodeStatus.textContent =
                            'This location was detected from the map pin. Review the address before submitting.';


                        useDetectedAddress.classList.remove('hidden');


                    } catch (error) {

                        console.error(
                            'Reverse geocoding error:',
                            error
                        );


                        lastDetectedAddress = '';


                        detectedLocation.textContent =
                            'Unable to detect a readable address.';


                        reverseGeocodeStatus.textContent =
                            'You can still enter the address manually.';


                        useDetectedAddress.classList.add('hidden');

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | SET LOCATION
                |--------------------------------------------------------------------------
                */

                function setLocation(
                    lat,
                    lng,
                    shouldReverseGeocode = true
                ) {

                    latitudeInput.value =
                        Number(lat).toFixed(7);


                    longitudeInput.value =
                        Number(lng).toFixed(7);


                    /*
                    |--------------------------------------------------------------------------
                    | CREATE OR MOVE MARKER
                    |--------------------------------------------------------------------------
                    */

                    if (marker) {

                        marker.setLatLng([
                            lat,
                            lng
                        ]);

                    } else {

                        marker =
                            L.marker(
                                [lat, lng],
                                {
                                    draggable: true
                                }
                            ).addTo(map);


                        /*
                        |--------------------------------------------------------------------------
                        | DRAG MARKER
                        |--------------------------------------------------------------------------
                        */

                        marker.on(
                            'dragend',
                            function (event) {

                                const position =
                                    event.target.getLatLng();


                                setLocation(
                                    position.lat,
                                    position.lng,
                                    true
                                );

                            }
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | REVERSE GEOCODE
                    |--------------------------------------------------------------------------
                    */

                    if (shouldReverseGeocode) {

                        reverseGeocode(
                            lat,
                            lng
                        );

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | RESTORE OLD LOCATION AFTER VALIDATION ERROR
                |--------------------------------------------------------------------------
                */

                if (
                    !isNaN(savedLat) &&
                    !isNaN(savedLng)
                ) {

                    setLocation(
                        savedLat,
                        savedLng,
                        true
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | CLICK MAP
                |--------------------------------------------------------------------------
                */

                map.on(
                    'click',
                    function (event) {

                        setLocation(
                            event.latlng.lat,
                            event.latlng.lng,
                            true
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | USE DETECTED ADDRESS
                |--------------------------------------------------------------------------
                */

                useDetectedAddress.addEventListener(
                    'click',
                    function () {

                        if (!lastDetectedAddress) {
                            return;
                        }


                        addressInput.value =
                            lastDetectedAddress;


                        addressInput.focus();


                        reverseGeocodeStatus.textContent =
                            'Detected location copied to the address field. You may edit it to include Purok/PRK or other local details.';

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | USE MY LOCATION
                |--------------------------------------------------------------------------
                */

                document
                    .getElementById('locateMe')
                    .addEventListener(
                        'click',
                        function () {

                            if (!navigator.geolocation) {

                                alert(
                                    'Location services are not supported by your browser.'
                                );

                                return;

                            }


                            const button = this;


                            button.disabled = true;

                            button.innerHTML =
                                '<i class="fas fa-spinner fa-spin"></i> Locating...';


                            navigator.geolocation.getCurrentPosition(

                                function (position) {

                                    const lat =
                                        position.coords.latitude;


                                    const lng =
                                        position.coords.longitude;


                                    map.setView(
                                        [lat, lng],
                                        17
                                    );


                                    setLocation(
                                        lat,
                                        lng,
                                        true
                                    );


                                    button.disabled = false;

                                    button.innerHTML =
                                        '<i class="fas fa-location-crosshairs"></i> Use my location';

                                },


                                function (error) {

                                    console.error(
                                        'Geolocation error:',
                                        error
                                    );


                                    alert(
                                        'Unable to get your location. Please allow location access or select the location manually on the map.'
                                    );


                                    button.disabled = false;

                                    button.innerHTML =
                                        '<i class="fas fa-location-crosshairs"></i> Use my location';

                                },

                                {
                                    enableHighAccuracy: true,
                                    timeout: 10000,
                                    maximumAge: 0
                                }

                            );

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | LEAFLET RENDERING FIX
                |--------------------------------------------------------------------------
                */

                setTimeout(
                    function () {

                        map.invalidateSize();

                    },
                    300
                );

            });

        </script>


        {{-- ========================================================= --}}
        {{-- CATEGORY HELP --}}
        {{-- ========================================================= --}}

        <script>

            document.addEventListener('DOMContentLoaded', function () {

                const categoryHelp =
                    document.getElementById('categoryHelp');

                const categoryHelpTitle =
                    document.getElementById('categoryHelpTitle');

                const categoryHelpText =
                    document.getElementById('categoryHelpText');


                const guidance = {

                    @foreach ($categories as $category)

                        "{{ $category->id }}": {

                            title:
                                @json($category->name),

                            text:
                                @json(
                                    $category->description
                                    ?: 'Please provide details about your concern so Customer Service can assist you.'
                                )

                        }

                        {{ !$loop->last ? ',' : '' }}

                    @endforeach

                };


                const categoryInputs =
                    document.querySelectorAll(
                        'input[name="complaint_category_id"]'
                    );


                function showCategoryHelp(categoryId) {

                    const data =
                        guidance[categoryId];


                    if (!data) {

                        categoryHelp.classList.add('hidden');

                        return;

                    }


                    categoryHelpTitle.textContent =
                        data.title;


                    categoryHelpText.textContent =
                        data.text;


                    categoryHelp.classList.remove('hidden');

                }


                categoryInputs.forEach(
                    function (input) {

                        input.addEventListener(
                            'change',
                            function () {

                                showCategoryHelp(
                                    this.value
                                );

                            }
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | RESTORE CATEGORY AFTER VALIDATION ERROR
                |--------------------------------------------------------------------------
                */

                const selectedCategory =
                    document.querySelector(
                        'input[name="complaint_category_id"]:checked'
                    );


                if (selectedCategory) {

                    showCategoryHelp(
                        selectedCategory.value
                    );

                }

            });

        </script>

    @endpush

@endsection
2. resources/views/consumer/complaints/edit.blade.php

For edit, use this complete version.

The important difference is that it loads the complaint's existing address, coordinates, landmark, subject, description, and category.

@extends('consumer.layouts.app')

@section('title', 'Edit Complaint')

@section('content')

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="mb-8">

            <div class="flex items-center gap-3 mb-3">

                <div
                    class="
                        w-12 h-12
                        rounded-2xl
                        bg-sky-100
                        text-sky-700
                        flex
                        items-center
                        justify-center
                        text-2xl
                    ">

                    <i class="fas fa-pen-to-square"></i>

                </div>


                <div>

                    <h1 class="text-2xl font-bold text-slate-800">
                        Edit Complaint
                    </h1>

                    <p class="text-sm text-slate-500">
                        Update the information about your concern.
                    </p>

                </div>

            </div>


            <div
                class="
                    bg-amber-50
                    border border-amber-100
                    rounded-2xl
                    p-4
                ">

                <p class="text-sm text-amber-800">

                    <strong>Complaint {{ $complaint->complaint_no }}</strong>

                    <br>

                    You can edit this complaint while it is still pending
                    review by Customer Service.

                </p>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- VALIDATION ERRORS --}}
        {{-- ========================================================= --}}

        @if ($errors->any())

            <div
                class="
                    mb-6
                    bg-red-50
                    border border-red-200
                    rounded-2xl
                    p-5
                ">

                <div class="font-semibold text-red-700 mb-2">

                    Please check the following:

                </div>


                <ul
                    class="
                        list-disc
                        ml-5
                        text-sm
                        text-red-600
                        space-y-1
                    ">

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- FORM --}}
        {{-- ========================================================= --}}

        <form
            method="POST"
            action="{{ route('consumer.complaints.update', $complaint) }}"
            enctype="multipart/form-data"
            class="space-y-8">

            @csrf

            @method('PUT')


            {{-- ===================================================== --}}
            {{-- CATEGORY --}}
            {{-- ===================================================== --}}

            <section
                class="
                    bg-white
                    rounded-3xl
                    shadow-sm
                    border border-slate-200
                    p-6 md:p-8
                ">

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-800">

                        What is your concern?

                    </h2>


                    <p class="text-sm text-slate-500 mt-1">

                        Select the option that best describes your concern.

                    </p>

                </div>


                <div
                    class="
                        grid
                        sm:grid-cols-2
                        lg:grid-cols-3
                        gap-4
                    ">

                    @foreach ($categories as $category)

                        <label
                            class="
                                relative
                                cursor-pointer
                                block
                            ">

                            <input
                                type="radio"
                                name="complaint_category_id"
                                value="{{ $category->id }}"
                                class="peer sr-only"
                                {{ old('complaint_category_id', $complaint->complaint_category_id) == $category->id ? 'checked' : '' }}
                                required>


                            <div
                                class="
                                    h-full
                                    rounded-2xl
                                    border-2
                                    border-slate-200
                                    bg-white
                                    p-5
                                    transition-all
                                    duration-200

                                    hover:border-sky-300
                                    hover:bg-sky-50/50

                                    peer-checked:border-sky-600
                                    peer-checked:bg-sky-50
                                    peer-checked:shadow-md
                                ">

                                <div
                                    class="
                                        flex
                                        items-start
                                        justify-between
                                        gap-4
                                    ">

                                    <div class="flex-1">

                                        <h3
                                            class="
                                                font-semibold
                                                text-slate-800
                                                peer-checked:text-sky-700
                                            ">

                                            {{ $category->name }}

                                        </h3>


                                        @if ($category->description)

                                            <p
                                                class="
                                                    text-sm
                                                    text-slate-500
                                                    mt-2
                                                ">

                                                {{ $category->description }}

                                            </p>

                                        @endif

                                    </div>


                                    <div
                                        class="
                                            flex-shrink-0
                                            w-6 h-6
                                            rounded-full
                                            border-2
                                            border-slate-300
                                            flex
                                            items-center
                                            justify-center
                                            transition
                                            peer-checked:border-sky-600
                                        ">

                                        <div
                                            class="
                                                w-3 h-3
                                                rounded-full
                                                bg-sky-600
                                                scale-0
                                                transition-transform
                                                peer-checked:scale-100
                                            ">

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </label>

                    @endforeach

                </div>


                {{-- CATEGORY HELP --}}

                <div
                    id="categoryHelp"
                    class="
                        hidden
                        mt-6
                        rounded-2xl
                        bg-sky-50
                        border
                        border-sky-100
                        p-5
                    ">

                    <div class="flex gap-3">

                        <div
                            class="
                                w-10 h-10
                                flex-shrink-0
                                rounded-xl
                                bg-white
                                flex
                                items-center
                                justify-center
                                text-xl
                                shadow-sm
                            ">

                            <i
                                class="
                                    fas
                                    fa-info-circle
                                    text-sky-600
                                ">
                            </i>

                        </div>


                        <div>

                            <h3
                                id="categoryHelpTitle"
                                class="
                                    font-semibold
                                    text-sky-800
                                ">
                            </h3>


                            <p
                                id="categoryHelpText"
                                class="
                                    text-sm
                                    text-sky-700
                                    mt-1
                                    leading-6
                                ">
                            </p>

                        </div>

                    </div>

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- COMPLAINT DETAILS --}}
            {{-- ===================================================== --}}

            <section
                class="
                    bg-white
                    rounded-3xl
                    shadow-sm
                    border border-slate-200
                    p-6 md:p-8
                ">

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-800">

                        Tell us about the problem

                    </h2>


                    <p class="text-sm text-slate-500 mt-1">

                        Update the details if something has changed.

                    </p>

                </div>


                {{-- SUBJECT --}}

                <div class="mb-6">

                    <label
                        for="subject"
                        class="
                            block
                            text-sm
                            font-semibold
                            text-slate-700
                            mb-2
                        ">

                        Short description

                        <span class="text-red-500">*</span>

                    </label>


                    <input
                        id="subject"
                        name="subject"
                        type="text"
                        value="{{ old('subject', $complaint->subject) }}"
                        required
                        maxlength="255"
                        placeholder="Example: No water in our house"
                        class="
                            w-full
                            rounded-xl
                            border-slate-300
                            px-4 py-3
                            focus:border-sky-500
                            focus:ring-sky-500
                        ">

                </div>


                {{-- DESCRIPTION --}}

                <div>

                    <label
                        for="description"
                        class="
                            block
                            text-sm
                            font-semibold
                            text-slate-700
                            mb-2
                        ">

                        What happened?

                        <span class="text-red-500">*</span>

                    </label>


                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        required
                        maxlength="5000"
                        placeholder="Example: We have had no water since this morning."
                        class="
                            w-full
                            rounded-xl
                            border-slate-300
                            px-4 py-3
                            focus:border-sky-500
                            focus:ring-sky-500
                        ">{{ old('description', $complaint->description) }}</textarea>


                    <p class="text-xs text-slate-400 mt-2">

                        Please avoid passwords, payment details,
                        or other sensitive information.

                    </p>

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- LOCATION --}}
            {{-- ===================================================== --}}

            <section
                class="
                    bg-white
                    rounded-3xl
                    shadow-sm
                    border border-slate-200
                    p-6 md:p-8
                ">

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-800">

                        Where is the concern?

                    </h2>


                    <p class="text-sm text-slate-500 mt-1">

                        Update the address or confirm the geographic
                        location using the map.

                    </p>

                </div>


                {{-- ADDRESS --}}

                <div class="mb-6">

                    <label
                        for="address"
                        class="
                            block
                            text-sm
                            font-semibold
                            text-slate-700
                            mb-2
                        ">

                        Address

                        <span class="text-red-500">*</span>

                    </label>


                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        required
                        maxlength="500"
                        placeholder="House number, street/purok, barangay"
                        class="
                            w-full
                            rounded-xl
                            border-slate-300
                            px-4 py-3
                            focus:border-sky-500
                            focus:ring-sky-500
                        ">{{ old('address', $complaint->address) }}</textarea>


                    <p class="text-xs text-slate-500 mt-2">

                        Include your Purok/PRK, street, and barangay
                        when available.

                    </p>

                </div>


                {{-- LANDMARK --}}

                <div class="mb-6">

                    <label
                        for="landmark"
                        class="
                            block
                            text-sm
                            font-semibold
                            text-slate-700
                            mb-2
                        ">

                        Nearby landmark

                        <span
                            class="
                                text-slate-400
                                font-normal
                            ">

                            (Optional)

                        </span>

                    </label>


                    <input
                        id="landmark"
                        name="landmark"
                        type="text"
                        value="{{ old('landmark', $complaint->landmark) }}"
                        maxlength="255"
                        placeholder="Example: Near barangay hall"
                        class="
                            w-full
                            rounded-xl
                            border-slate-300
                            px-4 py-3
                            focus:border-sky-500
                            focus:ring-sky-500
                        ">

                </div>


                {{-- MAP --}}

                <div>

                    <div
                        class="
                            flex
                            flex-col
                            sm:flex-row
                            sm:items-center
                            sm:justify-between
                            gap-3
                            mb-3
                        ">

                        <div>

                            <label
                                class="
                                    block
                                    text-sm
                                    font-semibold
                                    text-slate-700
                                ">

                                Confirm location on the map

                            </label>


                            <p
                                class="
                                    text-xs
                                    text-slate-500
                                    mt-1
                                ">

                                The address identifies the place.
                                The map pin records its geographic location.

                            </p>

                        </div>


                        <button
                            type="button"
                            id="locateMe"
                            class="
                                inline-flex
                                items-center
                                justify-center
                                gap-2
                                text-sm
                                font-semibold
                                text-sky-600
                                hover:text-sky-800
                                whitespace-nowrap
                            ">

                            <i class="fas fa-location-crosshairs"></i>

                            Use my location

                        </button>

                    </div>


                    {{-- MAP --}}

                    <div
                        id="complaintMap"
                        class="
                            w-full
                            h-80
                            rounded-2xl
                            border border-slate-300
                            overflow-hidden
                            bg-slate-100
                        ">
                    </div>


                    <p class="text-xs text-slate-500 mt-3">

                        Click the map or drag the marker to the location
                        where the concern occurred.

                    </p>


                    {{-- DETECTED LOCATION --}}

                    <div
                        id="detectedLocationBox"
                        class="
                            hidden
                            mt-4
                            rounded-2xl
                            border
                            border-sky-100
                            bg-sky-50
                            p-4
                        ">

                        <div class="flex items-start gap-3">

                            <div
                                class="
                                    w-10 h-10
                                    rounded-xl
                                    bg-white
                                    text-sky-600
                                    flex
                                    items-center
                                    justify-center
                                    flex-shrink-0
                                ">

                                <i class="fas fa-map-pin"></i>

                            </div>


                            <div
                                class="
                                    flex-1
                                    min-w-0
                                ">

                                <p
                                    class="
                                        text-xs
                                        font-semibold
                                        uppercase
                                        tracking-wide
                                        text-sky-600
                                    ">

                                    Detected map location

                                </p>


                                <p
                                    id="detectedLocation"
                                    class="
                                        mt-1
                                        text-sm
                                        font-medium
                                        text-slate-700
                                        break-words
                                    ">

                                    —

                                </p>


                                <p
                                    id="reverseGeocodeStatus"
                                    class="
                                        mt-1
                                        text-xs
                                        text-slate-500
                                    ">

                                    Location selected.

                                </p>

                            </div>

                        </div>


                        <button
                            type="button"
                            id="useDetectedAddress"
                            class="
                                hidden
                                mt-3
                                text-sm
                                font-semibold
                                text-sky-700
                                hover:text-sky-900
                            ">

                            <i class="fas fa-arrow-down mr-1"></i>

                            Use detected location as address

                        </button>

                    </div>


                    {{-- HIDDEN COORDINATES --}}

                    <input
                        type="hidden"
                        id="latitude"
                        name="latitude"
                        value="{{ old('latitude', $complaint->latitude) }}">


                    <input
                        type="hidden"
                        id="longitude"
                        name="longitude"
                        value="{{ old('longitude', $complaint->longitude) }}">

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- PHOTO --}}
            {{-- ===================================================== --}}

            <section
                class="
                    bg-white
                    rounded-3xl
                    shadow-sm
                    border border-slate-200
                    p-6 md:p-8
                ">

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-800">

                        Complaint photo

                    </h2>


                    <p class="text-sm text-slate-500 mt-1">

                        You may replace the existing photo
                        or leave it unchanged.

                    </p>

                </div>


                @if ($complaint->photo)

                    <div class="mb-5">

                        <p
                            class="
                                text-sm
                                font-semibold
                                text-slate-700
                                mb-2
                            ">

                            Current photo

                        </p>


                        <img
                            src="{{ asset('storage/' . $complaint->photo) }}"
                            alt="Current complaint photo"
                            class="
                                max-h-64
                                rounded-2xl
                                border
                                border-slate-200
                            ">

                    </div>

                @endif


                <input
                    type="file"
                    name="photo"
                    accept="image/jpeg,image/png,image/webp"
                    class="
                        w-full
                        rounded-xl
                        border
                        border-slate-300
                        px-4 py-3
                        text-sm
                    ">


                <p class="text-xs text-slate-400 mt-2">

                    JPG, PNG, or WEBP. Maximum 5 MB.

                </p>

            </section>


            {{-- ===================================================== --}}
            {{-- ACTIONS --}}
            {{-- ===================================================== --}}

            <div
                class="
                    bg-white
                    rounded-3xl
                    border border-slate-200
                    shadow-sm
                    p-6 md:p-8
                ">

                <div class="flex items-start gap-4">

                    <div
                        class="
                            w-11 h-11
                            rounded-xl
                            bg-sky-100
                            flex
                            items-center
                            justify-center
                            text-xl
                            flex-shrink-0
                        ">

                        <i class="fas fa-circle-info text-sky-600"></i>

                    </div>


                    <div>

                        <h3 class="font-bold text-slate-800">

                            Before you save

                        </h3>


                        <p
                            class="
                                text-sm
                                text-slate-500
                                mt-1
                                leading-6
                            ">

                            Make sure the complaint details,
                            address, and map location are correct.

                        </p>

                    </div>

                </div>


                <div
                    class="
                        mt-8
                        flex
                        flex-col
                        sm:flex-row
                        justify-end
                        gap-3
                    ">

                    <a
                        href="{{ route('consumer.complaints.show', $complaint) }}"
                        class="
                            px-6 py-3
                            rounded-xl
                            border border-slate-300
                            text-slate-700
                            text-center
                            hover:bg-slate-50
                            transition
                        ">

                        Cancel

                    </a>


                    <button
                        type="submit"
                        class="
                            px-7 py-3
                            rounded-xl
                            bg-sky-700
                            text-white
                            font-semibold
                            hover:bg-sky-800
                            transition
                            shadow-lg
                            shadow-sky-700/20
                        ">

                        <i class="fas fa-save mr-2"></i>

                        Save Changes

                    </button>

                </div>

            </div>

        </form>

    </div>


    {{-- ============================================================= --}}
    {{-- LEAFLET + REVERSE GEOCODING --}}
    {{-- ============================================================= --}}

    @push('scripts')

        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        />

        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
        </script>


        <script>

            document.addEventListener('DOMContentLoaded', function () {

                /*
                |--------------------------------------------------------------------------
                | DEFAULT LOCATION
                |--------------------------------------------------------------------------
                */

                const defaultLat = 10.9447;
                const defaultLng = 123.4247;


                /*
                |--------------------------------------------------------------------------
                | ELEMENTS
                |--------------------------------------------------------------------------
                */

                const latitudeInput =
                    document.getElementById('latitude');

                const longitudeInput =
                    document.getElementById('longitude');

                const addressInput =
                    document.getElementById('address');

                const detectedLocationBox =
                    document.getElementById('detectedLocationBox');

                const detectedLocation =
                    document.getElementById('detectedLocation');

                const reverseGeocodeStatus =
                    document.getElementById('reverseGeocodeStatus');

                const useDetectedAddress =
                    document.getElementById('useDetectedAddress');


                /*
                |--------------------------------------------------------------------------
                | EXISTING COORDINATES
                |--------------------------------------------------------------------------
                */

                const savedLat =
                    parseFloat(latitudeInput.value);

                const savedLng =
                    parseFloat(longitudeInput.value);


                const startLat =
                    !isNaN(savedLat)
                        ? savedLat
                        : defaultLat;


                const startLng =
                    !isNaN(savedLng)
                        ? savedLng
                        : defaultLng;


                /*
                |--------------------------------------------------------------------------
                | MAP
                |--------------------------------------------------------------------------
                */

                const map =
                    L.map('complaintMap')
                        .setView(
                            [startLat, startLng],
                            !isNaN(savedLat) && !isNaN(savedLng)
                                ? 17
                                : 14
                        );


                /*
                |--------------------------------------------------------------------------
                | OPENSTREETMAP
                |--------------------------------------------------------------------------
                */

                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        maxZoom: 19,
                        attribution:
                            '&copy; OpenStreetMap contributors'
                    }
                ).addTo(map);


                /*
                |--------------------------------------------------------------------------
                | MARKER
                |--------------------------------------------------------------------------
                */

                let marker = null;


                /*
                |--------------------------------------------------------------------------
                | LAST DETECTED ADDRESS
                |--------------------------------------------------------------------------
                */

                let lastDetectedAddress = '';


                /*
                |--------------------------------------------------------------------------
                | REVERSE GEOCODING
                |--------------------------------------------------------------------------
                */

                async function reverseGeocode(lat, lng) {

                    detectedLocationBox.classList.remove('hidden');

                    detectedLocation.textContent =
                        'Detecting location...';

                    reverseGeocodeStatus.textContent =
                        'Please wait while the map location is being identified.';

                    useDetectedAddress.classList.add('hidden');


                    try {

                        const url =
                            'https://nominatim.openstreetmap.org/reverse' +
                            '?format=jsonv2' +
                            '&lat=' + encodeURIComponent(lat) +
                            '&lon=' + encodeURIComponent(lng) +
                            '&zoom=18' +
                            '&addressdetails=1' +
                            '&accept-language=en';


                        const response =
                            await fetch(url, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });


                        if (!response.ok) {

                            throw new Error(
                                'Reverse geocoding request failed.'
                            );

                        }


                        const data =
                            await response.json();


                        if (!data || !data.display_name) {

                            throw new Error(
                                'No readable address was found.'
                            );

                        }


                        lastDetectedAddress =
                            data.display_name;


                        detectedLocation.textContent =
                            lastDetectedAddress;


                        reverseGeocodeStatus.textContent =
                            'This location was detected from the map pin. Review the address before submitting.';


                        useDetectedAddress.classList.remove('hidden');


                    } catch (error) {

                        console.error(
                            'Reverse geocoding error:',
                            error
                        );


                        lastDetectedAddress = '';


                        detectedLocation.textContent =
                            'Unable to detect a readable address.';


                        reverseGeocodeStatus.textContent =
                            'You can still enter the address manually.';


                        useDetectedAddress.classList.add('hidden');

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | SET LOCATION
                |--------------------------------------------------------------------------
                */

                function setLocation(
                    lat,
                    lng,
                    shouldReverseGeocode = true
                ) {

                    latitudeInput.value =
                        Number(lat).toFixed(7);


                    longitudeInput.value =
                        Number(lng).toFixed(7);


                    if (marker) {

                        marker.setLatLng([
                            lat,
                            lng
                        ]);

                    } else {

                        marker =
                            L.marker(
                                [lat, lng],
                                {
                                    draggable: true
                                }
                            ).addTo(map);


                        marker.on(
                            'dragend',
                            function (event) {

                                const position =
                                    event.target.getLatLng();


                                setLocation(
                                    position.lat,
                                    position.lng,
                                    true
                                );

                            }
                        );

                    }


                    if (shouldReverseGeocode) {

                        reverseGeocode(
                            lat,
                            lng
                        );

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | RESTORE EXISTING LOCATION
                |--------------------------------------------------------------------------
                */

                if (
                    !isNaN(savedLat) &&
                    !isNaN(savedLng)
                ) {

                    setLocation(
                        savedLat,
                        savedLng,
                        true
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | CLICK MAP
                |--------------------------------------------------------------------------
                */

                map.on(
                    'click',
                    function (event) {

                        setLocation(
                            event.latlng.lat,
                            event.latlng.lng,
                            true
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | USE DETECTED ADDRESS
                |--------------------------------------------------------------------------
                */

                useDetectedAddress.addEventListener(
                    'click',
                    function () {

                        if (!lastDetectedAddress) {
                            return;
                        }


                        addressInput.value =
                            lastDetectedAddress;


                        addressInput.focus();


                        reverseGeocodeStatus.textContent =
                            'Detected location copied to the address field. You may edit it to include Purok/PRK or other local details.';

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | USE MY LOCATION
                |--------------------------------------------------------------------------
                */

                document
                    .getElementById('locateMe')
                    .addEventListener(
                        'click',
                        function () {

                            if (!navigator.geolocation) {

                                alert(
                                    'Location services are not supported by your browser.'
                                );

                                return;

                            }


                            const button = this;


                            button.disabled = true;

                            button.innerHTML =
                                '<i class="fas fa-spinner fa-spin"></i> Locating...';


                            navigator.geolocation.getCurrentPosition(

                                function (position) {

                                    const lat =
                                        position.coords.latitude;


                                    const lng =
                                        position.coords.longitude;


                                    map.setView(
                                        [lat, lng],
                                        17
                                    );


                                    setLocation(
                                        lat,
                                        lng,
                                        true
                                    );


                                    button.disabled = false;

                                    button.innerHTML =
                                        '<i class="fas fa-location-crosshairs"></i> Use my location';

                                },


                                function (error) {

                                    console.error(
                                        'Geolocation error:',
                                        error
                                    );


                                    alert(
                                        'Unable to get your location. Please allow location access or select the location manually on the map.'
                                    );


                                    button.disabled = false;

                                    button.innerHTML =
                                        '<i class="fas fa-location-crosshairs"></i> Use my location';

                                },

                                {
                                    enableHighAccuracy: true,
                                    timeout: 10000,
                                    maximumAge: 0
                                }

                            );

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | LEAFLET RENDERING FIX
                |--------------------------------------------------------------------------
                */

                setTimeout(
                    function () {

                        map.invalidateSize();

                    },
                    300
                );

            });

        </script>


        {{-- ========================================================= --}}
        {{-- CATEGORY HELP --}}
        {{-- ========================================================= --}}

        <script>

            document.addEventListener('DOMContentLoaded', function () {

                const categoryHelp =
                    document.getElementById('categoryHelp');

                const categoryHelpTitle =
                    document.getElementById('categoryHelpTitle');

                const categoryHelpText =
                    document.getElementById('categoryHelpText');


                const guidance = {

                    @foreach ($categories as $category)

                        "{{ $category->id }}": {

                            title:
                                @json($category->name),

                            text:
                                @json(
                                    $category->description
                                    ?: 'Please provide details about your concern so Customer Service can assist you.'
                                )

                        }

                        {{ !$loop->last ? ',' : '' }}

                    @endforeach

                };


                const categoryInputs =
                    document.querySelectorAll(
                        'input[name="complaint_category_id"]'
                    );


                function showCategoryHelp(categoryId) {

                    const data =
                        guidance[categoryId];


                    if (!data) {

                        categoryHelp.classList.add('hidden');

                        return;

                    }


                    categoryHelpTitle.textContent =
                        data.title;


                    categoryHelpText.textContent =
                        data.text;


                    categoryHelp.classList.remove('hidden');

                }


                categoryInputs.forEach(
                    function (input) {

                        input.addEventListener(
                            'change',
                            function () {

                                showCategoryHelp(
                                    this.value
                                );

                            }
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | RESTORE CATEGORY
                |--------------------------------------------------------------------------
                */

                const selectedCategory =
                    document.querySelector(
                        'input[name="complaint_category_id"]:checked'
                    );


                if (selectedCategory) {

                    showCategoryHelp(
                        selectedCategory.value
                    );

                }

            });

        </script>

    @endpush

@endsection
3. What changed in the location design

The important part is now this:

Consumer enters:
Address
┌───────────────────────────────────────────┐
│ Purok 3, Rizal Street, Brgy. Poblacion   │
└───────────────────────────────────────────┘

Then the consumer uses the map:

Confirm location on the map

┌───────────────────────────────────────────┐
│                                           │
│                  📍                       │
│                                           │
│                   MAP                     │
│                                           │
└───────────────────────────────────────────┘

Click the map or drag the marker...

Then reverse geocoding produces:

DETECTED MAP LOCATION

Poblacion, Sagay City, Negros Occidental,
Philippines

[ ↓ Use detected location as address ]

The consumer doesn't have to use the detected address.

That is intentional.

4. Why I made "Use detected location as address" optional

This is particularly important for your Sagay Water District use case.

Suppose reverse geocoding gives:

Poblacion, Sagay City, Negros Occidental, Philippines

But the actual consumer knows:

Purok 3, Rizal Street, Brgy. Poblacion

The consumer should not lose the local address just because the map doesn't know the Purok.

So:

MANUAL ADDRESS
        +
MAP COORDINATES
        +
REVERSE-GEOCODED LOCATION
        ↓
COMPLETE LOCATION INFORMATION

That's much better than relying entirely on reverse geocoding.

5. One important backend check

Your StoreConsumerComplaintRequest and UpdateConsumerComplaintRequest must allow:

'latitude'
'longitude'

For example:

'latitude' => [
    'nullable',
    'numeric',
    'between:-90,90',
],

'longitude' => [
    'nullable',
    'numeric',
    'between:-180,180',
],

And your Complaint model needs them in $fillable if you're using Complaint::create($validated) / $complaint->update($validated):

'latitude',
'longitude',

Your controller already passes the validated data into Complaint::create($validated), so the controller itself doesn't need a special reverse-geocoding function if we're doing the lookup in the browser.

6. One thing I would check before you run this

Make sure your layout has:

@stack('scripts')

before </body>.

Your original complaint page uses @push('scripts') for Leaflet, so without @stack('scripts'), the JavaScript won't actually be rendered.

Final location concept

I would keep this exact concept for your project:

Address: tells SWD where the consumer says the concern is
Map pin: tells SWD where the concern is geographically
Reverse geocoding: helps describe/verify the geographic location
Landmark: gives field personnel an additional local reference

And this is especially useful later for your AI-assisted plumber assignment recommendation, because the system can use the stored coordinates to compare the complaint location with known plumber/field assignment locations—while the actual assignment remains a human SWD decision.

Today 11:14 AM
but before i forgot lets modify first the category into complaint, since we have department is that possible to connect the complaint type, sample when they click engeering and operation the complaint dropdown only show for that division or sample conplaint like dirty water, no water,, etc,.. it prepared to create a new division 

Yes — that is absolutely possible, and I think this is the right structure for your system.

Instead of calling it Category, we can change the concept to:

Division → Complaint Type

For example:

Division
├── Engineering Operation
│   ├── Dirty Water
│   ├── No Water
│   ├── Low Water Pressure
│   ├── Mainline Leakage
│   └── Service Connection Leakage
│
└── Commercial Services
    ├── Malfunction Meter
    ├── Erroneous Reading
    ├── Large Consumption
    └── Reclassification

Then the consumer sees two steps.

Step 1 — Select Division
What type of service concern is this?

○ Engineering Operation
  Water supply, leakage, pressure, and
  other technical/service concerns

○ Commercial Services
  Billing, meter, account, and
  commercial concerns
Step 2 — Select Complaint Type

If they select Engineering Operation:

Complaint Type

┌─────────────────────────────────┐
│ Select complaint type       ▼  │
├─────────────────────────────────┤
│ Dirty Water                     │
│ No Water                        │
│ Low Water Pressure              │
│ Mainline Leakage                │
│ Service Connection Leakage      │
└─────────────────────────────────┘

If they change to Commercial Services, the dropdown automatically changes:

Complaint Type

┌─────────────────────────────────┐
│ Select complaint type       ▼  │
├─────────────────────────────────┤
│ Malfunction Meter               │
│ Erroneous Reading               │
│ Large Consumption               │
│ Reclassification                │
└─────────────────────────────────┘

So yes, we should create a new divisions table and connect complaint_types to it.

Recommended database structure

Instead of your current:

complaint_categories
        ↓
    complaints

I recommend:

divisions
     ↓
complaint_types
     ↓
complaints
divisions
id
name
description
is_active
timestamps

Example:

1 | Engineering Operation | Technical and field-related concerns | 1
2 | Commercial Services   | Commercial and account-related concerns | 1
complaint_types
id
division_id
name
description
is_active
timestamps

Example:

1 | 1 | Dirty Water
2 | 1 | No Water
3 | 1 | Low Water Pressure
4 | 1 | Mainline Leakage

5 | 2 | Malfunction Meter
6 | 2 | Erroneous Reading
7 | 2 | Large Consumption
8 | 2 | Reclassification

Then:

complaints

Instead of:

complaint_category_id

we can eventually have:

division_id
complaint_type_id

That gives you a very clear relationship:

Complaint
   │
   ├── Division
   │      └── Engineering Operation
   │
   └── Complaint Type
          └── No Water
Why I recommend storing both

You might wonder:

"If complaint type already belongs to a division, why save division_id in complaints?"

For your system, storing both can be useful because the complaint's final routing/confirmed division is an important business record.

For example:

AI suggestion:
Engineering Operation
No Water

        ↓

Customer Service reviews

        ↓

Confirmed:
Engineering Operation
No Water

Your database then preserves the final confirmed values.

However, we should enforce that the selected complaint_type_id actually belongs to the selected division_id. Don't trust the JavaScript dropdown alone. Laravel validation/backend checks must enforce it too.

This also fits your AI design

This makes your AI classification much cleaner.

The AI can eventually produce:

AI ANALYSIS

Suggested Division:
Engineering Operation

Suggested Complaint Type:
No Water

Confidence:
91%

Alternative:
Low Water Pressure

Then Customer Service can confirm/change it.

So your taxonomy becomes:

AI
 ↓
Division recommendation
 ↓
Complaint type recommendation
 ↓
Human confirmation
 ↓
Final division + complaint type

That is much cleaner than having one generic "category."

One thing I would NOT do yet

I wouldn't hard-code the complaint types directly into the Blade file like:

Engineering Operation = [
    "Dirty Water",
    "No Water"
]

Instead, let Laravel retrieve them from the database.

That way your Administrator can eventually manage:

Administration
    ↓
Divisions
    ↓
Complaint Types

For example:

Admin → Divisions

Engineering Operation
Commercial Services

and:

Admin → Complaint Types

Engineering Operation
    ├── Dirty Water
    ├── No Water
    └── Mainline Leakage

Commercial Services
    ├── Malfunction Meter
    └── Erroneous Reading

This also means if SWD later says:

"Add another complaint type."

you don't have to modify the code.

And this affects your current create/edit form

Your current form says:

What is your concern?

and loops through $categories.

We should change that section to:

What is your concern?

Division
[ Engineering Operation ] [ Commercial Services ]

Complaint Type
[ Select a complaint type ▼ ]

The complaint-type dropdown will be dynamically populated based on the selected division.

Backend flow

The request would eventually look like:

division_id = 1
complaint_type_id = 2

Laravel checks:

Does division 1 exist?
        ↓
Does complaint type 2 exist?
        ↓
Does complaint type 2 belong to division 1?
        ↓
YES
        ↓
Save complaint

If someone maliciously sends:

division_id = 1
complaint_type_id = 8

where type 8 belongs to Commercial Services, Laravel rejects it.

That's important for security and data integrity.

Migration relationship

The clean structure will be:

┌──────────────────┐
│    divisions     │
├──────────────────┤
│ id               │
│ name             │
│ description      │
│ is_active        │
└────────┬─────────┘
         │
         │ 1
         │
         │ many
         ▼
┌──────────────────────┐
│   complaint_types    │
├──────────────────────┤
│ id                   │
│ division_id          │
│ name                 │
│ description          │
│ is_active             │
└──────────┬───────────┘
           │
           │ 1
           │
           │ many
           ▼
┌──────────────────────┐
│      complaints      │
├──────────────────────┤
│ id                   │
│ division_id          │
│ complaint_type_id    │
│ consumer_id          │
│ subject              │
│ description          │
│ address              │
│ latitude             │
│ longitude            │
│ ...                  │
└──────────────────────┘
One caution

Before we populate the actual complaint types, use the exact official SWD list. The examples like Dirty Water, No Water, Malfunction Meter, etc. are the examples you've already mentioned, but we shouldn't assume they're the complete official taxonomy.

So yes: let's modify this first before we continue with the map.

The implementation sequence I'd use is:

1. Create divisions table
↓
2. Connect complaint_types to divisions
↓
3. Update Complaint model relationships
↓
4. Update create/edit validation
↓
5. Update ComplaintController
↓
6. Replace Category UI with Division + dependent Complaint Type dropdown
↓
7. Update index/show/manager/customer-service views
↓
8. Test Engineering Operation → only Engineering complaint types
↓
9. Test Commercial Services → only Commercial complaint types
↓
10. Then return to the map/reverse-geocoding modification
