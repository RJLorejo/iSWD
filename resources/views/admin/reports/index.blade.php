@extends('admin.layouts.app')

@section('title', 'System Reports')

@section('content')
<div class="min-h-screen bg-slate-50 px-3 py-5 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-6">

        {{-- HEADER --}}
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-cyan-700">
                    Sagay Water District
                </p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    System Reports
                </h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                    Monitor consumer accounts, complaint activity, AI analysis, customer feedback, and maintenance operations.
                </p>
            </div>

            <a href="{{ route('admin.reports.print', request()->only('date_from', 'date_to')) }}"
               target="_blank"
               rel="noopener"
               class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-cyan-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-800 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5h-2M6 14h12v7H6z"/>
                </svg>
                Print Report
            </a>
        </header>

        {{-- DATE FILTERS --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="mb-4">
                <h2 class="font-bold text-slate-900">Reporting Period</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Filter report data by record date.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.reports.index') }}">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:items-end">
                    <div>
                        <label for="date_from" class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Date from
                        </label>
                        <input id="date_from"
                               name="date_from"
                               type="date"
                               value="{{ $filters['date_from'] }}"
                               max="{{ $filters['date_to'] ?: now()->toDateString() }}"
                               class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-100">
                    </div>

                    <div>
                        <label for="date_to" class="mb-1.5 block text-sm font-semibold text-slate-700">
                            Date to
                        </label>
                        <input id="date_to"
                               name="date_to"
                               type="date"
                               value="{{ $filters['date_to'] }}"
                               min="{{ $filters['date_from'] }}"
                               max="{{ now()->toDateString() }}"
                               class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-100">
                    </div>

                    <button type="submit"
                            class="min-h-11 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Apply Filters
                    </button>

                    <a href="{{ route('admin.reports.index') }}"
                       class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Clear Filters
                    </a>
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

        {{-- CONSUMER OVERVIEW --}}
        <section>
            <div class="mb-3">
                <h2 class="text-lg font-bold text-slate-900">Consumer Overview</h2>
                <p class="text-sm text-slate-500">Registration and account verification.</p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6">
                @foreach ([
                    ['Total Consumers', $consumerStats['total'], 'All registrations'],
                    ['Active Accounts', $consumerStats['active'], 'Currently active'],
                    ['Inactive Accounts', $consumerStats['inactive'], 'Currently inactive'],
                    ['Verified', $consumerStats['verified'], 'Verified accounts'],
                    ['Pending Verification', $consumerStats['pending'], 'Awaiting verification'],
                    ['Rejected', $consumerStats['rejected'], 'Rejected registrations'],
                ] as [$label, $value, $description])
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <p class="text-xs font-medium leading-5 text-slate-500">{{ $label }}</p>
                        <p class="mt-2 text-2xl font-bold tabular-nums text-slate-900">
                            {{ number_format($value) }}
                        </p>
                        <p class="mt-1 text-xs text-slate-400">{{ $description }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- COMPLAINT SUMMARY --}}
        <section>
            <div class="mb-3">
                <h2 class="text-lg font-bold text-slate-900">Complaint Workflow</h2>
                <p class="text-sm text-slate-500">Current distribution of recorded complaint statuses in the selected period.</p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 xl:grid-cols-8">
                @foreach ([
                    ['Total Complaints', 'total'],
                    ['Pending', 'pending'],
                    ['Verified', 'verified'],
                    ['Assigned', 'assigned'],
                    ['In Progress', 'in_progress'],
                    ['Completed', 'completed'],
                    ['Closed', 'closed'],
                    ['Rejected', 'rejected'],
                ] as [$label, $key])
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <p class="text-xs font-medium leading-5 text-slate-500">{{ $label }}</p>
                        <p class="mt-2 text-2xl font-bold tabular-nums text-slate-900">
                            {{ number_format($complaintStats[$key]) }}
                        </p>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- AI SUMMARY --}}
        <section>
            <div class="mb-3">
                <h2 class="text-lg font-bold text-slate-900">AI Complaint Analysis</h2>
                <p class="text-sm text-slate-500">Recorded urgency classifications and model confidence.</p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
                @foreach ([
                    ['AI Analyses', number_format($aiStats['total'])],
                    ['High Urgency', number_format($aiStats['high'])],
                    ['Medium Urgency', number_format($aiStats['medium'])],
                    ['Low Urgency', number_format($aiStats['low'])],
                    ['Ambiguous Cases', number_format($aiStats['ambiguous'])],
                    ['Average Confidence', number_format($aiStats['average_confidence'] * 100, 2) . '%'],
                    ['Average Urgency Score', number_format($aiStats['average_urgency_score'], 2)],
                ] as [$label, $value])
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <p class="text-sm text-slate-500">{{ $label }}</p>
                        <p class="mt-2 break-words text-2xl font-bold tabular-nums text-slate-900">{{ $value }}</p>
                    </article>
                @endforeach
            </div>

            <p class="mt-3 text-xs leading-5 text-slate-500">
                Confidence is shown as a percentage, assuming stored confidence values range from 0 to 1.
                Urgency values use recorded labels and are not recalculated by this report.
            </p>
        </section>

        {{-- ANALYTICS CHARTS --}}
        <section class="space-y-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Analytics Dashboard</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Visual summaries of complaint activity, AI urgency, and consumer experience.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">

                {{-- MONTHLY TREND --}}
                <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                    <div class="mb-4">
                        <h3 class="font-bold text-slate-900">Monthly Activity</h3>
                        <p class="mt-1 text-xs text-slate-500">Complaints submitted vs. work orders completed.</p>
                    </div>
                    <div class="relative h-64 sm:h-72">
                        <canvas id="monthlyTrendChart" role="img" aria-label="Monthly complaints and completed work orders chart"></canvas>
                    </div>
                    <p id="monthlyTrendEmpty" class="hidden py-8 text-center text-sm text-slate-500">
                        No monthly activity data available for this period.
                    </p>
                </article>

                {{-- COMPLAINT STATUS --}}
                <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                    <div class="mb-4">
                        <h3 class="font-bold text-slate-900">Complaint Status Distribution</h3>
                        <p class="mt-1 text-xs text-slate-500">Number of complaints in each workflow status.</p>
                    </div>
                    <div class="relative h-64 sm:h-72">
                        <canvas id="complaintStatusChart" role="img" aria-label="Complaint workflow status chart"></canvas>
                    </div>
                </article>

                {{-- AI URGENCY --}}
                <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                    <div class="mb-4">
                        <h3 class="font-bold text-slate-900">AI Urgency Distribution</h3>
                        <p class="mt-1 text-xs text-slate-500">Breakdown of recorded AI urgency labels.</p>
                    </div>
                    <div class="relative h-64 sm:h-72">
                        <canvas id="aiUrgencyChart" role="img" aria-label="AI urgency distribution doughnut chart"></canvas>
                    </div>
                    <p id="aiUrgencyEmpty" class="hidden py-8 text-center text-sm text-slate-500">
                        No AI urgency data available for this period.
                    </p>
                </article>

                {{-- FEEDBACK RATINGS --}}
                <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                    <div class="mb-4">
                        <h3 class="font-bold text-slate-900">Consumer Feedback Ratings</h3>
                        <p class="mt-1 text-xs text-slate-500">Average ratings on a five-point scale.</p>
                    </div>
                    <div class="relative h-64 sm:h-72">
                        <canvas id="feedbackRatingsChart" role="img" aria-label="Consumer feedback average ratings chart"></canvas>
                    </div>
                    @if (($feedbackStats['total'] ?? 0) == 0)
                        <p class="mt-2 text-center text-xs text-slate-400">
                            No feedback responses are recorded for this period.
                        </p>
                    @endif
                </article>
            </div>
        </section>

        {{-- FEEDBACK SUMMARY --}}
        <section>
            <div class="mb-3">
                <h2 class="text-lg font-bold text-slate-900">Consumer Feedback Summary</h2>
                <p class="text-sm text-slate-500">Ratings and reported resolution experience.</p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ([
                    ['Feedback Responses', number_format($feedbackStats['total'])],
                    ['Average Overall Rating', number_format($feedbackStats['average_overall'], 2) . ' / 5'],
                    ['Service Quality', number_format($feedbackStats['average_service_quality'], 2) . ' / 5'],
                    ['Response Time', number_format($feedbackStats['average_response_time'], 2) . ' / 5'],
                    ['Personnel Courtesy', number_format($feedbackStats['average_personnel_courtesy'], 2) . ' / 5'],
                    ['Resolved', number_format($feedbackStats['resolved'])],
                    ['Partially Resolved', number_format($feedbackStats['partially_resolved'])],
                    ['Not Resolved', number_format($feedbackStats['not_resolved'])],
                ] as [$label, $value])
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <p class="text-sm text-slate-500">{{ $label }}</p>
                        <p class="mt-2 text-2xl font-bold tabular-nums text-slate-900">{{ $value }}</p>
                    </article>
                @endforeach
            </div>

            {{-- LATEST FEEDBACK TABLE --}}
            <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h3 class="font-bold text-slate-900">Latest Feedback</h3>
                    <p class="mt-1 text-sm text-slate-500">Consumer comments and reported resolution outcomes.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3">Complaint</th>
                                <th class="whitespace-nowrap px-4 py-3">Consumer</th>
                                <th class="whitespace-nowrap px-4 py-3">Rating</th>
                                <th class="whitespace-nowrap px-4 py-3">Resolution</th>
                                <th class="px-4 py-3">Comments</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($latestFeedback as $feedback)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-800">
                                        {{ $feedback->complaint?->complaint_no ?? 'N/A' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                        {{ $feedback->consumer?->full_name ?? 'Consumer unavailable' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <span class="font-semibold text-amber-600">
                                            {{ $feedback->overall_rating }} / 5
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                        {{ $feedback->resolution_status }}
                                    </td>
                                    <td class="min-w-48 max-w-sm px-4 py-3 text-slate-600">
                                        {{ $feedback->comments ?: 'No comments' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                        No feedback recorded for this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- MAINTENANCE PERFORMANCE --}}
        <section>
            <div class="mb-3">
                <h2 class="text-lg font-bold text-slate-900">Maintenance Performance</h2>
                <p class="text-sm text-slate-500">Work orders recorded during the selected period.</p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6">
                @foreach ([
                    ['Total Work Orders', 'total'],
                    ['Completed', 'completed'],
                    ['In Progress', 'in_progress'],
                    ['Scheduled', 'scheduled'],
                    ['Pending Workload', 'pending_workload'],
                    ['Rescheduled', 'rescheduled'],
                    ['Cancelled', 'cancelled'],
                    ['Emergency Dispatches', 'emergency'],
                    ['Regular Dispatches', 'regular'],
                    ['Late Additions', 'late_additions'],
                    ['Approved', 'approved'],
                ] as [$label, $key])
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <p class="text-xs font-medium leading-5 text-slate-500">{{ $label }}</p>
                        <p class="mt-2 text-2xl font-bold tabular-nums text-slate-900">
                            {{ number_format($workOrderStats[$key]) }}
                        </p>
                    </article>
                @endforeach
            </div>

            {{-- WORK ORDER DETAILS TABLE --}}
            <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h3 class="font-bold text-slate-900">Work Order Details</h3>
                    <p class="mt-1 text-sm text-slate-500">Latest work orders in the selected period.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-3">Complaint</th>
                                <th class="whitespace-nowrap px-4 py-3">Dispatch</th>
                                <th class="whitespace-nowrap px-4 py-3">Work Order Status</th>
                                <th class="whitespace-nowrap px-4 py-3">Approval</th>
                                <th class="px-4 py-3">Technicians</th>
                                <th class="whitespace-nowrap px-4 py-3">Completed At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($workOrders as $workOrder)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-800">
                                        {{ $workOrder->complaint?->complaint_no ?? 'N/A' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        {{ $workOrder->dispatch_type ?: 'N/A' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                            {{ $workOrder->status }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                        {{ $workOrder->approval_status ?: 'N/A' }}
                                    </td>
                                    <td class="min-w-48 px-4 py-3 text-slate-600">
                                        {{ $workOrder->technicians->pluck('name')->filter()->join(', ') ?: 'Not assigned' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                        {{ $workOrder->completed_at?->format('M d, Y h:i A') ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                        No work orders found for this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-slate-500">
                        Showing the work orders returned by the report query.
                    </p>
                    <div>
                        {{ $workOrders->links() }}
                    </div>
                </div>
            </div>
        </section>

        {{-- COMPLAINT CATEGORIES --}}
        <section>
            <div class="mb-3">
                <h2 class="text-lg font-bold text-slate-900">Complaints by Category</h2>
                <p class="text-sm text-slate-500">Categories with the highest recorded complaint volume.</p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Category</th>
                                <th class="px-5 py-3 text-right">Complaints</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($complaintsByCategory as $category)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-5 py-3 font-medium text-slate-700">
                                        {{ $category->category_name }}
                                    </td>
                                    <td class="px-5 py-3 text-right font-semibold text-slate-900">
                                        {{ number_format($category->total) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-5 py-8 text-center text-slate-500">
                                        No complaint categories found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- REPORT NOTES --}}
        <footer class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs leading-5 text-slate-500">
            Report counts use each record's creation date unless otherwise specified.
            AI analysis uses analyzed date. Monthly completed work orders use completion date.
            Counts describe recorded data and do not independently establish service quality or AI accuracy.
        </footer>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js could not be loaded. Check your internet connection or Content Security Policy.');
        return;
    }

    const monthlyCanvas = document.getElementById('monthlyTrendChart');
    const statusCanvas = document.getElementById('complaintStatusChart');
    const urgencyCanvas = document.getElementById('aiUrgencyChart');
    const feedbackCanvas = document.getElementById('feedbackRatingsChart');

    const monthlyData = @json(collect($monthlyTrend)->values());
    const urgencyLabels = @json(collect($aiUrgencyBreakdown)->pluck('urgency')->values());
    const urgencyValues = @json(collect($aiUrgencyBreakdown)->pluck('total')->values());

    const complaintStatusLabels = [
        'Pending', 'Verified', 'Assigned', 'In Progress',
        'Completed', 'Closed', 'Rejected'
    ];

    const complaintStatusValues = [
        Number(@json($complaintStats['pending'])),
        Number(@json($complaintStats['verified'])),
        Number(@json($complaintStats['assigned'])),
        Number(@json($complaintStats['in_progress'])),
        Number(@json($complaintStats['completed'])),
        Number(@json($complaintStats['closed'])),
        Number(@json($complaintStats['rejected']))
    ];

    const feedbackValues = [
        Number(@json($feedbackStats['average_overall'])),
        Number(@json($feedbackStats['average_service_quality'])),
        Number(@json($feedbackStats['average_response_time'])),
        Number(@json($feedbackStats['average_personnel_courtesy']))
    ];

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        resizeDelay: 150,
        animation: {
            duration: 450
        },
        interaction: {
            intersect: false,
            mode: 'index'
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 18,
                    boxWidth: 8,
                    boxHeight: 8,
                    font: {
                        size: 11
                    }
                }
            },
            tooltip: {
                enabled: true,
                padding: 12,
                cornerRadius: 8
            }
        }
    };

    // 1. MONTHLY ACTIVITY LINE CHART
    if (monthlyCanvas) {
        const hasMonthlyData = Array.isArray(monthlyData) && monthlyData.length > 0;

        if (hasMonthlyData) {
            new Chart(monthlyCanvas, {
                type: 'line',
                data: {
                    labels: monthlyData.map(item => item.month),
                    datasets: [
                        {
                            label: 'Complaints',
                            data: monthlyData.map(item => Number(item.complaints || 0)),
                            borderColor: '#0891b2',
                            backgroundColor: 'rgba(8, 145, 178, 0.12)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointHoverRadius: 5
                        },
                        {
                            label: 'Completed Work Orders',
                            data: monthlyData.map(item => Number(item.completed_work_orders || 0)),
                            borderColor: '#16a34a',
                            backgroundColor: 'rgba(22, 163, 74, 0.08)',
                            fill: false,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }
                    ]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0,
                                autoSkip: true
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            grid: { color: '#f1f5f9' }
                        }
                    }
                }
            });
        } else {
            monthlyCanvas.classList.add('hidden');
            document.getElementById('monthlyTrendEmpty')?.classList.remove('hidden');
        }
    }

    // 2. COMPLAINT WORKFLOW BAR CHART
    if (statusCanvas) {
        new Chart(statusCanvas, {
            type: 'bar',
            data: {
                labels: complaintStatusLabels,
                datasets: [{
                    label: 'Complaints',
                    data: complaintStatusValues,
                    backgroundColor: [
                        '#f59e0b',
                        '#0ea5e9',
                        '#6366f1',
                        '#8b5cf6',
                        '#16a34a',
                        '#64748b',
                        '#e11d48'
                    ],
                    borderRadius: 6,
                    maxBarThickness: 35
                }]
            },
            options: {
                ...commonOptions,
                indexAxis: 'y',
                plugins: {
                    ...commonOptions.plugins,
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: '#f1f5f9' }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 3. AI URGENCY DOUGHNUT CHART
    if (urgencyCanvas) {
        const hasUrgencyData = Array.isArray(urgencyValues) &&
            urgencyValues.some(value => Number(value) > 0);

        if (hasUrgencyData) {
            new Chart(urgencyCanvas, {
                type: 'doughnut',
                data: {
                    labels: urgencyLabels,
                    datasets: [{
                        data: urgencyValues.map(value => Number(value)),
                        backgroundColor: [
                            '#dc2626',
                            '#f59e0b',
                            '#16a34a',
                            '#94a3b8',
                            '#6366f1',
                            '#0891b2'
                        ],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 7
                    }]
                },
                options: {
                    ...commonOptions,
                    cutout: '66%',
                    plugins: {
                        ...commonOptions.plugins,
                        legend: {
                            ...commonOptions.plugins.legend,
                            position: 'bottom'
                        }
                    }
                }
            });
        } else {
            urgencyCanvas.classList.add('hidden');
            document.getElementById('aiUrgencyEmpty')?.classList.remove('hidden');
        }
    }

    // 4. CONSUMER FEEDBACK RATINGS BAR CHART
    if (feedbackCanvas) {
        new Chart(feedbackCanvas, {
            type: 'bar',
            data: {
                labels: [
                    'Overall',
                    'Service Quality',
                    'Response Time',
                    'Personnel Courtesy'
                ],
                datasets: [{
                    label: 'Average Rating / 5',
                    data: feedbackValues,
                    backgroundColor: [
                        '#0891b2',
                        '#0d9488',
                        '#6366f1',
                        '#8b5cf6'
                    ],
                    borderRadius: 7,
                    maxBarThickness: 48
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    legend: { display: false },
                    tooltip: {
                        ...commonOptions.plugins.tooltip,
                        callbacks: {
                            label: function(context) {
                                return ' Average rating: ' +
                                    Number(context.raw || 0).toFixed(2) + ' / 5';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 0,
                            autoSkip: false,
                            font: { size: 10 }
                        }
                    },
                    y: {
                        min: 0,
                        max: 5,
                        ticks: { stepSize: 1 },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });
    }
});
</script>
@endpush
