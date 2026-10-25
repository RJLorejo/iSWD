
@extends('maintenance-manager.layouts.app')

@section('title', 'Pending Workload')

@section('content')
    @php
        $pendingPrintRecords = $workOrders->getCollection()->map(function ($order) {
            $followUp = $order->getRelation('displayFollowUp');
            return [
                'complaint' => $order->complaint?->complaint_no ?? 'Unknown',
                'visit' => $order->visit_number,
                'category' => $order->complaint?->category?->name ?? 'Maintenance Concern',
                'address' => $order->complaint?->address ?? 'Not recorded',
                'reason' => $order->pending_reason ?: 'Not recorded',
                'team' => $order->technicians->pluck('full_name')->filter()->implode(', ') ?: 'Not recorded',
                'original_date' => $order->started_at ? \Carbon\Carbon::parse($order->started_at)->timezone('Asia/Manila')->format('M d, Y') : 'Not recorded',
                'follow_up' => $followUp ? ($followUp->schedule?->status === 'Finalized' ? 'Finalized' : 'Draft') : 'Awaiting Follow-up',
            ];
        })->values()->all();
    @endphp

    <div class="mx-auto max-w-7xl space-y-6 pb-12">

        {{-- Header --}}
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-semibold text-sky-700">
                    Maintenance Management
                </p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">
                    Pending Workload
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Review unfinished maintenance reports and manage follow-up planning.
                </p>
            </div>

            <button type="button"
                onclick="printPendingWorkloadReport()"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-print"></i>
                Print Current Page
            </button>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <p class="font-semibold">Please check the following:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Summary --}}
        <div class="grid grid-cols-3 gap-3">
            @foreach ([
                ['All Reports', $counts['all'], 'all'],
                ['Awaiting Follow-up', $counts['awaiting'], 'awaiting'],
                ['Follow-up Planned', $counts['planned'], 'planned'],
            ] as [$label, $count, $filter])
                <a href="{{ route('maintenance-manager.pending-workloads.index', [
                    'status' => $filter,
                    'search' => $search,
                ]) }}"
                    class="rounded-2xl border p-4 shadow-sm transition hover:border-sky-300
                    {{ $status === $filter
                        ? 'border-sky-300 bg-sky-50'
                        : 'border-slate-200 bg-white' }}">
                    <p class="text-xs font-medium text-slate-500 sm:text-sm">
                        {{ $label }}
                    </p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">
                        {{ $count }}
                    </p>
                </a>
            @endforeach
        </div>

        {{-- Search and Filter --}}
        <form method="GET"
            action="{{ route('maintenance-manager.pending-workloads.index') }}"
            class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-end">

            <div class="flex-1">
                <label class="mb-1 block text-xs font-semibold text-slate-600">
                    Search Reports
                </label>
                <input type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Complaint number, reason, address..."
                    class="w-full rounded-xl border-slate-300 text-sm">
            </div>

            <div class="sm:w-52">
                <label class="mb-1 block text-xs font-semibold text-slate-600">
                    Follow-up Status
                </label>
                <select name="status"
                    class="w-full rounded-xl border-slate-300 text-sm">
                    <option value="all" @selected($status === 'all')>All Reports</option>
                    <option value="awaiting" @selected($status === 'awaiting')>Awaiting Follow-up</option>
                    <option value="planned" @selected($status === 'planned')>Follow-up Planned</option>
                </select>
            </div>

            <button type="submit"
                class="rounded-xl bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-800">
                <i class="fas fa-search mr-1"></i> Filter
            </button>

            <a href="{{ route('maintenance-manager.pending-workloads.index') }}"
                class="rounded-xl border border-slate-200 px-4 py-2.5 text-center text-sm font-semibold text-slate-600 hover:bg-slate-50">
                Reset
            </a>
        </form>

        {{-- Printable report heading --}}
        <div class="hidden print-report-heading">
            <h1>Sagay Water District</h1>
            <h2>Pending Workload Report</h2>
            <p>
                Status: {{ ucfirst($status) }}
                | Search: {{ $search ?: 'All' }}
                | Printed: {{ now('Asia/Manila')->format('F d, Y g:i A') }}
            </p>
            <p>
                Page {{ $workOrders->currentPage() }}
                of {{ $workOrders->lastPage() }}
                — {{ $workOrders->total() }} matching records
            </p>
        </div>

        {{-- Cards --}}
        <div class="space-y-4">
            @forelse ($workOrders as $order)
                @php
                    $followUp = $order->getRelation('displayFollowUp');
                    $confirmed = $followUp?->schedule?->status === 'Finalized';
                @endphp

                <article class="print-card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col justify-between gap-3 border-b border-slate-100 bg-slate-50 px-5 py-4 sm:flex-row sm:items-start">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-bold text-slate-900">
                                    {{ $order->complaint?->complaint_no ?? 'Unknown Complaint' }}
                                </h2>
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                    Pending Workload
                                </span>
                                <span class="rounded-full bg-white px-2.5 py-1 text-xs text-slate-600">
                                    Visit #{{ $order->visit_number }}
                                </span>
                            </div>

                            <p class="mt-2 text-sm font-medium text-slate-800">
                                {{ $order->complaint?->category?->name ?? 'Maintenance Concern' }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $order->complaint?->address ?? 'No address recorded' }}
                            </p>
                        </div>

                        <span class="self-start rounded-full px-3 py-1.5 text-xs font-semibold
                            {{ $confirmed
                                ? 'bg-emerald-100 text-emerald-800'
                                : ($followUp
                                    ? 'bg-sky-100 text-sky-800'
                                    : 'bg-amber-100 text-amber-800') }}">
                            {{ $confirmed
                                ? 'Follow-up Finalized'
                                : ($followUp ? 'Draft Follow-up' : 'Awaiting Follow-up') }}
                        </span>
                    </div>

                    <div class="space-y-4 p-5">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Technician's Reason
                            </p>
                            <p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-slate-800">
                                {{ $order->pending_reason }}
                            </p>
                        </div>

                        <div class="grid gap-3 text-sm sm:grid-cols-3">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Maintenance Team</p>
                                <p class="mt-1 font-semibold text-slate-800">
                                    {{ $order->technicians->pluck('full_name')->join(', ') ?: 'Not recorded' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Original Work Date</p>
                                <p class="mt-1 font-semibold text-slate-800">
                                    {{ $order->started_at
                                        ? \Carbon\Carbon::parse($order->started_at)->timezone('Asia/Manila')->format('M d, Y')
                                        : 'Not recorded' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Reported On</p>
                                <p class="mt-1 font-semibold text-slate-800">
                                    {{ $order->pending_recorded_at
                                        ? \Carbon\Carbon::parse($order->pending_recorded_at)->timezone('Asia/Manila')->format('M d, Y g:i A')
                                        : 'Not recorded' }}
                                </p>
                            </div>
                        </div>

                        @if ($followUp)
                            <div class="rounded-xl border border-sky-200 bg-sky-50 p-3 text-sm text-sky-900">
                                <strong>Follow-up Visit #{{ $followUp->visit_number }}</strong>
                                <span class="ml-1">
                                    — {{ $followUp->schedule?->schedule_date
                                        ? \Carbon\Carbon::parse($followUp->schedule->schedule_date)->format('M d, Y')
                                        : 'Date unavailable' }}
                                </span>
                                <p class="mt-1 text-xs">
                                    Schedule: {{ $followUp->schedule?->status ?? 'Not finalized' }}
                                    · Work Order: {{ $followUp->status }}
                                </p>
                            </div>
                        @else
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
                                No follow-up visit has been planned yet. Manager decision required.
                            </div>
                        @endif

                        <div class="no-print flex flex-wrap items-center gap-3 border-t border-slate-100 pt-4">
                            <a href="{{ route('maintenance-manager.complaints.show', $order->complaint_id) }}"
                                class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                <i class="fas fa-eye mr-1"></i> View Complaint
                            </a>

                            @if (!$followUp)
                                <div class="w-full sm:max-w-xl">
                                    @include('maintenance-manager.schedules.partials.pending-follow-up-form', [
                                        'order' => $order,
                                    ])
                                </div>
                            @else
                                <a href="{{ route('maintenance-manager.schedules.index', [
                                    'date' => $followUp->schedule?->schedule_date
                                        ? \Carbon\Carbon::parse($followUp->schedule->schedule_date)->format('Y-m-d')
                                        : now('Asia/Manila')->toDateString(),
                                ]) }}"
                                    class="rounded-xl bg-sky-700 px-4 py-2.5 text-xs font-semibold text-white hover:bg-sky-800">
                                    <i class="fas fa-calendar-check mr-1"></i>
                                    View Follow-up Schedule
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white px-6 py-14 text-center">
                    <i class="fas fa-clipboard-check text-3xl text-slate-300"></i>
                    <h3 class="mt-3 font-semibold text-slate-800">
                        No Pending Workload reports found
                    </h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Submitted unfinished-maintenance reports will appear here.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($workOrders->hasPages())
            <div class="no-print rounded-xl border border-slate-200 bg-white p-4">
                {{ $workOrders->links() }}
            </div>
        @endif

        <p class="no-print text-xs text-slate-500">
            Showing {{ $workOrders->firstItem() ?? 0 }}–{{ $workOrders->lastItem() ?? 0 }}
            of {{ $workOrders->total() }} matching reports.
            Printing includes only the current page (maximum 10 reports).
        </p>
    </div>

@include('maintenance-manager.partials.report-print-template')
<script>
function printPendingWorkloadReport() {
    const records = @js($pendingPrintRecords);
    const esc = value => String(value ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    const headers = ['Complaint / Visit','Category / Address','Technician Reason','Maintenance Team','Original Date','Follow-up Status'];
    const tableHtml = '<thead><tr>' + headers.map(h=>'<th>'+esc(h)+'</th>').join('') + '</tr></thead><tbody>' +
        (records.length ? records.map(r=>'<tr>'+[r.complaint+' · Visit #'+r.visit,r.category+' — '+r.address,r.reason,r.team,r.original_date,r.follow_up].map(v=>'<td>'+esc(v)+'</td>').join('')+'</tr>').join('') : '<tr><td colspan="6">No matching records.</td></tr>') + '</tbody>';
    openMaintenanceReport({title:'Pending Workload Report',subtitle:'Maintenance Management',period:@js('Status: '.ucfirst($status).' | Search: '.($search ?: 'All')),tableHtml,summary:`${records.length} records on current page · ${@js($workOrders->total())} matching records overall`});
}
</script>

@endsection
