@extends('maintenance-manager.layouts.app')

@section('title', 'Daily Maintenance Planning')

@section('content')
    @php
        $manilaToday = \Carbon\CarbonImmutable::now('Asia/Manila')->startOfDay();
        $todayDate = $manilaToday->toDateString();

        $selectedDate = \Carbon\CarbonImmutable::parse($date, 'Asia/Manila')->startOfDay();
        $dateLabel = $selectedDate->format('F d, Y');

        $previousWorkingDay = $selectedDate->subDay();

        while ($previousWorkingDay->isWeekend()) {
            $previousWorkingDay = $previousWorkingDay->subDay();
        }

        $nextWorkingDay = $selectedDate->addDay();

        while ($nextWorkingDay->isWeekend()) {
            $nextWorkingDay = $nextWorkingDay->addDay();
        }

        $todayOrders = collect($todaySchedule?->workOrders ?? []);
        $selectedOrders = collect($schedule?->workOrders ?? []);

        /*
|--------------------------------------------------------------------------
| Latest Rescheduling History
|--------------------------------------------------------------------------
| Retrieve the latest rescheduling event for each displayed work order.
| The reason is stored in maintenance_schedule_histories.
*/

        $displayedWorkOrderIds = $todayOrders->concat($selectedOrders)->pluck('id')->unique()->values();

        $missedOriginalIds = $todayOrders
            ->concat($selectedOrders)
            ->pluck('rescheduled_from_work_order_id')
            ->filter()
            ->unique()
            ->values();
        $missedOriginals = $missedOriginalIds->isNotEmpty()
            ? \App\Models\MaintenanceWorkOrder::query()
                ->with('schedule')
                ->whereIn('id', $missedOriginalIds)
                ->get()
                ->keyBy('id')
            : collect();

        $reschedulingHistories = $displayedWorkOrderIds->isNotEmpty()
            ? \App\Models\MaintenanceScheduleHistory::query()
                ->whereIn('maintenance_work_order_id', $displayedWorkOrderIds)
                ->where('action', 'Rescheduled')
                ->orderByDesc('id')
                ->get()
                ->unique('maintenance_work_order_id')
                ->keyBy('maintenance_work_order_id')
            : collect();

        // Show a follow-up action only for the latest unresolved visit of a complaint.
        // Same-day Pending Workload can still be resumed by the original team.
        $complaintIds = $todayOrders->concat($selectedOrders)->pluck('complaint_id')->filter()->unique();
        $latestVisitsByComplaint = $complaintIds->isEmpty()
            ? collect()
            : \App\Models\MaintenanceWorkOrder::query()
                ->whereIn('complaint_id', $complaintIds)
                ->with(['schedule', 'complaint'])
                ->orderByDesc('visit_number')
                ->orderByDesc('id')
                ->get()
                ->groupBy('complaint_id')
                ->map(fn($visits) => $visits->first());

        $followUpEligibleOrderIds = $latestVisitsByComplaint
            ->filter(function ($order) use ($manilaToday) {
                return $order->status === 'Pending Workload' &&
                    $order->schedule?->schedule_date &&
                    \Carbon\CarbonImmutable::parse($order->schedule->schedule_date)->startOfDay()->lt($manilaToday) &&
                    !in_array($order->complaint?->status, ['Completed', 'Closed'], true);
            })
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        // Flag the latest pending visit immediately, including on its scheduled day.
        // Only overdue visits offer creation of a separate follow-up work order.
        $pendingReviewOrderIds = $latestVisitsByComplaint
            ->filter(
                fn($order) => $order->status === 'Pending Workload' &&
                    !in_array($order->complaint?->status, ['Completed', 'Closed'], true),
            )
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        $isFinalized = $schedule?->status === 'Finalized';
        $isDraft = $schedule?->status === 'Draft';

        $readyComplaints = collect($planningComplaints ?? []);
        $pendingOrders = collect($pendingWorkOrders ?? []);
        $missedOrders = collect($overdueWorkOrders ?? []);
        $duties = collect($availableDuties ?? []);

        $attentionCount = $pendingOrders->count() + $missedOrders->count();

        $statusClass = function ($status) {
            return match ($status) {
                'Scheduled' => 'border-blue-200 bg-blue-50 text-blue-700',
                'In Progress' => 'border-amber-200 bg-amber-50 text-amber-700',
                'Completed' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                'Pending Workload' => 'border-orange-200 bg-orange-50 text-orange-700',
                'Cancelled', 'Rescheduled' => 'border-slate-200 bg-slate-100 text-slate-600',
                default => 'border-slate-200 bg-slate-50 text-slate-600',
            };
        };

        $makeOrderData = function ($orders, $dateValue, $section) use (
            $followUpEligibleOrderIds,
            $pendingReviewOrderIds,
        ) {
            return $orders
                ->map(function ($order) use ($dateValue, $section, $followUpEligibleOrderIds, $pendingReviewOrderIds) {
                    $complaint = $order->complaint;
                    $team = $order->technicians->pluck('full_name')->filter()->implode(', ');

                    return [
                        'id' => (int) $order->id,
                        'complaint_id' => (int) $order->complaint_id,
                        'number' => $complaint?->complaint_no ?? 'Unknown Complaint',
                        'category' => $complaint?->category?->name ?? 'Water Service Concern',
                        'description' => $complaint?->description ?? '',
                        'address' => $complaint?->address ?? 'Not provided',
                        'team' => $team ?: 'No work-order team recorded',
                        'status' => $order->status ?? 'Unknown',
                        'approval_status' => $order->approval_status ?? 'Draft',
                        'visit' => $order->visit_number,
                        'dispatch' => $order->dispatch_type ?? '',
                        'pending_reason' => $order->pending_reason ?? '',
                        'needs_follow_up' => in_array((int) $order->id, $followUpEligibleOrderIds, true),
                        'pending_review' => in_array((int) $order->id, $pendingReviewOrderIds, true),
                        'follow_up_url' =>
                            route('maintenance-manager.complaints.show', [
                                'complaint' => $order->complaint_id,
                                'source' => 'schedule',
                                'date' => $dateValue,
                                'section' => $section,
                            ]) . '#same-day-follow-up-form',
                        'show_url' => route('maintenance-manager.complaints.show', [
                            'complaint' => $order->complaint_id,
                            'source' => 'schedule',
                            'date' => $dateValue,
                            'section' => $section,
                        ]),
                    ];
                })
                ->values()
                ->all();
        };

        $todayOrderData = $makeOrderData($todayOrders, $todayDate, 'today');

        $selectedOrderData = $makeOrderData($selectedOrders, $selectedDate->toDateString(), 'scheduled');
    @endphp

    <div class="mx-auto max-w-7xl space-y-6 pb-12">

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-sky-700">
                    Maintenance Management
                </p>

                <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">
                    Daily Maintenance Planning
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                    Review daily work orders, organize maintenance teams,
                    and finalize work schedules.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">


                <a href="{{ route('maintenance-manager.complaints.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-folder-open"></i>
                    All Complaints
                </a>
            </div>
        </div>

        {{-- FEEDBACK --}}
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                <i class="fas fa-circle-check mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <i class="fas fa-circle-exclamation mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <p class="font-semibold">Please correct the following:</p>

                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- OVERVIEW --}}
        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ([['Today’s Work Orders', $todayOrders->count(), 'fa-calendar-day', 'text-sky-700'], ['Ready for Planning', $readyComplaints->count(), 'fa-list-check', 'text-violet-700'], ['Available Plumbers', $duties->count(), 'fa-users', 'text-emerald-700'], ['Needs Attention', $attentionCount, 'fa-triangle-exclamation', 'text-amber-700']] as [$label, $value, $icon, $color])
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-xs font-medium text-slate-500">
                        {{ $label }}
                    </p>

                    <div class="mt-3 flex items-center justify-between gap-2">
                        <span class="text-2xl font-bold text-slate-900">
                            {{ $value }}
                        </span>

                        <i class="fas {{ $icon }} {{ $color }}"></i>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ATTENTION LINKS --}}
        <div class="grid gap-3 sm:grid-cols-2">
            <a href="{{ route('maintenance-manager.pending-workloads.index') }}"
                class="group flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-amber-300 hover:bg-amber-50">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <i class="fas fa-clipboard-list"></i>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-900">
                            Pending Workload
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $pendingOrders->count() }} unfinished work order(s)
                        </p>
                    </div>
                </div>

                <i class="fas fa-arrow-right text-slate-400 group-hover:text-amber-700"></i>
            </a>

            <a href="{{ route('maintenance-manager.backlogs.index') }}"
                class="group flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-red-300 hover:bg-red-50">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700">
                        <i class="fas fa-calendar-xmark"></i>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-900">
                            Missed Schedules
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $missedOrders->count() }} overdue work order(s)
                        </p>
                    </div>
                </div>

                <i class="fas fa-arrow-right text-slate-400 group-hover:text-red-700"></i>
            </a>
        </div>

        {{-- ====================================================== --}}
        {{-- TODAY'S WORKLOAD --}}
        {{-- ====================================================== --}}
        <section id="today" x-data="maintenanceOrderTable(@js($todayOrderData), 'today')"
            class="scroll-mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            <i class="fas fa-calendar-check mr-2 text-sky-700"></i>
                            Today's Workload
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $manilaToday->format('F d, Y') }}
                            · {{ $todayOrders->count() }} work order(s)
                        </p>
                    </div>

                    <span
                        class="rounded-full px-3 py-1.5 text-xs font-semibold
                        {{ $todaySchedule?->status === 'Finalized' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $todaySchedule?->status ?? 'No Schedule' }}
                    </span>
                </div>

                {{-- SEARCH + PRINT --}}
                <div class="mt-5 flex flex-wrap items-end justify-between gap-3">
                    <div class="w-full max-w-lg">
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Search Work Orders
                        </label>

                        <div class="relative">
                            <i class="fas fa-magnifying-glass absolute left-3 top-3.5 text-xs text-slate-400"></i>

                            <input type="search" x-model.debounce.250ms="search" @input="page = 1"
                                placeholder="Complaint, location, plumber, status..."
                                class="w-full rounded-xl border-slate-200 py-2.5 pl-9 pr-3 text-sm">
                        </div>
                    </div>

                    <button type="button" @click="printList('Today\'s Workload', @js($manilaToday->format('F d, Y')))"
                        :disabled="filtered.length === 0"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40">
                        <i class="fas fa-print"></i>
                        Print
                    </button>
                </div>
            </div>


            {{-- DESKTOP TABLE --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-5 py-3">Work Order</th>
                            <th class="px-5 py-3">Location</th>
                            <th class="px-5 py-3">Assigned Team</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        <template x-for="order in paginated" :key="order.id">
                            <tr class="align-top">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-900" x-text="order.number"></p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        WO #<span x-text="order.id"></span>
                                        · Visit #<span x-text="order.visit"></span>
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500" x-text="order.category"></p>
                                </td>

                                <td class="max-w-xs break-words px-5 py-4 text-slate-600" x-text="order.address"></td>

                                <td class="max-w-xs break-words px-5 py-4 text-slate-600" x-text="order.team"></td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold"
                                        :class="statusClass(order.status)" x-text="order.status"></span>

                                    <p class="mt-1 text-xs font-semibold"
                                        :class="order.approval_status === 'Approved' ? 'text-emerald-700' : 'text-amber-700'"
                                        x-text="order.approval_status === 'Approved' ? 'Approved' : 'Draft / Awaiting Approval'">
                                    </p>

                                    <p x-show="order.pending_review" x-cloak class="mt-2 text-xs font-bold text-orange-700">
                                        <i class="fas fa-circle-exclamation mr-1"></i>
                                        <span
                                            x-text="order.needs_follow_up ? 'Follow-up needed' : 'Needs manager review · Same-day resume possible'"></span>
                                    </p>

                                    <p x-show="order.pending_reason"
                                        class="mt-2 max-w-xs break-words text-xs leading-5 text-orange-700"
                                        x-text="order.pending_reason"></p>
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <div class="flex flex-col items-end gap-2">
                                        <a x-show="order.needs_follow_up" x-cloak :href="order.follow_up_url"
                                            class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg bg-orange-600 px-3 py-2 text-xs font-semibold text-white hover:bg-orange-700">
                                            <i class="fas fa-calendar-plus"></i>
                                            Plan Follow-up
                                        </a>

                                        <a x-show="order.pending_review && !order.needs_follow_up" x-cloak
                                            :href="order.show_url"
                                            class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg border border-orange-200 bg-orange-50 px-3 py-2 text-xs font-semibold text-orange-800 hover:bg-orange-100">
                                            <i class="fas fa-clipboard-check"></i>
                                            Review Pending Workload
                                        </a>

                                        <a :href="order.show_url"
                                            class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg border border-sky-200 px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-50">
                                            <i class="fas fa-eye"></i>
                                            Show Details
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filtered.length === 0" x-cloak>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">
                                No matching work orders for today.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- MOBILE WORK ORDER CARDS --}}
            <div class="space-y-3 p-3 md:hidden">
                <template x-for="order in paginated" :key="'today-card-' + order.id">
                    <article class="min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex min-w-0 items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="break-words text-sm font-bold text-slate-900" x-text="order.number"></p>
                                <p class="mt-1 text-xs text-slate-500">
                                    WO #<span x-text="order.id"></span>
                                    · Visit #<span x-text="order.visit"></span>
                                </p>
                                <p class="mt-1 break-words text-xs text-slate-500" x-text="order.category"></p>
                            </div>

                            <span
                                class="inline-flex max-w-[45%] shrink-0 whitespace-normal rounded-full border px-2.5 py-1 text-center text-xs font-semibold"
                                :class="statusClass(order.status)" x-text="order.status"></span>
                        </div>

                        <div class="mt-4 space-y-3 border-t border-slate-100 pt-3">
                            <div>
                                <p class="text-xs font-semibold text-slate-500">
                                    <i class="fas fa-location-dot mr-1"></i> Location
                                </p>
                                <p class="mt-1 break-words text-sm text-slate-700" x-text="order.address"></p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-slate-500">
                                    <i class="fas fa-users mr-1"></i> Assigned Team
                                </p>
                                <p class="mt-1 break-words text-sm text-slate-700" x-text="order.team"></p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-slate-500">Approval</p>
                                <p class="mt-1 text-sm font-semibold"
                                    :class="order.approval_status === 'Approved' ? 'text-emerald-700' : 'text-amber-700'"
                                    x-text="order.approval_status === 'Approved' ? 'Approved' : 'Draft / Awaiting Approval'">
                                </p>
                            </div>

                            <div x-show="order.pending_review" x-cloak
                                class="rounded-lg border border-orange-200 bg-orange-50 p-3 text-xs font-semibold leading-5 text-orange-800">
                                <i class="fas fa-circle-exclamation mr-1"></i>
                                <span
                                    x-text="order.needs_follow_up ? 'Follow-up needed' : 'Needs manager review · Same-day resume possible'"></span>
                            </div>

                            <div x-show="order.pending_reason" x-cloak
                                class="break-words rounded-lg bg-orange-50 p-3 text-xs leading-5 text-orange-800"
                                x-text="order.pending_reason"></div>
                        </div>

                        <div class="mt-4 flex flex-col gap-2 border-t border-slate-100 pt-3">
                            <a x-show="order.needs_follow_up" x-cloak :href="order.follow_up_url"
                                class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg bg-orange-600 px-3 py-2.5 text-xs font-semibold text-white hover:bg-orange-700">
                                <i class="fas fa-calendar-plus"></i>
                                Plan Follow-up
                            </a>

                            <a x-show="order.pending_review && !order.needs_follow_up" x-cloak :href="order.show_url"
                                class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg border border-orange-200 bg-orange-50 px-3 py-2.5 text-xs font-semibold text-orange-800 hover:bg-orange-100">
                                <i class="fas fa-clipboard-check"></i>
                                Review Pending Workload
                            </a>

                            <a :href="order.show_url"
                                class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg border border-sky-200 bg-white px-3 py-2.5 text-xs font-semibold text-sky-700 hover:bg-sky-50">
                                <i class="fas fa-eye"></i>
                                Show Details
                            </a>
                        </div>
                    </article>
                </template>

                <div x-show="filtered.length === 0" x-cloak
                    class="rounded-xl border border-dashed border-slate-300 px-4 py-10 text-center text-sm text-slate-500">
                    No matching work orders for today.
                </div>
            </div>


            {{-- PAGINATION --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-4">
                <p class="text-xs text-slate-500">
                    Showing
                    <span x-text="first"></span>–<span x-text="last"></span>
                    of <span x-text="filtered.length"></span>
                </p>

                <div class="flex items-center gap-2">
                    <button type="button" @click="page = Math.max(1, page - 1)" :disabled="page <= 1"
                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs disabled:opacity-40">
                        Previous
                    </button>

                    <span class="px-2 text-xs font-semibold text-slate-600" x-text="page + ' / ' + totalPages"></span>

                    <button type="button" @click="page = Math.min(totalPages, page + 1)" :disabled="page >= totalPages"
                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs disabled:opacity-40">
                        Next
                    </button>
                </div>
            </div>
        </section>

        {{-- ====================================================== --}}
        {{-- PLANNING & SCHEDULED WORK --}}
        {{-- ====================================================== --}}
        <section id="scheduled" x-data="maintenanceOrderTable(@js($selectedOrderData), 'scheduled')"
            class="scroll-mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="space-y-4 border-b border-slate-100 p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            <i class="fas fa-calendar-days mr-2 text-sky-700"></i>
                            Planning & Scheduled Work
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Review and manage work orders for the selected date.
                        </p>
                    </div>

                    {{-- DATE SELECTOR --}}
                    <form method="GET" action="{{ route('maintenance-manager.schedules.index') }}"
                        class="flex flex-wrap items-center gap-2">
                        <input type="date" name="date" value="{{ $selectedDate->toDateString() }}" required
                            class="rounded-xl border-slate-300 text-sm">

                        <button type="submit"
                            class="rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-800">
                            View Date
                        </button>

                        <a href="{{ route('maintenance-manager.schedules.index', ['date' => $todayDate]) }}#scheduled"
                            class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Today
                        </a>


                        <a href="{{ route('maintenance-manager.schedules.index', [
                            'date' => $previousWorkingDay->toDateString(),
                        ]) }}#scheduled"
                            class="inline-flex items-center gap-2 rounded-xl
           border border-slate-200 px-3 py-2.5
           text-sm font-semibold text-slate-700
           hover:bg-slate-50">

                            <i class="fas fa-chevron-left text-xs"></i>
                            Previous
                        </a>


                        <a href="{{ route('maintenance-manager.schedules.index', [
                            'date' => $nextWorkingDay->toDateString(),
                        ]) }}#scheduled"
                            class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Next Working Day
                        </a>
                    </form>
                </div>

                {{-- SCHEDULE STATUS + FINALIZE --}}
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-semibold text-slate-800">
                            {{ $dateLabel }}
                        </span>

                        <span
                            class="rounded-full px-3 py-1 text-xs font-semibold
                            {{ $isFinalized ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $schedule?->status ?? 'Not Created' }}
                        </span>

                        <span class="text-xs text-slate-500">
                            {{ $selectedOrders->count() }} work order(s)
                        </span>
                    </div>

                    @if ($isDraft && $selectedOrders->where('status', 'Scheduled')->isNotEmpty())
                        <form method="POST" action="{{ route('maintenance-manager.schedules.finalize', $schedule) }}"
                            onsubmit="return confirm('Finalize this daily maintenance schedule? Technician assignments and work dates will be confirmed.')">
                            @csrf

                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800">
                                <i class="fas fa-check-double"></i>
                                Finalize Schedule
                            </button>
                        </form>
                    @endif
                </div>

                @if ($isFinalized)
                    <p class="text-xs leading-5 text-emerald-700">
                        <i class="fas fa-lock mr-1"></i>
                        This schedule is finalized. Approved work orders remain locked;
                        unapproved Draft Additions can still be rescheduled.
                    </p>
                @elseif ($isDraft)
                    <p class="text-xs leading-5 text-amber-700">
                        <i class="fas fa-pen-to-square mr-1"></i>
                        Draft schedule. Review work orders and technician assignments
                        before finalization.
                    </p>
                @endif

                {{-- SEARCH + PRINT --}}
                <div class="flex flex-wrap items-end justify-between gap-3 border-t border-slate-100 pt-4">
                    <div class="w-full max-w-lg">
                        <label class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Search Scheduled Work
                        </label>

                        <div class="relative">
                            <i class="fas fa-magnifying-glass absolute left-3 top-3.5 text-xs text-slate-400"></i>

                            <input type="search" x-model.debounce.250ms="search" @input="page = 1"
                                placeholder="Work order, complaint, technician..."
                                class="w-full rounded-xl border-slate-200 py-2.5 pl-9 pr-3 text-sm">
                        </div>
                    </div>

                    <button type="button" @click="printList('Planning & Scheduled Work', @js($dateLabel))"
                        :disabled="filtered.length === 0"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40">
                        <i class="fas fa-print"></i>
                        Print
                    </button>
                </div>
            </div>

            {{-- DESKTOP TABLE --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[650px] text-left text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-5 py-3">Work Order</th>
                            <th class="px-5 py-3">Assigned Team</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($selectedOrders as $order)
                            @php
                                $teamNames = $order->technicians->pluck('full_name')->filter()->implode(', ');

                                $canReplace =
                                    $schedule?->status === 'Finalized' &&
                                    $order->status === 'Scheduled' &&
                                    $order->approval_status === 'Approved' &&
                                    !$order->started_at &&
                                    !$order->completed_at &&
                                    $selectedDate->greaterThanOrEqualTo($manilaToday);

                                $canReschedule =
                                    in_array($schedule?->status, ['Draft', 'Finalized'], true) &&
                                    $order->status === 'Scheduled' &&
                                    $order->approval_status === 'Draft' &&
                                    !$order->started_at;
                            @endphp

                            <tr x-show="visibleIds.includes({{ (int) $order->id }})" class="align-top">
                                <td class="px-5 py-4">
                                    <p class="break-words font-bold text-slate-900">
                                        {{ $order->complaint?->complaint_no ?? 'Unknown Complaint' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        Work Order #{{ $order->id }} · Visit #{{ $order->visit_number }}
                                    </p>
                                    <p class="mt-2 max-w-xs break-words text-xs leading-5 text-slate-500">
                                        {{ \Illuminate\Support\Str::limit($order->complaint?->description ?? '', 100) }}
                                    </p>
                                </td>

                                <td class="max-w-xs break-words px-5 py-4 text-slate-600">
                                    {{ $teamNames ?: 'No work-order team recorded' }}
                                </td>

                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClass($order->status) }}">
                                        {{ $order->status }}
                                    </span>

                                    <div class="mt-2">
                                        @if ($order->approval_status === 'Approved')
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                                                <i class="fas fa-circle-check"></i> Approved
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-700">
                                                <i class="fas fa-clock"></i>
                                                {{ $isFinalized ? 'Draft Addition' : 'Draft Work Order' }}
                                            </span>
                                        @endif
                                    </div>

                                    @if ($order->rescheduled_from_work_order_id)
                                        @php
                                            $missedOriginal = $missedOriginals->get(
                                                (int) $order->rescheduled_from_work_order_id,
                                            );
                                        @endphp
                                        @if ($missedOriginal)
                                            <details
                                                class="mt-2 max-w-sm rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs text-sky-900">
                                                <summary class="cursor-pointer font-semibold">
                                                    <i class="fas fa-calendar-xmark mr-1"></i>
                                                    Missed Visit #{{ $missedOriginal->visit_number }} — View Reasons
                                                </summary>
                                                <div class="mt-3 space-y-3 border-t border-sky-200 pt-3">
                                                    <p><strong>Original date:</strong>
                                                        {{ $missedOriginal->schedule?->schedule_date?->format('M d, Y') ?? 'Unknown' }}
                                                    </p>
                                                    <div>
                                                        <p class="font-bold">Plumber's explanation</p>
                                                        <p class="mt-1 whitespace-pre-line break-words">
                                                            {{ $missedOriginal->missed_reason ?: 'No plumber report submitted.' }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold">Manager's verified reason</p>
                                                        <p class="mt-1 whitespace-pre-line break-words">
                                                            {{ $missedOriginal->missed_manager_reason ?: 'Not recorded (legacy record).' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </details>
                                        @endif
                                    @endif

                                    @php
                                        $latestReschedule = $reschedulingHistories->get($order->id);
                                        $previousScheduleDate = $latestReschedule
                                            ? data_get($latestReschedule->old_values, 'schedule_date')
                                            : null;
                                        $newScheduleDate = $latestReschedule
                                            ? data_get($latestReschedule->new_values, 'schedule_date')
                                            : null;
                                    @endphp

                                    @if ($latestReschedule)
                                        <details
                                            class="mt-2 max-w-sm rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900">
                                            <summary
                                                class="flex cursor-pointer list-none items-center gap-2 font-semibold [&::-webkit-details-marker]:hidden">
                                                <i class="fas fa-clock-rotate-left text-amber-700"></i>
                                                <span>Rescheduled</span>
                                                <span class="font-normal text-amber-700">
                                                    {{ $previousScheduleDate ? \Carbon\Carbon::parse($previousScheduleDate)->format('M d') : 'Unknown' }}
                                                    &rarr;
                                                    {{ $newScheduleDate ? \Carbon\Carbon::parse($newScheduleDate)->format('M d, Y') : 'Unknown' }}
                                                </span>
                                                <i class="fas fa-chevron-down ml-auto text-[10px]"></i>
                                            </summary>
                                            <div class="mt-3 space-y-2 border-t border-amber-200 pt-3">
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div class="rounded-lg border border-slate-200 bg-white p-2">
                                                        <p class="text-[11px] text-slate-500">Previous Date</p>
                                                        <p class="mt-1 font-semibold text-slate-800">
                                                            {{ $previousScheduleDate ? \Carbon\Carbon::parse($previousScheduleDate)->format('M d, Y') : 'Not recorded' }}
                                                        </p>
                                                    </div>
                                                    <div class="rounded-lg border border-sky-200 bg-sky-50 p-2">
                                                        <p class="text-[11px] text-sky-600">Rescheduled To</p>
                                                        <p class="mt-1 font-semibold text-sky-900">
                                                            {{ $newScheduleDate ? \Carbon\Carbon::parse($newScheduleDate)->format('M d, Y') : 'Not recorded' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p
                                                        class="text-[11px] font-bold uppercase tracking-wide text-amber-800">
                                                        Manager's Reason</p>
                                                    <p
                                                        class="mt-1 whitespace-pre-line break-words leading-5 text-amber-950">
                                                        {{ $latestReschedule->reason ?: 'No reason recorded.' }}
                                                    </p>
                                                </div>
                                                <p class="text-[11px] text-amber-700">
                                                    <i class="far fa-clock mr-1"></i>
                                                    Recorded on
                                                    {{ $latestReschedule->created_at ? $latestReschedule->created_at->copy()->timezone('Asia/Manila')->format('M d, Y h:i A') : 'Unknown date' }}
                                                </p>
                                            </div>
                                        </details>
                                    @endif

                                    @if (in_array((int) $order->id, $pendingReviewOrderIds, true))
                                        <p class="mt-2 text-xs font-bold text-orange-700">
                                            {{ in_array((int) $order->id, $followUpEligibleOrderIds, true) ? 'Follow-up needed' : 'Needs manager review · Same-day resume possible' }}
                                        </p>
                                    @endif

                                    @if ($order->pending_reason)
                                        <p class="mt-2 max-w-xs break-words text-xs leading-5 text-orange-700">
                                            {{ $order->pending_reason }}
                                        </p>
                                    @endif

                                    @if ($order->is_late_addition && filled($order->late_addition_reason))
                                        <details
                                            class="mt-2 max-w-sm rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900">
                                            <summary class="flex cursor-pointer items-center gap-2 font-semibold">
                                                <i class="fas fa-clipboard-check text-amber-700"></i>
                                                <span>Early Scheduling Justification</span>
                                                <i class="fas fa-chevron-down ml-auto text-[10px]"></i>
                                            </summary>
                                            <div class="mt-3 border-t border-amber-200 pt-3">
                                                <p class="whitespace-pre-line break-words leading-5">
                                                    {{ $order->late_addition_reason }}</p>
                                                <p class="mt-2 text-[11px] text-amber-700">Recorded by Maintenance
                                                    Management</p>
                                            </div>
                                        </details>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex min-w-40 flex-col gap-2">
                                        <a href="{{ route('maintenance-manager.complaints.show', [
                                            'complaint' => $order->complaint_id,
                                            'source' => 'schedule',
                                            'date' => $selectedDate->toDateString(),
                                            'section' => 'scheduled',
                                        ]) }}"
                                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-sky-200 bg-white px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-50">
                                            <i class="fas fa-eye"></i> Show Details
                                        </a>

                                        @if (in_array((int) $order->id, $followUpEligibleOrderIds, true))
                                            <a href="{{ route('maintenance-manager.complaints.show', [
                                                'complaint' => $order->complaint_id,
                                                'source' => 'schedule',
                                                'date' => $selectedDate->toDateString(),
                                                'section' => 'scheduled',
                                            ]) }}#same-day-follow-up-form"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-600 px-3 py-2 text-xs font-semibold text-white hover:bg-orange-700">
                                                <i class="fas fa-calendar-plus"></i> Plan Follow-up
                                            </a>
                                        @endif

                                        @if (in_array((int) $order->id, $pendingReviewOrderIds, true) &&
                                                !in_array((int) $order->id, $followUpEligibleOrderIds, true))
                                            <a href="{{ route('maintenance-manager.complaints.show', [
                                                'complaint' => $order->complaint_id,
                                                'source' => 'schedule',
                                                'date' => $selectedDate->toDateString(),
                                                'section' => 'scheduled',
                                            ]) }}"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-orange-200 bg-orange-50 px-3 py-2 text-xs font-semibold text-orange-800 hover:bg-orange-100">
                                                <i class="fas fa-clipboard-check"></i> Review Pending Workload
                                            </a>
                                        @endif

                                        @if ($canReplace)
                                            <button type="button"
                                                @click="$dispatch('open-plumber-replacement', {
                                        id: {{ (int) $order->id }},
                                        complaint: @js($order->complaint?->complaint_no ?? 'Complaint'),
                                        currentDate: @js($selectedDate->toDateString()),
                                        team: @js($order->technicians->map(fn($t) => ['id' => (int) $t->id, 'name' => $t->full_name])->values()->all()),
                                        action: @js(route('maintenance-manager.schedules.replace-plumber', $order))
                                    })"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800 hover:bg-amber-100">
                                                <i class="fas fa-user-gear"></i> Replace Plumber
                                            </button>
                                        @endif

                                        @if (
                                            $order->status === 'In Progress' &&
                                                $order->approval_status === 'Approved' &&
                                                $schedule?->status === 'Finalized' &&
                                                $order->started_at &&
                                                !$order->completed_at)
                                            <a href="{{ route('maintenance-manager.schedules.handover.edit', $order) }}"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-violet-200 bg-violet-50 px-3 py-2 text-xs font-semibold text-violet-700 hover:bg-violet-100">
                                                <i class="fas fa-right-left"></i> Record Handover
                                            </a>
                                        @endif

                                        @if ($canReschedule)
                                            <button type="button"
                                                @click="$dispatch('open-draft-reschedule', {
                                        id: {{ (int) $order->id }},
                                        complaint: @js($order->complaint?->complaint_no ?? 'Complaint'),
                                        currentDate: @js($selectedDate->toDateString()),
                                        technicianIds: @js($order->technicians->pluck('id')->map(fn($id) => (int) $id)->values()->all()),
                                        action: @js(route('maintenance-manager.schedules.reschedule', $order))
                                    })"
                                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-100">
                                                <i class="fas fa-calendar-days"></i> Reschedule
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <tr x-show="filtered.length === 0" x-cloak>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-500">
                                No matching work orders for the selected date.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- MOBILE WORK ORDER CARDS --}}
            <div class="space-y-3 p-3 md:hidden">
                @foreach ($selectedOrders as $order)
                    @php
                        $teamNames = $order->technicians->pluck('full_name')->filter()->implode(', ');

                        $canReplace =
                            $schedule?->status === 'Finalized' &&
                            $order->status === 'Scheduled' &&
                            $order->approval_status === 'Approved' &&
                            !$order->started_at &&
                            !$order->completed_at &&
                            $selectedDate->greaterThanOrEqualTo($manilaToday);

                        $canReschedule =
                            in_array($schedule?->status, ['Draft', 'Finalized'], true) &&
                            $order->status === 'Scheduled' &&
                            $order->approval_status === 'Draft' &&
                            !$order->started_at;

                        $latestReschedule = $reschedulingHistories->get($order->id);
                        $previousScheduleDate = $latestReschedule
                            ? data_get($latestReschedule->old_values, 'schedule_date')
                            : null;
                        $newScheduleDate = $latestReschedule
                            ? data_get($latestReschedule->new_values, 'schedule_date')
                            : null;
                    @endphp

                    <article x-show="visibleIds.includes({{ (int) $order->id }})"
                        class="min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex min-w-0 items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="break-words text-sm font-bold text-slate-900">
                                    {{ $order->complaint?->complaint_no ?? 'Unknown Complaint' }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Work Order #{{ $order->id }} · Visit #{{ $order->visit_number }}
                                </p>
                                <p class="mt-2 break-words text-xs leading-5 text-slate-500">
                                    {{ \Illuminate\Support\Str::limit($order->complaint?->description ?? '', 100) }}
                                </p>
                            </div>

                            <span
                                class="inline-flex max-w-[45%] shrink-0 whitespace-normal rounded-full border px-2.5 py-1 text-center text-xs font-semibold {{ $statusClass($order->status) }}">
                                {{ $order->status }}
                            </span>
                        </div>

                        <div class="mt-4 space-y-3 border-t border-slate-100 pt-3">
                            <div>
                                <p class="text-xs font-semibold text-slate-500">
                                    <i class="fas fa-users mr-1"></i> Assigned Team
                                </p>
                                <p class="mt-1 break-words text-sm text-slate-700">
                                    {{ $teamNames ?: 'No work-order team recorded' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-slate-500">Approval</p>
                                @if ($order->approval_status === 'Approved')
                                    <span
                                        class="mt-1 inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                                        <i class="fas fa-circle-check"></i> Approved
                                    </span>
                                @else
                                    <span
                                        class="mt-1 inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-700">
                                        <i class="fas fa-clock"></i>
                                        {{ $isFinalized ? 'Draft Addition' : 'Draft Work Order' }}
                                    </span>
                                @endif
                            </div>

                            @if ($order->rescheduled_from_work_order_id)
                                @php
                                    $missedOriginal = $missedOriginals->get(
                                        (int) $order->rescheduled_from_work_order_id,
                                    );
                                @endphp
                                @if ($missedOriginal)
                                    <details
                                        class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs text-sky-900">
                                        <summary class="cursor-pointer font-semibold">
                                            <i class="fas fa-calendar-xmark mr-1"></i>
                                            Missed Visit #{{ $missedOriginal->visit_number }} — View Reasons
                                        </summary>
                                        <div class="mt-3 space-y-3 border-t border-sky-200 pt-3">
                                            <p><strong>Original date:</strong>
                                                {{ $missedOriginal->schedule?->schedule_date?->format('M d, Y') ?? 'Unknown' }}
                                            </p>
                                            <div>
                                                <p class="font-bold">Plumber's explanation</p>
                                                <p class="mt-1 whitespace-pre-line break-words">
                                                    {{ $missedOriginal->missed_reason ?: 'No plumber report submitted.' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="font-bold">Manager's verified reason</p>
                                                <p class="mt-1 whitespace-pre-line break-words">
                                                    {{ $missedOriginal->missed_manager_reason ?: 'Not recorded (legacy record).' }}
                                                </p>
                                            </div>
                                        </div>
                                    </details>
                                @endif
                            @endif

                            @if ($latestReschedule)
                                <details
                                    class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900">
                                    <summary
                                        class="flex cursor-pointer list-none flex-wrap items-center gap-2 font-semibold [&::-webkit-details-marker]:hidden">
                                        <i class="fas fa-clock-rotate-left text-amber-700"></i>
                                        <span>Rescheduled</span>
                                        <span class="font-normal text-amber-700">
                                            {{ $previousScheduleDate ? \Carbon\Carbon::parse($previousScheduleDate)->format('M d') : 'Unknown' }}
                                            &rarr;
                                            {{ $newScheduleDate ? \Carbon\Carbon::parse($newScheduleDate)->format('M d, Y') : 'Unknown' }}
                                        </span>
                                    </summary>
                                    <div class="mt-3 space-y-2 border-t border-amber-200 pt-3">
                                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                            <div class="rounded-lg border border-slate-200 bg-white p-2">
                                                <p class="text-[11px] text-slate-500">Previous Date</p>
                                                <p class="mt-1 break-words font-semibold text-slate-800">
                                                    {{ $previousScheduleDate ? \Carbon\Carbon::parse($previousScheduleDate)->format('M d, Y') : 'Not recorded' }}
                                                </p>
                                            </div>
                                            <div class="rounded-lg border border-sky-200 bg-sky-50 p-2">
                                                <p class="text-[11px] text-sky-600">Rescheduled To</p>
                                                <p class="mt-1 break-words font-semibold text-sky-900">
                                                    {{ $newScheduleDate ? \Carbon\Carbon::parse($newScheduleDate)->format('M d, Y') : 'Not recorded' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-bold uppercase tracking-wide text-amber-800">
                                                Manager's Reason</p>
                                            <p class="mt-1 whitespace-pre-line break-words leading-5 text-amber-950">
                                                {{ $latestReschedule->reason ?: 'No reason recorded.' }}
                                            </p>
                                        </div>
                                        <p class="text-[11px] text-amber-700">
                                            <i class="far fa-clock mr-1"></i>
                                            Recorded on
                                            {{ $latestReschedule->created_at ? $latestReschedule->created_at->copy()->timezone('Asia/Manila')->format('M d, Y h:i A') : 'Unknown date' }}
                                        </p>
                                    </div>
                                </details>
                            @endif

                            @if (in_array((int) $order->id, $pendingReviewOrderIds, true))
                                <div
                                    class="rounded-lg border border-orange-200 bg-orange-50 p-3 text-xs font-semibold leading-5 text-orange-800">
                                    <i class="fas fa-circle-exclamation mr-1"></i>
                                    {{ in_array((int) $order->id, $followUpEligibleOrderIds, true) ? 'Follow-up needed' : 'Needs manager review · Same-day resume possible' }}
                                </div>
                            @endif

                            @if ($order->pending_reason)
                                <div class="break-words rounded-lg bg-orange-50 p-3 text-xs leading-5 text-orange-800">
                                    {{ $order->pending_reason }}
                                </div>
                            @endif

                            @if ($order->is_late_addition && filled($order->late_addition_reason))
                                <details
                                    class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900">
                                    <summary class="flex cursor-pointer items-center gap-2 font-semibold">
                                        <i class="fas fa-clipboard-check text-amber-700"></i>
                                        <span>Early Scheduling Justification</span>
                                    </summary>
                                    <div class="mt-3 border-t border-amber-200 pt-3">
                                        <p class="whitespace-pre-line break-words leading-5">
                                            {{ $order->late_addition_reason }}</p>
                                        <p class="mt-2 text-[11px] text-amber-700">Recorded by Maintenance Management</p>
                                    </div>
                                </details>
                            @endif
                        </div>

                        <div class="mt-4 flex flex-col gap-2 border-t border-slate-100 pt-3">
                            <a href="{{ route('maintenance-manager.complaints.show', [
                                'complaint' => $order->complaint_id,
                                'source' => 'schedule',
                                'date' => $selectedDate->toDateString(),
                                'section' => 'scheduled',
                            ]) }}"
                                class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg border border-sky-200 bg-white px-3 py-2.5 text-xs font-semibold text-sky-700 hover:bg-sky-50">
                                <i class="fas fa-eye"></i> Show Details
                            </a>

                            @if (in_array((int) $order->id, $followUpEligibleOrderIds, true))
                                <a href="{{ route('maintenance-manager.complaints.show', [
                                    'complaint' => $order->complaint_id,
                                    'source' => 'schedule',
                                    'date' => $selectedDate->toDateString(),
                                    'section' => 'scheduled',
                                ]) }}#same-day-follow-up-form"
                                    class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg bg-orange-600 px-3 py-2.5 text-xs font-semibold text-white hover:bg-orange-700">
                                    <i class="fas fa-calendar-plus"></i> Plan Follow-up
                                </a>
                            @endif

                            @if (in_array((int) $order->id, $pendingReviewOrderIds, true) &&
                                    !in_array((int) $order->id, $followUpEligibleOrderIds, true))
                                <a href="{{ route('maintenance-manager.complaints.show', [
                                    'complaint' => $order->complaint_id,
                                    'source' => 'schedule',
                                    'date' => $selectedDate->toDateString(),
                                    'section' => 'scheduled',
                                ]) }}"
                                    class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg border border-orange-200 bg-orange-50 px-3 py-2.5 text-xs font-semibold text-orange-800 hover:bg-orange-100">
                                    <i class="fas fa-clipboard-check"></i> Review Pending Workload
                                </a>
                            @endif

                            @if ($canReplace)
                                <button type="button"
                                    @click="$dispatch('open-plumber-replacement', {
                            id: {{ (int) $order->id }},
                            complaint: @js($order->complaint?->complaint_no ?? 'Complaint'),
                            currentDate: @js($selectedDate->toDateString()),
                            team: @js($order->technicians->map(fn($t) => ['id' => (int) $t->id, 'name' => $t->full_name])->values()->all()),
                            action: @js(route('maintenance-manager.schedules.replace-plumber', $order))
                        })"
                                    class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2.5 text-xs font-semibold text-amber-800 hover:bg-amber-100">
                                    <i class="fas fa-user-gear"></i> Replace Plumber
                                </button>
                            @endif

                            @if (
                                $order->status === 'In Progress' &&
                                    $order->approval_status === 'Approved' &&
                                    $schedule?->status === 'Finalized' &&
                                    $order->started_at &&
                                    !$order->completed_at)
                                <a href="{{ route('maintenance-manager.schedules.handover.edit', $order) }}"
                                    class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg border border-violet-200 bg-violet-50 px-3 py-2.5 text-xs font-semibold text-violet-700 hover:bg-violet-100">
                                    <i class="fas fa-right-left"></i> Record Handover
                                </a>
                            @endif

                            @if ($canReschedule)
                                <button type="button"
                                    @click="$dispatch('open-draft-reschedule', {
                            id: {{ (int) $order->id }},
                            complaint: @js($order->complaint?->complaint_no ?? 'Complaint'),
                            currentDate: @js($selectedDate->toDateString()),
                            technicianIds: @js($order->technicians->pluck('id')->map(fn($id) => (int) $id)->values()->all()),
                            action: @js(route('maintenance-manager.schedules.reschedule', $order))
                        })"
                                    class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg border border-sky-200 bg-sky-50 px-3 py-2.5 text-xs font-semibold text-sky-700 hover:bg-sky-100">
                                    <i class="fas fa-calendar-days"></i> Reschedule
                                </button>
                            @endif
                        </div>
                    </article>
                @endforeach

                <div x-show="filtered.length === 0" x-cloak
                    class="rounded-xl border border-dashed border-slate-300 px-4 py-10 text-center text-sm text-slate-500">
                    No matching work orders for the selected date.
                </div>
            </div>


            {{-- PAGINATION --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-4">
                <p class="text-xs text-slate-500">
                    Showing
                    <span x-text="first"></span>–<span x-text="last"></span>
                    of <span x-text="filtered.length"></span>
                </p>

                <div class="flex items-center gap-2">
                    <button type="button" @click="page = Math.max(1, page - 1)" :disabled="page <= 1"
                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs disabled:opacity-40">
                        Previous
                    </button>

                    <span class="px-2 text-xs font-semibold text-slate-600" x-text="page + ' / ' + totalPages"></span>

                    <button type="button" @click="page = Math.min(totalPages, page + 1)" :disabled="page >= totalPages"
                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs disabled:opacity-40">
                        Next
                    </button>
                </div>
            </div>
            @if ($isFinalized && $schedule)
                @php
                    $draftAdditions = $schedule->workOrders
                        ->where('approval_status', 'Draft')
                        ->where('status', 'Scheduled');

                    $draftAdditionCount = $draftAdditions->count();
                @endphp

                @if ($draftAdditionCount > 0)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h3 class="text-sm font-bold text-amber-900">
                                    <i class="fas fa-clock mr-2"></i>
                                    Draft Additions Awaiting Approval
                                </h3>

                                <p class="mt-1 text-xs leading-5 text-amber-800">
                                    {{ $draftAdditionCount }} new work order(s)
                                    require manager approval before technician dispatch.
                                    Existing approved work orders remain unchanged.
                                </p>
                            </div>

                            <form method="POST"
                                action="{{ route('maintenance-manager.schedules.finalize-additions', $schedule) }}"
                                onsubmit="return confirm(
                        'Approve all Draft Additions for this date?'
                    )">

                                @csrf

                                <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl
                               bg-amber-600 px-5 py-3 text-sm font-semibold
                               text-white hover:bg-amber-700">
                                    <i class="fas fa-check-circle"></i>
                                    Finalize New Additions
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endif

        </section>


        {{-- ====================================================== --}}
        {{-- DAILY MAINTENANCE PLANNING --}}
        {{-- Supports Draft Planning and Finalized Schedule Additions --}}
        {{-- ====================================================== --}}

        <div id="batch-planning" class="scroll-mt-6">
            @include('maintenance-manager.schedules.partials.batch-planning')
        </div>



        {{-- WORKFLOW GUIDANCE --}}
        <div class="rounded-2xl border border-sky-200 bg-sky-50 p-5">
            <h3 class="text-sm font-bold text-sky-900">
                <i class="fas fa-circle-info mr-2"></i>
                Maintenance Planning Rules
            </h3>

            <div class="mt-3 space-y-2 text-xs leading-6 text-sky-800">
                <p>
                    <strong>Draft:</strong>
                    Managers can prepare and review work orders and technician assignments.
                </p>

                <p>
                    <strong>Finalized:</strong>
                    Assigned technicians can start eligible work orders only on
                    their scheduled date, subject to technician-side authorization rules.
                </p>

                <p>
                    <strong>Pending Workload:</strong>
                    Assigned plumbers can record a reason before starting or after an interruption.
                    They may resume on the same approved scheduled day. If the day passes,
                    the manager must create and finalize a separate follow-up work order.
                </p>

                <p>
                    <strong>Missed Schedule:</strong>
                    A finalized work order that was not started by its scheduled date
                    requires manager review.
                </p>
            </div>
        </div>
    </div>




    {{-- Approved work-order amendment: replacement before maintenance begins. --}}
    <div x-data="plumberReplacementModal()" @open-plumber-replacement.window="openModal($event.detail)"
        @keydown.escape.window="closeModal()" x-cloak>
        <div x-show="open" style="display:none" class="fixed inset-0 z-[110] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60" @click="closeModal()"></div>
            <div class="relative z-10 w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
                <div class="flex items-start justify-between border-b p-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Replace Plumber</h2>
                        <p class="mt-1 text-xs text-slate-500" x-text="complaint + ' · Work Order #' + orderId"></p>
                        <p class="mt-1 text-xs text-emerald-700">Approved · Finalized · Date remains unchanged</p>
                    </div>
                    <button type="button" @click="closeModal()" class="text-slate-500" aria-label="Close">✕</button>
                </div>
                <form method="POST" :action="action" @submit="submitting = true" class="space-y-4 p-5">
                    @csrf
                    @method('PATCH')
                    <p class="rounded-lg bg-slate-50 p-3 text-sm text-slate-700">Work Date: <strong
                            x-text="currentDate"></strong></p>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Unavailable plumber</label>
                        <select name="old_technician_id" x-model.number="oldId" required
                            class="w-full rounded-lg border-slate-300 text-sm">
                            <option value="">Select plumber to replace</option>
                            <template x-for="member in team" :key="member.id">
                                <option :value="member.id" x-text="member.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Replacement plumber on duty</label>
                        <p x-show="loading" class="text-xs text-sky-700">Checking duty records...</p>
                        <p x-show="error" class="text-xs text-red-700" x-text="error"></p>
                        <select name="new_technician_id" x-model.number="newId" required
                            class="w-full rounded-lg border-slate-300 text-sm">
                            <option value="">Select replacement</option>
                            <template x-for="person in eligible" :key="person.id">
                                <option :value="person.id" :disabled="!person.available"
                                    x-text="person.name + ' · ' + person.duty_status + (!person.available ? ' (' + person.reason + ')' : '')">
                                </option>
                            </template>
                        </select>
                        <p x-show="!loading && !error && eligible.filter(person => person.available).length === 0"
                            class="mt-2 text-xs text-amber-700">
                            No available replacement plumber for this date. Update availability or replan the work order.
                        </p>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-700">Reason for replacement</label>
                        <textarea name="reason" x-model="reason" rows="3" minlength="10" maxlength="2000" required
                            placeholder="Example: Assigned plumber reported sick leave before starting maintenance."
                            class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                    </div>
                    <p class="rounded-lg bg-amber-50 p-3 text-xs text-amber-900">
                        This replaces one plumber before work starts. The date, Finalized status and Approved status remain
                        unchanged.
                        The previous assignment and reason are recorded in history.
                    </p>
                    <div class="flex justify-end gap-2 border-t pt-4">
                        <button type="button" @click="closeModal()"
                            class="rounded-lg border px-4 py-2 text-sm">Cancel</button>
                        <button type="submit"
                            :disabled="submitting || loading || !!error || !oldId || !newId || !eligible.some(person => Number(
                                    person.id) === Number(newId) && person.available) || oldId === newId || reason
                                .trim().length < 10"
                            class="rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white disabled:opacity-40">
                            <span x-text="submitting ? 'Saving...' : 'Confirm Replacement'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        /**
         * Safely read Laravel availability responses.
         * Handles UTF-8 BOMs, HTML redirects, invalid JSON and HTTP errors.
         */
        async function readAvailabilityResponse(response) {
            const raw = await response.text();
            const cleaned = raw.replace(/^\uFEFF/, '').trim();
            const contentType = response.headers.get('content-type') || '';

            if (response.redirected || /text\/html/i.test(contentType) ||
                /^<!doctype\s+html|^<html[\s>]/i.test(cleaned)) {
                throw new Error(
                    'Please try again. If the problem continues, contact the system administrator.'
                );
            }

            let result;

            try {
                result = JSON.parse(cleaned);
            } catch (error) {
                throw new Error(
                    'We couldn’t load the duty schedule. Please try again.'
                );
            }

            if (!response.ok) {
                const validationErrors = Object.values(result.errors || {})
                    .flat()
                    .filter(Boolean)
                    .join(' ');

                throw new Error(
                    result.message ||
                    validationErrors ||
                    `Availability request failed (HTTP ${response.status}).`
                );
            }

            return result;
        }

        function plumberReplacementModal() {
            return {
                open: false,
                loading: false,
                submitting: false,
                error: '',
                orderId: null,
                complaint: '',
                action: '',
                currentDate: '',
                team: [],
                oldId: '',
                newId: '',
                reason: '',
                technicians: [],

                get eligible() {
                    const teamIds = this.team.map(member => Number(member.id));

                    return this.technicians.filter(
                        person => !teamIds.includes(Number(person.id))
                    );
                },

                async openModal(data) {
                    this.orderId = data.id;
                    this.complaint = data.complaint;
                    this.action = data.action;
                    this.currentDate = data.currentDate;
                    this.team = data.team || [];
                    this.oldId = this.team.length === 1 ?
                        Number(this.team[0].id) :
                        '';
                    this.newId = '';
                    this.reason = '';
                    this.technicians = [];
                    this.error = '';
                    this.submitting = false;
                    this.open = true;
                    this.loading = true;

                    try {
                        const url = new URL(
                            @js(route('maintenance-manager.schedules.index')),
                            window.location.origin
                        );

                        url.searchParams.set(
                            'availability_date',
                            this.currentDate
                        );

                        const response = await fetch(url.toString(), {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });

                        const result = await readAvailabilityResponse(response);

                        if (this.open && this.orderId === data.id) {
                            this.technicians =
                                result.all_technicians ||
                                result.technicians || [];

                            if (!Array.isArray(this.technicians)) {
                                throw new Error(
                                    'The server returned an unexpected plumber availability format.'
                                );
                            }
                        }
                    } catch (error) {
                        if (this.open && this.orderId === data.id) {
                            this.error = error.message ||
                                'Could not check plumber availability.';
                        }
                    } finally {
                        if (this.orderId === data.id) {
                            this.loading = false;
                        }
                    }
                },

                closeModal() {
                    if (!this.submitting) {
                        this.open = false;
                    }
                }
            };
        }
    </script>


    {{-- Draft-only rescheduling modal. This does not change Approved work orders. --}}
    <div x-data="draftRescheduleModal()" @open-draft-reschedule.window="openModal($event.detail)"
        @keydown.escape.window="closeModal()" x-cloak>
        <div x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none">
            <div class="absolute inset-0 bg-slate-900/60" @click="closeModal()"></div>
            <div class="relative z-10 w-full max-w-xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl"
                role="dialog" aria-modal="true" aria-label="Reschedule Draft Work Order">
                <div class="flex items-start justify-between border-b border-slate-100 p-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Reschedule Draft Work Order</h2>
                        <p class="mt-1 text-xs text-slate-500" x-text="complaint + ' · Work Order #' + orderId"></p>
                    </div>
                    <button type="button" @click="closeModal()"
                        class="rounded-lg px-2 py-1 text-slate-500 hover:bg-slate-100" aria-label="Close">✕</button>
                </div>
                <form method="POST" :action="action" @submit="submitting = true" class="space-y-4 p-5">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">Current Date</label>
                            <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                                x-text="currentDate"></div>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-slate-600">New Work Date</label>
                            <input type="date" name="schedule_date" x-model="date" @change="loadAvailability()"
                                min="{{ $todayDate }}" required class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-3">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-slate-800">Plumbers on duty for the new date</p>
                            <span x-show="loading" class="text-xs text-sky-700">Checking...</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Current plumbers are preselected if available. Select
                            replacement plumbers when needed.</p>

                        <template x-if="error">
                            <div class="mt-3 rounded-xl border border-red-200 bg-red-50 p-3">
                                <div class="flex items-start gap-2">
                                    <i class="fas fa-circle-exclamation mt-0.5 text-red-600"></i>

                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-red-900">
                                            Unable to check plumber availability
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-red-800" x-text="error">
                                        </p>

                                        <button type="button" @click="loadAvailability()"
                                            :disabled="loading || !date || date === currentDate"
                                            class="mt-3 inline-flex items-center gap-2 rounded-lg border border-red-300
                        bg-white px-3 py-2 text-xs font-semibold text-red-800
                        hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-50">
                                            <i class="fas fa-rotate-right" :class="loading ? 'fa-spin' : ''"></i>
                                            <span x-text="loading ? 'Checking...' : 'Try Again'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>


                        <template
                            x-if="!loading && !error &&
        technicians.filter(person => person.available).length === 0">
                            <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-3">
                                <p class="text-sm font-semibold text-amber-900">
                                    <i class="fas fa-calendar-xmark mr-1"></i>
                                    No plumbers available for this date
                                </p>

                                <p class="mt-1 text-xs leading-5 text-amber-800">
                                    Choose another working day or ask the administrator to
                                    review the plumber duty records.
                                </p>
                            </div>
                        </template>

                        <div class="mt-3 space-y-2">
                            <template x-for="person in technicians" :key="person.id">
                                <label class="flex items-center gap-2 rounded-lg border border-slate-100 p-2 text-sm"
                                    :class="person.available ? 'cursor-pointer hover:bg-slate-50' :
                                        'cursor-not-allowed bg-slate-50 opacity-65'">
                                    <input type="checkbox" name="technician_ids[]" :value="person.id"
                                        x-model.number="selectedIds" :disabled="!person.available"
                                        class="rounded border-slate-300 text-sky-600">
                                    <span x-text="person.name"></span>
                                    <span class="ml-auto text-[11px]"
                                        :class="person.available ? 'text-emerald-700' : 'text-red-600'"
                                        x-text="person.duty_status + (!person.available ? ' · ' + person.reason : '')"></span>
                                </label>
                            </template>
                        </div>

                        <p x-show="!loading && !error && technicians.length > 0 && missingPrevious.length > 0" x-cloak
                            class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-900"
                            role="status">
                            <span class="font-semibold">Team update needed:</span>

                            Previously assigned
                            <span x-text="missingPrevious.length > 1 ? 'plumbers ' : 'plumber '"></span>

                            <span class="font-semibold"
                                x-text="missingPrevious.map(id => plumberName(id)).join(', ')"></span>

                            <span
                                x-text="missingPrevious.length > 1
        ? ' are not on duty for the selected date.'
        : ' is not on duty for the selected date.'"></span>

                            Please select eligible replacement plumbers below.
                        </p>

                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-600">Reason for rescheduling / plumber
                            change</label>
                        <textarea name="reason" x-model="reason" rows="3" minlength="10" maxlength="2000" required
                            class="w-full rounded-lg border-slate-300 text-sm"
                            placeholder="Explain the date change and any plumber reassignment..."></textarea>
                    </div>
                    <p class="rounded-lg bg-blue-50 p-3 text-xs text-blue-800">This work order remains
                        <strong>Draft</strong>. The manager must finalize it before maintenance can start. The change is
                        recorded in scheduling history.
                    </p>
                    <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                        <button type="button" @click="closeModal()"
                            class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold">Cancel</button>
                        <button type="submit"
                            :disabled="submitting || loading || !!error || !date || date === currentDate || !selectedIds.length ||
                                selectedIds.some(id => !technicians.some(t => Number(t.id) === Number(id) && t
                                    .available)) || reason.trim().length < 10"
                            class="rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-40">
                            <span x-text="submitting ? 'Saving...' : 'Confirm Reschedule'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- TABLE SEARCH / PAGINATION / PRINT --}}
    {{-- ====================================================== --}}
    <script>
        function draftRescheduleModal() {
            return {
                open: false,
                loading: false,
                submitting: false,
                error: '',
                orderId: null,
                complaint: '',
                action: '',
                currentDate: '',
                date: '',
                reason: '',
                originalIds: [],
                selectedIds: [],
                technicians: [],


                plumberName(id) {
                    const technician = this.technicians.find(
                        person => Number(person.id) === Number(id)
                    );

                    if (technician?.name) {
                        return technician.name;
                    }

                    return 'Previously assigned plumber';
                },


                get missingPrevious() {
                    const available = this.technicians
                        .filter(technician => technician.available)
                        .map(technician => Number(technician.id));

                    return this.originalIds.filter(
                        id => !available.includes(Number(id))
                    );
                },

                openModal(data) {
                    this.orderId = data.id;
                    this.complaint = data.complaint;
                    this.action = data.action;
                    this.currentDate = data.currentDate;
                    this.date = '';
                    this.reason = '';
                    this.originalIds = (data.technicianIds || []).map(Number);
                    this.selectedIds = [];
                    this.technicians = [];
                    this.error = '';
                    this.submitting = false;
                    this.loading = false;
                    this.open = true;
                },

                closeModal() {
                    if (!this.submitting) {
                        this.open = false;
                    }
                },

                async loadAvailability() {
                    this.selectedIds = [];
                    this.technicians = [];
                    this.error = '';

                    if (!this.date || this.date === this.currentDate) {
                        if (this.date === this.currentDate) {
                            this.error = 'Choose a different work date.';
                        }

                        return;
                    }

                    const requestedDate = this.date;
                    const requestedOrderId = this.orderId;

                    this.loading = true;

                    try {
                        const url = new URL(
                            @js(route('maintenance-manager.schedules.index')),
                            window.location.origin
                        );

                        url.searchParams.set(
                            'availability_date',
                            requestedDate
                        );

                        const response = await fetch(url.toString(), {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });

                        const data = await readAvailabilityResponse(response);

                        if (
                            requestedDate !== this.date ||
                            requestedOrderId !== this.orderId ||
                            !this.open
                        ) {
                            return;
                        }

                        const technicians =
                            data.all_technicians ||
                            data.technicians || [];

                        if (!Array.isArray(technicians)) {
                            throw new Error(
                                'The server returned an unexpected plumber availability format.'
                            );
                        }

                        this.technicians = technicians;

                        const availableIds = technicians
                            .filter(technician => technician.available)
                            .map(technician => Number(technician.id));

                        this.selectedIds = this.originalIds.filter(
                            id => availableIds.includes(Number(id))
                        );
                    } catch (error) {
                        if (
                            requestedDate === this.date &&
                            requestedOrderId === this.orderId &&
                            this.open
                        ) {
                            this.error = error.message ||
                                'We couldn’t load the duty schedule. Please try again.';
                        }
                    } finally {
                        if (
                            requestedDate === this.date &&
                            requestedOrderId === this.orderId
                        ) {
                            this.loading = false;
                        }
                    }
                }
            };
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('maintenanceOrderTable', (orders, tableType) => ({
                orders: orders || [],
                tableType: tableType,
                search: '',
                page: 1,
                perPage: 10,

                get filtered() {
                    const query = this.search.toLowerCase().trim();

                    if (!query) {
                        return this.orders;
                    }

                    return this.orders.filter(order => {
                        const searchable = [
                            order.id,
                            order.number,
                            order.category,
                            order.description,
                            order.address,
                            order.team,
                            order.status,
                            order.approval_status,
                            order.visit,
                            order.dispatch,
                            order.pending_reason
                        ].join(' ').toLowerCase();

                        return searchable.includes(query);
                    });
                },

                get totalPages() {
                    return Math.max(
                        1,
                        Math.ceil(this.filtered.length / this.perPage)
                    );
                },

                get paginated() {
                    const start = (this.page - 1) * this.perPage;

                    return this.filtered.slice(
                        start,
                        start + this.perPage
                    );
                },

                get visibleIds() {
                    return this.paginated.map(order => order.id);
                },

                get first() {
                    if (this.filtered.length === 0) {
                        return 0;
                    }

                    return (this.page - 1) * this.perPage + 1;
                },

                get last() {
                    return Math.min(
                        this.page * this.perPage,
                        this.filtered.length
                    );
                },

                statusClass(status) {
                    switch (status) {
                        case 'Scheduled':
                            return 'border-blue-200 bg-blue-50 text-blue-700';

                        case 'In Progress':
                            return 'border-amber-200 bg-amber-50 text-amber-700';

                        case 'Completed':
                            return 'border-emerald-200 bg-emerald-50 text-emerald-700';

                        case 'Pending Workload':
                            return 'border-orange-200 bg-orange-50 text-orange-700';

                        default:
                            return 'border-slate-200 bg-slate-100 text-slate-600';
                    }
                },

                printList(title, dateLabel) {
                    if (this.filtered.length === 0) {
                        return;
                    }

                    const escapeHtml = value => String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');

                    const rows = this.filtered.map(order => `
                        <tr>
                            <td>
                                WO #${escapeHtml(order.id)}<br>
                                ${escapeHtml(order.number)}<br>
                                Visit #${escapeHtml(order.visit)}
                            </td>
                            <td>${escapeHtml(order.category)}</td>
                            <td>${escapeHtml(order.address)}</td>
                            <td>${escapeHtml(order.team)}</td>
                            <td>${escapeHtml(order.status)}</td>
                        </tr>
                    `).join('');

                    const tableHtml =
                        `<thead><tr><th style="width:20%">Work Order</th><th style="width:17%">Complaint Type</th><th style="width:25%">Location</th><th style="width:24%">Assigned Plumber(s)</th><th style="width:14%">Status</th></tr></thead><tbody>${rows}</tbody>`;
                    openMaintenanceReport({
                        title,
                        subtitle: 'Maintenance Management · Official Work Order Report',
                        period: `Schedule Date / Period: ${dateLabel}`,
                        tableHtml,
                        summary: `Total matching work orders: ${this.filtered.length}`
                    });
                }
            }));
        });
    </script>
    @include('maintenance-manager.partials.report-print-template')
@endsection
