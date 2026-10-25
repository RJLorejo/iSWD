@extends('maintenance-manager.layouts.app')
@section('title', 'Maintenance Insights & Reports')
@section('content')
<style>
    .manager-insights, .manager-insights > *, .manager-insights form, .manager-insights section {min-width:0;max-width:100%}
    .manager-insights input[type="date"], .manager-insights select {box-sizing:border-box;min-width:0;max-width:100%;width:100%}
    .manager-insights canvas {max-width:100%}
    @media(max-width:639px){.manager-insights{overflow-x:clip}.manager-insights .grid > *{min-width:0}}
</style>
<div class="manager-insights mx-auto w-full min-w-0 max-w-7xl space-y-5 overflow-x-clip pb-8 sm:space-y-6">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-bold uppercase tracking-wider text-sky-700">Maintenance Management</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Insights & Reports</h1>
            <p class="mt-1 text-sm text-slate-500">Analyze finalized, approved maintenance visits across any reporting period.</p>
        </div>
        <a href="{{ route('maintenance-manager.insights.print', request()->only(['date_from', 'date_to', 'status'])) }}" target="_blank"
           class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 sm:w-auto">
            <i class="fas fa-print"></i> Print report
        </a>
    </header>

    <form method="GET" action="{{ route('maintenance-manager.insights.index') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
            <h2 class="font-semibold text-slate-900"><i class="fas fa-filter mr-2 text-sky-700"></i>Report filters</h2>
            <span class="text-xs text-slate-500">Scheduled work date · {{ $from->format('M d, Y') }} – {{ $to->format('M d, Y') }}</span>
        </div>
        <div class="grid min-w-0 grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
            <label class="block min-w-0"><span class="mb-1 block text-xs font-semibold text-slate-600">Date From</span>
                <input type="date" name="date_from" value="{{ old('date_from', request('date_from', $from->toDateString())) }}" required
                    class="w-full min-w-0 rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500"></label>
            <label class="block min-w-0"><span class="mb-1 block text-xs font-semibold text-slate-600">Date To</span>
                <input type="date" name="date_to" value="{{ old('date_to', request('date_to', $to->toDateString())) }}" required
                    class="w-full min-w-0 rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500"></label>
            <label class="block min-w-0"><span class="mb-1 block text-xs font-semibold text-slate-600">Work order status</span>
                <select name="status" class="w-full min-w-0 rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                    @foreach (['all' => 'All statuses', 'Scheduled' => 'Scheduled', 'In Progress' => 'In Progress', 'Completed' => 'Completed', 'Pending Workload' => 'Pending Workload', 'Rescheduled' => 'Rescheduled', 'Cancelled' => 'Cancelled'] as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="min-w-0 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800"><i class="fas fa-magnifying-glass mr-1"></i> Apply filters</button>
            <a href="{{ route('maintenance-manager.insights.index') }}" class="min-w-0 rounded-xl border border-slate-300 px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset to this year</a>
        </div>
        @if ($errors->any())<p class="mt-3 text-sm font-medium text-rose-600">{{ $errors->first() }}</p>@endif
    </form>

    @php
        $cards = [
            ['label' => 'Total visits', 'value' => number_format($total), 'icon' => 'fa-clipboard-list', 'color' => 'sky', 'hint' => 'Approved, finalized work orders'],
            ['label' => 'Completed', 'value' => number_format($completed->count()), 'icon' => 'fa-circle-check', 'color' => 'emerald', 'hint' => 'Current completed status'],
            ['label' => 'Completion rate', 'value' => $eligible > 0 ? number_format($completed->count() / $eligible * 100, 1) . '%' : 'N/A', 'icon' => 'fa-chart-line', 'color' => 'indigo', 'hint' => 'Completed / non-cancelled visits'],
            ['label' => 'Pending workload', 'value' => number_format($pending->count()), 'icon' => 'fa-clock-rotate-left', 'color' => 'amber', 'hint' => 'Currently unfinished visits'],
            ['label' => 'Confirmed missed', 'value' => number_format($missedConfirmed->count()), 'icon' => 'fa-calendar-xmark', 'color' => 'rose', 'hint' => 'Manager-confirmed records'],
            ['label' => 'Avg. work duration', 'value' => ($averageMinutes === null ? 'N/A' : ($averageMinutes < 60 ? number_format($averageMinutes, 1) . ' mins' : number_format($averageMinutes / 60, 2) . ' hrs')), 'icon' => 'fa-stopwatch', 'color' => 'violet', 'hint' => 'Elapsed time per completed visit; ' . $timedCount . ' timed visits'],
            ['label' => 'Total recorded work time', 'value' => ($totalMinutes === null ? 'N/A' : ($totalMinutes < 60 ? number_format($totalMinutes, 1) . ' mins' : number_format($totalMinutes / 60, 2) . ' hrs')), 'icon' => 'fa-clock', 'color' => 'sky', 'hint' => 'Sum of recorded visit durations; not employee-hours'],
        ];
    @endphp
    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 sm:gap-4">
        @foreach ($cards as $card)
            <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-500">{{ $card['label'] }}</span>
                    <i class="fas {{ $card['icon'] }} text-sky-700"></i>
                </div>
                <p class="mt-3 break-words text-2xl font-bold tabular-nums text-slate-900 sm:text-3xl">{{ $card['value'] }}</p>
                <p class="mt-2 text-[11px] leading-4 text-slate-500">{{ $card['hint'] }}</p>
            </div>
        @endforeach
    </section>

    <div class="grid min-w-0 grid-cols-1 gap-4 xl:grid-cols-2">
        <section class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <h2 class="font-bold text-slate-900">Monthly Maintenance Trends</h2>
            <p class="mt-1 text-xs text-slate-500">Visits grouped by scheduled month. Chart supports up to 24 months.</p>
            @if ($monthlyChartAvailable && count($monthly))
                <div class="relative mt-5 h-72 w-full min-w-0"><canvas id="insightsTrend" aria-label="Monthly maintenance trend"></canvas></div>
            @else
                <div class="mt-5 rounded-xl bg-slate-50 p-6 text-sm text-slate-500">Select a range of 24 months or less to see monthly trends. The table and print report still include your full selected range.</div>
            @endif
        </section>
        <section class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <h2 class="font-bold text-slate-900">Visit Status Distribution</h2>
            <p class="mt-1 text-xs text-slate-500">Current status of visits scheduled within the reporting period.</p>
            @if ($total)
                <div class="relative mx-auto mt-5 h-72 w-full max-w-md"><canvas id="insightsStatus" aria-label="Work order status distribution"></canvas></div>
            @else
                <div class="mt-5 rounded-xl bg-slate-50 p-6 text-sm text-slate-500">No approved visits found for the selected filters.</div>
            @endif
        </section>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-4 sm:p-5">
            <h2 class="font-bold text-slate-900">Plumber Assignment Summary</h2>
            <p class="mt-1 text-xs text-slate-500">Counts are per plumber assignment. Shared visits count once for each assigned plumber.</p>
        </div>
        <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 lg:grid-cols-3 sm:p-5">
            @forelse ($plumbers as $plumber)
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="break-words text-sm font-bold text-slate-900">{{ $plumber['name'] }}</p>
                    <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                        <div><p class="text-xl font-bold text-sky-700">{{ $plumber['assigned'] }}</p><p class="text-[11px] text-slate-500">Assigned</p></div>
                        <div><p class="text-xl font-bold text-emerald-700">{{ $plumber['completed'] }}</p><p class="text-[11px] text-slate-500">Completed</p></div>
                        <div><p class="text-xl font-bold text-amber-700">{{ $plumber['pending'] }}</p><p class="text-[11px] text-slate-500">Pending</p></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No plumber assignments in the selected period.</p>
            @endforelse
        </div>
    </section>

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 p-4 sm:p-5">
            <div><h2 class="font-bold text-slate-900">Detailed Work Order Records</h2><p class="mt-1 text-xs text-slate-500">{{ number_format($total) }} filtered visits · showing up to 15 per page</p></div>
            <a href="{{ route('maintenance-manager.insights.print', request()->only(['date_from', 'date_to', 'status'])) }}" target="_blank" class="text-sm font-semibold text-sky-700 hover:underline"><i class="fas fa-print mr-1"></i> Print all filtered records</a>
        </div>
        <div class="hidden w-full overflow-x-auto overscroll-x-contain lg:block">
            <table class="w-full min-w-[820px] text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-600"><tr>
                    <th class="px-4 py-3">Scheduled</th><th class="px-4 py-3">Complaint / Visit</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Plumber(s)</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Notes</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pageOrders as $order)
                        <tr class="hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3">{{ $order->schedule?->schedule_date?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-4 py-3"><a href="{{ route('maintenance-manager.complaints.show', $order->complaint_id) }}" class="font-semibold text-sky-700 hover:underline">{{ $order->complaint?->complaint_no ?? '#' . $order->complaint_id }}</a><p class="mt-1 text-xs text-slate-500">Visit #{{ $order->visit_number }} · WO #{{ $order->id }}</p></td>
                            <td class="px-4 py-3">{{ $order->complaint?->category?->name ?? '—' }}</td>
                            <td class="max-w-[220px] break-words px-4 py-3">{{ $order->technicians->pluck('full_name')->filter()->join(', ') ?: 'Not recorded' }}</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $order->status }}</span></td>
                            <td class="max-w-[250px] break-words px-4 py-3 text-slate-600">{{ \Illuminate\Support\Str::limit($order->pending_reason ?: ($order->missed_manager_reason ?: ($order->manager_remarks ?: '—')), 100) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">No maintenance work orders match the selected filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Mobile-friendly work order cards; desktop keeps the full table. --}}
        <div class="divide-y divide-slate-100 lg:hidden">
            @forelse ($pageOrders as $order)
                <article class="min-w-0 space-y-3 p-4">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="break-words text-sm font-bold text-sky-700">{{ $order->complaint?->complaint_no ?? 'Complaint #' . $order->complaint_id }}</p>
                            <p class="mt-1 text-xs text-slate-500">Visit #{{ $order->visit_number }} · WO #{{ $order->id }}</p>
                        </div>
                        <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">{{ $order->status }}</span>
                    </div>
                    <dl class="space-y-2 text-sm">
                        <div><dt class="text-xs text-slate-400">Scheduled</dt><dd class="text-slate-700">{{ $order->schedule?->schedule_date?->format('M d, Y') ?? '—' }}</dd></div>
                        <div><dt class="text-xs text-slate-400">Complaint type</dt><dd class="break-words text-slate-700">{{ $order->complaint?->category?->name ?? '—' }}</dd></div>
                        <div><dt class="text-xs text-slate-400">Plumber(s)</dt><dd class="break-words text-slate-700">{{ $order->technicians->pluck('full_name')->filter()->join(', ') ?: 'Not recorded' }}</dd></div>
                        @if ($order->pending_reason || $order->missed_manager_reason || $order->manager_remarks)
                            <div><dt class="text-xs text-slate-400">Reason / remarks</dt><dd class="whitespace-pre-wrap break-words text-slate-700">{{ $order->pending_reason ?: ($order->missed_manager_reason ?: $order->manager_remarks) }}</dd></div>
                        @endif
                    </dl>
                    <a href="{{ route('maintenance-manager.complaints.show', $order->complaint_id) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-sky-700">View complaint <i class="fas fa-arrow-right"></i></a>
                </article>
            @empty
                <div class="p-8 text-center text-sm text-slate-500">No records match the selected filters.</div>
            @endforelse
        </div>
        @if ($pages > 1)
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 p-4 text-sm">
                <span class="text-slate-500">Page {{ $currentPage }} of {{ $pages }}</span>
                <div class="flex gap-2">
                    @if ($currentPage > 1)<a class="rounded-lg border px-4 py-2 hover:bg-slate-50" href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}">Previous</a>@endif
                    @if ($currentPage < $pages)<a class="rounded-lg border px-4 py-2 hover:bg-slate-50" href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}">Next</a>@endif
                </div>
            </div>
        @endif
    </section>
    <p class="text-xs leading-5 text-slate-500">Reporting basis: approved work orders on finalized schedules, selected by scheduled date. Statuses are current, not reconstructed historical snapshots. Confirmed missed visits require a manager confirmation timestamp. Cancelled visits are excluded from the completion-rate denominator. Duration uses recorded start and completion times.</p>
