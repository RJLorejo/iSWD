@extends('technician.layouts.app')

@section('title', 'Complaint Details')

@section('content')

    @php
        $currentTechnician = $complaint->technicians->firstWhere('id', auth()->id());
        $myAssignmentStatus = $currentTechnician?->pivot?->status;

        $report = $complaint->maintenanceReport;
        $commercialResolution = $complaint->commercialResolution;

        $isEngineering = str_contains(strtolower($complaint->division?->name ?? ''), 'engineering');

        $isForwardedRequest = !$isEngineering && !is_null($commercialResolution?->forwarded_to_maintenance_at);

        $displayStatus = match ($complaint->status) {
            'Completed' => 'Accomplished',
            default => $complaint->status,
        };

        $statusClasses = match ($complaint->status) {
            'Assigned' => 'bg-blue-50 text-blue-700 border-blue-200',
            'In Progress' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Completed' => 'bg-green-50 text-green-700 border-green-200',
            'Closed' => 'bg-gray-100 text-gray-700 border-gray-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };

        $urgency = strtoupper(trim($complaint->aiAnalysis?->urgency_level ?? ''));

        $urgencyClasses = match ($urgency) {
            'HIGH' => 'bg-red-50 text-red-700 border-red-200',
            'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',
            'LOW' => 'bg-green-50 text-green-700 border-green-200',
            default => 'bg-gray-50 text-gray-500 border-gray-200',
        };

        $assignmentDate = $complaint->technicians->pluck('pivot.assigned_at')->filter()->sort()->first();

        $startedDate = $complaint->technicians->pluck('pivot.started_at')->filter()->sort()->first();

        $completedDate = $complaint->technicians->pluck('pivot.completed_at')->filter()->sort()->first();

        $assignedNames = $complaint->technicians
            ->map(fn($technician) => $technician->full_name)
            ->filter()
            ->implode(', ');

        $reportSubmitted =
            $report && in_array($report->review_status, ['Pending Review', 'Returned', 'Approved'], true);

        $serviceAddress = $complaint->address;
        $serviceLandmark = $complaint->landmark;
        $serviceLatitude = $complaint->latitude;
        $serviceLongitude = $complaint->longitude;

        if (!$isEngineering && $complaint->consumer?->address) {
            $consumerAddress = $complaint->consumer->address;

            $serviceAddress = $consumerAddress->full_address ?? ($consumerAddress->address ?? $complaint->address);

            $serviceLandmark = $consumerAddress->landmark ?? $complaint->landmark;

            $serviceLatitude = $consumerAddress->latitude ?? $complaint->latitude;

            $serviceLongitude = $consumerAddress->longitude ?? $complaint->longitude;
        }

        $hasCoordinates = !is_null($serviceLatitude) && !is_null($serviceLongitude);

        $hasCsProcessing = !$isEngineering && $commercialResolution;

        $initialProcessingStarted = $commercialResolution?->created_at;

        $initialProcessingCompleted = $commercialResolution?->initial_processing_completed_at;

        $forwardedAt = $commercialResolution?->forwarded_to_maintenance_at;

        $forwarder = $commercialResolution?->forwarder;

        $processor = $commercialResolution?->processor;
    @endphp


    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

            <div>

                <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">

                    <a href="{{ route('technician.complaints.index') }}" class="hover:text-sky-600 transition">
                        My Complaints
                    </a>

                    <i class="fas fa-chevron-right text-[10px]"></i>

                    <span>
                        {{ $complaint->complaint_no }}
                    </span>

                </div>


                <div class="flex flex-wrap items-center gap-3 mt-2">

                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        {{ $complaint->category?->name ?? 'Service Complaint' }}
                    </h1>

                    @if ($urgency)
                        <span
                            class="inline-flex items-center gap-1.5
                                   px-2.5 py-1 rounded-full border
                                   text-xs font-bold
                                   {{ $urgencyClasses }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                            {{ $urgency }} URGENCY
                        </span>
                    @else
                        <span
                            class="inline-flex items-center
                                   px-2.5 py-1 rounded-full border
                                   bg-gray-50 border-gray-200
                                   text-xs font-semibold text-gray-400">
                            NOT ASSESSED
                        </span>
                    @endif

                </div>


                <p class="text-sm text-gray-500 mt-2">

                    {{ $complaint->complaint_no }}

                    @if ($complaint->division?->name)
                        · {{ $complaint->division->name }}
                    @endif

                </p>

            </div>


            <a href="{{ route('technician.complaints.index') }}"
                class="inline-flex items-center justify-center gap-2
                       px-4 py-2.5 rounded-xl
                       border border-gray-300
                       bg-white text-gray-700
                       hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left"></i>

                Back
            </a>

        </div>


        @if (session('success'))
            <div
                class="rounded-xl border border-green-200
                       bg-green-50 px-4 py-3
                       text-sm text-green-800
                       flex items-start gap-3">
                <i class="fas fa-circle-check mt-0.5"></i>

                <span>
                    {{ session('success') }}
                </span>
            </div>
        @endif


        @if (session('error'))
            <div
                class="rounded-xl border border-red-200
                       bg-red-50 px-4 py-3
                       text-sm text-red-800
                       flex items-start gap-3">
                <i class="fas fa-circle-exclamation mt-0.5"></i>

                <span>
                    {{ session('error') }}
                </span>
            </div>
        @endif


        <div
            class="bg-white rounded-2xl
                   border border-gray-200
                   shadow-sm overflow-hidden">

            <div class="p-5 sm:p-6">

                <div class="flex flex-col xl:flex-row
                           xl:items-center xl:justify-between gap-5">

                    <div class="flex items-start gap-4">

                        <div
                            class="w-12 h-12 rounded-xl shrink-0
                                   flex items-center justify-center
                                   @if ($complaint->status === 'Assigned') bg-blue-50 text-blue-600
                                   @elseif ($complaint->status === 'In Progress')
                                       bg-amber-50 text-amber-600
                                   @elseif ($complaint->status === 'Completed')
                                       bg-green-50 text-green-600
                                   @else
                                       bg-gray-100 text-gray-600 @endif">

                            @if ($complaint->status === 'Assigned')
                                <i class="fas fa-user-clock"></i>
                            @elseif ($complaint->status === 'In Progress')
                                <i class="fas fa-screwdriver-wrench"></i>
                            @elseif ($complaint->status === 'Completed')
                                <i class="fas fa-clipboard-check"></i>
                            @else
                                <i class="fas fa-circle-check"></i>
                            @endif

                        </div>


                        <div>

                            <p class="text-xs uppercase tracking-wide font-semibold text-gray-400">
                                Current Status
                            </p>

                            <div class="flex flex-wrap items-center gap-2 mt-1">

                                <span
                                    class="inline-flex items-center gap-1.5
                                           px-3 py-1.5 rounded-full border
                                           text-sm font-semibold
                                           {{ $statusClasses }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                    {{ $displayStatus }}
                                </span>

                            </div>


                            <p class="text-sm text-gray-500 mt-2">

                                @if ($complaint->status === 'Assigned')
                                    Maintenance has been assigned and is waiting to start.
                                @elseif ($complaint->status === 'In Progress')
                                    Maintenance work is currently in progress.
                                @elseif ($complaint->status === 'Completed' && $report?->review_status === 'Returned')
                                    The accomplishment report was returned for correction.
                                @elseif ($complaint->status === 'Completed' && $report?->review_status === 'Pending Review')
                                    Maintenance is accomplished and waiting for Manager review.
                                @elseif ($complaint->status === 'Completed' && $report?->review_status === 'Approved')
                                    The accomplishment report has been approved.
                                @elseif ($complaint->status === 'Closed')
                                    The complaint has been finalized and closed.
                                @else
                                    Current maintenance status.
                                @endif

                            </p>

                        </div>

                    </div>


                    <div class="flex flex-wrap gap-2">

                        @if ($complaint->status === 'Assigned' && $myAssignmentStatus === 'Assigned')

                            <form method="POST" action="{{ route('technician.maintenance-reports.start', $complaint) }}">
                                @csrf

                                <button type="submit"
                                    class="inline-flex items-center gap-2
                                           px-5 py-3 rounded-xl
                                           bg-sky-600 text-white
                                           font-semibold
                                           hover:bg-sky-700 transition">
                                    <i class="fas fa-play"></i>

                                    Start Maintenance
                                </button>

                            </form>
                        @elseif ($complaint->status === 'In Progress')
                            <a href="{{ route('technician.maintenance-reports.create', $complaint) }}"
                                class="inline-flex items-center gap-2
                                       px-5 py-3 rounded-xl
                                       bg-sky-600 text-white
                                       font-semibold
                                       hover:bg-sky-700 transition">
                                <i class="fas fa-file-pen"></i>

                                Continue Accomplishment Report
                            </a>
                        @elseif ($complaint->status === 'Completed' && $report?->review_status === 'Returned')
                            <a href="{{ route('technician.maintenance-reports.create', $complaint) }}"
                                class="inline-flex items-center gap-2
                                       px-5 py-3 rounded-xl
                                       bg-red-600 text-white
                                       font-semibold
                                       hover:bg-red-700 transition">
                                <i class="fas fa-pen-to-square"></i>
                                Revise Accomplishment Report
                            </a>

                            @if ($reportSubmitted)
                                <a href="{{ route('technician.maintenance-reports.show', $complaint) }}"
                                    class="inline-flex items-center gap-2
                                           px-5 py-3 rounded-xl
                                           border border-gray-300
                                           bg-white text-gray-700
                                           font-semibold
                                           hover:bg-gray-50 transition">
                                    <i class="fas fa-eye"></i>

                                    View Returned Report
                                </a>
                            @endif
                        @elseif (in_array($complaint->status, ['Completed', 'Closed'], true) && $reportSubmitted)
                            <a href="{{ route('technician.maintenance-reports.show', $complaint) }}"
                                class="inline-flex items-center gap-2
           px-5 py-3 rounded-xl
           bg-green-600 text-white
           font-semibold
           hover:bg-green-700 transition">
                                @if ($report?->review_status === 'Pending Review')
                                    <i class="fas fa-eye"></i>
                                    View Submitted Report
                                @elseif ($report?->review_status === 'Approved')
                                    <i class="fas fa-circle-check"></i>
                                    View Approved Report
                                @else
                                    <i class="fas fa-file-circle-check"></i>
                                    View Accomplishment Report
                                @endif
                            </a>


                            <a href="{{ route('technician.maintenance-reports.print', $complaint) }}" target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2
                                       px-5 py-3 rounded-xl
                                       bg-gray-800 text-white
                                       font-semibold
                                       hover:bg-gray-900 transition">
                                <i class="fas fa-print"></i>

                                Print
                            </a>

                        @endif

                    </div>

                </div>

            </div>


            @if ($complaint->status === 'Completed' && $report?->review_status === 'Pending Review')

                <div class="border-t border-violet-100
                           bg-violet-50 px-5 sm:px-6 py-4">

                    <div class="flex items-start gap-3">

                        <i class="fas fa-clock text-violet-600 mt-0.5"></i>

                        <div>

                            <p class="font-semibold text-violet-900">
                                Waiting for Manager Review
                            </p>

                            <p class="text-sm text-violet-800 mt-1">
                                Your accomplishment report has been submitted.
                                No further action is required unless it is returned.
                            </p>

                        </div>

                    </div>

                </div>
            @elseif ($complaint->status === 'Completed' && $report?->review_status === 'Returned')
                <div class="border-t border-red-100
                           bg-red-50 px-5 sm:px-6 py-4">

                    <div class="flex items-start gap-3">

                        <i class="fas fa-rotate-left text-red-600 mt-0.5"></i>

                        <div class="flex-1">

                            <p class="font-semibold text-red-900">
                                Report Returned for Correction
                            </p>

                            <p class="text-sm text-red-800 mt-1">
                                Review the Manager remarks, correct the report,
                                and submit it again.
                            </p>


                            @if ($report?->review_remarks)
                                <div
                                    class="mt-3 p-3 rounded-xl
                                           bg-white border border-red-200">

                                    <p
                                        class="text-xs uppercase tracking-wide
                                               font-semibold text-red-700">
                                        Manager Remarks
                                    </p>

                                    <p
                                        class="text-sm text-red-900
                                               whitespace-pre-line mt-1">
                                        {{ $report->review_remarks }}
                                    </p>

                                </div>
                            @endif

                        </div>

                    </div>

                </div>
            @elseif (in_array($complaint->status, ['Completed', 'Closed'], true) && $report?->review_status === 'Approved')
                <div class="border-t border-green-100
                           bg-green-50 px-5 sm:px-6 py-4">

                    <div class="flex items-start gap-3">

                        <i class="fas fa-circle-check text-green-600 mt-0.5"></i>

                        <div>

                            <p class="font-semibold text-green-900">
                                Accomplishment Report Approved
                            </p>

                            <p class="text-sm text-green-800 mt-1">
                                The Manager has reviewed and approved
                                the maintenance accomplishment report.
                            </p>

                            @if ($report?->reviewed_at)
                                <p class="text-xs text-green-700 mt-2">
                                    {{ $report->reviewed_at->format('M d, Y h:i A') }}
                                </p>
                            @endif

                        </div>

                    </div>

                </div>

            @endif

        </div>


        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div class="xl:col-span-2 space-y-6">

                <div
                    class="bg-white rounded-2xl
                           border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-gray-100 bg-slate-50/60">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-11 h-11 rounded-xl
                   bg-sky-100 text-sky-700
                   flex items-center justify-center shrink-0">
                                <i class="fas fa-file-lines"></i>
                            </div>

                            <div>

                                <h2 class="font-semibold text-gray-900">
                                    Complaint Information
                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Consumer and complaint details for field maintenance.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5 sm:p-6 space-y-6">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Consumer
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Walk-in / Unregistered') }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Account Number
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->consumer?->account_number ?? '—' }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Contact Number
                                </p>

                                <p class="font-medium text-gray-900 mt-1">
                                    {{ $complaint->complainant_phone ?? ($complaint->consumer?->phone ?? '—') }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Division
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->division?->name ?? 'Not specified' }}
                                </p>

                            </div>


                            <div class="sm:col-span-2">

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Complaint Type
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->category?->name ?? 'Uncategorized' }}
                                </p>

                            </div>

                        </div>


                        <div class="border-t border-gray-100 pt-5">

                            <p
                                class="text-xs uppercase tracking-wide
                                       font-semibold text-gray-400">
                                Consumer Concern
                            </p>

                            <div
                                class="mt-2 p-4 rounded-xl
                                       bg-gray-50 border border-gray-100">

                                <p
                                    class="text-gray-700 leading-relaxed
                                           whitespace-pre-line">
                                    {{ $complaint->description ?: 'No description provided.' }}
                                </p>

                            </div>

                        </div>


                        {{-- Supporting Photos --}}
                        @php
                            $supportingPhotos = $complaint->photos ?? collect();

                            /*
    |--------------------------------------------------------------------------
    | Temporary Legacy Photo Fallback
    |--------------------------------------------------------------------------
    | This can be removed after old complaints.photo records have been
    | migrated into complaint_photos.
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
                                            class="text-xs uppercase tracking-wide
                           font-semibold text-gray-400">
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


                @if ($isForwardedRequest && $commercialResolution)

                    <div
                        class="bg-white rounded-2xl
                               border border-gray-200 shadow-sm overflow-hidden">

                        <div class="px-5 sm:px-6 py-5 border-b border-cyan-100 bg-cyan-50/50">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-11 h-11 rounded-xl
                   bg-cyan-100 text-cyan-700
                   flex items-center justify-center shrink-0">
                                    <i class="fas fa-headset"></i>
                                </div>

                                <div>

                                    <h2 class="font-semibold text-gray-900">
                                        Customer Service Processing Result
                                    </h2>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Findings and recommendation recorded before maintenance.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="p-5 sm:p-6 space-y-5">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Findings
                                </p>

                                <div
                                    class="mt-2 p-4 rounded-xl
                                           bg-gray-50 border border-gray-100">
                                    <p
                                        class="text-sm text-gray-700
                                               whitespace-pre-line leading-relaxed">
                                        {{ $commercialResolution->findings ?: 'No findings recorded.' }}
                                    </p>
                                </div>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Resolution / Recommendation
                                </p>

                                <div
                                    class="mt-2 p-4 rounded-xl
                                           bg-sky-50 border border-sky-100">
                                    <p
                                        class="text-sm text-sky-900
                                               whitespace-pre-line leading-relaxed">
                                        {{ $commercialResolution->resolution ?: 'No recommendation recorded.' }}
                                    </p>
                                </div>

                            </div>


                            <div class="grid grid-cols-1 sm:grid-cols-2
                                       gap-4 pt-2">

                                <div>

                                    <p class="text-xs text-gray-400">
                                        Processed By
                                    </p>

                                    <p class="text-sm font-semibold text-gray-800 mt-1">
                                        {{ $processor?->full_name ?? 'Customer Service' }}
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs text-gray-400">
                                        Forwarded to Maintenance
                                    </p>

                                    <p class="text-sm font-semibold text-gray-800 mt-1">

                                        @if ($forwardedAt)
                                            {{ $forwardedAt->format('M d, Y h:i A') }}
                                        @else
                                            —
                                        @endif

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>
                @elseif ($isEngineering)
                    <div
                        class="bg-white rounded-2xl
                               border border-gray-200 shadow-sm overflow-hidden">

                        <div class="px-5 sm:px-6 py-5 border-b border-green-100 bg-green-50/40">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-11 h-11 rounded-xl
                   bg-green-100 text-green-700
                   flex items-center justify-center shrink-0">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>

                                <div>

                                    <h2 class="font-semibold text-gray-900">
                                        Complaint Assessment
                                    </h2>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Verification result before maintenance assignment.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div
                                class="p-4 rounded-xl
                                       bg-green-50 border border-green-100">

                                <p class="text-xs uppercase tracking-wide font-semibold text-green-700">
                                    Verification Result
                                </p>

                                <p class="text-sm text-green-900 mt-2">
                                    This complaint was verified by Customer Service.
                                </p>

                            </div>


                            <div
                                class="p-4 rounded-xl
                                       bg-sky-50 border border-sky-100">

                                <p class="text-xs uppercase tracking-wide font-semibold text-sky-700">
                                    Next Step
                                </p>

                                <p class="text-sm text-sky-900 mt-2">
                                    The complaint was forwarded for maintenance
                                    and plumber assignment.
                                </p>

                            </div>

                        </div>

                    </div>

                @endif


                <div
                    class="bg-white rounded-2xl
                           border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-emerald-100 bg-emerald-50/40">

                        <div
                            class="flex flex-col sm:flex-row
                                   sm:items-center sm:justify-between gap-3">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-11 h-11 rounded-xl
               bg-emerald-100 text-emerald-700
               flex items-center justify-center shrink-0">
                                    <i class="fas fa-map-location-dot"></i>
                                </div>

                                <div>

                                    <h2 class="font-semibold text-gray-900">
                                        Service Location
                                    </h2>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Address, landmark, and map for field maintenance.
                                    </p>

                                </div>

                            </div>


                            @if ($hasCoordinates)
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $serviceLatitude }},{{ $serviceLongitude }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center gap-2
                                           px-4 py-2.5 rounded-xl
                                           bg-sky-600 text-white
                                           text-sm font-semibold
                                           hover:bg-sky-700 transition">
                                    <i class="fas fa-route"></i>

                                    Get Directions
                                </a>
                            @endif

                        </div>

                    </div>


                    <div class="p-5 sm:p-6 space-y-5">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Address
                                </p>

                                <p class="text-gray-700 mt-2 leading-relaxed">
                                    {{ $serviceAddress ?: 'No address provided' }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Landmark
                                </p>

                                <p class="text-gray-700 mt-2">
                                    {{ $serviceLandmark ?: 'No landmark provided' }}
                                </p>

                            </div>

                        </div>


                        @if ($hasCoordinates)
                            <div id="complaint-map"
                                class="w-full h-[350px] sm:h-[430px]
                                       rounded-2xl overflow-hidden
                                       border border-gray-200 z-0">
                            </div>
                        @else
                            <div
                                class="rounded-xl border border-amber-200
                                       bg-amber-50 p-4">

                                <div class="flex items-start gap-3">

                                    <i class="fas fa-map-location-dot text-amber-600 mt-0.5"></i>

                                    <div>

                                        <p class="font-semibold text-amber-900">
                                            Map location unavailable
                                        </p>

                                        <p class="text-sm text-amber-800 mt-1">
                                            Use the recorded service address and landmark
                                            to locate the service point.
                                        </p>

                                    </div>

                                </div>

                            </div>
                        @endif

                    </div>

                </div>


                @if ($reportSubmitted)

                    <div
                        class="bg-white rounded-2xl
                               border border-gray-200 shadow-sm overflow-hidden">

                        <div class="px-5 sm:px-6 py-5 border-b border-violet-100 bg-violet-50/40">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-11 h-11 rounded-xl
                   bg-violet-100 text-violet-700
                   flex items-center justify-center shrink-0">
                                    <i class="fas fa-file-circle-check"></i>
                                </div>

                                <div>

                                    <h2 class="font-semibold text-gray-900">
                                        Maintenance Result
                                    </h2>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Submitted maintenance accomplishment information.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="p-5 sm:p-6 space-y-5">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Diagnosis / Findings
                                </p>

                                <p
                                    class="text-sm text-gray-700
                                           whitespace-pre-line mt-2">
                                    {{ $report->diagnosis ?: '—' }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Root Cause
                                </p>

                                <p
                                    class="text-sm text-gray-700
                                           whitespace-pre-line mt-2">
                                    {{ $report->root_cause ?: '—' }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Materials / Parts
                                </p>

                                <p
                                    class="text-sm text-gray-700
                                           whitespace-pre-line mt-2">
                                    {{ $report->materials_parts ?: 'None recorded.' }}
                                </p>

                            </div>


                            @if ($report->technician_notes)
                                <div>

                                    <p
                                        class="text-xs uppercase tracking-wide
                                               font-semibold text-gray-400">
                                        Plumber Notes
                                    </p>

                                    <p
                                        class="text-sm text-gray-700
                                               whitespace-pre-line mt-2">
                                        {{ $report->technician_notes }}
                                    </p>

                                </div>
                            @endif


                            @if ($report->before_photo || $report->after_photo)

                                <div
                                    class="grid grid-cols-1 md:grid-cols-2
                                           gap-4 pt-2">

                                    @if ($report->before_photo)
                                        <div>

                                            <p class="text-xs font-semibold text-gray-500 mb-2">
                                                Before Photo
                                            </p>

                                            <img src="{{ asset('storage/' . $report->before_photo) }}"
                                                alt="Before maintenance"
                                                class="w-full h-56 object-cover
                                                       rounded-xl border border-gray-200">

                                        </div>
                                    @endif


                                    @if ($report->after_photo)
                                        <div>

                                            <p class="text-xs font-semibold text-gray-500 mb-2">
                                                After Photo
                                            </p>

                                            <img src="{{ asset('storage/' . $report->after_photo) }}"
                                                alt="After maintenance"
                                                class="w-full h-56 object-cover
                                                       rounded-xl border border-gray-200">

                                        </div>
                                    @endif

                                </div>

                            @endif

                        </div>

                    </div>

                @endif

            </div>


            <div class="space-y-6">

                <div
                    class="bg-white rounded-2xl
                           border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-indigo-100 bg-indigo-50/40">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-11 h-11 rounded-xl
                   bg-indigo-100 text-indigo-700
                   flex items-center justify-center shrink-0">
                                <i class="fas fa-timeline"></i>
                            </div>

                            <div>

                                <h2 class="font-semibold text-gray-900">
                                    Complaint Tracking
                                </h2>

                                <p class="text-xs text-gray-500 mt-1">
                                    Complete maintenance status and activity history.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5">

                        <div class="relative">

                            <div
                                class="absolute left-[15px] top-4 bottom-4
                                       w-px bg-gray-200">
                            </div>


                            <div class="space-y-6">

                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               bg-green-100 text-green-600
                                               flex items-center justify-center shrink-0">
                                        <i class="fas fa-check text-xs"></i>
                                    </div>

                                    <div class="pt-0.5">

                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $isForwardedRequest ? 'Request Submitted' : 'Complaint Submitted' }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $complaint->created_at?->format('M d, Y • h:i A') }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            Submitted by
                                            {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Consumer') }}
                                        </p>

                                    </div>

                                </div>


                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               {{ $complaint->verified_at ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                               flex items-center justify-center shrink-0">
                                        <i class="fas fa-check text-xs"></i>
                                    </div>

                                    <div class="pt-0.5">

                                        <p class="text-sm font-semibold text-gray-900">
                                            Verified
                                        </p>

                                        @if ($complaint->verified_at)

                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $complaint->verified_at->format('M d, Y • h:i A') }}
                                            </p>

                                            @if ($complaint->verifier)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Verified by
                                                    {{ $complaint->verifier->full_name }}
                                                </p>
                                            @endif
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Pending
                                            </p>

                                        @endif

                                    </div>

                                </div>


                                @if ($hasCsProcessing)

                                    <div class="relative flex gap-4">

                                        <div
                                            class="relative z-10 w-8 h-8 rounded-full
                                                   {{ $initialProcessingStarted ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                                   flex items-center justify-center shrink-0">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>

                                        <div class="pt-0.5">

                                            <p class="text-sm font-semibold text-gray-900">
                                                Initial Processing Started
                                            </p>

                                            @if ($initialProcessingStarted)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $initialProcessingStarted->format('M d, Y • h:i A') }}
                                                </p>

                                                <p class="text-xs text-gray-500 mt-1">
                                                    Started by
                                                    {{ $processor?->full_name ?? 'Customer Service' }}
                                                </p>
                                            @endif

                                        </div>

                                    </div>


                                    <div class="relative flex gap-4">

                                        <div
                                            class="relative z-10 w-8 h-8 rounded-full
                                                   {{ $initialProcessingCompleted ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                                   flex items-center justify-center shrink-0">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>

                                        <div class="pt-0.5">

                                            <p class="text-sm font-semibold text-gray-900">
                                                Initial Processing Completed
                                            </p>

                                            @if ($initialProcessingCompleted)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $initialProcessingCompleted->format('M d, Y • h:i A') }}
                                                </p>

                                                <p class="text-xs text-gray-500 mt-1">
                                                    Processed by
                                                    {{ $processor?->full_name ?? 'Customer Service' }}
                                                </p>
                                            @else
                                                <p class="text-xs text-gray-400 mt-1">
                                                    Pending
                                                </p>
                                            @endif

                                        </div>

                                    </div>


                                    <div class="relative flex gap-4">

                                        <div
                                            class="relative z-10 w-8 h-8 rounded-full
                                                   {{ $forwardedAt ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                                   flex items-center justify-center shrink-0">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>

                                        <div class="pt-0.5">

                                            <p class="text-sm font-semibold text-gray-900">
                                                Forwarded to Maintenance
                                            </p>

                                            @if ($forwardedAt)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $forwardedAt->format('M d, Y • h:i A') }}
                                                </p>

                                                <p class="text-xs text-gray-500 mt-1">
                                                    Forwarded by
                                                    {{ $forwarder?->full_name ?? 'Customer Service' }}
                                                </p>
                                            @else
                                                <p class="text-xs text-gray-400 mt-1">
                                                    Pending
                                                </p>
                                            @endif

                                        </div>

                                    </div>

                                @endif


                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               {{ $complaint->technicians->count() ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                               flex items-center justify-center shrink-0">
                                        <i class="fas fa-check text-xs"></i>
                                    </div>

                                    <div class="pt-0.5">

                                        <p class="text-sm font-semibold text-gray-900">
                                            Plumber Assigned
                                        </p>

                                        @if ($complaint->technicians->count())

                                            @if ($assignmentDate)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ \Carbon\Carbon::parse($assignmentDate)->format('M d, Y • h:i A') }}
                                                </p>
                                            @endif

                                            <p class="text-xs text-gray-500 mt-1">
                                                Assigned to {{ $assignedNames }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Pending
                                            </p>

                                        @endif

                                    </div>

                                </div>


                                <div class="relative flex gap-4">

                                    @php
                                        $maintenanceStarted =
                                            !is_null($startedDate) ||
                                            in_array($complaint->status, ['In Progress', 'Completed', 'Closed'], true);
                                    @endphp

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               {{ $maintenanceStarted ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                               flex items-center justify-center shrink-0">
                                        @if ($complaint->status === 'In Progress')
                                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                        @else
                                            <i class="fas fa-check text-xs"></i>
                                        @endif
                                    </div>

                                    <div class="pt-0.5">

                                        <p class="text-sm font-semibold text-gray-900">
                                            Maintenance Started
                                        </p>

                                        @if ($startedDate)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ \Carbon\Carbon::parse($startedDate)->format('M d, Y • h:i A') }}
                                            </p>

                                            <p class="text-xs text-gray-500 mt-1">
                                                Handled by {{ $assignedNames }}
                                            </p>
                                        @elseif ($complaint->status === 'Assigned')
                                            <p class="text-xs text-gray-400 mt-1">
                                                Waiting to start
                                            </p>
                                        @endif

                                    </div>

                                </div>


                                <div class="relative flex gap-4">

                                    @php
                                        $maintenanceAccomplished =
                                            $reportSubmitted ||
                                            in_array($complaint->status, ['Completed', 'Closed'], true);
                                    @endphp

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               {{ $maintenanceAccomplished ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                               flex items-center justify-center shrink-0">
                                        @if ($complaint->status === 'Completed' && $report?->review_status === 'Pending Review')
                                            <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                        @else
                                            <i class="fas fa-check text-xs"></i>
                                        @endif
                                    </div>

                                    <div class="pt-0.5">

                                        <p class="text-sm font-semibold text-gray-900">
                                            Maintenance Accomplished
                                        </p>

                                        @if ($report?->submitted_at)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $report->submitted_at->format('M d, Y • h:i A') }}
                                            </p>

                                            <p class="text-xs text-gray-500 mt-1">
                                                Accomplished by {{ $assignedNames }}
                                            </p>
                                        @elseif ($completedDate)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{($completedDate)->format('M d, Y • h:i A') }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Pending
                                            </p>
                                        @endif

                                    </div>

                                </div>


                                <div class="relative flex gap-4">

                                    @php
                                        $reviewCompleted = $report?->review_status === 'Approved';

                                        $reviewReturned = $report?->review_status === 'Returned';

                                        $reviewPending = $report?->review_status === 'Pending Review';
                                    @endphp

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               @if ($reviewCompleted) bg-green-100 text-green-600
                                               @elseif ($reviewReturned)
                                                   bg-red-100 text-red-600
                                               @elseif ($reviewPending)
                                                   bg-violet-100 text-violet-600
                                               @else
                                                   bg-gray-100 text-gray-400 @endif
                                               flex items-center justify-center shrink-0">

                                        @if ($reviewCompleted)
                                            <i class="fas fa-check text-xs"></i>
                                        @elseif ($reviewReturned)
                                            <i class="fas fa-rotate-left text-xs"></i>
                                        @elseif ($reviewPending)
                                            <span class="w-2.5 h-2.5 rounded-full bg-violet-500"></span>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                        @endif

                                    </div>

                                    <div class="pt-0.5">

                                        <p class="text-sm font-semibold text-gray-900">
                                            Manager Review
                                        </p>

                                        @if ($reviewCompleted)

                                            <p class="text-xs text-green-600 mt-1">
                                                Approved
                                            </p>

                                            @if ($report?->reviewed_at)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $report->reviewed_at->format('M d, Y • h:i A') }}
                                                </p>
                                            @endif
                                        @elseif ($reviewReturned)
                                            <p class="text-xs text-red-600 mt-1">
                                                Returned for correction
                                            </p>
                                        @elseif ($reviewPending)
                                            <p class="text-xs text-violet-600 mt-1">
                                                Pending review
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Pending
                                            </p>

                                        @endif

                                    </div>

                                </div>


                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               {{ $complaint->status === 'Closed' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                               flex items-center justify-center shrink-0">
                                        @if ($complaint->status === 'Closed')
                                            <i class="fas fa-check text-xs"></i>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                        @endif
                                    </div>

                                    <div class="pt-0.5">

                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $isForwardedRequest ? 'Request Closed' : 'Complaint Closed' }}
                                        </p>

                                        @if ($complaint->status === 'Closed')
                                            <p class="text-xs text-green-600 mt-1">
                                                Finalized by Maintenance Management
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Pending
                                            </p>
                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <div
                    class="bg-white rounded-2xl
                           border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-blue-100 bg-blue-50/40">

                        <div class="flex items-center justify-between gap-3">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-11 h-11 rounded-xl
                       bg-blue-100 text-blue-700
                       flex items-center justify-center shrink-0">
                                    <i class="fas fa-users-gear"></i>
                                </div>

                                <div>

                                    <h2 class="font-semibold text-gray-900">
                                        Maintenance Team
                                    </h2>

                                    <p class="text-xs text-gray-500 mt-1">
                                        Plumbers assigned to work on this complaint.
                                    </p>

                                </div>

                            </div>

                            <span
                                class="inline-flex items-center justify-center
                   min-w-9 h-9 px-2.5 rounded-full
                   bg-blue-100 text-blue-700
                   text-sm font-bold">
                                {{ $complaint->technicians->count() }}
                            </span>

                        </div>

                    </div>


                    <div class="p-5">

                        <div class="space-y-3">

                            @foreach ($complaint->technicians as $technician)
                                <div
                                    class="flex items-center gap-3 p-3 rounded-xl
                                           {{ $technician->id === auth()->id() ? 'bg-sky-50 border border-sky-200' : 'bg-gray-50 border border-gray-100' }}">

                                    <div
                                        class="w-10 h-10 rounded-full
                                               bg-sky-100 text-sky-700
                                               flex items-center justify-center
                                               font-bold shrink-0">
                                        {{ strtoupper(substr($technician->first_name ?? 'P', 0, 1)) }}
                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <div class="flex flex-wrap items-center gap-2">

                                            <p class="font-semibold text-gray-900 truncate">
                                                {{ $technician->full_name }}
                                            </p>

                                            @if ($technician->id === auth()->id())
                                                <span
                                                    class="px-2 py-0.5 rounded-full
                                                           bg-sky-600 text-white
                                                           text-[10px] font-bold">
                                                    YOU
                                                </span>
                                            @endif

                                        </div>


                                        <p class="text-xs text-gray-500 mt-0.5">
                                            Maintenance Plumber
                                        </p>


                                        @if ($technician->pivot?->status)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $technician->pivot->status }}
                                            </p>
                                        @endif

                                    </div>

                                </div>
                            @endforeach

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    @if ($hasCoordinates)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const latitude = {{ (float) $serviceLatitude }};
                const longitude = {{ (float) $serviceLongitude }};

                const mapElement =
                    document.getElementById('complaint-map');

                if (!mapElement) {
                    return;
                }

                const map = L.map(
                    'complaint-map', {
                        scrollWheelZoom: false
                    }
                ).setView(
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
                        <strong>
                            {{ addslashes($complaint->complaint_no) }}
                        </strong>

                        <br>

                        <span>
                            {{ addslashes($complaint->category?->name ?? 'Service Complaint') }}
                        </span>

                        <br><br>

                        <span style="font-size:12px;color:#6b7280">
                            {{ addslashes($serviceAddress ?? '') }}
                        </span>
                    </div>
                `);

                setTimeout(function() {
                    map.invalidateSize();
                }, 300);

            });
        </script>
    @endif

@endsection
