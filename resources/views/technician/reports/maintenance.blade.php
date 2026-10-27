
@extends('technician.layouts.app')

@section('title', 'Work Summary')

@section('content')

    @php
        $workStatusTotal = $statusBreakdown->sum();
        $urgencyTotal = $urgencyBreakdown->sum();

        $statusColors = [
            'Draft' => 'bg-violet-500',
            'Scheduled' => 'bg-blue-500',
            'In Progress' => 'bg-amber-500',
            'Pending Workload' => 'bg-orange-500',
            'Completed' => 'bg-emerald-500',
        ];

        $urgencyColors = [
            'HIGH' => 'bg-red-500',
            'MODERATE' => 'bg-amber-500',
            'MEDIUM' => 'bg-amber-500',
            'LOW' => 'bg-green-500',
        ];
    @endphp

    <div class="mx-auto w-full max-w-7xl min-w-0 space-y-4 overflow-hidden pb-8 sm:space-y-6">

        {{-- HEADER --}}

        <header class="flex min-w-0 flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600 sm:text-xs">
                    Maintenance Operations
                </p>

                <h1 class="mt-1 break-words text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Work Summary
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                    Overview of your maintenance visits, current workload and accomplishments.
                </p>
            </div>

            <a href="{{ route('technician.reports.maintenance.print', request()->query()) }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 sm:w-auto">

                <i class="fas fa-print text-sky-600" aria-hidden="true"></i>
                Print Summary
            </a>
        </header>

        {{-- DATE FILTER --}}

        <section class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-5">
            <div class="mb-3">
                <h2 class="text-sm font-semibold text-slate-900">Filter Reporting Period</h2>
                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Choose a date range to update your work summary.
                </p>
            </div>

            <form method="GET"
                action="{{ route('technician.reports.maintenance') }}"
                class="grid min-w-0 grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">

                <div class="min-w-0">
                    <label for="report-from" class="mb-1.5 block text-xs font-semibold text-slate-600">
                        From Date
                    </label>

                    <input id="report-from"
                        type="date"
                        name="from"
                        value="{{ request('from', $from->format('Y-m-d')) }}"
                        class="block min-h-11 w-full min-w-0 rounded-xl border-slate-300 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500">
                </div>

                <div class="min-w-0">
                    <label for="report-to" class="mb-1.5 block text-xs font-semibold text-slate-600">
                        To Date
                    </label>

                    <input id="report-to"
                        type="date"
                        name="to"
                        value="{{ request('to', $to->format('Y-m-d')) }}"
                        class="block min-h-11 w-full min-w-0 rounded-xl border-slate-300 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500">
                </div>

                <div class="grid grid-cols-[minmax(0,1fr)_48px] gap-2 sm:col-span-2 lg:col-span-1">
                    <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                        <i class="fas fa-filter" aria-hidden="true"></i>
                        Apply Filters
                    </button>

                    <a href="{{ route('technician.reports.maintenance') }}"
                        aria-label="Reset date filters"
                        title="Reset Filters"
                        class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-sky-500">

                        <i class="fas fa-rotate-left" aria-hidden="true"></i>
                    </a>
                </div>
            </form>
        </section>

        {{-- REPORTING PERIOD --}}

        <div class="flex min-w-0 items-start gap-3 rounded-xl border border-sky-100 bg-sky-50/70 px-3 py-3 sm:items-center sm:px-4">
            <i class="fas fa-calendar-days mt-0.5 text-sky-600 sm:mt-0" aria-hidden="true"></i>

            <div class="min-w-0">
                <p class="text-xs font-medium text-slate-500">Reporting Period</p>

                <p class="mt-1 break-words text-sm font-semibold text-slate-800">
                    {{ $from->format('M d, Y') }}
                    <span class="mx-1 text-slate-400">—</span>
                    {{ $to->format('M d, Y') }}
                </p>
            </div>
        </div>

        {{-- PERFORMANCE OVERVIEW --}}

        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-4 py-4 sm:px-5">
                <h2 class="text-base font-bold text-slate-900">Performance Overview</h2>
                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Completed visits during the selected period and current assigned workload.
                </p>
            </div>

            <div class="grid grid-cols-1 divide-y divide-slate-100 sm:grid-cols-2 sm:divide-y-0 xl:grid-cols-4">

                {{-- ACCOMPLISHED --}}

                <article class="min-w-0 p-4 sm:border-b sm:border-r sm:p-5 xl:border-b-0">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600">
                            <i class="fas fa-circle-check" aria-hidden="true"></i>
                        </span>
                        <h3 class="text-xs font-semibold text-slate-500 sm:text-sm">Accomplished Visits</h3>
                    </div>

                    <p class="mt-3 break-words text-3xl font-bold tabular-nums text-slate-900">
                        {{ $accomplishedCount }}
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Completed during selected period
                    </p>
                </article>

                {{-- COMPLETION RATE --}}

                <article class="min-w-0 p-4 sm:border-b sm:p-5 xl:border-b-0 xl:border-r">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                            <i class="fas fa-chart-line" aria-hidden="true"></i>
                        </span>
                        <h3 class="text-xs font-semibold text-slate-500 sm:text-sm">Completion Rate</h3>
                    </div>

                    <p class="mt-3 break-words text-3xl font-bold tabular-nums text-slate-900">
                        {{ number_format((float) $completionRate, 1) }}%
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Completed visits compared with completed visits and approved current workload
                    </p>
                </article>

                {{-- AVERAGE COMPLETION --}}

                <article class="min-w-0 p-4 sm:border-r sm:p-5">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                            <i class="fas fa-stopwatch" aria-hidden="true"></i>
                        </span>
                        <h3 class="text-xs font-semibold text-slate-500 sm:text-sm">Avg. Completion Time</h3>
                    </div>

                    <div class="mt-3 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                        <p class="break-words text-3xl font-bold tabular-nums text-slate-900">
                            @if ($averageCompletionMinutes === null)
                                N/A
                            @elseif ($averageCompletionMinutes < 60)
                                {{ number_format($averageCompletionMinutes, 1) }}
                            @else
                                {{ number_format($averageCompletionMinutes / 60, 2) }}
                            @endif
                        </p>

                        <span class="text-xs text-slate-500">
                            @if ($averageCompletionMinutes === null)
                                No data
                            @elseif ($averageCompletionMinutes < 60)
                                mins
                            @else
                                hrs
                            @endif
                        </span>
                    </div>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Actual start-to-completion duration
                    </p>
                </article>

                {{-- PENDING WORKLOAD --}}

                <article class="min-w-0 p-4 sm:p-5">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <i class="fas fa-clock-rotate-left" aria-hidden="true"></i>
                        </span>
                        <h3 class="text-xs font-semibold text-slate-500 sm:text-sm">Pending Workload</h3>
                    </div>

                    <p class="mt-3 break-words text-3xl font-bold tabular-nums text-slate-900">
                        {{ $pendingWorkloadCount }}
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Pending visits counted by the summary
                    </p>
                </article>
            </div>
        </section>

        {{-- CHARTS --}}

        <section class="grid min-w-0 grid-cols-1 gap-4 xl:grid-cols-2">

            {{-- VERIFIED URGENCY --}}

            <div class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-start justify-between gap-3 border-b border-slate-100 px-4 py-4 sm:px-5">
                    <div class="min-w-0">
                        <h2 class="font-bold text-slate-900">Urgency Overview</h2>
                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Urgency distribution of current active complaints.
                        </p>
                    </div>

                    <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold tabular-nums text-slate-700">
                        {{ $urgencyTotal }}
                    </span>
                </div>

                <div class="space-y-5 p-4 sm:p-5">
                    @foreach ($urgencyBreakdown as $urgency => $count)
                        @php
                            $urgencyUpper = strtoupper((string) $urgency);
                            $color = $urgencyColors[$urgencyUpper] ?? 'bg-slate-400';
                            $percentage = $urgencyTotal > 0 ? ($count / $urgencyTotal) * 100 : 0;
                        @endphp

                        <div class="min-w-0">
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <div class="flex min-w-0 items-center gap-2">
                                    <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $color }}"></span>
                                    <span class="break-words text-sm font-medium text-slate-700">
                                        {{ $urgency }}
                                    </span>
                                </div>

                                <div class="flex shrink-0 items-center gap-3">
                                    <span class="text-xs text-slate-500">
                                        {{ number_format($percentage, 0) }}%
                                    </span>
                                    <span class="min-w-5 text-right text-sm font-bold tabular-nums text-slate-900">
                                        {{ $count }}
                                    </span>
                                </div>
                            </div>

                            <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full {{ $color }} transition-all duration-300"
                                    style="width: {{ min(100, $percentage) }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if ($urgencyTotal == 0)
                        <div class="rounded-xl bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                            No current active complaints to assess.
                        </div>
                    @endif
                </div>
            </div>

            {{-- WORK STATUS --}}

            <div class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-4 py-4 sm:px-5">
                    <h2 class="font-bold text-slate-900">Work Order Status</h2>
                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Distribution of maintenance visits assigned to you.
                    </p>
                </div>

                <div class="grid min-w-0 grid-cols-1 items-center gap-5 p-4 sm:p-5 md:grid-cols-2">

                    {{-- DOUGHNUT CHART --}}

                    <div class="mx-auto w-full max-w-[220px]">
                        <div class="relative aspect-square w-full">
                            <canvas id="workStatusChart"
                                role="img"
                                aria-label="Doughnut chart showing maintenance work order status counts">
                            </canvas>

                            <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                                <div class="max-w-[75%] text-center">
                                    <p class="break-words text-3xl font-bold tabular-nums text-slate-900">
                                        {{ $workStatusTotal }}
                                    </p>
                                    <p class="mt-1 text-[10px] font-semibold uppercase tracking-wide text-slate-500 sm:text-[11px]">
                                        Counted Visits
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- LEGEND --}}

                    <div class="min-w-0 space-y-2.5">
                        @foreach ($statusBreakdown as $status => $count)
                            <div class="flex min-w-0 items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/70 px-3 py-3 sm:px-4">
                                <div class="flex min-w-0 items-center gap-2.5">
                                    <span class="h-3 w-3 shrink-0 rounded-full {{ $statusColors[$status] ?? 'bg-slate-400' }}"></span>
                                    <span class="break-words text-sm text-slate-600">
                                        {{ $status }}
                                    </span>
                                </div>

                                <span class="shrink-0 text-base font-bold tabular-nums text-slate-900">
                                    {{ $count }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- CURRENT WORK --}}

        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-start justify-between gap-3 border-b border-slate-100 px-4 py-4 sm:px-5">
                <div class="min-w-0">
                    <h2 class="font-bold text-slate-900">Current Work</h2>
                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Complaints with scheduled or in-progress maintenance.
                    </p>
                </div>

                <span class="shrink-0 rounded-full bg-sky-50 px-3 py-1 text-sm font-bold tabular-nums text-sky-700">
                    {{ $currentComplaints->count() }}
                </span>
            </div>

            {{-- DESKTOP TABLE --}}

            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 xl:px-5">Complaint</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 xl:px-5">Consumer</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 xl:px-5">Complaint Type</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 xl:px-5">Status / Schedule</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 xl:px-5">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($currentComplaints as $complaint)
                            @php
                                $urgency = strtoupper(trim((string) ($complaint->aiAnalysis?->verified_urgency_level ?? '')));

                                $urgency = in_array($urgency, ['HIGH', 'MODERATE', 'LOW'], true)
                                    ? $urgency
                                    : 'NOT ASSESSED';

                                $statusClass = match ($complaint->status) {
                                    'Assigned' => 'border-blue-200 bg-blue-50 text-blue-700',
                                    'In Progress' => 'border-amber-200 bg-amber-50 text-amber-700',
                                    default => 'border-slate-200 bg-slate-50 text-slate-600',
                                };

                                $currentOrder = $currentWorkOrders->get($complaint->id);
                            @endphp

                            <tr class="transition hover:bg-slate-50">
                                <td class="break-words px-4 py-4 xl:px-5">
                                    <p class="font-bold text-sky-700">
                                        {{ $complaint->complaint_no }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $urgency }} Urgency
                                    </p>
                                </td>

                                <td class="break-words px-4 py-4 xl:px-5">
                                    <p class="font-medium text-slate-900">
                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}
                                    </p>
                                    @if ($complaint->consumer?->account_number)
                                        <p class="mt-1 break-all text-xs text-slate-500">
                                            {{ $complaint->consumer->account_number }}
                                        </p>
                                    @endif
                                </td>

                                <td class="break-words px-4 py-4 text-slate-700">
                                    {{ $complaint->category?->name ?? 'Uncategorized' }}
                                </td>

                                <td class="px-4 py-4 xl:px-5">
                                    <span class="inline-flex max-w-full rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $complaint->status }}
                                    </span>

                                    @if ($currentOrder)
                                        <p class="mt-2 break-words text-xs font-semibold {{ $currentOrder->approval_status === 'Approved' ? 'text-emerald-700' : 'text-violet-700' }}">
                                            {{ $currentOrder->approval_status === 'Approved' ? 'Approved' : 'Draft — Awaiting Approval' }}
                                            · Work Order #{{ $currentOrder->id }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $currentOrder->schedule?->schedule_date?->format('M d, Y') ?? 'Date pending' }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-center xl:px-5">
                                    <a href="{{ route('technician.complaints.show', $complaint) }}"
                                        aria-label="View complaint {{ $complaint->complaint_no }}"
                                        title="View Complaint"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-sky-100 bg-sky-50 text-sky-600 transition hover:bg-sky-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                                    No scheduled or in-progress maintenance complaints.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE AND TABLET CARDS --}}

            <div class="divide-y divide-slate-100 lg:hidden">
                @forelse ($currentComplaints as $complaint)
                    @php
                        $urgency = strtoupper(trim((string) ($complaint->aiAnalysis?->verified_urgency_level ?? '')));

                        $urgency = in_array($urgency, ['HIGH', 'MODERATE', 'LOW'], true)
                            ? $urgency
                            : 'NOT ASSESSED';

                        $statusClass = match ($complaint->status) {
                            'Assigned' => 'border-blue-200 bg-blue-50 text-blue-700',
                            'In Progress' => 'border-amber-200 bg-amber-50 text-amber-700',
                            default => 'border-slate-200 bg-slate-50 text-slate-600',
                        };

                        $currentOrder = $currentWorkOrders->get($complaint->id);
                    @endphp

                    <article class="min-w-0 space-y-3 p-4 sm:p-5">
                        <div class="flex min-w-0 items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="break-words text-sm font-bold text-sky-700">
                                    {{ $complaint->complaint_no }}
                                </p>
                                <p class="mt-1 break-words text-xs text-slate-500">
                                    {{ $complaint->category?->name ?? 'Uncategorized' }}
                                </p>
                            </div>

                            <span class="shrink-0 rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $statusClass }}">
                                {{ $complaint->status }}
                            </span>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Consumer</p>
                            <p class="mt-1 break-words text-sm font-medium text-slate-800">
                                {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}
                            </p>

                            @if ($complaint->consumer?->account_number)
                                <p class="mt-1 break-all text-xs text-slate-500">
                                    Account: {{ $complaint->consumer->account_number }}
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-700">
                                {{ $urgency }} Urgency
                            </span>
                        </div>

                        @if ($currentOrder)
                            <div class="rounded-xl border border-slate-100 p-3">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                    Work Order #{{ $currentOrder->id }}
                                </p>

                                <p class="mt-1 break-words text-sm font-semibold {{ $currentOrder->approval_status === 'Approved' ? 'text-emerald-700' : 'text-violet-700' }}">
                                    {{ $currentOrder->approval_status === 'Approved' ? 'Approved' : 'Draft — Awaiting Approval' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Schedule:
                                    {{ $currentOrder->schedule?->schedule_date?->format('M d, Y') ?? 'Date pending' }}
                                </p>
                            </div>
                        @endif

                        <a href="{{ route('technician.complaints.show', $complaint) }}"
                            class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-sky-100 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-100 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                            View Complaint
                        </a>
                    </article>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-slate-500">
                        No current maintenance work.
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ACCOMPLISHED WORK --}}

        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex min-w-0 flex-col gap-3 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <div class="min-w-0">
                    <h2 class="font-bold text-slate-900">Accomplished Work</h2>
                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Unique complaints with completed maintenance visits during the selected period.
                    </p>
                </div>

                <span class="self-start rounded-full bg-green-50 px-3 py-1.5 text-xs font-bold text-green-700 sm:shrink-0 sm:text-sm">
                    {{ $completedComplaintsList->count() }} Complaints
                </span>
            </div>

            {{-- DESKTOP TABLE --}}

            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 xl:px-5">Complaint</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 xl:px-5">Consumer</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 xl:px-5">Complaint Type</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 xl:px-5">Accomplished</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 xl:px-5">Review</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 xl:px-5">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($completedComplaintsList as $complaint)
                            @php
                                $reviewStatus = $complaint->maintenanceReport?->review_status ?? 'Not Submitted';

                                $reviewClass = match ($reviewStatus) {
                                    'Approved' => 'border-green-200 bg-green-50 text-green-700',
                                    'Pending Review' => 'border-amber-200 bg-amber-50 text-amber-700',
                                    'Returned' => 'border-red-200 bg-red-50 text-red-700',
                                    default => 'border-slate-200 bg-slate-50 text-slate-600',
                                };
                            @endphp

                            <tr class="transition hover:bg-slate-50">
                                <td class="break-words px-4 py-4 xl:px-5">
                                    <p class="font-bold text-sky-700">{{ $complaint->complaint_no }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $complaint->status }}</p>
                                </td>

                                <td class="break-words px-4 py-4 xl:px-5">
                                    <p class="font-medium text-slate-900">
                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}
                                    </p>

                                    @if ($complaint->consumer?->account_number)
                                        <p class="mt-1 break-all text-xs text-slate-500">
                                            {{ $complaint->consumer->account_number }}
                                        </p>
                                    @endif
                                </td>

                                <td class="break-words px-4 py-4 text-slate-700">
                                    {{ $complaint->category?->name ?? 'Uncategorized' }}
                                </td>

                                <td class="px-4 py-4 text-slate-700 xl:px-5">
                                    {{ $completedVisitDates->get($complaint->id)?->format('M d, Y') ?? '—' }}
                                </td>

                                <td class="px-4 py-4 xl:px-5">
                                    <span class="inline-flex max-w-full rounded-full border px-2.5 py-1 text-xs font-semibold {{ $reviewClass }}">
                                        {{ $reviewStatus }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 text-center xl:px-5">
                                    <a href="{{ route('technician.complaints.show', $complaint) }}"
                                        aria-label="View complaint {{ $complaint->complaint_no }}"
                                        title="View Complaint"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-sky-100 bg-sky-50 text-sky-600 transition hover:bg-sky-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
                                    No accomplished maintenance work for this reporting period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE AND TABLET CARDS --}}

            <div class="divide-y divide-slate-100 lg:hidden">
                @forelse ($completedComplaintsList as $complaint)
                    @php
                        $reviewStatus = $complaint->maintenanceReport?->review_status ?? 'Not Submitted';

                        $reviewClass = match ($reviewStatus) {
                            'Approved' => 'bg-green-50 text-green-700',
                            'Pending Review' => 'bg-amber-50 text-amber-700',
                            'Returned' => 'bg-red-50 text-red-700',
                            default => 'bg-slate-100 text-slate-600',
                        };
                    @endphp

                    <article class="min-w-0 space-y-3 p-4 sm:p-5">
                        <div class="flex min-w-0 items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="break-words text-sm font-bold text-sky-700">
                                    {{ $complaint->complaint_no }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Completed:
                                    {{ $completedVisitDates->get($complaint->id)?->format('M d, Y') ?? 'Date unavailable' }}
                                </p>
                            </div>

                            <span class="max-w-[55%] shrink-0 break-words rounded-full px-2.5 py-1 text-center text-[11px] font-semibold {{ $reviewClass }}">
                                {{ $reviewStatus }}
                            </span>
                        </div>

                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                Complaint Type
                            </p>
                            <p class="mt-1 break-words text-sm font-semibold text-slate-900">
                                {{ $complaint->category?->name ?? 'Uncategorized' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                Consumer
                            </p>
                            <p class="mt-1 break-words text-sm text-slate-700">
                                {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}
                            </p>

                            @if ($complaint->consumer?->account_number)
                                <p class="mt-1 break-all text-xs text-slate-500">
                                    Account: {{ $complaint->consumer->account_number }}
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-3">
                            <span class="text-xs text-slate-500">
                                Status: {{ $complaint->status }}
                            </span>

                            <a href="{{ route('technician.complaints.show', $complaint) }}"
                                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg px-2 py-2 text-sm font-semibold text-sky-700 hover:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-sky-500">
                                View Complaint
                                <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-slate-500">
                        No accomplished maintenance work for this reporting period.
                    </div>
                @endforelse
            </div>
        </section>

        {{-- MAINTENANCE PARTICIPATION --}}

        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-4 py-4 sm:px-5">
                <h2 class="text-base font-bold text-slate-900 sm:text-lg">
                    My Maintenance Participation
                </h2>
                <p class="mt-1 text-xs leading-5 text-slate-500 sm:text-sm">
                    Work you actually participated in, including handovers and unfinished visits.
                    Handover records do not increase your accomplished-visit count.
                </p>
            </div>

            {{-- DESKTOP PARTICIPATION TABLE --}}

            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500">Complaint / Visit</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500">Participation</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500">Started</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500">Ended</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500">Contribution</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($participationHistory as $participation)
                            <tr class="align-top hover:bg-slate-50">
                                <td class="break-words px-4 py-4 font-semibold text-sky-700">
                                    {{ $participation->workOrder?->complaint?->complaint_no ?? 'Complaint' }}
                                    <p class="mt-1 text-xs font-normal text-slate-500">
                                        WO #{{ $participation->maintenance_work_order_id }}
                                        · Visit #{{ $participation->workOrder?->visit_number ?? '—' }}
                                    </p>
                                </td>

                                <td class="break-words px-4 py-4 text-slate-700">
                                    {{ $participation->status }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-xs text-slate-600">
                                    {{ $participation->started_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? '—' }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4 text-xs text-slate-600">
                                    {{ $participation->ended_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? '—' }}
                                </td>

                                <td class="max-w-sm whitespace-pre-line break-words px-4 py-4 text-slate-600">
                                    {{ $participation->contribution_notes ?: 'Not specified' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                    No participation records in this date range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE PARTICIPATION CARDS --}}

            <div class="divide-y divide-slate-100 lg:hidden">
                @forelse ($participationHistory as $participation)
                    <article class="min-w-0 space-y-3 p-4 sm:p-5">
                        <div class="flex min-w-0 items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="break-words text-sm font-bold text-sky-700">
                                    {{ $participation->workOrder?->complaint?->complaint_no ?? 'Complaint' }}
                                </p>
                                <p class="mt-1 break-words text-xs text-slate-500">
                                    Work Order #{{ $participation->maintenance_work_order_id }}
                                    · Visit #{{ $participation->workOrder?->visit_number ?? '—' }}
                                </p>
                            </div>

                            <span class="max-w-[50%] shrink-0 break-words rounded-full bg-sky-50 px-2.5 py-1 text-center text-[11px] font-semibold text-sky-700">
                                {{ $participation->status }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 gap-3 rounded-xl bg-slate-50 p-3 sm:grid-cols-2">
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                    Started
                                </p>
                                <p class="mt-1 break-words text-xs font-medium text-slate-700">
                                    {{ $participation->started_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'Not recorded' }}
                                </p>
                            </div>

                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                    Ended
                                </p>
                                <p class="mt-1 break-words text-xs font-medium text-slate-700">
                                    {{ $participation->ended_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'Not recorded' }}
                                </p>
                            </div>
                        </div>

                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                Contribution
                            </p>
                            <p class="mt-1 whitespace-pre-line break-words text-sm leading-6 text-slate-600">
                                {{ $participation->contribution_notes ?: 'Not specified' }}
                            </p>
                        </div>
                    </article>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-slate-500">
                        No participation records in this date range.
                    </div>
                @endforelse
            </div>
        </section>

    </div>

    {{-- CHART.JS --}}

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('workStatusChart');

            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            const labels = @json($statusBreakdown->keys()->values());
            const values = @json($statusBreakdown->values()->values());

            const colors = [
                '#8b5cf6',
                '#3b82f6',
                '#f59e0b',
                '#f97316',
                '#22c55e'
            ];

            const hasData = values.some(value => Number(value) > 0);

            new Chart(canvas, {
                type: 'doughnut',

                data: {
                    labels: hasData ? labels : ['No Work'],
                    datasets: [{
                        data: hasData ? values : [1],
                        backgroundColor: hasData ? colors : ['#e2e8f0'],
                        borderWidth: 0,
                        hoverOffset: hasData ? 4 : 0
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            enabled: hasData,

                            callbacks: {
                                label: function (context) {
                                    return ' ' + context.label + ': ' + context.raw;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

@endsection
