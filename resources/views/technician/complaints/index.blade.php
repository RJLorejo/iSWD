@extends('technician.layouts.app')

@section('title', $pageTitle ?? 'All Complaints')

@section('content')
    @php
        $isActivePage = ($scope ?? 'all') === 'active';
        $viewScope = $viewScope ?? request('scope', 'all');
        $filterLabels = [
            'scheduled' => 'Scheduled Visits',
            'in_progress' => 'In Progress Visits',
            'pending_workload' => 'Unresolved Pending Visits',
            'completed' => 'Accomplished Visits',
            'active' => 'Active Visits',
            'high_urgency' => 'High Urgency Complaints',
            'today' => "Today's Scheduled Visits",
            'overdue' => 'Overdue Scheduled Visits',
        ];

        $indexRoute = $isActivePage ? 'technician.active-work-orders.index' : 'technician.complaints.index';

        $hasFilters =
            ($viewScope !== 'all' && !$isActivePage) ||
            request()->filled('search') ||
            request()->filled('status') ||
            request()->filled('urgency') ||
            request()->filled('date_from') ||
            request()->filled('date_to');

        $statusStyles = [
            'Assigned' => 'bg-blue-50 text-blue-700 border-blue-200',
            'In Progress' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Closed' => 'bg-slate-100 text-slate-600 border-slate-200',
        ];

        $urgencyStyles = [
            'HIGH' => 'bg-red-50 text-red-700 border-red-200',
            'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',
            'LOW' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        ];

        $notFinalizedUrgencyStyle = 'bg-slate-50 text-slate-500 border-slate-200';
    @endphp

    <div class="mx-auto max-w-7xl space-y-5 pb-8">

        {{-- ========================================================= --}}
        {{-- PAGE HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div class="min-w-0">

                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-sky-600">
                    <i class="fas fa-screwdriver-wrench"></i>
                    Maintenance Operations
                </div>

                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    {{ $pageTitle ?? 'All Complaints' }}
                </h1>

                <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                    {{ $pageDescription ?? 'View maintenance complaints assigned to you.' }}
                </p>

                @if (!$isActivePage && isset($filterLabels[$viewScope]))
                    <div
                        class="mt-2 inline-flex items-center gap-2 rounded-lg bg-sky-100 px-3 py-1.5 text-xs font-semibold text-sky-800">
                        <i class="fas fa-filter"></i>
                        Filtered by: {{ $filterLabels[$viewScope] }}
                        <a href="{{ route('technician.complaints.index') }}" class="ml-1 underline">Clear</a>
                    </div>
                @endif

            </div>

            <div class="flex flex-wrap items-center gap-2">

                <div
                    class="inline-flex items-center gap-2 rounded-xl border border-sky-100 bg-sky-50 px-3.5 py-2.5 text-sm font-semibold text-sky-700">
                    <span class="h-2 w-2 rounded-full bg-sky-500"></span>

                    {{ $activeCount }}

                    Active {{ $activeCount === 1 ? 'Complaint' : 'Complaints' }}
                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- FLASH MESSAGES --}}
        {{-- ========================================================= --}}

        @if (session('success'))
            <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
                role="status">

                <i class="fas fa-circle-check mt-0.5"></i>

                <span>{{ session('success') }}</span>

            </div>
        @endif

        @if (session('error'))
            <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                role="alert">

                <i class="fas fa-circle-exclamation mt-0.5"></i>

                <span>{{ session('error') }}</span>

            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 gap-3 min-[420px]:grid-cols-3 sm:gap-4">

            {{-- ASSIGNED --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

                <div class="flex items-start justify-between gap-2">

                    <div class="min-w-0">

                        <p class="text-xs font-medium text-slate-500 sm:text-sm">
                            Assigned Complaints
                        </p>

                        <p class="mt-2 text-2xl font-bold tabular-nums text-slate-900 sm:text-3xl">
                            {{ $assignedCount }}
                        </p>

                    </div>

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fas fa-clipboard-list"></i>
                    </div>

                </div>

                <p class="mt-2 text-xs text-slate-400">
                    Waiting to start
                </p>

            </div>


            {{-- IN PROGRESS --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

                <div class="flex items-start justify-between gap-2">

                    <div class="min-w-0">

                        <p class="text-xs font-medium text-slate-500 sm:text-sm">
                            In Progress Complaints
                        </p>

                        <p class="mt-2 text-2xl font-bold tabular-nums text-amber-600 sm:text-3xl">
                            {{ $inProgressCount }}
                        </p>

                    </div>

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <i class="fas fa-screwdriver-wrench"></i>
                    </div>

                </div>

                <p class="mt-2 text-xs text-slate-400">
                    Active field work
                </p>

            </div>


            {{-- HIGH URGENCY --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

                <div class="flex items-start justify-between gap-2">

                    <div class="min-w-0">

                        <p class="text-xs font-medium text-slate-500 sm:text-sm">
                            High Urgency Complaints
                        </p>

                        <p class="mt-2 text-2xl font-bold tabular-nums text-red-600 sm:text-3xl">
                            {{ $urgentCount }}
                        </p>

                    </div>

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>

                </div>

                <p class="mt-2 text-xs text-slate-400">
                    Needs attention
                </p>

            </div>

        </div>


        <p class="text-xs leading-5 text-slate-500">
            The cards above count complaint statuses. Dashboard visit cards count individual maintenance work orders,
            so their totals may differ when a complaint has multiple visits or is awaiting a follow-up schedule.
        </p>

        {{-- ========================================================= --}}
        {{-- PAGE NAVIGATION --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

            {{-- ACTIVE WORK ORDERS --}}
            <a href="{{ route('technician.active-work-orders.index') }}"
                @if ($isActivePage) aria-current="page" @endif
                class="group flex items-center gap-3 rounded-2xl border p-4 transition
                    {{ $isActivePage
                        ? 'border-sky-300 bg-sky-50 shadow-sm'
                        : 'border-slate-200 bg-white hover:border-sky-200 hover:bg-slate-50' }}">

                <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                    {{ $isActivePage ? 'bg-sky-600 text-white' : 'bg-sky-100 text-sky-600' }}">

                    <i class="fas fa-list-check"></i>

                </div>

                <div class="min-w-0 flex-1">

                    <p class="text-sm font-bold text-slate-900">
                        Active Work Orders
                    </p>

                    <p class="mt-0.5 text-xs text-slate-500">
                        First assigned, first in queue
                    </p>

                </div>

                <span
                    class="rounded-full px-2.5 py-1 text-xs font-bold
                    {{ $isActivePage ? 'bg-sky-600 text-white' : 'bg-slate-100 text-slate-600' }}">

                    {{ $activeCount }}

                </span>

            </a>


            {{-- ALL COMPLAINTS --}}
            <a href="{{ route('technician.complaints.index') }}"
                @if (!$isActivePage) aria-current="page" @endif
                class="group flex items-center gap-3 rounded-2xl border p-4 transition
                    {{ !$isActivePage
                        ? 'border-sky-300 bg-sky-50 shadow-sm'
                        : 'border-slate-200 bg-white hover:border-sky-200 hover:bg-slate-50' }}">

                <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                    {{ !$isActivePage ? 'bg-sky-600 text-white' : 'bg-sky-100 text-sky-600' }}">

                    <i class="fas fa-clock-rotate-left"></i>

                </div>

                <div class="min-w-0 flex-1">

                    <p class="text-sm font-bold text-slate-900">
                        All Complaints
                    </p>

                    <p class="mt-0.5 text-xs text-slate-500">
                        Newest complaints first
                    </p>

                </div>

                <i class="fas fa-chevron-right text-xs text-slate-400"></i>

            </a>

        </div>


        {{-- ========================================================= --}}
        {{-- QUEUE INFORMATION --}}
        {{-- ========================================================= --}}

        @if ($isActivePage)
            <div class="flex items-start gap-3 rounded-2xl border border-sky-100 bg-sky-50/70 p-4">

                <i class="fas fa-circle-info mt-0.5 text-sky-600"></i>

                <div class="min-w-0">

                    <p class="text-sm font-semibold text-sky-900">
                        First Come, First Served Queue
                    </p>

                    <p class="mt-1 text-xs leading-5 text-sky-800 sm:text-sm">
                        Work orders are listed by assignment date and time,
                        from oldest to newest. Follow the Maintenance Manager's
                        finalized schedule before starting field work.
                    </p>

                </div>

            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- FILTERS --}}
        {{-- ========================================================= --}}

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <form method="GET" action="{{ route($indexRoute) }}" class="p-4 sm:p-5">
                @if (!$isActivePage && $viewScope !== 'all')
                    <input type="hidden" name="scope" value="{{ $viewScope }}">
                @endif

                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">

                    <div>

                        <h2 class="text-sm font-bold text-slate-900">
                            <i class="fas fa-filter mr-2 text-sky-600"></i>
                            Filter Complaints
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Narrow down complaints using the available filters.
                        </p>

                    </div>

                    @if ($hasFilters)
                        <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">
                            Filters Applied
                        </span>
                    @endif

                </div>


                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-6">

                    {{-- SEARCH --}}
                    <div class="sm:col-span-2">

                        <label for="complaint-search" class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Search
                        </label>

                        <div class="relative">

                            <i
                                class="fas fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>

                            <input id="complaint-search" type="search" name="search" value="{{ request('search') }}"
                                placeholder="Complaint number, consumer, type..."
                                class="w-full rounded-xl border-slate-300 py-2.5 pl-10 pr-3 text-sm focus:border-sky-500 focus:ring-sky-500">

                        </div>

                    </div>


                    {{-- STATUS --}}
                    <div>

                        <label for="complaint-status" class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Status
                        </label>

                        <select id="complaint-status" name="status"
                            class="w-full rounded-xl border-slate-300 py-2.5 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500">

                            <option value="">All Statuses</option>

                            <option value="Assigned" @selected(request('status') === 'Assigned')>
                                Assigned
                            </option>

                            <option value="In Progress" @selected(request('status') === 'In Progress')>
                                In Progress
                            </option>

                            @if (!$isActivePage)
                                <option value="Completed" @selected(request('status') === 'Completed')>
                                    Completed
                                </option>

                                <option value="Closed" @selected(request('status') === 'Closed')>
                                    Closed
                                </option>
                            @endif

                        </select>

                    </div>


                    {{-- URGENCY --}}
                    <div>

                        <label for="complaint-urgency" class="mb-1.5 block text-xs font-semibold text-slate-600">
                            CS Final Urgency
                        </label>

                        <select id="complaint-urgency" name="urgency"
                            class="w-full rounded-xl border-slate-300 py-2.5 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500">

                            <option value="">All Urgency Levels</option>

                            <option value="High" @selected(strtoupper((string) request('urgency')) === 'HIGH')>
                                High
                            </option>

                            <option value="Moderate" @selected(strtoupper((string) request('urgency')) === 'MODERATE')>
                                Moderate
                            </option>

                            <option value="Low" @selected(strtoupper((string) request('urgency')) === 'LOW')>
                                Low
                            </option>

                            <option value="not_assessed" @selected(request('urgency') === 'not_assessed')>
                                Not Assessed
                            </option>

                        </select>

                    </div>


                    {{-- DATE FROM --}}
                    <div>

                        <label for="complaint-date-from" class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Date From
                        </label>

                        <input id="complaint-date-from" type="date" name="date_from"
                            value="{{ request('date_from') }}"
                            class="w-full rounded-xl border-slate-300 py-2.5 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500">

                    </div>


                    {{-- DATE TO --}}
                    <div>

                        <label for="complaint-date-to" class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Date To
                        </label>

                        <input id="complaint-date-to" type="date" name="date_to" value="{{ request('date_to') }}"
                            min="{{ request('date_from') }}"
                            class="w-full rounded-xl border-slate-300 py-2.5 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500">

                    </div>

                </div>


                {{-- FILTER ACTIONS --}}
                <div class="mt-4 flex flex-wrap items-center gap-2">

                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">

                        <i class="fas fa-filter"></i>
                        Apply Filters

                    </button>


                    @if ($hasFilters)
                        <a href="{{ route($indexRoute) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">

                            <i class="fas fa-xmark"></i>
                            Clear

                        </a>
                    @endif


                    {{-- PRINT REPORT --}}
                    <a href="{{ route(
                        'technician.complaints.print-report',
                        array_merge(request()->only(['search', 'status', 'urgency', 'date_from', 'date_to']), [
                            'scope' => $isActivePage ? 'active' : $viewScope,
                        ]),
                    ) }}"
                        target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">

                        <i class="fas fa-print"></i>
                        Print Report

                    </a>

                </div>

            </form>

        </div>


        {{-- ========================================================= --}}
        {{-- COMPLAINT LIST --}}
        {{-- ========================================================= --}}

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- LIST HEADER --}}
            <div class="border-b border-slate-100 px-4 py-4 sm:px-6">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <h2 class="text-base font-bold text-slate-900">

                            <i class="fas fa-clipboard-list mr-2 text-sky-600"></i>

                            {{ $pageTitle ?? 'All Complaints' }}

                        </h2>

                        <p class="mt-1 text-xs text-slate-500 sm:text-sm">

                            {{ $isActivePage
                                ? 'Assigned and in-progress complaints requiring attention.'
                                : 'All maintenance complaints assigned to you.' }}

                        </p>

                    </div>


                    <div class="flex flex-wrap items-center gap-2">

                        <span
                            class="inline-flex items-center gap-1.5 rounded-lg border border-sky-100 bg-sky-50 px-2.5 py-1.5 text-xs font-semibold text-sky-700">

                            <i class="fas fa-arrow-down-wide-short"></i>

                            {{ $sortDescription ?? ($isActivePage ? 'Oldest assigned first' : 'Newest complaints first') }}

                        </span>

                        <span class="text-xs text-slate-500">

                            {{ $complaints->firstItem() ?? 0 }}
                            –
                            {{ $complaints->lastItem() ?? 0 }}
                            of
                            {{ $complaints->total() }}

                        </span>

                    </div>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- DESKTOP TABLE --}}
            {{-- ===================================================== --}}

            <div class="hidden overflow-x-auto lg:block">

                <table class="w-full min-w-[950px] text-left text-sm">

                    <thead class="border-b border-slate-200 bg-slate-50">

                        <tr>

                            <th scope="col"
                                class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Complaint
                            </th>

                            <th scope="col"
                                class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Consumer
                            </th>

                            <th scope="col"
                                class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Complaint Type
                            </th>

                            <th scope="col"
                                class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Location
                            </th>

                            <th scope="col"
                                class="px-5 py-3.5 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th scope="col"
                                class="px-5 py-3.5 text-right text-xs font-bold uppercase tracking-wide text-slate-500">
                                Details
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($complaints as $complaint)

                            @php
                                $statusClass =
                                    $statusStyles[$complaint->status] ?? 'bg-slate-50 text-slate-600 border-slate-200';

                                $finalUrgency = strtoupper(
                                    trim((string) ($complaint->aiAnalysis?->verified_urgency_level ?? '')),
                                );

                                $urgencyClass = $urgencyStyles[$finalUrgency] ?? $notFinalizedUrgencyStyle;
                                $workState = $complaint->myScheduleState ?? [];
                                $workDate = $workState['schedule_date'] ?? null;

                                $myAssignedAt = $complaint->technicians->firstWhere('id', auth()->id())?->pivot
                                    ?->assigned_at;

                                $detailUrl = route('technician.complaints.show', $complaint);
                            @endphp

                            <tr onclick="window.location.href = @js($detailUrl)"
                                onkeydown="if (event.target === this && (event.key === 'Enter' || event.key === ' ')) { event.preventDefault(); this.click(); }"
                                tabindex="0" role="link" aria-label="View complaint {{ $complaint->complaint_no }}"
                                title="View complaint {{ $complaint->complaint_no }}"
                                class="group cursor-pointer transition-colors hover:bg-sky-50/60 focus:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-sky-400">


                                {{-- COMPLAINT --}}
                                <td class="px-5 py-4 align-top">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600">

                                            <i class="fas fa-file-lines"></i>

                                        </div>

                                        <div class="min-w-0 space-y-2">

                                            <p class="font-bold text-sky-700">
                                                {{ $complaint->complaint_no }}
                                            </p>


                                            {{-- CS FINAL URGENCY --}}
                                            <div>
                                                <p
                                                    class="mb-1 text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                                    CS Final Decision
                                                </p>

                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-[10px] font-bold {{ $urgencyClass }}">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

                                                    {{ $finalUrgency !== '' ? $finalUrgency : 'NOT FINALIZED' }}
                                                </span>
                                            </div>


                                            {{-- SCHEDULE STATUS --}}
                                            <div>

                                                <span
                                                    class="inline-flex max-w-[230px] items-center gap-1.5 rounded-lg border border-sky-100 bg-sky-50 px-2 py-1 text-[11px] font-semibold text-sky-700">

                                                    <i class="fas fa-calendar-check shrink-0"></i>

                                                    <span class="truncate">
                                                        {{ $workState['label'] ?? 'Not Scheduled' }}
                                                    </span>

                                                </span>

                                            </div>


                                            @if ($workDate)
                                                <p class="text-xs text-slate-500">

                                                    {{ \Carbon\CarbonImmutable::parse($workDate)->format('M d, Y') }}

                                                    @if (!empty($workState['start_time']))
                                                        · {{ substr((string) $workState['start_time'], 0, 5) }}
                                                    @endif

                                                    @if (!empty($workState['end_time']))
                                                        – {{ substr((string) $workState['end_time'], 0, 5) }}
                                                    @endif

                                                </p>
                                            @endif


                                            {{-- DATE --}}
                                            <p class="text-xs leading-5 text-slate-500">

                                                <span class="font-semibold">
                                                    {{ $isActivePage ? 'Assigned:' : 'Submitted:' }}
                                                </span>

                                                @if ($isActivePage)
                                                    {{ $myAssignedAt ? \Carbon\Carbon::parse($myAssignedAt)->format('M d, Y h:i A') : 'Date unavailable' }}
                                                @else
                                                    {{ $complaint->created_at?->format('M d, Y h:i A') ?? 'Date unavailable' }}
                                                @endif

                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- CONSUMER --}}
                                <td class="px-5 py-4 align-top">

                                    <p class="font-semibold text-slate-800">
                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}
                                    </p>

                                    @if ($complaint->consumer?->account_number)
                                        <p class="mt-1 text-xs text-slate-500">
                                            Account: {{ $complaint->consumer->account_number }}
                                        </p>
                                    @elseif ($complaint->complainant_phone)
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $complaint->complainant_phone }}
                                        </p>
                                    @endif

                                </td>


                                {{-- COMPLAINT TYPE --}}
                                <td class="px-5 py-4 align-top">

                                    <span
                                        class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-medium leading-5 text-slate-700">

                                        {{ $complaint->category?->name ?? 'Uncategorized' }}

                                    </span>

                                </td>


                                {{-- LOCATION --}}
                                <td class="px-5 py-4 align-top">

                                    <div class="flex items-start gap-2">

                                        <i class="fas fa-location-dot mt-0.5 text-slate-400"></i>

                                        <div class="min-w-0">

                                            <p class="max-w-[220px] break-words text-sm leading-5 text-slate-700">
                                                {{ \Illuminate\Support\Str::limit($complaint->address ?: 'No address provided', 65) }}
                                            </p>

                                            @if (!is_null($complaint->latitude) && !is_null($complaint->longitude))
                                                <p class="mt-1 text-[11px] text-emerald-600">
                                                    <i class="fas fa-map-location-dot mr-1"></i>
                                                    GPS available
                                                </p>
                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- STATUS --}}
                                <td class="px-5 py-4 align-top">

                                    <span
                                        class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border px-2.5 py-1.5 text-xs font-semibold {{ $statusClass }}">

                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

                                        {{ $complaint->status }}

                                    </span>

                                </td>


                                {{-- DETAILS --}}
                                <td class="px-5 py-4 text-right align-top">

                                    <span
                                        class="inline-flex items-center gap-2 rounded-lg bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 transition group-hover:bg-sky-600 group-hover:text-white">

                                        View

                                        <i class="fas fa-arrow-right"></i>

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="px-6 py-16 text-center">

                                    <div
                                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                                        <i class="fas fa-clipboard-check text-xl"></i>

                                    </div>

                                    <h3 class="mt-4 font-bold text-slate-900">
                                        No complaints found
                                    </h3>

                                    <p class="mx-auto mt-1 max-w-sm text-sm leading-6 text-slate-500">

                                        {{ $hasFilters
                                            ? 'No complaints match your selected filters.'
                                            : ($isActivePage
                                                ? 'You currently have no assigned or in-progress complaints.'
                                                : 'There are no complaints assigned to you yet.') }}

                                    </p>

                                    @if ($hasFilters)
                                        <a href="{{ route($indexRoute) }}"
                                            class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-800">

                                            <i class="fas fa-xmark"></i>
                                            Clear Filters

                                        </a>
                                    @endif

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ===================================================== --}}
            {{-- MOBILE AND TABLET CARDS --}}
            {{-- ===================================================== --}}

            <div class="divide-y divide-slate-100 lg:hidden">

                @forelse ($complaints as $complaint)

                    @php
                        $statusClass =
                            $statusStyles[$complaint->status] ?? 'bg-slate-50 text-slate-600 border-slate-200';

                        $finalUrgency = strtoupper(
                            trim((string) ($complaint->aiAnalysis?->verified_urgency_level ?? '')),
                        );

                        $urgencyClass = $urgencyStyles[$finalUrgency] ?? $notFinalizedUrgencyStyle;
                        $workState = $complaint->myScheduleState ?? [];
                        $workDate = $workState['schedule_date'] ?? null;

                        $myAssignedAt = $complaint->technicians->firstWhere('id', auth()->id())?->pivot?->assigned_at;

                        $detailUrl = route('technician.complaints.show', $complaint);
                    @endphp

                    <div onclick="window.location.href = @js($detailUrl)"
                        onkeydown="if (event.target === this && (event.key === 'Enter' || event.key === ' ')) { event.preventDefault(); this.click(); }"
                        tabindex="0" role="link" aria-label="View complaint {{ $complaint->complaint_no }}"
                        class="group cursor-pointer p-4 transition-colors hover:bg-sky-50/60 focus:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-sky-400 sm:p-5">


                        {{-- TOP --}}
                        <div class="flex flex-wrap items-start justify-between gap-3">

                            <div class="min-w-0 flex-1">

                                <p class="break-words text-base font-bold text-sky-700">
                                    {{ $complaint->complaint_no }}
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-500">

                                    <span class="font-semibold">
                                        {{ $isActivePage ? 'Assigned:' : 'Submitted:' }}
                                    </span>

                                    @if ($isActivePage)
                                        {{ $myAssignedAt ? \Carbon\Carbon::parse($myAssignedAt)->format('M d, Y h:i A') : 'Date unavailable' }}
                                    @else
                                        {{ $complaint->created_at?->format('M d, Y h:i A') ?? 'Date unavailable' }}
                                    @endif

                                </p>

                            </div>


                            <span
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-full border px-2.5 py-1.5 text-[11px] font-semibold {{ $statusClass }}">

                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

                                {{ $complaint->status }}

                            </span>

                        </div>


                        {{-- BADGES --}}
                        <div class="mt-3 flex flex-wrap items-center gap-2">

                            {{-- CS FINAL URGENCY --}}
                            <span title="Urgency finalized by Customer Service"
                                class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[10px] font-bold {{ $urgencyClass }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

                                CS Final:
                                {{ $finalUrgency !== '' ? $finalUrgency : 'NOT FINALIZED' }}
                            </span>

                            <span
                                class="inline-flex items-center gap-1.5 rounded-lg border border-sky-100 bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">

                                <i class="fas fa-calendar-days"></i>

                                {{ $workState['label'] ?? 'Not Scheduled' }}

                            </span>

                        </div>


                        {{-- SCHEDULE DATE --}}
                        @if ($workDate)
                            <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">

                                <i class="fas fa-clock text-slate-400"></i>

                                <span>

                                    {{ \Carbon\CarbonImmutable::parse($workDate)->format('M d, Y') }}

                                    @if (!empty($workState['start_time']))
                                        · {{ substr((string) $workState['start_time'], 0, 5) }}
                                    @endif

                                    @if (!empty($workState['end_time']))
                                        – {{ substr((string) $workState['end_time'], 0, 5) }}
                                    @endif

                                </span>

                            </div>
                        @endif


                        {{-- DETAILS --}}
                        <div class="mt-4 grid grid-cols-1 gap-3 min-[420px]:grid-cols-2">

                            {{-- CONSUMER --}}
                            <div class="min-w-0">

                                <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">
                                    Consumer
                                </p>

                                <p class="mt-1 break-words text-sm font-semibold text-slate-800">
                                    {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}
                                </p>

                                @if ($complaint->consumer?->account_number)
                                    <p class="mt-1 text-xs text-slate-500">
                                        Account: {{ $complaint->consumer->account_number }}
                                    </p>
                                @endif

                            </div>


                            {{-- COMPLAINT TYPE --}}
                            <div class="min-w-0">

                                <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">
                                    Complaint Type
                                </p>

                                <p class="mt-1 break-words text-sm font-medium text-slate-700">
                                    {{ $complaint->category?->name ?? 'Uncategorized' }}
                                </p>

                            </div>

                        </div>


                        {{-- LOCATION --}}
                        <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-3">

                            <div class="flex items-start gap-2">

                                <i class="fas fa-location-dot mt-0.5 text-slate-400"></i>

                                <div class="min-w-0 flex-1">

                                    <p class="break-words text-sm leading-5 text-slate-600">
                                        {{ $complaint->address ?: 'No address provided' }}
                                    </p>

                                    @if (!is_null($complaint->latitude) && !is_null($complaint->longitude))
                                        <p class="mt-1 text-xs text-emerald-600">

                                            <i class="fas fa-map-location-dot mr-1"></i>

                                            GPS available

                                        </p>
                                    @endif

                                </div>

                            </div>

                        </div>


                        {{-- FOOTER --}}
                        <div class="mt-4 flex items-center justify-between gap-3">

                            <span class="text-xs text-slate-400">

                                Updated {{ $complaint->updated_at?->diffForHumans() ?? 'N/A' }}

                            </span>

                            <span
                                class="inline-flex items-center gap-2 rounded-lg bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 transition group-hover:bg-sky-600 group-hover:text-white">

                                View Details

                                <i class="fas fa-arrow-right"></i>

                            </span>

                        </div>

                    </div>

                @empty

                    <div class="px-5 py-14 text-center">

                        <div
                            class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <i class="fas fa-clipboard-check text-xl"></i>
                        </div>

                        <h3 class="mt-4 font-bold text-slate-900">
                            No complaints found
                        </h3>

                        <p class="mx-auto mt-1 max-w-sm text-sm leading-6 text-slate-500">

                            {{ $hasFilters
                                ? 'No complaints match your selected filters.'
                                : ($isActivePage
                                    ? 'You currently have no active work orders.'
                                    : 'There are no complaints assigned to you yet.') }}

                        </p>

                        @if ($hasFilters)
                            <a href="{{ route($indexRoute) }}"
                                class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-sky-700">

                                <i class="fas fa-xmark"></i>
                                Clear Filters

                            </a>
                        @endif

                    </div>

                @endforelse

            </div>


            {{-- ===================================================== --}}
            {{-- PAGINATION --}}
            {{-- ===================================================== --}}

            @if ($complaints->hasPages())
                <div class="border-t border-slate-100 px-4 py-4 sm:px-6">

                    {{ $complaints->links() }}

                </div>
            @endif

        </div>


        {{-- ========================================================= --}}
        {{-- FOOTER NOTE --}}
        {{-- ========================================================= --}}

        @if ($isActivePage)
            <p class="flex items-start gap-2 text-xs leading-5 text-slate-500">

                <i class="fas fa-circle-info mt-0.5 text-slate-400"></i>

                <span>
                    Queue order is based on assignment time. Actual maintenance
                    activities must follow the approved schedule and work-order
                    permissions.
                </span>

            </p>
        @endif

    </div>
@endsection
