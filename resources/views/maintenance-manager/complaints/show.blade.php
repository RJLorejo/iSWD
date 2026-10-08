@extends('maintenance-manager.layouts.app')

@section('title', 'Complaint Details')

@section('content')

    @php
        $divisionName = strtolower(trim((string) $complaint->division?->name));

        $isEngineering = $isEngineering ?? str_contains($divisionName, 'engineering');
        $isCommercial = str_contains($divisionName, 'commercial');

        // Use the controller value. Do not restore the old Engineering-only rule here.
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
            $maintenanceReport &&
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

        $urgencyLevel = $complaint->aiAnalysis?->urgency_level ?? 'Not Assessed';

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

        $firstAssignedAt = $complaint->technicians
            ->map(fn($technician) => $technician->pivot?->assigned_at)
            ->filter()
            ->map(fn($date) => \Carbon\Carbon::parse($date))
            ->sort()
            ->first();

        $firstStartedAt = $complaint->technicians
            ->map(fn($technician) => $technician->pivot?->started_at)
            ->filter()
            ->map(fn($date) => \Carbon\Carbon::parse($date))
            ->sort()
            ->first();

        $firstCompletedAt = $complaint->technicians
            ->map(fn($technician) => $technician->pivot?->completed_at)
            ->filter()
            ->map(fn($date) => \Carbon\Carbon::parse($date))
            ->sort()
            ->first();

        $assignedNames = $complaint->technicians->pluck('full_name')->filter()->implode(', ');

        $maintenanceStarted =
            $firstStartedAt || in_array($complaint->status, ['In Progress', 'Completed', 'Closed'], true);

        $maintenanceAccomplished = $reportSubmitted || in_array($complaint->status, ['Completed', 'Closed'], true);

        $managerReviewed = $maintenanceReport && $maintenanceReport->review_status === 'Approved';

        $isClosed = $complaint->status === 'Closed';
    @endphp


    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

            <div>

                <div class="flex flex-wrap items-center gap-2 text-sm">

                    <a href="{{ route('maintenance-manager.complaints.index') }}"
                        class="inline-flex items-center gap-1.5 text-gray-500 transition hover:text-blue-600">

                        <i class="fas fa-arrow-left text-xs"></i>
                        Complaints

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
                class="inline-flex w-fit items-center gap-2 rounded-full border
                px-3 py-2 text-sm font-semibold {{ $statusClasses }}">

                <span class="h-2 w-2 rounded-full bg-current"></span>

                {{ $displayStatus }}

            </span>

        </div>


        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800">
                <div class="flex items-start gap-3">
                    <i class="fas fa-circle-check mt-0.5"></i>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
                <div class="flex items-start gap-3">
                    <i class="fas fa-circle-exclamation mt-0.5"></i>
                    <p>{{ session('error') }}</p>
                </div>
            </div>
        @endif


        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">
                    Status
                </p>

                <p class="mt-1 text-sm font-bold text-gray-900">
                    {{ $displayStatus }}
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">
                    AI Urgency
                </p>

                <div class="mt-1">
                    <span
                        class="inline-flex rounded-full border px-2.5 py-1
                        text-xs font-bold {{ $urgencyClass }}">
                        {{ $urgencyLevel }}
                    </span>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">
                    Division
                </p>

                <p class="mt-1 text-sm font-bold text-gray-900">
                    {{ $complaint->division?->name ?? 'Not specified' }}
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">
                    Submitted
                </p>

                <p class="mt-1 text-sm font-bold text-gray-900">
                    {{ $complaint->created_at?->format('M d, Y') }}
                </p>

                <p class="mt-0.5 text-xs text-gray-400">
                    {{ $complaint->created_at?->format('h:i A') }}
                </p>
            </div>

        </div>


        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

            <div class="space-y-6 xl:col-span-2">

                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                    <div class="border-b border-gray-100 px-5 py-4">
                        <h2 class="font-bold text-gray-900">
                            Complaint Information
                        </h2>

                        <p class="mt-1 text-xs text-gray-500">
                            Consumer concern and service details
                        </p>
                    </div>

                    <div class="space-y-6 p-5">

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Consumer
                                </p>

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
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Contact
                                </p>

                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $complaint->complainant_phone ?? ($complaint->consumer?->phone ?? 'Not provided') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Complaint Type
                                </p>

                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $complaint->category?->name ?? 'Uncategorized' }}
                                </p>
                            </div>

                        </div>


                        <div>

                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Concern
                            </p>

                            <div class="mt-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                    {{ $complaint->description }}
                                </p>
                            </div>

                        </div>


                        <div>

                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                {{ $locationLabel }}
                            </p>

                            <div class="mt-2 grid grid-cols-1 gap-4 lg:grid-cols-5">

                                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 lg:col-span-2">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center
                                            rounded-xl bg-blue-100 text-blue-700">

                                            <i class="fas fa-location-dot"></i>

                                        </div>

                                        <div class="min-w-0">

                                            <p class="text-sm font-semibold leading-6 text-gray-800">
                                                {{ $displayAddress }}
                                            </p>

                                            @if ($isEngineering && $complaint->landmark)
                                                <p class="mt-1 text-xs text-gray-500">
                                                    Landmark: {{ $complaint->landmark }}
                                                </p>
                                            @endif

                                            @if ($isCommercial && $complaint->consumer)
                                                <p class="mt-2 text-xs leading-5 text-blue-600">
                                                    Registered service location of the linked SWD consumer account.
                                                </p>
                                            @endif

                                        </div>

                                    </div>

                                    @if (!$hasDisplayCoordinates)
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
                                            class="h-[280px] w-full overflow-hidden rounded-xl
                                            border border-gray-200 bg-gray-100 sm:h-[320px]">
                                        </div>
                                    @else
                                        <div
                                            class="flex h-[260px] items-center justify-center rounded-xl
                                            border border-dashed border-gray-300 bg-gray-50 p-6 text-center">

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


                        {{-- Supporting Photos --}}
                        @php
                            $supportingPhotos = $complaint->photos ?? collect();

                            /*
    |--------------------------------------------------------------------------
    | Temporary Legacy Photo Fallback
    |--------------------------------------------------------------------------
    | Keep the old complaints.photo available only when this complaint has
    | not yet been migrated to complaint_photos.
    */
                            $legacyPhoto = $supportingPhotos->isEmpty() ? $complaint->photo : null;

                            $supportingPhotoCount = $supportingPhotos->count() + ($legacyPhoto ? 1 : 0);
                        @endphp

                        @if ($supportingPhotoCount > 0)

                            <div class="border-t border-gray-100 pt-5">

                                <div
                                    class="mb-4 flex flex-col gap-2
                   sm:flex-row sm:items-center sm:justify-between">

                                    <div>

                                        <p
                                            class="text-xs font-semibold uppercase
                           tracking-wide text-gray-400">
                                            Supporting Photos
                                        </p>

                                        <p class="mt-1 text-xs text-gray-500">
                                            Photos submitted with this complaint.
                                        </p>

                                    </div>

                                    <span
                                        class="inline-flex w-fit items-center gap-1.5
                       rounded-full border border-gray-200
                       bg-gray-50 px-2.5 py-1
                       text-xs font-semibold text-gray-600">

                                        <i class="fas fa-images"></i>

                                        {{ $supportingPhotoCount }}
                                        {{ $supportingPhotoCount === 1 ? 'Photo' : 'Photos' }}

                                    </span>

                                </div>


                                <div
                                    class="grid grid-cols-1 gap-4
                   sm:grid-cols-2
                   lg:grid-cols-3">

                                    @foreach ($supportingPhotos as $photo)
                                        <a href="{{ asset('storage/' . $photo->photo) }}" target="_blank"
                                            rel="noopener noreferrer"
                                            class="group relative block overflow-hidden
                           rounded-xl border border-gray-200
                           bg-gray-50">

                                            <img src="{{ asset('storage/' . $photo->photo) }}"
                                                alt="Supporting photo {{ $loop->iteration }}" loading="lazy"
                                                class="h-56 w-full object-cover
                               transition duration-300
                               group-hover:scale-[1.02]">

                                            <div
                                                class="absolute inset-x-0 bottom-0
                               flex items-center justify-between
                               bg-gradient-to-t from-black/70
                               to-transparent px-3 pb-3 pt-8">

                                                <span class="text-xs font-semibold text-white">
                                                    Photo {{ $loop->iteration }}
                                                </span>

                                                <span
                                                    class="flex h-8 w-8 items-center
                                   justify-center rounded-lg
                                   bg-white/20 text-white
                                   backdrop-blur-sm">

                                                    <i class="fas fa-up-right-from-square text-xs"></i>

                                                </span>

                                            </div>

                                        </a>
                                    @endforeach


                                    @if ($legacyPhoto)
                                        <a href="{{ asset('storage/' . $legacyPhoto) }}" target="_blank"
                                            rel="noopener noreferrer"
                                            class="group relative block overflow-hidden
                           rounded-xl border border-gray-200
                           bg-gray-50">

                                            <img src="{{ asset('storage/' . $legacyPhoto) }}" alt="Supporting photo"
                                                loading="lazy"
                                                class="h-56 w-full object-cover
                               transition duration-300
                               group-hover:scale-[1.02]">

                                            <div
                                                class="absolute inset-x-0 bottom-0
                               flex items-center justify-between
                               bg-gradient-to-t from-black/70
                               to-transparent px-3 pb-3 pt-8">

                                                <span class="text-xs font-semibold text-white">
                                                    Photo 1
                                                </span>

                                                <span
                                                    class="flex h-8 w-8 items-center
                                   justify-center rounded-lg
                                   bg-white/20 text-white
                                   backdrop-blur-sm">

                                                    <i class="fas fa-up-right-from-square text-xs"></i>

                                                </span>

                                            </div>

                                        </a>
                                    @endif

                                </div>

                            </div>

                        @endif

                    </div>

                </div>


                @if ($hasInitialProcessing)

                    <div class="overflow-hidden rounded-2xl border border-sky-200 bg-white shadow-sm">

                        <div class="border-b border-sky-100 bg-sky-50/60 px-5 py-4">

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center
                                    rounded-xl bg-sky-100 text-sky-700">

                                    <i class="fas fa-clipboard-check"></i>

                                </div>

                                <div>
                                    <h2 class="font-bold text-gray-900">
                                        Customer Service Processing Result
                                    </h2>

                                    <p class="mt-1 text-xs text-gray-500">
                                        Initial findings and recommendation recorded before maintenance handoff.
                                    </p>
                                </div>

                            </div>

                        </div>


                        <div class="space-y-6 p-5">

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">

                                <div>
                                    <p class="text-xs text-gray-500">
                                        Processing Started
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ $commercialResolution->started_at?->format('M d, Y h:i A') ?? '—' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">
                                        Processed By
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ $commercialResolution->processor?->full_name ?? 'Customer Service' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">
                                        Initial Processing Completed
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-gray-900">
                                        {{ $commercialResolution->initial_processing_completed_at?->format('M d, Y h:i A') ?? '—' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">
                                        Forwarded to Maintenance
                                    </p>

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

                                <div class="mb-2 flex items-center gap-2">
                                    <i class="fas fa-magnifying-glass text-sky-600"></i>

                                    <h3 class="text-sm font-bold text-gray-900">
                                        Findings
                                    </h3>
                                </div>

                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                        {{ $commercialResolution->findings ?: 'No findings recorded.' }}
                                    </p>
                                </div>

                            </div>


                            <div>

                                <div class="mb-2 flex items-center gap-2">
                                    <i class="fas fa-route text-emerald-600"></i>

                                    <h3 class="text-sm font-bold text-gray-900">
                                        Resolution / Recommendation
                                    </h3>
                                </div>

                                <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                                    <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                        {{ $commercialResolution->resolution_remarks ?: 'No resolution or recommendation recorded.' }}
                                    </p>
                                </div>

                            </div>


                            @if ($wasForwardedToMaintenance)
                                <div class="rounded-xl border border-violet-100 bg-violet-50 p-4">

                                    <div class="flex items-start gap-3">

                                        <i class="fas fa-circle-info mt-0.5 text-violet-600"></i>

                                        <div>
                                            <p class="text-sm font-semibold text-violet-900">
                                                Maintenance Handoff
                                            </p>

                                            <p class="mt-1 text-xs leading-5 text-violet-700">
                                                Customer Service determined that this request requires physical
                                                maintenance work and forwarded it for plumber assignment.
                                            </p>
                                        </div>

                                    </div>

                                </div>
                            @endif

                        </div>

                    </div>

                @endif


                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                    <div class="border-b border-gray-100 px-5 py-4">
                        <h2 class="font-bold text-gray-900">
                            Complaint Tracking
                        </h2>

                        <p class="mt-1 text-xs text-gray-500">
                            Processing and maintenance history
                        </p>
                    </div>


                    <div class="p-5">

                        <div>

                            <div class="flex gap-4">

                                <div class="flex flex-col items-center">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center
                                        rounded-full bg-emerald-100 text-emerald-700">

                                        <i class="fas fa-check text-xs"></i>
                                    </div>

                                    <div class="min-h-12 w-px flex-1 bg-emerald-200"></div>
                                </div>

                                <div class="pb-6">

                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $isCommercial ? 'Request Submitted' : 'Complaint Submitted' }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $complaint->created_at?->format('M d, Y • h:i A') }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        Submitted by
                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Consumer') }}
                                    </p>

                                </div>

                            </div>


                            <div class="flex gap-4">

                                <div class="flex flex-col items-center">

                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                        {{ $complaint->verified_at ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">

                                        <i
                                            class="fas {{ $complaint->verified_at ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                    </div>

                                    <div
                                        class="min-h-12 w-px flex-1
                                        {{ $complaint->verified_at ? 'bg-emerald-200' : 'bg-gray-200' }}">
                                    </div>

                                </div>

                                <div class="pb-6">

                                    <p
                                        class="text-sm font-semibold
                                        {{ $complaint->verified_at ? 'text-gray-900' : 'text-gray-400' }}">
                                        Verified
                                    </p>

                                    @if ($complaint->verified_at)
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $complaint->verified_at->format('M d, Y • h:i A') }}
                                        </p>

                                        <p class="mt-1 text-xs text-gray-500">
                                            Verified by {{ $complaint->verifier?->full_name ?? 'Customer Service' }}
                                        </p>
                                    @else
                                        <p class="mt-1 text-xs text-gray-400">
                                            Pending
                                        </p>
                                    @endif

                                </div>

                            </div>


                            @if ($hasInitialProcessing)

                                <div class="flex gap-4">

                                    <div class="flex flex-col items-center">

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                            {{ $commercialResolution->started_at ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">

                                            <i
                                                class="fas {{ $commercialResolution->started_at ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                        </div>

                                        <div
                                            class="min-h-12 w-px flex-1
                                            {{ $commercialResolution->started_at ? 'bg-emerald-200' : 'bg-gray-200' }}">
                                        </div>

                                    </div>

                                    <div class="pb-6">

                                        <p
                                            class="text-sm font-semibold
                                            {{ $commercialResolution->started_at ? 'text-gray-900' : 'text-gray-400' }}">
                                            Initial Processing Started
                                        </p>

                                        @if ($commercialResolution->started_at)
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $commercialResolution->started_at->format('M d, Y • h:i A') }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-500">
                                                Started by
                                                {{ $commercialResolution->processor?->full_name ?? 'Customer Service' }}
                                            </p>
                                        @endif

                                    </div>

                                </div>


                                <div class="flex gap-4">

                                    <div class="flex flex-col items-center">

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                            {{ $commercialResolution->initial_processing_completed_at
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-gray-100 text-gray-400' }}">

                                            <i
                                                class="fas {{ $commercialResolution->initial_processing_completed_at ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                        </div>

                                        <div
                                            class="min-h-12 w-px flex-1
                                            {{ $commercialResolution->initial_processing_completed_at ? 'bg-emerald-200' : 'bg-gray-200' }}">
                                        </div>

                                    </div>

                                    <div class="pb-6">

                                        <p
                                            class="text-sm font-semibold
                                            {{ $commercialResolution->initial_processing_completed_at ? 'text-gray-900' : 'text-gray-400' }}">
                                            Initial Processing Completed
                                        </p>

                                        @if ($commercialResolution->initial_processing_completed_at)
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $commercialResolution->initial_processing_completed_at->format('M d, Y • h:i A') }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-500">
                                                Processed by
                                                {{ $commercialResolution->processor?->full_name ?? 'Customer Service' }}
                                            </p>
                                        @else
                                            <p class="mt-1 text-xs text-gray-400">
                                                Pending
                                            </p>
                                        @endif

                                    </div>

                                </div>


                                @if ($wasForwardedToMaintenance || $complaint->status !== 'Closed')

                                    <div class="flex gap-4">

                                        <div class="flex flex-col items-center">

                                            <div
                                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                                {{ $wasForwardedToMaintenance ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">

                                                <i
                                                    class="fas {{ $wasForwardedToMaintenance ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                            </div>

                                            <div
                                                class="min-h-12 w-px flex-1
                                                {{ $wasForwardedToMaintenance ? 'bg-emerald-200' : 'bg-gray-200' }}">
                                            </div>

                                        </div>

                                        <div class="pb-6">

                                            <p
                                                class="text-sm font-semibold
                                                {{ $wasForwardedToMaintenance ? 'text-gray-900' : 'text-gray-400' }}">
                                                Forwarded to Maintenance
                                            </p>

                                            @if ($wasForwardedToMaintenance)
                                                <p class="mt-1 text-xs text-gray-500">
                                                    {{ $commercialResolution->forwarded_to_maintenance_at->format('M d, Y • h:i A') }}
                                                </p>

                                                <p class="mt-1 text-xs text-gray-500">
                                                    Forwarded by
                                                    {{ $commercialResolution->forwarder?->full_name ?? 'Customer Service' }}
                                                </p>
                                            @else
                                                <p class="mt-1 text-xs text-gray-400">
                                                    Pending Customer Service decision
                                                </p>
                                            @endif

                                        </div>

                                    </div>

                                @endif
                            @elseif ($isEngineering)
                                <div class="flex gap-4">

                                    <div class="flex flex-col items-center">

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                            {{ $complaint->verified_at ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">

                                            <i
                                                class="fas {{ $complaint->verified_at ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                        </div>

                                        <div
                                            class="min-h-12 w-px flex-1
                                            {{ $complaint->verified_at ? 'bg-emerald-200' : 'bg-gray-200' }}">
                                        </div>

                                    </div>

                                    <div class="pb-6">

                                        <p
                                            class="text-sm font-semibold
                                            {{ $complaint->verified_at ? 'text-gray-900' : 'text-gray-400' }}">
                                            Forwarded to Maintenance
                                        </p>

                                        @if ($complaint->verified_at)
                                            <p class="mt-1 text-xs text-gray-500">
                                                Ready for Maintenance Management after verification
                                            </p>
                                        @else
                                            <p class="mt-1 text-xs text-gray-400">
                                                Pending verification
                                            </p>
                                        @endif

                                    </div>

                                </div>

                            @endif


                            @if (!$hasInitialProcessing || $wasForwardedToMaintenance)

                                <div class="flex gap-4">

                                    <div class="flex flex-col items-center">

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                            {{ $complaint->technicians->isNotEmpty() ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">

                                            <i
                                                class="fas {{ $complaint->technicians->isNotEmpty() ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                        </div>

                                        <div
                                            class="min-h-12 w-px flex-1
                                            {{ $complaint->technicians->isNotEmpty() ? 'bg-emerald-200' : 'bg-gray-200' }}">
                                        </div>

                                    </div>

                                    <div class="pb-6">

                                        <p
                                            class="text-sm font-semibold
                                            {{ $complaint->technicians->isNotEmpty() ? 'text-gray-900' : 'text-gray-400' }}">
                                            Plumber Assigned
                                        </p>

                                        @if ($complaint->technicians->isNotEmpty())
                                            @if ($firstAssignedAt)
                                                <p class="mt-1 text-xs text-gray-500">
                                                    {{ $firstAssignedAt->format('M d, Y • h:i A') }}
                                                </p>
                                            @endif

                                            <p class="mt-1 text-xs text-gray-500">
                                                Assigned to {{ $assignedNames }}
                                            </p>
                                        @else
                                            <p class="mt-1 text-xs text-gray-400">
                                                Pending assignment
                                            </p>
                                        @endif

                                    </div>

                                </div>


                                <div class="flex gap-4">

                                    <div class="flex flex-col items-center">

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                            {{ $maintenanceStarted ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">

                                            <i
                                                class="fas {{ $maintenanceStarted ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                        </div>

                                        <div
                                            class="min-h-12 w-px flex-1
                                            {{ $maintenanceStarted ? 'bg-emerald-200' : 'bg-gray-200' }}">
                                        </div>

                                    </div>

                                    <div class="pb-6">

                                        <p
                                            class="text-sm font-semibold
                                            {{ $maintenanceStarted ? 'text-gray-900' : 'text-gray-400' }}">
                                            Maintenance Started
                                        </p>

                                        @if ($maintenanceStarted)
                                            @if ($firstStartedAt)
                                                <p class="mt-1 text-xs text-gray-500">
                                                    {{ $firstStartedAt->format('M d, Y • h:i A') }}
                                                </p>
                                            @endif

                                            <p class="mt-1 text-xs text-gray-500">
                                                Handled by {{ $assignedNames ?: 'Maintenance Team' }}
                                            </p>
                                        @else
                                            <p class="mt-1 text-xs text-gray-400">
                                                Pending
                                            </p>
                                        @endif

                                    </div>

                                </div>


                                <div class="flex gap-4">

                                    <div class="flex flex-col items-center">

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                            {{ $maintenanceAccomplished ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">

                                            <i
                                                class="fas {{ $maintenanceAccomplished ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                        </div>

                                        <div
                                            class="min-h-12 w-px flex-1
                                            {{ $maintenanceAccomplished ? 'bg-emerald-200' : 'bg-gray-200' }}">
                                        </div>

                                    </div>

                                    <div class="pb-6">

                                        <p
                                            class="text-sm font-semibold
                                            {{ $maintenanceAccomplished ? 'text-gray-900' : 'text-gray-400' }}">
                                            Maintenance Accomplished
                                        </p>

                                        @if ($maintenanceAccomplished)
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $firstCompletedAt?->format('M d, Y • h:i A') ??
                                                    ($complaint->completed_at?->format('M d, Y • h:i A') ?? 'Report submitted') }}
                                            </p>

                                            <p class="mt-1 text-xs text-gray-500">
                                                Accomplished by {{ $assignedNames ?: 'Maintenance Team' }}
                                            </p>
                                        @else
                                            <p class="mt-1 text-xs text-gray-400">
                                                Pending plumber accomplishment report
                                            </p>
                                        @endif

                                    </div>

                                </div>


                                <div class="flex gap-4">

                                    <div class="flex flex-col items-center">

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                            {{ $managerReviewed
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : ($reportSubmitted
                                                    ? 'bg-amber-100 text-amber-700'
                                                    : 'bg-gray-100 text-gray-400') }}">

                                            <i class="fas {{ $managerReviewed ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                        </div>

                                        <div
                                            class="min-h-12 w-px flex-1
                                            {{ $managerReviewed ? 'bg-emerald-200' : 'bg-gray-200' }}">
                                        </div>

                                    </div>

                                    <div class="pb-6">

                                        <p
                                            class="text-sm font-semibold
                                            {{ $managerReviewed ? 'text-gray-900' : ($reportSubmitted ? 'text-amber-700' : 'text-gray-400') }}">
                                            Manager Review
                                        </p>

                                        @if ($managerReviewed)
                                            <p class="mt-1 text-xs text-gray-500">
                                                Maintenance report approved
                                            </p>

                                            @if ($maintenanceReport->reviewer)
                                                <p class="mt-1 text-xs text-gray-500">
                                                    Reviewed by {{ $maintenanceReport->reviewer->full_name }}
                                                </p>
                                            @endif
                                        @elseif ($maintenanceReport?->review_status === 'Returned')
                                            <p class="mt-1 text-xs text-amber-700">
                                                Report returned for correction
                                            </p>
                                        @elseif ($reportSubmitted)
                                            <p class="mt-1 text-xs text-amber-700">
                                                Pending manager review
                                            </p>
                                        @else
                                            <p class="mt-1 text-xs text-gray-400">
                                                Pending
                                            </p>
                                        @endif

                                    </div>

                                </div>


                                <div class="flex gap-4">

                                    <div class="flex flex-col items-center">

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                            {{ $isClosed ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">

                                            <i class="fas {{ $isClosed ? 'fa-check' : 'fa-circle' }} text-xs"></i>
                                        </div>

                                    </div>

                                    <div>

                                        <p
                                            class="text-sm font-semibold
                                            {{ $isClosed ? 'text-gray-900' : 'text-gray-400' }}">
                                            {{ $isCommercial ? 'Request Closed' : 'Complaint Closed' }}
                                        </p>

                                        @if ($isClosed)
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $complaint->completed_at?->format('M d, Y • h:i A') ?? $complaint->updated_at?->format('M d, Y • h:i A') }}
                                            </p>
                                        @else
                                            <p class="mt-1 text-xs text-gray-400">
                                                Pending finalization
                                            </p>
                                        @endif

                                    </div>

                                </div>
                            @elseif ($isClosed)
                                <div class="flex gap-4">

                                    <div class="flex flex-col items-center">
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center
                                            rounded-full bg-emerald-100 text-emerald-700">

                                            <i class="fas fa-check text-xs"></i>
                                        </div>
                                    </div>

                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">
                                            Request Closed
                                        </p>

                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $complaint->completed_at?->format('M d, Y • h:i A') ?? $complaint->updated_at?->format('M d, Y • h:i A') }}
                                        </p>

                                        <p class="mt-1 text-xs text-gray-500">
                                            Closed after Customer Service processing
                                        </p>
                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>


                @if ($reportSubmitted)

                    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                        <div
                            class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4
                            sm:flex-row sm:items-center sm:justify-between">

                            <div>
                                <h2 class="font-bold text-gray-900">
                                    Maintenance Result
                                </h2>

                                <p class="mt-1 text-xs text-gray-500">
                                    Plumber-submitted accomplishment report
                                </p>
                            </div>

                            @php
                                $reviewClass = match ($maintenanceReport->review_status) {
                                    'Approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Returned' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-blue-50 text-blue-700 border-blue-200',
                                };
                            @endphp

                            <span
                                class="inline-flex w-fit rounded-full border px-2.5 py-1
                                text-xs font-semibold {{ $reviewClass }}">

                                {{ $maintenanceReport->review_status }}

                            </span>

                        </div>


                        <div class="space-y-6 p-5">

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Diagnosis / Findings
                                    </p>

                                    <div class="mt-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                        <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                            {{ $maintenanceReport->diagnosis ?: 'No diagnosis recorded.' }}
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Root Cause
                                    </p>

                                    <div class="mt-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                        <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                            {{ $maintenanceReport->root_cause ?: 'No root cause recorded.' }}
                                        </p>
                                    </div>
                                </div>

                            </div>


                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Materials / Parts
                                </p>

                                <div class="mt-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                    <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                        {{ $maintenanceReport->materials_parts ?: 'No materials or parts recorded.' }}
                                    </p>
                                </div>
                            </div>


                            @if ($maintenanceReport->technician_notes)
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Plumber Notes
                                    </p>

                                    <div class="mt-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                        <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                            {{ $maintenanceReport->technician_notes }}
                                        </p>
                                    </div>
                                </div>
                            @endif


                            @if ($maintenanceReport->before_photo || $maintenanceReport->after_photo)

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                            Before Photo
                                        </p>

                                        @if ($maintenanceReport->before_photo)
                                            <img src="{{ asset('storage/' . $maintenanceReport->before_photo) }}"
                                                alt="Before maintenance"
                                                class="mt-2 h-64 w-full rounded-xl border
                                                border-gray-200 object-cover">
                                        @else
                                            <div
                                                class="mt-2 flex h-64 items-center justify-center
                                                rounded-xl border border-dashed border-gray-300 bg-gray-50">

                                                <p class="text-sm text-gray-400">
                                                    No before photo
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                            After Photo
                                        </p>

                                        @if ($maintenanceReport->after_photo)
                                            <img src="{{ asset('storage/' . $maintenanceReport->after_photo) }}"
                                                alt="After maintenance"
                                                class="mt-2 h-64 w-full rounded-xl border
                                                border-gray-200 object-cover">
                                        @else
                                            <div
                                                class="mt-2 flex h-64 items-center justify-center
                                                rounded-xl border border-dashed border-gray-300 bg-gray-50">

                                                <p class="text-sm text-gray-400">
                                                    No after photo
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                </div>

                            @endif


                            <div class="border-t border-gray-100 pt-5">

                                <a href="{{ route('maintenance-manager.complaints.report', $complaint) }}"
                                    target="_blank"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl
                                    bg-gray-900 px-4 py-3 text-sm font-semibold text-white
                                    transition hover:bg-gray-800">

                                    <i class="fas fa-file-lines"></i>
                                    View / Print Report

                                </a>

                            </div>

                        </div>

                    </div>

                @endif

            </div>


            <div class="space-y-6">

                @if ($canAssign)

                    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                        <div class="border-b border-gray-100 px-5 py-4">

                            <h2 class="font-bold text-gray-900">
                                Plumber Assignment
                            </h2>

                            <p class="mt-1 text-xs text-gray-500">
                                Review the AI recommendation and select the plumbers who will handle this concern.
                            </p>

                        </div>


                        <form method="POST" action="{{ route('maintenance-manager.complaints.assign', $complaint) }}"
                            id="assignmentForm" class="p-5">

                            @csrf


                            @if (!empty($plumberRecommendationError))

                                <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center
                                            rounded-xl bg-amber-100 text-amber-600">

                                            <i class="fas fa-triangle-exclamation"></i>

                                        </div>

                                        <div>
                                            <p class="text-sm font-semibold text-amber-900">
                                                Recommendation Unavailable
                                            </p>

                                            <p class="mt-1 text-xs leading-5 text-amber-700">
                                                {{ $plumberRecommendationError }}
                                            </p>

                                            <p class="mt-2 text-xs text-amber-600">
                                                You can continue assigning plumbers manually.
                                            </p>
                                        </div>

                                    </div>

                                </div>
                            @elseif ($plumberRecommendations['has_recommendations'] ?? false)
                                <div class="mb-6">

                                    <div class="mb-3 flex items-start justify-between gap-3">

                                        <div class="flex items-center gap-2">

                                            <div
                                                class="flex h-8 w-8 items-center justify-center
                                                rounded-lg bg-violet-100 text-violet-600">

                                                <i class="fas fa-wand-magic-sparkles text-sm"></i>

                                            </div>

                                            <div>
                                                <p class="text-sm font-bold text-gray-900">
                                                    AI-Assisted Recommendation
                                                </p>

                                                <p class="mt-0.5 text-xs text-gray-500">
                                                    Decision support for plumber assignment
                                                </p>
                                            </div>

                                        </div>

                                        <span
                                            class="inline-flex rounded-full border border-violet-100
                                            bg-violet-50 px-2 py-1 text-[10px] font-semibold text-violet-700">

                                            AI Assisted

                                        </span>

                                    </div>


                                    <div class="space-y-3">

                                        @foreach ($plumberRecommendations['recommendations'] ?? [] as $index => $recommendation)
                                            @php

                                                $serviceArea =
                                                    $recommendation['service_area']['name'] ??
                                                    'No permanent area assigned';
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
                                                class="overflow-hidden rounded-xl border
                                                {{ $isTopRecommendation ? 'border-violet-200' : 'border-gray-200' }}
                                                bg-white">

                                                @if ($isTopRecommendation)
                                                    <div class="border-b border-violet-100 bg-violet-50 px-4 py-2">
                                                        <p
                                                            class="text-[10px] font-bold uppercase tracking-wide text-violet-700">
                                                            <i class="fas fa-star mr-1"></i>
                                                            Top Recommendation
                                                        </p>
                                                    </div>
                                                @endif


                                                <div class="p-4">

                                                    <div class="flex items-start justify-between gap-3">

                                                        <div class="min-w-0">

                                                            <p class="truncate text-sm font-bold text-gray-900">
                                                                {{ $recommendation['name'] ?? 'Plumber' }}
                                                            </p>

                                                            <div
                                                                class="mt-1 flex items-center gap-1.5 text-xs text-gray-500">
                                                                <i class="fas fa-location-dot text-gray-400"></i>

                                                                <span class="truncate">
                                                                    {{ $serviceArea }}
                                                                </span>
                                                            </div>

                                                            <div class="mt-2 flex flex-wrap items-center gap-2">

                                                                <span
                                                                    class="inline-flex rounded-lg border px-2 py-1
                                                                    text-[10px] font-semibold {{ $recommendationClass }}">

                                                                    {{ $recommendationLevel }}

                                                                </span>

                                                                <span class="text-xs text-gray-500">
                                                                    Score
                                                                    <span class="font-bold text-gray-700">
                                                                        {{ number_format($score, 1) }}%
                                                                    </span>
                                                                </span>

                                                            </div>

                                                        </div>

                                                    </div>


                                                    <div class="mt-4 grid grid-cols-2 gap-2">

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


                                                    <div class="mt-2 rounded-lg border border-gray-100 bg-gray-50 p-3">

                                                        <p
                                                            class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">
                                                            Nearest Active Assignment
                                                        </p>

                                                        @if ($distance !== null)
                                                            <p class="mt-1 text-xs font-bold text-gray-800">

                                                                @if ((float) $distance < 1)
                                                                    {{ number_format((float) $distance * 1000, 0) }}
                                                                    meters away
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
                                                        <div class="mt-4">

                                                            <p class="text-xs font-bold text-gray-700">
                                                                Why recommended
                                                            </p>

                                                            <div class="mt-2 space-y-2">

                                                                @foreach ($recommendation['reasons'] as $reason)
                                                                    <div class="flex items-start gap-2">

                                                                        <i
                                                                            class="fas fa-circle-check mt-0.5
                                                                            text-[11px] text-emerald-500"></i>

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
                                                            class="mt-4 rounded-lg border border-amber-100 bg-amber-50 p-3">

                                                            <p class="text-xs font-bold text-amber-800">
                                                                Considerations
                                                            </p>

                                                            <div class="mt-2 space-y-2">

                                                                @foreach ($recommendation['considerations'] as $consideration)
                                                                    <div class="flex items-start gap-2">

                                                                        <i
                                                                            class="fas fa-circle-info mt-0.5
                                                                            text-[11px] text-amber-500"></i>

                                                                        <p class="text-[11px] leading-4 text-amber-700">
                                                                            {{ $consideration }}
                                                                        </p>

                                                                    </div>
                                                                @endforeach

                                                            </div>

                                                        </div>
                                                    @endif


                                                    <button type="button"
                                                        class="ai-select-plumber mt-4 inline-flex w-full
                                                        items-center justify-center gap-2 rounded-xl
                                                        bg-violet-600 px-3 py-2.5 text-xs font-semibold
                                                        text-white transition hover:bg-violet-700"
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


                                    <div class="mt-3 rounded-xl border border-blue-100 bg-blue-50 p-3">

                                        <div class="flex items-start gap-2">

                                            <i class="fas fa-circle-info mt-0.5 text-xs text-blue-600"></i>

                                            <p class="text-[10px] leading-4 text-blue-700">
                                                Recommendations use available operational information such as
                                                active workload and active-assignment locations. The Maintenance
                                                Manager makes the final assignment decision.
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            @endif


                            <div class="mb-2 flex items-center justify-between">
                                <label for="plumberDropdownTrigger" class="text-sm font-bold text-gray-900">
                                    Select Plumbers
                                </label>

                                <span id="selectedCount"
                                    class="inline-flex h-6 min-w-6 items-center justify-center rounded-full
        bg-blue-600 px-2 text-xs font-bold text-white">
                                    0
                                </span>
                            </div>

                            <div id="plumberDropdown" class="rounded-xl border border-gray-300 bg-white shadow-sm">

                                {{-- Trigger --}}
                                <button type="button" id="plumberDropdownTrigger" aria-haspopup="listbox"
                                    aria-expanded="false"
                                    class="flex w-full items-center justify-between gap-3 rounded-xl px-3.5 py-3
        text-left text-sm transition hover:bg-gray-50
        focus:outline-none focus:ring-2 focus:ring-blue-500/30">

                                    <span class="flex min-w-0 items-center gap-2.5">
                                        <span
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                            <i class="fas fa-users text-xs"></i>
                                        </span>
                                        <span id="plumberDropdownLabel" class="truncate text-gray-500">
                                            Choose one or more plumbers
                                        </span>
                                    </span>

                                    <i id="plumberDropdownChevron"
                                        class="fas fa-chevron-down text-xs text-gray-400 transition-transform"></i>
                                </button>

                                {{-- Panel --}}
                                <div id="plumberDropdownPanel" class="hidden border-t border-gray-100">

                                    {{-- Search --}}
                                    <div class="p-3 pb-2">
                                        <div class="relative">
                                            <i
                                                class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                                            <input type="text" id="plumberSearch"
                                                placeholder="Search name, area, or ID..." autocomplete="off"
                                                class="w-full rounded-lg border border-gray-200 bg-gray-50 py-2 pl-8 pr-3
                    text-xs text-gray-700 placeholder-gray-400
                    focus:border-blue-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                        </div>
                                    </div>

                                    {{-- Bulk actions --}}
                                    <div class="flex items-center justify-between px-4 pb-2">
                                        <button type="button" id="plumberSelectAll"
                                            class="text-[11px] font-semibold text-blue-600 hover:text-blue-800">
                                            Select all
                                        </button>
                                        <button type="button" id="plumberClearAll"
                                            class="text-[11px] font-semibold text-gray-500 hover:text-red-600">
                                            Clear
                                        </button>
                                    </div>

                                    {{-- Options --}}
                                    <div class="max-h-64 divide-y divide-gray-50 overflow-y-auto border-t border-gray-100"
                                        role="listbox" aria-multiselectable="true">

                                        @forelse ($technicians as $technician)
                                            @php
                                                $initials = strtoupper(
                                                    substr($technician->first_name ?? '', 0, 1) .
                                                        substr($technician->last_name ?? '', 0, 1),
                                                );
                                                $areaName =
                                                    $technician->serviceArea?->name ?? 'No permanent area assigned';
                                            @endphp

                                            <label
                                                class="plumber-option flex cursor-pointer items-center gap-3 px-4 py-2.5
                    transition hover:bg-blue-50/60"
                                                data-name="{{ $technician->full_name }}"
                                                data-initials="{{ $initials }}"
                                                data-search="{{ strtolower($technician->full_name . ' ' . $areaName . ' ' . $technician->employee_id) }}">

                                                <input type="checkbox" id="plumber-checkbox-{{ $technician->id }}"
                                                    name="technician_ids[]" value="{{ $technician->id }}"
                                                    class="technician-checkbox h-4 w-4 shrink-0 rounded border-gray-300
                        text-blue-600 focus:ring-blue-500">

                                                <span
                                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg
                        bg-blue-100 text-xs font-bold text-blue-700">
                                                    {{ $initials }}
                                                </span>

                                                <span class="min-w-0 flex-1">
                                                    <span class="block truncate text-sm font-semibold text-gray-900">
                                                        {{ $technician->full_name }}
                                                    </span>

                                                    <span
                                                        class="mt-0.5 flex items-center gap-1.5 text-[11px] text-gray-500">
                                                        <i class="fas fa-location-dot text-gray-400"></i>
                                                        <span class="truncate">{{ $areaName }}</span>
                                                    </span>

                                                    @if ($technician->employee_id || $technician->position?->name)
                                                        <span class="block truncate text-[11px] text-gray-400">
                                                            {{ $technician->employee_id ? 'ID: ' . $technician->employee_id : '' }}
                                                            {{ $technician->employee_id && $technician->position?->name ? '•' : '' }}
                                                            {{ $technician->position?->name }}
                                                        </span>
                                                    @endif
                                                </span>
                                            </label>

                                        @empty
                                            <div class="p-5 text-center">
                                                <p class="text-sm font-medium text-gray-700">No active plumbers available.
                                                </p>
                                            </div>
                                        @endforelse

                                    </div>

                                    <div id="plumberNoResults" class="hidden p-5 text-center">
                                        <p class="text-xs font-medium text-gray-500">No plumbers match your search.</p>
                                    </div>

                                    <div class="border-t border-gray-100 p-3">
                                        <button type="button" id="plumberDropdownDone"
                                            class="w-full rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold
                text-white transition hover:bg-gray-800">
                                            Done
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Selected chips --}}
                            <div id="selectedChips" class="mt-3 flex flex-wrap gap-2"></div>

                            @error('technician_ids')
                                <p class="mt-3 text-xs font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror



                            <button type="submit" id="assignButton" disabled
                                class="mt-5 inline-flex w-full items-center justify-center gap-2
                                rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold
                                text-white transition hover:bg-blue-700
                                disabled:cursor-not-allowed disabled:opacity-50">

                                <i class="fas fa-users"></i>
                                Assign Maintenance Team

                            </button>

                        </form>

                    </div>

                @endif


                @if ($complaint->technicians->isNotEmpty())

                    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                        <div class="border-b border-gray-100 px-5 py-4">

                            <div class="flex items-center justify-between gap-3">

                                <div>
                                    <h2 class="font-bold text-gray-900">
                                        Assigned Plumbers
                                    </h2>

                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $complaint->technicians->count() }}
                                        {{ $complaint->technicians->count() === 1 ? 'plumber' : 'plumbers' }}
                                        assigned
                                    </p>
                                </div>

                                <div
                                    class="flex h-9 w-9 items-center justify-center
                                    rounded-xl bg-indigo-50 text-indigo-600">

                                    <i class="fas fa-users-gear"></i>

                                </div>

                            </div>

                        </div>


                        <div class="space-y-3 p-5">

                            @foreach ($complaint->technicians as $technician)
                                <div
                                    class="flex items-center gap-3 rounded-xl
                                    border border-gray-100 bg-gray-50 p-3">

                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center
                                        rounded-xl bg-blue-100 text-xs font-bold text-blue-700">

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

                                    </div>

                                    <span class="shrink-0 text-[10px] font-semibold text-gray-500">
                                        {{ $technician->pivot?->status ?? 'Assigned' }}
                                    </span>

                                </div>
                            @endforeach

                        </div>

                    </div>

                @endif


                @if (!$canAssign && $complaint->technicians->isEmpty() && $complaint->status === 'For Maintenance')
                    <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5">

                        <div class="flex items-start gap-3">

                            <i class="fas fa-circle-info mt-0.5 text-violet-600"></i>

                            <div>
                                <p class="text-sm font-semibold text-violet-900">
                                    Ready for Maintenance Assignment
                                </p>

                                <p class="mt-1 text-xs leading-5 text-violet-700">
                                    This request was forwarded by Customer Service and is waiting for plumber assignment.
                                </p>
                            </div>

                        </div>

                    </div>
                @endif

            </div>

        </div>

    </div>


    @if ($canAssign)
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const checkboxes = document.querySelectorAll('.technician-checkbox');
                const options = document.querySelectorAll('.plumber-option');
                const selectedCount = document.getElementById('selectedCount');
                const assignButton = document.getElementById('assignButton');
                const aiButtons = document.querySelectorAll('.ai-select-plumber');

                const trigger = document.getElementById('plumberDropdownTrigger');
                const panel = document.getElementById('plumberDropdownPanel');
                const chevron = document.getElementById('plumberDropdownChevron');
                const label = document.getElementById('plumberDropdownLabel');
                const chipsBox = document.getElementById('selectedChips');
                const search = document.getElementById('plumberSearch');
                const noResults = document.getElementById('plumberNoResults');
                const dropdown = document.getElementById('plumberDropdown');

                /* ---------- Dropdown open / close ---------- */
                function setOpen(open) {
                    panel.classList.toggle('hidden', !open);
                    chevron.classList.toggle('rotate-180', open);
                    trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                    if (open && search) search.focus();
                }

                trigger.addEventListener('click', () => setOpen(panel.classList.contains('hidden')));
                document.getElementById('plumberDropdownDone')?.addEventListener('click', () => setOpen(false));

                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') setOpen(false);
                });

                /* ---------- Search ---------- */
                search?.addEventListener('input', function() {
                    const q = this.value.trim().toLowerCase();
                    let visible = 0;

                    options.forEach(opt => {
                        const match = opt.dataset.search.includes(q);
                        opt.classList.toggle('hidden', !match);
                        if (match) visible++;
                    });

                    noResults.classList.toggle('hidden', visible > 0);
                });

                /* ---------- Bulk actions (only visible/filtered rows) ---------- */
                function setVisible(checked) {
                    options.forEach(opt => {
                        if (opt.classList.contains('hidden')) return;
                        const cb = opt.querySelector('.technician-checkbox');
                        cb.checked = checked;
                    });
                    updateSelection();
                }

                document.getElementById('plumberSelectAll')?.addEventListener('click', () => setVisible(true));
                document.getElementById('plumberClearAll')?.addEventListener('click', () => setVisible(false));

                /* ---------- AI recommendation buttons ---------- */
                function updateAiButtons() {
                    aiButtons.forEach(function(button) {
                        const checkbox = document.getElementById(
                            `plumber-checkbox-${button.dataset.plumberId}`);
                        if (!checkbox) return;

                        const text = button.querySelector('.ai-select-text');
                        const icon = button.querySelector('i');

                        button.classList.toggle('bg-emerald-600', checkbox.checked);
                        button.classList.toggle('hover:bg-emerald-700', checkbox.checked);
                        button.classList.toggle('bg-violet-600', !checkbox.checked);
                        button.classList.toggle('hover:bg-violet-700', !checkbox.checked);

                        if (text) text.textContent = checkbox.checked ? 'Selected' : 'Select Plumber';
                        if (icon) icon.className = checkbox.checked ? 'fas fa-circle-check' :
                            'fas fa-user-check';
                    });
                }

                /* ---------- Chips ---------- */
                function renderChips(selected) {
                    chipsBox.innerHTML = '';

                    selected.forEach(cb => {
                        const opt = cb.closest('.plumber-option');

                        const chip = document.createElement('span');
                        chip.className =
                            'inline-flex items-center gap-1.5 rounded-full border border-blue-200 ' +
                            'bg-blue-50 py-1 pl-1 pr-2 text-xs font-semibold text-blue-800';

                        const avatar = document.createElement('span');
                        avatar.className =
                            'flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 ' +
                            'text-[9px] font-bold text-white';
                        avatar.textContent = opt.dataset.initials;

                        const name = document.createElement('span');
                        name.textContent = opt.dataset.name;

                        const remove = document.createElement('button');
                        remove.type = 'button';
                        remove.className = 'text-blue-400 transition hover:text-red-600';
                        remove.setAttribute('aria-label', 'Remove ' + opt.dataset.name);
                        remove.innerHTML = '<i class="fas fa-xmark text-[10px]"></i>';
                        remove.addEventListener('click', () => {
                            cb.checked = false;
                            updateSelection();
                        });

                        chip.append(avatar, name, remove);
                        chipsBox.appendChild(chip);
                    });
                }

                /* ---------- Main update ---------- */
                function updateSelection() {
                    const selected = Array.from(checkboxes).filter(cb => cb.checked);
                    const count = selected.length;

                    options.forEach(opt => {
                        const checked = opt.querySelector('.technician-checkbox').checked;
                        opt.classList.toggle('bg-blue-50', checked);
                    });

                    if (selectedCount) selectedCount.textContent = count;

                    // Trigger label
                    if (count === 0) {
                        label.textContent = 'Choose one or more plumbers';
                        label.className = 'truncate text-gray-500';
                    } else if (count === 1) {
                        label.textContent = selected[0].closest('.plumber-option').dataset.name;
                        label.className = 'truncate font-semibold text-gray-900';
                    } else {
                        label.textContent = `${count} plumbers selected`;
                        label.className = 'truncate font-semibold text-gray-900';
                    }

                    dropdown.classList.toggle('border-blue-400', count > 0);
                    dropdown.classList.toggle('border-gray-300', count === 0);

                    renderChips(selected);

                    if (assignButton) {
                        assignButton.disabled = count === 0;
                        assignButton.innerHTML = count > 0 ?
                            `<i class="fas fa-users"></i> Assign ${count} Plumber${count > 1 ? 's' : ''}` :
                            `<i class="fas fa-users"></i> Assign Maintenance Team`;
                    }

                    updateAiButtons();
                }

                checkboxes.forEach(cb => cb.addEventListener('change', updateSelection));

                aiButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        const checkbox = document.getElementById(
                            `plumber-checkbox-${this.dataset.plumberId}`);
                        if (!checkbox) return;

                        checkbox.checked = !checkbox.checked;
                        checkbox.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    });
                });

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

                    const mapElement =
                        document.getElementById('complaintMap');

                    if (
                        !mapElement ||
                        typeof L === 'undefined'
                    ) {
                        return;
                    }

                    const latitude =
                        parseFloat(@json((float) $displayLatitude));

                    const longitude =
                        parseFloat(@json((float) $displayLongitude));

                    if (
                        Number.isNaN(latitude) ||
                        Number.isNaN(longitude)
                    ) {
                        return;
                    }

                    const map =
                        L.map(
                            'complaintMap', {
                                scrollWheelZoom: false,
                                zoomControl: true
                            }
                        )
                        .setView(
                            [latitude, longitude],
                            16
                        );

                    L.tileLayer(
                        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap contributors'
                        }
                    ).addTo(map);

                    const marker =
                        L.marker(
                            [latitude, longitude]
                        )
                        .addTo(map);

                    marker.bindPopup(
                        '<strong>{{ addslashes($complaint->complaint_no) }}</strong><br>' +
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

                    window.addEventListener(
                        'resize',
                        refreshMap
                    );

                });
            </script>
        @endpush
    @endif

@endsection
