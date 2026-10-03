@extends('maintenance-manager.layouts.app')

@section('title', 'Complaint Details')

@section('content')

    @php
        $divisionName = strtolower(trim((string) $complaint->division?->name));

        $isEngineering = $isEngineering ?? str_contains($divisionName, 'engineering');

        $isCommercial = str_contains($divisionName, 'commercial');

        $canAssign = $isEngineering && $complaint->status === 'Verified' && $complaint->technicians->isEmpty();

        if ($isCommercial && $complaint->consumer) {
            $displayAddress = $complaint->consumer->address?->full_address ?? 'No registered account address recorded.';

            $displayLatitude = $complaint->consumer->address?->latitude;

            $displayLongitude = $complaint->consumer->address?->longitude;

            $locationLabel = 'Registered Service Location';
        } else {
            $displayAddress = $complaint->address ?: 'No address recorded.';

            $displayLatitude = $complaint->latitude;

            $displayLongitude = $complaint->longitude;

            $locationLabel = $isEngineering ? 'Complaint Location' : 'Service Location';
        }

        $hasDisplayCoordinates = $displayLatitude !== null && $displayLongitude !== null;
    @endphp


    <div class="max-w-7xl mx-auto space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

            <div>

                <div class="flex flex-wrap items-center gap-2">

                    <a href="{{ route('maintenance-manager.complaints.index') }}"
                        class="inline-flex items-center gap-1.5 text-sm
                           text-gray-500 hover:text-blue-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>

                        Complaints
                    </a>

                    <span class="text-gray-300">/</span>

                    <span class="text-sm text-gray-500">
                        {{ $complaint->complaint_no }}
                    </span>

                </div>


                <div class="mt-3">

                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        {{ $complaint->category?->name ?? 'Water Service Complaint' }}
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Complaint #{{ $complaint->complaint_no }}
                    </p>

                </div>

            </div>


            {{-- STATUS --}}
            @php

                $statusClasses = match ($complaint->status) {
                    'Verified' => 'bg-blue-50 text-blue-700 border-blue-200',

                    'Assigned' => 'bg-indigo-50 text-indigo-700 border-indigo-200',

                    'In Progress' => 'bg-amber-50 text-amber-700 border-amber-200',

                    'Completed' => 'bg-green-50 text-green-700 border-green-200',

                    'Closed' => 'bg-gray-100 text-gray-700 border-gray-200',

                    default => 'bg-gray-50 text-gray-600 border-gray-200',
                };

            @endphp


            <div class="flex items-center gap-2">

                <span
                    class="inline-flex items-center gap-2 px-3 py-2
                         rounded-full border text-sm font-semibold
                         {{ $statusClasses }}">

                    <span class="w-2 h-2 rounded-full bg-current"></span>

                    {{ $complaint->status }}

                </span>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- MAIN GRID --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 gap-6">


            {{-- ===================================================== --}}
            {{-- LEFT / MAIN CONTENT --}}
            {{-- ===================================================== --}}

            <div class="space-y-6">


                {{-- ================================================= --}}
                {{-- COMPLAINT INFORMATION + MAINTENANCE TIMELINE --}}
                {{-- ================================================= --}}

                <div class="grid grid-cols-1 {{ $complaint->technicians->count() ? 'xl:grid-cols-3' : '' }} gap-6">

                    {{-- COMPLAINT INFORMATION --}}
                    <div
                        class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden {{ $complaint->technicians->count() ? 'xl:col-span-2' : '' }}">

                        <div class="px-5 py-4 border-b border-gray-100">

                            <h2 class="font-bold text-gray-900">
                                Complaint Information
                            </h2>

                            <p class="text-xs text-gray-500 mt-1">
                                Complaint details and service location
                            </p>

                        </div>

                        <div class="p-5 space-y-6">

                            {{-- DESCRIPTION --}}
                            <div>

                                <p class="text-xs uppercase tracking-wide font-semibold text-gray-400">
                                    Description
                                </p>

                                <div class="mt-2 rounded-xl bg-gray-50 border border-gray-100 p-4">

                                    <p class="text-sm leading-6 text-gray-700 whitespace-pre-line">
                                        {{ $complaint->description }}
                                    </p>

                                </div>

                            </div>


                            {{-- TYPE + DIVISION + URGENCY --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

                                <div>

                                    <p class="text-xs uppercase tracking-wide font-semibold text-gray-400">
                                        Complaint Type
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-gray-800">
                                        {{ $complaint->category?->name ?? 'Uncategorized' }}
                                    </p>

                                </div>

                                <div>

                                    <p class="text-xs uppercase tracking-wide font-semibold text-gray-400">
                                        Division
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-gray-800">
                                        {{ $complaint->division?->name ?? 'Not specified' }}
                                    </p>

                                </div>

                                <div>

                                    @php
                                        $urgencyLevel = $complaint->aiAnalysis?->urgency_level ?? 'Not Analyzed';

                                        $urgencyClass = match ($urgencyLevel) {
                                            'High' => 'bg-red-50 text-red-700 border-red-200',
                                            'Moderate' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'Low' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            default => 'bg-gray-50 text-gray-600 border-gray-200',
                                        };
                                    @endphp

                                    <p class="text-xs uppercase tracking-wide font-semibold text-gray-400">
                                        Urgency
                                    </p>

                                    <div class="mt-1">

                                        <span
                                            class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-bold {{ $urgencyClass }}">

                                            {{ $urgencyLevel }}

                                        </span>

                                    </div>

                                </div>

                            </div>


                            {{-- LOCATION --}}
                            <div>

                                <p class="text-xs uppercase tracking-wide font-semibold text-gray-400">
                                    {{ $locationLabel }}
                                </p>

                                <div class="mt-2 grid grid-cols-1 lg:grid-cols-5 gap-4">

                                    <div class="lg:col-span-2 rounded-xl bg-gray-50 border border-gray-100 p-4">

                                        <div class="flex items-start gap-3">

                                            <div
                                                class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">

                                                <i class="fas fa-location-dot"></i>

                                            </div>

                                            <div class="min-w-0">

                                                <p class="text-sm font-semibold text-gray-800">
                                                    {{ $displayAddress }}
                                                </p>

                                                @if ($isEngineering && $complaint->landmark)
                                                    <p class="mt-1 text-xs text-gray-500">
                                                        Landmark:
                                                        {{ $complaint->landmark }}
                                                    </p>
                                                @endif

                                                @if ($isCommercial && $complaint->consumer)
                                                    <p class="mt-2 text-[11px] leading-5 text-blue-600">
                                                        Registered service location of the linked SWD consumer account.
                                                    </p>
                                                @endif

                                            </div>

                                        </div>

                                        @if ($hasDisplayCoordinates)
                                            <div class="mt-4 flex flex-wrap gap-2">

                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-lg bg-white border border-gray-200 text-xs text-gray-500">
                                                    Latitude:
                                                    {{ number_format((float) $displayLatitude, 6) }}
                                                </span>

                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-lg bg-white border border-gray-200 text-xs text-gray-500">
                                                    Longitude:
                                                    {{ number_format((float) $displayLongitude, 6) }}
                                                </span>

                                            </div>
                                        @else
                                            <div class="mt-4 rounded-lg border border-amber-100 bg-amber-50 p-3">

                                                <p class="text-xs text-amber-700">
                                                    No saved coordinates are available for this service location.
                                                </p>

                                            </div>
                                        @endif

                                    </div>


                                    <div class="lg:col-span-3">

                                        @if ($hasDisplayCoordinates)
                                            <div id="complaintMap"
                                                class="h-[280px] sm:h-[320px] lg:h-full lg:min-h-[320px] w-full rounded-xl border border-gray-200 overflow-hidden bg-gray-100">
                                            </div>
                                        @else
                                            <div
                                                class="h-[240px] lg:h-full lg:min-h-[320px] rounded-xl border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center p-6 text-center">

                                                <div>

                                                    <i class="fas fa-map-location-dot text-2xl text-gray-400"></i>

                                                    <p class="mt-3 text-sm font-semibold text-gray-700">
                                                        Map unavailable
                                                    </p>

                                                    <p class="mt-1 text-xs text-gray-500">
                                                        This service location has no saved coordinates.
                                                    </p>

                                                </div>

                                            </div>
                                        @endif

                                    </div>

                                </div>

                            </div>


                            @if ($complaint->photo)
                                <div>

                                    <p class="text-xs uppercase tracking-wide font-semibold text-gray-400">
                                        Submitted Photo
                                    </p>

                                    <div class="mt-2">

                                        <img src="{{ asset('storage/' . $complaint->photo) }}" alt="Complaint photo"
                                            class="w-full max-h-[450px] object-cover rounded-xl border border-gray-200">

                                    </div>

                                </div>
                            @endif

                        </div>

                    </div>


                    {{-- MAINTENANCE TIMELINE --}}
                    @if ($complaint->technicians->count())

                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                            <div class="px-5 py-4 border-b border-gray-100">

                                <h2 class="font-bold text-gray-900">
                                    Maintenance Timeline
                                </h2>

                                <p class="text-xs text-gray-500 mt-1">
                                    Work timestamps per plumber
                                </p>

                            </div>

                            <div class="p-5 space-y-4">

                                @foreach ($complaint->technicians as $technician)
                                    @php
                                        $assignedAt = $technician->pivot?->assigned_at
                                            ? \Carbon\Carbon::parse($technician->pivot->assigned_at)
                                            : null;

                                        $startedAt = $technician->pivot?->started_at
                                            ? \Carbon\Carbon::parse($technician->pivot->started_at)
                                            : null;

                                        $completedAt = $technician->pivot?->completed_at
                                            ? \Carbon\Carbon::parse($technician->pivot->completed_at)
                                            : null;

                                        $durationText = null;

                                        if ($startedAt && $completedAt) {
                                            $totalMinutes = $startedAt->diffInMinutes($completedAt);

                                            $hours = intdiv($totalMinutes, 60);

                                            $minutes = $totalMinutes % 60;

                                            $durationText = collect([
                                                $hours > 0 ? $hours . ' hr' . ($hours !== 1 ? 's' : '') : null,

                                                $minutes > 0 ? $minutes . ' min' : null,
                                            ])
                                                ->filter()
                                                ->implode(' ');

                                            if ($durationText === '') {
                                                $durationText = 'Less than 1 min';
                                            }
                                        }
                                    @endphp

                                    <div class="rounded-xl border border-gray-200 overflow-hidden">

                                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">

                                            <p class="text-sm font-bold text-gray-900">
                                                {{ $technician->full_name }}
                                            </p>

                                            <p class="text-[10px] text-gray-500 mt-0.5">
                                                {{ $technician->pivot?->status ?? 'Assigned' }}
                                            </p>

                                        </div>

                                        <div class="p-4 space-y-4">

                                            <div class="flex gap-3">

                                                <div
                                                    class="w-7 h-7 rounded-full {{ $assignedAt ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center shrink-0">

                                                    <i class="fas fa-check text-[9px]"></i>

                                                </div>

                                                <div>

                                                    <p class="text-xs font-bold text-gray-800">
                                                        Assigned
                                                    </p>

                                                    <p class="text-[11px] text-gray-500 mt-0.5">
                                                        {{ $assignedAt?->format('M d, Y h:i A') ?? 'Not recorded' }}
                                                    </p>

                                                </div>

                                            </div>


                                            <div class="flex gap-3">

                                                <div
                                                    class="w-7 h-7 rounded-full {{ $startedAt ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center shrink-0">

                                                    <i class="fas fa-play text-[9px]"></i>

                                                </div>

                                                <div>

                                                    <p class="text-xs font-bold text-gray-800">
                                                        Started
                                                    </p>

                                                    <p class="text-[11px] text-gray-500 mt-0.5">
                                                        {{ $startedAt?->format('M d, Y h:i A') ?? 'Not started yet' }}
                                                    </p>

                                                </div>

                                            </div>


                                            <div class="flex gap-3">

                                                <div
                                                    class="w-7 h-7 rounded-full {{ $completedAt ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center shrink-0">

                                                    <i class="fas fa-flag-checkered text-[9px]"></i>

                                                </div>

                                                <div>

                                                    <p class="text-xs font-bold text-gray-800">
                                                        Completed
                                                    </p>

                                                    <p class="text-[11px] text-gray-500 mt-0.5">
                                                        {{ $completedAt?->format('M d, Y h:i A') ?? 'Not completed yet' }}
                                                    </p>

                                                </div>

                                            </div>


                                            @if ($durationText)
                                                <div class="pt-3 border-t border-gray-100">

                                                    <p
                                                        class="text-[10px] uppercase tracking-wide font-semibold text-gray-400">
                                                        Maintenance Duration
                                                    </p>

                                                    <p class="mt-1 text-sm font-bold text-indigo-700">
                                                        {{ $durationText }}
                                                    </p>

                                                </div>
                                            @endif

                                        </div>

                                    </div>
                                @endforeach

                            </div>

                        </div>

                    @endif

                </div>


                {{-- ================================================= --}}
                {{-- CONSUMER --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-100">

                        <h2 class="font-bold text-gray-900">
                            Consumer Information
                        </h2>

                    </div>


                    <div class="p-5">

                        <div class="flex items-center gap-4">

                            <div
                                class="w-14 h-14 rounded-2xl bg-blue-100
                                    text-blue-700 flex items-center justify-center
                                    text-lg font-bold shrink-0">

                                {{ strtoupper(
                                    substr($complaint->consumer?->first_name ?? ($complaint->complainant_name ?? 'C'), 0, 1) .
                                        substr($complaint->consumer?->last_name ?? '', 0, 1),
                                ) }}

                            </div>


                            <div>

                                <h3 class="font-bold text-gray-900">
                                    {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown Consumer') }}
                                </h3>

                                @if ($complaint->consumer?->account_number)
                                    <p class="text-sm text-blue-600 mt-0.5">
                                        Account No:
                                        {{ $complaint->consumer->account_number }}
                                    </p>
                                @endif

                                @if ($complaint->complainant_phone)
                                    <p class="text-sm text-gray-500 mt-0.5">
                                        {{ $complaint->complainant_phone }}
                                    </p>
                                @endif

                            </div>

                        </div>

                    </div>

                </div>



                {{-- ================================================= --}}
                {{-- MAINTENANCE REPORT --}}
                {{-- ================================================= --}}

                @if ($complaint->maintenanceReport)
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                        <div
                            class="px-5 py-4 border-b border-gray-100
                                flex items-center justify-between">

                            <div>

                                <h2 class="font-bold text-gray-900">
                                    Maintenance Report
                                </h2>

                                <p class="text-xs text-gray-500 mt-1">
                                    Plumber-submitted maintenance documentation
                                </p>

                            </div>

                        </div>


                        <div class="p-5">

                            <div class="rounded-xl bg-green-50 border border-green-100 p-4">

                                <div class="flex items-start gap-3">

                                    <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>

                                    <div>

                                        <p class="text-sm font-semibold text-green-800">
                                            Maintenance report submitted
                                        </p>

                                        <p class="mt-1 text-xs text-green-700">
                                            The maintenance team has submitted a report
                                            for this complaint.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                @endif

            </div>


            {{-- ===================================================== --}}
            {{-- RIGHT SIDEBAR --}}
            {{-- ===================================================== --}}

            <div class="space-y-6">


                {{-- ================================================= --}}
                {{-- COMMERCIAL REVIEW NOTICE --}}
                {{-- ================================================= --}}

                @if ($isCommercial)
                    <div class="bg-white rounded-2xl border border-sky-200 shadow-sm overflow-hidden">

                        <div class="p-5 flex items-start gap-4">

                            <div
                                class="w-11 h-11 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">

                                <i class="fas fa-building"></i>

                            </div>

                            <div>

                                <h2 class="font-bold text-gray-900">
                                    Commercial Complaint
                                </h2>

                                <p class="mt-1 text-sm leading-6 text-gray-600">
                                    This complaint is available for Maintenance Manager review.
                                    Plumber recommendation and maintenance-team assignment are only
                                    available for Engineering complaints.
                                </p>

                            </div>

                        </div>

                    </div>
                @endif


                {{-- ================================================= --}}
                {{-- ASSIGN TECHNICIANS --}}
                {{-- ================================================= --}}

                @if ($canAssign)

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                        <div class="px-5 py-4 border-b border-gray-100">

                            <h2 class="font-bold text-gray-900">
                                Assign Maintenance Team
                            </h2>

                            <p class="text-xs text-gray-500 mt-1">
                                Review the AI-assisted recommendation and select the plumbers who will handle this
                                complaint.
                            </p>

                        </div>

                        <form method="POST" action="{{ route('maintenance-manager.complaints.assign', $complaint) }}"
                            id="assignmentForm" class="p-5">

                            @csrf

                            @if (!empty($plumberRecommendationError))

                                <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                            <i class="fas fa-triangle-exclamation"></i>
                                        </div>

                                        <div>

                                            <p class="text-sm font-semibold text-amber-900">
                                                Recommendation Unavailable
                                            </p>

                                            <p class="text-xs text-amber-700 mt-1 leading-5">
                                                {{ $plumberRecommendationError }}
                                            </p>

                                            <p class="text-[11px] text-amber-600 mt-2">
                                                You can continue assigning plumbers manually.
                                            </p>

                                        </div>

                                    </div>

                                </div>
                            @elseif($plumberRecommendations['has_recommendations'] ?? false)
                                <div class="mb-5">

                                    <div class="flex items-start justify-between gap-3 mb-3">

                                        <div>

                                            <div class="flex items-center gap-2">

                                                <div
                                                    class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center">
                                                    <i class="fas fa-wand-magic-sparkles text-sm"></i>
                                                </div>

                                                <div>

                                                    <p class="text-sm font-bold text-gray-900">
                                                        AI-Assisted Recommendation
                                                    </p>

                                                    <p class="text-[11px] text-gray-500 mt-0.5">
                                                        Decision support for plumber assignment
                                                    </p>

                                                </div>

                                            </div>

                                        </div>

                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full bg-violet-50 border border-violet-100 text-[10px] font-semibold text-violet-700">
                                            AI Assisted
                                        </span>

                                    </div>

                                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

                                        @foreach ($plumberRecommendations['recommendations'] ?? [] as $index => $recommendation)
                                            @php
                                                $recommendationLevel =
                                                    $recommendation['recommendation'] ?? 'Alternative';

                                                $availability = $recommendation['availability'] ?? 'Unknown';

                                                $score = (float) ($recommendation['score_percentage'] ?? 0);

                                                $distance = $recommendation['nearest_active_assignment_km'] ?? null;

                                                $nearbyComplaint =
                                                    $recommendation['nearest_active_complaint_no'] ?? null;

                                                $isTopRecommendation = $index === 0;

                                                $recommendationClass = match ($recommendationLevel) {
                                                    'Highly Suitable'
                                                        => 'bg-emerald-50 text-emerald-700 border-emerald-200',

                                                    'Suitable' => 'bg-blue-50 text-blue-700 border-blue-200',

                                                    'Alternative' => 'bg-amber-50 text-amber-700 border-amber-200',

                                                    default => 'bg-gray-50 text-gray-600 border-gray-200',
                                                };

                                                $availabilityClass = match ($availability) {
                                                    'Available' => 'text-emerald-600',

                                                    'Assigned' => 'text-blue-600',

                                                    'Working' => 'text-amber-600',

                                                    'Busy' => 'text-red-600',

                                                    default => 'text-gray-600',
                                                };
                                            @endphp

                                            <div
                                                class="rounded-xl border {{ $isTopRecommendation ? 'border-violet-200' : 'border-gray-200' }} bg-white overflow-hidden">

                                                @if ($isTopRecommendation)
                                                    <div class="px-4 py-2 bg-violet-50 border-b border-violet-100">

                                                        <p
                                                            class="text-[10px] uppercase tracking-wide font-bold text-violet-700">
                                                            <i class="fas fa-star mr-1"></i>
                                                            Top Recommendation
                                                        </p>

                                                    </div>
                                                @endif

                                                <div class="p-4">

                                                    <div class="flex items-start justify-between gap-3">

                                                        <div class="min-w-0">

                                                            <p class="text-sm font-bold text-gray-900 truncate">
                                                                {{ $recommendation['name'] ?? 'Plumber' }}
                                                            </p>

                                                            <div class="flex flex-wrap items-center gap-2 mt-2">

                                                                <span
                                                                    class="inline-flex items-center px-2 py-1 rounded-lg border text-[10px] font-semibold {{ $recommendationClass }}">
                                                                    {{ $recommendationLevel }}
                                                                </span>

                                                                <span class="text-[11px] text-gray-500">
                                                                    Recommendation Score:
                                                                    <span class="font-bold text-gray-700">
                                                                        {{ number_format($score, 1) }}%
                                                                    </span>
                                                                </span>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="grid grid-cols-2 gap-2 mt-4">

                                                        <div class="rounded-lg bg-gray-50 border border-gray-100 p-3">

                                                            <p
                                                                class="text-[10px] uppercase tracking-wide font-semibold text-gray-400">
                                                                Availability
                                                            </p>

                                                            <p class="text-xs font-bold mt-1 {{ $availabilityClass }}">
                                                                {{ $availability }}
                                                            </p>

                                                        </div>

                                                        <div class="rounded-lg bg-gray-50 border border-gray-100 p-3">

                                                            <p
                                                                class="text-[10px] uppercase tracking-wide font-semibold text-gray-400">
                                                                Active Workload
                                                            </p>

                                                            <p class="text-xs font-bold text-gray-800 mt-1">
                                                                {{ $recommendation['active_workload'] ?? 0 }}
                                                                {{ ((int) ($recommendation['active_workload'] ?? 0)) === 1 ? 'complaint' : 'complaints' }}
                                                            </p>

                                                        </div>

                                                    </div>

                                                    <div class="mt-2 rounded-lg bg-gray-50 border border-gray-100 p-3">

                                                        <p
                                                            class="text-[10px] uppercase tracking-wide font-semibold text-gray-400">
                                                            Nearest Active Assignment
                                                        </p>

                                                        @if ($distance !== null)
                                                            <p class="text-xs font-bold text-gray-800 mt-1">

                                                                @if ((float) $distance < 1)
                                                                    {{ number_format((float) $distance * 1000, 0) }} meters
                                                                    away
                                                                @else
                                                                    {{ number_format((float) $distance, 2) }} km away
                                                                @endif

                                                            </p>

                                                            @if ($nearbyComplaint)
                                                                <p class="text-[10px] text-gray-500 mt-1">
                                                                    Based on {{ $nearbyComplaint }}
                                                                </p>
                                                            @endif
                                                        @else
                                                            <p class="text-xs font-semibold text-gray-500 mt-1">
                                                                Not available
                                                            </p>

                                                            <p class="text-[10px] text-gray-400 mt-1">
                                                                No usable active-assignment location for comparison.
                                                            </p>
                                                        @endif

                                                    </div>

                                                    @if (!empty($recommendation['reasons']))
                                                        <div class="mt-4">

                                                            <p class="text-[11px] font-bold text-gray-700">
                                                                Why recommended
                                                            </p>

                                                            <div class="mt-2 space-y-2">

                                                                @foreach ($recommendation['reasons'] as $reason)
                                                                    <div class="flex items-start gap-2">

                                                                        <i
                                                                            class="fas fa-circle-check text-emerald-500 text-[11px] mt-0.5"></i>

                                                                        <p class="text-[11px] leading-4 text-gray-600">
                                                                            {{ $reason }}
                                                                        </p>

                                                                    </div>
                                                                @endforeach

                                                            </div>

                                                        </div>
                                                    @endif

                                                    @if (!empty($recommendation['considerations']))
                                                        <div
                                                            class="mt-4 rounded-lg bg-amber-50 border border-amber-100 p-3">

                                                            <p class="text-[11px] font-bold text-amber-800">
                                                                Considerations
                                                            </p>

                                                            <div class="mt-2 space-y-2">

                                                                @foreach ($recommendation['considerations'] as $consideration)
                                                                    <div class="flex items-start gap-2">

                                                                        <i
                                                                            class="fas fa-circle-info text-amber-500 text-[11px] mt-0.5"></i>

                                                                        <p class="text-[11px] leading-4 text-amber-700">
                                                                            {{ $consideration }}
                                                                        </p>

                                                                    </div>
                                                                @endforeach

                                                            </div>

                                                        </div>
                                                    @endif

                                                    <button type="button"
                                                        class="ai-select-plumber mt-4 w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold transition"
                                                        data-plumber-id="{{ $recommendation['plumber_id'] }}">

                                                        <i class="fas fa-user-check"></i>

                                                        <span class="ai-select-text">
                                                            Select Plumber
                                                        </span>

                                                    </button>

                                                </div>

                                            </div>
                                        @endforeach

                                    </div>

                                    <div class="mt-3 rounded-xl bg-blue-50 border border-blue-100 p-3">

                                        <div class="flex items-start gap-2">

                                            <i class="fas fa-circle-info text-blue-600 text-xs mt-0.5"></i>

                                            <p class="text-[10px] leading-4 text-blue-700">
                                                Recommendations use operational information currently available in the
                                                system, including active workload, assignment status, and active-assignment
                                                locations when available. They do not represent a plumber's live physical
                                                location. The Maintenance Manager makes the final assignment decision.
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            @endif

                            <div
                                class="mb-4 flex items-center justify-between rounded-xl bg-blue-50 border border-blue-100 p-3">

                                <div>

                                    <p class="text-xs font-semibold text-blue-800">
                                        Plumber Selection
                                    </p>

                                </div>

                                <span id="selectedCount"
                                    class="inline-flex items-center justify-center min-w-8 h-8 px-2 rounded-full bg-blue-600 text-white text-xs font-bold">
                                    0
                                </span>

                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">

                                @forelse($technicians as $technician)
                                    <label class="technician-card block cursor-pointer"
                                        data-technician-name="{{ $technician->full_name }}">

                                        <input type="checkbox" id="plumber-checkbox-{{ $technician->id }}"
                                            name="technician_ids[]" value="{{ $technician->id }}"
                                            class="technician-checkbox peer sr-only">

                                        <div
                                            class="rounded-xl border-2 border-gray-200
               bg-white p-3 transition-all
               peer-checked:border-blue-500
               peer-checked:bg-blue-50
               hover:border-blue-300">

                                            <div class="flex items-center gap-3">

                                                <div
                                                    class="w-10 h-10 rounded-xl
                       bg-blue-100 text-blue-700
                       flex items-center justify-center
                       text-xs font-bold shrink-0">

                                                    {{ strtoupper(substr($technician->first_name ?? '', 0, 1) . substr($technician->last_name ?? '', 0, 1)) }}

                                                </div>

                                                <div class="min-w-0 flex-1">

                                                    <p class="text-sm font-semibold text-gray-900">
                                                        {{ $technician->full_name }}
                                                    </p>

                                                    @if ($technician->employee_id)
                                                        <p class="text-[11px] text-gray-500">
                                                            Employee ID:
                                                            {{ $technician->employee_id }}
                                                        </p>
                                                    @endif

                                                    @if ($technician->position?->name)
                                                        <p class="text-[11px] text-gray-400">
                                                            {{ $technician->position->name }}
                                                        </p>
                                                    @endif

                                                </div>

                                                <div
                                                    class="plumber-check-indicator
                       w-6 h-6 rounded-full
                       border-2 border-gray-300
                       bg-white
                       flex items-center justify-center
                       shrink-0
                       transition-all">

                                                    <i
                                                        class="fas fa-check
                           text-white text-[10px]
                           hidden"></i>

                                                </div>

                                            </div>

                                        </div>

                                    </label>

                                @empty

                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-5 text-center">

                                        <p class="text-sm font-medium text-gray-700">
                                            No active plumbers available.
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            Please check the plumber accounts.
                                        </p>

                                    </div>
                                @endforelse

                            </div>

                            @error('technician_ids')
                                <p class="mt-3 text-xs font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            <button type="submit" id="assignButton" disabled
                                class="mt-5 w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">

                                <i class="fas fa-users"></i>

                                Assign Maintenance Team

                            </button>

                        </form>

                    </div>

                @endif


                {{-- ================================================= --}}
                {{-- CURRENT TEAM --}}
                {{-- ================================================= --}}

                @if ($complaint->technicians->count())

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                        <div class="px-5 py-4 border-b border-gray-100">

                            <div class="flex items-center justify-between">

                                <div>

                                    <h2 class="font-bold text-gray-900">
                                        Assigned Maintenance Team
                                    </h2>

                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $complaint->technicians->count() }}
                                        {{ $complaint->technicians->count() === 1 ? 'plumber' : 'plumbers' }}
                                        assigned
                                    </p>

                                </div>


                                <div
                                    class="w-9 h-9 rounded-xl bg-indigo-50
                                        flex items-center justify-center">

                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5V4H2v16h5M9 20v-6h6v6" />
                                    </svg>

                                </div>

                            </div>

                        </div>


                        <div class="p-5 space-y-3">

                            @foreach ($complaint->technicians as $technician)
                                <div
                                    class="flex items-center gap-3 rounded-xl
                                        border border-gray-100 bg-gray-50 p-3">

                                    <div
                                        class="w-10 h-10 rounded-xl bg-blue-100
                                            text-blue-700 flex items-center
                                            justify-center text-xs font-bold shrink-0">

                                        {{ strtoupper(substr($technician->first_name ?? '', 0, 1) . substr($technician->last_name ?? '', 0, 1)) }}

                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $technician->full_name }}
                                        </p>

                                        @if ($technician->employee_id)
                                            <p class="text-[11px] text-gray-500">
                                                ID: {{ $technician->employee_id }}
                                            </p>
                                        @endif

                                        @if ($technician->pivot?->assignment_role)
                                            <span
                                                class="inline-flex mt-1 px-2 py-0.5
                                                     rounded-md bg-white border
                                                     border-gray-200 text-[10px]
                                                     font-medium text-gray-500">

                                                {{ $technician->pivot->assignment_role }}

                                            </span>
                                        @endif

                                    </div>


                                    @if ($technician->pivot?->status)
                                        <span
                                            class="text-[10px] font-semibold
                                                 text-gray-500 shrink-0">

                                            {{ $technician->pivot->status }}

                                        </span>
                                    @endif

                                </div>
                            @endforeach

                        </div>

                    </div>

                @endif


                {{-- ================================================= --}}
                {{-- COMPLAINT TIMELINE --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-100">

                        <h2 class="font-bold text-gray-900">
                            Complaint Progress
                        </h2>

                    </div>


                    <div class="p-5">

                        <div class="relative space-y-6">


                            {{-- SUBMITTED --}}
                            <div class="flex gap-3">

                                <div
                                    class="w-8 h-8 rounded-full bg-blue-100
                                        text-blue-600 flex items-center
                                        justify-center shrink-0">

                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-gray-900">
                                        Submitted
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        {{ $complaint->created_at?->format('M d, Y h:i A') }}
                                    </p>

                                </div>

                            </div>


                            {{-- VERIFIED --}}
                            @if ($complaint->verified_at)
                                <div class="flex gap-3">

                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-100
                                            text-blue-600 flex items-center
                                            justify-center shrink-0">

                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>

                                    </div>

                                    <div>

                                        <p class="text-sm font-semibold text-gray-900">
                                            Verified
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            {{ $complaint->verified_at->format('M d, Y h:i A') }}
                                        </p>

                                        <p class="text-xs text-blue-600 mt-1">
                                            Verified by:
                                            <span class="font-semibold">
                                                {{ $complaint->verifier?->full_name ?? ($complaint->verifiedBy?->full_name ?? 'Unknown') }}
                                            </span>
                                        </p>

                                    </div>

                                </div>
                            @endif


                            {{-- ASSIGNED --}}
                            @if ($complaint->technicians->count())

                                <div class="flex gap-3">

                                    <div
                                        class="w-8 h-8 rounded-full bg-indigo-100
                                            text-indigo-600 flex items-center
                                            justify-center shrink-0">

                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5V4H2v16h5M9 20v-6h6v6" />
                                        </svg>

                                    </div>

                                    <div class="min-w-0">

                                        <p class="text-sm font-semibold text-gray-900">
                                            Assigned
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            Maintenance team assigned
                                        </p>

                                        <div class="mt-2 space-y-1">

                                            @foreach ($complaint->technicians as $technician)
                                                <p class="text-xs text-indigo-600">
                                                    • {{ $technician->full_name }}
                                                </p>
                                            @endforeach

                                        </div>

                                    </div>

                                </div>

                            @endif


                            {{-- IN PROGRESS --}}
                            @if (in_array($complaint->status, ['In Progress', 'Completed', 'Closed']))
                                <div class="flex gap-3">

                                    <div
                                        class="w-8 h-8 rounded-full bg-amber-100
                                            text-amber-600 flex items-center
                                            justify-center shrink-0">

                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 2" />
                                        </svg>

                                    </div>

                                    <div>

                                        <p class="text-sm font-semibold text-gray-900">
                                            In Progress
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            Maintenance work is underway.
                                        </p>

                                    </div>

                                </div>
                            @endif


                            {{-- COMPLETED --}}
                            @if (in_array($complaint->status, ['Completed', 'Closed']))
                                <div class="flex gap-3">

                                    <div
                                        class="w-8 h-8 rounded-full bg-green-100
                                            text-green-600 flex items-center
                                            justify-center shrink-0">

                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>

                                    </div>

                                    <div>

                                        <p class="text-sm font-semibold text-gray-900">
                                            Completed
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            Maintenance work has been completed.
                                        </p>

                                    </div>

                                </div>
                            @endif


                            {{-- CLOSED --}}
                            @if ($complaint->status === 'Closed')
                                <div class="flex gap-3">

                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-100
                                            text-gray-600 flex items-center
                                            justify-center shrink-0">

                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>

                                    </div>

                                    <div>

                                        <p class="text-sm font-semibold text-gray-900">
                                            Closed
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            Complaint case finalized.
                                        </p>

                                    </div>

                                </div>
                            @endif

                        </div>

                    </div>

                </div>


                {{-- REPORT LINK --}}
                <a href="{{ route('maintenance-manager.complaints.report', $complaint) }}" target="_blank"
                    class="w-full inline-flex items-center justify-center gap-2
                       px-4 py-3 rounded-xl bg-gray-900 text-white
                       text-sm font-semibold hover:bg-gray-800 transition">

                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2h-4.5L15 6H9L10.5 4H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>

                    View / Print Report

                </a>

            </div>

        </div>

    </div>


    {{-- ============================================================= --}}
    {{-- MULTI-TECHNICIAN SELECTION SCRIPT --}}
    {{-- ============================================================= --}}

    @if ($canAssign)
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const checkboxes =
                    document.querySelectorAll(
                        '.technician-checkbox'
                    );

                const selectedCount =
                    document.getElementById(
                        'selectedCount'
                    );

                const assignButton =
                    document.getElementById(
                        'assignButton'
                    );

                const aiButtons =
                    document.querySelectorAll(
                        '.ai-select-plumber'
                    );


                function updateAiButtons() {

                    aiButtons.forEach(function(button) {

                        const checkbox =
                            document.getElementById(
                                `plumber-checkbox-${button.dataset.plumberId}`
                            );

                        if (!checkbox) {
                            return;
                        }

                        const text =
                            button.querySelector(
                                '.ai-select-text'
                            );

                        const icon =
                            button.querySelector('i');

                        if (checkbox.checked) {

                            button.classList.remove(
                                'bg-violet-600',
                                'hover:bg-violet-700'
                            );

                            button.classList.add(
                                'bg-emerald-600',
                                'hover:bg-emerald-700'
                            );

                            if (text) {
                                text.textContent =
                                    'Selected';
                            }

                            if (icon) {
                                icon.className =
                                    'fas fa-circle-check';
                            }

                        } else {

                            button.classList.remove(
                                'bg-emerald-600',
                                'hover:bg-emerald-700'
                            );

                            button.classList.add(
                                'bg-violet-600',
                                'hover:bg-violet-700'
                            );

                            if (text) {
                                text.textContent =
                                    'Select Plumber';
                            }

                            if (icon) {
                                icon.className =
                                    'fas fa-user-check';
                            }

                        }

                    });

                }


                function updateSelection() {

                    const selected =
                        Array.from(checkboxes)
                        .filter(
                            checkbox =>
                            checkbox.checked
                        );

                    const count =
                        selected.length;


                    checkboxes.forEach(function(checkbox) {

                        const card =
                            checkbox.closest(
                                '.technician-card'
                            );

                        const indicator =
                            card?.querySelector(
                                '.plumber-check-indicator'
                            );

                        const checkIcon =
                            indicator?.querySelector('i');

                        if (!indicator || !checkIcon) {
                            return;
                        }

                        if (checkbox.checked) {

                            indicator.classList.remove(
                                'border-gray-300',
                                'bg-white'
                            );

                            indicator.classList.add(
                                'border-blue-600',
                                'bg-blue-600'
                            );

                            checkIcon.classList.remove(
                                'hidden'
                            );

                        } else {

                            indicator.classList.remove(
                                'border-blue-600',
                                'bg-blue-600'
                            );

                            indicator.classList.add(
                                'border-gray-300',
                                'bg-white'
                            );

                            checkIcon.classList.add(
                                'hidden'
                            );

                        }

                    });


                    if (selectedCount) {
                        selectedCount.textContent =
                            count;
                    }


                    if (assignButton) {

                        assignButton.disabled =
                            count === 0;

                        if (count > 0) {

                            assignButton.innerHTML = `
                                <i class="fas fa-users"></i>
                                Assign ${count}
                                Plumber${count > 1 ? 's' : ''}
                            `;

                        } else {

                            assignButton.innerHTML = `
                                <i class="fas fa-users"></i>
                                Assign Maintenance Team
                            `;

                        }

                    }


                    updateAiButtons();

                }


                checkboxes.forEach(
                    function(checkbox) {

                        checkbox.addEventListener(
                            'change',
                            updateSelection
                        );

                    }
                );


                aiButtons.forEach(
                    function(button) {

                        button.addEventListener(
                            'click',
                            function() {

                                const checkbox =
                                    document.getElementById(
                                        `plumber-checkbox-${this.dataset.plumberId}`
                                    );

                                if (!checkbox) {
                                    return;
                                }

                                checkbox.checked = !checkbox.checked;

                                checkbox.dispatchEvent(
                                    new Event(
                                        'change', {
                                            bubbles: true
                                        }
                                    )
                                );

                            }
                        );

                    }
                );


                updateSelection();

            });
        </script>
    @endif


    @if ($hasDisplayCoordinates)
        @push('styles')
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        @endpush

        @push('scripts')
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const mapElement = document.getElementById('complaintMap');

                    if (!mapElement || typeof L === 'undefined') {
                        return;
                    }

                    const latitude = parseFloat(@json((float) $displayLatitude));
                    const longitude = parseFloat(@json((float) $displayLongitude));

                    if (Number.isNaN(latitude) || Number.isNaN(longitude)) {
                        return;
                    }

                    const map = L.map('complaintMap', {
                        scrollWheelZoom: false,
                        zoomControl: true
                    }).setView([latitude, longitude], 16);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    const marker = L.marker([latitude, longitude]).addTo(map);

                    marker.bindPopup(
                        '<strong>{{ addslashes($complaint->complaint_no) }}</strong><br>' +
                        '<span style="font-size:12px;color:#64748b;">Complaint service location</span>'
                    );

                    function refreshMap() {
                        map.invalidateSize({
                            animate: false,
                            pan: false
                        });
                    }

                    requestAnimationFrame(function() {
                        refreshMap();
                        setTimeout(refreshMap, 100);
                        setTimeout(refreshMap, 300);
                    });

                    window.addEventListener('resize', refreshMap);
                });
            </script>
        @endpush
    @endif

@endsection
