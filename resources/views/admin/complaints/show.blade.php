```blade
@extends('admin.layouts.app')

@section('title', 'Complaint Details')

@section('content')
@php
    /*
    |--------------------------------------------------------------------------
    | Read-only Admin Complaint Details
    |--------------------------------------------------------------------------
    */

    $returnTo = request()->query('return_to', 'complaints');

    $candidateBackUrl = request()->query('back_url');
    $backUrl = route('admin.complaints.index');

    // Accept only a local relative URL or a URL on this same host.
    if (is_string($candidateBackUrl) && $candidateBackUrl !== '') {
        $parts = parse_url($candidateBackUrl);

        if (
            is_array($parts) &&
            isset($parts['path']) &&
            str_starts_with($parts['path'], '/') &&
            !str_starts_with($parts['path'], '//')
        ) {
            $isRelativeUrl = !isset($parts['host']) && !isset($parts['scheme']);

            $isSameHostUrl =
                isset($parts['host']) &&
                strtolower($parts['host']) === strtolower(request()->getHost()) &&
                in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true);

            if ($isRelativeUrl || $isSameHostUrl) {
                $backUrl = $candidateBackUrl;
            }
        }
    }

    $isCommercial = str_contains(
        strtolower((string) $complaint->division?->name),
        'commercial'
    );

    $isEngineering = str_contains(
        strtolower((string) $complaint->division?->name),
        'engineering'
    );

    $aiAnalysis = $complaint->aiAnalysis;
    $commercialResolution = $complaint->commercialResolution;
    $report = $complaint->maintenanceReport;

    $reportApproved = $report !== null
        && $report->submitted_at !== null
        && $report->review_status === 'Approved';

    $consumerCategory = $aiAnalysis?->consumerCategory ?? $complaint->category;
    $predictedCategory = $aiAnalysis?->predictedCategory;
    $verifiedCategory = $aiAnalysis?->verifiedCategory;

    $aiSuggestedUrgency = $aiAnalysis?->urgency_level;
    $verifiedUrgency = $aiAnalysis?->verified_urgency_level;
    $urgencyLevel = $verifiedUrgency ?: $aiSuggestedUrgency;
    $urgencyLabel = $urgencyLevel ?: 'Not Assessed';
    $urgencySourceLabel = $verifiedUrgency
        ? 'Customer Service verified'
        : 'AI suggested';

    $urgencyClasses = match ($urgencyLevel) {
        'High' => 'bg-red-50 border-red-200 text-red-700',
        'Moderate' => 'bg-amber-50 border-amber-200 text-amber-700',
        'Low' => 'bg-green-50 border-green-200 text-green-700',
        default => 'bg-gray-50 border-gray-200 text-gray-600',
    };

    $statusLabel = match ($complaint->status) {
        'Completed' => 'Accomplished',
        default => $complaint->status ?: 'Unknown',
    };

    $statusClasses = match ($complaint->status) {
        'Pending' => 'bg-amber-50 text-amber-700 border-amber-200',
        'Verified' => 'bg-green-50 text-green-700 border-green-200',
        'CS Processing' => 'bg-sky-50 text-sky-700 border-sky-200',
        'For Maintenance' => 'bg-violet-50 text-violet-700 border-violet-200',
        'Assigned' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'In Progress' => 'bg-blue-50 text-blue-700 border-blue-200',
        'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'Closed' => 'bg-slate-100 text-slate-700 border-slate-200',
        'Rejected' => 'bg-red-50 text-red-700 border-red-200',
        default => 'bg-gray-50 text-gray-700 border-gray-200',
    };

    $displayAddress = $isCommercial && $complaint->consumer
        ? ($complaint->consumer->address?->full_address
            ?? 'No registered account address recorded.')
        : ($complaint->address ?: 'No address recorded.');

    $addressLabel = $isCommercial && $complaint->consumer
        ? 'Account Address'
        : ($isEngineering ? 'Service Address' : 'Address');

    $assignedNames = $complaint->technicians
        ->pluck('full_name')
        ->filter()
        ->values();

    $firstAssignedAt = $complaint->technicians
        ->map(fn ($technician) => $technician->pivot?->assigned_at)
        ->filter()
        ->sort()
        ->first();

    $firstStartedAt = $complaint->technicians
        ->map(fn ($technician) => $technician->pivot?->started_at)
        ->filter()
        ->sort()
        ->first();

    $firstCompletedAt = $complaint->technicians
        ->map(fn ($technician) => $technician->pivot?->completed_at)
        ->filter()
        ->sort()
        ->first();

    $isClosedWithoutMaintenance = $isCommercial
        && $complaint->status === 'Closed'
        && !$commercialResolution?->forwarded_to_maintenance_at;

    $rawAnalysis = is_array($aiAnalysis?->raw_analysis)
        ? $aiAnalysis->raw_analysis
        : [];

    $supportingSummary = data_get($rawAnalysis, 'supporting_evidence.summary');
    $supportingIndicators = data_get($rawAnalysis, 'supporting_evidence.indicators', []);
    $urgencyReasons = data_get($rawAnalysis, 'urgency.reasons', []);

    // All maintenance visit history is displayed as read-only information.
    $workOrders = \App\Models\MaintenanceWorkOrder::query()
        ->with(['schedule', 'technicians'])
        ->where('complaint_id', $complaint->id)
        ->orderBy('visit_number')
        ->orderBy('id')
        ->get();

    $hasPendingWorkload = $workOrders->contains(
        fn ($order) => $order->status === 'Pending Workload'
            || filled($order->pending_reason)
    );

    $hasMissedSchedule = $workOrders->contains(function ($order) {
        return $order->status === 'Rescheduled'
            || filled($order->missed_reason)
            || filled($order->missed_manager_reason)
            || filled($order->missed_confirmed_at)
            || filled($order->missed_reported_at);
    });

    // The controller may supply these results if related-complaint analysis
    // is already implemented. Defaults prevent undefined-variable errors.
    $similarComplaints = $similarComplaints ?? [
        'count' => 0,
        'has_possible_related_complaints' => false,
        'matches' => [],
    ];

    $similarComplaintError = $similarComplaintError ?? null;


    /*
|--------------------------------------------------------------------------
| Maintenance Work Order History
|--------------------------------------------------------------------------
*/
$workOrders = \App\Models\MaintenanceWorkOrder::query()
    ->with(['schedule', 'technicians'])
    ->where('complaint_id', $complaint->id)
    ->orderBy('visit_number')
    ->orderBy('id')
    ->get();

$hasPendingWorkload = $workOrders->contains(
    fn ($order) => $order->status === 'Pending Workload'
);

$hasMissedSchedule = $workOrders->contains(function ($order) {
    return $order->status === 'Rescheduled'
        || filled($order->missed_reason)
        || filled($order->missed_manager_reason)
        || filled($order->missed_confirmed_at);
});
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="text-sm font-medium text-gray-500">Admin</span>
                <span class="text-gray-300">•</span>
                <span class="text-sm font-semibold text-gray-700">
                    {{ $complaint->complaint_no }}
                </span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                {{ $complaint->category?->name ?? 'Water Service Concern' }}
            </h1>

            <p class="text-sm text-gray-500 mt-2">
                Read-only complaint record, supporting evidence, review history,
                and maintenance progress.
            </p>
        </div>

        <a href="{{ $backUrl }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
            border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
            <i class="fas fa-arrow-left"></i>
            Back to Complaints
        </a>
    </div>

    {{-- Overview --}}
    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm">
        <div class="p-5 sm:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        Complaint Status
                    </p>
                    <span class="mt-2 inline-flex items-center px-3 py-1.5 rounded-full
                        border text-sm font-semibold {{ $statusClasses }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        Complaint Urgency
                    </p>
                    <span class="mt-2 inline-flex items-center px-3 py-1.5 rounded-full
                        border text-sm font-semibold {{ $urgencyClasses }}">
                        {{ $urgencyLabel }}
                    </span>
                    <p class="mt-2 text-xs text-gray-500">{{ $urgencySourceLabel }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        Division
                    </p>
                    <p class="mt-2 text-sm font-semibold text-gray-900">
                        {{ $complaint->division?->name ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                        Submitted
                    </p>
                    <p class="mt-2 text-sm font-semibold text-gray-900">
                        {{ $complaint->created_at?->format('M d, Y h:i A') ?? '—' }}
                    </p>
                </div>

            </div>

            @if ($hasPendingWorkload || $hasMissedSchedule)
                <div class="flex flex-wrap gap-2 mt-5 pt-5 border-t border-gray-100">
                    @if ($hasPendingWorkload)
                        <span class="inline-flex items-center gap-2 rounded-full border
                            border-orange-200 bg-orange-50 px-3 py-2 text-xs font-semibold text-orange-800">
                            <i class="fas fa-clock"></i>
                            Pending Maintenance Workload
                        </span>
                    @endif

                    @if ($hasMissedSchedule)
                        <span class="inline-flex items-center gap-2 rounded-full border
                            border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-800">
                            <i class="fas fa-calendar-xmark"></i>
                            Missed Schedule / Rescheduling Record
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </section>



    {{-- Complaint Information --}}
    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Complaint Information</h2>
            <p class="text-sm text-gray-500 mt-1">
                Original concern and consumer account information.
            </p>
        </div>

        <div class="p-5 sm:p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                <div>
                    <p class="text-xs text-gray-500">Complaint Number</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $complaint->complaint_no ?: '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Complainant</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $complaint->complainant_name ?: '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Contact Number</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $complaint->complainant_phone ?: '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">SWD Account</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $complaint->consumer?->account_number ?: 'Walk-in / No linked account' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Linked Consumer</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $complaint->consumer?->full_name ?? 'No linked consumer' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Complaint Type</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $complaint->category?->name ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Division</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $complaint->division?->name ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500">Last Updated</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $complaint->updated_at?->format('M d, Y h:i A') ?? '—' }}
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs text-gray-500">Consumer's Concern</p>
                <div class="mt-2 rounded-xl bg-gray-50 border border-gray-200 p-4
                    text-sm text-gray-700 whitespace-pre-line break-words">
                    {{ $complaint->description ?: 'No description recorded.' }}
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs text-gray-500">{{ $addressLabel }}</p>
                <p class="mt-1 text-sm font-semibold text-gray-900 break-words">
                    {{ $displayAddress }}
                </p>

                @if ($complaint->landmark)
                    <p class="mt-4 text-xs text-gray-500">Landmark</p>
                    <p class="mt-1 text-sm text-gray-700">
                        {{ $complaint->landmark }}
                    </p>
                @endif

                @if ($complaint->latitude !== null && $complaint->longitude !== null)
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Latitude</p>
                            <p class="mt-1 text-sm text-gray-800">
                                {{ $complaint->latitude }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Longitude</p>
                            <p class="mt-1 text-sm text-gray-800">
                                {{ $complaint->longitude }}
                            </p>
                        </div>
                    </div>

                    @if (!$isCommercial)
                        <div id="complaint-map"
                            class="mt-5 w-full h-72 rounded-xl border border-gray-200 overflow-hidden">
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </section>

    {{-- Supporting Photos --}}
    @if ($complaint->photos && $complaint->photos->isNotEmpty())
        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Supporting Photos</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Evidence attached to the original complaint.
                </p>
            </div>

            <div class="p-5 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($complaint->photos as $photo)
                        @if ($photo->photo)
                            <a href="{{ asset('storage/' . $photo->photo) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group block rounded-2xl border border-gray-200 overflow-hidden
                                bg-gray-50 hover:border-blue-300 transition">
                                <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                                    <img src="{{ asset('storage/' . $photo->photo) }}"
                                        alt="Supporting complaint photo {{ $loop->iteration }}"
                                        loading="lazy"
                                        class="w-full h-full object-cover group-hover:scale-[1.02] transition duration-200">
                                </div>
                                <div class="px-3 py-2.5 flex items-center justify-between gap-3 bg-white">
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-camera mr-1"></i>
                                        Photo {{ $loop->iteration }}
                                    </span>
                                    <span class="text-xs font-medium text-blue-600">
                                        View full size
                                    </span>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- AI-Assisted Analysis: information only, no assignment actions --}}
    @if ($aiAnalysis)
        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">AI-Assisted Analysis</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Recorded AI suggestions and Customer Service verification for reference.
                </p>
            </div>

            <div class="p-5 sm:p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <p class="text-xs text-gray-500">Consumer-Submitted Category</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $consumerCategory?->name ?? 'Not available' }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-violet-200 bg-violet-50 p-4">
                        <p class="text-xs text-violet-700">AI-Suggested Category</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $predictedCategory?->name ?? ($aiAnalysis->predicted_type ?? 'Not available') }}
                        </p>

                        <p class="mt-3 text-xs text-violet-700">AI Urgency Suggestion</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $aiAnalysis->urgency_level ?: 'Not Assessed' }}
                        </p>

                        @if (!is_null($aiAnalysis->urgency_score))
                            <p class="mt-1 text-xs text-gray-500">
                                AI urgency score: {{ $aiAnalysis->urgency_score }}
                            </p>
                        @endif
                    </div>

                    <div class="rounded-xl border border-green-200 bg-green-50 p-4">
                        <p class="text-xs font-semibold text-green-700">
                            Verified Classification
                        </p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $verifiedCategory?->name
                                ?? ($complaint->status === 'Pending'
                                    ? 'Pending review'
                                    : ($complaint->category?->name ?? 'Not available')) }}
                        </p>

                        <p class="mt-3 text-xs text-green-700">Verified Urgency</p>
                        <span class="mt-1 inline-flex items-center px-2.5 py-1 rounded-full
                            border text-xs font-semibold {{ $urgencyClasses }}">
                            {{ $urgencyLabel }}
                        </span>

                        @if ($aiAnalysis->urgency_verified_at)
                            <p class="mt-2 text-xs text-gray-500">
                                Verified on
                                {{ \Illuminate\Support\Carbon::parse($aiAnalysis->urgency_verified_at)->format('M d, Y h:i A') }}
                            </p>
                        @endif
                    </div>
                </div>

                @if ($supportingSummary)
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-2">AI Summary</p>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4
                            text-sm text-gray-700 whitespace-pre-line">
                            {{ $supportingSummary }}
                        </div>
                    </div>
                @endif

                @if (!empty($supportingIndicators) || !empty($urgencyReasons))
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        @if (!empty($supportingIndicators))
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-2">
                                    Classification Indicators
                                </p>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    @foreach ($supportingIndicators as $indicator)
                                        <li class="flex gap-2">
                                            <i class="fas fa-circle text-[6px] mt-2 text-gray-400"></i>
                                            <span>
                                                {{ is_array($indicator)
                                                    ? data_get($indicator, 'text', json_encode($indicator))
                                                    : $indicator }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (!empty($urgencyReasons))
                            <div>
                                <p class="text-xs font-medium text-gray-500 mb-2">
                                    Urgency Indicators
                                </p>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    @foreach ($urgencyReasons as $reason)
                                        <li class="flex gap-2">
                                            <i class="fas fa-circle text-[6px] mt-2 text-gray-400"></i>
                                            <span>
                                                {{ is_array($reason)
                                                    ? data_get($reason, 'text', json_encode($reason))
                                                    : $reason }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- Related Complaint Analysis --}}
    <details class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <summary class="flex cursor-pointer list-none items-center justify-between gap-4
            px-5 py-4 sm:px-6 hover:bg-gray-50">
            <div class="min-w-0">
                <h2 class="font-semibold text-gray-900">Related Complaint Analysis</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Possible relationships identified between this complaint and other records.
                </p>
                <span class="inline-flex mt-2 items-center rounded-full border border-violet-100
                    bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">
                    {{ $similarComplaints['count'] ?? count($similarComplaints['matches'] ?? []) }}
                    {{ ($similarComplaints['count'] ?? count($similarComplaints['matches'] ?? [])) === 1
                        ? 'Possible Match'
                        : 'Possible Matches' }}
                </span>
            </div>
            <i class="fas fa-chevron-down text-sm text-gray-400 transition-transform group-open:rotate-180"></i>
        </summary>

        <div class="border-t border-gray-100 p-5 sm:p-6">
            @if ($similarComplaintError)
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm font-semibold text-amber-800">
                        Related complaint analysis unavailable
                    </p>
                    <p class="text-sm text-amber-700 mt-1">
                        {{ $similarComplaintError }}
                    </p>
                </div>
            @elseif (
                ($similarComplaints['has_possible_related_complaints'] ?? false)
                && !empty($similarComplaints['matches'])
            )
                <div class="space-y-4">
                    @foreach ($similarComplaints['matches'] as $match)
                        @php
                            $relatedComplaintId = isset($match['complaint_id'])
                                ? (int) $match['complaint_id']
                                : null;

                            $relationship = $match['relationship'] ?? 'Possibly Related';
                            $scorePercentage = (int) round((float) ($match['score_percentage'] ?? 0));
                            $distanceKm = $match['distance_km'] ?? null;
                            $hoursDifference = $match['hours_difference'] ?? null;
                            $evidence = is_array($match['evidence'] ?? null)
                                ? $match['evidence']
                                : [];

                            $relationshipClasses = match ($relationship) {
                                'Likely Related' => 'bg-red-50 text-red-700 border-red-200',
                                'Possibly Related' => 'bg-amber-50 text-amber-700 border-amber-200',
                                default => 'bg-gray-50 text-gray-700 border-gray-200',
                            };
                        @endphp

                        <article class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">
                                        {{ $match['complaint_no'] ?? 'Unknown Complaint' }}
                                    </p>
                                    <span class="inline-flex mt-2 px-2.5 py-1 rounded-full
                                        border text-xs font-semibold {{ $relationshipClasses }}">
                                        {{ $relationship }}
                                    </span>
                                    @if (!empty($match['status']))
                                        <p class="mt-2 text-xs text-gray-500">
                                            Status: {{ $match['status'] }}
                                        </p>
                                    @endif
                                </div>

                                <div class="sm:text-right">
                                    <p class="text-xs text-gray-400">Relationship Score</p>
                                    <p class="text-2xl font-bold text-gray-900">
                                        {{ $scorePercentage }}%
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-4">
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-xs text-gray-500">Location</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ $distanceKm !== null
                                            ? number_format((float) $distanceKm, 2) . ' km away'
                                            : 'Not available' }}
                                    </p>
                                </div>

                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-xs text-gray-500">Description Similarity</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ (int) round((float) ($match['text_similarity_percentage'] ?? 0)) }}%
                                    </p>
                                </div>

                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-xs text-gray-500">Complaint Type</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ !empty($match['same_complaint_type'])
                                            ? 'Same type'
                                            : 'Different type / unknown' }}
                                    </p>
                                </div>

                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-xs text-gray-500">Time Difference</p>
                                    <p class="mt-1 text-sm font-semibold text-gray-900">
                                        @if ($hoursDifference !== null)
                                            {{ number_format((float) $hoursDifference, 1) }} hours
                                        @else
                                            Not available
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if (!empty($evidence))
                                <div class="mt-4 border-t border-gray-100 pt-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Supporting Evidence
                                    </p>
                                    <ul class="mt-2 space-y-2">
                                        @foreach ($evidence as $item)
                                            <li class="flex gap-2 text-sm text-gray-600">
                                                <i class="fas fa-check text-green-500 mt-1"></i>
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <p class="mt-4 text-xs text-gray-500">
                                Relationship analysis is supporting information and does not,
                                by itself, establish that two complaints concern the same incident.
                            </p>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="py-6 text-center">
                    <i class="fas fa-link-slash text-2xl text-gray-300"></i>
                    <p class="mt-3 text-sm font-semibold text-gray-700">
                        No related complaints detected
                    </p>
                    <p class="mt-1 text-xs text-gray-500">
                        No possible matches were supplied for this complaint.
                    </p>
                </div>
            @endif
        </div>
    </details>

    {{-- Maintenance Scheduling History --}}
    @if ($workOrders->isNotEmpty())
        <details class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4
                px-5 py-4 sm:px-6 hover:bg-gray-50">
                <div class="min-w-0">
                    <h2 class="font-semibold text-gray-900">Maintenance Scheduling History</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Visit schedules, pending workload explanations, missed visit reasons,
                        and scheduling justifications.
                    </p>
                    <span class="inline-flex mt-2 rounded-full border border-blue-100 bg-blue-50
                        px-2.5 py-1 text-xs font-semibold text-blue-700">
                        {{ $workOrders->count() }}
                        {{ $workOrders->count() === 1 ? 'Visit record' : 'Visit records' }}
                    </span>
                </div>
                <i class="fas fa-chevron-down text-sm text-gray-400 transition-transform group-open:rotate-180"></i>
            </summary>

            <div class="space-y-4 border-t border-gray-100 p-5 sm:p-6">
                @foreach ($workOrders as $order)
                    @php
                        $visitDate = $order->schedule?->schedule_date;

                        $isPastUnstartedVisit = $visitDate
                            && \Illuminate\Support\Carbon::parse($visitDate)->startOfDay()->lt(today())
                            && !$order->started_at;

                        $hasMissedRecord = filled($order->missed_reason)
                            || filled($order->missed_manager_reason)
                            || $order->missed_confirmed_at
                            || $order->missed_reported_at;

                        $isMissedVisit = $hasMissedRecord
                            || ($order->status === 'Rescheduled' && $isPastUnstartedVisit);
                    @endphp

                    <article class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-sm font-bold text-gray-900">
                                    Visit {{ $order->visit_number }} · Work Order #{{ $order->id }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    <i class="far fa-calendar mr-1"></i>
                                    {{ $visitDate
                                        ? \Illuminate\Support\Carbon::parse($visitDate)->format('M d, Y')
                                        : 'No scheduled date recorded' }}
                                </p>
                                @if ($order->rescheduled_from_work_order_id)
                                    <p class="mt-2 text-xs text-blue-700">
                                        Replacement visit for Work Order
                                        #{{ $order->rescheduled_from_work_order_id }}
                                    </p>
                                @endif
                            </div>

                            <span class="inline-flex self-start rounded-full border border-gray-200
                                bg-white px-3 py-1 text-xs font-semibold text-gray-700">
                                {{ $order->status }}
                            </span>
                        </div>

                        {{-- Pending workload reason --}}
                        @if ($order->status === 'Pending Workload' || filled($order->pending_reason))
                            <div class="mt-4 rounded-xl border border-orange-200 bg-orange-50 p-4 space-y-2">
                                <h3 class="text-sm font-bold text-orange-900">
                                    <i class="fas fa-clock mr-1"></i>
                                    Pending Workload / Unfinished Visit
                                </h3>
                                <p class="text-xs text-orange-800">
                                    Recorded information about work that remains unfinished for this visit.
                                </p>

                                <p class="text-xs font-semibold text-orange-900">Recorded reason</p>
                                <p class="text-sm whitespace-pre-line break-words text-orange-950">
                                    {{ $order->pending_reason ?: 'No pending workload reason recorded.' }}
                                </p>

                                @if (filled($order->manager_remarks))
                                    <p class="text-xs font-semibold text-orange-900 pt-1">
                                        Manager Remarks
                                    </p>
                                    <p class="text-sm whitespace-pre-line break-words text-orange-950">
                                        {{ $order->manager_remarks }}
                                    </p>
                                @endif

                                @if ($order->pending_recorded_at)
                                    <p class="text-xs text-orange-700">
                                        Recorded
                                        {{ \Illuminate\Support\Carbon::parse($order->pending_recorded_at)->format('M d, Y h:i A') }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        {{-- Missed or rescheduled visit reasons --}}
                        @if ($isMissedVisit)
                            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-4 space-y-3">
                                <div>
                                    <h3 class="text-sm font-bold text-rose-900">
                                        <i class="fas fa-calendar-xmark mr-1"></i>
                                        Missed Schedule / Rescheduling Record
                                    </h3>
                                    <p class="mt-1 text-xs text-rose-800">
                                        This record is separate from Pending Workload.
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-rose-900">
                                        Plumber's Reported Reason
                                    </p>
                                    <p class="mt-1 text-sm whitespace-pre-line break-words text-rose-950">
                                        {{ $order->missed_reason ?: 'No plumber reason was submitted.' }}
                                    </p>
                                    @if ($order->missed_reported_at)
                                        <p class="mt-1 text-xs text-rose-700">
                                            Reported
                                            {{ \Illuminate\Support\Carbon::parse($order->missed_reported_at)->format('M d, Y h:i A') }}
                                        </p>
                                    @endif
                                </div>

                                <div class="border-t border-rose-200 pt-3">
                                    <p class="text-xs font-semibold text-rose-900">
                                        Manager's Verified Reason
                                    </p>
                                    <p class="mt-1 text-sm whitespace-pre-line break-words text-rose-950">
                                        {{ $order->missed_manager_reason
                                            ?: 'Not recorded for this visit (may be a legacy record).' }}
                                    </p>
                                    @if ($order->missed_confirmed_at)
                                        <p class="mt-1 text-xs text-rose-700">
                                            Confirmed
                                            {{ \Illuminate\Support\Carbon::parse($order->missed_confirmed_at)->format('M d, Y h:i A') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @elseif (
                            $isPastUnstartedVisit
                            && $order->status === 'Scheduled'
                            && $order->schedule?->status === 'Finalized'
                        )
                            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                <p class="text-sm font-semibold text-amber-900">
                                    Past Scheduled Visit — Awaiting Maintenance Verification
                                </p>
                                <p class="mt-1 text-xs text-amber-800">
                                    No start time is recorded. This alone does not establish that
                                    physical work did not occur.
                                </p>
                            </div>
                        @endif

                        {{-- Early scheduling justification --}}
                        @if ($order->is_late_addition && filled($order->late_addition_reason))
                            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                <h3 class="text-sm font-bold text-amber-900">
                                    <i class="fas fa-clipboard-check mr-1"></i>
                                    Early Scheduling Justification
                                </h3>
                                <p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-amber-900">
                                    {{ $order->late_addition_reason }}
                                </p>
                                <p class="mt-2 text-xs text-amber-700">
                                    Recorded by Maintenance Management
                                </p>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </details>
    @endif

    {{-- Maintenance Result --}}
    @if ($report)
        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-gray-900">Maintenance Result</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Accomplishment information submitted by the assigned plumber.
                        </p>
                    </div>

                    <span class="inline-flex self-start sm:self-auto px-2.5 py-1 rounded-full text-xs font-semibold
                        {{ $report->review_status === 'Approved'
                            ? 'bg-green-50 text-green-700'
                            : ($report->review_status === 'Returned'
                                ? 'bg-red-50 text-red-700'
                                : 'bg-amber-50 text-amber-700') }}">
                        {{ $report->review_status ?: 'Not Reviewed' }}
                    </span>
                </div>
            </div>

            <div class="p-5 sm:p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <p class="text-xs text-gray-500">Plumber</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $report->technician?->full_name ?? $assignedNames->first() ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Maintenance Started</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $report->started_at?->format('M d, Y h:i A') ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Accomplished</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $report->submitted_at?->format('M d, Y h:i A') ?? '—' }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-2">Diagnosis / Findings</p>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4
                            text-sm text-gray-700 whitespace-pre-line">
                            {{ $report->diagnosis ?: 'No diagnosis recorded.' }}
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-2">Root Cause</p>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4
                            text-sm text-gray-700 whitespace-pre-line">
                            {{ $report->root_cause ?: 'No root cause recorded.' }}
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-900 mb-2">Materials / Parts</p>
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4
                        text-sm text-gray-700 whitespace-pre-line">
                        {{ $report->materials_parts ?: 'None recorded.' }}
                    </div>
                </div>

                @if ($report->technician_notes)
                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-2">Plumber Notes</p>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4
                            text-sm text-gray-700 whitespace-pre-line">
                            {{ $report->technician_notes }}
                        </div>
                    </div>
                @endif

                @if ($report->before_photo || $report->after_photo)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @if ($report->before_photo)
                            <div>
                                <p class="text-sm font-semibold text-gray-900 mb-2">Before Maintenance</p>
                                <a href="{{ asset('storage/' . $report->before_photo) }}"
                                    target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset('storage/' . $report->before_photo) }}"
                                        alt="Before maintenance"
                                        loading="lazy"
                                        class="w-full max-h-80 object-cover rounded-xl border border-gray-200">
                                </a>
                            </div>
                        @endif

                        @if ($report->after_photo)
                            <div>
                                <p class="text-sm font-semibold text-gray-900 mb-2">After Maintenance</p>
                                <a href="{{ asset('storage/' . $report->after_photo) }}"
                                    target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset('storage/' . $report->after_photo) }}"
                                        alt="After maintenance"
                                        loading="lazy"
                                        class="w-full max-h-80 object-cover rounded-xl border border-gray-200">
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($report->review_remarks)
                    <div class="border-t border-gray-100 pt-5">
                        <p class="text-sm font-semibold text-gray-900 mb-2">
                            Manager Review Remarks
                        </p>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4
                            text-sm text-gray-700 whitespace-pre-line">
                            {{ $report->review_remarks }}
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 border-t border-gray-100 pt-5">
                    <div>
                        <p class="text-xs text-gray-500">Review Status</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $report->review_status ?: 'Not available' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Reviewed By</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $report->reviewer?->full_name ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Reviewed At</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $report->reviewed_at?->format('M d, Y h:i A') ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Customer Service Processing Result: read-only --}}
    @if (
        $isCommercial
        && $commercialResolution
        && $commercialResolution->initial_processing_completed_at
    )
        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">
                    Customer Service Processing Result
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Recorded findings and recommendation before closure or maintenance forwarding.
                </p>
            </div>

            <div class="p-5 sm:p-6 space-y-5">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-2">Findings</p>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4
                            text-sm text-gray-700 whitespace-pre-line">
                            {{ $commercialResolution->findings ?: 'No findings recorded.' }}
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-2">
                            Resolution / Recommendation
                        </p>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4
                            text-sm text-gray-700 whitespace-pre-line">
                            {{ $commercialResolution->resolution_remarks ?: 'No recommendation recorded.' }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 border-t border-gray-100 pt-5">
                    <div>
                        <p class="text-xs text-gray-500">Processing Started</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $commercialResolution->started_at?->format('M d, Y h:i A') ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Processing Completed</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $commercialResolution->initial_processing_completed_at?->format('M d, Y h:i A') ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Processed By</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $commercialResolution->processor?->full_name ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Forwarded to Maintenance</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $commercialResolution->forwarded_to_maintenance_at
                                ? \Illuminate\Support\Carbon::parse($commercialResolution->forwarded_to_maintenance_at)->format('M d, Y h:i A')
                                : 'Not recorded' }}
                        </p>
                    </div>
                </div>

                @if ($commercialResolution->forwarder)
                    <div>
                        <p class="text-xs text-gray-500">Forwarded By</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $commercialResolution->forwarder->full_name }}
                        </p>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- Complaint Tracking: timeline is informational only --}}
    <section class="bg-white border border-gray-200 rounded-2xl shadow-sm">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Complaint Tracking</h2>
            <p class="text-sm text-gray-500 mt-1">
                Recorded milestones and the current state of this complaint or service request.
            </p>
        </div>

        <div class="p-5 sm:p-6">
            <div class="max-w-4xl">

                {{-- Submitted --}}
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-9 h-9 rounded-full bg-green-100 text-green-700
                            flex items-center justify-center shrink-0">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <div class="w-px flex-1 min-h-12 bg-green-200"></div>
                    </div>

                    <div class="pb-6">
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $isCommercial ? 'Request Submitted' : 'Complaint Submitted' }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $complaint->created_at?->format('M d, Y • h:i A') ?? 'Date not recorded' }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Submitted by {{ $complaint->complainant_name ?: 'Consumer' }}
                        </p>
                    </div>
                </div>

                {{-- Verification / Rejection --}}
                @php
                    $verificationReached = $complaint->verified_at
                        || !in_array($complaint->status, ['Pending'], true);
                @endphp

                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                            {{ $verificationReached
                                ? ($complaint->status === 'Rejected'
                                    ? 'bg-red-100 text-red-700'
                                    : 'bg-green-100 text-green-700')
                                : 'bg-gray-100 text-gray-400' }}">
                            <i class="fas {{ $verificationReached ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                        </div>
                        @if ($complaint->status !== 'Rejected')
                            <div class="w-px flex-1 min-h-12
                                {{ $verificationReached ? 'bg-green-200' : 'bg-gray-200' }}"></div>
                        @endif
                    </div>

                    <div class="pb-6">
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $complaint->status === 'Rejected' ? 'Rejected' : 'Verified' }}
                        </p>

                        @if ($complaint->verified_at)
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $complaint->verified_at->format('M d, Y • h:i A') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $complaint->status === 'Rejected' ? 'Reviewed' : 'Verified' }}
                                by {{ $complaint->verifier?->full_name ?? 'Customer Service' }}
                            </p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $complaint->status === 'Pending'
                                    ? 'Waiting for Customer Service verification'
                                    : 'Verification timestamp not recorded' }}
                            </p>
                        @endif

                        @if ($complaint->verification_reason)
                            <div class="mt-3 rounded-lg border border-gray-200 bg-gray-50 p-3">
                                <p class="text-xs font-semibold text-gray-600">
                                    Verification / Rejection Reason
                                </p>
                                <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">
                                    {{ $complaint->verification_reason }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($complaint->status !== 'Rejected')

                    @if ($isCommercial)
                        {{-- Commercial processing --}}
                        @php
                            $processingStarted = (bool) $commercialResolution?->started_at;
                            $processingCompleted = (bool) $commercialResolution?->initial_processing_completed_at;
                            $forwarded = (bool) $commercialResolution?->forwarded_to_maintenance_at
                                || in_array(
                                    $complaint->status,
                                    ['For Maintenance', 'Assigned', 'In Progress', 'Completed', 'Closed'],
                                    true
                                );
                        @endphp

                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                    {{ $processingStarted ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                    <i class="fas {{ $processingStarted ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                </div>
                                <div class="w-px flex-1 min-h-12
                                    {{ $processingStarted ? 'bg-green-200' : 'bg-gray-200' }}"></div>
                            </div>

                            <div class="pb-6">
                                <p class="text-sm font-semibold text-gray-900">
                                    Initial Processing Started
                                </p>
                                @if ($processingStarted)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $commercialResolution->started_at->format('M d, Y • h:i A') }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Started by
                                        {{ $commercialResolution->processor?->full_name ?? 'Customer Service' }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 mt-1">Not started</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                    {{ $processingCompleted ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                    <i class="fas {{ $processingCompleted ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                </div>
                                <div class="w-px flex-1 min-h-12
                                    {{ $processingCompleted ? 'bg-green-200' : 'bg-gray-200' }}"></div>
                            </div>

                            <div class="pb-6">
                                <p class="text-sm font-semibold text-gray-900">
                                    Initial Processing Completed
                                </p>
                                @if ($processingCompleted)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $commercialResolution->initial_processing_completed_at->format('M d, Y • h:i A') }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Processed by
                                        {{ $commercialResolution->processor?->full_name ?? 'Customer Service' }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 mt-1">Pending</p>
                                @endif
                            </div>
                        </div>

                        @if (!$isClosedWithoutMaintenance)
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                        {{ $forwarded ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                        <i class="fas {{ $forwarded ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                    </div>
                                    <div class="w-px flex-1 min-h-12
                                        {{ $forwarded ? 'bg-green-200' : 'bg-gray-200' }}"></div>
                                </div>

                                <div class="pb-6">
                                    <p class="text-sm font-semibold text-gray-900">
                                        Forwarded to Maintenance
                                    </p>
                                    @if ($forwarded)
                                        @if ($commercialResolution?->forwarded_to_maintenance_at)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ \Illuminate\Support\Carbon::parse($commercialResolution->forwarded_to_maintenance_at)->format('M d, Y • h:i A') }}
                                            </p>
                                        @endif
                                        <p class="text-xs text-gray-500 mt-1">
                                            Forwarded by
                                            {{ $commercialResolution?->forwarder?->full_name ?? 'Customer Service' }}
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-400 mt-1">
                                            Pending Customer Service decision
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @else
                        {{-- Engineering complaints --}}
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                    {{ $complaint->verified_at ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                    <i class="fas {{ $complaint->verified_at ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                </div>
                                <div class="w-px flex-1 min-h-12
                                    {{ $complaint->verified_at ? 'bg-green-200' : 'bg-gray-200' }}"></div>
                            </div>
                            <div class="pb-6">
                                <p class="text-sm font-semibold text-gray-900">
                                    Forwarded to Maintenance
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $complaint->verified_at
                                        ? 'Ready for Maintenance after verification'
                                        : 'Pending verification' }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if (!$isClosedWithoutMaintenance)
                        {{-- Assignment --}}
                        @php
                            $assigned = $complaint->technicians->isNotEmpty();
                        @endphp

                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                    {{ $assigned ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                    <i class="fas {{ $assigned ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                </div>
                                <div class="w-px flex-1 min-h-12
                                    {{ $assigned ? 'bg-green-200' : 'bg-gray-200' }}"></div>
                            </div>

                            <div class="pb-6">
                                <p class="text-sm font-semibold text-gray-900">Plumber Assigned</p>
                                @if ($assigned)
                                    @if ($firstAssignedAt)
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ \Illuminate\Support\Carbon::parse($firstAssignedAt)->format('M d, Y • h:i A') }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">
                                        Assigned to {{ $assignedNames->join(', ') }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 mt-1">
                                        No plumber assignment recorded
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Maintenance started --}}
                        @php
                            $maintenanceStarted = $report?->started_at ?: $firstStartedAt;
                        @endphp

                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                    {{ $maintenanceStarted ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                    <i class="fas {{ $maintenanceStarted ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                </div>
                                <div class="w-px flex-1 min-h-12
                                    {{ $maintenanceStarted ? 'bg-green-200' : 'bg-gray-200' }}"></div>
                            </div>

                            <div class="pb-6">
                                <p class="text-sm font-semibold text-gray-900">Maintenance Started</p>
                                @if ($maintenanceStarted)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ \Illuminate\Support\Carbon::parse($maintenanceStarted)->format('M d, Y • h:i A') }}
                                    </p>
                                    @if ($report?->technician)
                                        <p class="text-xs text-gray-500 mt-1">
                                            Started by {{ $report->technician->full_name }}
                                        </p>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-400 mt-1">Not started</p>
                                @endif
                            </div>
                        </div>

                        {{-- Accomplishment --}}
                        @php
                            $accomplished = (bool) $report?->submitted_at;
                        @endphp

                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                    {{ $accomplished ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                    <i class="fas {{ $accomplished ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                </div>
                                <div class="w-px flex-1 min-h-12
                                    {{ $accomplished ? 'bg-green-200' : 'bg-gray-200' }}"></div>
                            </div>

                            <div class="pb-6">
                                <p class="text-sm font-semibold text-gray-900">Maintenance Accomplished</p>
                                @if ($accomplished)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $report->submitted_at->format('M d, Y • h:i A') }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Accomplished by
                                        {{ $report->technician?->full_name ?? $assignedNames->first() ?? 'Assigned plumber' }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 mt-1">Pending</p>
                                @endif
                            </div>
                        </div>

                        {{-- Manager review --}}
                        @php
                            $reviewed = (bool) $report?->reviewed_at;
                        @endphp

                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                    {{ $reviewed ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                    <i class="fas {{ $reviewed ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                </div>
                                <div class="w-px flex-1 min-h-12
                                    {{ $reviewed ? 'bg-green-200' : 'bg-gray-200' }}"></div>
                            </div>

                            <div class="pb-6">
                                <p class="text-sm font-semibold text-gray-900">Manager Review</p>
                                @if ($reviewed)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $report->reviewed_at->format('M d, Y • h:i A') }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $report->review_status }}
                                        @if ($report->reviewer)
                                            by {{ $report->reviewer->full_name }}
                                        @endif
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $report?->review_status === 'Pending Review'
                                            ? 'Awaiting manager review'
                                            : 'Pending' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Final closure --}}
                    @php
                        $maintenanceClosed = $complaint->status === 'Closed';
                        $closureDate = $maintenanceClosed
                            ? ($complaint->closed_at ?? $complaint->completed_at)
                            : null;
                    @endphp

                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                {{ $maintenanceClosed ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }}">
                                <i class="fas {{ $maintenanceClosed ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $isCommercial ? 'Request Closed' : 'Complaint Closed' }}
                            </p>

                            @if ($maintenanceClosed)
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $closureDate?->format('M d, Y • h:i A') ?? 'Closure date not recorded' }}
                                </p>
                                <p class="text-xs text-green-700 mt-1">
                                    Complaint officially closed
                                </p>
                            @elseif ($reportApproved)
                                <p class="text-xs text-amber-600 mt-1">
                                    Maintenance report approved; final closure is not recorded.
                                </p>
                            @else
                                <p class="text-xs text-gray-400 mt-1">
                                    Final closure not recorded
                                </p>
                            @endif
                        </div>
                    </div>

                @endif
            </div>
        </div>
    </section>

</div>

@if (!$isCommercial && $complaint->latitude !== null && $complaint->longitude !== null)
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const mapElement = document.getElementById('complaint-map');

                if (!mapElement || typeof L === 'undefined') {
                    return;
                }

                const latitude = {{ (float) $complaint->latitude }};
                const longitude = {{ (float) $complaint->longitude }};

                const map = L.map('complaint-map', {
                    zoomControl: true,
                    attributionControl: true
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                map.setView([latitude, longitude], 17);

                L.marker([latitude, longitude])
                    .addTo(map)
                    .bindPopup(
                        @json($complaint->complaint_no)
                        + '<br>'
                        + @json($complaint->category?->name ?? 'Water Service Concern')
                    );

                setTimeout(function () {
                    map.invalidateSize();
                }, 200);
            });
        </script>
    @endpush
@endif
@endsection
```
