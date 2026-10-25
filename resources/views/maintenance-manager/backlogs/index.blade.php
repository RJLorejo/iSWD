
@extends('maintenance-manager.layouts.app')

@section('title', 'Missed Schedules')

@section('content')
<div class="mx-auto max-w-7xl space-y-6 pb-12">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-sky-700">
                Maintenance Management
            </p>

            <h1 class="mt-1 text-2xl font-bold text-slate-900">
                Missed Schedules
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Review finalized maintenance visits that were not started
                on their scheduled date.
            </p>
        </div>

        <button type="button"
            onclick="printMissedSchedulesReport()"
            class="no-print inline-flex items-center justify-center gap-2
                rounded-xl border border-slate-200 bg-white
                px-4 py-2.5 text-sm font-semibold text-slate-700
                hover:bg-slate-50">
            <i class="fas fa-print"></i>
            Print Report
        </button>
    </div>

    {{-- Summary --}}
    <div class="grid gap-3 sm:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center
                    rounded-xl bg-red-100 text-red-700">
                    <i class="fas fa-calendar-xmark"></i>
                </div>

                <div>
                    <p class="text-xs font-semibold text-slate-500">
                        Missed Work Orders
                    </p>

                    <p class="mt-1 text-2xl font-bold text-slate-900">
                        {{ $totalMissed }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">
                Report Date
            </p>

            <p class="mt-2 text-lg font-bold text-slate-900">
                {{ \Carbon\Carbon::parse($today)->format('F d, Y') }}
            </p>


        </div>
    </div>

    {{-- Search and date filters --}}
    <form method="GET"
        action="{{ route('maintenance-manager.backlogs.index') }}"
        class="no-print rounded-2xl border border-slate-200
            bg-white p-4 shadow-sm">

        <div class="grid gap-3 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="mb-1 block text-xs font-semibold text-slate-600">
                    Search Complaint
                </label>

                <input type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Complaint number, concern, address..."
                    class="w-full rounded-xl border-slate-300 text-sm">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">
                    From Date
                </label>

                <input type="date"
                    name="date_from"
                    value="{{ $dateFrom }}"
                    max="{{ $today }}"
                    class="w-full rounded-xl border-slate-300 text-sm">
            </div>

            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600">
                    To Date
                </label>

                <input type="date"
                    name="date_to"
                    value="{{ $dateTo }}"
                    max="{{ $today }}"
                    class="w-full rounded-xl border-slate-300 text-sm">
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <button type="submit"
                class="rounded-xl bg-sky-700 px-5 py-2.5
                    text-sm font-semibold text-white hover:bg-sky-800">
                <i class="fas fa-filter mr-1"></i>
                Apply Filters
            </button>

            <a href="{{ route('maintenance-manager.backlogs.index') }}"
                class="rounded-xl border border-slate-200 px-5 py-2.5
                    text-sm font-semibold text-slate-600 hover:bg-slate-50">
                Reset
            </a>
        </div>
    </form>

    @if ($errors->any())
        <div class="no-print rounded-xl border border-red-200
            bg-red-50 p-4 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Print heading --}}
    <div class="print-heading hidden">
        <h1>Sagay Water District</h1>
        <h2>Missed Maintenance Schedules</h2>
        <p>
            Printed:
            {{ now('Asia/Manila')->format('F d, Y g:i A') }}
        </p>
        <p>
            Search: {{ $search ?: 'All' }}
            | From: {{ $dateFrom ?: 'All' }}
            | To: {{ $dateTo ?: 'All' }}
        </p>
        <p>
            Page {{ $workOrders->currentPage() }}
            of {{ $workOrders->lastPage() }}
            | {{ $workOrders->total() }} matching work orders
        </p>
    </div>

    {{-- Work order cards --}}
    <div class="space-y-4">
        @forelse ($workOrders as $order)
            @php
                $complaint = $order->complaint;
                $scheduledDate = $order->schedule?->schedule_date;

                $daysOverdue = $scheduledDate
                    ? \Carbon\Carbon::parse($scheduledDate)
                        ->startOfDay()
                        ->diffInDays(
                            \Carbon\Carbon::parse($today)->startOfDay()
                        )
                    : 0;
            @endphp

            <article class="print-card overflow-hidden rounded-2xl
                border border-slate-200 bg-white shadow-sm">

                <div class="flex flex-wrap items-start justify-between
                    gap-3 border-b border-slate-100 bg-slate-50 p-5">

                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-bold text-slate-900">
                                {{ $complaint?->complaint_no ?? 'Unknown Complaint' }}
                            </h2>

                            <span class="rounded-full bg-red-100
                                px-2.5 py-1 text-xs font-semibold text-red-700">
                                Missed Schedule
                            </span>
                        </div>

                        <p class="mt-2 text-sm font-medium text-slate-700">
                            {{ $complaint?->category?->name ?? 'Maintenance Concern' }}
                        </p>
                    </div>

                    <span class="rounded-full bg-amber-100
                        px-3 py-1.5 text-xs font-semibold text-amber-800">
                        {{ (int) $daysOverdue }}
                        {{ (int) $daysOverdue === 1 ? 'day' : 'days' }} overdue
                    </span>
                </div>

                <div class="space-y-4 p-5">

                    <p class="text-sm leading-6 text-slate-700">
                        {{ \Illuminate\Support\Str::limit(
                            $complaint?->description ?? 'No description',
                            200
                        ) }}
                    </p>

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs text-slate-500">
                                Scheduled Date
                            </p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $scheduledDate
                                    ? \Carbon\Carbon::parse($scheduledDate)->format('M d, Y')
                                    : 'Not recorded' }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs text-slate-500">
                                Work Order
                            </p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                #{{ $order->id }}
                                · Visit {{ $order->visit_number }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs text-slate-500">
                                Maintenance Team
                            </p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $order->technicians
                                    ->pluck('full_name')
                                    ->join(', ') ?: 'Not recorded' }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-xs text-slate-500">
                                Complaint Status
                            </p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">
                                {{ $complaint?->status ?? 'Unknown' }}
                            </p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-amber-200
                        bg-amber-50 p-4">

                        <p class="text-sm font-semibold text-amber-900">
                            Manager Review Required
                        </p>

                        <p class="mt-1 text-sm text-amber-800">
                            This finalized visit remains Scheduled even though
                            its work date has passed. Confirm with the
                            maintenance team whether the visit actually took
                            place before making any schedule changes.
                        </p>
                    </div>

                    @if ($order->missed_reported_at)
                        <div class="rounded-xl border border-sky-200 bg-sky-50 p-4">
                            <p class="text-sm font-semibold text-sky-900">Plumber's Submitted Explanation</p>
                            <p class="mt-2 whitespace-pre-line text-sm text-sky-800">{{ $order->missed_reason }}</p>
                            <p class="mt-2 text-xs text-sky-700">Reported {{ $order->missed_reported_at->format('M d, Y h:i A') }}</p>
                        </div>
                    @else
                        <p class="text-xs text-slate-500">No plumber explanation submitted yet. Manager may verify and record the reason.</p>
                    @endif

                    <div class="no-print flex flex-wrap gap-2
                        border-t border-slate-100 pt-4">

                        <a href="{{ route(
                            'maintenance-manager.complaints.show',
                            $order->complaint_id
                        ) }}"
                            class="rounded-xl bg-sky-700 px-4 py-2.5
                                text-xs font-semibold text-white
                                hover:bg-sky-800">
                            <i class="fas fa-eye mr-1"></i>
                            Review Complaint
                        </a>

                        <a href="{{ route('maintenance-manager.backlogs.reschedule.edit', $order) }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-amber-700">
                            <i class="fas fa-calendar-plus"></i>
                            Review &amp; Reschedule
                        </a>
                    </div>
                </div>
            </article>

        @empty
            <div class="rounded-2xl border border-slate-200
                bg-white px-6 py-14 text-center">

                <i class="fas fa-calendar-check
                    text-3xl text-emerald-500"></i>

                <h3 class="mt-3 font-semibold text-slate-900">
                    No missed schedules found
                </h3>

                <p class="mt-2 text-sm text-slate-500">
                    There are no finalized, unstarted overdue visits
                    matching your filters.
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($workOrders->hasPages())
        <div class="no-print rounded-xl border border-slate-200
            bg-white p-4">
            {{ $workOrders->links() }}
        </div>
    @endif

    <p class="no-print text-xs text-slate-500">
        Showing {{ $workOrders->firstItem() ?? 0 }}
        to {{ $workOrders->lastItem() ?? 0 }}
        of {{ $workOrders->total() }} matching work orders.
        Maximum 10 per page.
    </p>
