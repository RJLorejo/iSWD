
@extends('customer-service.layouts.app')

@section('title', 'Reports & Analytics')

@section('content')

@php
    $statusLabels = [
        'Pending',
        'Verified',
        'CS Processing',
        'For Maintenance',
        'Assigned',
        'In Progress',
        'Completed',
        'Closed',
        'Rejected',
    ];

    $statusColors = [
        'Pending' => 'bg-amber-500',
        'Verified' => 'bg-sky-500',
        'CS Processing' => 'bg-indigo-500',
        'For Maintenance' => 'bg-cyan-500',
        'Assigned' => 'bg-blue-500',
        'In Progress' => 'bg-violet-500',
        'Completed' => 'bg-emerald-500',
        'Closed' => 'bg-green-600',
        'Rejected' => 'bg-rose-500',
    ];

    $periodLabels = [
        'day' => 'Today',
        'week' => 'This Week',
        'month' => 'This Month',
        'year' => 'This Year',
        'custom' => 'Custom Range',
    ];

    $trendTotal = collect($trend)->sum('count');

    $maxCategory = max(
        1,
        $categoryStats->max('total') ?? 0
    );

    $maxDivision = max(
        1,
        $divisionStats->max('total') ?? 0
    );

    $ratedFeedback = collect(range(1, 5))
        ->sum(fn ($rating) => (int) ($ratingStats[$rating] ?? 0));

    $positiveFeedback =
        (int) ($ratingStats[4] ?? 0)
        + (int) ($ratingStats[5] ?? 0);

    $positiveRate = $ratedFeedback > 0
        ? round($positiveFeedback / $ratedFeedback * 100, 1)
        : 0;
@endphp

