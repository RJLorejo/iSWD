@extends('maintenance-manager.layouts.app')

@section('title', 'Complaint Details')

@section('content')

    @php
        $divisionName = strtolower(trim((string) $complaint->division?->name));

        $isEngineering = $isEngineering ?? str_contains($divisionName, 'engineering');
        $isCommercial = str_contains($divisionName, 'commercial');

        $canAssign =
            $canAssign ??
            (($isEngineering && $complaint->status === 'Verified') || $complaint->status === 'For Maintenance') &&
                $complaint->technicians->isEmpty();

        $commercialResolution = $complaint->commercialResolution;
        $maintenanceReport = $complaint->maintenanceReport;

        $hasInitialProcessing =
            $commercialResolution &&
            ($commercialResolution->started_at ||
                $commercialResolution->initial_processing_completed_at ||
                filled($commercialResolution->findings) ||
                filled($commercialResolution->resolution_remarks));

        $wasForwardedToMaintenance = (bool) $commercialResolution?->forwarded_to_maintenance_at;

        $reportSubmitted =
            $maintenanceReport !== null &&
            $maintenanceReport->submitted_at !== null &&
            in_array($maintenanceReport->review_status, ['Pending Review', 'Returned', 'Approved'], true);

        if ($isCommercial && $complaint->consumer) {
            $displayAddress =
                $complaint->consumer->address?->full_address ??
                ($complaint->address ?? 'No registered service address recorded.');

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

        /*
                |--------------------------------------------------------------------------
                | Effective Urgency
                |--------------------------------------------------------------------------
                | Customer Service's verified urgency takes priority.
        | AI-predicted urgency is used when no verified value exists.
        */
$urgencyLevel =
    $complaint->aiAnalysis?->verified_urgency_level ?: $complaint->aiAnalysis?->urgency_level ?: 'Not Assessed';

$urgencyClass = match ($urgencyLevel) {
    'High' => 'bg-red-50 text-red-700 border-red-200',
    'Moderate' => 'bg-amber-50 text-amber-700 border-amber-200',
    'Low' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    default => 'bg-gray-50 text-gray-600 border-gray-200',
};

$displayStatus = $complaint->status === 'Completed' ? 'Accomplished' : $complaint->status;

$statusClasses = match ($complaint->status) {
    'Verified' => 'bg-blue-50 text-blue-700 border-blue-200',
    'For Maintenance' => 'bg-violet-50 text-violet-700 border-violet-200',
    'Assigned' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
    'In Progress' => 'bg-amber-50 text-amber-700 border-amber-200',
    'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'Closed' => 'bg-gray-100 text-gray-700 border-gray-200',
    default => 'bg-gray-50 text-gray-600 border-gray-200',
};

/*
        |--------------------------------------------------------------------------
        | Work Orders
        |--------------------------------------------------------------------------
        | The work-order pivot is authoritative for each scheduled visit.
        | Complaint-level technician associations are historical only.
        */
$workOrders = \App\Models\MaintenanceWorkOrder::query()
    ->with(['schedule', 'technicians'])
    ->where('complaint_id', $complaint->id)
    ->orderBy('visit_number')
    ->orderBy('id')
    ->get();

$latestWorkOrder = $workOrders->last();

$activeWorkOrder = $workOrders
    ->filter(fn($order) => in_array($order->status, ['Scheduled', 'In Progress'], true))
    ->last();

$displayWorkOrder = $activeWorkOrder ?? $latestWorkOrder;

$workOrderTeam = $displayWorkOrder?->technicians ?? collect();

$hasWorkOrderTeam = $workOrders->contains(fn($order) => $order->technicians->isNotEmpty());

$firstAssignedAt = $complaint->technicians
    ->map(fn($technician) => $technician->pivot?->assigned_at)
    ->filter()
    ->map(fn($date) => \Carbon\Carbon::parse($date))
    ->sort()
    ->first();

$firstStartedAt = $workOrders
    ->pluck('started_at')
    ->filter()
    ->map(fn($date) => \Carbon\Carbon::parse($date))
    ->sort()
    ->first();

$firstCompletedAt = $workOrders
    ->pluck('completed_at')
    ->filter()
    ->map(fn($date) => \Carbon\Carbon::parse($date))
    ->sort()
    ->first();

$historicalTechnicians = $complaint->technicians;

$maintenanceStarted =
    $firstStartedAt !== null || in_array($complaint->status, ['In Progress', 'Completed', 'Closed'], true);

$maintenanceAccomplished = $reportSubmitted || in_array($complaint->status, ['Completed', 'Closed'], true);

$managerReviewed = $maintenanceReport && $maintenanceReport->review_status === 'Approved';

$isClosed = $complaint->status === 'Closed';

$backUrl = $backUrl ?? route('maintenance-manager.complaints.index');
$backLabel = $backLabel ?? 'Back to Complaints';

$hasMaintenanceWorkflow =
    !$hasInitialProcessing || $wasForwardedToMaintenance || $isEngineering || $workOrders->isNotEmpty();

$latestPendingOrder = $workOrders->where('status', 'Pending Workload')->last();

$eligibleFollowUpPlumbers = \App\Models\User::role('Maintenance Technician')
    ->where('is_active', true)
    ->orderBy('first_name')
    ->orderBy('last_name')
    ->get();

$hasUnresolvedPendingWorkload =
    $latestPendingOrder &&
    !$workOrders->contains(
        fn($order) => $order->visit_number > $latestPendingOrder->visit_number &&
            in_array($order->status, ['Scheduled', 'In Progress', 'Completed'], true),
            );
    @endphp

    <div class="mx-auto max-w-7xl space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

            <div>
                <div class="flex flex-wrap items-center gap-2 text-sm">

                    <a href="{{ $backUrl }}"
                        class="inline-flex items-center gap-2 rounded-xl
                               border border-slate-200 bg-white px-4 py-2.5
                               text-sm font-semibold text-slate-700
                               transition hover:bg-slate-50">
                        <i class="fas fa-arrow-left"></i>
                        {{ $backLabel }}
                    </a>

                    <span class="text-gray-300">/</span>

                    <span class="text-gray-500">
                        {{ $complaint->complaint_no }}
                    </span>
                </div>

                <h1 class="mt-3 text-2xl font-bold text-gray-900 sm:text-3xl">
                    {{ $complaint->category?->name ?? 'Water Service Concern' }}
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $complaint->complaint_no }}
                </p>
            </div>

            <span
                class="inline-flex w-fit items-center gap-2 rounded-full
                       border px-3 py-2 text-sm font-semibold {{ $statusClasses }}">
                <span class="h-2 w-2 rounded-full bg-current"></span>
                {{ $displayStatus }}
            </span>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800">
                <i class="fas fa-circle-check mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
                <i class="fas fa-circle-exclamation mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Summary --}}
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">Status</p>
                <p class="mt-1 text-sm font-bold text-gray-900">
                    {{ $displayStatus }}
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">Urgency</p>
                <span class="mt-1 inline-flex rounded-full border px-2.5 py-1 text-xs font-bold {{ $urgencyClass }}">
                    {{ $urgencyLevel }}
                </span>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">Division</p>
                <p class="mt-1 text-sm font-bold text-gray-900">
                    {{ $complaint->division?->name ?? 'Not specified' }}
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">Submitted</p>
                <p class="mt-1 text-sm font-bold text-gray-900">
                    {{ $complaint->created_at?->format('M d, Y') }}
                </p>
                <p class="mt-0.5 text-xs text-gray-400">
                    {{ $complaint->created_at?->format('h:i A') }}
                </p>
            </div>

        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            {{-- Main column --}}
            <div class="space-y-6 xl:col-span-2">

                {{-- Pending Workload Review --}}
                @if ($workOrders->contains(fn($order) => $order->status === 'Pending Workload'))
                    <section id="pending-workload-review"
                        class="overflow-hidden rounded-2xl border border-orange-200 bg-white shadow-sm">

                        <div class="border-b border-orange-100 bg-orange-50 px-5 py-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h2 class="flex items-center gap-2 font-bold text-orange-950">
                                        <i class="fas fa-clipboard-list"></i>
                                        Pending Workload Review
                                    </h2>
                                </div>

                                @if ($hasUnresolvedPendingWorkload)
                                    <span
                                        class="rounded-full border border-orange-300 bg-white px-3 py-1 text-xs font-bold text-orange-800">
                                        Awaiting Follow-up
                                    </span>
                                @else
                                    <span
                                        class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                        Historical Record
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-4 p-5">

                            @php
                                $pendingOrders = $workOrders
                                    ->where('status', 'Pending Workload')
                                    ->sortByDesc('visit_number')
                                    ->values();

                                $pendingOrderCount = $pendingOrders->count();
                            @endphp

                            @foreach ($pendingOrders as $pendingIndex => $pendingOrder)
                                {{-- Start collapsed history after the latest pending record --}}
                                @if ($pendingIndex === 1)
                                    <details class="overflow-hidden rounded-xl border border-orange-200 bg-white">
                                        <summary
                                            class="flex cursor-pointer list-none items-center justify-between gap-3 p-4 transition hover:bg-orange-50">
                                            <div>
                                                <p class="text-sm font-bold text-slate-900">
                                                    <i class="fas fa-clock-rotate-left mr-2 text-orange-600"></i>
                                                    View previous pending workload records
                                                </p>
                                                <p class="mt-1 text-xs text-slate-500">
                                                    {{ $pendingOrderCount - 1 }}
                                                    previous {{ $pendingOrderCount - 1 === 1 ? 'record' : 'records' }}
                                                </p>
                                            </div>

                                            <i class="fas fa-chevron-down shrink-0 text-orange-600"></i>
                                        </summary>

                                        <div class="space-y-4 border-t border-orange-100 bg-orange-50/30 p-3">
                                @endif

                                @php
                                    $hasLaterVisit = $workOrders->contains(
                                        fn($candidate) => (int) $candidate->visit_number >
                                            (int) $pendingOrder->visit_number,
                                    );
                                @endphp

                                <div class="rounded-xl border border-orange-100 bg-orange-50/40 p-4">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">
                                                Visit {{ $pendingOrder->visit_number }} · Work Order
                                                #{{ $pendingOrder->id }}
                                            </p>
                                            <p class="mt-1 text-xs text-slate-500">
                                                Scheduled:
                                                {{ $pendingOrder->schedule?->schedule_date
                                                    ? \Carbon\Carbon::parse($pendingOrder->schedule->schedule_date)->format('M d, Y')
                                                    : 'Date unavailable' }}
                                            </p>
                                        </div>

                                        <span
                                            class="rounded-full border border-orange-200 bg-orange-100 px-2.5 py-1 text-[11px] font-semibold text-orange-800">
                                            Pending Workload
                                        </span>
                                    </div>

                                    <div class="mt-3 rounded-lg border border-orange-200 bg-white p-3">
                                        <p class="text-xs font-bold text-orange-900">
                                            <i class="fas fa-circle-exclamation mr-1"></i>
                                            Reason for Unfinished Maintenance
                                        </p>
                                        <p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-slate-700">
                                            {{ $pendingOrder->pending_reason ?: 'No reason recorded.' }}</p>
                                    </div>

                                    <div class="mt-3 grid gap-3 text-xs text-slate-600 sm:grid-cols-2">
                                        <div>
                                            <span class="font-semibold text-slate-700">Maintenance started:</span>
                                            {{ $pendingOrder->started_at
                                                ? \Carbon\Carbon::parse($pendingOrder->started_at)->format('M d, Y h:i:s A') . ' PHT'
                                                : 'Not recorded' }}
                                        </div>

                                        <div>
                                            <span class="font-semibold text-slate-700">Pending recorded:</span>
                                            {{ $pendingOrder->pending_recorded_at
                                                ? \Carbon\Carbon::parse($pendingOrder->pending_recorded_at)->format('M d, Y h:i:s A') . ' PHT'
                                                : 'Not recorded' }}
                                        </div>
                                    </div>

                                    @if ($pendingOrder->technicians->isNotEmpty())
                                        <p class="mt-3 text-xs text-slate-600">
                                            <span class="font-semibold">Assigned team:</span>
                                            {{ $pendingOrder->technicians->pluck('full_name')->filter()->implode(', ') }}
                                        </p>
                                    @endif

                                    @if ($hasLaterVisit)
                                        <p class="mt-3 text-xs font-medium text-slate-500">
                                            <i class="fas fa-clock-rotate-left mr-1"></i>
                                            A later visit exists; this unfinished visit remains part of the permanent
                                            history.
                                        </p>
                                    @endif
                                </div>
                            @endforeach

                            @if ($pendingOrderCount > 1)
                        </div>
                        </details>
                @endif

                {{-- Keep follow-up actions outside the collapsed history --}}
                @if ($hasUnresolvedPendingWorkload && !in_array($complaint->status, ['Completed', 'Closed'], true))
                    <div
                        class="flex flex-col gap-3 rounded-xl border border-sky-200 bg-sky-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs leading-5 text-sky-900">
                            Create a Draft follow-up below, then approve it in Daily Maintenance Planning. The original
                            visit remains in history.
                        </p>

                        <a href="#same-day-follow-up-form"
                            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-3 text-xs font-bold text-white transition hover:bg-sky-800">
                            <i class="fas fa-calendar-plus"></i>
                            Plan Follow-up Visit
                        </a>
                    </div>
                @endif

                @if ($hasUnresolvedPendingWorkload && !in_array($complaint->status, ['Completed', 'Closed'], true))
                    <form id="same-day-follow-up-form" method="POST"
                        action="{{ route('maintenance-manager.schedules.pending-follow-up', $latestPendingOrder) }}"
                        onsubmit="return confirmFollowUpDraft(event)"
                        class="space-y-4 rounded-xl border border-sky-200 bg-white p-4">

                        @csrf

                        <h3 class="text-sm font-bold text-slate-900">Create Follow-up Work Order</h3>

                        <p class="text-xs text-slate-600">
                            Select the follow-up date and team. Approval is completed in Daily Maintenance Planning.
                        </p>

                        @if ($errors->any())
                            <div class="rounded-lg bg-red-50 p-3 text-xs text-red-700">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <div>
                            <label for="followup_date" class="mb-1 block text-xs font-semibold text-slate-700">
                                Follow-up date
                            </label>

                            <input id="followup_date" type="date" name="schedule_date" required
                                min="{{ now('Asia/Manila')->toDateString() }}"
                                value="{{ old('schedule_date', now('Asia/Manila')->toDateString()) }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        </div>

                        <div>
                            <p class="mb-2 text-xs font-semibold text-slate-700">Assign plumber(s)</p>

                            <div class="max-h-44 space-y-2 overflow-y-auto rounded-lg border border-slate-200 p-3">
                                @forelse ($eligibleFollowUpPlumbers as $plumber)
                                    <label class="flex items-center gap-2 text-sm text-slate-700">
                                        <input type="checkbox" name="technician_ids[]" value="{{ $plumber->id }}"
                                            @checked(in_array($plumber->id, array_map('intval', old('technician_ids', []))))>
                                        {{ $plumber->full_name }}
                                    </label>
                                @empty
                                    <p class="text-xs text-amber-700">No active maintenance technicians found.</p>
                                @endforelse
                            </div>

                            <p class="mt-1 text-xs text-slate-500">
                                Only technicians with valid duty assignments for the selected date can be approved.
                            </p>
                        </div>

                        <div>
                            <label for="followup_remarks" class="mb-1 block text-xs font-semibold text-slate-700">
                                Manager remarks (optional)
                            </label>

                            <textarea id="followup_remarks" name="manager_remarks" maxlength="2000" rows="2"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('manager_remarks') }}</textarea>
                        </div>

                        <button type="submit"
                            class="rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white hover:bg-sky-800">
                            Create Draft Follow-up
                        </button>

                        <p id="followup-form-error" role="alert" class="hidden text-xs font-semibold text-red-700"></p>
                    </form>
                @endif

            </div>
            </section>
            @endif

            {{-- Complaint Information --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="font-bold text-gray-900">Complaint Information</h2>
                    <p class="mt-1 text-xs text-gray-500">
                        Consumer concern and service details
                    </p>
                </div>

                <div class="space-y-6 p-5">

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Consumer</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown Consumer') }}
                            </p>

                            @if ($complaint->consumer?->account_number)
                                <p class="mt-0.5 text-xs text-blue-600">
                                    Account {{ $complaint->consumer->account_number }}
                                </p>
                            @endif
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Contact</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $complaint->complainant_phone ?? ($complaint->consumer?->phone ?? 'Not provided') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Complaint Type</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                {{ $complaint->category?->name ?? 'Uncategorized' }}
                            </p>
                        </div>

                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Concern</p>
                        <div class="mt-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                            <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                {{ $complaint->description }}
                            </p>
                        </div>
                    </div>

                    {{-- Location --}}
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            {{ $locationLabel }}
                        </p>

                        <div class="mt-3 space-y-3">
                            <div class="flex items-start gap-3 rounded-xl border border-sky-100 bg-sky-50/60 p-4">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                                    <i class="fas fa-location-dot"></i>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-sky-700">Service Address
                                    </p>
                                    <p class="mt-1 break-words text-sm font-semibold leading-6 text-slate-900">
                                        {{ $displayAddress }}</p>

                                    @if ($isEngineering && $complaint->landmark)
                                        <p class="mt-1 text-xs text-slate-600">Landmark: {{ $complaint->landmark }}</p>
                                    @endif

                                    @if ($isCommercial && $complaint->consumer)
                                        <p class="mt-1 text-xs text-sky-700">Registered service location of the linked SWD
                                            consumer account.</p>
                                    @endif
                                </div>
                            </div>

                            @if ($hasDisplayCoordinates)
                                <div
                                    class="mx-auto w-full max-w-3xl overflow-hidden rounded-xl border border-gray-200 bg-gray-100 shadow-sm">
                                    <div id="complaintMap" class="h-[260px] w-full sm:h-[330px] lg:h-[380px]"></div>
                                </div>
                            @else
                                <div
                                    class="mx-auto flex h-[230px] w-full max-w-3xl items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 p-6 text-center">
                                    <div>
                                        <i class="fas fa-map-location-dot text-2xl text-gray-400"></i>
                                        <p class="mt-3 text-sm font-semibold text-gray-700">Map unavailable</p>
                                        <p class="mt-1 text-xs text-gray-500">No saved coordinates are available for this
                                            location.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Supporting Photos --}}
                    @php
                        $supportingPhotos = $complaint->photos ?? collect();
                        $legacyPhoto = $supportingPhotos->isEmpty() ? $complaint->photo : null;
                        $supportingPhotoCount = $supportingPhotos->count() + ($legacyPhoto ? 1 : 0);
                    @endphp

                    @if ($supportingPhotoCount > 0)
                        <div class="border-t border-gray-100 pt-5">

                            <div class="mb-4 flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Supporting
                                        Photos</p>
                                    <p class="mt-1 text-xs text-gray-500">Photos submitted with this complaint.</p>
                                </div>

                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                    <i class="fas fa-images"></i>
                                    {{ $supportingPhotoCount }}
                                    {{ $supportingPhotoCount === 1 ? 'Photo' : 'Photos' }}
                                </span>
                            </div>

                            <div class="flex flex-wrap justify-center gap-4">

                                @foreach ($supportingPhotos as $photo)
                                    <a href="{{ asset('storage/' . $photo->photo) }}" target="_blank"
                                        rel="noopener noreferrer"
                                        class="group relative block w-full max-w-[320px] overflow-hidden rounded-xl border border-gray-200 bg-gray-50 shadow-sm sm:w-[calc(50%-0.5rem)] lg:w-[calc(33.333%-0.75rem)]">

                                        <img src="{{ asset('storage/' . $photo->photo) }}"
                                            alt="Supporting photo {{ $loop->iteration }}" loading="lazy"
                                            class="h-56 w-full object-contain transition duration-300 group-hover:scale-[1.02]">

                                        <div
                                            class="absolute inset-x-0 bottom-0 flex items-center justify-between bg-gradient-to-t from-black/70 to-transparent px-3 pb-3 pt-8">
                                            <span class="text-xs font-semibold text-white">Photo
                                                {{ $loop->iteration }}</span>
                                            <i class="fas fa-up-right-from-square text-xs text-white"></i>
                                        </div>
                                    </a>
                                @endforeach

                                @if ($legacyPhoto)
                                    <a href="{{ asset('storage/' . $legacyPhoto) }}" target="_blank"
                                        rel="noopener noreferrer"
                                        class="group relative block w-full max-w-[320px] overflow-hidden rounded-xl border border-gray-200 bg-gray-50 shadow-sm sm:w-[calc(50%-0.5rem)] lg:w-[calc(33.333%-0.75rem)]">

                                        <img src="{{ asset('storage/' . $legacyPhoto) }}" alt="Supporting photo"
                                            loading="lazy" class="h-56 w-full object-contain">

                                        <div
                                            class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent px-3 pb-3 pt-8">
                                            <span class="text-xs font-semibold text-white">Photo 1</span>
                                        </div>
                                    </a>
                                @endif

                            </div>
                        </div>
                    @endif

                </div>
            </div>

            {{-- Customer Service Processing --}}
            @if ($hasInitialProcessing)
                <div class="overflow-hidden rounded-2xl border border-sky-200 bg-white shadow-sm">

                    <div class="border-b border-sky-100 bg-sky-50/60 px-5 py-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                                <i class="fas fa-clipboard-check"></i>
                            </div>

                            <div>
                                <h2 class="font-bold text-gray-900">Customer Service Processing Result</h2>
                                <p class="mt-1 text-xs text-gray-500">
                                    Initial findings and recommendation recorded before maintenance handoff.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 p-5">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <p class="text-xs text-gray-500">Processing Started</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $commercialResolution->started_at?->format('M d, Y h:i A') ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">Processed By</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $commercialResolution->processor?->full_name ?? 'Customer Service' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">Initial Processing Completed</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $commercialResolution->initial_processing_completed_at?->format('M d, Y h:i A') ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-500">Forwarded to Maintenance</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $commercialResolution->forwarded_to_maintenance_at?->format('M d, Y h:i A') ?? 'Not forwarded' }}
                                </p>

                                @if ($commercialResolution->forwarded_to_maintenance_at)
                                    <p class="mt-0.5 text-xs text-gray-500">
                                        By {{ $commercialResolution->forwarder?->full_name ?? 'Customer Service' }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h3 class="mb-2 text-sm font-bold text-gray-900">
                                <i class="fas fa-magnifying-glass mr-2 text-sky-600"></i>
                                Findings
                            </h3>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                    {{ $commercialResolution->findings ?: 'No findings recorded.' }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <h3 class="mb-2 text-sm font-bold text-gray-900">
                                <i class="fas fa-route mr-2 text-emerald-600"></i>
                                Resolution / Recommendation
                            </h3>
                            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                                <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                    {{ $commercialResolution->resolution_remarks ?: 'No resolution or recommendation recorded.' }}
                                </p>
                            </div>
                        </div>

                        @if ($wasForwardedToMaintenance)
                            <div class="rounded-xl border border-violet-100 bg-violet-50 p-4">
                                <p class="text-sm font-semibold text-violet-900">
                                    <i class="fas fa-circle-info mr-2"></i>
                                    Maintenance Handoff
                                </p>
                                <p class="mt-1 text-xs leading-5 text-violet-700">
                                    Customer Service determined that this request requires physical
                                    maintenance work and forwarded it for maintenance planning.
                                </p>
                            </div>
                        @endif

                    </div>
                </div>
            @endif

            {{-- Complaint Tracking --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="font-bold text-gray-900">Complaint Tracking</h2>
                    <p class="mt-1 text-xs text-gray-500">Processing and maintenance history</p>
                </div>

                <div class="p-5">
                    @php
                        $trackingSteps = [];

                        $trackingSteps[] = [
                            'title' => $isCommercial ? 'Request Submitted' : 'Complaint Submitted',
                            'done' => true,
                            'detail' => $complaint->created_at?->format('M d, Y • h:i A'),
                            'subdetail' =>
                                'Submitted by ' .
                                ($complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Consumer')),
                        ];

                        $trackingSteps[] = [
                            'title' => 'Verified',
                            'done' => (bool) $complaint->verified_at,
                            'detail' => $complaint->verified_at?->format('M d, Y • h:i A'),
                            'subdetail' => $complaint->verified_at
                                ? 'Verified by ' . ($complaint->verifier?->full_name ?? 'Customer Service')
                                : 'Pending verification',
                        ];

                        if ($hasInitialProcessing) {
                            $trackingSteps[] = [
                                'title' => 'Initial Processing Started',
                                'done' => (bool) $commercialResolution->started_at,
                                'detail' => $commercialResolution->started_at?->format('M d, Y • h:i A'),
                                'subdetail' => $commercialResolution->started_at
                                    ? 'Processed by ' .
                                        ($commercialResolution->processor?->full_name ??
                                            'Customer Service (name not recorded)')
                                    : 'Pending processing',
                            ];

                            $trackingSteps[] = [
                                'title' => 'Initial Processing Completed',
                                'done' => (bool) $commercialResolution->initial_processing_completed_at,
                                'detail' => $commercialResolution->initial_processing_completed_at?->format(
                                    'M d, Y • h:i A',
                                ),
                                'subdetail' => $commercialResolution->initial_processing_completed_at
                                    ? 'Processed by ' .
                                        ($commercialResolution->processor?->full_name ?? 'Customer Service')
                                    : 'Pending',
                            ];
                        }

                        if ($hasInitialProcessing || $isEngineering) {
                            $forwarded =
                                $wasForwardedToMaintenance || ($isEngineering && (bool) $complaint->verified_at);

                            $trackingSteps[] = [
                                'title' => 'Forwarded to Maintenance',
                                'done' => $forwarded,
                                'detail' => $wasForwardedToMaintenance
                                    ? $commercialResolution->forwarded_to_maintenance_at?->format('M d, Y • h:i A')
                                    : null,
                                'subdetail' => $forwarded
                                    ? 'Forwarded by ' .
                                        ($commercialResolution?->forwarder?->full_name ??
                                            'Customer Service (name not recorded)')
                                    : 'Pending Customer Service decision',
                            ];
                        }

                        if ($hasMaintenanceWorkflow) {
                            $trackingSteps[] = [
                                'title' => 'Maintenance Team Scheduled',
                                'done' => $hasWorkOrderTeam,
                                'detail' => $displayWorkOrder?->created_at?->format('M d, Y • h:i A'),
                                'subdetail' => $hasWorkOrderTeam
                                    ? 'Team: ' .
                                        ($workOrderTeam->pluck('full_name')->filter()->implode(', ') ?:
                                            'Name not recorded') .
                                        ($displayWorkOrder?->schedule?->schedule_date
                                            ? ' · Work date: ' .
                                                \Carbon\Carbon::parse(
                                                    $displayWorkOrder->schedule->schedule_date,
                                                )->format('M d, Y')
                                            : '')
                                    : 'Pending maintenance planning',
                            ];

                            $trackingSteps[] = [
                                'title' => 'Maintenance Started',
                                'done' => $maintenanceStarted,
                                'detail' => $firstStartedAt?->format('M d, Y • h:i A'),
                                'subdetail' => $maintenanceStarted
                                    ? 'Technician(s): ' .
                                        ($workOrders
                                            ->first(fn($order) => $order->started_at)
                                            ?->technicians?->pluck('full_name')
                                            ->filter()
                                            ->implode(', ') ?:
                                            'Name not recorded')
                                    : 'Not yet started',
                            ];

                            $trackingSteps[] = [
                                'title' => 'Maintenance Accomplished',
                                'done' => $maintenanceAccomplished,
                                'detail' =>
                                    $firstCompletedAt?->format('M d, Y • h:i A') ??
                                    $complaint->completed_at?->format('M d, Y • h:i A'),
                                'subdetail' => $maintenanceAccomplished
                                    ? 'Technician(s): ' .
                                        ($workOrders
                                            ->first(fn($order) => $order->completed_at)
                                            ?->technicians?->pluck('full_name')
                                            ->filter()
                                            ->implode(', ') ?:
                                            $maintenanceReport?->technician?->full_name ?? 'Name not recorded')
                                    : 'Pending accomplishment report',
                            ];

                            $trackingSteps[] = [
                                'title' => 'Manager Review',
                                'done' => $managerReviewed,
                                'detail' => $maintenanceReport?->reviewed_at
                                    ? \Carbon\Carbon::parse($maintenanceReport->reviewed_at)->format('M d, Y • h:i A')
                                    : null,
                                'subdetail' => $managerReviewed
                                    ? 'Approved by ' .
                                        ($maintenanceReport && method_exists($maintenanceReport, 'reviewer')
                                            ? $maintenanceReport->reviewer?->full_name ??
                                                'Maintenance Manager (name not recorded)'
                                            : 'Maintenance Manager (name not recorded)')
                                    : ($maintenanceReport?->review_status === 'Returned'
                                        ? 'Returned by ' .
                                            ($maintenanceReport && method_exists($maintenanceReport, 'reviewer')
                                                ? $maintenanceReport->reviewer?->full_name ??
                                                    'Maintenance Manager (name not recorded)'
                                                : 'Maintenance Manager (name not recorded)')
                                        : ($reportSubmitted
                                            ? 'Pending manager review'
                                            : 'Pending')),
                            ];
                        }

                        $trackingSteps[] = [
                            'title' => $isCommercial ? 'Request Closed' : 'Complaint Closed',
                            'done' => $isClosed,
                            'detail' => $isClosed ? $complaint->updated_at?->format('M d, Y • h:i A') : null,
                            'subdetail' => $isClosed
                                ? 'Closed by Customer Service (name not recorded)'
                                : 'Pending finalization',
                        ];
                    @endphp

                    <div>
                        @foreach ($trackingSteps as $step)
                            <div class="flex gap-4">

                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $step['done'] ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">
                                        <i class="fas {{ $step['done'] ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                    </div>

                                    @unless ($loop->last)
                                        <div
                                            class="min-h-12 w-px flex-1 {{ $step['done'] ? 'bg-emerald-200' : 'bg-gray-200' }}">
                                        </div>
                                    @endunless
                                </div>

                                <div class="{{ $loop->last ? '' : 'pb-6' }}">
                                    <p
                                        class="text-sm font-semibold {{ $step['done'] ? 'text-gray-900' : 'text-gray-400' }}">
                                        {{ $step['title'] }}
                                    </p>

                                    @if ($step['detail'])
                                        <p class="mt-1 flex items-center gap-1.5 text-xs text-slate-500">
                                            <i class="far fa-clock"></i> {{ $step['detail'] }}
                                        </p>
                                    @endif

                                    @if ($step['subdetail'])
                                        <p class="mt-1 text-xs {{ $step['done'] ? 'text-gray-500' : 'text-gray-400' }}">
                                            {{ $step['subdetail'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

            {{-- Maintenance Result --}}
            @if ($reportSubmitted)
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                    <div
                        class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="font-bold text-gray-900">Maintenance Result</h2>
                            <p class="mt-1 text-xs text-gray-500">Technician-submitted accomplishment report</p>
                        </div>

                        @php
                            $reviewClass = match ($maintenanceReport->review_status) {
                                'Approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'Returned' => 'bg-amber-50 text-amber-700 border-amber-200',
                                default => 'bg-blue-50 text-blue-700 border-blue-200',
                            };
                        @endphp

                        <span
                            class="inline-flex w-fit rounded-full border px-2.5 py-1 text-xs font-semibold {{ $reviewClass }}">
                            {{ $maintenanceReport->review_status }}
                        </span>
                    </div>

                    <div class="space-y-6 p-5">

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Diagnosis / Findings
                                </p>
                                <div class="mt-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                    <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                        {{ $maintenanceReport->diagnosis ?: 'No diagnosis recorded.' }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Root Cause</p>
                                <div class="mt-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                    <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                        {{ $maintenanceReport->root_cause ?: 'No root cause recorded.' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Materials / Parts</p>
                            <div class="mt-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                    {{ $maintenanceReport->materials_parts ?: 'No materials or parts recorded.' }}
                                </p>
                            </div>
                        </div>

                        @if ($maintenanceReport->technician_notes)
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Technician Notes</p>
                                <div class="mt-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                    <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                        {{ $maintenanceReport->technician_notes }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if ($maintenanceReport->before_photo || $maintenanceReport->after_photo)
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                                <div class="text-center">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Before Photo</p>

                                    @if ($maintenanceReport->before_photo)
                                        <a href="{{ asset('storage/' . $maintenanceReport->before_photo) }}"
                                            target="_blank" rel="noopener noreferrer">
                                            <img src="{{ asset('storage/' . $maintenanceReport->before_photo) }}"
                                                alt="Before maintenance"
                                                class="mx-auto mt-2 h-64 w-full max-w-md rounded-xl border border-gray-200 bg-gray-50 object-contain">
                                        </a>
                                    @else
                                        <div
                                            class="mt-2 flex h-64 items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50">
                                            <p class="text-sm text-gray-400">No before photo</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-center">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">After Photo</p>

                                    @if ($maintenanceReport->after_photo)
                                        <a href="{{ asset('storage/' . $maintenanceReport->after_photo) }}"
                                            target="_blank" rel="noopener noreferrer">
                                            <img src="{{ asset('storage/' . $maintenanceReport->after_photo) }}"
                                                alt="After maintenance"
                                                class="mx-auto mt-2 h-64 w-full max-w-md rounded-xl border border-gray-200 bg-gray-50 object-contain">
                                        </a>
                                    @else
                                        <div
                                            class="mt-2 flex h-64 items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50">
                                            <p class="text-sm text-gray-400">No after photo</p>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        @endif

                        <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-5">

                            @if ($reportSubmitted)
                                <a href="{{ route('maintenance-manager.maintenance-reviews.show', $maintenanceReport) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-800">
                                    <i class="fas fa-clipboard-check"></i>
                                    View & Review Accomplishment
                                </a>
                            @endif

                            <a href="{{ route('maintenance-manager.complaints.report', $complaint) }}" target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                                <i class="fas fa-print"></i>
                                Print Complaint Report
                            </a>

                        </div>

                    </div>
                </div>
            @endif

        </div>

        {{-- Right sidebar --}}
        <div class="space-y-6">

            {{-- AI-Assisted Plumber Review --}}
            @if ($canAssign)
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                    <div class="border-b border-gray-100 px-5 py-4">
                        <h2 class="font-bold text-gray-900">AI-Assisted Plumber Review</h2>
                        <p class="mt-1 text-xs text-gray-500">
                            Review suggested plumbers and operational information.
                            Final team assignment and work date selection take place
                            in Daily Maintenance Planning.
                        </p>
                    </div>

                    <div id="assignmentForm" class="space-y-5 p-5">

                        <div class="rounded-xl border border-sky-200 bg-sky-50 p-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-circle-info mt-0.5 text-sky-700"></i>
                                <div>
                                    <h3 class="text-sm font-bold text-sky-900">AI Recommendations Are Advisory</h3>
                                    <p class="mt-1 text-xs leading-5 text-sky-800">
                                        Review the recommended plumbers below.
                                        Plumber availability, team assignment, and work dates
                                        are confirmed only in Daily Maintenance Planning.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- AI Recommendations --}}
                        <div class="border-t border-gray-100 pt-5">

                            <div class="mb-4 flex items-start gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-700">
                                    <i class="fas fa-wand-magic-sparkles"></i>
                                </div>

                                <div>
                                    <h3 class="text-sm font-bold text-gray-900">AI-Assisted Recommendations</h3>
                                    <p class="mt-1 text-xs leading-5 text-gray-500">
                                        Decision support based on workload, availability,
                                        and active assignment locations.
                                    </p>
                                </div>
                            </div>

                            @if (!empty($plumberRecommendationError))
                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                                    <p class="text-sm font-semibold text-amber-900">
                                        <i class="fas fa-triangle-exclamation mr-2"></i>
                                        Recommendation Unavailable
                                    </p>
                                    <p class="mt-2 text-xs leading-5 text-amber-700">
                                        {{ $plumberRecommendationError }}
                                    </p>
                                    <p class="mt-2 text-xs text-amber-600">
                                        You can continue planning the team manually.
                                    </p>
                                </div>
                            @elseif ($plumberRecommendations['has_recommendations'] ?? false)
                                @php
                                    $recommendations = $plumberRecommendations['recommendations'] ?? [];
                                    $recommendationCount = count($recommendations);
                                @endphp

                                <div class="space-y-3">

                                    @foreach ($recommendations as $index => $recommendation)
                                        {{-- Open the alternatives section before rendering recommendation #2 --}}
                                        @if ($index === 1)
                                            <details class="overflow-hidden rounded-xl border border-violet-200 bg-white">

                                                <summary
                                                    class="flex cursor-pointer list-none items-center justify-between gap-3 p-4 transition hover:bg-violet-50">
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-bold text-slate-900">
                                                            <i class="fas fa-users mr-2 text-violet-600"></i>
                                                            View all recommendations
                                                        </p>
                                                        <p class="mt-1 text-xs text-slate-500">
                                                            {{ $recommendationCount - 1 }}
                                                            alternative
                                                            {{ $recommendationCount - 1 === 1 ? 'plumber' : 'plumbers' }}
                                                        </p>
                                                    </div>

                                                    <i class="fas fa-chevron-down shrink-0 text-sm text-violet-600"></i>
                                                </summary>

                                                <div class="space-y-3 border-t border-violet-100 bg-slate-50 p-3">
                                        @endif

                                        @php
                                            $serviceArea =
                                                $recommendation['service_area']['name'] ?? 'No permanent area assigned';

                                            $recommendationLevel = $recommendation['recommendation'] ?? 'Alternative';

                                            $availability = $recommendation['availability'] ?? 'Unknown';

                                            $score = (float) ($recommendation['score_percentage'] ?? 0);

                                            $distance = $recommendation['nearest_active_assignment_km'] ?? null;

                                            $nearbyComplaint = $recommendation['nearest_active_complaint_no'] ?? null;

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
                                            class="overflow-hidden rounded-xl border bg-white {{ $isTopRecommendation ? 'border-violet-200' : 'border-gray-200' }}">

                                            @if ($isTopRecommendation)
                                                <div class="border-b border-violet-100 bg-violet-50 px-4 py-2">
                                                    <p
                                                        class="text-[10px] font-bold uppercase tracking-wide text-violet-700">
                                                        <i class="fas fa-star mr-1"></i>
                                                        Top Recommendation
                                                    </p>
                                                </div>
                                            @endif

                                            <div class="space-y-4 p-4">

                                                <div>
                                                    <p class="text-sm font-bold text-gray-900">
                                                        {{ $recommendation['name'] ?? 'Plumber' }}
                                                    </p>

                                                    <p class="mt-1 text-xs text-gray-500">
                                                        <i class="fas fa-location-dot mr-1"></i>
                                                        {{ $serviceArea }}
                                                    </p>

                                                    <div class="mt-2 flex flex-wrap items-center gap-2">
                                                        <span
                                                            class="inline-flex rounded-lg border px-2 py-1 text-[10px] font-semibold {{ $recommendationClass }}">
                                                            {{ $recommendationLevel }}
                                                        </span>

                                                        <span class="text-xs text-gray-500">
                                                            Score
                                                            <strong
                                                                class="text-gray-700">{{ number_format($score, 1) }}%</strong>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-2">

                                                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                                                        <p
                                                            class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                                            Availability
                                                        </p>
                                                        <p class="mt-1 text-xs font-bold {{ $availabilityClass }}">
                                                            {{ $availability }}
                                                        </p>
                                                    </div>

                                                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                                                        <p
                                                            class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                                            Active Workload
                                                        </p>
                                                        <p class="mt-1 text-xs font-bold text-gray-800">
                                                            {{ $recommendation['active_workload'] ?? 0 }}
                                                            {{ ((int) ($recommendation['active_workload'] ?? 0)) === 1 ? 'complaint' : 'complaints' }}
                                                        </p>
                                                    </div>

                                                </div>

                                                <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                                                    <p
                                                        class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                                        Nearest Active Assignment
                                                    </p>

                                                    @if ($distance !== null)
                                                        <p class="mt-1 text-xs font-bold text-gray-800">
                                                            @if ((float) $distance < 1)
                                                                {{ number_format((float) $distance * 1000, 0) }} meters
                                                                away
                                                            @else
                                                                {{ number_format((float) $distance, 2) }} km away
                                                            @endif
                                                        </p>

                                                        @if ($nearbyComplaint)
                                                            <p class="mt-1 text-[10px] text-gray-500">
                                                                Based on {{ $nearbyComplaint }}
                                                            </p>
                                                        @endif
                                                    @else
                                                        <p class="mt-1 text-xs font-semibold text-gray-500">
                                                            Not available
                                                        </p>
                                                    @endif
                                                </div>

                                                @if (!empty($recommendation['reasons']))
                                                    <div>
                                                        <p class="text-xs font-bold text-gray-700">Why recommended</p>

                                                        <div class="mt-2 space-y-2">
                                                            @foreach ($recommendation['reasons'] as $reason)
                                                                <div class="flex items-start gap-2">
                                                                    <i
                                                                        class="fas fa-circle-check mt-0.5 text-[11px] text-emerald-500"></i>
                                                                    <p class="text-[11px] leading-4 text-gray-600">
                                                                        {{ $reason }}</p>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (!empty($recommendation['considerations']))
                                                    <div class="rounded-lg border border-amber-100 bg-amber-50 p-3">
                                                        <p class="text-xs font-bold text-amber-800">Considerations</p>

                                                        <div class="mt-2 space-y-2">
                                                            @foreach ($recommendation['considerations'] as $consideration)
                                                                <div class="flex items-start gap-2">
                                                                    <i
                                                                        class="fas fa-circle-info mt-0.5 text-[11px] text-amber-500"></i>
                                                                    <p class="text-[11px] leading-4 text-amber-700">
                                                                        {{ $consideration }}</p>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <div
                                                    class="flex items-center gap-2 rounded-xl border border-violet-200 bg-violet-50 px-3 py-2.5 text-xs font-semibold text-violet-700">
                                                    <i class="fas fa-lightbulb"></i>
                                                    AI Recommendation — Review Only
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Close the alternatives container only when alternatives exist --}}
                                    @if ($recommendationCount > 1)
                                </div>
                                </details>
                            @endif

                        </div>

                        <div class="mt-3 rounded-xl border border-blue-100 bg-blue-50 p-3">
                            <p class="text-[10px] leading-4 text-blue-700">
                                Recommendations use available operational information.
                                The Maintenance Manager makes the final assignment decision.
                            </p>
                        </div>
                    @else
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-sm font-semibold text-gray-700">No AI recommendations available</p>
                            <p class="mt-1 text-xs text-gray-500">
                                Continue to Daily Maintenance Planning to select an on-duty team.
                            </p>
                        </div>
            @endif
        </div>

        {{-- Single planning action --}}
        <div class="border-t border-gray-100 pt-5">
            <p class="mb-3 text-xs leading-5 text-gray-500">
                Select the work date, check technician duties,
                and confirm the dedicated maintenance team.
            </p>

            <a href="{{ route('maintenance-manager.schedules.index') . '#ready' }}"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-3.5 text-sm font-semibold text-white transition hover:bg-sky-800">
                <i class="fas fa-calendar-plus"></i>
                Plan Maintenance
            </a>
        </div>

    </div>
    </div>
    @endif

    {{-- Work Order / Schedule --}}
    @if ($displayWorkOrder)
        @php
            $orderStatusClass = match ($displayWorkOrder->status) {
                'Scheduled' => 'bg-blue-50 text-blue-700 border-blue-200',
                'In Progress' => 'bg-amber-50 text-amber-700 border-amber-200',
                'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'Pending Workload' => 'bg-orange-50 text-orange-700 border-orange-200',
                default => 'bg-gray-50 text-gray-600 border-gray-200',
            };
        @endphp

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

            <div class="border-b border-gray-100 px-5 py-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-gray-900">Maintenance Work Order</h2>
                        <p class="mt-1 text-xs text-gray-500">Current or most recent scheduled visit</p>
                    </div>

                    <span class="rounded-full border px-2.5 py-1 text-[10px] font-semibold {{ $orderStatusClass }}">
                        {{ $displayWorkOrder->status }}
                    </span>
                </div>
            </div>

            <div class="space-y-4 p-5">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-gray-500">Work Order</p>
                        <p class="mt-1 text-sm font-bold text-gray-900">#{{ $displayWorkOrder->id }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Visit</p>
                        <p class="mt-1 text-sm font-bold text-gray-900">{{ $displayWorkOrder->visit_number }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Work Date</p>
                        <p class="mt-1 text-sm font-bold text-gray-900">
                            {{ $displayWorkOrder->schedule?->schedule_date
                                ? \Carbon\Carbon::parse($displayWorkOrder->schedule->schedule_date)->format('M d, Y')
                                : 'Not scheduled' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Schedule</p>
                        <p class="mt-1 text-sm font-bold text-gray-900">
                            {{ $displayWorkOrder->schedule?->status ?? 'Unavailable' }}
                        </p>
                    </div>
                </div>

                @if ($displayWorkOrder->started_at)
                    <div class="border-t border-gray-100 pt-3">
                        <p class="text-xs text-gray-500">Started</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ \Carbon\Carbon::parse($displayWorkOrder->started_at)->format('M d, Y h:i A') }}
                        </p>
                    </div>
                @endif

                @if ($displayWorkOrder->completed_at)
                    <div>
                        <p class="text-xs text-gray-500">Completed</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ \Carbon\Carbon::parse($displayWorkOrder->completed_at)->format('M d, Y h:i A') }}
                        </p>
                    </div>
                @endif

                {{-- Manager Early Scheduling Justification --}}
                @if ($displayWorkOrder->is_late_addition && filled($displayWorkOrder->late_addition_reason))
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clipboard-check text-amber-700"></i>
                            <h3 class="text-sm font-bold text-amber-900">Early Scheduling Justification</h3>
                        </div>

                        <p class="mt-2 whitespace-pre-line break-words text-xs leading-5 text-amber-900">
                            {{ $displayWorkOrder->late_addition_reason }}
                        </p>

                        <p class="mt-2 text-[11px] text-amber-700">Recorded by Maintenance Management</p>
                    </div>
                @endif

                <div class="border-t border-gray-100 pt-4">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Dedicated Maintenance Team
                    </p>

                    @forelse ($workOrderTeam as $technician)
                        <div class="mb-2 flex items-center gap-3 rounded-xl bg-gray-50 p-3">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">
                                {{ strtoupper(substr($technician->first_name ?? '', 0, 1) . substr($technician->last_name ?? '', 0, 1)) }}
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900">{{ $technician->full_name }}</p>
                                @if ($technician->employee_id)
                                    <p class="text-xs text-gray-500">{{ $technician->employee_id }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500">No dedicated work-order team recorded.</p>
                    @endforelse
                </div>

                {{-- Maintenance Visit History --}}
                <div id="maintenance-visit-history" class="border-t border-gray-100 pt-5">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                <i class="fas fa-clock-rotate-left mr-1 text-sky-700"></i>
                                Maintenance Visit History
                            </h3>
                            <p class="mt-1 text-xs text-slate-500">
                                Latest visit displayed first. Earlier visits remain available below.
                            </p>
                        </div>

                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                            {{ $workOrders->count() }} {{ $workOrders->count() === 1 ? 'Visit' : 'Visits' }}
                        </span>
                    </div>

                    @php
                        $historyOrders = $workOrders->sortByDesc('visit_number')->values();

                        $historyCount = $historyOrders->count();
                    @endphp

                    <div class="space-y-4 border-l-2 border-slate-200 pl-4">

                        @foreach ($historyOrders as $historyIndex => $order)
                            {{-- Start the collapsed history before the second visit --}}
                            @if ($historyIndex === 1)
                                <details class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                    <summary
                                        class="flex cursor-pointer list-none items-center justify-between gap-3 p-4 transition hover:bg-slate-50">
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">
                                                <i class="fas fa-clock-rotate-left mr-2 text-sky-700"></i>
                                                View all visit history
                                            </p>
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $historyCount - 1 }}
                                                earlier {{ $historyCount - 1 === 1 ? 'visit' : 'visits' }}
                                            </p>
                                        </div>

                                        <i class="fas fa-chevron-down shrink-0 text-sky-700"></i>
                                    </summary>

                                    <div class="space-y-4 border-t border-slate-200 bg-slate-50 p-3">
                            @endif

                            @php
                                $visitStatusStyle = match ($order->status) {
                                    'Scheduled' => 'border-blue-200 bg-blue-50 text-blue-800',
                                    'In Progress' => 'border-amber-200 bg-amber-50 text-amber-800',
                                    'Completed' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                                    'Pending Workload' => 'border-orange-200 bg-orange-50 text-orange-800',
                                    'Rescheduled' => 'border-violet-200 bg-violet-50 text-violet-800',
                                    'Cancelled' => 'border-slate-200 bg-slate-100 text-slate-700',
                                    default => 'border-slate-200 bg-slate-50 text-slate-700',
                                };

                                $orderDate = $order->schedule?->schedule_date;

                                $isMissedOriginal =
                                    $order->status === 'Rescheduled' &&
                                    $order->started_at === null &&
                                    $order->completed_at === null;

                                $hasMissedDetails =
                                    filled($order->missed_reason) ||
                                    filled($order->missed_manager_reason) ||
                                    filled($order->missed_confirmed_at);
                            @endphp

                            <article class="relative rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                                <span
                                    class="absolute -left-[25px] top-5 h-3.5 w-3.5 rounded-full border-2 border-white bg-sky-600"></span>

                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">
                                            Visit #{{ $order->visit_number }} · Work Order #{{ $order->id }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $orderDate ? \Carbon\Carbon::parse($orderDate)->format('M d, Y (l)') : 'Work date not recorded' }}
                                        </p>
                                    </div>

                                    <span
                                        class="rounded-full border px-2.5 py-1 text-[11px] font-bold {{ $visitStatusStyle }}">
                                        {{ $order->status }}
                                    </span>
                                </div>

                                <dl class="mt-3 grid grid-cols-1 gap-3 text-xs sm:grid-cols-2">
                                    <div>
                                        <dt class="font-semibold text-slate-500">Schedule approval</dt>
                                        <dd class="mt-1 text-slate-800">
                                            {{ $order->approval_status ?? 'Not recorded' }} ·
                                            {{ $order->schedule?->status ?? 'No schedule' }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="font-semibold text-slate-500">Assigned plumber(s)</dt>
                                        <dd class="mt-1 break-words text-slate-800">
                                            {{ $order->technicians->pluck('full_name')->filter()->implode(', ') ?: 'No team recorded' }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="font-semibold text-slate-500">Actual start</dt>
                                        <dd class="mt-1 text-slate-800">
                                            {{ $order->started_at ? \Carbon\Carbon::parse($order->started_at)->format('M d, Y h:i A') : 'Not recorded' }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="font-semibold text-slate-500">Actual completion</dt>
                                        <dd class="mt-1 text-slate-800">
                                            {{ $order->completed_at ? \Carbon\Carbon::parse($order->completed_at)->format('M d, Y h:i A') : 'Not recorded' }}
                                        </dd>
                                    </div>
                                </dl>

                                @if ($order->rescheduled_from_work_order_id)
                                    <p class="mt-3 rounded-lg bg-violet-50 px-3 py-2 text-xs text-violet-800">
                                        <i class="fas fa-link mr-1"></i>
                                        Replacement visit for Work Order #{{ $order->rescheduled_from_work_order_id }}.
                                    </p>
                                @endif


                                {{-- Plumber Replacement History --}}
                                @php
                                    $replacementHistories = $plumberReplacementHistories->get($order->id, collect());

                                    $replacementHistoryId = 'replacement-history-' . $order->id;
                                @endphp

                                @if ($replacementHistories->isNotEmpty())
                                    <div class="mt-3 overflow-hidden rounded-lg border border-sky-200 bg-sky-50/70">

                                        {{-- Clickable header --}}
                                        <button type="button"
                                            onclick="const panel = document.getElementById(@js($replacementHistoryId)); const icon = this.querySelector('[data-history-icon]'); const isExpanded = this.getAttribute('aria-expanded') === 'true'; this.setAttribute('aria-expanded', String(!isExpanded)); panel.hidden = isExpanded; icon.classList.toggle('rotate-180', !isExpanded);"
                                            aria-expanded="false" aria-controls="{{ $replacementHistoryId }}"
                                            class="flex w-full items-center justify-between gap-3 p-3 text-left transition hover:bg-sky-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-sky-500">
                                            <span class="flex min-w-0 items-center gap-2">
                                                <i class="fas fa-user-gear text-sky-700"></i>

                                                <span class="text-xs font-bold text-sky-900">
                                                    Plumber Replacement History
                                                    <span class="ml-1 font-medium text-sky-700">
                                                        ({{ $replacementHistories->count() }})
                                                    </span>
                                                </span>
                                            </span>

                                            <i data-history-icon
                                                class="fas fa-chevron-down shrink-0 text-xs text-sky-700 transition-transform duration-200"></i>
                                        </button>

                                        {{-- Collapsible content --}}
                                        <div id="{{ $replacementHistoryId }}" hidden>
                                            <div class="space-y-3 border-t border-sky-200 p-3">

                                                @foreach ($replacementHistories as $replacementHistory)
                                                    <div
                                                        @if (!$loop->first) class="border-t border-sky-200 pt-3" @endif>

                                                        <p class="text-[11px] font-semibold text-sky-800">
                                                            Reason for Replacement
                                                        </p>

                                                        <p
                                                            class="mt-1 whitespace-pre-line break-words text-xs leading-5 text-slate-800">
                                                            {{ $replacementHistory->reason ?: 'No replacement reason recorded.' }}
                                                        </p>

                                                        <div class="mt-2 space-y-1 text-[11px] text-slate-600">
                                                            <p>
                                                                <span class="font-semibold">Recorded by:</span>
                                                                {{ $replacementHistory->performer?->full_name ?? ($replacementHistory->performer?->name ?? 'Maintenance Manager (name unavailable)') }}
                                                            </p>

                                                            @if ($replacementHistory->created_at)
                                                                <p>
                                                                    <span class="font-semibold">Recorded on:</span>
                                                                    {{ \Carbon\Carbon::parse($replacementHistory->created_at)->timezone('Asia/Manila')->format('M d, Y h:i A') }}
                                                                    PHT
                                                                </p>
                                                            @endif
                                                        </div>

                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>
                                @endif


                                @if ($order->status === 'Pending Workload')
                                    <div class="mt-3 rounded-lg border border-orange-200 bg-orange-50 p-3">
                                        <p class="text-xs font-bold text-orange-900">Unfinished maintenance reason</p>
                                        <p class="mt-1 whitespace-pre-line break-words text-xs leading-5 text-orange-950">
                                            {{ $order->pending_reason ?: 'No reason recorded.' }}
                                        </p>

                                        @if ($order->pending_recorded_at)
                                            <p class="mt-2 text-[11px] text-orange-800">
                                                Recorded
                                                {{ \Carbon\Carbon::parse($order->pending_recorded_at)->format('M d, Y h:i A') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                @if ($isMissedOriginal || $hasMissedDetails)
                                    <div class="mt-3 space-y-2 rounded-lg border border-violet-200 bg-violet-50 p-3">
                                        <p class="text-xs font-bold text-violet-900">Missed schedule / rescheduling record
                                        </p>

                                        <div>
                                            <p class="text-[11px] font-semibold text-violet-800">Plumber explanation</p>
                                            <p class="mt-1 whitespace-pre-line break-words text-xs text-slate-800">
                                                {{ $order->missed_reason ?: 'No plumber explanation recorded.' }}
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-[11px] font-semibold text-violet-800">Manager verified reason
                                            </p>
                                            <p class="mt-1 whitespace-pre-line break-words text-xs text-slate-800">
                                                {{ $order->missed_manager_reason ?: 'Not recorded.' }}
                                            </p>
                                        </div>

                                        @if ($order->missed_confirmed_at)
                                            <p class="text-[11px] text-violet-800">
                                                Confirmed
                                                {{ \Carbon\Carbon::parse($order->missed_confirmed_at)->format('M d, Y h:i A') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                @if ($order->is_late_addition && filled($order->late_addition_reason))
                                    <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
                                        <p class="text-xs font-bold text-amber-900">Early scheduling justification</p>
                                        <p class="mt-1 whitespace-pre-line break-words text-xs leading-5 text-amber-950">
                                            {{ $order->late_addition_reason }}
                                        </p>
                                    </div>
                                @endif

                                @if (filled($order->manager_remarks))
                                    <div class="mt-3 rounded-lg bg-slate-50 p-3">
                                        <p class="text-xs font-bold text-slate-700">Manager remarks</p>
                                        <p class="mt-1 whitespace-pre-line break-words text-xs leading-5 text-slate-700">
                                            {{ $order->manager_remarks }}
                                        </p>
                                    </div>
                                @endif

                            </article>
                        @endforeach

                        @if ($historyCount > 1)
                    </div>
                    </details>
    @endif

    </div>

    <p class="mt-4 text-[11px] leading-5 text-slate-500">
        Scheduled dates and work-order statuses are recorded system data. A missed physical visit
        is confirmed only through the manager's verification process; an absent start time alone
        does not prove that no work occurred.
    </p>
    </div>

    </div>
    </div>
    @endif

    {{-- Historical complaint-level associations --}}
    @if ($historicalTechnicians->isNotEmpty())
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="font-bold text-gray-900">Technicians Associated with Complaint</h2>
                <p class="mt-1 text-xs text-gray-500">
                    Historical complaint-level associations;
                    work-order teams are displayed separately.
                </p>
            </div>

            <div class="space-y-3 p-5">

                @foreach ($historicalTechnicians as $technician)
                    <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 p-3">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-xs font-bold text-blue-700">
                            {{ strtoupper(substr($technician->first_name ?? '', 0, 1) . substr($technician->last_name ?? '', 0, 1)) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900">{{ $technician->full_name }}</p>

                            @if ($technician->employee_id)
                                <p class="text-[11px] text-gray-500">ID: {{ $technician->employee_id }}</p>
                            @endif
                        </div>

                        <span class="shrink-0 text-[10px] font-semibold text-gray-500">
                            {{ $technician->pivot?->status ?? 'Associated' }}
                        </span>
                    </div>
                @endforeach

            </div>
        </div>
    @endif

    @if (!$canAssign && !$displayWorkOrder && $complaint->status === 'For Maintenance')
        <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5">
            <p class="text-sm font-semibold text-violet-900">Ready for Maintenance Planning</p>
            <p class="mt-1 text-xs leading-5 text-violet-700">
                This request is waiting for a maintenance work date and dedicated team.
            </p>
        </div>
    @endif

    </div>
    </div>

    </div>

    {{-- Leaflet Map --}}
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

                    const latitude = Number(@json((float) $displayLatitude));
                    const longitude = Number(@json((float) $displayLongitude));

                    if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                        return;
                    }

                    const map = L.map('complaintMap', {
                        scrollWheelZoom: false,
                        zoomControl: true
                    }).setView([latitude, longitude], 16);

                    L.tileLayer(
                        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap contributors'
                        }
                    ).addTo(map);

                    const marker = L.marker([latitude, longitude]).addTo(map);

                    marker.bindPopup(
                        '<strong>' +
                        @json($complaint->complaint_no) +
                        '</strong><br>' +
                        '<span style="font-size:12px;color:#64748b;">Service location</span>'
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

    <script>
        function confirmFollowUpDraft(event) {
            const form = event.currentTarget;
            const dateField = form.querySelector('[name="schedule_date"]');
            const selected = [...form.querySelectorAll('[name="technician_ids[]"]:checked')];
            const error = form.querySelector('#followup-form-error');

            const showError = (message) => {
                error.textContent = message;
                error.classList.remove('hidden');
                return false;
            };

            error.classList.add('hidden');

            if (!dateField.value) {
                return showError('Select a follow-up date.');
            }

            const parts = dateField.value.split('-').map(Number);
            const selectedDate = new Date(Date.UTC(parts[0], parts[1] - 1, parts[2]));

            if (selectedDate.getUTCDay() === 0 || selectedDate.getUTCDay() === 6) {
                return showError('Select a working day (Monday to Friday).');
            }

            if (selected.length === 0) {
                return showError('Select at least one plumber.');
            }

            const names = selected
                .map(input => input.closest('label')?.textContent.trim())
                .filter(Boolean);

            const message = 'Create a Draft follow-up work order?\n\n' +
                'Date: ' + dateField.value + '\n' +
                'Assigned plumber(s): ' + names.join(', ') + '\n\n' +
                'The previous Pending Workload visit will remain in history. ' +
                'The new follow-up must still be approved in Daily Maintenance Planning.';

            if (!window.confirm(message)) {
                return false;
            }

            const button = form.querySelector('button[type="submit"]');

            if (button) {
                button.disabled = true;
                button.textContent = 'Creating Draft...';
            }

            return true;
        }
    </script>

@endsection
