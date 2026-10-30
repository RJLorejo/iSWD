@php
    /*
    |--------------------------------------------------------------------------
    | ACTIVE COMPLAINT COUNT (initial value for first paint)
    |--------------------------------------------------------------------------
    | Alpine refreshes this via the technician.sidebar-counts endpoint.
    */

    $myActiveComplaintCount = \App\Models\Complaint::query()
        ->whereHas('technicians', function ($query) {
            $query->where('users.id', auth()->id());
        })
        ->whereIn('status', ['Assigned', 'In Progress'])
        ->count();



/*
|--------------------------------------------------------------------------
| ROUTE STATES
|--------------------------------------------------------------------------
*/

$dashboardActive = request()->routeIs('technician.dashboard');

$activeWorkOrdersActive = request()->routeIs(
    'technician.active-work-orders.*'
);

$allComplaintsActive = request()->routeIs(
    'technician.complaints.*'
) && !request()->routeIs(
    'technician.complaints.print-report'
);

$missedVisitsActive = request()->routeIs(
    'technician.schedule.index',
    'technician.schedule.print'
) && request('view') === 'missed';

$myScheduleActive = request()->routeIs(
    'technician.schedule.index'
) && request('view') !== 'missed';

$pendingWorkloadActive = request()->routeIs(
    'technician.pending-workload.*'
);

$reportsActive = request()->routeIs('technician.reports.*')
    || request()->routeIs('technician.maintenance-reports.index');

$reportHistoryActive = request()->routeIs(
    'technician.maintenance-reports.history'
);

$hasReportHistoryRoute = \Illuminate\Support\Facades\Route::has(
    'technician.maintenance-reports.history'
);


/*
|--------------------------------------------------------------------------
| OPTIONAL ROUTES
|--------------------------------------------------------------------------
*/

$hasScheduleRoute = \Illuminate\Support\Facades\Route::has(
    'technician.schedule.index'
);

$hasSchedulePrintRoute = \Illuminate\Support\Facades\Route::has(
    'technician.schedule.print'
);

$hasPendingWorkloadRoute = \Illuminate\Support\Facades\Route::has(
    'technician.pending-workload.index'
);


    /*
    |--------------------------------------------------------------------------
    | OPTIONAL ROUTES
    |--------------------------------------------------------------------------
    */

    $hasScheduleRoute = \Illuminate\Support\Facades\Route::has(
        'technician.schedule.index'
    );

    $hasPendingWorkloadRoute = \Illuminate\Support\Facades\Route::has(
        'technician.pending-workload.index'
    );
@endphp


{{-- ================================================================ --}}
{{-- SIDEBAR --}}
{{-- ================================================================ --}}

