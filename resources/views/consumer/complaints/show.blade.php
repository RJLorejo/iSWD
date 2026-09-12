@extends('consumer.layouts.app')

@section('title', 'Complaint Details')

@section('content')

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div
            class="
                flex flex-col
                lg:flex-row
                lg:items-center
                lg:justify-between
                gap-4 mb-8
            ">

            <div>
            <a href="{{ route('consumer.complaints.index') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-800">

                <i class="fas fa-arrow-left"></i>

                Back to Complaint

            </a>

                <div class="flex items-center gap-3">

                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">

                            {{ $complaint->complaint_no }}

                        </h1>

                    </div>

                </div>


                <p class="text-sm text-slate-500 mt-3">

                    Submitted
                    {{ optional($complaint->created_at)->format('F d, Y h:i A') }}

                </p>

            </div>


            {{-- STATUS + EDIT --}}

            <div class="flex items-center gap-3">

                @php

                    $statusClasses = match ($complaint->status) {
                        'Pending' => 'bg-amber-100 text-amber-800',

                        'Verified' => 'bg-sky-100 text-sky-800',

                        'Assigned' => 'bg-indigo-100 text-indigo-800',

                        'In Progress' => 'bg-blue-100 text-blue-800',

                        'Completed' => 'bg-emerald-100 text-emerald-800',

                        'Closed' => 'bg-slate-100 text-slate-700',

                        'Rejected' => 'bg-red-100 text-red-800',

                        default => 'bg-slate-100 text-slate-700',
                    };

                @endphp


                <span
                    class="
                        inline-flex items-center
                        px-4 py-2
                        rounded-full
                        text-sm font-semibold
                        {{ $statusClasses }}
                    ">

                    {{ $complaint->status }}

                </span>


                @if ($complaint->status === 'Pending')
                    <a href="{{ route('consumer.complaints.edit', $complaint) }}"
                        class="
                            inline-flex items-center gap-2
                            px-4 py-2
                            rounded-xl
                            bg-sky-700
                            text-white
                            text-sm
                            font-semibold
                            hover:bg-sky-800
                            transition
                        ">

                        <i class="fas fa-pencil-alt"></i>
                        Edit Complaint

                    </a>
                @endif

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- MAIN GRID --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


            {{-- ===================================================== --}}
            {{-- LEFT / MAIN CONTENT --}}
            {{-- ===================================================== --}}

            <div class="lg:col-span-2 space-y-6">


                {{-- ================================================= --}}
                {{-- COMPLAINT DETAILS --}}
                {{-- ================================================= --}}

                <section
                    class="
                        bg-white rounded-3xl
                        border border-slate-200
                        shadow-sm
                        p-6 md:p-8
                    ">

                    <div class="mb-6">

                        <h2 class="text-lg font-bold text-slate-800">
                            Complaint Details
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Information you submitted about your concern.
                        </p>

                    </div>


                    <div class="space-y-6">


                        {{-- CATEGORY --}}

                        <div>

                            <p
                                class="
                                    text-xs font-semibold
                                    uppercase tracking-wide
                                    text-slate-400
                                ">
                                Category
                            </p>

                            <p class="text-sm font-semibold text-slate-800 mt-1">

                                {{ optional($complaint->category)->name ?? 'Water Service Concern' }}

                            </p>

                        </div>


                        {{-- SUBJECT --}}

                        <div>

                            <p
                                class="
                                    text-xs font-semibold
                                    uppercase tracking-wide
                                    text-slate-400
                                ">
                                Short Description
                            </p>

                            <p class="text-base font-semibold text-slate-800 mt-1">

                                {{ $complaint->subject }}

                            </p>

                        </div>


                        {{-- DESCRIPTION --}}

                        <div>

                            <p
                                class="
                                    text-xs font-semibold
                                    uppercase tracking-wide
                                    text-slate-400
                                ">
                                What Happened
                            </p>

                            <p
                                class="
                                    text-sm text-slate-600
                                    mt-2 leading-7
                                    whitespace-pre-line
                                ">

                                {{ $complaint->description }}

                            </p>

                        </div>


                    </div>

                </section>


                {{-- ================================================= --}}
                {{-- SERVICE LOCATION --}}
                {{-- ================================================= --}}

                <section
                    class="
                        bg-white rounded-3xl
                        border border-slate-200
                        shadow-sm
                        p-6 md:p-8
                    ">

                    <div class="mb-6">

                        <h2 class="text-lg font-bold text-slate-800">
                            Service Location
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Location information provided for this concern.
                        </p>

                    </div>


                    {{-- ADDRESS --}}

                    <div class="mb-5">

                        <p
                            class="
                                text-xs font-semibold
                                uppercase tracking-wide
                                text-slate-400
                            ">
                            Address
                        </p>

                        <p
                            class="
                                text-sm text-slate-700
                                mt-1 whitespace-pre-line
                            ">

                            {{ $complaint->address }}

                        </p>

                    </div>


                    {{-- LANDMARK --}}

                    @if ($complaint->landmark)
                        <div class="mb-6">

                            <p
                                class="
                                    text-xs font-semibold
                                    uppercase tracking-wide
                                    text-slate-400
                                ">
                                Nearby Landmark
                            </p>

                            <p class="text-sm text-slate-700 mt-1">

                                {{ $complaint->landmark }}

                            </p>

                        </div>
                    @endif


                    {{-- ================================================= --}}
                    {{-- MAP --}}
                    {{-- ================================================= --}}

                    @if ($complaint->latitude !== null && $complaint->longitude !== null)
                        <div>

                            <div class="flex items-center justify-between mb-3">

                                <label
                                    class="
                                        block text-sm font-semibold
                                        text-slate-700
                                    ">

                                    Map Location

                                </label>


                                <a href="https://www.google.com/maps/search/?api=1&query={{ $complaint->latitude }},{{ $complaint->longitude }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="
                                        text-sm font-semibold
                                        text-sky-600
                                        hover:text-sky-800
                                    ">

                                    🗺️ Open in Google Maps

                                </a>

                            </div>


                            {{-- EXACT SAME MAP STYLE AS CREATE --}}

                            <div id="complaintMap"
                                class="
                                    w-full h-80
                                    rounded-2xl
                                    border border-slate-300
                                    overflow-hidden
                                    bg-slate-100
                                ">
                            </div>


                            <p class="text-xs text-slate-500 mt-3">

                                Complaint location:
                                {{ $complaint->latitude }},
                                {{ $complaint->longitude }}

                            </p>

                        </div>
                    @else
                        <div
                            class="
                                rounded-2xl
                                border border-slate-200
                                bg-slate-50
                                p-5
                            ">

                            <div class="flex items-start gap-3">

                                <div
                                    class="
                                        w-10 h-10
                                        rounded-xl
                                        bg-white
                                        flex items-center justify-center
                                    ">
                                    📍
                                </div>

                                <div>

                                    <p class="font-semibold text-slate-700">
                                        No map location available
                                    </p>

                                    <p class="text-sm text-slate-500 mt-1">

                                        No GPS coordinates were provided
                                        for this complaint.

                                    </p>

                                </div>

                            </div>

                        </div>
                    @endif


                </section>


                {{-- ================================================= --}}
                {{-- ASSIGNED MAINTENANCE TEAM --}}
                {{-- ================================================= --}}

                @if ($complaint->technicians->count())

                    <section
                        class="
                            bg-white rounded-3xl
                            border border-slate-200
                            shadow-sm
                            p-6 md:p-8
                        ">

                        <div class="mb-6">

                            <h2 class="text-lg font-bold text-slate-800">
                                Assigned Maintenance Team
                            </h2>

                            <p class="text-sm text-slate-500 mt-1">

                                Maintenance personnel assigned to handle
                                your concern.

                            </p>

                        </div>


                        <div class="space-y-3">

                            @foreach ($complaint->technicians as $technician)
                                <div
                                    class="
                                        flex items-center gap-4
                                        rounded-2xl
                                        border border-slate-200
                                        p-4
                                        bg-slate-50
                                    ">

                                    <div
                                        class="
                                            w-11 h-11
                                            rounded-full
                                            bg-sky-100
                                            text-sky-700
                                            flex items-center justify-center
                                            font-bold
                                        ">

                                        {{ strtoupper(substr($technician->first_name ?? '', 0, 1) . substr($technician->last_name ?? '', 0, 1)) }}

                                    </div>


                                    <div>

                                        <p class="font-semibold text-slate-800">

                                            {{ $technician->full_name }}

                                        </p>

                                        <p class="text-xs text-slate-500">

                                            Maintenance Technician

                                        </p>

                                    </div>

                                </div>
                            @endforeach

                        </div>

                    </section>

                @endif


                {{-- ================================================= --}}
                {{-- PHOTO EVIDENCE --}}
                {{-- ================================================= --}}

                @if ($complaint->photo)
                    <section
                        class="
                            bg-white rounded-3xl
                            border border-slate-200
                            shadow-sm
                            p-6 md:p-8
                        ">

                        <div class="mb-6">

                            <h2 class="text-lg font-bold text-slate-800">
                                Photo Evidence
                            </h2>

                            <p class="text-sm text-slate-500 mt-1">
                                Photo submitted with this complaint.
                            </p>

                        </div>


                        <img src="{{ asset('storage/' . $complaint->photo) }}" alt="Complaint photo"
                            class="
                                w-full
                                max-h-[500px]
                                object-contain
                                rounded-2xl
                                border border-slate-200
                                bg-slate-50
                            ">

                    </section>
                @endif


                {{-- ================================================= --}}
                {{-- AI SUPPORT --}}
                {{-- ================================================= --}}

                <section
                    class="
                        bg-sky-50
                        border border-sky-100
                        rounded-3xl
                        p-6 md:p-8
                    ">

                    <div class="flex items-start gap-4">

                        <div
                            class="
                                w-11 h-11
                                rounded-xl
                                bg-white
                                flex items-center justify-center
                                text-xl
                                shadow-sm
                                flex-shrink-0
                            ">
                            <i class="fas fa-robot text-sky-600"></i>
                        </div>


                        <div>

                            <h2 class="font-bold text-sky-900">
                                AI Complaint Support
                            </h2>

                            <p
                                class="
                                    text-sm text-sky-800
                                    mt-2 leading-6
                                ">

                                The system can assist in understanding
                                your concern and providing relevant
                                service information. Final complaint
                                decisions and actions are handled by
                                Sagay Water District personnel.

                            </p>

                        </div>

                    </div>

                </section>


                {{-- ================================================= --}}
                {{-- COMPLETION --}}
                {{-- ================================================= --}}

                @if (in_array($complaint->status, ['Completed', 'Closed']))

                    <section
                        class="
                            bg-emerald-50
                            border border-emerald-200
                            rounded-3xl
                            p-6 md:p-8
                        ">

                        <div class="flex items-start gap-4">

                            <div
                                class="
                                    w-11 h-11
                                    rounded-xl
                                    bg-white
                                    flex items-center justify-center
                                    text-xl
                                    flex-shrink-0
                                ">
                                ✓
                            </div>


                            <div>

                                <h2 class="font-bold text-emerald-900">

                                    Concern Completed

                                </h2>


                                <p
                                    class="
                                        text-sm text-emerald-800
                                        mt-2 leading-6
                                    ">

                                    Your complaint has been marked
                                    as completed by the Maintenance team.

                                </p>


                                @if ($complaint->completed_at)
                                    <p
                                        class="
                                            text-xs
                                            text-emerald-700
                                            mt-2
                                        ">

                                        Completed:
                                        {{ $complaint->completed_at->format('F d, Y h:i A') }}

                                    </p>
                                @endif


                                @if (Route::has('consumer.feedback.create'))
                                    <a href="{{ route('consumer.feedback.create', $complaint) }}"
                                        class="
                                            inline-flex
                                            items-center
                                            gap-2
                                            mt-5
                                            px-5 py-2.5
                                            rounded-xl
                                            bg-emerald-700
                                            text-white
                                            text-sm
                                            font-semibold
                                            hover:bg-emerald-800
                                            transition
                                        ">

                                        ⭐ Give Feedback

                                    </a>
                                @endif

                            </div>

                        </div>

                    </section>

                @endif

            </div>


            {{-- ===================================================== --}}
            {{-- RIGHT SIDEBAR --}}
            {{-- ===================================================== --}}

            <div class="space-y-6">


                {{-- ================================================= --}}
                {{-- STATUS TRACKING --}}
                {{-- ================================================= --}}

                <section
                    class="
                        bg-white rounded-3xl
                        border border-slate-200
                        shadow-sm
                        p-6
                    ">

                    <h2 class="text-lg font-bold text-slate-800">
                        Complaint Tracking
                    </h2>

                    <p class="text-sm text-slate-500 mt-1 mb-6">
                        Follow the progress of your concern.
                    </p>


                    @php

                        $statuses = [
                            'Pending' => 'Submitted',
                            'Verified' => 'Verified',
                            'Assigned' => 'Assigned',
                            'In Progress' => 'In Progress',
                            'Completed' => 'Completed',
                            'Closed' => 'Closed',
                        ];

                        $statusOrder = array_keys($statuses);

                        $currentIndex = array_search($complaint->status, $statusOrder);

                    @endphp


                    <div class="space-y-0">

                        @foreach ($statuses as $status => $label)
                            @php

                                $index = array_search($status, $statusOrder);

                                $isCompleted = $currentIndex !== false && $index <= $currentIndex;

                                $isCurrent = $complaint->status === $status;

                            @endphp


                            <div class="flex gap-3">

                                <div class="flex flex-col items-center">

                                    <div
                                        class="
                                            w-9 h-9
                                            rounded-full
                                            flex items-center justify-center
                                            text-sm font-bold
                                            {{ $isCompleted ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-400' }}
                                        ">

                                        @if ($isCompleted)
                                            ✓
                                        @else
                                            {{ $index + 1 }}
                                        @endif

                                    </div>


                                    @if (!$loop->last)
                                        <div
                                            class="
                                                w-px h-12
                                                {{ $isCompleted ? 'bg-sky-300' : 'bg-slate-200' }}
                                            ">
                                        </div>
                                    @endif

                                </div>


                                <div class="pt-1 pb-5">

                                    <p
                                        class="
                                            text-sm font-semibold
                                            {{ $isCurrent ? 'text-sky-700' : 'text-slate-700' }}
                                        ">

                                        {{ $label }}

                                    </p>


                                    @if ($status === 'Verified' && $complaint->verified_at)
                                        <p class="text-xs text-slate-400 mt-1">

                                            {{ $complaint->verified_at->format('M d, Y h:i A') }}

                                        </p>
                                    @endif


                                    @if ($status === 'Verified' && $complaint->verifier)
                                        <p class="text-xs text-slate-500 mt-1">

                                            Reviewed by
                                            {{ $complaint->verifier->full_name }}

                                        </p>
                                    @endif


                                    @if ($status === 'Assigned' && $complaint->technicians->count())
                                        <div class="mt-2 space-y-1">

                                            <p
                                                class="
                                                    text-xs font-semibold
                                                    text-slate-500
                                                ">
                                                Assigned technicians:
                                            </p>


                                            @foreach ($complaint->technicians as $technician)
                                                <p class="text-xs text-slate-500">

                                                    •
                                                    {{ $technician->full_name }}

                                                </p>
                                            @endforeach

                                        </div>
                                    @endif

                                </div>

                            </div>
                        @endforeach

                    </div>

                </section>


                {{-- ================================================= --}}
                {{-- QUICK INFORMATION --}}
                {{-- ================================================= --}}

                <section
                    class="
                        bg-white rounded-3xl
                        border border-slate-200
                        shadow-sm
                        p-6
                    ">

                    <h2 class="text-lg font-bold text-slate-800">
                        Complaint Information
                    </h2>


                    <div class="mt-5 space-y-4">

                        <div>

                            <p
                                class="
                                    text-xs
                                    uppercase
                                    tracking-wide
                                    font-semibold
                                    text-slate-400
                                ">
                                Complaint Number
                            </p>

                            <p class="text-sm font-semibold text-slate-800 mt-1">

                                {{ $complaint->complaint_no }}

                            </p>

                        </div>


                        <div>

                            <p
                                class="
                                    text-xs
                                    uppercase
                                    tracking-wide
                                    font-semibold
                                    text-slate-400
                                ">
                                Category
                            </p>

                            <p class="text-sm text-slate-700 mt-1">

                                {{ optional($complaint->category)->name ?? 'Water Service Concern' }}

                            </p>

                        </div>


                        <div>

                            <p
                                class="
                                    text-xs
                                    uppercase
                                    tracking-wide
                                    font-semibold
                                    text-slate-400
                                ">
                                Submitted
                            </p>

                            <p class="text-sm text-slate-700 mt-1">

                                {{ optional($complaint->created_at)->format('F d, Y') }}

                            </p>

                        </div>


                        @if ($complaint->technicians->count())
                            <div>

                                <p
                                    class="
                                        text-xs
                                        uppercase
                                        tracking-wide
                                        font-semibold
                                        text-slate-400
                                    ">
                                    Maintenance Team
                                </p>

                                <p class="text-sm text-slate-700 mt-1">

                                    {{ $complaint->technicians->count() }}
                                    technician{{ $complaint->technicians->count() > 1 ? 's' : '' }}

                                </p>

                            </div>
                        @endif

                    </div>

                </section>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- LEAFLET MAP --}}
    {{-- ========================================================= --}}

    @if ($complaint->latitude !== null && $complaint->longitude !== null)
        @push('scripts')
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const savedLat =
                        parseFloat(
                            @json($complaint->latitude)
                        );


                    const savedLng =
                        parseFloat(
                            @json($complaint->longitude)
                        );


                    if (
                        isNaN(savedLat) ||
                        isNaN(savedLng)
                    ) {

                        return;

                    }


                    const map =
                        L.map('complaintMap')
                        .setView(
                            [savedLat, savedLng],
                            14
                        );


                    L.tileLayer(
                        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap contributors'
                        }
                    ).addTo(map);


                    /*
                    |--------------------------------------------------------------------------
                    | Complaint marker
                    |--------------------------------------------------------------------------
                    */

                    const marker =
                        L.marker(
                            [
                                savedLat,
                                savedLng
                            ]
                        ).addTo(map);


                    marker.bindPopup(
                        '<strong>Complaint Location</strong>'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Leaflet rendering fix
                    |--------------------------------------------------------------------------
                    */

                    setTimeout(function() {

                        map.invalidateSize();

                    }, 300);

                });
            </script>
        @endpush
    @endif

@endsection
