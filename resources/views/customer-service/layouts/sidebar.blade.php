
@php
    /*
    |--------------------------------------------------------------------------
    | Initial notification counts
    |--------------------------------------------------------------------------
    | JavaScript refreshes these badges after the page loads.
    | The server-rendered feedback count prevents an initial visual delay.
    */
    $sidebarFeedbackCount = \App\Models\ComplaintFeedback::query()
        ->where('handling_status', 'New')
        ->count();
@endphp

<aside
    :class="[
        sidebarMini ? 'lg:w-20' : 'lg:w-72',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-gradient-to-b from-sky-800 via-blue-800 to-cyan-800 text-white shadow-2xl transition-all duration-300 ease-in-out">

    {{-- Header --}}
    <div class="flex h-16 shrink-0 items-center border-b border-white/10 px-4 lg:h-[72px]"
        :class="sidebarMini ? 'lg:justify-center lg:px-2' : ''">

        <a href="{{ route('customer-service.dashboard') }}"
            class="flex min-w-0 items-center gap-3"
            :class="sidebarMini ? 'lg:justify-center' : ''">

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white ring-1 ring-white/10">
                <i class="fas fa-droplet"></i>
            </div>

            <div x-show="!sidebarMini" x-transition.opacity class="min-w-0">
                <p class="text-lg font-bold tracking-tight">iSWD</p>
                <p class="truncate text-[11px] font-medium text-sky-200">
                    Customer Service Portal
                </p>
            </div>
        </a>

        <button type="button"
            @click="sidebarOpen = false"
            class="ml-auto flex h-9 w-9 items-center justify-center rounded-xl text-sky-100 transition hover:bg-white/10 hover:text-white lg:hidden"
            aria-label="Close navigation">
            <i class="fas fa-xmark"></i>
        </button>
    </div>

    {{-- User profile --}}
    <div class="shrink-0 border-b border-white/10 p-4"
        :class="sidebarMini ? 'lg:px-3' : ''">

        <div class="flex items-center gap-3 rounded-2xl bg-white/10 p-3 ring-1 ring-white/5"
            :class="sidebarMini ? 'lg:justify-center lg:p-2' : ''">

            <img
                src="{{ auth()->user()->avatar_url }}"
                alt="{{ auth()->user()->full_name ?? auth()->user()->name }}"
                class="h-10 w-10 shrink-0 rounded-xl border border-white/20 object-cover">

            <div x-show="!sidebarMini" x-transition.opacity class="min-w-0">
                <p class="truncate text-sm font-bold text-white">
                    {{ auth()->user()->full_name ?? auth()->user()->name }}
                </p>
                <p class="mt-0.5 truncate text-[11px] text-sky-200">
                    Customer Service
                </p>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-5">
        <nav class="space-y-1.5">

            {{-- Overview --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">
                    Overview
                </p>
            </div>

            <a href="{{ route('customer-service.dashboard') }}"
                title="Dashboard"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.dashboard')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.dashboard')
                        ? 'bg-sky-50 text-sky-700'
                        : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fas fa-chart-line"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Dashboard</span>
            </a>

            {{-- Consumer Services --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">
                    Consumer Services
                </p>
            </div>

            {{-- Announcements --}}
            <a href="{{ route('customer-service.announcements.index') }}"
                title="Announcements"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.announcements.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.announcements.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fa-solid fa-bullhorn"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Announcements</span>
            </a>

            {{-- Consumers --}}
            <a href="{{ route('customer-service.consumers.index') }}"
                title="Consumers"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.consumers.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.consumers.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fas fa-users"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Consumers</span>
            </a>

            {{-- Complaints --}}
            <a href="{{ route('customer-service.complaints.index') }}"
                title="Complaints"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.complaints.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.complaints.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fas fa-file-circle-exclamation"></i>
                    <span id="sidebar-complaints-badge"
                        data-sidebar-badge="complaints"
                        class="absolute -right-1 -top-1 hidden h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white ring-2 ring-sky-800">0</span>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Complaints</span>
            </a>

            {{-- Complaint Verification --}}
            <a href="{{ route('customer-service.complaint-verification.index') }}"
                title="Complaint Verification"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.complaint-verification.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.complaint-verification.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fas fa-shield-halved"></i>
                    <span id="sidebar-verification-badge"
                        data-sidebar-badge="verification"
                        class="absolute -right-1 -top-1 hidden h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white ring-2 ring-sky-800">0</span>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Complaint Verification</span>
            </a>

            {{-- Commercial Services --}}
            <a href="{{ route('customer-service.commercial-services.index') }}"
                title="Commercial Services"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.commercial-services.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.commercial-services.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fas fa-building"></i>
                    <span id="sidebar-commercial-badge"
                        data-sidebar-badge="commercial_services"
                        class="absolute -right-1 -top-1 hidden h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white ring-2 ring-sky-800">0</span>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Commercial Services</span>
            </a>

            {{-- Pending Final Closure --}}
            <a href="{{ route('customer-service.final-closures.index') }}"
                title="Pending Final Closure"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.final-closures.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.final-closures.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fas fa-clipboard-check"></i>
                    <span id="sidebar-final-closures-badge"
                        data-sidebar-badge="final_closures"
                        class="absolute -right-1 -top-1 hidden h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white ring-2 ring-sky-800">0</span>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Pending Final Closure</span>
            </a>

            {{-- Pending Workload --}}
            <a href="{{ route('customer-service.pending-workloads.index') }}"
                title="Pending Workload"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.pending-workloads.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.pending-workloads.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span id="sidebar-pending-workloads-badge"
                        data-sidebar-badge="pending_workloads"
                        class="absolute -right-1 -top-1 hidden h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white ring-2 ring-sky-800">0</span>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Pending Workload</span>
            </a>

            {{-- Missed Schedule --}}
            <a href="{{ route('customer-service.missed-schedules.index') }}"
                title="Missed Schedule"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.missed-schedules.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.missed-schedules.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fas fa-calendar-xmark"></i>
                    <span id="sidebar-missed-schedules-badge"
                        data-sidebar-badge="missed_schedules"
                        class="absolute -right-1 -top-1 hidden h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white ring-2 ring-sky-800">0</span>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Missed Schedule</span>
            </a>

            {{-- Consumer Feedback --}}
            <a href="{{ route('customer-service.feedback.index') }}"
                title="Consumer Feedback"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.feedback.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.feedback.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fas fa-star"></i>
                    <span id="sidebar-feedback-badge"
                        data-sidebar-badge="feedback"
                        class="absolute -right-1 -top-1 {{ $sidebarFeedbackCount > 0 ? 'flex' : 'hidden' }} h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white ring-2 ring-sky-800"
                        style="{{ $sidebarFeedbackCount > 0 ? '' : 'display:none' }}">{{ $sidebarFeedbackCount > 99 ? '99+' : $sidebarFeedbackCount }}</span>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Consumer Feedback</span>
            </a>

            {{-- Configuration --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">
                    Configuration
                </p>
            </div>

            <a href="{{ route('customer-service.complaint-categories.index') }}"
                title="Complaint Types"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.complaint-categories.*') ? 'bg-white text-sky-800 shadow-sm' : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.complaint-categories.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fas fa-list-check"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Complaint Types</span>
            </a>

            <a href="{{ route('customer-service.divisions.index') }}"
                title="Divisions"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.divisions.*') ? 'bg-white text-sky-800 shadow-sm' : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.divisions.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fas fa-sitemap"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Divisions</span>
            </a>

            {{-- Insights --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">
                    Insights
                </p>
            </div>

            <a href="{{ route('customer-service.reports.index') }}"
                title="Reports"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-sky-100 transition hover:bg-white/10 hover:text-white"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sky-200 group-hover:text-white">
                    <i class="fas fa-chart-column"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Reports &amp; Analytics</span>
            </a>

        </nav>
    </div>

    {{-- Footer --}}
    <div class="shrink-0 border-t border-white/10 p-3"
        :class="sidebarMini ? 'lg:px-2' : ''">

        <div class="rounded-xl bg-black/10 px-3 py-3"
            :class="sidebarMini ? 'lg:flex lg:justify-center lg:px-2' : ''">

            <div x-show="!sidebarMini" x-transition.opacity>
                <p class="text-[11px] font-semibold text-white">Sagay Water District</p>
                <p class="mt-0.5 text-[10px] text-sky-200">
                    Consumer service management
                </p>
            </div>

            <i x-show="sidebarMini" class="fas fa-building text-sm text-sky-200"></i>
        </div>
    </div>
</aside>

<div x-cloak x-show="sidebarOpen" x-transition.opacity
    @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-[1px] lg:hidden">
</div>

{{-- Automatic sidebar notification polling --}}
<script>
(() => {
    if (window.customerServiceSidebarNotificationsStarted) {
        return;
    }

    window.customerServiceSidebarNotificationsStarted = true;

    const endpoint = @json(route('customer-service.sidebar-notifications'));

    function updateBadge(key, count) {
        const badge = document.querySelector(
            '[data-sidebar-badge="' + key + '"]'
        );

        if (!badge) {
            return;
        }

        const value = Math.max(0, Number(count) || 0);

        badge.textContent = value > 99 ? '99+' : String(value);
        badge.style.display = value > 0 ? 'flex' : 'none';
    }

    async function refreshSidebarNotifications() {
        if (document.hidden) {
            return;
        }

        try {
            const response = await fetch(endpoint, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                cache: 'no-store'
            });

            if (!response.ok) {
                console.error(
                    'Sidebar notification request failed:',
                    response.status
                );
                return;
            }

            const data = await response.json();

            updateBadge('complaints', data.complaints);
            updateBadge('verification', data.verification);
            updateBadge('commercial_services', data.commercial_services);
            updateBadge('final_closures', data.final_closures);
            updateBadge('pending_workloads', data.pending_workloads);
            updateBadge('missed_schedules', data.missed_schedules);
            updateBadge('feedback', data.feedback);
        } catch (error) {
            console.error('Could not refresh sidebar notifications.', error);
        }
    }

    refreshSidebarNotifications();

    // Poll every 3 seconds without reloading the page.
    window.setInterval(refreshSidebarNotifications, 3000);

    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            refreshSidebarNotifications();
        }
    });
})();
</script>
