@extends('technician.layouts.app')

@section('title', $pageTitle)

@section('content')
@php
    $today = now('Asia/Manila')->toDateString();
@endphp
<div class="mx-auto max-w-7xl space-y-5 pb-10">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-sky-600">
                <i class="fas fa-screwdriver-wrench mr-1"></i> Maintenance Operations
            </p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">{{ $pageTitle }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                One row per assigned maintenance visit. Counts match the dashboard's visit metrics.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('technician.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <a href="{{ route('technician.complaints.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">
                <i class="fas fa-folder-open"></i> All Complaints
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <form method="GET" action="{{ route('technician.complaints.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <input type="hidden" name="scope" value="{{ $viewScope }}">
            <div class="min-w-0 flex-1">
                <label for="visit-search" class="mb-1.5 block text-xs font-semibold text-slate-600">Search visits</label>
                <input id="visit-search" name="search" type="search" value="{{ request('search') }}" placeholder="Complaint number, consumer, location, type..." class="w-full rounded-xl border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
            </div>
            <div class="sm:w-40">
                <label for="visit-urgency" class="mb-1.5 block text-xs font-semibold text-slate-600">AI Urgency</label>
                <select id="visit-urgency" name="urgency" class="w-full rounded-xl border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
                    <option value="">All urgency levels</option>
                    @foreach (['HIGH' => 'High', 'MODERATE' => 'Moderate', 'LOW' => 'Low'] as $value => $label)
                        <option value="{{ $value }}" @selected(strtoupper((string) request('urgency')) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-700">
                <i class="fas fa-filter mr-1"></i> Apply
            </button>
            <a href="{{ route('technician.complaints.index', ['scope' => $viewScope]) }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-center text-sm font-semibold text-slate-600 hover:bg-slate-50">Clear</a>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-4">
            <div>
                <h2 class="font-semibold text-slate-900">{{ $pageTitle }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ number_format($orders->total()) }} matching maintenance {{ $orders->total() === 1 ? 'visit' : 'visits' }}</p>
            </div>
            <span class="rounded-lg bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700">Visit-level results</span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($orders as $order)
                @php
                    $complaint = $order->complaint;
                    $urgency = strtoupper(trim((string) ($complaint?->aiAnalysis?->urgency_level ?? '')));
                    $date = $order->schedule?->schedule_date
                        ? \Carbon\Carbon::parse($order->schedule->schedule_date)->toDateString()
                        : null;
                    $isOverdue = $order->status === 'Scheduled' && $date && $date < $today;
                    $isToday = $order->status === 'Scheduled' && $date === $today;
                    $duration = null;
                    if ($order->started_at && $order->completed_at) {
                        $seconds = $order->started_at->diffInSeconds($order->completed_at, false);
                        if ($seconds >= 0) {
                            $seconds = (int) round($seconds);
                            $duration = sprintf('%d hr %d min %d sec', intdiv($seconds, 3600), intdiv($seconds % 3600, 60), $seconds % 60);
                        }
                    }
                    $statusClass = match ($order->status) {
                        'Scheduled' => 'border-blue-200 bg-blue-50 text-blue-700',
                        'In Progress' => 'border-amber-200 bg-amber-50 text-amber-700',
                        'Pending Workload' => 'border-orange-200 bg-orange-50 text-orange-700',
                        'Completed' => 'border-green-200 bg-green-50 text-green-700',
                        default => 'border-slate-200 bg-slate-50 text-slate-600',
                    };
                @endphp
                <div class="flex flex-col gap-4 p-4 transition hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between sm:p-5">
                    <div class="min-w-0 flex-1 space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-bold text-sky-700">{{ $complaint?->complaint_no ?? 'Unknown Complaint' }}</span>
                            <span class="rounded-full border border-slate-200 bg-slate-50 px-2 py-0.5 text-[11px] font-semibold text-slate-700">Visit {{ $order->visit_number }}</span>
                            <span class="rounded-full border px-2 py-0.5 text-[11px] font-bold {{ $statusClass }}">{{ $order->status }}</span>
                            @if ($urgency === 'HIGH')
                                <span class="rounded-full bg-red-50 px-2 py-0.5 text-[11px] font-bold text-red-700"><i class="fas fa-triangle-exclamation mr-1"></i>High urgency</span>
                            @endif
                            @if ($isToday)
                                <span class="rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-bold text-sky-700">Today</span>
                            @elseif ($isOverdue)
                                <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[11px] font-bold text-rose-700">Overdue</span>
                            @endif
                        </div>
                        <p class="text-sm font-semibold text-slate-900">{{ $complaint?->category?->name ?? 'Uncategorized' }}</p>
                        <div class="flex flex-wrap gap-x-5 gap-y-1 text-xs text-slate-500">
                            <span><i class="far fa-user mr-1"></i>{{ $complaint?->consumer?->full_name ?? $complaint?->complainant_name ?? 'Unknown Consumer' }}</span>
                            <span><i class="far fa-calendar mr-1"></i>Scheduled: {{ $date ? \Carbon\Carbon::parse($date)->format('M d, Y') : 'Not scheduled' }}</span>
                            @if ($order->started_at)
                                <span><i class="far fa-clock mr-1"></i>Started: {{ $order->started_at->format('M d, Y h:i:s A') }}</span>
                            @endif
                            @if ($order->completed_at)
                                <span><i class="fas fa-check mr-1"></i>Completed: {{ $order->completed_at->format('M d, Y h:i:s A') }}</span>
                            @endif
                            @if ($duration)
                                <span><i class="fas fa-stopwatch mr-1"></i>Duration: {{ $duration }}</span>
                            @endif
                        </div>
                        @if ($order->status === 'Pending Workload' && $order->pending_reason)
                            <p class="rounded-lg bg-orange-50 px-3 py-2 text-xs text-orange-800"><strong>Pending reason:</strong> {{ $order->pending_reason }}</p>
                        @endif
                    </div>
                    @if ($complaint)
                        <a href="{{ route('technician.complaints.show', $complaint) }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 hover:bg-sky-100">
                            View Complaint <i class="fas fa-arrow-right"></i>
                        </a>
                    @endif
                </div>
            @empty
                <div class="p-12 text-center">
                    <i class="fas fa-clipboard-list text-3xl text-slate-300"></i>
                    <h3 class="mt-3 font-semibold text-slate-900">No matching visits</h3>
                    <p class="mt-1 text-sm text-slate-500">Try clearing your search filters or choosing another dashboard group.</p>
                </div>
            @endforelse
        </div>
        @if ($orders->hasPages())
            <div class="border-t border-slate-100 px-4 py-4 sm:px-6">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection
