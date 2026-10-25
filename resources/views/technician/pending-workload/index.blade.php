@extends('technician.layouts.app')

@section('title', 'Pending Workload')

@section('content')

    @php
        $filter = $filter ?? 'all';
        $hasFilters = request()->filled('search') || $filter !== 'all';

        $followupStyles = [
            'Awaiting Reschedule' => 'border-amber-200 bg-amber-50 text-amber-700',
            'Follow-up Scheduled' => 'border-sky-200 bg-sky-50 text-sky-700',
            'Follow-up In Progress' => 'border-violet-200 bg-violet-50 text-violet-700',
            'Awaiting Manager Finalization' => 'border-orange-200 bg-orange-50 text-orange-700',
        ];
    @endphp

    <div class="mx-auto max-w-7xl space-y-6 pb-8">

        {{-- HEADER --}}

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-sky-600">
                    <i class="fas fa-screwdriver-wrench"></i>
                    Maintenance Operations
                </p>

                <h1 class="mt-2 text-2xl font-bold text-slate-900 sm:text-3xl">
                    Pending Workload
                </h1>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Monitor unresolved maintenance visits and their follow-up progress.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 self-start">
                <a href="{{ route('technician.pending-workload.print', request()->only(['search', 'filter'])) }}"
                   target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-sky-700">
                    <i class="fas fa-print"></i>
                    Print Report
                </a>
                <span
                    class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">
                <i class="fas fa-clock-rotate-left"></i>
                Unresolved Maintenance
                </span>
            </div>

        </div>

        {{-- STATISTICS --}}

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Total Pending
                        </p>
                        <h2 class="mt-2 text-3xl font-bold text-slate-900">
                            {{ $totalPending }}
                        </h2>
                        <p class="mt-2 text-xs text-slate-500">
                            Unresolved assigned visits
                        </p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <i class="fas fa-list-check text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Awaiting Reschedule
                        </p>
                        <h2 class="mt-2 text-3xl font-bold text-slate-900">
                            {{ $awaitingFollowupCount }}
                        </h2>
                        <p class="mt-2 text-xs text-slate-500">
                            No follow-up visit created
                        </p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                        <i class="fas fa-calendar-xmark text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Follow-up Created
                        </p>
                        <h2 class="mt-2 text-3xl font-bold text-slate-900">
                            {{ $followupScheduledCount }}
                        </h2>
                        <p class="mt-2 text-xs text-slate-500">
                            Includes visits awaiting finalization
                        </p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                        <i class="fas fa-calendar-check text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Oldest Pending
                        </p>
                        <h2 class="mt-2 text-xl font-bold text-slate-900">
                            {{ $oldestPending?->pending_recorded_at?->format('M d, Y') ?? 'None' }}
                        </h2>
                        <p class="mt-2 text-xs text-slate-500">
                            Across all unresolved visits
                        </p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- INFORMATION --}}

        <div class="flex items-start gap-3 rounded-2xl border border-sky-200 bg-sky-50 p-4">
            <i class="fas fa-circle-info mt-1 text-sky-600"></i>

            <div>
                <p class="text-sm font-semibold text-sky-900">
                    Follow-up Tracking
                </p>

                <p class="mt-1 text-sm leading-6 text-sky-800">
                    Pending visits remain visible until a later visit for the same
                    complaint is completed. Follow-up scheduling is managed by
                    the Maintenance Manager. This page is for tracking only and
                    does not modify maintenance schedules or work-order statuses.
                </p>
            </div>
        </div>

        {{-- FILTERS --}}

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">

            <form method="GET" action="{{ route('technician.pending-workload.index') }}" class="space-y-4">

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

                    <div class="lg:col-span-2">
                        <label for="pending-search" class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Search Complaints
                        </label>

                        <div class="relative">
                            <i
                                class="fas fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>

                            <input id="pending-search" type="search" name="search" value="{{ request('search') }}"
                                placeholder="Complaint number, category, name or location"
                                class="w-full rounded-xl border-slate-300 py-2.5 pl-10 pr-3 text-sm focus:border-sky-500 focus:ring-sky-500">
                        </div>
                    </div>

                    <div>
                        <label for="pending-filter" class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Follow-up Status
                        </label>

                        <select id="pending-filter" name="filter"
                            class="w-full rounded-xl border-slate-300 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">

                            <option value="all" @selected($filter === 'all')>
                                All Unresolved
                            </option>

                            <option value="awaiting_followup" @selected($filter === 'awaiting_followup')>
                                Awaiting Reschedule
                            </option>

                            <option value="followup_scheduled" @selected($filter === 'followup_scheduled')>
                                Follow-up Created
                            </option>

                        </select>
                    </div>

                </div>

                <div class="flex flex-wrap items-center gap-3">

                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700">
                        <i class="fas fa-filter"></i>
                        Apply Filters
                    </button>

                    @if ($hasFilters)
                        <a href="{{ route('technician.pending-workload.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                            <i class="fas fa-xmark"></i>
                            Clear Filters
                        </a>
                    @endif

                </div>

            </form>

        </div>

        {{-- WORK ORDER LIST --}}

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div
                class="flex flex-col gap-2 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">

                <div>
                    <h2 class="text-base font-bold text-slate-900">
                        <i class="fas fa-clipboard-list mr-2 text-amber-600"></i>
                        Pending Maintenance Visits
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Oldest unresolved visits first
                    </p>
                </div>

                <span class="text-xs text-slate-500">
                    Showing {{ $workOrders->firstItem() ?? 0 }}
                    – {{ $workOrders->lastItem() ?? 0 }}
                    of {{ $workOrders->total() }}
                </span>

            </div>

            <div class="divide-y divide-slate-100">

                @forelse ($workOrders as $workOrder)
                    @php
                        $complaint = $workOrder->complaint;
                        $followup = $workOrder->latestFollowup;
                        $followupStatus = $workOrder->followup_display_status ?? 'Awaiting Reschedule';

                        $followupStyle =
                            $followupStyles[$followupStatus] ?? 'border-slate-200 bg-slate-50 text-slate-600';

                        $pendingDate = $workOrder->pending_recorded_at;

                        $pendingDays = $pendingDate
                            ? max(
                                0,
                                (int) $pendingDate
                                    ->copy()
                                    ->startOfDay()
                                    ->diffInDays(now('Asia/Manila')->startOfDay()),
                            )
                            : null;

                        $urgency = strtoupper(trim((string) ($complaint?->aiAnalysis?->urgency_level ?? '')));

                        $urgencyStyle = match ($urgency) {
                            'CRITICAL', 'HIGH' => 'border-red-200 bg-red-50 text-red-700',
                            'MEDIUM', 'MODERATE' => 'border-amber-200 bg-amber-50 text-amber-700',
                            'LOW' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                            default => 'border-slate-200 bg-slate-50 text-slate-600',
                        };
                    @endphp

                    <div class="p-4 transition hover:bg-slate-50/70 sm:p-5">

                        <div class="flex flex-col gap-4 xl:flex-row xl:items-start">

                            {{-- PENDING DURATION --}}

                            <div class="xl:w-28 xl:shrink-0">

                                <div
                                    class="inline-flex flex-col rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 xl:w-full xl:items-center">

                                    <span class="text-[11px] font-semibold text-amber-700">
                                        Pending For
                                    </span>

                                    <span class="mt-1 text-2xl font-bold text-amber-800">
                                        {{ $pendingDays ?? '—' }}
                                    </span>

                                    <span class="text-[11px] text-amber-700">
                                        {{ $pendingDays === 1 ? 'day' : 'days' }}
                                    </span>

                                </div>

                            </div>

                            {{-- MAIN CONTENT --}}

                            <div class="min-w-0 flex-1">

                                <div class="flex flex-wrap items-center gap-2">

                                    <span class="text-sm font-bold text-sky-700">
                                        {{ $complaint?->complaint_no ?? 'Complaint unavailable' }}
                                    </span>

                                    <span
                                        class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700">
                                        Pending Workload
                                    </span>

                                    @if ($urgency)
                                        <span
                                            class="rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $urgencyStyle }}">
                                            {{ $urgency }} Urgency
                                        </span>
                                    @endif

                                </div>

                                <h3 class="mt-2 text-base font-semibold text-slate-900">
                                    {{ $complaint?->category?->name ?? 'Uncategorized Complaint' }}
                                </h3>

                                <div class="mt-3 grid grid-cols-1 gap-3 text-sm md:grid-cols-2">

                                    <div class="flex items-start gap-2 text-slate-600">
                                        <i class="fas fa-user mt-1 w-4 text-slate-400"></i>
                                        <span class="break-words">
                                            {{ $complaint?->consumer?->full_name ?? ($complaint?->complainant_name ?? 'Unknown Consumer') }}
                                        </span>
                                    </div>

                                    <div class="flex items-start gap-2 text-slate-600">
                                        <i class="fas fa-calendar-days mt-1 w-4 text-slate-400"></i>
                                        <span>
                                            Original Schedule:
                                            <strong>
                                                {{ $workOrder->schedule?->schedule_date?->format('M d, Y') ?? 'N/A' }}
                                            </strong>
                                        </span>
                                    </div>

                                    <div class="flex items-start gap-2 text-slate-600">
                                        <i class="fas fa-hashtag mt-1 w-4 text-slate-400"></i>
                                        Visit {{ $workOrder->visit_number }}

                                        @if ($workOrder->dispatch_type === 'Emergency')
                                            <span class="ml-2 text-xs font-semibold text-red-600">
                                                <i class="fas fa-bolt mr-1"></i>
                                                Emergency
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-start gap-2 text-slate-600">
                                        <i class="fas fa-location-dot mt-1 w-4 text-slate-400"></i>
                                        <span class="break-words">
                                            {{ $complaint?->address ?: 'Address unavailable' }}
                                        </span>
                                    </div>

                                </div>

                                {{-- PENDING REASON --}}

                                <div class="mt-4 rounded-xl border border-amber-100 bg-amber-50/60 p-3">

                                    <p class="flex items-center gap-2 text-xs font-bold text-amber-800">
                                        <i class="fas fa-circle-exclamation"></i>
                                        Reason for Pending
                                    </p>

                                    <p class="mt-1.5 whitespace-pre-line text-sm leading-6 text-amber-900">
                                        {{ $workOrder->pending_reason ?: 'No pending reason recorded.' }}
                                    </p>

                                </div>

                                {{-- FOLLOW-UP TRACKING --}}

                                <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-4">

                                    <div class="flex flex-wrap items-center justify-between gap-3">

                                        <p class="flex items-center gap-2 text-sm font-bold text-slate-800">
                                            <i class="fas fa-arrows-rotate text-sky-600"></i>
                                            Follow-up Tracking
                                        </p>

                                        <span
                                            class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $followupStyle }}">
                                            {{ $followupStatus }}
                                        </span>

                                    </div>

                                    @if ($followup)
                                        <div class="mt-3 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">

                                            <div>
                                                <p class="text-xs text-slate-500">
                                                    Follow-up Visit
                                                </p>
                                                <p class="mt-1 font-semibold text-slate-800">
                                                    Visit {{ $followup->visit_number }}
                                                </p>
                                            </div>

                                            <div>
                                                <p class="text-xs text-slate-500">
                                                    Schedule Date
                                                </p>
                                                <p class="mt-1 font-semibold text-slate-800">
                                                    {{ $followup->schedule?->schedule_date?->format('M d, Y') ?? 'Not finalized' }}
                                                </p>
                                            </div>

                                            <div>
                                                <p class="text-xs text-slate-500">
                                                    Work Order Status
                                                </p>
                                                <p class="mt-1 font-semibold text-slate-800">
                                                    {{ $followup->status }}
                                                </p>
                                            </div>

                                            <div>
                                                <p class="text-xs text-slate-500">
                                                    Schedule Approval
                                                </p>
                                                <p class="mt-1 font-semibold text-slate-800">
                                                    {{ $followup->schedule?->status === 'Finalized' ? 'Finalized by Manager' : 'Awaiting Finalization' }}
                                                </p>
                                            </div>

                                        </div>
                                    @else
                                        <p class="mt-2 text-sm leading-6 text-slate-500">
                                            No follow-up visit has been created for this complaint.
                                            Awaiting the Maintenance Manager's scheduling action.
                                        </p>
                                    @endif

                                </div>

                                {{-- MANAGER REMARKS --}}

                                @if ($workOrder->manager_remarks)
                                    <div class="mt-3 rounded-xl border border-slate-200 bg-white p-3">

                                        <p class="text-xs font-semibold text-slate-700">
                                            Manager Remarks
                                        </p>

                                        <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-600">
                                            {{ $workOrder->manager_remarks }}
                                        </p>

                                    </div>
                                @endif

                                <p class="mt-3 text-xs text-slate-400">
                                    Pending recorded:
                                    {{ $pendingDate?->format('M d, Y h:i A') ?? 'Not available' }}
                                </p>

                            </div>

                            {{-- ACTION --}}

                            <div class="xl:shrink-0">

                                @if ($complaint)
                                    <a href="{{ route('technician.complaints.show', $complaint) }}"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700 xl:w-auto">

                                        View Complaint
                                        <i class="fas fa-arrow-right"></i>

                                    </a>
                                @endif

                            </div>

                        </div>

                    </div>

                @empty

                    {{-- EMPTY STATE --}}

                    <div class="px-5 py-16 text-center">

                        <div
                            class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                            <i class="fas fa-circle-check text-2xl"></i>
                        </div>

                        <h3 class="mt-4 text-lg font-bold text-slate-900">
                            {{ $hasFilters ? 'No matching pending work orders' : 'No unresolved pending workload' }}
                        </h3>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                            {{ $hasFilters
                                ? 'Try changing your search or follow-up status filter.'
                                : 'There are currently no unresolved pending maintenance visits assigned to you.' }}
                        </p>

                        @if ($hasFilters)
                            <a href="{{ route('technician.pending-workload.index') }}"
                                class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-sky-700">
                                <i class="fas fa-xmark"></i>
                                Clear Filters
                            </a>
                        @endif

                    </div>
                @endforelse

            </div>

            {{-- PAGINATION --}}

            @if ($workOrders->hasPages())
                <div class="border-t border-slate-100 px-4 py-4 sm:px-6">
                    {{ $workOrders->links() }}
                </div>
            @endif

        </div>

    </div>

@endsection
