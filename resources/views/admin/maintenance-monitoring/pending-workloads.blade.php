@extends('admin.layouts.app')

@section('title', 'Pending Workload Monitoring')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6 pb-10">

        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-semibold text-sky-700">Maintenance Monitoring</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">Pending Workload</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Monitor unfinished maintenance reports and follow-up planning.
                </p>
            </div>

            <a href="{{ route('admin.maintenance-monitoring.pending-workloads.print', request()->query()) }}"
                class="no-print inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-print"></i>
                Print Report
            </a>
        </div>

        <div class="rounded-xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900">
            <i class="fas fa-eye mr-1"></i>
            <strong>Monitoring view:</strong>
            Admin can review these records but cannot schedule follow-up visits here.
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            @foreach ([['All Reports', $counts['all'], 'all'], ['Awaiting Follow-up', $counts['awaiting'], 'awaiting'], ['Follow-up Planned', $counts['planned'], 'planned']] as [$label, $count, $filter])
                <a href="{{ route('admin.maintenance-monitoring.pending-workloads', [
                    'status' => $filter,
                    'search' => $search,
                ]) }}"
                    class="rounded-2xl border p-4 shadow-sm transition hover:border-sky-300
                    {{ $status === $filter ? 'border-sky-300 bg-sky-50' : 'border-slate-200 bg-white' }}">
                    <p class="text-sm text-slate-500">{{ $label }}</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $count }}</p>
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.maintenance-monitoring.pending-workloads') }}"
            class="no-print flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-end">
            <div class="flex-1">
                <label for="search" class="mb-1 block text-xs font-semibold text-slate-600">
                    Search reports
                </label>
                <input id="search" type="search" name="search" value="{{ $search }}"
                    placeholder="Complaint number, reason, or address" class="w-full rounded-xl border-slate-300 text-sm">
            </div>

            <div class="sm:w-56">
                <label for="status" class="mb-1 block text-xs font-semibold text-slate-600">
                    Follow-up status
                </label>
                <select id="status" name="status" class="w-full rounded-xl border-slate-300 text-sm">
                    <option value="all" @selected($status === 'all')>All reports</option>
                    <option value="awaiting" @selected($status === 'awaiting')>Awaiting follow-up</option>
                    <option value="planned" @selected($status === 'planned')>Follow-up planned</option>
                </select>
            </div>

            <button type="submit"
                class="rounded-xl bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-800">
                <i class="fas fa-filter mr-1"></i> Apply
            </button>

            <a href="{{ route('admin.maintenance-monitoring.pending-workloads') }}"
                class="rounded-xl border border-slate-200 px-4 py-2.5 text-center text-sm font-semibold text-slate-600 hover:bg-slate-50">
                Reset
            </a>
        </form>

        <div class="space-y-4">
            @forelse ($workOrders as $order)
                @php
                    $followUp = $order->getRelation('displayFollowUp');
                    $confirmed = $followUp?->schedule?->status === 'Finalized';
                    $complaint = $order->complaint;
                @endphp

                <article class="print-card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div
                        class="flex flex-col justify-between gap-3 border-b border-slate-100 bg-slate-50 p-5 sm:flex-row sm:items-start">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-bold text-slate-900">
                                    {{ $complaint?->complaint_no ?? 'Unknown Complaint' }}
                                </h2>

                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">
                                    Pending Workload
                                </span>

                                <span class="rounded-full bg-white px-2.5 py-1 text-xs text-slate-600">
                                    Visit #{{ $order->visit_number }}
                                </span>
                            </div>

                            <p class="mt-2 text-sm font-medium text-slate-800">
                                {{ $complaint?->category?->name ?? 'Maintenance Concern' }}
                            </p>

                            <p class="mt-1 break-words text-xs text-slate-500">
                                {{ $complaint?->address ?? 'No address recorded' }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="rounded-full px-3 py-1.5 text-xs font-semibold
                                {{ $confirmed
                                    ? 'bg-emerald-100 text-emerald-800'
                                    : ($followUp
                                        ? 'bg-sky-100 text-sky-800'
                                        : 'bg-amber-100 text-amber-800') }}">
                                {{ $confirmed ? 'Follow-up Finalized' : ($followUp ? 'Draft Follow-up' : 'Awaiting Follow-up') }}
                            </span>

                            @if ($complaint)
                                <a href="{{ route('admin.complaints.show', [
                                    'complaint' => $complaint->id,
                                    'back_url' => request()->fullUrl(),
                                ]) }}"
                                    class="no-print inline-flex items-center gap-2 rounded-xl bg-sky-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-800">
                                    <i class="fas fa-eye"></i>
                                    View Complaint
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4 p-5">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                Technician's Reason
                            </p>
                            <p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-slate-800">
                                {{ $order->pending_reason ?: 'Not recorded' }}
                            </p>
                        </div>

                        <div class="grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-3">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Maintenance Team</p>
                                <p class="mt-1 break-words font-semibold text-slate-800">
                                    {{ $order->technicians->pluck('full_name')->filter()->join(', ') ?: 'Not recorded' }}
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
                            <div class="rounded-xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900">
                                <strong>Follow-up Visit #{{ $followUp->visit_number }}</strong>

                                <p class="mt-1">
                                    Date:
                                    {{ $followUp->schedule?->schedule_date
                                        ? \Carbon\Carbon::parse($followUp->schedule->schedule_date)->format('M d, Y')
                                        : 'Date unavailable' }}
                                </p>

                                <p class="mt-1 text-xs">
                                    Schedule: {{ $followUp->schedule?->status ?? 'Not finalized' }}
                                    · Work Order: {{ $followUp->status }}
                                </p>
                            </div>
                        @else
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                                No follow-up visit has been planned yet.
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white px-6 py-14 text-center">
                    <i class="fas fa-clipboard-check text-3xl text-slate-300"></i>
                    <h3 class="mt-3 font-semibold text-slate-800">No pending workloads found</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        No unfinished maintenance reports match your filters.
                    </p>
                </div>
            @endforelse
        </div>

        @if ($workOrders->hasPages())
            <div class="no-print rounded-xl border border-slate-200 bg-white p-4">
                {{ $workOrders->withQueryString()->links() }}
            </div>
        @endif

        <p class="no-print text-xs text-slate-500">
            Showing {{ $workOrders->firstItem() ?? 0 }}–{{ $workOrders->lastItem() ?? 0 }}
            of {{ $workOrders->total() }} matching reports.
        </p>
    </div>

    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 9mm;
            }

            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .print-card {
                break-inside: avoid;
                box-shadow: none !important;
            }
        }
    </style>
@endsection