</div>
<script>
(function () {
    const monthly = @js($monthly);
    const statuses = @js($statuses);
    function draw() {
        if (typeof Chart === 'undefined') return;
        const trend = document.getElementById('insightsTrend');
        if (trend) new Chart(trend, {
            type: 'line',
            data: {labels: monthly.map(x => x.label), datasets: [
                {label:'Visits', data: monthly.map(x => x.total), borderColor:'#0284c7', backgroundColor:'#0284c722', tension:.3},
                {label:'Completed', data: monthly.map(x => x.completed), borderColor:'#059669', backgroundColor:'#05966922', tension:.3},
                {label:'Pending workload', data: monthly.map(x => x.pending), borderColor:'#d97706', tension:.3}
            ]},
            options: {responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}}, scales:{y:{beginAtZero:true, ticks:{precision:0}}}}
        });
        const pie = document.getElementById('insightsStatus');
        if (pie) new Chart(pie, {
            type:'doughnut',
            data:{labels:Object.keys(statuses), datasets:[{data:Object.values(statuses), backgroundColor:['#0284c7','#059669','#f59e0b','#7c3aed','#f43f5e','#94a3b8']}]},
            options:{responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}}}
        });
    }
    if (typeof Chart !== 'undefined') draw();
    else {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js';
        script.onload = draw;
        script.onerror = () => document.querySelectorAll('#insightsTrend, #insightsStatus').forEach(el => {el.replaceWith(document.createTextNode('Charts unavailable. Detailed data is available below.'));});
        document.head.appendChild(script);
    }
})();
</script>
@endsection
