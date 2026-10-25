@php
    // Initial values for first paint. Alpine refreshes them via the sidebar-counts endpoint.
    $pendingReviews = \App\Models\MaintenanceReport::query()
        ->whereNotNull('submitted_at')
        ->where('review_status', 'Pending Review')
        ->count();

    $readyForAssignment = \App\Models\Complaint::query()
        ->where('complaints.status', 'Verified')
        ->whereDoesntHave('technicians')
        ->count();

    $navActive = 'bg-white text-sky-800 shadow-sm';
    $navInactive = 'text-sky-100 hover:bg-white/10 hover:text-white';
    $iconActive = 'bg-sky-50 text-sky-700';
    $iconInactive = 'text-sky-200 group-hover:text-white';
@endphp

<aside
    x-data="{
        counts: {
            readyForAssignment: {{ (int) $readyForAssignment }},
            pendingReviews: {{ (int) $pendingReviews }},
        },
        timer: null,
        fmt(n, max) { return n > max ? max + '+' : n; },
        async refresh() {
            if (document.hidden) return;
            try {
                const res = await fetch('{{ route('maintenance-manager.sidebar-counts') }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                if (res.status === 401 || res.status === 419) { clearInterval(this.timer); return; }
                if (!res.ok) return;
                this.counts = await res.json();
            } catch (e) { /* network hiccup: keep last known counts */ }
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
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col
           bg-gradient-to-b from-sky-800 via-blue-800 to-cyan-800
           text-white shadow-2xl transition-all duration-300 ease-in-out"
>

    {{-- BRAND --}}
    <div
        class="flex h-16 shrink-0 items-center border-b border-white/10 px-4 lg:h-[72px]"
        :class="sidebarMini ? 'lg:justify-center lg:px-2' : ''"
    >
        <a
            href="{{ route('maintenance-manager.dashboard') }}"
            class="flex min-w-0 items-center gap-3"
            :class="sidebarMini ? 'lg:justify-center' : ''"
        >
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center
                       rounded-xl bg-white/15 text-white ring-1 ring-white/10"
            >
                <i class="fas fa-droplet"></i>
            </div>

            <div x-show="!sidebarMini" x-transition.opacity class="min-w-0">
                <p class="text-lg font-bold tracking-tight">
                    iSWD
                </p>

                <p class="truncate text-[11px] font-medium text-sky-200">
                    Maintenance Manager Portal
                </p>
            </div>
        </a>

        <button
            type="button"
            @click="sidebarOpen = false"
            class="ml-auto flex h-9 w-9 items-center justify-center
                   rounded-xl text-sky-100 transition
                   hover:bg-white/10 hover:text-white lg:hidden"
            aria-label="Close navigation"
        >
            <i class="fas fa-xmark"></i>
        </button>
    </div>

    {{-- USER PROFILE --}}
    <div
        class="shrink-0 border-b border-white/10 p-4"
        :class="sidebarMini ? 'lg:px-3' : ''"
    >
        <div
            class="flex items-center gap-3 rounded-2xl bg-white/10
                   p-3 ring-1 ring-white/5"
            :class="sidebarMini ? 'lg:justify-center lg:p-2' : ''"
        >
            <img
                src="{{ auth()->user()->avatar_url }}"
                alt="{{ auth()->user()->full_name ?? auth()->user()->name }}"
                class="h-10 w-10 shrink-0 rounded-xl border
                       border-white/20 object-cover"
            >

            <div x-show="!sidebarMini" x-transition.opacity class="min-w-0">
                <p class="truncate text-sm font-bold text-white">
                    {{ auth()->user()->full_name ?? auth()->user()->name }}
                </p>

                <p class="mt-0.5 truncate text-[11px] text-sky-200">
                    Maintenance Manager
                </p>
            </div>
        </div>
    </div>

    {{-- NAVIGATION: organized by manager workflow --}}
    <div class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-5">
        <nav class="space-y-1.5">

            {{-- OVERVIEW --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5 first:pt-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">Overview</p>
            </div>

            <a href="{{ route('maintenance-manager.dashboard') }}" title="Dashboard"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('maintenance-manager.dashboard') ? $navActive : $navInactive }}"
               :class="sidebarMini ? 'lg:justify-center' : ''"
               {{ request()->routeIs('maintenance-manager.dashboard') ? 'aria-current=page' : '' }}>
                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ request()->routeIs('maintenance-manager.dashboard') ? $iconActive : $iconInactive }}">
                    <i class="fas fa-chart-line"></i>
                </span>
                <span x-show="!sidebarMini" x-transition.opacity class="min-w-0 flex-1 truncate">Dashboard</span>
            </a>

            {{-- COMPLAINT INTAKE --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5 first:pt-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">Complaint Intake</p>
            </div>

            <a href="{{ route('maintenance-manager.complaints.index') }}" title="All Complaints"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('maintenance-manager.complaints.index', 'maintenance-manager.complaints.show', 'maintenance-manager.complaints.report') ? $navActive : $navInactive }}"
               :class="sidebarMini ? 'lg:justify-center' : ''"
               {{ request()->routeIs('maintenance-manager.complaints.index', 'maintenance-manager.complaints.show', 'maintenance-manager.complaints.report') ? 'aria-current=page' : '' }}>
                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ request()->routeIs('maintenance-manager.complaints.index', 'maintenance-manager.complaints.show', 'maintenance-manager.complaints.report') ? $iconActive : $iconInactive }}">
                    <i class="fas fa-file-circle-exclamation"></i>
                </span>
                <span x-show="!sidebarMini" x-transition.opacity class="min-w-0 flex-1 truncate">All Complaints</span>
            </a>

            {{-- MAINTENANCE OPERATIONS --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5 first:pt-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">Maintenance Operations</p>
            </div>

            {{-- Daily Maintenance Planning (live badge: verified complaints awaiting assignment) --}}
            <a href="{{ route('maintenance-manager.schedules.index') }}" title="Daily Maintenance Planning"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('maintenance-manager.schedules.*') ? $navActive : $navInactive }}"
               :class="sidebarMini ? 'lg:justify-center' : ''"
               {{ request()->routeIs('maintenance-manager.schedules.*') ? 'aria-current=page' : '' }}>
                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ request()->routeIs('maintenance-manager.schedules.*') ? $iconActive : $iconInactive }}">
                    <i class="fas fa-calendar-days"></i>
                    <span x-cloak
                          x-show="sidebarMini && counts.readyForAssignment > 0"
                          x-text="fmt(counts.readyForAssignment, 9)"
                          class="absolute -right-1 -top-1 hidden h-4 min-w-4 items-center justify-center rounded-full bg-amber-500 px-1 text-[9px] font-bold text-white ring-2 ring-sky-800 lg:flex"></span>
                </span>
                <span x-show="!sidebarMini" x-transition.opacity class="min-w-0 flex-1 truncate">Daily Maintenance Planning</span>
                <span x-cloak
                      x-show="!sidebarMini && counts.readyForAssignment > 0"
                      x-text="fmt(counts.readyForAssignment, 99)"
                      title="Verified complaints awaiting assignment"
                      class="ml-auto inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-amber-500 px-1.5 text-[10px] font-bold text-white"></span>
            </a>

            <a href="{{ route('maintenance-manager.technician-duties.index') }}" title="Technician Duty Management"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('maintenance-manager.technician-duties.*') ? $navActive : $navInactive }}"
               :class="sidebarMini ? 'lg:justify-center' : ''"
               {{ request()->routeIs('maintenance-manager.technician-duties.*') ? 'aria-current=page' : '' }}>
                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ request()->routeIs('maintenance-manager.technician-duties.*') ? $iconActive : $iconInactive }}">
                    <i class="fas fa-users-gear"></i>
                </span>
                <span x-show="!sidebarMini" x-transition.opacity class="min-w-0 flex-1 truncate">Technician Duty Management</span>
            </a>

            <a href="{{ route('maintenance-manager.pending-workloads.index') }}" title="Pending Workload"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('maintenance-manager.pending-workloads.*') ? $navActive : $navInactive }}"
               :class="sidebarMini ? 'lg:justify-center' : ''"
               {{ request()->routeIs('maintenance-manager.pending-workloads.*') ? 'aria-current=page' : '' }}>
                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ request()->routeIs('maintenance-manager.pending-workloads.*') ? $iconActive : $iconInactive }}">
                    <i class="fas fa-clock-rotate-left"></i>
                </span>
                <span x-show="!sidebarMini" x-transition.opacity class="min-w-0 flex-1 truncate">Pending Workload</span>
            </a>

            <a href="{{ route('maintenance-manager.backlogs.index') }}" title="Missed Schedules"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('maintenance-manager.backlogs.*') ? $navActive : $navInactive }}"
               :class="sidebarMini ? 'lg:justify-center' : ''"
               {{ request()->routeIs('maintenance-manager.backlogs.*') ? 'aria-current=page' : '' }}>
                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ request()->routeIs('maintenance-manager.backlogs.*') ? $iconActive : $iconInactive }}">
                    <i class="fas fa-calendar-xmark"></i>
                </span>
                <span x-show="!sidebarMini" x-transition.opacity class="min-w-0 flex-1 truncate">Missed Schedules</span>
            </a>

            {{-- REVIEW & APPROVAL --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5 first:pt-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">Review & Approval</p>
            </div>

            {{-- Maintenance Reviews (live badge: reports pending review) --}}
            <a href="{{ route('maintenance-manager.maintenance-reviews.index') }}" title="Maintenance Reviews"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('maintenance-manager.maintenance-reviews.*') ? $navActive : $navInactive }}"
               :class="sidebarMini ? 'lg:justify-center' : ''"
               {{ request()->routeIs('maintenance-manager.maintenance-reviews.*') ? 'aria-current=page' : '' }}>
                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ request()->routeIs('maintenance-manager.maintenance-reviews.*') ? $iconActive : $iconInactive }}">
                    <i class="fas fa-clipboard-check"></i>
                    <span x-cloak
                          x-show="sidebarMini && counts.pendingReviews > 0"
                          x-text="fmt(counts.pendingReviews, 9)"
                          class="absolute -right-1 -top-1 hidden h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-bold text-white ring-2 ring-sky-800 lg:flex"></span>
                </span>
                <span x-show="!sidebarMini" x-transition.opacity class="min-w-0 flex-1 truncate">Maintenance Reviews</span>
                <span x-cloak
                      x-show="!sidebarMini && counts.pendingReviews > 0"
                      x-text="fmt(counts.pendingReviews, 99)"
                      title="Reports awaiting your review"
                      class="ml-auto inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-500 px-1.5 text-[10px] font-bold text-white"></span>
            </a>

            {{-- CONFIGURATION --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5 first:pt-0">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">Configuration</p>
            </div>

            <a href="{{ route('maintenance-manager.service-areas.index') }}" title="Plumber Service Areas"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('maintenance-manager.service-areas.*') ? $navActive : $navInactive }}"
               :class="sidebarMini ? 'lg:justify-center' : ''"
               {{ request()->routeIs('maintenance-manager.service-areas.*') ? 'aria-current=page' : '' }}>
                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ request()->routeIs('maintenance-manager.service-areas.*') ? $iconActive : $iconInactive }}">
                    <i class="fas fa-map-location-dot"></i>
                </span>
                <span x-show="!sidebarMini" x-transition.opacity class="min-w-0 flex-1 truncate">Plumber Service Areas</span>
            </a>

            {{-- REPORTS --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">Reports</p>
            </div>

            <a href="{{ route('maintenance-manager.insights.index') }}" title="Insights & Reports"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('maintenance-manager.insights.*') ? $navActive : $navInactive }}"
               :class="sidebarMini ? 'lg:justify-center' : ''"
               {{ request()->routeIs('maintenance-manager.insights.*') ? 'aria-current=page' : '' }}>
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ request()->routeIs('maintenance-manager.insights.*') ? $iconActive : $iconInactive }}">
                    <i class="fas fa-chart-area"></i>
                </span>
                <span x-show="!sidebarMini" x-transition.opacity class="min-w-0 flex-1 truncate">Insights & Reports</span>
            </a>
        </nav>
    </div>

    {{-- FOOTER --}}
    <div
        class="shrink-0 border-t border-white/10 p-3"
        :class="sidebarMini ? 'lg:px-2' : ''"
    >
        <div
            class="rounded-xl bg-black/10 px-3 py-3"
            :class="sidebarMini ? 'lg:flex lg:justify-center lg:px-2' : ''"
        >
            <div x-show="!sidebarMini" x-transition.opacity>
                <p class="text-[11px] font-semibold text-white">
                    Sagay Water District
                </p>

                <p class="mt-0.5 text-[10px] text-sky-200">
                    Maintenance management
                </p>
            </div>

            <i
                x-show="sidebarMini"
                class="fas fa-building text-sm text-sky-200"
            ></i>
        </div>
    </div>
</aside>

{{-- MOBILE OVERLAY --}}
<div
    x-cloak
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-[1px] lg:hidden"
></div>