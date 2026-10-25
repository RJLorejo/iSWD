
@extends('customer-service.layouts.app')

@section('title', 'Missed Schedule Monitoring')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6 pb-10">

        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-semibold text-sky-700">Maintenance Monitoring</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">Missed Schedules</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Monitor finalized maintenance visits that remain unstarted after their scheduled date.
                </p>
            </div>

            <a
                href="{{ route('customer-service.missed-schedules.print', request()->query()) }}"
                class="no-print inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                <i class="fas fa-print"></i>
                Print Report
            </a>
        </div>

        <div class="rounded-xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900">
            <i class="fas fa-eye mr-1"></i>
            <strong>Monitoring view:</strong>
            Customer Service can review missed visits and submitted explanations. Schedule changes remain restricted to
            Maintenance Management.
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-100 text-red-700">
                        <i class="fas fa-calendar-xmark"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Missed Work Orders</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ $totalMissed }}</p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-slate-500">
                    Total overdue, finalized, unstarted work orders.
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold text-slate-500">Report Date</p>
                <p class="mt-2 text-lg font-bold text-slate-900">
                    {{ \Carbon\Carbon::parse($today)->format('F d, Y') }}
                </p>
            </div>
        </div>

        <form
            method="GET"
            action="{{ route('customer-service.missed-schedules.index') }}"
            class="no-print rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
        >
            <div class="grid gap-3 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label for="search" class="mb-1 block text-xs font-semibold text-slate-600">
                        Search Complaint
                    </label>
                    <input
                        id="search"
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Complaint number, concern, or address"
                        class="w-full rounded-xl border-slate-300 text-sm"
                    >
                </div>

                <div>
                    <label for="date_from" class="mb-1 block text-xs font-semibold text-slate-600">
                        From Date
                    </label>
                    <input
                        id="date_from"
                        type="date"
                        name="date_from"
                        value="{{ $dateFrom }}"
                        max="{{ $today }}"
                        class="w-full rounded-xl border-slate-300 text-sm"
                    >
                </div>

                <div>
                    <label for="date_to" class="mb-1 block text-xs font-semibold text-slate-600">
                        To Date
                    </label>
                    <input
                        id="date_to"
                        type="date"
                        name="date_to"
                        value="{{ $dateTo }}"
                        max="{{ $today }}"
                        class="w-full rounded-xl border-slate-300 text-sm"
                    >
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <button
                    type="submit"
                    class="rounded-xl bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-800"
                >
                    <i class="fas fa-filter mr-1"></i> Apply Filters
                </button>

                <a
                    href="{{ route('customer-service.missed-schedules.index') }}"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                >
                    Reset
                </a>
            </div>
        </form>

        <div class="space-y-4">
            @forelse ($workOrders as $order)
                @php
                    $complaint = $order->complaint;
                    $scheduledDate = $order->schedule?->schedule_date;

                    $daysOverdue = $scheduledDate
                        ? \Carbon\Carbon::parse($scheduledDate)
                            ->startOfDay()
                            ->diffInDays(\Carbon\Carbon::parse($today)->startOfDay())
                        : 0;
                @endphp

                <article class="print-card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 bg-slate-50 p-5">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-bold text-slate-900">
                                    {{ $complaint?->complaint_no ?? 'Unknown Complaint' }}
                                </h2>

                                <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                    Missed Schedule
                                </span>
                            </div>

                            <p class="mt-2 text-sm font-medium text-slate-700">
                                {{ $complaint?->category?->name ?? 'Maintenance Concern' }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-800">
                                {{ (int) $daysOverdue }}
                                {{ (int) $daysOverdue === 1 ? 'day' : 'days' }} overdue
                            </span>

                            @if ($complaint)
                                <a
                                    href="{{ route('customer-service.complaints.show', [
                                        'complaint' => $complaint->id,
                                        'back_url' => request()->fullUrl(),
                                    ]) }}"
                                    class="no-print inline-flex items-center gap-2 rounded-xl bg-sky-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-800"
                                >
                                    <i class="fas fa-eye"></i>
                                    View Complaint
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4 p-5">
                        <p class="break-words text-sm leading-6 text-slate-700">
                            {{ \Illuminate\Support\Str::limit($complaint?->description ?? 'No description', 200) }}
                        </p>

                        <p class="break-words text-sm text-slate-600">
                            <i class="fas fa-location-dot mr-1 text-slate-400"></i>
                            {{ $complaint?->address ?? 'No address recorded' }}
                        </p>

                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Scheduled Date</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $scheduledDate
                                        ? \Carbon\Carbon::parse($scheduledDate)->format('M d, Y')
                                        : 'Not recorded' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Work Order</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    #{{ $order->id }} · Visit {{ $order->visit_number }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Maintenance Team</p>
                                <p class="mt-1 break-words text-sm font-semibold text-slate-800">
                                    {{ $order->technicians->pluck('full_name')->filter()->join(', ') ?: 'Not recorded' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Complaint Status</p>
                                <p class="mt-1 text-sm font-semibold text-slate-800">
                                    {{ $complaint?->status ?? 'Unknown' }}
                                </p>
                            </div>
                        </div>

                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <p class="text-sm font-semibold text-amber-900">
                                Awaiting Maintenance Manager Review
                            </p>
                            <p class="mt-1 text-sm text-amber-800">
                                This finalized visit remains Scheduled even though its planned date has passed.
                                Customer Service should refer schedule corrections to Maintenance Management.
                            </p>
                        </div>

                        @if ($order->missed_reported_at)
                            <div class="rounded-xl border border-sky-200 bg-sky-50 p-4">
                                <p class="text-sm font-semibold text-sky-900">
                                    Technician's Submitted Explanation
                                </p>

                                <p class="mt-2 whitespace-pre-line break-words text-sm text-sky-800">
                                    {{ $order->missed_reason ?: 'No explanation recorded.' }}
                                </p>

                                <p class="mt-2 text-xs text-sky-700">
                                    Reported
                                    {{ $order->missed_reported_at->timezone('Asia/Manila')->format('M d, Y g:i A') }}
                                </p>
                            </div>
                        @else
                            <p class="text-xs text-slate-500">
                                No technician explanation has been submitted yet.
                            </p>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white px-6 py-14 text-center">
                    <i class="fas fa-calendar-check text-3xl text-emerald-500"></i>
                    <h3 class="mt-3 font-semibold text-slate-900">No missed schedules found</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        No overdue, finalized, unstarted visits match your filters.
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
            of {{ $workOrders->total() }} matching work orders.
        </p>
    </div>

    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 9mm;
            }

            body {
                background: white !important;
            }

            .no-print {
                display: none !important;
            }

            .print-card {
                break-inside: avoid;
                box-shadow: none !important;
            }
        }
    </style>
@endsection
