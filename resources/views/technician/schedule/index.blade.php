@extends('technician.layouts.app')

@section('title', 'My Schedule')

@section('content')
    @php
        $isHistory = $view === 'history';
        $isMissed = $view === 'missed';
        $hasFilters = request()->filled('search') || request()->filled('date_from') || request()->filled('date_to');

        $filters = request()->only(['search', 'date_from', 'date_to']);
    @endphp

    <div class="mx-auto w-full max-w-7xl min-w-0 space-y-5 pb-10">

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-sky-600">
                    <i class="fas fa-screwdriver-wrench"></i>
                    Maintenance Operations
                </p>

                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    My Schedule
                </h1>

                <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                    Track your finalized maintenance visits, review assignments,
                    and access your completed schedule history.
                </p>
            </div>

            <a href="{{ route('technician.schedule.print', array_merge($filters, ['view' => $view])) }}" target="_blank"
                rel="noopener noreferrer"
                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 sm:w-auto">
                <i class="fas fa-print"></i>
                Print {{ $isHistory ? 'History' : ($isMissed ? 'Missed Visits' : 'Schedule') }}
            </a>
        </div>

        {{-- DAILY DUTY: coverage area and shift, separate from work orders --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="flex items-center gap-2 text-base font-bold text-slate-900">
                        <i class="fas fa-user-clock text-sky-600"></i> My Duty Assignment
                    </h2>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ now('Asia/Manila')->format('l, F j, Y') }} · Set by the Maintenance Manager
                    </p>
                </div>
                @if ($todayDuty)
                    @php
                        $dutyBadge = match ($todayDuty->status) {
                            'On Duty' => 'bg-emerald-50 text-emerald-700',
                            'Off Duty' => 'bg-rose-50 text-rose-700',
                            'Completed' => 'bg-slate-100 text-slate-700',
                            default => 'bg-sky-50 text-sky-700',
                        };
                    @endphp
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $dutyBadge }}">
                        {{ $todayDuty->status }}
                    </span>
                @endif
            </div>

            @if ($todayDuty)
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl bg-sky-50 p-3">
                        <p class="text-xs font-semibold text-sky-700"><i class="fas fa-map-pin mr-1"></i> Assigned Duty Area
                        </p>
                        <p class="mt-1 break-words text-sm font-bold text-slate-900">
                            {{ $todayDuty->assigned_area ?: $todayDuty->technician?->serviceArea?->name ?? 'Not assigned' }}
                        </p>
                        @if (!$todayDuty->assigned_area && $todayDuty->technician?->serviceArea?->name)
                            <p class="mt-1 text-xs text-slate-500">Default service area</p>
                        @endif
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <p class="text-xs font-semibold text-slate-500"><i class="fas fa-clock mr-1"></i> Duty Hours</p>
                        <p class="mt-1 text-sm font-bold text-slate-900">
                            {{ \Carbon\Carbon::parse($todayDuty->start_time)->format('h:i A') }} –
                            {{ \Carbon\Carbon::parse($todayDuty->end_time)->format('h:i A') }}
                        </p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <p class="text-xs font-semibold text-slate-500"><i class="fas fa-screwdriver-wrench mr-1"></i> Duty
                            Type</p>
                        <p class="mt-1 text-sm font-bold text-slate-900">{{ $todayDuty->duty_type }}</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <p class="text-xs font-semibold text-slate-500"><i class="fas fa-location-dot mr-1"></i> Regular
                            Service Area</p>
                        <p class="mt-1 break-words text-sm font-bold text-slate-900">
                            {{ $todayDuty->technician?->serviceArea?->name ?? 'Not designated' }}
                        </p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-slate-500">
                    Your duty area indicates general coverage. See each approved work order below for its exact repair
                    location.
                </p>
            @else
                <div class="mt-4 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                    No duty record is scheduled for today. Contact the Maintenance Manager if you expected to be on duty.
                </div>
            @endif

            @if ($nextDuties->isNotEmpty())
                <details class="mt-4 border-t border-slate-100 pt-3">
                    <summary class="cursor-pointer text-sm font-semibold text-sky-700">View next {{ $nextDuties->count() }}
                        duty dates</summary>
                    <div class="mt-3 divide-y divide-slate-100">
                        @foreach ($nextDuties as $duty)
                            <div class="flex flex-wrap items-center justify-between gap-2 py-2 text-sm">
                                <span class="font-semibold text-slate-800">{{ $duty->duty_date->format('M d, Y') }}</span>
                                <span
                                    class="break-words text-slate-600">{{ $duty->assigned_area ?: $todayDuty?->technician?->serviceArea?->name ?? 'Default area' }}</span>
                                <span class="text-xs font-medium text-slate-500">{{ $duty->status }}</span>
                            </div>
                        @endforeach
                    </div>
                </details>
            @endif
        </section>

        {{-- SUMMARY --}}
        <div class="grid grid-cols-1 gap-3 min-[400px]:grid-cols-2 lg:grid-cols-4 sm:gap-4">

            @foreach ([['label' => 'Today', 'value' => $todayCount, 'icon' => 'fa-calendar-day', 'color' => 'sky'], ['label' => 'Upcoming', 'value' => $upcomingCount, 'icon' => 'fa-calendar-days', 'color' => 'blue'], ['label' => 'Completed', 'value' => $completedCount, 'icon' => 'fa-circle-check', 'color' => 'emerald'], ['label' => 'Missed', 'value' => $missedCount, 'icon' => 'fa-calendar-xmark', 'color' => 'rose']] as $stat)
                @php
                    $iconStyle = match ($stat['color']) {
                        'emerald' => 'bg-emerald-50 text-emerald-600',
                        'rose' => 'bg-rose-50 text-rose-600',
                        'blue' => 'bg-blue-50 text-blue-600',
                        default => 'bg-sky-50 text-sky-600',
                    };
                @endphp

                <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-500">
                                {{ $stat['label'] }}
                            </p>
                            <p class="mt-2 text-2xl font-bold tabular-nums text-slate-900 sm:text-3xl">
                                {{ number_format($stat['value']) }}
                            </p>
                        </div>

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $iconStyle }}">
                            <i class="fas {{ $stat['icon'] }}"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                {{ session('success') }}</div>
        @endif

        {{-- SCHEDULING NOTICE --}}
        <div class="flex items-start gap-3 rounded-2xl border border-sky-100 bg-sky-50 p-4">
            <i class="fas fa-circle-info mt-1 shrink-0 text-sky-600"></i>
            <p class="text-xs leading-6 text-sky-900 sm:text-sm">
                Only finalized schedules approved by the Maintenance Manager
                appear here. Scheduling changes are managed by the
                Maintenance Manager. Upcoming visits are sorted by date and
                planned start time.
            </p>
        </div>

        {{-- VIEW TABS --}}
        <div class="grid grid-cols-3 gap-1.5 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-sm" role="group"
            aria-label="Schedule view">

            <a href="{{ route('technician.schedule.index', array_merge($filters, ['view' => 'upcoming'])) }}"
                @if (!$isHistory && !$isMissed) aria-current="page" @endif
                class="flex min-h-12 items-center justify-center gap-2 rounded-xl px-2 py-3 text-xs font-semibold transition sm:text-sm
           {{ !$isHistory && !$isMissed ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">
                <i class="fas fa-calendar-days"></i>
                Upcoming
                <span
                    class="rounded-full px-2 py-0.5 text-[10px]
                {{ !$isHistory && !$isMissed ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                    {{ $upcomingCount }}
                </span>
            </a>

            <a href="{{ route('technician.schedule.index', array_merge($filters, ['view' => 'history'])) }}"
                @if ($isHistory) aria-current="page" @endif
                class="flex min-h-12 items-center justify-center gap-2 rounded-xl px-2 py-3 text-xs font-semibold transition sm:text-sm
           {{ $isHistory ? 'bg-sky-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">
                <i class="fas fa-clock-rotate-left"></i>
                History
                <span
                    class="rounded-full px-2 py-0.5 text-[10px]
                {{ $isHistory ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                    {{ $completedCount }}
                </span>
            </a>
            <a href="{{ route('technician.schedule.index', array_merge($filters, ['view' => 'missed'])) }}"
                @if ($isMissed) aria-current="page" @endif
                class="flex min-h-12 items-center justify-center gap-1 rounded-xl px-2 py-3 text-xs font-semibold transition sm:text-sm
           {{ $isMissed ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50' }}">
                <i class="fas fa-calendar-xmark"></i> Missed Visits
                <span
                    class="rounded-full px-2 py-0.5 text-[10px] {{ $isMissed ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $missedCount }}</span>
            </a>
        </div>

        {{-- FILTER PANEL --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="mb-4 flex items-center gap-2">
                <i class="fas fa-filter text-sky-600"></i>
                <h2 class="text-sm font-bold text-slate-900">
                    Search & Filter
                </h2>
            </div>

            <form method="GET" action="{{ route('technician.schedule.index') }}" id="scheduleFilterForm">

                <input type="hidden" name="view" value="{{ $view }}">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="md:col-span-2">
                        <label for="schedule-search" class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Search Maintenance Visit
                        </label>

                        <div class="relative">
                            <i
                                class="fas fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input id="schedule-search" name="search" type="search" maxlength="150"
                                value="{{ request('search') }}" placeholder="Complaint no., consumer, location..."
                                class="block min-h-11 w-full min-w-0 rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-800 focus:border-sky-500 focus:ring-sky-500">
                        </div>
                    </div>

                    <div class="min-w-0">
                        <label for="schedule-from" class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Date From
                        </label>
                        <input id="schedule-from" name="date_from" type="date" value="{{ request('date_from') }}"
                            class="block min-h-11 w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>

                    <div class="min-w-0">
                        <label for="schedule-to" class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Date To
                        </label>
                        <input id="schedule-to" name="date_to" type="date" min="{{ request('date_from') }}"
                            value="{{ request('date_to') }}"
                            class="block min-h-11 w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                </div>

                <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center">
                    <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700">
                        <i class="fas fa-magnifying-glass"></i>
                        Apply Filters
                    </button>

                    @if ($hasFilters)
                        <a href="{{ route('technician.schedule.index', ['view' => $view]) }}"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            <i class="fas fa-xmark"></i>
                            Clear Filters
                        </a>
                    @endif
                </div>
            </form>

            @if ($errors->any())
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- SCHEDULE RESULTS --}}
        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div
                class="flex flex-col gap-2 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 class="flex items-center gap-2 text-base font-bold text-slate-900">
                        <i class="fas {{ $isHistory ? 'fa-clock-rotate-left' : 'fa-calendar-check' }} text-sky-600"></i>
                        {{ $isHistory ? 'Completed Visits' : ($isMissed ? 'Missed Visits Awaiting Manager Review' : 'Upcoming Visits') }}
                    </h2>
                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        {{ $isHistory ? 'Most recent scheduled visits first' : ($isMissed ? 'Overdue unstarted visits, most recent first' : 'Earliest scheduled visits first') }}
                    </p>
                </div>

                <span class="self-start rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                    {{ $workOrders->firstItem() ?? 0 }}–{{ $workOrders->lastItem() ?? 0 }}
                    of {{ $workOrders->total() }} records
                </span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($workOrders as $workOrder)
                    @php
                        $complaint = $workOrder->complaint;
                        $scheduleDate = $workOrder->schedule?->schedule_date;
                        $date = $scheduleDate ? \Carbon\Carbon::parse($scheduleDate) : null;

                        $statusClass = match ($workOrder->status) {
                            'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'In Progress' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'Assigned', 'Scheduled' => 'bg-blue-50 text-blue-700 border-blue-200',
                            default => 'bg-slate-50 text-slate-700 border-slate-200',
                        };

                        $urgency = strtoupper(trim((string) ($complaint?->aiAnalysis?->urgency_level ?? '')));

                        $urgencyClass = match ($urgency) {
                            'HIGH', 'CRITICAL' => 'bg-red-50 text-red-700 border-red-200',
                            'MODERATE', 'MEDIUM' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'LOW' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            default => 'bg-slate-50 text-slate-600 border-slate-200',
                        };

                        $consumerName =
                            $complaint?->consumer?->full_name ?? ($complaint?->complainant_name ?? 'Unknown Consumer');

                        $detailUrl = $complaint ? route('technician.complaints.show', $complaint) : null;
                    @endphp

                    <article class="min-w-0 p-4 transition-colors hover:bg-slate-50/70 sm:p-5 lg:p-6">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:gap-5">

                            {{-- DATE --}}
                            <div class="flex shrink-0 items-center gap-3 md:w-20 md:flex-col md:gap-1">
                                <div
                                    class="flex h-16 w-16 shrink-0 flex-col items-center justify-center rounded-2xl border border-sky-100 bg-sky-50">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-sky-600">
                                        {{ $date?->format('M') ?? 'N/A' }}
                                    </span>
                                    <span class="text-2xl font-extrabold leading-tight text-sky-800">
                                        {{ $date?->format('d') ?? '--' }}
                                    </span>
                                </div>

                                <div class="text-left md:text-center">
                                    <p class="text-xs font-semibold text-slate-700">
                                        {{ $date?->format('l') ?? 'Date unavailable' }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ $date?->format('Y') ?? '' }}
                                    </p>
                                </div>
                            </div>

                            {{-- MAIN INFORMATION --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="break-all text-sm font-bold text-sky-700">
                                        {{ $complaint?->complaint_no ?? 'Complaint unavailable' }}
                                    </span>

                                    <span
                                        class="rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $statusClass }}">
                                        {{ $workOrder->status }}
                                    </span>

                                    @if ($urgency)
                                        <span
                                            class="rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $urgencyClass }}">
                                            {{ $urgency }} Urgency
                                        </span>
                                    @endif

                                    @if ($workOrder->dispatch_type === 'Emergency')
                                        <span
                                            class="rounded-full border border-red-200 bg-red-50 px-2.5 py-1 text-[11px] font-semibold text-red-700">
                                            <i class="fas fa-bolt mr-1"></i>
                                            Emergency
                                        </span>
                                    @endif
                                </div>

                                <h3 class="mt-2 break-words text-base font-bold leading-6 text-slate-900 sm:text-lg">
                                    {{ $complaint?->category?->name ?? 'Uncategorized Complaint' }}
                                </h3>

                                {{-- MAINTENANCE VISIT DETAILS --}}
                                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">

                                    {{-- VISIT NUMBER --}}
                                    <div
                                        class="flex min-w-0 items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-slate-600 shadow-sm ring-1 ring-slate-200">
                                            <i class="fas fa-hashtag"></i>
                                        </div>

                                        <div class="min-w-0">
                                            <p class="text-xs font-medium text-slate-500">
                                                Maintenance Visit
                                            </p>
                                            <p class="mt-1 text-sm font-bold text-slate-900">
                                                Visit {{ $workOrder->visit_number ?? 1 }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- DISPATCH TYPE --}}
                                    <div
                                        class="flex min-w-0 items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                                            <i class="fas fa-screwdriver-wrench"></i>
                                        </div>

                                        <div class="min-w-0">
                                            <p class="text-xs font-medium text-slate-500">
                                                Dispatch Type
                                            </p>
                                            <p class="mt-1 text-sm font-bold text-slate-900">
                                                {{ $workOrder->dispatch_type ?: 'Regular' }}
                                            </p>
                                        </div>
                                    </div>

                                </div>

                                {{-- CONSUMER AND LOCATION --}}
                                <div class="mt-4 grid grid-cols-1 gap-3 text-sm lg:grid-cols-2">
                                    <div class="flex min-w-0 items-start gap-2 text-slate-600">
                                        <i class="fas fa-user mt-1 w-4 shrink-0 text-slate-400"></i>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-semibold text-slate-400">
                                                Consumer
                                            </p>
                                            <p class="break-words">
                                                {{ $consumerName }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex min-w-0 items-start gap-2 text-slate-600">
                                        <i class="fas fa-location-dot mt-1 w-4 shrink-0 text-slate-400"></i>
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-semibold text-slate-400">
                                                Service Location
                                            </p>
                                            <p class="break-words">
                                                {{ $complaint?->address ?: 'Address unavailable' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                @if ($isMissed)
                                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                        <p class="text-sm font-bold text-amber-900">Missed Visit Explanation</p>
                                        <p class="mt-1 text-xs text-amber-800">An unstarted visit needs manager
                                            verification. Reporting a reason does not reschedule it.</p>
                                        @if ($workOrder->missed_reported_at)
                                            <p class="mt-3 whitespace-pre-line text-sm text-amber-900">
                                                {{ $workOrder->missed_reason }}</p>
                                            <p class="mt-2 text-xs text-amber-700">Submitted
                                                {{ $workOrder->missed_reported_at->format('M d, Y h:i A') }} · Awaiting
                                                manager review</p>
                                        @else
                                            <form method="POST"
                                                action="{{ route('technician.schedule.missed.report', $workOrder) }}"
                                                class="mt-3 space-y-3">
                                                @csrf
                                                <label for="missed-{{ $workOrder->id }}"
                                                    class="block text-xs font-semibold text-amber-900">Why was this visit
                                                    not started?</label>
                                                <textarea id="missed-{{ $workOrder->id }}" name="missed_reason" required minlength="10" maxlength="2000"
                                                    rows="3" class="w-full rounded-xl border-amber-300 text-sm"
                                                    placeholder="Describe what happened, including any attempted coordination.">{{ old('missed_reason') }}</textarea>
                                                <button type="submit"
                                                    class="rounded-xl bg-amber-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-800">Submit
                                                    Explanation</button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                                @if ($workOrder->manager_remarks)
                                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3 sm:p-4">
                                        <p class="flex items-center gap-2 text-xs font-bold text-slate-700">
                                            <i class="fas fa-clipboard-list text-slate-400"></i>
                                            Manager Remarks
                                        </p>
                                        <p
                                            class="mt-2 whitespace-pre-line break-words text-xs leading-6 text-slate-600 sm:text-sm">
                                            {{ $workOrder->manager_remarks }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- ACTION --}}
                            @if ($detailUrl)
                                <div class="shrink-0 md:w-auto">
                                    <a href="{{ $detailUrl }}"
                                        class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-sky-200 bg-white px-4 py-2.5 text-sm font-semibold text-sky-700 transition hover:border-sky-600 hover:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-sky-500 md:w-auto">
                                        View Complaint
                                        <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="px-5 py-14 text-center sm:py-20">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-sky-50 text-sky-500">
                            <i class="fas fa-calendar-xmark text-2xl"></i>
                        </div>

                        <h3 class="mt-4 text-base font-bold text-slate-900">
                            {{ $isHistory ? 'No Completed Visits Found' : ($isMissed ? 'No Missed Visits Found' : 'No Upcoming Visits Found') }}
                        </h3>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                            {{ $hasFilters
                                ? 'No maintenance visits match your selected search or date range.'
                                : ($isHistory
                                    ? 'Your completed maintenance visits will appear here.'
                                    : ($isMissed
                                        ? 'There are no overdue unstarted approved visits awaiting review.'
                                        : 'You currently have no upcoming finalized maintenance visits.')) }}
                        </p>

                        @if ($hasFilters)
                            <a href="{{ route('technician.schedule.index', ['view' => $view]) }}"
                                class="mt-5 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-700">
                                <i class="fas fa-rotate-left"></i>
                                Reset Filters
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($workOrders->hasPages())
                <div class="border-t border-slate-100 px-4 py-4 sm:px-6">
                    {{ $workOrders->links() }}
                </div>
            @endif
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const from = document.getElementById('schedule-from');
                const to = document.getElementById('schedule-to');
                const form = document.getElementById('scheduleFilterForm');

                if (!from || !to || !form) return;

                function syncDates() {
                    to.min = from.value || '';

                    if (from.value && to.value && to.value < from.value) {
                        to.value = '';
                    }
                }

                from.addEventListener('change', syncDates);

                form.addEventListener('submit', function(event) {
                    if (from.value && to.value && to.value < from.value) {
                        event.preventDefault();
                        to.setCustomValidity('End date cannot be earlier than start date.');
                        to.reportValidity();
                    } else {
                        to.setCustomValidity('');
                    }
                });

                to.addEventListener('change', function() {
                    to.setCustomValidity('');
                });

                syncDates();
            });
        </script>
    @endpush
@endsection