<aside
    x-data="{
        counts: { activeComplaints: {{ (int) $myActiveComplaintCount }} },
        timer: null,
        fmt(n, max) { return n > max ? max + '+' : n; },
        async refresh() {
            if (document.hidden) return;
            try {
                const res = await fetch('{{ route('technician.sidebar-counts') }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                if (res.status === 401 || res.status === 419) { clearInterval(this.timer); return; }
                if (!res.ok) return;
                this.counts = await res.json();
            } catch (e) { /* keep last known counts */ }
        },
        init() {
            this.timer = setInterval(() => this.refresh(), 15000);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) this.refresh();
            });
        }
    }"
    :class="[
        sidebarMini ? 'lg:w-20' : 'lg:w-72',
        sidebarOpen
            ? 'translate-x-0'
            : '-translate-x-full lg:translate-x-0'
    ]"
    class="fixed left-0 top-0 z-50
           flex h-screen flex-col
           bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700
           text-white
           transition-all duration-300">

    {{-- ============================================================ --}}
    {{-- BRAND --}}
    {{-- ============================================================ --}}

    <div class="flex h-20 shrink-0 items-center border-b border-white/10 px-4">

        <a href="{{ route('technician.dashboard') }}"
            title="iSWD Dashboard"
            class="flex w-full min-w-0 items-center gap-3">

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15">
                <i class="fa-solid fa-droplet text-lg"></i>
            </div>

            <div
                x-show="!sidebarMini"
                x-transition
                class="min-w-0">

                <h1 class="text-xl font-bold leading-tight">
                    iSWD
                </h1>

                <p class="mt-0.5 truncate text-[11px] text-sky-100">
                    Plumber Portal
                </p>

            </div>

        </a>

    </div>


    {{-- ============================================================ --}}
    {{-- TECHNICIAN PROFILE --}}
    {{-- ============================================================ --}}

    <div class="shrink-0 border-b border-white/10 p-3">

        <div class="flex items-center gap-3 rounded-xl bg-white/10 p-3">

            <img
                src="{{ auth()->user()->avatar_url }}"
                alt="User Avatar"
                class="h-11 w-11 shrink-0 rounded-xl object-cover ring-2 ring-white/20">

            <div
                x-show="!sidebarMini"
                x-transition
                class="min-w-0">

                <p class="truncate text-sm font-semibold">
                    {{ auth()->user()->full_name ?? auth()->user()->name }}
                </p>

                <p class="mt-0.5 truncate text-[11px] text-sky-100">
                    Plumber
                </p>

            </div>

        </div>

    </div>


    {{-- ============================================================ --}}
    {{-- NAVIGATION --}}
    {{-- ============================================================ --}}

    <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-4">


        {{-- ======================================================== --}}
        {{-- OVERVIEW --}}
        {{-- ======================================================== --}}

        <div>

            <p
                x-show="!sidebarMini"
                class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.16em] text-sky-200">
                Overview
            </p>

            <a
                href="{{ route('technician.dashboard') }}"
                title="Dashboard"
                @if ($dashboardActive) aria-current="page" @endif
                class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                    {{ $dashboardActive
                        ? 'bg-white text-sky-700 shadow-sm'
                        : 'text-white hover:bg-white/10' }}">

                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ $dashboardActive ? 'bg-sky-50' : 'bg-white/10' }}">

                    <i class="fa-solid fa-chart-line text-sm"></i>

                </div>

                <span
                    x-show="!sidebarMini"
                    x-transition
                    class="text-sm font-semibold">
                    Dashboard
                </span>

            </a>

        </div>


        {{-- ======================================================== --}}
        {{-- MAINTENANCE --}}
        {{-- ======================================================== --}}

        <div>

            <p
                x-show="!sidebarMini"
                class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.16em] text-sky-200">
                Maintenance
            </p>


            {{-- ==================================================== --}}
            {{-- ACTIVE WORK ORDERS (live badge) --}}
            {{-- ==================================================== --}}

            <a
                href="{{ route('technician.active-work-orders.index') }}"
                title="Active Work Orders"
                @if ($activeWorkOrdersActive) aria-current="page" @endif
                class="relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                    {{ $activeWorkOrdersActive
                        ? 'bg-white text-sky-700 shadow-sm'
                        : 'text-white hover:bg-white/10' }}">

                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ $activeWorkOrdersActive ? 'bg-sky-50' : 'bg-white/10' }}">

                    <i class="fa-solid fa-clipboard-list text-sm"></i>

                </div>

                <span
                    x-show="!sidebarMini"
                    x-transition
                    class="min-w-0 flex-1 truncate text-sm font-semibold">
                    Active Work Orders
                </span>

                {{-- Expanded sidebar: number badge --}}
                <span
                    x-cloak
                    x-show="!sidebarMini && counts.activeComplaints > 0"
                    x-text="fmt(counts.activeComplaints, 99)"
                    class="ml-auto flex h-[22px] min-w-[22px] items-center justify-center rounded-full bg-amber-400 px-1.5 text-[10px] font-bold text-amber-950"></span>

                {{-- Mini sidebar: dot indicator --}}
                <span
                    x-cloak
                    x-show="sidebarMini && counts.activeComplaints > 0"
                    class="absolute right-1.5 h-2 w-2 rounded-full bg-amber-400"></span>

            </a>


            {{-- ==================================================== --}}
            {{-- MY SCHEDULE --}}
            {{-- ==================================================== --}}

            @if ($hasScheduleRoute)

                <a
                    href="{{ route('technician.schedule.index') }}"
                    title="My Schedule"
                    @if ($myScheduleActive) aria-current="page" @endif
                    class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                        {{ $myScheduleActive
                            ? 'bg-white text-sky-700 shadow-sm'
                            : 'text-white hover:bg-white/10' }}">

                    <div
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                        {{ $myScheduleActive ? 'bg-sky-50' : 'bg-white/10' }}">

                        <i class="fa-solid fa-calendar-days text-sm"></i>

                    </div>

                    <span
                        x-show="!sidebarMini"
                        x-transition
                        class="truncate text-sm font-semibold">
                        My Schedule
                    </span>

                </a>

            @endif


{{-- ==================================================== --}}
{{-- MISSED VISITS --}}
{{-- ==================================================== --}}

@if ($hasScheduleRoute)

    <a
        href="{{ route('technician.schedule.index', ['view' => 'missed']) }}"
        title="Missed Visits"
        @if ($missedVisitsActive) aria-current="page" @endif
        class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 transition
            {{ $missedVisitsActive
                ? 'bg-white text-sky-700 shadow-sm'
                : 'text-white hover:bg-white/10' }}">

        <div
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
            {{ $missedVisitsActive ? 'bg-sky-50' : 'bg-white/10' }}">

            <i class="fa-solid fa-calendar-xmark text-sm"></i>

        </div>

        <span
            x-show="!sidebarMini"
            x-transition
            class="min-w-0 flex-1 truncate text-sm font-semibold">
            Missed Visits
        </span>

    </a>

@endif


