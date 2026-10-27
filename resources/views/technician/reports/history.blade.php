@extends('technician.layouts.app')

@section('title', 'Maintenance Report History')

@section('content')
@php
    $status = request('status', 'all');
    $hasFilters = request()->filled('search') || (request()->filled('status') && $status !== 'all') || request()->filled('from') || request()->filled('to');
@endphp
<div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-sky-700">Maintenance Technician</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">Maintenance Report History</h1>
            <p class="mt-1 text-sm text-slate-500">Accomplishment reports you submitted and their manager review status.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('technician.maintenance-reports.history.print', request()->only(['search', 'status', 'from', 'to'])) }}"
               target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-800">
                <i class="fas fa-print"></i> Print Report
            </a>
        <a href="{{ route('technician.maintenance-reports.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <i class="fas fa-chart-simple"></i> Maintenance Reports Overview
        </a>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ([
            ['label' => 'Total Submitted', 'value' => $statistics['total'], 'icon' => 'fa-file-lines', 'color' => 'text-sky-700 bg-sky-50'],
            ['label' => 'Pending Review', 'value' => $statistics['pending'], 'icon' => 'fa-clock', 'color' => 'text-blue-700 bg-blue-50'],
            ['label' => 'Approved', 'value' => $statistics['approved'], 'icon' => 'fa-circle-check', 'color' => 'text-emerald-700 bg-emerald-50'],
            ['label' => 'Returned', 'value' => $statistics['returned'], 'icon' => 'fa-rotate-left', 'color' => 'text-amber-700 bg-amber-50'],
        ] as $stat)
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-xs font-semibold text-slate-500">{{ $stat['label'] }}</p>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $stat['color'] }}"><i class="fas {{ $stat['icon'] }}"></i></span>
                </div>
                <p class="mt-3 text-2xl font-bold text-slate-900">{{ number_format($stat['value']) }}</p>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-5">
            <h2 class="text-base font-bold text-slate-900">Find a Report</h2>
            <form method="GET" action="{{ route('technician.maintenance-reports.history') }}" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                <div class="xl:col-span-2">
                    <label for="search" class="mb-1 block text-xs font-semibold text-slate-600">Complaint Number or Type</label>
                    <input id="search" name="search" value="{{ request('search') }}" maxlength="120" placeholder="Search complaint..." class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                </div>
                <div>
                    <label for="status" class="mb-1 block text-xs font-semibold text-slate-600">Review Status</label>
                    <select id="status" name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                        @foreach (['all' => 'All Statuses', 'Pending Review' => 'Pending Review', 'Approved' => 'Approved', 'Returned' => 'Returned'] as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="from" class="mb-1 block text-xs font-semibold text-slate-600">Submitted From</label>
                    <input id="from" type="date" name="from" value="{{ request('from') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                </div>
                <div>
                    <label for="to" class="mb-1 block text-xs font-semibold text-slate-600">Submitted To</label>
                    <input id="to" type="date" name="to" value="{{ request('to') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                </div>
                <div class="flex flex-wrap items-end gap-2 xl:col-span-5">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-800"><i class="fas fa-filter"></i> Apply Filters</button>
                    @if ($hasFilters)
                        <a href="{{ route('technician.maintenance-reports.history') }}" class="inline-flex items-center rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Clear Filters</a>
                    @endif
                </div>
            </form>
            @if ($errors->any())
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif
        </div>

        <div class="space-y-3 p-4 sm:p-5">
            @forelse ($reports as $report)
                @php
                    $complaint = $report->complaint;
                    $badgeClass = match ($report->review_status) {
                        'Approved' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                        'Returned' => 'border-amber-200 bg-amber-50 text-amber-800',
                        default => 'border-blue-200 bg-blue-50 text-blue-700',
                    };
                @endphp
                <div class="rounded-xl border border-slate-200 p-4 transition hover:border-sky-200 hover:bg-sky-50/20">
                    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-bold text-slate-900">{{ $complaint?->complaint_no ?? 'Complaint unavailable' }}</p>
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">{{ $report->review_status }}</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-700">{{ $complaint?->category?->name ?? 'Uncategorized' }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $complaint?->division?->name ?? 'Maintenance' }} · Report #{{ $report->id }}</p>
                        </div>
                        <div class="text-xs text-slate-500 sm:text-right">
                            <p>Submitted: <span class="font-semibold text-slate-700">{{ $report->submitted_at ? \Carbon\Carbon::parse($report->submitted_at)->format('M d, Y h:i A') : '—' }}</span></p>
                            @if ($report->resubmitted_at)
                                <p class="mt-1">Last resubmitted: {{ \Carbon\Carbon::parse($report->resubmitted_at)->format('M d, Y h:i A') }}</p>
                            @endif
                            @if ($report->reviewed_at)
                                <p class="mt-1">Reviewed: {{ \Carbon\Carbon::parse($report->reviewed_at)->format('M d, Y h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                    @if ($report->review_status === 'Returned' && filled($report->review_remarks))
                        <div class="mt-3 rounded-lg border border-amber-100 bg-amber-50 p-3 text-xs leading-5 text-amber-900">
                            <p class="font-bold">Manager's Correction Remarks</p>
                            <p class="mt-1 whitespace-pre-line break-words">{{ $report->review_remarks }}</p>
                        </div>
                    @endif
                    @if ($complaint)
                        <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-3">
                            <a href="{{ route('technician.maintenance-reports.show', $complaint) }}" class="inline-flex items-center gap-2 rounded-lg bg-sky-700 px-3 py-2 text-xs font-semibold text-white hover:bg-sky-800"><i class="fas fa-eye"></i> View Report</a>
                            <a href="{{ route('technician.maintenance-reports.print', $complaint) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"><i class="fas fa-print"></i> Print</a>
                            @if ($report->review_status === 'Returned')
                                <a href="{{ route('technician.maintenance-reports.create', $complaint) }}" class="inline-flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800 hover:bg-amber-100"><i class="fas fa-pen"></i> Revise Report</a>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="py-14 text-center">
                    <i class="fas fa-file-circle-xmark text-3xl text-slate-300"></i>
                    <h3 class="mt-3 font-bold text-slate-800">No submitted reports found</h3>
                    <p class="mt-1 text-sm text-slate-500">Try different filters or submit an accomplishment report first.</p>
                </div>
            @endforelse
        </div>
        @if ($reports->hasPages())
            <div class="border-t border-slate-100 px-5 py-4">{{ $reports->links() }}</div>
        @endif
    </div>
</div>
@endsection