</div>

<style>
    @media print {
        @page {
            size: A4;
            margin: 12mm;
        }

        body {
            background: white !important;
        }

        body * {
            visibility: hidden !important;
        }

        .print-heading,
        .print-heading *,
        .print-card,
        .print-card * {
            visibility: visible !important;
        }

        .print-heading {
            display: block !important;
            position: relative;
            margin-bottom: 16px;
            font-size: 12px;
        }

        .print-heading h1 {
            font-size: 18px;
            font-weight: bold;
        }

        .print-heading h2 {
            font-size: 15px;
            font-weight: bold;
        }

        .print-card {
            position: relative;
            break-inside: avoid;
            margin-bottom: 12px;
            box-shadow: none !important;
        }

        .no-print,
        .no-print * {
            display: none !important;
            visibility: hidden !important;
        }
    }
</style>

@php
    $missedPrintPeriod = 'From: ' . ($dateFrom ?: 'All') . ' | To: ' . ($dateTo ?: 'All');
    $missedPrintCurrentPage = $workOrders->currentPage();
    $missedPrintLastPage = $workOrders->lastPage();
    $missedPrintTotal = $workOrders->total();
    $missedPrintRecords = $workOrders->getCollection()->map(function ($order) {
        return [
            'complaint' => $order->complaint?->complaint_no ?? 'Unknown',
            'work_order' => $order->id,
            'visit' => $order->visit_number,
            'date' => $order->schedule?->schedule_date?->format('M d, Y') ?? 'N/A',
            'category' => $order->complaint?->category?->name ?? 'Maintenance Concern',
            'team' => $order->technicians->pluck('full_name')->filter()->implode(', ') ?: 'Not recorded',
            'reason' => $order->missed_reason ?: 'No plumber explanation submitted',
            'status' => 'Awaiting Manager Review',
        ];
    })->values()->all();