{{-- ==================================================== --}}
{{-- PRINT MISSED VISITS --}}
{{-- ==================================================== --}}

@if ($hasSchedulePrintRoute)

    <a
        href="{{ route('technician.schedule.print', ['view' => 'missed']) }}"
        target="_blank"
        rel="noopener"
        title="Print Missed Visits Report"
        class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 transition
            {{ $missedVisitsActive
                ? 'text-white hover:bg-white/10'
                : 'text-white hover:bg-white/10' }}">

        <div
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10">

            <i class="fa-solid fa-print text-sm"></i>

        </div>

        <span
            x-show="!sidebarMini"
            x-transition
            class="truncate text-sm font-semibold">
            Print Missed Visits
        </span>

    </a>

@endif



            {{-- ==================================================== --}}
            {{-- PENDING WORKLOAD --}}
            {{-- ==================================================== --}}

            @if ($hasPendingWorkloadRoute)

                <a
                    href="{{ route('technician.pending-workload.index') }}"
                    title="Pending Workload"
                    @if ($pendingWorkloadActive) aria-current="page" @endif
                    class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                        {{ $pendingWorkloadActive
                            ? 'bg-white text-sky-700 shadow-sm'
                            : 'text-white hover:bg-white/10' }}">

                    <div
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                        {{ $pendingWorkloadActive ? 'bg-sky-50' : 'bg-white/10' }}">

                        <i class="fa-solid fa-clock-rotate-left text-sm"></i>

                    </div>

                    <span
                        x-show="!sidebarMini"
                        x-transition
                        class="truncate text-sm font-semibold">
                        Pending Workload
                    </span>

                </a>

            @endif


            {{-- ==================================================== --}}
            {{-- ALL COMPLAINTS --}}
            {{-- ==================================================== --}}

            <a
                href="{{ route('technician.complaints.index') }}"
                title="All Complaints"
                @if ($allComplaintsActive) aria-current="page" @endif
                class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                    {{ $allComplaintsActive
                        ? 'bg-white text-sky-700 shadow-sm'
                        : 'text-white hover:bg-white/10' }}">

                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ $allComplaintsActive ? 'bg-sky-50' : 'bg-white/10' }}">

                    <i class="fa-solid fa-folder-open text-sm"></i>

                </div>

                <span
                    x-show="!sidebarMini"
                    x-transition
                    class="truncate text-sm font-semibold">
                    All Complaints
                </span>

            </a>

        </div>


        {{-- ======================================================== --}}
        {{-- REPORTS --}}
        {{-- ======================================================== --}}

        <div>

            <p
                x-show="!sidebarMini"
                class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.16em] text-sky-200">
                Reports
            </p>


            <a
                href="{{ route('technician.reports.maintenance') }}"
                title="Work Summary"
                @if ($reportsActive) aria-current="page" @endif
                class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                    {{ $reportsActive
                        ? 'bg-white text-sky-700 shadow-sm'
                        : 'text-white hover:bg-white/10' }}">

                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ $reportsActive ? 'bg-sky-50' : 'bg-white/10' }}">

                    <i class="fa-solid fa-chart-column text-sm"></i>

                </div>

                <span
                    x-show="!sidebarMini"
                    x-transition
                    class="truncate text-sm font-semibold">
                    Work Summary
                </span>

            </a>

            @if ($hasReportHistoryRoute)
                <a href="{{ route('technician.maintenance-reports.history') }}"
                    title="Report History"
                    @if ($reportHistoryActive) aria-current="page" @endif
                    class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                        {{ $reportHistoryActive ? 'bg-white text-sky-700 shadow-sm' : 'text-white hover:bg-white/10' }}">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                        {{ $reportHistoryActive ? 'bg-sky-50' : 'bg-white/10' }}">
                        <i class="fa-solid fa-file-lines text-sm"></i>
                    </div>
                    <span x-show="!sidebarMini" x-transition class="truncate text-sm font-semibold">
                        Report History
                    </span>
                </a>
            @endif
        </div>

    </nav>


    {{-- ============================================================ --}}
    {{-- FOOTER --}}
    {{-- ============================================================ --}}

    <div class="shrink-0 border-t border-white/10 p-3">

        <div class="rounded-xl bg-black/10 px-3 py-3">

            <div class="flex items-center gap-3">

                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10">

                    <i class="fa-solid fa-building text-xs"></i>

                </div>

                <div
                    x-show="!sidebarMini"
                    x-transition
                    class="min-w-0">

                    <p class="truncate text-[11px] font-semibold">
                        Sagay Water District
                    </p>

                    <p class="mt-0.5 truncate text-[9px] text-sky-200">
                        Field maintenance operations
                    </p>

                </div>

            </div>

        </div>

    </div>

</aside>


{{-- ================================================================ --}}
{{-- MOBILE OVERLAY --}}
{{-- ================================================================ --}}

<div
    x-cloak
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-black/50 lg:hidden">
</div>
