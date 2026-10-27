@php
    $adminUser = auth()->user();
    $sidebarNotificationUrl = route('admin.sidebar.notifications');
@endphp

<aside
    :class="[
        sidebarMini ? 'lg:w-20' : 'lg:w-72',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col
           bg-gradient-to-b from-sky-800 via-blue-800 to-cyan-800
           text-white shadow-2xl transition-all duration-300 ease-in-out">

    {{-- BRAND --}}
    <div class="flex h-16 shrink-0 items-center border-b border-white/10 px-4 lg:h-[72px]"
        :class="sidebarMini ? 'lg:justify-center lg:px-2' : ''">

        <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-3"
            :class="sidebarMini ? 'lg:justify-center' : ''">

            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center
                        rounded-xl bg-white/15 text-white ring-1 ring-white/10">
                <i class="fa-solid fa-droplet"></i>
            </div>

            <div x-show="!sidebarMini" x-transition.opacity class="min-w-0">
                <p class="text-lg font-bold tracking-tight">iSWD</p>
                <p class="truncate text-[11px] font-medium text-sky-200">
                    Division Manager
                </p>
            </div>
        </a>

        <button type="button" @click="sidebarOpen = false"
            class="ml-auto flex h-9 w-9 items-center justify-center rounded-xl
                       text-sky-100 transition hover:bg-white/10 hover:text-white lg:hidden"
            aria-label="Close navigation">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    {{-- ADMIN PROFILE --}}
    <div class="shrink-0 border-b border-white/10 p-4" :class="sidebarMini ? 'lg:px-3' : ''">

        <div class="flex items-center gap-3 rounded-2xl bg-white/10 p-3 ring-1 ring-white/5"
            :class="sidebarMini ? 'lg:justify-center lg:p-2' : ''">

            @if ($adminUser?->avatar)
                <img src="{{ asset('storage/' . $adminUser->avatar) }}"
                    alt="{{ $adminUser->full_name ?? ($adminUser->name ?? 'Division Manager') }}"
                    class="h-10 w-10 shrink-0 rounded-xl border border-white/20 object-cover">
            @else
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center
                            rounded-xl border border-white/20 bg-white/15
                            text-sm font-bold text-white">
                    {{ strtoupper(substr($adminUser?->first_name ?? ($adminUser?->name ?? 'A'), 0, 1)) }}
                </div>
            @endif

            <div x-show="!sidebarMini" x-transition.opacity class="min-w-0">
                <p class="truncate text-sm font-bold text-white">
                    {{ $adminUser?->full_name ?? ($adminUser?->name ?? 'Division Manager') }}
                </p>
                <p class="mt-0.5 truncate text-[11px] text-sky-200">
                    Division Manager
                </p>
            </div>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <div class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-5">
        <nav class="space-y-1.5">

            {{-- OVERVIEW --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">
                    Overview
                </p>
            </div>

            {{-- DASHBOARD --}}
            <a href="{{ route('admin.dashboard') }}" title="Dashboard"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm
                      font-semibold transition
                      {{ request()->routeIs('admin.dashboard')
                          ? 'bg-white text-sky-800 shadow-sm'
                          : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('admin.dashboard') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fa-solid fa-chart-line"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Dashboard</span>
            </a>

            {{-- ACCOUNT MANAGEMENT --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">
                    Account Management
                </p>
            </div>

            {{-- EMPLOYEES --}}
            <a href="{{ route('admin.users.index') }}" title="Employees"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm
                      font-semibold transition
                      {{ request()->routeIs('admin.users.*')
                          ? 'bg-white text-sky-800 shadow-sm'
                          : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('admin.users.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fa-solid fa-users"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Employees</span>
            </a>

            {{-- CONSUMER VERIFICATION --}}
            <a href="{{ route('admin.consumer-verifications.index') }}" title="Consumer Verification"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm
          font-semibold transition
          {{ request()->routeIs('admin.consumer-verifications.*')
              ? 'bg-white text-sky-800 shadow-sm'
              : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                {{-- SINGLE ICON --}}
                <span
                    class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
        {{ request()->routeIs('admin.consumer-verifications.*')
            ? 'bg-sky-50 text-sky-700'
            : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fa-solid fa-user-check"></i>

                    {{-- SINGLE AJAX BADGE --}}
                    <span id="consumer-verification-badge" hidden
                        class="absolute -right-1 -top-1 hidden h-4 min-w-4 items-center
                   justify-center rounded-full bg-red-500 px-1 text-[8px]
                   font-bold text-white ring-2 ring-sky-800">
                    </span>
                </span>

                {{-- LABEL --}}
                <span x-show="!sidebarMini" x-transition.opacity class="min-w-0 flex-1">
                    Consumer Verification
                </span>
            </a>

            {{-- CONSUMERS --}}
            <a href="{{ route('admin.consumers.index') }}" title="Consumers"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm
           font-semibold transition
           {{ request()->routeIs('admin.consumers.*')
               ? 'bg-white text-sky-800 shadow-sm'
               : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
               {{ request()->routeIs('admin.consumers.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fa-solid fa-users"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Consumers
                </span>
            </a>

            {{-- ORGANIZATION --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">
                    Organization
                </p>
            </div>

            {{-- DEPARTMENTS --}}
            <a href="{{ route('admin.departments.index') }}" title="Departments"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm
                      font-semibold transition
                      {{ request()->routeIs('admin.departments.*')
                          ? 'bg-white text-sky-800 shadow-sm'
                          : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('admin.departments.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fa-solid fa-building"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Departments</span>
            </a>

            {{-- POSITIONS --}}
            <a href="{{ route('admin.positions.index') }}" title="Positions"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm
                      font-semibold transition
                      {{ request()->routeIs('admin.positions.*')
                          ? 'bg-white text-sky-800 shadow-sm'
                          : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('admin.positions.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fa-solid fa-briefcase"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Positions</span>
            </a>

            {{-- SERVICE MANAGEMENT --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">
                    Service Management
                </p>
            </div>

            {{-- COMPLAINTS --}}
            <a href="{{ route('admin.complaints.index') }}" title="Complaints"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm
                      font-semibold transition
                      {{ request()->routeIs('admin.complaints.*')
                          ? 'bg-white text-sky-800 shadow-sm'
                          : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('admin.complaints.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fa-solid fa-file-circle-exclamation"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Complaints</span>
            </a>

            {{-- Pending Workloads --}}
            <a href="{{ route('admin.maintenance-monitoring.pending-workloads') }}" title="Pending Workloads"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
    {{ request()->routeIs('admin.maintenance-monitoring.pending-workloads*')
        ? 'bg-white text-sky-800 shadow-sm'
        : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
        {{ request()->routeIs('admin.maintenance-monitoring.pending-workloads*')
            ? 'bg-sky-50 text-sky-700'
            : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fas fa-clipboard-list"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Pending Workloads
                </span>
            </a>

            {{-- Missed Schedules --}}
            <a href="{{ route('admin.maintenance-monitoring.missed-schedules') }}" title="Missed Schedules"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
    {{ request()->routeIs('admin.maintenance-monitoring.missed-schedules*')
        ? 'bg-white text-sky-800 shadow-sm'
        : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
        {{ request()->routeIs('admin.maintenance-monitoring.missed-schedules*')
            ? 'bg-sky-50 text-sky-700'
            : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fas fa-calendar-xmark"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Missed Schedules
                </span>
            </a>


            {{-- CONSUMER FEEDBACK --}}
            <a href="{{ route('admin.consumer-feedback.index') }}" title="Consumer Feedback"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
    {{ request()->routeIs('admin.consumer-feedback.*')
        ? 'bg-white text-sky-800 shadow-sm'
        : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
        {{ request()->routeIs('admin.consumer-feedback.*')
            ? 'bg-sky-50 text-sky-700'
            : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fa-solid fa-comments"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Consumer Feedback
                </span>
            </a>


            {{-- SYSTEM --}}
            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">
                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">
                    System
                </p>
            </div>

            {{-- SYSTEM ACTIVITY LOG --}}
            <a href="{{ route('admin.audit-logs.index') }}" title="System Activity Log"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
    {{ request()->routeIs('admin.audit-logs.*')
        ? 'bg-white text-sky-800 shadow-sm'
        : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
        {{ request()->routeIs('admin.audit-logs.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    System Activity Log
                </span>
            </a>
            {{-- REPORTS --}}
            <a href="{{ route('admin.reports.index') }}" title="Reports"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm
                      font-semibold transition
                      {{ request()->routeIs('admin.reports.*')
                          ? 'bg-white text-sky-800 shadow-sm'
                          : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('admin.reports.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fa-solid fa-chart-column"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>Reports</span>
            </a>
            {{-- NOTIFICATION SETTINGS --}}
            <a href="{{ route('admin.settings.mail.index') }}" title="Notification Settings"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
    {{ request()->routeIs('admin.settings.mail.*')
        ? 'bg-white text-sky-800 shadow-sm'
        : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
        {{ request()->routeIs('admin.settings.mail.*')
            ? 'bg-sky-50 text-sky-700'
            : 'text-sky-200 group-hover:text-white' }}">
                    <i class="fa-solid fa-envelope"></i>
                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Notification Settings
                </span>
            </a>
        </nav>
    </div>

    {{-- FOOTER --}}
    <div class="shrink-0 border-t border-white/10 p-3" :class="sidebarMini ? 'lg:px-2' : ''">

        <div class="rounded-xl bg-black/10 px-3 py-3" :class="sidebarMini ? 'lg:flex lg:justify-center lg:px-2' : ''">

            <div x-show="!sidebarMini" x-transition.opacity>
                <p class="text-[11px] font-semibold text-white">Sagay Water District</p>
                <p class="mt-0.5 text-[10px] text-sky-200">Division Manager</p>
            </div>

            <i x-show="sidebarMini" class="fa-solid fa-building text-sm text-sky-200"></i>
        </div>
    </div>
</aside>

{{-- MOBILE OVERLAY --}}
<div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-[1px] lg:hidden">
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationUrl = @json($sidebarNotificationUrl);
        const badge = document.getElementById('consumer-verification-badge');

        if (!badge || window.adminSidebarNotificationsStarted) {
            return;
        }

        window.adminSidebarNotificationsStarted = true;

        let requestInProgress = false;
        let refreshTimer = null;

        async function refreshConsumerVerificationCount() {
            if (
                requestInProgress ||
                document.visibilityState === 'hidden'
            ) {
                return;
            }

            requestInProgress = true;

            try {
                const response = await fetch(notificationUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    cache: 'no-store'
                });

                if (!response.ok) {
                    throw new Error(
                        `Notification request failed: ${response.status}`
                    );
                }

                const data = await response.json();
                const count = Number(data.pending_consumer_verifications);

                if (!Number.isFinite(count) || count < 0) {
                    throw new Error('Invalid consumer verification count.');
                }

                badge.textContent = count > 99 ? '99+' : String(count);
                badge.classList.toggle('hidden', count === 0);
                badge.style.display = count > 0 ? 'flex' : 'none';
                badge.hidden = count === 0;

            } catch (error) {
                // Keep the last successful count when the request fails.
                console.error(
                    'Could not refresh Admin sidebar notifications:',
                    error
                );
            } finally {
                requestInProgress = false;
            }
        }

        function startPolling() {
            if (refreshTimer !== null) {
                return;
            }

            refreshConsumerVerificationCount();
            refreshTimer = window.setInterval(
                refreshConsumerVerificationCount,
                3000
            );
        }

        function stopPolling() {
            if (refreshTimer !== null) {
                window.clearInterval(refreshTimer);
                refreshTimer = null;
            }
        }

        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                refreshConsumerVerificationCount();

                if (refreshTimer === null) {
                    startPolling();
                }
            } else {
                stopPolling();
            }
        });

        startPolling();
    });
</script>