@endphp

<script>
function printMissedSchedulesReport() {
    const sourceCards = Array.from(document.querySelectorAll('article.print-card'));
    const esc = value => String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    const records = @js($missedPrintRecords);
    const period = @js($missedPrintPeriod);
    const currentPage = @js($missedPrintCurrentPage);
    const lastPage = @js($missedPrintLastPage);
    const total = @js($missedPrintTotal);
    const rows = records.map(item => `<tr>
        <td><strong>${esc(item.complaint)}</strong><div>WO #${esc(item.work_order)} · Visit #${esc(item.visit)}</div></td>
        <td>${esc(item.date)}</td><td>${esc(item.category)}</td>
        <td>${esc(item.team)}</td><td>${esc(item.reason)}</td>
    </tr>`).join('');
    const tableHtml = `<thead><tr><th style="width:20%">Complaint / Work Order</th><th style="width:15%">Scheduled Date</th><th style="width:18%">Complaint Type</th><th style="width:22%">Assigned Plumber(s)</th><th style="width:25%">Plumber's Explanation</th></tr></thead><tbody>${rows || '<tr><td colspan="5">No matching records on this page.</td></tr>'}</tbody>`;
    openMaintenanceReport({
        title: 'Missed Maintenance Schedules',
        subtitle: 'Maintenance Management · Overdue Maintenance Visits',
        period,
        tableHtml,
        summary: `Records on this page: ${records.length} · All matching records: ${total} · Page ${currentPage} of ${lastPage}`
    });
}
</script>

@include('maintenance-manager.partials.report-print-template')
@endsection
