@extends('technician.layouts.app')

@section('title', 'Complaint Details')

@section('content')

    @php
        $currentTechnician = $complaint->technicians->firstWhere('id', auth()->id());

        $myAssignmentStatus = $currentTechnician?->pivot?->status;

        $priorityClasses = match ($complaint->priority) {
            'Critical' => 'bg-red-100 text-red-700 border-red-200',
            'High' => 'bg-orange-100 text-orange-700 border-orange-200',
            'Medium' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            default => 'bg-green-100 text-green-700 border-green-200',
        };

        $statusClasses = match ($complaint->status) {
            'Assigned' => 'bg-blue-100 text-blue-700 border-blue-200',
            'In Progress' => 'bg-amber-100 text-amber-700 border-amber-200',
            'Completed' => 'bg-green-100 text-green-700 border-green-200',
            'Closed' => 'bg-gray-100 text-gray-700 border-gray-200',
            default => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    @endphp


    <div class="max-w-7xl mx-auto space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>

                <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">

                    <a href="{{ route('technician.complaints.index') }}" class="hover:text-indigo-600 transition">
                        My Complaints
                    </a>

                    <i class="fas fa-chevron-right text-[10px]"></i>

                    <span class="font-medium text-gray-700">
                        {{ $complaint->complaint_no }}
                    </span>

                </div>

                <div class="flex flex-wrap items-center gap-3 mt-3">

                    <span class="text-sm font-semibold text-indigo-600">
                        Maintenance Work
                    </span>

                    <span class="text-gray-300">
                        •
                    </span>

                    <span class="text-sm text-gray-500">
                        {{ $complaint->category?->name ?? 'Uncategorized' }}
                    </span>

                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">
                    {{ $complaint->subject }}
                </h1>

                <p class="text-sm text-gray-500 mt-2">
                    Complaint {{ $complaint->complaint_no }}
                    · Review the service request and perform the assigned maintenance work.
                </p>

            </div>


            <a href="{{ route('technician.complaints.index') }}"
                class="inline-flex items-center justify-center gap-2
                   px-4 py-2.5 rounded-xl
                   border border-gray-300
                   bg-white text-gray-700
                   hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left"></i>
                Back to My Complaints
            </a>

        </div>


        {{-- ========================================================= --}}
        {{-- FLASH MESSAGES --}}
        {{-- ========================================================= --}}

        @if (session('success'))
            <div
                class="flex items-start gap-3 p-4 rounded-xl
                    bg-green-50 border border-green-200 text-green-800">

                <i class="fas fa-circle-check mt-0.5"></i>

                <div>
                    <p class="font-semibold">
                        Success
                    </p>

                    <p class="text-sm mt-0.5">
                        {{ session('success') }}
                    </p>
                </div>

            </div>
        @endif


        @if (session('error'))
            <div
                class="flex items-start gap-3 p-4 rounded-xl
                    bg-red-50 border border-red-200 text-red-800">

                <i class="fas fa-circle-exclamation mt-0.5"></i>

                <div>
                    <p class="font-semibold">
                        Action unavailable
                    </p>

                    <p class="text-sm mt-0.5">
                        {{ session('error') }}
                    </p>
                </div>

            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- WORK STATUS / ACTION --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="p-5 sm:p-6">

                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">

                    {{-- Status --}}
                    <div class="flex items-start gap-4">

                        <div
                            class="w-14 h-14 shrink-0 rounded-2xl
                                flex items-center justify-center
                                @if ($complaint->status === 'Assigned') bg-blue-100 text-blue-600
                                @elseif($complaint->status === 'In Progress')
                                    bg-amber-100 text-amber-600
                                @elseif($complaint->status === 'Completed')
                                    bg-green-100 text-green-600
                                @else
                                    bg-gray-100 text-gray-600 @endif">

                            @if ($complaint->status === 'Assigned')
                                <i class="fas fa-clipboard-list text-xl"></i>
                            @elseif($complaint->status === 'In Progress')
                                <i class="fas fa-screwdriver-wrench text-xl"></i>
                            @elseif($complaint->status === 'Completed')
                                <i class="fas fa-circle-check text-xl"></i>
                            @else
                                <i class="fas fa-circle-info text-xl"></i>
                            @endif

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Maintenance Status
                            </p>

                            <div class="flex flex-wrap items-center gap-2 mt-1">

                                <span
                                    class="inline-flex items-center px-3 py-1.5
                                         rounded-full border
                                         text-sm font-semibold
                                         {{ $statusClasses }}">

                                    {{ $complaint->status }}

                                </span>


                                @if ($complaint->priority)

                                    <span
                                        class="inline-flex items-center px-3 py-1.5
                                             rounded-full border
                                             text-sm font-semibold
                                             {{ $priorityClasses }}">

                                        @if ($complaint->priority === 'Critical')
                                            <i class="fas fa-triangle-exclamation mr-1.5"></i>
                                        @endif

                                        {{ $complaint->priority }}

                                    </span>

                                @endif

                            </div>


                            {{-- Technician's assignment state --}}
                            @if ($myAssignmentStatus)
                                <p class="text-sm text-gray-500 mt-3">

                                    Your assignment status:

                                    <span class="font-semibold text-gray-800">
                                        {{ $myAssignmentStatus }}
                                    </span>

                                </p>
                            @endif

                        </div>

                    </div>


                    {{-- Actions --}}
                    <div class="flex flex-wrap gap-3">

                        {{-- START --}}
                        @if ($complaint->status === 'Assigned' && $myAssignmentStatus === 'Assigned')
                            <form method="POST" action="{{ route('technician.maintenance-reports.start', $complaint) }}">
                                @csrf

                                <button type="submit"
                                    class="inline-flex items-center gap-2
               px-5 py-3 rounded-xl
               bg-blue-600 text-white
               font-semibold
               hover:bg-blue-700
               transition shadow-sm">

                                    <i class="fas fa-play"></i>

                                    Start Maintenance

                                </button>
                            </form>
                        @endif


                        {{-- CONTINUE --}}
                        @if ($complaint->status === 'In Progress')
                            <a href="{{ route('technician.maintenance-reports.create', $complaint) }}"
                                class="inline-flex items-center gap-2
                                   px-5 py-3 rounded-xl
                                   bg-indigo-600 text-white
                                   font-semibold
                                   hover:bg-indigo-700
                                   transition shadow-sm">

                                <i class="fas fa-file-pen"></i>

                                Continue Maintenance Report

                            </a>
                        @endif


                        {{-- COMPLETED --}}
                        @if ($complaint->status === 'Completed' && $complaint->maintenanceReport)
                            <a href="{{ route('technician.maintenance-reports.show', $complaint) }}"
                                class="inline-flex items-center gap-2
                                   px-5 py-3 rounded-xl
                                   bg-green-600 text-white
                                   font-semibold
                                   hover:bg-green-700
                                   transition shadow-sm">

                                <i class="fas fa-file-circle-check"></i>

                                View Submitted Report

                            </a>


                            <a href="{{ route('technician.maintenance-reports.print', $complaint) }}" target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2
                                   px-5 py-3 rounded-xl
                                   bg-gray-800 text-white
                                   font-semibold
                                   hover:bg-gray-900
                                   transition shadow-sm">

                                <i class="fas fa-print"></i>

                                Print

                        @endif

                    </div>

                </div>

            </div>


            {{-- Manager validation notice --}}
            @if ($complaint->status === 'Completed')
                <div class="border-t border-green-100 bg-green-50 px-5 sm:px-6 py-4">

                    <div class="flex items-start gap-3">

                        <div
                            class="w-9 h-9 rounded-lg bg-green-100
                                text-green-600 flex items-center justify-center shrink-0">

                            <i class="fas fa-user-shield"></i>

                        </div>

                        <div>

                            <p class="font-semibold text-green-900">
                                Maintenance work submitted
                            </p>

                            <p class="text-sm text-green-800 mt-1">
                                The maintenance work and report have been submitted.
                                The Maintenance Manager will review and validate the completed work.
                            </p>

                        </div>

                    </div>

                </div>
            @endif

        </div>


        {{-- ========================================================= --}}
        {{-- MAIN GRID --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">


            {{-- ===================================================== --}}
            {{-- MAIN CONTENT --}}
            {{-- ===================================================== --}}

            <div class="xl:col-span-2 space-y-6">


                {{-- ================================================= --}}
                {{-- COMPLAINT DETAILS --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-file-lines text-indigo-600 mr-2"></i>

                            Complaint Details

                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Information submitted by the consumer.
                        </p>

                    </div>


                    <div class="p-5 sm:p-6 space-y-6">


                        {{-- Subject --}}
                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  text-gray-500 font-semibold">

                                Subject

                            </p>

                            <h3 class="text-xl font-bold text-gray-900 mt-1">
                                {{ $complaint->subject }}
                            </h3>

                        </div>


                        {{-- Description --}}
                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  text-gray-500 font-semibold">

                                Description

                            </p>

                            <div
                                class="mt-2 p-4 rounded-xl
                                    bg-gray-50 border border-gray-100">

                                <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                                    {{ $complaint->description }}
                                </p>

                            </div>

                        </div>


                        {{-- Category / Priority --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold">

                                    Complaint Category

                                </p>

                                <div class="flex items-center gap-3 mt-2">

                                    <div
                                        class="w-10 h-10 rounded-xl
                                            bg-indigo-50 text-indigo-600
                                            flex items-center justify-center">

                                        <i class="fas fa-layer-group"></i>

                                    </div>

                                    <div>

                                        <p class="font-semibold text-gray-900">

                                            {{ $complaint->category?->name ?? 'Uncategorized' }}

                                        </p>

                                        @if ($complaint->category?->description)
                                            <p class="text-xs text-gray-500 mt-0.5">

                                                {{ $complaint->category->description }}

                                            </p>
                                        @endif

                                    </div>

                                </div>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold">

                                    Priority

                                </p>

                                <span
                                    class="inline-flex items-center gap-2
                                       px-3 py-1.5 rounded-full
                                       border text-sm font-semibold
                                       {{ $priorityClasses }} mt-2">

                                    @if ($complaint->priority === 'Critical')
                                        <i class="fas fa-triangle-exclamation"></i>
                                    @elseif($complaint->priority === 'High')
                                        <i class="fas fa-arrow-up"></i>
                                    @else
                                        <i class="fas fa-circle-info"></i>
                                    @endif

                                    {{ $complaint->priority ?? 'Not set' }}

                                </span>

                            </div>

                        </div>


                        {{-- Complaint Photo --}}
                        @if ($complaint->photo)
                            <div class="border-t border-gray-100 pt-6">

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold mb-3">

                                    Consumer-Submitted Photo

                                </p>

                                <div
                                    class="rounded-2xl overflow-hidden
                                        border border-gray-200 bg-gray-50">

                                    <img src="{{ asset('storage/' . $complaint->photo) }}"
                                        alt="Consumer submitted complaint photo"
                                        class="w-full max-h-[500px] object-contain">

                                </div>

                            </div>
                        @endif

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- SERVICE LOCATION --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                            <div>

                                <h2 class="font-semibold text-gray-900">

                                    <i class="fas fa-map-location-dot text-indigo-600 mr-2"></i>

                                    Service Location

                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Use the address and map coordinates to locate the service point.
                                </p>

                            </div>


                            @if ($complaint->latitude && $complaint->longitude)
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $complaint->latitude }},{{ $complaint->longitude }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center
                                       gap-2 px-4 py-2.5 rounded-xl
                                       bg-indigo-600 text-white
                                       hover:bg-indigo-700 transition">

                                    <i class="fas fa-route"></i>

                                    Get Directions

                                </a>
                            @endif

                        </div>

                    </div>


                    <div class="p-5 sm:p-6 space-y-5">

                        {{-- Address --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold">

                                    Address

                                </p>

                                <div class="flex items-start gap-3 mt-2">

                                    <div
                                        class="w-10 h-10 shrink-0 rounded-xl
                                            bg-gray-100 text-gray-500
                                            flex items-center justify-center">

                                        <i class="fas fa-location-dot"></i>

                                    </div>

                                    <p class="text-gray-700 leading-relaxed">
                                        {{ $complaint->address }}
                                    </p>

                                </div>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold">

                                    Landmark

                                </p>

                                <div class="flex items-start gap-3 mt-2">

                                    <div
                                        class="w-10 h-10 shrink-0 rounded-xl
                                            bg-gray-100 text-gray-500
                                            flex items-center justify-center">

                                        <i class="fas fa-landmark"></i>

                                    </div>

                                    <p class="text-gray-700">
                                        {{ $complaint->landmark ?: 'No landmark provided' }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- Map --}}
                        @if ($complaint->latitude && $complaint->longitude)
                            <div id="complaint-map"
                                class="w-full h-[350px] sm:h-[430px]
                                   rounded-2xl overflow-hidden
                                   border border-gray-200 z-0">
                            </div>


                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                                <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">

                                    <p class="text-xs text-gray-500">
                                        Latitude
                                    </p>

                                    <p class="font-mono text-sm font-semibold text-gray-900 mt-1">
                                        {{ $complaint->latitude }}
                                    </p>

                                </div>


                                <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">

                                    <p class="text-xs text-gray-500">
                                        Longitude
                                    </p>

                                    <p class="font-mono text-sm font-semibold text-gray-900 mt-1">
                                        {{ $complaint->longitude }}
                                    </p>

                                </div>

                            </div>
                        @else
                            <div
                                class="rounded-xl border border-yellow-200
                                    bg-yellow-50 p-4">

                                <div class="flex items-start gap-3">

                                    <i
                                        class="fas fa-map-location-dot
                                          text-yellow-600 mt-0.5"></i>

                                    <div>

                                        <p class="font-semibold text-yellow-900">
                                            Map location unavailable
                                        </p>

                                        <p class="text-sm text-yellow-800 mt-1">
                                            No latitude and longitude coordinates were provided.
                                            Use the address and landmark to locate the service point.
                                        </p>

                                    </div>

                                </div>

                            </div>
                        @endif

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- VERIFICATION --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-shield-check text-green-600 mr-2"></i>

                            Customer Service Verification

                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Verification information recorded before maintenance assignment.
                        </p>

                    </div>


                    <div class="p-5 sm:p-6">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold">

                                    Verified By

                                </p>

                                <div class="flex items-center gap-3 mt-2">

                                    <div
                                        class="w-10 h-10 rounded-full
                                            bg-green-100 text-green-600
                                            flex items-center justify-center">

                                        <i class="fas fa-user-check"></i>

                                    </div>

                                    <div>

                                        <p class="font-semibold text-gray-900">

                                            {{ $complaint->verifier?->full_name ?? 'Not verified' }}

                                        </p>

                                        @if ($complaint->verified_at)
                                            <p class="text-xs text-gray-500 mt-0.5">

                                                {{ $complaint->verified_at->format('M d, Y h:i A') }}

                                            </p>
                                        @endif

                                    </div>

                                </div>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold">

                                    Verification Status

                                </p>

                                @if ($complaint->verified_at)
                                    <span
                                        class="inline-flex items-center gap-2
                                             px-3 py-1.5 rounded-full
                                             bg-green-100 text-green-700
                                             text-sm font-semibold mt-2">

                                        <i class="fas fa-circle-check"></i>

                                        Verified

                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-2
                                             px-3 py-1.5 rounded-full
                                             bg-gray-100 text-gray-600
                                             text-sm font-semibold mt-2">

                                        <i class="fas fa-clock"></i>

                                        Not Verified

                                    </span>
                                @endif

                            </div>

                        </div>


                        @if ($complaint->verification_reason)
                            <div class="mt-6 pt-5 border-t border-gray-100">

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold">

                                    Verification Notes

                                </p>

                                <div
                                    class="mt-2 p-4 rounded-xl
                                        bg-gray-50 border border-gray-100">

                                    <p class="text-gray-700 whitespace-pre-line">
                                        {{ $complaint->verification_reason }}
                                    </p>

                                </div>

                            </div>
                        @endif

                    </div>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- RIGHT SIDEBAR --}}
            {{-- ===================================================== --}}

            <div class="space-y-6">


                {{-- ================================================= --}}
                {{-- CONSUMER --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-user text-indigo-600 mr-2"></i>

                            Consumer Information

                        </h2>

                    </div>


                    <div class="p-5 space-y-5">

                        <div>

                            <p class="text-xs text-gray-500">
                                Consumer
                            </p>

                            <p class="font-semibold text-gray-900 mt-1">

                                {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Walk-in / Unregistered') }}

                            </p>

                        </div>


                        <div>

                            <p class="text-xs text-gray-500">
                                Account Number
                            </p>

                            <p class="font-semibold text-gray-900 mt-1">

                                {{ $complaint->consumer?->account_number ?? 'Walk-in / Unregistered' }}

                            </p>

                        </div>


                        <div>

                            <p class="text-xs text-gray-500">
                                Contact Number
                            </p>

                            <p class="font-medium text-gray-900 mt-1">

                                {{ $complaint->complainant_phone ?? ($complaint->consumer?->phone ?? '—') }}

                            </p>

                        </div>


                        <div class="pt-4 border-t border-gray-100">

                            <p class="text-xs text-gray-500">
                                Complaint Submitted
                            </p>

                            <p class="font-medium text-gray-900 mt-1">

                                {{ $complaint->created_at?->format('M d, Y h:i A') ?? '—' }}

                            </p>

                        </div>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- MAINTENANCE TEAM --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-gray-100">

                        <div class="flex items-center justify-between gap-3">

                            <div>

                                <h2 class="font-semibold text-gray-900">

                                    <i class="fas fa-users-gear text-indigo-600 mr-2"></i>

                                    Maintenance Team

                                </h2>

                                <p class="text-xs text-gray-500 mt-1">
                                    Technicians assigned to this complaint.
                                </p>

                            </div>


                            <span
                                class="inline-flex items-center justify-center
                                     min-w-8 h-8 px-2 rounded-full
                                     bg-indigo-50 text-indigo-700
                                     text-sm font-bold">

                                {{ $complaint->technicians->count() }}

                            </span>

                        </div>

                    </div>


                    <div class="p-5">

                        @if ($complaint->technicians->count())

                            <div class="space-y-3">

                                @foreach ($complaint->technicians as $technician)
                                    <div
                                        class="flex items-center gap-3 p-3 rounded-xl
                                    {{ $technician->id === auth()->id()
                                        ? 'bg-indigo-50 border border-indigo-200'
                                        : 'bg-gray-50 border border-gray-100' }}">

                                        <div
                                            class="w-10 h-10 rounded-full
                                                bg-indigo-100 text-indigo-700
                                                flex items-center justify-center
                                                font-bold shrink-0">

                                            {{ strtoupper(substr($technician->first_name ?? 'T', 0, 1)) }}

                                        </div>


                                        <div class="min-w-0 flex-1">

                                            <div class="flex flex-wrap items-center gap-2">

                                                <p class="font-semibold text-gray-900 truncate">

                                                    {{ $technician->full_name }}

                                                </p>

                                                @if ($technician->id === auth()->id())
                                                    <span
                                                        class="px-2 py-0.5 rounded-full
                                                             bg-indigo-600 text-white
                                                             text-[10px] font-bold">

                                                        YOU

                                                    </span>
                                                @endif

                                            </div>


                                            <p class="text-xs text-gray-500 mt-0.5">

                                                Maintenance Technician

                                            </p>


                                            @if ($technician->pivot?->status)
                                                <p class="text-xs text-gray-600 mt-1">

                                                    Status:

                                                    <span class="font-semibold">
                                                        {{ $technician->pivot->status }}
                                                    </span>

                                                </p>
                                            @endif

                                        </div>

                                    </div>
                                @endforeach

                            </div>
                        @else
                            <div class="text-center py-6">

                                <div
                                    class="w-12 h-12 mx-auto rounded-xl
                                        bg-gray-100 text-gray-400
                                        flex items-center justify-center">

                                    <i class="fas fa-users-slash"></i>

                                </div>

                                <p class="font-semibold text-gray-700 mt-3">
                                    No maintenance team assigned
                                </p>

                            </div>

                        @endif

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- CUSTOMER SERVICE --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-headset text-indigo-600 mr-2"></i>

                            Customer Service

                        </h2>

                    </div>


                    <div class="p-5">

                        <p class="text-xs text-gray-500">
                            Verified / handled by
                        </p>

                        <p class="font-semibold text-gray-900 mt-1">

                            {{ $complaint->verifier?->full_name ?? '—' }}

                        </p>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- WORK INFORMATION --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-clipboard-check text-indigo-600 mr-2"></i>

                            Work Information

                        </h2>

                    </div>


                    <div class="p-5 space-y-5">

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  text-gray-500 font-semibold">

                                Assigned Date

                            </p>

                            <p class="font-medium text-gray-900 mt-1">

                                @php
                                    $assignmentDate = $complaint->technicians
                                        ->pluck('pivot.assigned_at')
                                        ->filter()
                                        ->sort()
                                        ->first();
                                @endphp

                                {{ $assignmentDate ? \Carbon\Carbon::parse($assignmentDate)->format('M d, Y h:i A') : '—' }}

                            </p>

                        </div>


                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  text-gray-500 font-semibold">

                                Your Work Status

                            </p>

                            <p class="font-semibold text-gray-900 mt-1">

                                {{ $myAssignmentStatus ?? '—' }}

                            </p>

                        </div>


                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  text-gray-500 font-semibold">

                                Last Updated

                            </p>

                            <p class="font-medium text-gray-900 mt-1">

                                {{ $complaint->updated_at?->format('M d, Y h:i A') ?? '—' }}

                            </p>

                        </div>


                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  text-gray-500 font-semibold">

                                Maintenance Report

                            </p>

                            @if ($complaint->maintenanceReport)
                                <span
                                    class="inline-flex items-center gap-2
                                         px-3 py-1.5 mt-2 rounded-full
                                         bg-green-100 text-green-700
                                         text-xs font-semibold">

                                    <i class="fas fa-file-circle-check"></i>

                                    Report Available

                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-2
                                         px-3 py-1.5 mt-2 rounded-full
                                         bg-gray-100 text-gray-600
                                         text-xs font-semibold">

                                    <i class="fas fa-file"></i>

                                    No Report Yet

                                </span>
                            @endif

                        </div>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- WORK TIMELINE --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-timeline text-indigo-600 mr-2"></i>

                            Work Progress

                        </h2>

                    </div>


                    <div class="p-5">

                        <div class="relative">

                            <div
                                class="absolute left-4 top-3 bottom-3
                                    w-px bg-gray-200">
                            </div>


                            <div class="space-y-6">


                                {{-- Submitted --}}
                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                            bg-gray-100 text-gray-500
                                            flex items-center justify-center">

                                        <i class="fas fa-file-circle-plus text-xs"></i>

                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            Complaint Submitted
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $complaint->created_at?->format('M d, Y h:i A') }}
                                        </p>

                                    </div>

                                </div>


                                {{-- Verified --}}
                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                    {{ $complaint->verified_at ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                    flex items-center justify-center">

                                        <i class="fas fa-shield-check text-xs"></i>

                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            Verified by Customer Service
                                        </p>

                                        @if ($complaint->verified_at)

                                            <p class="text-xs text-green-600 mt-1">
                                                {{ $complaint->verified_at->format('M d, Y h:i A') }}
                                            </p>

                                            @if ($complaint->verifier)
                                                <p class="text-xs text-gray-500 mt-1">

                                                    By
                                                    <span class="font-medium">
                                                        {{ $complaint->verifier->full_name }}
                                                    </span>

                                                </p>
                                            @endif
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Not verified
                                            </p>

                                        @endif

                                    </div>

                                </div>


                                {{-- Assigned --}}
                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                    {{ $complaint->technicians->count() ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' }}
                                    flex items-center justify-center">

                                        <i class="fas fa-users-gear text-xs"></i>

                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            Maintenance Team Assigned
                                        </p>

                                        @if ($complaint->technicians->count())
                                            <p class="text-xs text-blue-600 mt-1">

                                                {{ $complaint->technicians->count() }}
                                                technician(s) assigned

                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                No maintenance team assigned
                                            </p>
                                        @endif

                                    </div>

                                </div>


                                {{-- Current status --}}
                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                    @if ($complaint->status === 'Completed') bg-green-100 text-green-600
                                    @elseif($complaint->status === 'In Progress')
                                        bg-amber-100 text-amber-600
                                    @else
                                        bg-blue-100 text-blue-600 @endif
                                    flex items-center justify-center">

                                        <i class="fas fa-screwdriver-wrench text-xs"></i>

                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            {{ $complaint->status }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">

                                            @if ($complaint->status === 'Assigned')
                                                Waiting for maintenance work
                                            @elseif($complaint->status === 'In Progress')
                                                Maintenance work is currently in progress
                                            @elseif($complaint->status === 'Completed')
                                                Maintenance work has been submitted for manager validation
                                            @else
                                                Current maintenance status
                                            @endif

                                        </p>

                                    </div>

                                </div>


                                {{-- Report --}}
                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                    {{ $complaint->maintenanceReport ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                    flex items-center justify-center">

                                        <i class="fas fa-file-circle-check text-xs"></i>

                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            Maintenance Report
                                        </p>

                                        @if ($complaint->maintenanceReport)
                                            <p class="text-xs text-green-600 mt-1">
                                                Report submitted
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Report not submitted yet
                                            </p>
                                        @endif

                                    </div>

                                </div>


                                {{-- Manager validation --}}
                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                            bg-gray-100 text-gray-400
                                            flex items-center justify-center">

                                        <i class="fas fa-user-shield text-xs"></i>

                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            Manager Validation
                                        </p>

                                        <p class="text-xs text-gray-400 mt-1">
                                            Pending manager review after maintenance submission
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- NAVIGATION --}}
                {{-- ================================================= --}}

                @if ($complaint->latitude && $complaint->longitude)
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">

                        <div class="p-5">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-10 h-10 shrink-0 rounded-xl
                                        bg-indigo-100 text-indigo-600
                                        flex items-center justify-center">

                                    <i class="fas fa-route"></i>

                                </div>

                                <div>

                                    <p class="font-semibold text-gray-900">
                                        Navigate to Service Location
                                    </p>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Use the recorded complaint coordinates for field inspection.
                                    </p>

                                </div>

                            </div>


                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $complaint->latitude }},{{ $complaint->longitude }}"
                                target="_blank" rel="noopener noreferrer"
                                class="mt-4 w-full inline-flex items-center
                                   justify-center gap-2
                                   px-4 py-2.5 rounded-xl
                                   border border-indigo-200
                                   bg-indigo-50 text-indigo-700
                                   hover:bg-indigo-100 transition">

                                <i class="fas fa-map-marked-alt"></i>

                                Open Navigation

                            </a>

                        </div>

                    </div>
                @endif

            </div>

        </div>

    </div>


    {{-- ============================================================= --}}
    {{-- LEAFLET --}}
    {{-- ============================================================= --}}

    @if ($complaint->latitude && $complaint->longitude)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const latitude = {{ (float) $complaint->latitude }};
                const longitude = {{ (float) $complaint->longitude }};

                const mapElement = document.getElementById('complaint-map');

                if (!mapElement) {
                    return;
                }

                const map = L.map('complaint-map', {
                    scrollWheelZoom: false
                }).setView(
                    [latitude, longitude],
                    16
                );


                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }
                ).addTo(map);


                const marker = L.marker([
                    latitude,
                    longitude
                ]).addTo(map);


                marker.bindPopup(`
                <div style="min-width:220px">

                    <strong style="font-size:14px">
                        {{ addslashes($complaint->complaint_no) }}
                    </strong>

                    <br>

                    <span style="font-size:13px">
                        {{ addslashes($complaint->subject) }}
                    </span>

                    <br><br>

                    <span style="font-size:12px;color:#6b7280">
                        {{ addslashes($complaint->address) }}
                    </span>

                </div>
            `).openPopup();


                setTimeout(function() {

                    map.invalidateSize();

                }, 300);

            });
        </script>
    @endif

@endsection
