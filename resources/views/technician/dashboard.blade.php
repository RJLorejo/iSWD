@extends('technician.layouts.app')

@section('title', 'Technician Dashboard')

@section('content')

    @php
        $filterUrl = fn(string $scope) => route('technician.complaints.index', ['scope' => $scope]);

        $metrics = [
            [
                'label' => 'Scheduled Visits',
                'value' => $assignedCount,
                'icon' => 'fa-calendar-check',
                'style' => 'bg-blue-50 text-blue-700',
                'description' => 'Awaiting maintenance',
                'scope' => 'scheduled',
            ],
            [
                'label' => 'In Progress',
                'value' => $inProgressCount,
                'icon' => 'fa-screwdriver-wrench',
                'style' => 'bg-amber-50 text-amber-700',
                'description' => 'Currently being handled',
                'scope' => 'in_progress',
            ],
            [
                'label' => 'Pending Workload',
                'value' => $pendingWorkloadCount,
                'icon' => 'fa-clock-rotate-left',
                'style' => 'bg-orange-50 text-orange-700',
                'description' => $historicalPendingCount . ' historical pending visits',
                'scope' => 'pending_workload',
            ],
            [
                'label' => 'Accomplished Visits',
                'value' => $completedCount,
                'icon' => 'fa-circle-check',
                'style' => 'bg-green-50 text-green-700',
                'description' => 'All-time completed visits',
                'scope' => 'completed',
            ],
        ];

        $insights = [
            [
                'label' => 'Active Visits',
                'value' => $activeCount,
                'description' => 'Scheduled + In Progress',
                'icon' => 'fa-list-check',
                'style' => 'bg-sky-50 text-sky-700',
                'scope' => 'active',
            ],
            [
                'label' => 'High Urgency',
                'value' => $urgentCount,
                'description' => 'Active high-urgency complaints',
                'icon' => 'fa-triangle-exclamation',
                'style' => 'bg-red-50 text-red-700',
                'scope' => 'high_urgency',
            ],
            [
                'label' => 'Workload Completion',
                'value' => number_format($completionRate, 1) . '%',
                'description' => 'Completed vs. counted visits',
                'icon' => 'fa-chart-line',
                'style' => 'bg-emerald-50 text-emerald-700',
                'scope' => 'all',
            ],
        ];

        $dailyMetrics = [
            [
                'label' => "Today's Scheduled Visits",
                'value' => $todayScheduledCount,
                'description' => 'Scheduled for today',
                'icon' => 'fa-calendar-day',
                'style' => 'bg-sky-50 text-sky-700',
                'scope' => 'today',
            ],
            [
                'label' => 'Overdue Scheduled Visits',
                'value' => $overdueCount,
                'description' => 'Scheduled date has passed',
                'icon' => 'fa-calendar-xmark',
                'style' => 'bg-rose-50 text-rose-700',
                'scope' => 'overdue',
            ],
        ];
    @endphp

    <div class="mx-auto max-w-7xl space-y-5 pb-10">

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-sky-600">
                    <i class="fas fa-screwdriver-wrench"></i>
                    Maintenance Operations
                </p>

                <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">
                    Technician Dashboard
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Welcome back, {{ auth()->user()->first_name ?? 'Technician' }}.
                    Here's your maintenance overview.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <span
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600">
                    <i class="far fa-calendar text-sky-600"></i>
                    {{ now('Asia/Manila')->format('M d, Y') }}
                    <span class="text-slate-400">PHT</span>
                </span>

                <a href="{{ route('technician.maintenance-reports.history') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-sky-200 bg-white px-4 py-2 text-xs font-semibold text-sky-700 transition hover:bg-sky-50">
                    <i class="fas fa-clock-rotate-left"></i>
                    Report History
                </a>

                <a href="{{ route('technician.reports.maintenance') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-sky-700">
                    <i class="fas fa-chart-column"></i>
                    Work Summary
                </a>
            </div>
        </div>

        {{-- FLASH MESSAGES --}}
        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ session('error') }}
            </div>
        @endif

        {{-- KPI CARDS --}}
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ($metrics as $metric)
                <a href="{{ $filterUrl($metric['scope']) }}" aria-label="View {{ $metric['label'] }}"
                    class="group block rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 sm:p-5">

                    <div class="flex items-start justify-between gap-2">
                        <span class="text-xs font-medium leading-5 text-slate-500 sm:text-sm">
                            {{ $metric['label'] }}
                        </span>

                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $metric['style'] }}">
                            <i class="fas {{ $metric['icon'] }}"></i>
                        </span>
                    </div>

                    <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($metric['value']) }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        {{ $metric['description'] }}
                    </p>

                    <p class="mt-3 flex items-center gap-1 text-xs font-semibold text-sky-600">
                        View filtered complaints
                        <i class="fas fa-arrow-right transition group-hover:translate-x-1"></i>
                    </p>
                </a>
            @endforeach
        </div>

        {{-- OPERATIONAL INSIGHTS --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            @foreach ($insights as $insight)
                <a href="{{ $filterUrl($insight['scope']) }}"
                    class="group flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-sky-300 hover:shadow-md">

                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-500">
                            {{ $insight['label'] }}
                        </p>

                        <p class="mt-1 text-xl font-bold text-slate-900">
                            {{ $insight['value'] }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            {{ $insight['description'] }}
                        </p>

                        <p class="mt-2 text-xs font-semibold text-sky-600">
                            View details
                            <i class="fas fa-arrow-right ml-1 transition group-hover:translate-x-1"></i>
                        </p>
                    </div>

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $insight['style'] }}">
                        <i class="fas {{ $insight['icon'] }} text-lg"></i>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- DAILY OVERVIEW --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            @foreach ($dailyMetrics as $metric)
                <a href="{{ $filterUrl($metric['scope']) }}"
                    class="group flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-sky-300 hover:shadow-md sm:p-5">

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ $metric['label'] }}
                        </p>

                        <p class="mt-2 text-3xl font-bold text-slate-900">
                            {{ number_format($metric['value']) }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $metric['description'] }}
                        </p>

                        <p class="mt-3 text-xs font-semibold text-sky-600">
                            View scheduled complaints
                            <i class="fas fa-arrow-right ml-1 transition group-hover:translate-x-1"></i>
                        </p>
                    </div>

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $metric['style'] }}">
                        <i class="fas {{ $metric['icon'] }} text-xl"></i>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- ACCOMPLISHMENT REPORT REVIEW STATUS --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="font-semibold text-slate-900">
                        <i class="fas fa-file-circle-check mr-2 text-sky-600"></i>
                        My Accomplishment Reports
                    </h2>
                    <p class="mt-1 text-xs text-slate-500">Manager review status of reports you submitted (all time)</p>
                </div>
                <a href="{{ route('technician.maintenance-reports.history') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-800">
                    Report History <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-4 sm:p-5">
                @foreach ([['label' => 'Total Submitted', 'value' => $reportTotalCount, 'style' => 'bg-sky-50 text-sky-700', 'icon' => 'fa-file-lines', 'status' => 'all'], ['label' => 'Pending Review', 'value' => $reportPendingCount, 'style' => 'bg-blue-50 text-blue-700', 'icon' => 'fa-hourglass-half', 'status' => 'Pending Review'], ['label' => 'Approved', 'value' => $reportApprovedCount, 'style' => 'bg-green-50 text-green-700', 'icon' => 'fa-circle-check', 'status' => 'Approved'], ['label' => 'Returned', 'value' => $reportReturnedCount, 'style' => 'bg-amber-50 text-amber-700', 'icon' => 'fa-rotate-left', 'status' => 'Returned']] as $reportMetric)
                    <a href="{{ route('technician.maintenance-reports.history', ['status' => $reportMetric['status']]) }}"
                        class="rounded-xl border border-slate-200 p-3 transition hover:border-sky-300 hover:shadow-sm sm:p-4">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-slate-500">{{ $reportMetric['label'] }}</span>
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $reportMetric['style'] }}">
                                <i class="fas {{ $reportMetric['icon'] }}"></i>
                            </span>
                        </div>
                        <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($reportMetric['value']) }}</p>
                        <p class="mt-1 text-xs text-sky-700">View reports <i class="fas fa-arrow-right ml-1"></i></p>
                    </a>
                @endforeach
            </div>
            <div class="border-t border-slate-100 px-5 py-4">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <h3 class="text-sm font-semibold text-slate-800">Recent Submitted Reports</h3>
                    <a href="{{ route('technician.maintenance-reports.history') }}"
                        class="text-xs font-semibold text-sky-700">View All</a>
                </div>
                <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                    @forelse ($recentSubmittedReports as $report)
                        @php
                            $reportStatusClass = match ($report->review_status) {
                                'Approved' => 'bg-green-50 text-green-700',
                                'Returned' => 'bg-amber-50 text-amber-700',
                                default => 'bg-blue-50 text-blue-700',
                            };
                        @endphp
                        <div
                            class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-100 p-3">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-sky-700">
                                    {{ $report->complaint?->complaint_no ?? 'Complaint unavailable' }}</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $report->complaint?->category?->name ?? 'Uncategorized' }}</p>
                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $report->submitted_at ? \Carbon\Carbon::parse($report->submitted_at)->timezone('Asia/Manila')->format('M d, Y') : 'Date unavailable' }}
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <span
                                    class="rounded-full px-2 py-1 text-[10px] font-semibold {{ $reportStatusClass }}">{{ $report->review_status }}</span>
                                @if ($report->complaint)
                                    <a href="{{ $report->review_status === 'Returned' ? route('technician.maintenance-reports.create', $report->complaint) : route('technician.maintenance-reports.show', $report->complaint) }}"
                                        class="text-xs font-semibold text-sky-700 hover:underline">
                                        {{ $report->review_status === 'Returned' ? 'Revise Report' : 'View Report' }} <i
                                            class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="py-5 text-center text-sm text-slate-500 md:col-span-2">No submitted accomplishment reports
                            yet.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ANALYTICS --}}
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

            {{-- SIX-MONTH TREND --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="font-semibold text-slate-900">
                            Maintenance Accomplishment Trend
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Completed visits over the last six calendar months
                        </p>
                    </div>

                    <span class="rounded-lg bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700">
                        Monthly
                    </span>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="relative h-64 w-full sm:h-72">
                        <canvas id="maintenanceTrendChart" role="img"
                            aria-label="Six-month accomplishment trend"></canvas>
                    </div>
                </div>
            </div>

            {{-- WORK ORDER DISTRIBUTION --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="font-semibold text-slate-900">
                        Work Order Distribution
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Current counted maintenance visits
                    </p>
                </div>

                <div class="p-5">
                    <div class="relative mx-auto h-52 w-full max-w-64">
                        <canvas id="workOrderStatusChart" role="img"
                            aria-label="Work order status distribution"></canvas>
                    </div>

                    @php
                        $statusColors = ['bg-blue-500', 'bg-amber-500', 'bg-orange-500', 'bg-green-500'];

                        $statusScopes = ['scheduled', 'in_progress', 'pending_workload', 'completed'];
                    @endphp

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        @foreach ($statusLabels as $index => $label)
                            <a href="{{ $filterUrl($statusScopes[$index]) }}"
                                class="flex items-start gap-2 rounded-lg p-1 transition hover:bg-slate-50">

                                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full {{ $statusColors[$index] }}"></span>

                                <div>
                                    <p class="text-xs text-slate-500">
                                        {{ $label }}
                                    </p>

                                    <p class="text-sm font-bold text-slate-900">
                                        {{ $statusValues[$index] }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTIVE MAINTENANCE QUEUE --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="font-semibold text-slate-900">
                        <i class="fas fa-helmet-safety mr-2 text-sky-600"></i>
                        Active Maintenance Queue
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        In-progress work first, then scheduled visits prioritized by AI urgency
                    </p>
                </div>

                <a href="{{ $filterUrl('active') }}" class="text-sm font-semibold text-sky-600 hover:text-sky-700">
                    View All
                    <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2">

                @forelse ($currentWorkOrders as $workOrder)
                    @php
                        $complaint = $workOrder->complaint;

                        $urgency = strtoupper(trim((string) ($complaint?->aiAnalysis?->verified_urgency_level ?? '')));

                        $urgencyStyle = match ($urgency) {
                            'HIGH' => 'bg-red-50 text-red-700 border-red-200',
                            'MODERATE', 'MEDIUM' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'LOW' => 'bg-green-50 text-green-700 border-green-200',
                            default => 'bg-slate-50 text-slate-500 border-slate-200',
                        };

                        $scheduleDate = $workOrder->schedule?->schedule_date
                            ? \Carbon\Carbon::parse($workOrder->schedule->schedule_date, 'Asia/Manila')->toDateString()
                            : null;

                        $todayDate = now('Asia/Manila')->toDateString();

                        $isToday = $workOrder->status === 'Scheduled' && $scheduleDate === $todayDate;

                        $isOverdue =
                            $workOrder->status === 'Scheduled' && $scheduleDate !== null && $scheduleDate < $todayDate;
                    @endphp

                    <div class="border-b border-slate-100 p-4 transition hover:bg-slate-50/70 sm:p-5 lg:odd:border-r">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-bold text-sky-700">
                                    {{ $complaint?->complaint_no ?? 'Unknown Complaint' }}
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ $complaint?->category?->name ?? 'Uncategorized' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    <i class="far fa-user mr-1"></i>
                                    {{ $complaint?->consumer?->full_name ?? ($complaint?->complainant_name ?? 'Unknown Consumer') }}
                                </p>
                            </div>

                            <span
                                class="rounded-full border px-2.5 py-1 text-[10px] font-bold
                            {{ $workOrder->status === 'In Progress'
                                ? 'border-amber-200 bg-amber-50 text-amber-700'
                                : 'border-blue-200 bg-blue-50 text-blue-700' }}">
                                {{ $workOrder->status }}
                            </span>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            @if ($urgency === 'HIGH')
                                <span class="rounded-full border px-2.5 py-1 text-[10px] font-bold {{ $urgencyStyle }}">
                                    <i class="fas fa-triangle-exclamation mr-1"></i>
                                    HIGH URGENCY
                                </span>
                            @elseif ($urgency)
                                <span
                                    class="rounded-full border px-2.5 py-1 text-[10px] font-semibold {{ $urgencyStyle }}">
                                    {{ $urgency }} URGENCY
                                </span>
                            @endif

                            @if ($isToday)
                                <span
                                    class="rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-[10px] font-bold text-sky-700">
                                    <i class="fas fa-calendar-day mr-1"></i>
                                    TODAY
                                </span>
                            @endif

                            @if ($isOverdue)
                                <span
                                    class="rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-[10px] font-bold text-rose-700">
                                    <i class="fas fa-clock mr-1"></i>
                                    OVERDUE
                                </span>
                            @endif
                        </div>

                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-3">
                            <div class="flex flex-wrap gap-3 text-xs text-slate-500">
                                <span>
                                    <i class="fas fa-rotate mr-1"></i>
                                    Visit {{ $workOrder->visit_number }}
                                </span>

                                @if ($scheduleDate)
                                    <span>
                                        <i class="far fa-calendar mr-1"></i>
                                        {{ \Carbon\Carbon::parse($scheduleDate)->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>

                            @if ($complaint)
                                <a href="{{ route('technician.complaints.show', $complaint) }}"
                                    class="inline-flex items-center gap-2 rounded-lg bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 transition hover:bg-sky-100">
                                    View Details
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                @empty
                    <div class="p-12 text-center lg:col-span-2">
                        <i class="fas fa-circle-check text-3xl text-green-500"></i>

                        <h3 class="mt-3 font-semibold text-slate-900">
                            No active maintenance visits
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Your scheduled and in-progress visits will appear here.
                        </p>
                    </div>
                @endforelse

            </div>
        </div>

        {{-- LOWER PANELS --}}
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">

            {{-- RECENT ACCOMPLISHMENTS --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="font-semibold text-slate-900">
                            <i class="fas fa-clipboard-check mr-2 text-green-600"></i>
                            Recently Accomplished
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Latest completed maintenance visits
                        </p>
                    </div>

                    <a href="{{ $filterUrl('completed') }}" class="text-xs font-semibold text-sky-600">
                        View All
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($recentCompletedOrders as $workOrder)
                        @php
                            $complaint = $workOrder->complaint;
                            $seconds = null;

                            if ($workOrder->started_at && $workOrder->completed_at) {
                                $difference = $workOrder->started_at->diffInSeconds($workOrder->completed_at, false);

                                if ($difference >= 0) {
                                    $seconds = (int) round($difference);
                                }
                            }

                            $duration =
                                $seconds === null
                                    ? 'N/A'
                                    : intdiv($seconds, 3600) .
                                        ' hr ' .
                                        intdiv($seconds % 3600, 60) .
                                        ' min ' .
                                        $seconds % 60 .
                                        ' sec';
                        @endphp

                        <a href="{{ $complaint ? route('technician.complaints.show', $complaint) : $filterUrl('completed') }}"
                            class="flex items-start justify-between gap-3 p-4 transition hover:bg-slate-50 sm:p-5">

                            <div>
                                <p class="text-sm font-bold text-sky-700">
                                    {{ $complaint?->complaint_no ?? 'Unknown Complaint' }}
                                    <span class="ml-1 text-xs font-normal text-slate-400">
                                        Visit {{ $workOrder->visit_number }}
                                    </span>
                                </p>

                                <p class="mt-1 text-sm font-medium text-slate-800">
                                    {{ $complaint?->category?->name ?? 'Uncategorized' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $workOrder->completed_at?->timezone('Asia/Manila')->format('M d, Y h:i:s A') ?? 'Date unavailable' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Recorded duration: {{ $duration }}
                                </p>
                            </div>

                            <i class="fas fa-arrow-right mt-2 text-sky-600"></i>
                        </a>
                    @empty
                        <div class="p-10 text-center text-sm text-slate-500">
                            No accomplished maintenance visits yet.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- RECENT UPDATES --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="font-semibold text-slate-900">
                        <i class="fas fa-clock-rotate-left mr-2 text-sky-600"></i>
                        Recent Work Order Updates
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Recently updated assigned visits
                    </p>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($recentActivityOrders as $workOrder)
                        @php
                            $complaint = $workOrder->complaint;

                            $activityStyle = match ($workOrder->status) {
                                'Scheduled' => 'bg-blue-50 text-blue-700',
                                'In Progress' => 'bg-amber-50 text-amber-700',
                                'Pending Workload' => 'bg-orange-50 text-orange-700',
                                'Completed' => 'bg-green-50 text-green-700',
                                default => 'bg-slate-100 text-slate-600',
                            };
                        @endphp

                        <a href="{{ $complaint ? route('technician.complaints.show', $complaint) : $filterUrl('all') }}"
                            class="flex items-start gap-3 p-4 transition hover:bg-slate-50 sm:p-5">

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                                <i class="fas fa-file-circle-check"></i>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-slate-900">
                                        {{ $complaint?->complaint_no ?? 'Unknown Complaint' }}
                                        <span class="font-normal text-slate-400">
                                            · Visit {{ $workOrder->visit_number }}
                                        </span>
                                    </p>

                                    <span class="rounded-full px-2 py-1 text-[10px] font-semibold {{ $activityStyle }}">
                                        {{ $workOrder->status }}
                                    </span>
                                </div>

                                <p class="mt-1 text-xs text-slate-600">
                                    {{ $complaint?->category?->name ?? 'Uncategorized' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Updated
                                    {{ $workOrder->updated_at?->timezone('Asia/Manila')->format('M d, Y h:i A') ?? 'N/A' }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="p-10 text-center text-sm text-slate-500">
                            No recent work-order updates.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- QUICK ACCESS --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ $filterUrl('active') }}"
                class="flex items-center justify-between rounded-2xl bg-sky-600 p-4 text-sm font-semibold text-white transition hover:bg-sky-700">
                <span><i class="fas fa-list-check mr-2"></i> Active Work</span>
                <i class="fas fa-arrow-right"></i>
            </a>

            <a href="{{ route('technician.reports.maintenance') }}"
                class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                <span><i class="fas fa-chart-line mr-2 text-sky-600"></i> Work Summary</span>
                <i class="fas fa-arrow-right"></i>
            </a>

            <a href="{{ route('technician.maintenance-reports.history') }}"
                class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                <span><i class="fas fa-file-lines mr-2 text-sky-600"></i> Report History</span>
                <i class="fas fa-arrow-right"></i>
            </a>

            <a href="{{ $filterUrl('all') }}"
                class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                <span><i class="fas fa-folder-open mr-2 text-sky-600"></i> All Complaints</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

    </div>

    {{-- CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Chart === 'undefined') return;

            Chart.defaults.font.family = 'Arial, sans-serif';
            Chart.defaults.color = '#64748b';

            const trendCanvas = document.getElementById('maintenanceTrendChart');

            if (trendCanvas) {
                const labels = @json($trendLabels);
                const values = @json($trendValues);

                new Chart(trendCanvas, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                                type: 'bar',
                                label: 'Completed Visits',
                                data: values,
                                backgroundColor: 'rgba(14,165,233,0.75)',
                                borderColor: '#0284c7',
                                borderWidth: 1,
                                borderRadius: 7,
                                maxBarThickness: 45,
                                order: 2
                            },
                            {
                                type: 'line',
                                label: 'Completion Trend',
                                data: values,
                                borderColor: '#0f172a',
                                backgroundColor: '#0f172a',
                                borderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                tension: 0.35,
                                fill: false,
                                order: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 9,
                                    padding: 18
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxRotation: 0
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    stepSize: 1
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            }
                        }
                    }
                });
            }

            const statusCanvas = document.getElementById('workOrderStatusChart');

            if (statusCanvas) {
                const labels = @json($statusLabels);
                const values = @json($statusValues);
                const hasData = values.some(value => Number(value) > 0);

                new Chart(statusCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: hasData ? labels : ['No Work Orders'],
                        datasets: [{
                            data: hasData ? values : [1],
                            backgroundColor: hasData ?
                                ['#3b82f6', '#f59e0b', '#f97316', '#22c55e'] :
                                ['#e2e8f0'],
                            borderWidth: 0,
                            hoverOffset: hasData ? 5 : 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '73%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: hasData
                            }
                        }
                    }
                });
            }
        });
    </script>

@endsection