<div class="w-full min-w-0 max-w-7xl mx-auto space-y-5 pb-10">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

        <div class="min-w-0">
            <div class="flex items-center gap-2 text-sm font-medium text-sky-700">
                <i class="fas fa-chart-line"></i>
                Customer Service
            </div>

            <h1 class="mt-1 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Reports & Analytics
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Monitor complaint activity, service trends, and consumer satisfaction.
            </p>
        </div>

        <a
            href="{{ route('customer-service.reports.print', request()->query()) }}"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800"
        >
            <i class="fas fa-print"></i>
            Print Report
        </a>
    </div>

    {{-- FILTERS --}}
    <form
        id="reportsFilterForm"
        method="GET"
        action="{{ route('customer-service.reports.index') }}"
        class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm"
    >

        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="font-semibold text-slate-900">
                    <i class="fas fa-filter mr-2 text-sky-600"></i>
                    Report Filters
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Select the reporting period, trend interval, and records you want to analyze.
                </p>
            </div>

            <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                {{ $periodLabels[$period] ?? 'This Month' }}
            </span>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-7">

            <div>
                <label for="reportPeriod"
                       class="mb-1.5 block text-xs font-semibold text-slate-600">
                    Time Period
                </label>

                <select
                    id="reportPeriod"
                    name="period"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500"
                >
                    @foreach ($periodLabels as $value => $label)
                        <option value="{{ $value }}" @selected($period === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="divisionFilter"
                       class="mb-1.5 block text-xs font-semibold text-slate-600">
                    Division
                </label>

                <select
                    id="divisionFilter"
                    name="division_id"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500"
                >
                    <option value="">All Divisions</option>

                    @foreach ($divisions as $division)
                        <option
                            value="{{ $division->id }}"
                            @selected((string) ($filters['division_id'] ?? '') === (string) $division->id)
                        >
                            {{ $division->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="statusFilter"
                       class="mb-1.5 block text-xs font-semibold text-slate-600">
                    Complaint Status
                </label>

                <select
                    id="statusFilter"
                    name="status"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500"
                >
                    <option value="">All Statuses</option>

                    @foreach ($statusLabels as $status)
                        <option
                            value="{{ $status }}"
                            @selected(($filters['status'] ?? '') === $status)
                        >
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="trendInterval"
                       class="mb-1.5 block text-xs font-semibold text-slate-600">
                    Trend Grouping
                </label>

                <select
                    id="trendInterval"
                    name="trend_interval"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500"
                >
                    <option value="month" @selected($trendInterval === 'month')>
                        Monthly
                    </option>
                    <option value="year" @selected($trendInterval === 'year')>
                        Yearly
                    </option>
                </select>
            </div>

            <div>
                <label for="trendDateFrom"
                       class="mb-1.5 block text-xs font-semibold text-slate-600">
                    Trend Start Date
                </label>

                <input
                    type="date"
                    id="trendDateFrom"
                    name="trend_date_from"
                    value="{{ $filters['trend_date_from'] ?? '' }}"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500"
                >
            </div>

            <div>
                <label for="trendDateTo"
                       class="mb-1.5 block text-xs font-semibold text-slate-600">
                    Trend End Date
                </label>

                <input
                    type="date"
                    id="trendDateTo"
                    name="trend_date_to"
                    value="{{ $filters['trend_date_to'] ?? '' }}"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500"
                >
            </div>

            <div class="flex items-end gap-2">
                <button
                    type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800"
                >
                    <i class="fas fa-magnifying-glass"></i>
                    Apply
                </button>

                <a
                    href="{{ route('customer-service.reports.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-600 transition hover:bg-slate-50"
                    title="Reset filters"
                >
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </div>

        <div
            id="customDateFields"
            class="mt-4 grid grid-cols-1 gap-3 border-t border-slate-100 pt-4 sm:grid-cols-2"
            style="{{ $period === 'custom' ? '' : 'display:none;' }}"
        >
            <div>
                <label for="dateFrom"
                       class="mb-1.5 block text-xs font-semibold text-slate-600">
                    Start Date
                </label>

                <input
                    type="date"
                    id="dateFrom"
                    name="date_from"
                    value="{{ $filters['date_from'] ?? '' }}"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500"
                >
            </div>

            <div>
                <label for="dateTo"
                       class="mb-1.5 block text-xs font-semibold text-slate-600">
                    End Date
                </label>

                <input
                    type="date"
                    id="dateTo"
                    name="date_to"
                    value="{{ $filters['date_to'] ?? '' }}"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500"
                >
            </div>
        </div>

        <p class="mt-2 text-xs text-slate-500">
            Leave trend dates blank to use the default window: the latest 12 months for Monthly or 5 years for Yearly. Trend dates are independent of the summary period.
        </p>

        @if ($errors->any())
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-500">
            <i class="far fa-calendar"></i>
            <span>
                {{ $start->format('M d, Y') }}
                —
                {{ $end->format('M d, Y') }}
            </span>

            <span class="text-slate-300">|</span>

            <span>Philippine Time</span>
        </div>
    </form>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">

        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <i class="fas fa-file-lines"></i>
                </span>
            </div>

            <p class="mt-4 text-2xl sm:text-3xl font-bold text-slate-900">
                {{ number_format($total) }}
            </p>

            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Total Complaints
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                <i class="fas fa-clock"></i>
            </span>

            <p class="mt-4 text-2xl sm:text-3xl font-bold text-slate-900">
                {{ number_format($activeCount) }}
            </p>

            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Active Complaints
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                <i class="fas fa-circle-check"></i>
            </span>

            <p class="mt-4 text-2xl sm:text-3xl font-bold text-slate-900">
                {{ number_format($completedCount) }}
            </p>

            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Completed / Closed
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                <i class="fas fa-chart-pie"></i>
            </span>

            <p class="mt-4 text-2xl sm:text-3xl font-bold text-slate-900">
                {{ number_format($resolutionRate, 1) }}%
            </p>

            <p class="mt-1 text-xs sm:text-sm text-slate-500">
                Resolution Rate
            </p>
        </div>
    </div>

    {{-- COMPLAINT TREND --}}
    <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">

        <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">
                    Complaint Volume Trend
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Complaints submitted {{ $trendInterval === 'year' ? 'by year' : 'by month' }} from {{ $trendStart->format('M d, Y') }} to {{ $trendEnd->format('M d, Y') }}. Trend dates are independent of the report summary period.
                </p>
            </div>

            <span class="rounded-lg bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700">
                {{ number_format($trendTotal) }} Trend Total
            </span>
        </div>

        <div class="relative h-64 w-full min-w-0 sm:h-80">
            <canvas id="complaintTrendChart" role="img" aria-label="Line chart showing complaint submissions by selected trend interval"></canvas>

            <div
                id="chartLoading"
                class="absolute inset-0 flex items-center justify-center bg-white text-sm text-slate-400"
            >
                <i class="fas fa-spinner fa-spin mr-2"></i>
                Loading chart...
            </div>
        </div>

        {{-- FALLBACK --}}
        <div id="trendFallback" class="mt-4">
            <h3 class="mb-2 text-sm font-semibold text-slate-700">
                Auditable trend data
            </h3>

            <div class="max-h-72 overflow-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th scope="col" class="px-4 py-2.5">Interval</th>
                            <th scope="col" class="px-4 py-2.5 text-right">Complaints submitted</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                @forelse ($trend as $point)
                    <tr>
                        <th scope="row" class="px-4 py-2.5 font-medium text-slate-700">
                            {{ $point['label'] }}
                        </th>
                        <td class="px-4 py-2.5 text-right tabular-nums text-slate-700">
                            {{ number_format($point['count']) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-3 text-center text-slate-500">
                            No complaint submissions match the trend filters.
                        </td>
                    </tr>
                @endforelse
                    </tbody>
                    @if (count($trend) > 0)
                        <tfoot class="border-t border-slate-200 bg-slate-50 font-semibold text-slate-800">
                            <tr>
                                <th scope="row" class="px-4 py-2.5">Total in trend range</th>
                                <td class="px-4 py-2.5 text-right tabular-nums">
                                    {{ number_format($trendTotal) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <p class="mt-4 text-xs text-slate-400">
            Counts use complaint submission dates and the selected division/status filters; they are not completion counts.
        </p>
    </div>

    {{-- STATUS AND CATEGORY --}}
    <div class="grid min-w-0 grid-cols-1 gap-5 xl:grid-cols-2">

        {{-- STATUS --}}
        <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">

            <div class="mb-5">
                <h2 class="font-bold text-slate-900">
                    Complaint Status
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Current workflow distribution
                </p>
            </div>

            <div class="space-y-4">
                @forelse ($statusCounts as $status => $count)
                    @php
                        $percentage = $total > 0
                            ? round(($count / $total) * 100, 1)
                            : 0;
                    @endphp

                    <div>
                        <div class="mb-1.5 flex items-center justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-2">
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $statusColors[$status] ?? 'bg-slate-400' }}"></span>

                                <span class="truncate text-sm text-slate-600">
                                    {{ $status }}
                                </span>
                            </div>

                            <div class="flex shrink-0 items-center gap-3">
                                <span class="text-xs text-slate-400">
                                    {{ $percentage }}%
                                </span>

                                <span class="text-sm font-semibold text-slate-900">
                                    {{ $count }}
                                </span>
                            </div>
                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div
                                class="h-full rounded-full {{ $statusColors[$status] ?? 'bg-slate-400' }}"
                                style="width: {{ $percentage }}%"
                            ></div>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center">
                        <i class="fas fa-chart-simple mb-3 text-2xl text-slate-300"></i>
                        <p class="text-sm text-slate-500">
                            No complaint records available.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- CATEGORIES --}}
        <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">

            <div class="mb-5">
                <h2 class="font-bold text-slate-900">
                    Most Reported Complaint Types
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Top complaint categories in the selected period
                </p>
            </div>

            <div class="space-y-4">
                @forelse ($categoryStats as $category)
                    @php
                        $percentage = $total > 0
                            ? round(($category->total / $total) * 100, 1)
                            : 0;

                        $barWidth = round(
                            ($category->total / $maxCategory) * 100,
                            1
                        );
                    @endphp

                    <div>
                        <div class="mb-1.5 flex items-center justify-between gap-3">
                            <span class="min-w-0 truncate text-sm text-slate-600"
                                  title="{{ $category->name }}">
                                {{ $category->name }}
                            </span>

                            <span class="shrink-0 text-sm font-semibold text-slate-900">
                                {{ $category->total }}
                                <span class="ml-1 text-xs font-normal text-slate-400">
                                    ({{ $percentage }}%)
                                </span>
                            </span>
                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div
                                class="h-full rounded-full bg-indigo-500"
                                style="width: {{ $barWidth }}%"
                            ></div>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center text-sm text-slate-500">
                        No complaint categories found.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- DIVISIONS AND AI URGENCY --}}
    <div class="grid min-w-0 grid-cols-1 gap-5 xl:grid-cols-2">

        {{-- DIVISIONS --}}
        <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm">

            <h2 class="font-bold text-slate-900">
                Complaints by Division
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Distribution of submitted complaints
            </p>

            <div class="mt-5 space-y-4">
                @forelse ($divisionStats as $division)
                    @php
                        $divisionPercentage = $total > 0
                            ? round($division->total / $total * 100, 1)
                            : 0;

                        $divisionWidth = round(
                            $division->total / $maxDivision * 100,
                            1
                        );
                    @endphp

                    <div>
                        <div class="mb-1.5 flex items-center justify-between gap-3">
                            <span class="min-w-0 truncate text-sm text-slate-600">
                                {{ $division->name }}
                            </span>

                            <span class="shrink-0 text-sm font-semibold text-slate-900">
                                {{ $division->total }}
                                <span class="text-xs font-normal text-slate-400">
                                    ({{ $divisionPercentage }}%)
                                </span>
                            </span>
                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div
                                class="h-full rounded-full bg-sky-600"
                                style="width: {{ $divisionWidth }}%"
                            ></div>
                        </div>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-slate-500">
                        No division data available.
                    </p>
                @endforelse
            </div>
        </div>


{{-- AI URGENCY AND CUSTOMER SERVICE VERIFICATION --}}

<section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

    <div class="mb-4">
        <h2 class="text-lg font-bold text-slate-900">
            AI Urgency & Verification Analysis
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Compare the AI's initial urgency recommendation with the urgency
            verified by Customer Service. AI recommendations do not replace
            the authorized staff decision.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
            <p class="text-sm font-medium text-blue-800">
                AI-Assessed Complaints
            </p>
            <p class="mt-2 text-2xl font-bold text-blue-950">
                {{ number_format((int) ($urgencySummary->ai_assessed ?? 0)) }}
            </p>
        </div>

        <div class="rounded-xl border border-green-100 bg-green-50 p-4">
            <p class="text-sm font-medium text-green-800">
                Verified Urgency
            </p>
            <p class="mt-2 text-2xl font-bold text-green-950">
                {{ number_format((int) ($urgencySummary->verified ?? 0)) }}
            </p>
            <p class="mt-1 text-xs text-green-700">
                {{ number_format($urgencyVerificationRate, 1) }}% of AI-assessed complaints
            </p>
        </div>

        <div class="rounded-xl border border-amber-100 bg-amber-50 p-4">
            <p class="text-sm font-medium text-amber-800">
                Pending Verification
            </p>
            <p class="mt-2 text-2xl font-bold text-amber-950">
                {{ number_format((int) ($urgencySummary->pending_verification ?? 0)) }}
            </p>
        </div>

        <div class="rounded-xl border border-violet-100 bg-violet-50 p-4">
            <p class="text-sm font-medium text-violet-800">
                Urgency Changed
            </p>
            <p class="mt-2 text-2xl font-bold text-violet-950">
                {{ number_format((int) ($urgencySummary->urgency_changed ?? 0)) }}
            </p>
            <p class="mt-1 text-xs text-violet-700">
                Verified level differs from AI suggestion
            </p>
        </div>

    </div>

    <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">

        {{-- AI DISTRIBUTION --}}
        <div class="overflow-hidden rounded-xl border border-slate-200">
            <div class="border-b border-slate-200 bg-blue-50 px-4 py-3">
                <h3 class="font-semibold text-blue-950">
                    AI-Suggested Urgency
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">AI Suggestion</th>
                            <th class="px-4 py-3 text-right">Complaints</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($urgencyStats as $item)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-700">
                                    {{ $item->name }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-900">
                                    {{ number_format($item->total) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-5 text-center text-slate-500">
                                    No AI urgency data available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- VERIFIED DISTRIBUTION --}}
        <div class="overflow-hidden rounded-xl border border-slate-200">
            <div class="border-b border-slate-200 bg-green-50 px-4 py-3">
                <h3 class="font-semibold text-green-950">
                    Customer Service-Verified Urgency
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Verified Result</th>
                            <th class="px-4 py-3 text-right">Complaints</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($verifiedUrgencyStats as $item)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-700">
                                    {{ $item->name }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-900">
                                    {{ number_format($item->total) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-5 text-center text-slate-500">
                                    No verified urgency data available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <p class="mt-4 text-xs leading-5 text-slate-500">
        Pending Verification means an AI analysis exists but no verified urgency
        is recorded. No AI Analysis means the complaint has no linked AI analysis
        record. A changed urgency means the recorded verified level differs from
        the original AI suggestion.
    </p>

</section>

    </div>

    {{-- CONSUMER FEEDBACK --}}
    <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">

        <div class="mb-5">
            <h2 class="font-bold text-slate-900">
                Consumer Feedback Analysis
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Consumer ratings and feedback requiring follow-up
            </p>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

            <div class="rounded-xl bg-slate-50 p-4">
                <div class="flex items-center gap-2 text-sky-700">
                    <i class="fas fa-comments"></i>
                    <span class="text-xs font-semibold">
                        Total Feedback
                    </span>
                </div>

                <p class="mt-3 text-2xl font-bold text-slate-900">
                    {{ number_format($feedbackTotal) }}
                </p>
            </div>

            <div class="rounded-xl bg-slate-50 p-4">
                <div class="flex items-center gap-2 text-amber-600">
                    <i class="fas fa-star"></i>
                    <span class="text-xs font-semibold">
                        Average Rating
                    </span>
                </div>

                <p class="mt-3 text-2xl font-bold text-slate-900">
                    {{ $ratedFeedback > 0 ? number_format($averageRating, 2) : '—' }}
                    <span class="text-sm font-normal text-slate-400">
                        / 5
                    </span>
                </p>
            </div>

            <div class="rounded-xl bg-slate-50 p-4">
                <div class="flex items-center gap-2 text-rose-600">
                    <i class="fas fa-bell"></i>
                    <span class="text-xs font-semibold">
                        Follow-ups
                    </span>
                </div>

                <p class="mt-3 text-2xl font-bold text-slate-900">
                    {{ number_format($attentionCount) }}
                </p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">

            <div>
                <h3 class="mb-4 text-sm font-semibold text-slate-800">
                    Rating Distribution
                </h3>

                <div class="space-y-3">
                    @foreach ([5, 4, 3, 2, 1] as $rating)
                        @php
                            $ratingCount = (int) ($ratingStats[$rating] ?? 0);

                            $ratingPercentage = $ratedFeedback > 0
                                ? round($ratingCount / $ratedFeedback * 100, 1)
                                : 0;
                        @endphp

                        <div class="flex items-center gap-3">
                            <span class="w-10 shrink-0 text-xs text-slate-600">
                                {{ $rating }}
                                <i class="fas fa-star text-amber-400"></i>
                            </span>

                            <div class="h-2.5 flex-1 overflow-hidden rounded-full bg-slate-100">
                                <div
                                    class="h-full rounded-full bg-amber-400"
                                    style="width: {{ $ratingPercentage }}%"
                                ></div>
                            </div>

                            <span class="w-8 shrink-0 text-right text-xs font-semibold text-slate-700">
                                {{ $ratingCount }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col justify-center rounded-xl bg-sky-50 p-5">
                <div class="flex items-center gap-2 text-sky-700">
                    <i class="fas fa-face-smile"></i>

                    <span class="text-sm font-semibold">
                        Positive Feedback
                    </span>
                </div>

                <p class="mt-3 text-3xl font-bold text-sky-800">
                    {{ number_format($positiveRate, 1) }}%
                </p>

                <p class="mt-2 text-sm leading-relaxed text-sky-800/80">
                    Percentage of rated feedback with an overall rating of
                    4 or 5 stars during the selected period.
                </p>

                <p class="mt-3 text-xs text-sky-700">
                    Based on {{ number_format($ratedFeedback) }} rated responses.
                </p>
            </div>
        </div>
    </div>

    {{-- COMPLAINT RECORDS --}}
    <div class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
            <div>
                <h2 class="font-bold text-slate-900">
                    Detailed Complaint Records
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Complaints matching the selected report filters
                </p>
            </div>

            <span class="self-start rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                {{ number_format($total) }} Records
            </span>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="min-w-[760px] w-full text-left text-sm">

                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="whitespace-nowrap px-5 py-4 font-semibold">
                            Complaint No.
                        </th>

                        <th class="whitespace-nowrap px-5 py-4 font-semibold">
                            Submitted
                        </th>

                        <th class="px-5 py-4 font-semibold">
                            Division
                        </th>

                        <th class="px-5 py-4 font-semibold">
                            Complaint Type
                        </th>

                        <th class="whitespace-nowrap px-5 py-4 font-semibold">
                            Status
                        </th>

                        <th class="px-5 py-4 text-right font-semibold">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($records as $complaint)

                        <tr class="transition hover:bg-slate-50">

                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="font-semibold text-sky-700">
                                    {{ $complaint->complaint_no }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                {{ $complaint->created_at?->copy()->timezone('Asia/Manila')->format('M d, Y') ?? '—' }}

                                <div class="mt-1 text-xs text-slate-400">
                                    {{ $complaint->created_at?->copy()->timezone('Asia/Manila')->format('h:i A') }}
                                </div>
                            </td>

                            <td class="px-5 py-4 text-slate-600">
                                {{ $complaint->division?->name ?? '—' }}
                            </td>

                            <td class="px-5 py-4 text-slate-600">
                                {{ $complaint->category?->name ?? '—' }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                    {{ $complaint->status }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-right">
                                <a
                                    href="{{ route('customer-service.complaints.show', $complaint) }}"
                                    class="inline-flex items-center gap-1 text-xs font-semibold text-sky-700 hover:text-sky-900"
                                >
                                    View
                                    <i class="fas fa-arrow-up-right-from-square"></i>
                                </a>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                        <i class="fas fa-folder-open text-xl"></i>
                                    </span>

                                    <p class="mt-3 font-semibold text-slate-700">
                                        No complaint records found
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Try selecting a different period or filter.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($records->hasPages())
            <div class="border-t border-slate-100 p-4">
                {{ $records->links() }}
            </div>
        @endif
    </div>

    <p class="text-center text-xs text-slate-400">
        iSWD Customer Service Analytics
        •
        Generated {{ now('Asia/Manila')->format('M d, Y h:i A') }}
    </p>

</div>

{{-- CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>

<script>
(function () {
    function initializeReports() {

        // Custom date filters
        const periodSelect = document.getElementById('reportPeriod');
        const customDateFields = document.getElementById('customDateFields');

        if (periodSelect && customDateFields) {
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');

            const updateDateFields = function () {
                const isCustom = periodSelect.value === 'custom';

                customDateFields.style.display = isCustom ? 'grid' : 'none';

                customDateFields.querySelectorAll('input').forEach(function (input) {
                    input.disabled = !isCustom;
                });

                if (dateFrom && dateTo) {
                    dateTo.min = dateFrom.value || '';
                    dateFrom.max = dateTo.value || '';
                }
            };

            if (dateFrom) dateFrom.addEventListener('change', updateDateFields);
            if (dateTo) dateTo.addEventListener('change', updateDateFields);
            periodSelect.addEventListener('change', updateDateFields);
            updateDateFields();
        }

        // Keep trend date controls ordered and give the browser a clear validation cue.
        const trendDateFrom = document.getElementById('trendDateFrom');
        const trendDateTo = document.getElementById('trendDateTo');

        const updateTrendDateBounds = function () {
            if (trendDateFrom && trendDateTo) {
                trendDateTo.min = trendDateFrom.value || '';
                trendDateFrom.max = trendDateTo.value || '';
            }
        };

        if (trendDateFrom) trendDateFrom.addEventListener('change', updateTrendDateBounds);
        if (trendDateTo) trendDateTo.addEventListener('change', updateTrendDateBounds);
        updateTrendDateBounds();

        // Complaint volume chart
        const canvas = document.getElementById('complaintTrendChart');
        const loading = document.getElementById('chartLoading');
        const fallback = document.getElementById('trendFallback');

        if (!canvas) return;

        const trendData = @json($trend);

        const showFallback = function () {
            if (loading) loading.classList.add('hidden');
            canvas.style.display = 'none';
            if (fallback) fallback.classList.remove('hidden');
        };

        if (typeof Chart === 'undefined') {
            showFallback();
            return;
        }

        if (!Array.isArray(trendData) || trendData.length === 0) {
            showFallback();
            return;
        }

        try {
            const existing = Chart.getChart(canvas);

            if (existing) {
                existing.destroy();
            }

            new Chart(canvas, {
                type: 'line',

                data: {
                    labels: trendData.map(item => item.label),

                    datasets: [{
                        label: 'Complaints',
                        data: trendData.map(item => Number(item.count) || 0),
                        borderColor: '#0284c7',
                        backgroundColor: 'rgba(2, 132, 199, 0.10)',
                        borderWidth: 2.5,
                        tension: 0.3,
                        fill: true,
                        pointRadius: trendData.length > 40 ? 0 : 3,
                        pointHoverRadius: 5,
                        pointBackgroundColor: '#0284c7',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            backgroundColor: '#0f172a',
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    return 'Complaints: ' + context.parsed.y;
                                }
                            }
                        }
                    },

                    scales: {
                        x: {
                            grid: {
                                display: false
                            },

                            ticks: {
                                color: '#64748b',
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 8
                            }
                        },

                        y: {
                            beginAtZero: true,

                            ticks: {
                                precision: 0,
                                color: '#64748b'
                            },

                            grid: {
                                color: '#f1f5f9'
                            }
                        }
                    }
                }
            });

            if (loading) loading.classList.add('hidden');
            canvas.style.display = 'block';

        } catch (error) {
            console.error('Reports chart initialization failed:', error);
            showFallback();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeReports);
    } else {
        initializeReports();
    }
})();
</script>

@endsection
