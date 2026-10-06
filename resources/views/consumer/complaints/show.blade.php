@extends('consumer.layouts.app')

@section('title', 'Complaint Details')

@section('content')

    @php
        $divisionName = strtolower((string) optional($complaint->division)->name);

        $isEngineering = str_contains($divisionName, 'engineering');
        $isCommercial = str_contains($divisionName, 'commercial');

        $commercialResolution = $complaint->commercialResolution;
        $report = $complaint->maintenanceReport;

        $statusLabel = match ($complaint->status) {
            'Pending' => 'Submitted',
            'Verified' => 'Verified',
            'CS Processing' => 'Under Initial Processing',
            'For Maintenance' => 'Forwarded to Maintenance',
            'Assigned' => 'Plumber Assigned',
            'In Progress' => 'Maintenance In Progress',
            'Completed' => 'Maintenance Completed',
            'Closed' => $isCommercial ? 'Request Closed' : 'Complaint Closed',
            'Rejected' => 'Rejected',
            default => $complaint->status,
        };

        $statusClasses = match ($complaint->status) {
            'Pending' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Verified' => 'bg-sky-50 text-sky-700 border-sky-200',
            'CS Processing' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
            'For Maintenance' => 'bg-violet-50 text-violet-700 border-violet-200',
            'Assigned' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'In Progress' => 'bg-blue-50 text-blue-700 border-blue-200',
            'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Closed' => 'bg-slate-100 text-slate-700 border-slate-200',
            'Rejected' => 'bg-red-50 text-red-700 border-red-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };

        $statusIcon = match ($complaint->status) {
            'Pending' => 'fa-clock',
            'Verified' => 'fa-circle-check',
            'CS Processing' => 'fa-clipboard-list',
            'For Maintenance' => 'fa-share',
            'Assigned' => 'fa-user-group',
            'In Progress' => 'fa-screwdriver-wrench',
            'Completed' => 'fa-clipboard-check',
            'Closed' => 'fa-lock',
            'Rejected' => 'fa-circle-xmark',
            default => 'fa-circle-info',
        };

        $firstAssignedAt = $complaint->technicians->pluck('pivot.assigned_at')->filter()->sort()->first();

        $firstStartedAt = $complaint->technicians->pluck('pivot.started_at')->filter()->sort()->first();

        $firstCompletedAt = $complaint->technicians->pluck('pivot.completed_at')->filter()->sort()->first();

        $assignedNames = $complaint->technicians->pluck('full_name')->filter()->values();

        $isClosedWithoutMaintenance =
            $isCommercial && $complaint->status === 'Closed' && !$commercialResolution?->forwarded_to_maintenance_at;

        $hasMaintenanceStage =
            $complaint->status === 'For Maintenance' ||
            $complaint->technicians->isNotEmpty() ||
            $report ||
            $commercialResolution?->forwarded_to_maintenance_at;
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

        <div class="mb-6">

            <a href="{{ route('consumer.complaints.index') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-sky-700">

                <i class="fas fa-arrow-left text-xs"></i>

                Back to Complaints

            </a>

        </div>

        <div class="mb-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

            <div class="p-6 md:p-8">

                <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                    <div class="flex items-start gap-4">

                        <div
                            class="hidden sm:flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-xl text-sky-700">

                            <i class="fas fa-file-lines"></i>

                        </div>

                        <div>

                            <div class="flex flex-wrap items-center gap-2">

                                <p class="text-xs font-bold uppercase tracking-wider text-sky-600">
                                    @if ($isCommercial)
                                        Commercial Services Complaint
                                    @else
                                        Water Service Complaint
                                    @endif
                                </p>

                                <span class="text-slate-300">•</span>

                                <p class="text-xs font-medium text-slate-400">
                                    {{ $complaint->complaint_no }}
                                </p>

                            </div>

                            <h1 class="mt-2 text-2xl font-bold text-slate-900 md:text-3xl">
                                {{ optional($complaint->category)->name ?? 'Water Service Concern' }}
                            </h1>

                            <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-500">

                                <span class="inline-flex items-center gap-2">
                                    <i class="far fa-calendar text-slate-400"></i>
                                    Submitted
                                    {{ $complaint->created_at->timezone('Asia/Manila')->format('M d, Y') }}
                                </span>

                                <span class="inline-flex items-center gap-2">
                                    <i class="far fa-clock text-slate-400"></i>
                                    {{ $complaint->created_at->timezone('Asia/Manila')->format('g:i A') }}
                                </span>

                            </div>

                        </div>

                    </div>

                    <div class="flex flex-wrap items-center gap-3">

                        <span
                            class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold {{ $statusClasses }}">

                            <i class="fas {{ $statusIcon }}"></i>

                            {{ $statusLabel }}

                        </span>

                        @if ($complaint->status === 'Pending')
                            <a href="{{ route('consumer.complaints.edit', $complaint) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800">

                                <i class="fas fa-pen"></i>

                                Edit Complaint

                            </a>
                        @endif

                    </div>

                </div>

            </div>

        </div>

        @if ($complaint->status === 'Rejected')

            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5">

                <div class="flex items-start gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-red-600">

                        <i class="fas fa-circle-xmark"></i>

                    </div>

                    <div>

                        <h2 class="font-bold text-red-900">
                            Complaint Not Accepted
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-red-700">
                            Customer Service was unable to proceed with this complaint.
                        </p>

                        @if ($complaint->verification_reason)
                            <div class="mt-3 rounded-xl border border-red-100 bg-white/70 p-3">

                                <p class="text-xs font-semibold uppercase tracking-wide text-red-500">
                                    Reason
                                </p>

                                <p class="mt-1 text-sm leading-6 text-red-800">
                                    {{ $complaint->verification_reason }}
                                </p>

                            </div>
                        @endif

                    </div>

                </div>

            </div>

        @endif


        @if (in_array($complaint->status, ['Completed', 'Closed'], true))

            @if (!$complaint->feedback)

                <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                    <div class="p-6 md:p-7">

                        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                            <div class="flex items-start gap-4">

                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-500">

                                    <i class="fas fa-star"></i>

                                </div>

                                <div>

                                    <h2 class="font-bold text-slate-900">
                                        How was your service experience?
                                    </h2>

                                    <p class="mt-1 max-w-xl text-sm leading-6 text-slate-500">
                                        Your complaint has been completed. Share your
                                        experience to help Sagay Water District improve its
                                        services.
                                    </p>

                                </div>

                            </div>

                            <a href="{{ route('consumer.complaints.feedback.create', $complaint) }}"
                                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800">

                                <i class="fas fa-star"></i>

                                Give Feedback

                            </a>

                        </div>

                    </div>

                </section>
            @else
                @php
                    $feedback = $complaint->feedback;
                @endphp

                <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-6 py-5 md:px-7">

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                    <i class="fas fa-star"></i>

                                </div>

                                <div>

                                    <h2 class="font-bold text-slate-900">
                                        Your Service Feedback
                                    </h2>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Submitted
                                        {{ $feedback->created_at->format('M d, Y \a\t h:i A') }}
                                    </p>

                                </div>

                            </div>

                            <span
                                class="inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">

                                <i class="fas fa-circle-check text-[10px]"></i>

                                Feedback Submitted

                            </span>

                        </div>

                    </div>

                    <div class="p-6 md:p-7">

                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Overall
                                </p>

                                <div class="mt-3 flex items-center gap-1">

                                    @for ($i = 1; $i <= 5; $i++)
                                        <i
                                            class="fas fa-star text-sm
                                    {{ $i <= $feedback->overall_rating ? 'text-amber-400' : 'text-slate-200' }}">
                                        </i>
                                    @endfor

                                </div>

                                <p class="mt-2 text-sm font-bold text-slate-800">
                                    {{ $feedback->overall_rating }}/5
                                </p>

                            </div>

                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Service Quality
                                </p>

                                <div class="mt-3 flex items-center gap-1">

                                    @for ($i = 1; $i <= 5; $i++)
                                        <i
                                            class="fas fa-star text-sm
                                    {{ $i <= $feedback->service_quality_rating ? 'text-amber-400' : 'text-slate-200' }}">
                                        </i>
                                    @endfor

                                </div>

                                <p class="mt-2 text-sm font-bold text-slate-800">
                                    {{ $feedback->service_quality_rating }}/5
                                </p>

                            </div>

                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Response Time
                                </p>

                                <div class="mt-3 flex items-center gap-1">

                                    @for ($i = 1; $i <= 5; $i++)
                                        <i
                                            class="fas fa-star text-sm
                                    {{ $i <= $feedback->response_time_rating ? 'text-amber-400' : 'text-slate-200' }}">
                                        </i>
                                    @endfor

                                </div>

                                <p class="mt-2 text-sm font-bold text-slate-800">
                                    {{ $feedback->response_time_rating }}/5
                                </p>

                            </div>

                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Personnel Courtesy
                                </p>

                                <div class="mt-3 flex items-center gap-1">

                                    @for ($i = 1; $i <= 5; $i++)
                                        <i
                                            class="fas fa-star text-sm
                                    {{ $i <= $feedback->personnel_courtesy_rating ? 'text-amber-400' : 'text-slate-200' }}">
                                        </i>
                                    @endfor

                                </div>

                                <p class="mt-2 text-sm font-bold text-slate-800">
                                    {{ $feedback->personnel_courtesy_rating }}/5
                                </p>

                            </div>

                        </div>

                        <div class="mt-5 rounded-2xl border border-slate-100 bg-slate-50 p-5">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                <div>

                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Concern Resolution
                                    </p>

                                    <div class="mt-2">

                                        @if ($feedback->resolution_status === 'Resolved')
                                            <span
                                                class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700">

                                                <i class="fas fa-circle-check"></i>

                                                Resolved

                                            </span>
                                        @elseif ($feedback->resolution_status === 'Partially Resolved')
                                            <span
                                                class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-700">

                                                <i class="fas fa-circle-half-stroke"></i>

                                                Partially Resolved

                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-2 rounded-full bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700">

                                                <i class="fas fa-circle-xmark"></i>

                                                Not Resolved

                                            </span>
                                        @endif

                                    </div>

                                </div>

                                <div class="sm:text-right">

                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Average Rating
                                    </p>

                                    <p class="mt-1 text-xl font-bold text-slate-900">
                                        {{ number_format($feedback->average_rating, 1) }}
                                        <span class="text-sm font-medium text-slate-400">
                                            / 5
                                        </span>
                                    </p>

                                </div>

                            </div>

                        </div>

                        @if ($feedback->comments)
                            <div class="mt-5 rounded-2xl border border-slate-200 p-5">

                                <div class="flex items-center gap-2">

                                    <i class="fas fa-comment-dots text-sm text-sky-600"></i>

                                    <h3 class="text-sm font-semibold text-slate-800">
                                        Your Comments
                                    </h3>

                                </div>

                                <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">
                                    {{ $feedback->comments }}</p>

                            </div>
                        @endif

                        <div class="mt-5 flex items-start gap-3 rounded-2xl bg-emerald-50 p-4">

                            <div
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-emerald-600">

                                <i class="fas fa-heart text-xs"></i>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-emerald-900">
                                    Thank you for your feedback
                                </p>

                                <p class="mt-1 text-xs leading-5 text-emerald-700">
                                    Your response has been recorded and can help Sagay
                                    Water District improve its consumer services.
                                </p>

                            </div>

                        </div>

                    </div>

                </section>

            @endif

        @endif



        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            <div class="space-y-6 lg:col-span-2">

                <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-6 py-5 md:px-7">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-700">

                                <i class="fas fa-file-lines"></i>

                            </div>

                            <div>

                                <h2 class="font-bold text-slate-900">
                                    Complaint Details
                                </h2>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    @if ($isCommercial)
                                        Information submitted for this Commercial Services concern.
                                    @else
                                        Information submitted for this water service concern.
                                    @endif
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="p-6 md:p-7">

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                            <div class="rounded-2xl bg-slate-50 p-4">

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Division
                                </p>

                                <p class="mt-2 text-sm font-semibold text-slate-800">
                                    {{ optional($complaint->division)->name ?? 'Not specified' }}
                                </p>

                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4">

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Complaint Type
                                </p>

                                <p class="mt-2 text-sm font-semibold text-slate-800">
                                    {{ optional($complaint->category)->name ?? 'Water Service Concern' }}
                                </p>

                            </div>

                        </div>

                        <div class="mt-6">

                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Description
                            </p>

                            <div class="mt-2 rounded-2xl border border-slate-100 bg-slate-50 p-5">

                                <p class="whitespace-pre-line text-sm leading-7 text-slate-700">
                                    {{ $complaint->description }}</p>

                            </div>

                        </div>

                    </div>

                </section>

                @if ($isCommercial)
                    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                        <div class="border-b border-slate-100 px-6 py-5 md:px-7">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-700">

                                    <i class="fas fa-user-check"></i>

                                </div>

                                <div>

                                    <h2 class="font-bold text-slate-900">
                                        Consumer Account
                                    </h2>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        SWD account associated with this complaint.
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="p-6 md:p-7">

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                                <div class="rounded-2xl bg-slate-50 p-4">

                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Account Number
                                    </p>

                                    <p class="mt-2 text-sm font-semibold text-slate-800">
                                        {{ $complaint->consumer?->account_number ?? 'Not available' }}
                                    </p>

                                </div>

                                <div class="rounded-2xl bg-slate-50 p-4">

                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Account Holder
                                    </p>

                                    <p class="mt-2 text-sm font-semibold text-slate-800">
                                        {{ $complaint->consumer?->full_name ?? $complaint->complainant_name }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    </section>
                @endif
                @if ($isCommercial && $commercialResolution?->initial_processing_completed_at)
                    @php
                        $commercialResolution = $complaint->commercialResolution;
                    @endphp

                    <section class="overflow-hidden rounded-3xl border border-emerald-200 bg-white shadow-sm">

                        <div class="border-b border-emerald-100 bg-emerald-50/60 px-6 py-5 md:px-7">

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">

                                    <i class="fas fa-circle-check"></i>

                                </div>

                                <div>

                                    <h2 class="font-bold text-slate-900">
                                        Commercial Services Resolution
                                    </h2>

                                    <p class="mt-0.5 text-xs leading-5 text-slate-500">
                                        Review the findings and resolution provided by Sagay Water District
                                        for your concern.
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="p-6 md:p-7">

                            <div>

                                <div class="flex items-center gap-2">

                                    <i class="fas fa-magnifying-glass text-sm text-sky-600"></i>

                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Findings
                                    </p>

                                </div>

                                <div class="mt-3 rounded-2xl border border-slate-100 bg-slate-50 p-5">

                                    <p class="whitespace-pre-line text-sm leading-7 text-slate-700">
                                        {{ $commercialResolution->findings ?: 'No findings were recorded.' }}
                                    </p>

                                </div>

                            </div>

                            <div class="mt-6">

                                <div class="flex items-center gap-2">

                                    <i class="fas fa-clipboard-check text-sm text-emerald-600"></i>

                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Resolution
                                    </p>

                                </div>

                                <div class="mt-3 rounded-2xl border border-emerald-100 bg-emerald-50/50 p-5">

                                    <p class="whitespace-pre-line text-sm leading-7 text-slate-700">
                                        {{ $commercialResolution->resolution_remarks ?: 'No resolution was recorded.' }}
                                    </p>

                                </div>

                            </div>

                            @if ($commercialResolution->completed_at)
                                <div class="mt-6 border-t border-slate-100 pt-5">

                                    <div class="flex items-center gap-2 text-xs text-slate-500">

                                        <i class="far fa-clock text-slate-400"></i>

                                        <span>
                                            Resolution completed
                                            {{ $commercialResolution->completed_at->timezone('Asia/Manila')->format('M d, Y \a\t g:i A') }}
                                        </span>

                                    </div>

                                </div>
                            @endif

                        </div>

                    </section>
                @endif

                @if ($isEngineering)

                    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                        <div
                            class="flex flex-col gap-3 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between md:px-7">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">

                                    <i class="fas fa-location-dot"></i>

                                </div>

                                <div>

                                    <h2 class="font-bold text-slate-900">
                                        Service Location
                                    </h2>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Location where the reported concern occurred.
                                    </p>

                                </div>

                            </div>

                            @if ($complaint->latitude !== null && $complaint->longitude !== null)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $complaint->latitude }},{{ $complaint->longitude }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 text-sm font-semibold text-sky-700 transition hover:text-sky-900">

                                    <i class="fas fa-arrow-up-right-from-square text-xs"></i>

                                    Open in Maps

                                </a>
                            @endif

                        </div>

                        <div class="p-6 md:p-7">

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                                <div class="{{ $complaint->landmark ? '' : 'sm:col-span-2' }}">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500">

                                            <i class="fas fa-location-dot"></i>

                                        </div>

                                        <div>

                                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                Address
                                            </p>

                                            <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">
                                                {{ $complaint->address }}</p>

                                        </div>

                                    </div>

                                </div>

                                @if ($complaint->landmark)
                                    <div>

                                        <div class="flex items-start gap-3">

                                            <div
                                                class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500">

                                                <i class="fas fa-signs-post"></i>

                                            </div>

                                            <div>

                                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                                    Nearby Landmark
                                                </p>

                                                <p class="mt-1 text-sm leading-6 text-slate-700">
                                                    {{ $complaint->landmark }}
                                                </p>

                                            </div>

                                        </div>

                                    </div>
                                @endif

                            </div>

                            @if ($complaint->latitude !== null && $complaint->longitude !== null)
                                <div class="mt-6">

                                    <div id="complaintMap"
                                        class="h-80 w-full overflow-hidden rounded-2xl border border-slate-200 bg-slate-100">
                                    </div>

                                    <p class="mt-3 flex items-center gap-2 text-xs text-slate-500">

                                        <i class="fas fa-circle-info text-sky-500"></i>

                                        The pin shows the location provided with your complaint.

                                    </p>

                                </div>
                            @else
                                <div
                                    class="mt-6 flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">

                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-slate-400">

                                        <i class="fas fa-map-location-dot"></i>

                                    </div>

                                    <div>

                                        <p class="text-sm font-semibold text-slate-700">
                                            No map location provided
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            The address above will be used as the service location.
                                        </p>

                                    </div>

                                </div>
                            @endif

                        </div>

                    </section>
                @endif

                @if ($isEngineering && $complaint->technicians->count())

                    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                        <div class="border-b border-slate-100 px-6 py-5 md:px-7">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">

                                    <i class="fas fa-user-group"></i>

                                </div>

                                <div>

                                    <h2 class="font-bold text-slate-900">
                                        Assigned Plumber
                                    </h2>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Plumbers assigned to handle your water service concern.
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="p-6 md:p-7">

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                                @foreach ($complaint->technicians as $technician)
                                    @php
                                        $initials = strtoupper(
                                            substr($technician->first_name ?? '', 0, 1) .
                                                substr($technician->last_name ?? '', 0, 1),
                                        );

                                        $assignmentStatus = $technician->pivot?->status ?? 'Assigned';

                                        $assignmentClass = match ($assignmentStatus) {
                                            'In Progress' => 'bg-blue-50 text-blue-700',
                                            'Accomplished' => 'bg-violet-50 text-violet-700',
                                            'Completed' => 'bg-emerald-50 text-emerald-700',
                                            default => 'bg-indigo-50 text-indigo-700',
                                        };
                                    @endphp

                                    <div class="flex items-center gap-4 rounded-2xl border border-slate-200 p-4">

                                        <div
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sm font-bold text-sky-700">

                                            {{ $initials ?: 'ST' }}

                                        </div>

                                        <div class="min-w-0 flex-1">

                                            <p class="truncate text-sm font-semibold text-slate-800">
                                                {{ $technician->full_name }}
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-500">
                                                Service Personnel
                                            </p>

                                        </div>

                                        <span
                                            class="hidden rounded-lg px-2.5 py-1 text-[11px] font-semibold sm:inline-flex {{ $assignmentClass }}">

                                            {{ $assignmentStatus }}

                                        </span>

                                    </div>
                                @endforeach

                            </div>

                        </div>

                    </section>

                @endif

                @if ($complaint->photo)
                    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                        <div class="border-b border-slate-100 px-6 py-5 md:px-7">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-700">

                                    <i class="fas fa-image"></i>

                                </div>

                                <div>

                                    <h2 class="font-bold text-slate-900">
                                        {{ $isCommercial ? 'Supporting Evidence' : 'Supporting Photo' }}
                                    </h2>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        @if ($isCommercial)
                                            Supporting image submitted with this Commercial Services complaint.
                                        @else
                                            Photo submitted with this complaint.
                                        @endif
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="p-6 md:p-7">

                            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">

                                <img src="{{ asset('storage/' . $complaint->photo) }}"
                                    alt="Supporting photo for complaint {{ $complaint->complaint_no }}"
                                    class="max-h-[520px] w-full object-contain">

                            </div>

                        </div>

                    </section>
                @endif

                <section class="rounded-3xl border border-sky-100 bg-gradient-to-br from-sky-50 to-white p-6 md:p-7">

                    <div class="flex items-start gap-4">

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-600 text-white shadow-sm">

                            <i class="fas fa-robot"></i>

                        </div>

                        <div>

                            <h2 class="font-bold text-sky-950">
                                AI-Assisted Complaint Support
                            </h2>

                            <p class="mt-2 text-sm leading-6 text-sky-800">
                                iSWD may assist personnel in understanding,
                                classifying, and assessing submitted concerns.
                                Final verification, service decisions, assignments,
                                and complaint actions are handled by authorized
                                Sagay Water District personnel.
                            </p>

                        </div>

                    </div>

                </section>

            </div>

            <div class="space-y-6">
<section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

    <div class="border-b border-slate-100 px-6 py-5">

        <div class="flex items-center gap-3">

            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                <i class="fas fa-route"></i>
            </div>

            <div>
                <h2 class="font-bold text-slate-900">
                    Complaint Tracking
                </h2>

                <p class="mt-0.5 text-xs text-slate-500">
                    Follow the progress of your concern.
                </p>
            </div>

        </div>

    </div>


    <div class="p-6">

        <div class="max-w-xl">

            {{-- Submitted --}}
            <div class="flex gap-4">

                <div class="flex flex-col items-center">

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center
                               rounded-full bg-sky-600 text-white">

                        <i class="fas fa-check text-xs"></i>

                    </div>

                    <div class="min-h-12 w-px flex-1 bg-sky-300"></div>

                </div>


                <div class="pb-6">

                    <p class="text-sm font-semibold text-slate-800">
                        Submitted
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        Your concern was submitted to Sagay Water District.
                    </p>

                    <p class="mt-1.5 text-[11px] font-medium text-slate-400">

                        <i class="far fa-clock mr-1"></i>

                        {{ $complaint->created_at
                            ->timezone('Asia/Manila')
                            ->format('M d, Y · g:i A') }}

                    </p>

                </div>

            </div>


            {{-- Rejected --}}
            @if ($complaint->status === 'Rejected')

                <div class="flex gap-4">

                    <div class="flex flex-col items-center">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center
                                   rounded-full bg-red-600 text-white">

                            <i class="fas fa-xmark text-xs"></i>

                        </div>

                    </div>


                    <div>

                        <p class="text-sm font-semibold text-red-700">
                            Complaint Rejected
                        </p>

                        @if ($complaint->verifier)

                            <p class="mt-1 text-xs text-slate-500">
                                Reviewed by

                                <span class="font-semibold text-slate-700">
                                    {{ $complaint->verifier->full_name }}
                                </span>
                            </p>

                        @else

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Customer Service reviewed the concern and was unable to proceed with it.
                            </p>

                        @endif


                        @if ($complaint->verification_reason)

                            <p class="mt-2 text-xs text-red-600">
                                {{ $complaint->verification_reason }}
                            </p>

                        @endif

                    </div>

                </div>

            @else

                {{-- Verified --}}
                @php
                    $verified = (bool) $complaint->verified_at;
                @endphp


                <div class="flex gap-4">

                    <div class="flex flex-col items-center">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                            {{ $verified
                                ? 'bg-sky-600 text-white'
                                : 'border border-slate-200 bg-white text-slate-400' }}">

                            <i class="fas {{ $verified ? 'fa-check' : 'fa-circle' }} text-xs"></i>

                        </div>


                        <div
                            class="min-h-12 w-px flex-1
                            {{ $verified ? 'bg-sky-300' : 'bg-slate-200' }}">
                        </div>

                    </div>


                    <div class="pb-6">

                        <p
                            class="text-sm font-semibold
                            {{ $verified ? 'text-slate-800' : 'text-slate-400' }}">

                            Verified

                        </p>


                        @if ($verified)

                            <p class="mt-1 text-xs text-slate-500">
                                Reviewed by

                                <span class="font-semibold text-slate-700">
                                    {{ $complaint->verifier?->full_name ?? 'Customer Service' }}
                                </span>
                            </p>


                            <p class="mt-1.5 text-[11px] font-medium text-slate-400">

                                <i class="far fa-clock mr-1"></i>

                                {{ $complaint->verified_at
                                    ->timezone('Asia/Manila')
                                    ->format('M d, Y · g:i A') }}

                            </p>

                        @else

                            <p class="mt-1 text-xs text-slate-500">
                                Customer Service reviewed and verified your concern.
                            </p>

                        @endif

                    </div>

                </div>



                {{-- ========================================================= --}}
                {{-- COMMERCIAL SERVICES WORKFLOW --}}
                {{-- ========================================================= --}}

                @if ($isCommercial)

                    @php
                        $processingStarted =
                            (bool) $commercialResolution?->started_at;

                        $processingCompleted =
                            (bool) $commercialResolution?->initial_processing_completed_at;
                    @endphp


                    {{-- Under Initial Processing --}}
                    <div class="flex gap-4">

                        <div class="flex flex-col items-center">

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                {{ $processingStarted
                                    ? 'bg-sky-600 text-white'
                                    : 'border border-slate-200 bg-white text-slate-400' }}">

                                <i
                                    class="fas
                                    {{ $processingStarted ? 'fa-check' : 'fa-clipboard-list' }}
                                    text-xs">
                                </i>

                            </div>


                            <div
                                class="min-h-12 w-px flex-1
                                {{ $processingStarted ? 'bg-sky-300' : 'bg-slate-200' }}">
                            </div>

                        </div>


                        <div class="pb-6">

                            <p
                                class="text-sm font-semibold
                                {{ $processingStarted ? 'text-slate-800' : 'text-slate-400' }}">

                                Under Initial Processing

                            </p>


                            @if ($processingStarted)

                                <p class="mt-1 text-xs text-slate-500">
                                    Started by

                                    <span class="font-semibold text-slate-700">
                                        {{ $commercialResolution?->processor?->full_name ?? 'Customer Service' }}
                                    </span>
                                </p>


                                <p class="mt-1.5 text-[11px] font-medium text-slate-400">

                                    <i class="far fa-clock mr-1"></i>

                                    {{ $commercialResolution->started_at
                                        ->timezone('Asia/Manila')
                                        ->format('M d, Y · g:i A') }}

                                </p>

                            @else

                                <p class="mt-1 text-xs text-slate-500">
                                    Customer Service is reviewing the requirements and details of your request.
                                </p>

                            @endif

                        </div>

                    </div>


                    {{-- Initial Processing Completed --}}
                    <div class="flex gap-4">

                        <div class="flex flex-col items-center">

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                {{ $processingCompleted
                                    ? 'bg-sky-600 text-white'
                                    : 'border border-slate-200 bg-white text-slate-400' }}">

                                <i
                                    class="fas
                                    {{ $processingCompleted ? 'fa-check' : 'fa-circle' }}
                                    text-xs">
                                </i>

                            </div>


                            @if (!$isClosedWithoutMaintenance)

                                <div
                                    class="min-h-12 w-px flex-1
                                    {{ $processingCompleted ? 'bg-sky-300' : 'bg-slate-200' }}">
                                </div>

                            @endif

                        </div>


                        <div class="pb-6">

                            <p
                                class="text-sm font-semibold
                                {{ $processingCompleted ? 'text-slate-800' : 'text-slate-400' }}">

                                Initial Processing Completed

                            </p>


                            @if ($processingCompleted)

                                <p class="mt-1 text-xs text-slate-500">
                                    Completed by

                                    <span class="font-semibold text-slate-700">
                                        {{ $commercialResolution?->processor?->full_name ?? 'Customer Service' }}
                                    </span>
                                </p>


                                <p class="mt-1.5 text-[11px] font-medium text-slate-400">

                                    <i class="far fa-clock mr-1"></i>

                                    {{ $commercialResolution->initial_processing_completed_at
                                        ->timezone('Asia/Manila')
                                        ->format('M d, Y · g:i A') }}

                                </p>

                            @else

                                <p class="mt-1 text-xs text-slate-500">
                                    Customer Service will complete the initial assessment of your request.
                                </p>

                            @endif

                        </div>

                    </div>



                    {{-- Commercial Closed Without Maintenance --}}
                    @if ($isClosedWithoutMaintenance)

                        <div class="flex gap-4">

                            <div class="flex flex-col items-center">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center
                                           rounded-full bg-sky-600 text-white">

                                    <i class="fas fa-check text-xs"></i>

                                </div>

                            </div>


                            <div>

                                <p class="text-sm font-semibold text-slate-800">
                                    Request Closed
                                </p>


                                <p class="mt-1 text-xs text-slate-500">
                                    Completed by

                                    <span class="font-semibold text-slate-700">
                                        {{ $commercialResolution?->processor?->full_name ?? 'Customer Service' }}
                                    </span>
                                </p>


                                <p class="mt-1.5 text-[11px] font-medium text-slate-400">

                                    <i class="far fa-clock mr-1"></i>

                                    {{ ($complaint->completed_at ?? $complaint->updated_at)
                                        ->timezone('Asia/Manila')
                                        ->format('M d, Y · g:i A') }}

                                </p>

                            </div>

                        </div>

                    @else

                        {{-- Forwarded to Maintenance --}}
                        @php
                            $forwarded =
                                (bool) $commercialResolution?->forwarded_to_maintenance_at ||
                                in_array(
                                    $complaint->status,
                                    [
                                        'For Maintenance',
                                        'Assigned',
                                        'In Progress',
                                        'Completed',
                                        'Closed',
                                    ],
                                    true
                                );
                        @endphp


                        <div class="flex gap-4">

                            <div class="flex flex-col items-center">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                    {{ $forwarded
                                        ? 'bg-sky-600 text-white'
                                        : 'border border-slate-200 bg-white text-slate-400' }}">

                                    <i
                                        class="fas
                                        {{ $forwarded ? 'fa-check' : 'fa-share' }}
                                        text-xs">
                                    </i>

                                </div>


                                <div
                                    class="min-h-12 w-px flex-1
                                    {{ $forwarded ? 'bg-sky-300' : 'bg-slate-200' }}">
                                </div>

                            </div>


                            <div class="pb-6">

                                <p
                                    class="text-sm font-semibold
                                    {{ $forwarded ? 'text-slate-800' : 'text-slate-400' }}">

                                    Forwarded to Maintenance

                                </p>


                                @if ($forwarded)

                                    <p class="mt-1 text-xs text-slate-500">
                                        Forwarded by

                                        <span class="font-semibold text-slate-700">
                                            {{ $commercialResolution?->forwarder?->full_name ?? 'Customer Service' }}
                                        </span>
                                    </p>


                                    @if ($commercialResolution?->forwarded_to_maintenance_at)

                                        <p class="mt-1.5 text-[11px] font-medium text-slate-400">

                                            <i class="far fa-clock mr-1"></i>

                                            {{ $commercialResolution->forwarded_to_maintenance_at
                                                ->timezone('Asia/Manila')
                                                ->format('M d, Y · g:i A') }}

                                        </p>

                                    @endif

                                @else

                                    <p class="mt-1 text-xs text-slate-500">
                                        Physical maintenance work may be required for your request.
                                    </p>

                                @endif

                            </div>

                        </div>

                    @endif


                {{-- ========================================================= --}}
                {{-- ENGINEERING WORKFLOW --}}
                {{-- ========================================================= --}}

                @else

                    {{-- Forwarded to Maintenance --}}
                    <div class="flex gap-4">

                        <div class="flex flex-col items-center">

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                {{ $verified
                                    ? 'bg-sky-600 text-white'
                                    : 'border border-slate-200 bg-white text-slate-400' }}">

                                <i
                                    class="fas
                                    {{ $verified ? 'fa-check' : 'fa-share' }}
                                    text-xs">
                                </i>

                            </div>


                            <div
                                class="min-h-12 w-px flex-1
                                {{ $verified ? 'bg-sky-300' : 'bg-slate-200' }}">
                            </div>

                        </div>


                        <div class="pb-6">

                            <p
                                class="text-sm font-semibold
                                {{ $verified ? 'text-slate-800' : 'text-slate-400' }}">

                                Forwarded to Maintenance

                            </p>


                            @if ($verified)

                                <p class="mt-1 text-xs text-slate-500">
                                    Forwarded after verification by

                                    <span class="font-semibold text-slate-700">
                                        {{ $complaint->verifier?->full_name ?? 'Customer Service' }}
                                    </span>
                                </p>


                                <p class="mt-1.5 text-[11px] font-medium text-slate-400">

                                    <i class="far fa-clock mr-1"></i>

                                    {{ $complaint->verified_at
                                        ->timezone('Asia/Manila')
                                        ->format('M d, Y · g:i A') }}

                                </p>

                            @else

                                <p class="mt-1 text-xs text-slate-500">
                                    Your verified complaint will be forwarded for maintenance handling.
                                </p>

                            @endif

                        </div>

                    </div>

                @endif



                {{-- ========================================================= --}}
                {{-- MAINTENANCE WORKFLOW --}}
                {{-- ========================================================= --}}

                @if (!$isClosedWithoutMaintenance)

                    {{-- Plumber Assigned --}}
                    @php
                        $assigned = $complaint->technicians->isNotEmpty();
                    @endphp


                    <div class="flex gap-4">

                        <div class="flex flex-col items-center">

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                {{ $assigned
                                    ? 'bg-sky-600 text-white'
                                    : 'border border-slate-200 bg-white text-slate-400' }}">

                                <i
                                    class="fas
                                    {{ $assigned ? 'fa-check' : 'fa-user-group' }}
                                    text-xs">
                                </i>

                            </div>


                            <div
                                class="min-h-12 w-px flex-1
                                {{ $assigned ? 'bg-sky-300' : 'bg-slate-200' }}">
                            </div>

                        </div>


                        <div class="pb-6">

                            <p
                                class="text-sm font-semibold
                                {{ $assigned ? 'text-slate-800' : 'text-slate-400' }}">

                                Plumber Assigned

                            </p>


                            @if ($assigned)

                                <p class="mt-1 text-xs text-slate-500">
                                    Assigned to

                                    <span class="font-semibold text-slate-700">
                                        {{ $assignedNames->join(', ') }}
                                    </span>
                                </p>


                                @if ($firstAssignedAt)

                                    <p class="mt-1.5 text-[11px] font-medium text-slate-400">

                                        <i class="far fa-clock mr-1"></i>

                                        {{ \Illuminate\Support\Carbon::parse($firstAssignedAt)
                                            ->timezone('Asia/Manila')
                                            ->format('M d, Y · g:i A') }}

                                    </p>

                                @endif

                            @else

                                <p class="mt-1 text-xs text-slate-400">
                                    Waiting for plumber assignment.
                                </p>

                            @endif

                        </div>

                    </div>



                    {{-- Maintenance In Progress --}}
                    @php
                        $maintenanceStarted =
                            $report?->started_at ??
                            $firstStartedAt;
                    @endphp


                    <div class="flex gap-4">

                        <div class="flex flex-col items-center">

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                {{ $maintenanceStarted
                                    ? 'bg-sky-600 text-white'
                                    : 'border border-slate-200 bg-white text-slate-400' }}">

                                <i
                                    class="fas
                                    {{ $maintenanceStarted ? 'fa-check' : 'fa-screwdriver-wrench' }}
                                    text-xs">
                                </i>

                            </div>


                            <div
                                class="min-h-12 w-px flex-1
                                {{ $maintenanceStarted ? 'bg-sky-300' : 'bg-slate-200' }}">
                            </div>

                        </div>


                        <div class="pb-6">

                            <p
                                class="text-sm font-semibold
                                {{ $maintenanceStarted ? 'text-slate-800' : 'text-slate-400' }}">

                                Maintenance In Progress

                            </p>


                            @if ($maintenanceStarted)

                                <p class="mt-1 text-xs text-slate-500">
                                    Maintenance team

                                    <span class="font-semibold text-slate-700">
                                        {{ $assignedNames->join(', ') }}
                                    </span>
                                </p>


                                <p class="mt-1.5 text-[11px] font-medium text-slate-400">

                                    <i class="far fa-clock mr-1"></i>

                                    {{ \Illuminate\Support\Carbon::parse($maintenanceStarted)
                                        ->timezone('Asia/Manila')
                                        ->format('M d, Y · g:i A') }}

                                </p>

                            @else

                                <p class="mt-1 text-xs text-slate-500">
                                    The assigned plumber will begin the maintenance work.
                                </p>

                            @endif

                        </div>

                    </div>



                    {{-- Maintenance Completed --}}
                    @php
                        $maintenanceCompleted =
                            (bool) $report?->submitted_at ||
                            in_array(
                                $complaint->status,
                                ['Completed', 'Closed'],
                                true
                            );

                        $maintenanceCompletedAt =
                            $report?->submitted_at ??
                            $firstCompletedAt ??
                            ($maintenanceCompleted
                                ? $complaint->completed_at
                                : null);
                    @endphp


                    <div class="flex gap-4">

                        <div class="flex flex-col items-center">

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                {{ $maintenanceCompleted
                                    ? 'bg-sky-600 text-white'
                                    : 'border border-slate-200 bg-white text-slate-400' }}">

                                <i
                                    class="fas
                                    {{ $maintenanceCompleted ? 'fa-check' : 'fa-clipboard-check' }}
                                    text-xs">
                                </i>

                            </div>


                            <div
                                class="min-h-12 w-px flex-1
                                {{ $maintenanceCompleted ? 'bg-sky-300' : 'bg-slate-200' }}">
                            </div>

                        </div>


                        <div class="pb-6">

                            <p
                                class="text-sm font-semibold
                                {{ $maintenanceCompleted ? 'text-slate-800' : 'text-slate-400' }}">

                                Maintenance Completed

                            </p>


                            @if ($maintenanceCompleted)

                                <p class="mt-1 text-xs text-slate-500">
                                    Report submitted by

                                    <span class="font-semibold text-slate-700">
                                        {{ $report?->technician?->full_name ?? 'Assigned plumber' }}
                                    </span>
                                </p>


                                @if ($maintenanceCompletedAt)

                                    <p class="mt-1.5 text-[11px] font-medium text-slate-400">

                                        <i class="far fa-clock mr-1"></i>

                                        {{ \Illuminate\Support\Carbon::parse($maintenanceCompletedAt)
                                            ->timezone('Asia/Manila')
                                            ->format('M d, Y · g:i A') }}

                                    </p>

                                @endif

                            @else

                                <p class="mt-1 text-xs text-slate-500">
                                    The assigned plumber will submit the maintenance accomplishment report.
                                </p>

                            @endif

                        </div>

                    </div>



                    {{-- Closed --}}
                    @php
                        $closed = $complaint->status === 'Closed';
                    @endphp


                    <div class="flex gap-4">

                        <div class="flex flex-col items-center">

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full
                                {{ $closed
                                    ? 'bg-sky-600 text-white'
                                    : 'border border-slate-200 bg-white text-slate-400' }}">

                                <i
                                    class="fas
                                    {{ $closed ? 'fa-check' : 'fa-lock' }}
                                    text-xs">
                                </i>

                            </div>

                        </div>


                        <div>

                            <p
                                class="text-sm font-semibold
                                {{ $closed ? 'text-slate-800' : 'text-slate-400' }}">

                                {{ $isCommercial
                                    ? 'Request Closed'
                                    : 'Complaint Closed' }}

                            </p>


                            @if ($closed)

                                <p class="mt-1 text-xs text-slate-500">
                                    Your concern has been finalized by Sagay Water District.
                                </p>


                                <p class="mt-1.5 text-[11px] font-medium text-slate-400">

                                    <i class="far fa-clock mr-1"></i>

                                    {{ $complaint->updated_at
                                        ->timezone('Asia/Manila')
                                        ->format('M d, Y · g:i A') }}

                                </p>

                            @else

                                <p class="mt-1 text-xs text-slate-500">
                                    Pending final review and closure.
                                </p>

                            @endif

                        </div>

                    </div>

                @endif

            @endif

        </div>

    </div>

</section>

                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">

                    <div class="flex items-start gap-3">

                        <i class="fas fa-circle-info mt-0.5 text-amber-600"></i>

                        <p class="text-xs leading-5 text-amber-800">

                            @if ($isCommercial)
                                Complaint verification, processing, review, and final actions
                                are performed by authorized Sagay Water District personnel.
                            @else
                                Complaint verification, plumber assignments, accomplishment
                                review, and final actions are performed by authorized
                                Sagay Water District personnel.
                            @endif

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

    @if ($complaint->latitude !== null && $complaint->longitude !== null)
        @push('scripts')
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const mapElement = document.getElementById('complaintMap');

                    if (!mapElement || typeof L === 'undefined') {
                        return;
                    }

                    const savedLat = parseFloat(
                        @json($complaint->latitude)
                    );

                    const savedLng = parseFloat(
                        @json($complaint->longitude)
                    );

                    if (
                        Number.isNaN(savedLat) ||
                        Number.isNaN(savedLng)
                    ) {
                        return;
                    }

                    const map = L.map(
                        'complaintMap', {
                            scrollWheelZoom: false
                        }
                    ).setView(
                        [savedLat, savedLng],
                        16
                    );

                    L.tileLayer(
                        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap contributors'
                        }
                    ).addTo(map);

                    const marker = L.marker([
                        savedLat,
                        savedLng
                    ]).addTo(map);

                    const complaintType =
                        @json(optional($complaint->category)->name ?? 'Water Service Concern');

                    marker.bindPopup(
                        '<div style="min-width:160px">' +
                        '<strong>' + escapeHtml(complaintType) + '</strong>' +
                        '<br><span style="font-size:12px;color:#64748b;">Service location</span>' +
                        '</div>'
                    );

                    function escapeHtml(value) {
                        const element =
                            document.createElement('div');

                        element.textContent =
                            value ?? '';

                        return element.innerHTML;
                    }

                    setTimeout(function() {
                        map.invalidateSize();
                    }, 300);
                });
            </script>
        @endpush
    @endif

@endsection
