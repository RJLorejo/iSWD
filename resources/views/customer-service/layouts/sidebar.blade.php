@php
    $newFeedbackCount = \App\Models\ComplaintFeedback::query()->where('handling_status', 'New')->count();
@endphp

<aside
    :class="[
        sidebarMini ? 'lg:w-20' : 'lg:w-72',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-gradient-to-b from-sky-800 via-blue-800 to-cyan-800 text-white shadow-2xl transition-all duration-300 ease-in-out">

    <div class="flex h-16 shrink-0 items-center border-b border-white/10 px-4 lg:h-[72px]"
        :class="sidebarMini ? 'lg:justify-center lg:px-2' : ''">

        <a href="{{ route('customer-service.dashboard') }}" class="flex min-w-0 items-center gap-3"
            :class="sidebarMini ? 'lg:justify-center' : ''">

            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white ring-1 ring-white/10">

                <i class="fas fa-droplet"></i>

            </div>

            <div x-show="!sidebarMini" x-transition.opacity class="min-w-0">

                <p class="text-lg font-bold tracking-tight">
                    iSWD
                </p>

                <p class="truncate text-[11px] font-medium text-sky-200">
                    Customer Service Portal
                </p>

            </div>

        </a>

        <button type="button" @click="sidebarOpen = false"
            class="ml-auto flex h-9 w-9 items-center justify-center rounded-xl text-sky-100 transition hover:bg-white/10 hover:text-white lg:hidden"
            aria-label="Close navigation">

            <i class="fas fa-xmark"></i>

        </button>

    </div>

    <div class="shrink-0 border-b border-white/10 p-4" :class="sidebarMini ? 'lg:px-3' : ''">

        <div class="flex items-center gap-3 rounded-2xl bg-white/10 p-3 ring-1 ring-white/5"
            :class="sidebarMini ? 'lg:justify-center lg:p-2' : ''">

            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->full_name ?? auth()->user()->name }}"
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

            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2">

                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">
                    Overview
                </p>

            </div>

            <a href="{{ route('customer-service.dashboard') }}" title="Dashboard"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.dashboard')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.dashboard')
                        ? 'bg-sky-50 text-sky-700'
                        : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-chart-line"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Dashboard
                </span>

            </a>

            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">

                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">
                    Consumer Services
                </p>

            </div>

            <a href="{{ route('customer-service.announcements.index') }}" title="Announcements"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.announcements.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.announcements.*')
                        ? 'bg-sky-50 text-sky-700'
                        : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fa-solid fa-bullhorn"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Announcements
                </span>

            </a>

            <a href="{{ route('customer-service.consumers.index') }}" title="Consumers"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.consumers.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.consumers.*')
                        ? 'bg-sky-50 text-sky-700'
                        : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-users"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Consumers
                </span>

            </a>

            <a href="{{ route('customer-service.complaints.index') }}" title="Complaints"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.complaints.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.complaints.*')
                        ? 'bg-sky-50 text-sky-700'
                        : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-file-circle-exclamation"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Complaints
                </span>

            </a>

            <a href="{{ route('customer-service.complaint-verification.index') }}" title="Complaint Verification"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.complaint-verification.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.complaint-verification.*')
                        ? 'bg-sky-50 text-sky-700'
                        : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-shield-halved"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Complaint Verification
                </span>

            </a>

            <a href="{{ route('customer-service.feedback.index') }}" title="Consumer Feedback"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.feedback.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.feedback.*')
                        ? 'bg-sky-50 text-sky-700'
                        : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-star"></i>

                    @if ($newFeedbackCount > 0)
                        <span x-show="sidebarMini"
                            class="absolute -right-1 -top-1 hidden h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[8px] font-bold text-white ring-2 ring-sky-800 lg:flex">

                            {{ $newFeedbackCount > 9 ? '9+' : $newFeedbackCount }}

                        </span>
                    @endif

                </span>

                <span x-show="!sidebarMini" x-transition.opacity class="min-w-0 flex-1">
                    Consumer Feedback
                </span>

                @if ($newFeedbackCount > 0)
                    <span x-show="!sidebarMini" x-transition.opacity
                        class="ml-auto flex h-5 min-w-5 items-center justify-center rounded-full
                        {{ request()->routeIs('customer-service.feedback.*') ? 'bg-red-100 text-red-600' : 'bg-red-500 text-white' }}
                        px-1.5 text-[9px] font-bold">

                        {{ $newFeedbackCount > 99 ? '99+' : $newFeedbackCount }}

                    </span>
                @endif

            </a>

            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">

                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">
                    Configuration
                </p>

            </div>

            <a href="{{ route('customer-service.complaint-categories.index') }}" title="Complaint Types"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.complaint-categories.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.complaint-categories.*')
                        ? 'bg-sky-50 text-sky-700'
                        : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-list-check"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Complaint Types
                </span>

            </a>

            <a href="{{ route('customer-service.divisions.index') }}" title="Divisions"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition
                {{ request()->routeIs('customer-service.divisions.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('customer-service.divisions.*')
                        ? 'bg-sky-50 text-sky-700'
                        : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-sitemap"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Divisions
                </span>

            </a>

            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">

                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-sky-300">
                    Insights
                </p>

            </div>

            <a href="#" title="Reports"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-sky-100 transition hover:bg-white/10 hover:text-white"
                :class="sidebarMini ? 'lg:justify-center' : ''">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-sky-200 group-hover:text-white">

                    <i class="fas fa-chart-column"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Reports
                </span>

            </a>

        </nav>

    </div>

    <div class="shrink-0 border-t border-white/10 p-3" :class="sidebarMini ? 'lg:px-2' : ''">

        <div class="rounded-xl bg-black/10 px-3 py-3" :class="sidebarMini ? 'lg:flex lg:justify-center lg:px-2' : ''">

            <div x-show="!sidebarMini" x-transition.opacity>

                <p class="text-[11px] font-semibold text-white">
                    Sagay Water District
                </p>

                <p class="mt-0.5 text-[10px] text-sky-200">
                    Consumer service management
                </p>

            </div>

            <i x-show="sidebarMini" class="fas fa-building text-sm text-sky-200">
            </i>

        </div>

    </div>

</aside>

<div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-[1px] lg:hidden">
</div>
