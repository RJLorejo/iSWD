@php

    $forAssignmentCount = \App\Models\Complaint::query()
        ->whereDoesntHave('technicians')
        ->where(function ($query) {
            $query
                ->where(function ($engineeringQuery) {
                    $engineeringQuery->where('status', 'Verified')->whereHas('division', function ($divisionQuery) {
                        $divisionQuery->where('name', 'like', '%Engineering%');
                    });
                })
                ->orWhere('status', 'For Maintenance');
        })
        ->count();

    $pendingReviews = \App\Models\MaintenanceReport::query()
        ->whereNotNull('submitted_at')
        ->where('review_status', 'Pending Review')
        ->count();

@endphp


<aside
    :class="[
        sidebarMini ? 'lg:w-20' : 'lg:w-72',
        sidebarOpen ?
        'translate-x-0' :
        '-translate-x-full lg:translate-x-0'
    ]"
    class="fixed inset-y-0 left-0 z-50
           flex w-72 flex-col
           bg-gradient-to-b
           from-sky-800 via-blue-800 to-cyan-800
           text-white shadow-2xl
           transition-all duration-300 ease-in-out">


    {{-- ========================================================= --}}
    {{-- BRAND --}}
    {{-- ========================================================= --}}

    <div class="flex h-16 shrink-0 items-center
               border-b border-white/10
               px-4 lg:h-[72px]"
        :class="sidebarMini
            ?
            'lg:justify-center lg:px-2' :
            ''">


        <a href="{{ route('maintenance-manager.dashboard') }}" class="flex min-w-0 items-center gap-3"
            :class="sidebarMini
                ?
                'lg:justify-center' :
                ''">


            {{-- LOGO --}}

            <div
                class="flex h-10 w-10 shrink-0
                       items-center justify-center
                       rounded-xl bg-white/15
                       text-white ring-1 ring-white/10">

                <i class="fas fa-droplet"></i>

            </div>


            {{-- BRAND TEXT --}}

            <div x-show="!sidebarMini" x-transition.opacity class="min-w-0">

                <p class="text-lg font-bold tracking-tight">
                    iSWD
                </p>

                <p class="truncate text-[11px]
                           font-medium text-sky-200">

                    Maintenance Manager Portal

                </p>

            </div>

        </a>


        {{-- MOBILE CLOSE BUTTON --}}

        <button type="button" @click="sidebarOpen = false"
            class="ml-auto flex h-9 w-9
                   items-center justify-center
                   rounded-xl text-sky-100
                   transition
                   hover:bg-white/10 hover:text-white
                   lg:hidden"
            aria-label="Close navigation">

            <i class="fas fa-xmark"></i>

        </button>

    </div>



    {{-- ========================================================= --}}
    {{-- USER PROFILE --}}
    {{-- ========================================================= --}}

    <div class="shrink-0 border-b
               border-white/10 p-4"
        :class="sidebarMini
            ?
            'lg:px-3' :
            ''">


        <div class="flex items-center gap-3
                   rounded-2xl bg-white/10
                   p-3 ring-1 ring-white/5"
            :class="sidebarMini
                ?
                'lg:justify-center lg:p-2' :
                ''">


            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->full_name ?? auth()->user()->name }}"
                class="h-10 w-10 shrink-0
                       rounded-xl border
                       border-white/20 object-cover">


            <div x-show="!sidebarMini" x-transition.opacity class="min-w-0">

                <p class="truncate text-sm
                           font-bold text-white">

                    {{ auth()->user()->full_name ?? auth()->user()->name }}

                </p>

                <p class="mt-0.5 truncate
                           text-[11px] text-sky-200">

                    Maintenance Manager

                </p>

            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- NAVIGATION --}}
    {{-- ========================================================= --}}

    <div class="flex-1 overflow-y-auto
               overflow-x-hidden px-3 py-5">

        <nav class="space-y-1.5">


            {{-- ================================================= --}}
            {{-- OVERVIEW --}}
            {{-- ================================================= --}}

            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2">

                <p
                    class="text-[10px] font-bold
                           uppercase tracking-[0.18em]
                           text-sky-300">

                    Overview

                </p>

            </div>


            {{-- DASHBOARD --}}

            <a href="{{ route('maintenance-manager.dashboard') }}" title="Dashboard"
                class="group flex items-center gap-3
                       rounded-xl px-3 py-2.5
                       text-sm font-semibold
                       transition

                       {{ request()->routeIs('maintenance-manager.dashboard')
                           ? 'bg-white text-sky-800 shadow-sm'
                           : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini
                    ?
                    'lg:justify-center' :
                    ''">


                <span
                    class="flex h-8 w-8 shrink-0
                           items-center justify-center
                           rounded-lg

                           {{ request()->routeIs('maintenance-manager.dashboard')
                               ? 'bg-sky-50 text-sky-700'
                               : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-chart-line"></i>

                </span>


                <span x-show="!sidebarMini" x-transition.opacity>

                    Dashboard

                </span>

            </a>



            {{-- ================================================= --}}
            {{-- COMPLAINT MANAGEMENT --}}
            {{-- ================================================= --}}

            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">

                <p
                    class="text-[10px] font-bold
                           uppercase tracking-[0.18em]
                           text-sky-300">

                    Complaint Management

                </p>

            </div>



            {{-- ================================================= --}}
            {{-- ALL COMPLAINTS --}}
            {{-- ================================================= --}}

            @php

                $allComplaintsActive =
                    request()->routeIs('maintenance-manager.complaints.index') ||
                    request()->routeIs('maintenance-manager.complaints.show') ||
                    request()->routeIs('maintenance-manager.complaints.report');

            @endphp


            <a href="{{ route('maintenance-manager.complaints.index') }}" title="All Complaints"
                class="group flex items-center gap-3
                       rounded-xl px-3 py-2.5
                       text-sm font-semibold
                       transition

                       {{ $allComplaintsActive ? 'bg-white text-sky-800 shadow-sm' : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini
                    ?
                    'lg:justify-center' :
                    ''">


                <span
                    class="flex h-8 w-8 shrink-0
                           items-center justify-center
                           rounded-lg

                           {{ $allComplaintsActive ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-file-circle-exclamation"></i>

                </span>


                <span x-show="!sidebarMini" x-transition.opacity>

                    All Complaints

                </span>

            </a>

            <a href="{{ route('maintenance-manager.complaints.for-assignment') }}" title="For Assignment"
                class="group flex items-center gap-3
                       rounded-xl px-3 py-2.5
                       text-sm font-semibold
                       transition

                       {{ request()->routeIs('maintenance-manager.complaints.for-assignment')
                           ? 'bg-white text-sky-800 shadow-sm'
                           : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini
                    ?
                    'lg:justify-center' :
                    ''">


                {{-- ICON --}}

                <span
                    class="relative flex h-8 w-8
                           shrink-0 items-center
                           justify-center rounded-lg

                           {{ request()->routeIs('maintenance-manager.complaints.for-assignment')
                               ? 'bg-sky-50 text-sky-700'
                               : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-user-plus"></i>


                    {{-- MINI SIDEBAR BADGE --}}

                    @if ($forAssignmentCount > 0)
                        <span x-show="sidebarMini"
                            class="absolute -right-1 -top-1
                                   hidden h-4 min-w-4
                                   items-center justify-center
                                   rounded-full bg-amber-400
                                   px-1 text-[8px]
                                   font-bold text-slate-900
                                   ring-2 ring-sky-800
                                   lg:flex">

                            {{ $forAssignmentCount > 9 ? '9+' : $forAssignmentCount }}

                        </span>
                    @endif

                </span>


                {{-- LABEL --}}

                <span x-show="!sidebarMini" x-transition.opacity class="min-w-0 flex-1">

                    For Assignment

                </span>


                {{-- NORMAL BADGE --}}

                @if ($forAssignmentCount > 0)
                    <span x-show="!sidebarMini" x-transition.opacity
                        class="ml-auto flex h-5
                               min-w-5 items-center
                               justify-center rounded-full
                               px-1.5 text-[9px]
                               font-bold

                               {{ request()->routeIs('maintenance-manager.complaints.for-assignment')
                                   ? 'bg-amber-100 text-amber-700'
                                   : 'bg-amber-400 text-slate-900' }}">

                        {{ $forAssignmentCount > 99 ? '99+' : $forAssignmentCount }}

                    </span>
                @endif

            </a>



            {{-- ================================================= --}}
            {{-- MAINTENANCE --}}
            {{-- ================================================= --}}

            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">

                <p
                    class="text-[10px] font-bold
                           uppercase tracking-[0.18em]
                           text-sky-300">

                    Maintenance

                </p>

            </div>
            {{-- ================================================= --}}
            {{-- MAINTENANCE REVIEWS --}}
            {{-- ================================================= --}}

            <a href="{{ route('maintenance-manager.maintenance-reviews.index') }}" title="Maintenance Reviews"
                class="group flex items-center gap-3
           rounded-xl px-3 py-2.5
           text-sm font-semibold
           transition

           {{ request()->routeIs('maintenance-manager.maintenance-reviews.*')
               ? 'bg-white text-sky-800 shadow-sm'
               : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini
                    ?
                    'lg:justify-center' :
                    ''">


                <span
                    class="relative flex h-8 w-8
               shrink-0 items-center
               justify-center rounded-lg

               {{ request()->routeIs('maintenance-manager.maintenance-reviews.*')
                   ? 'bg-sky-50 text-sky-700'
                   : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-clipboard-check"></i>


                    {{-- MINI BADGE --}}

                    @if ($pendingReviews > 0)
                        <span x-show="sidebarMini"
                            class="absolute -right-1 -top-1
                       hidden h-4 min-w-4
                       items-center justify-center
                       rounded-full bg-red-500
                       px-1 text-[8px]
                       font-bold text-white
                       ring-2 ring-sky-800
                       lg:flex">

                            {{ $pendingReviews > 9 ? '9+' : $pendingReviews }}

                        </span>
                    @endif

                </span>


                <span x-show="!sidebarMini" x-transition.opacity class="min-w-0 flex-1">

                    Maintenance Reviews

                </span>


                {{-- NORMAL BADGE --}}

                @if ($pendingReviews > 0)
                    <span x-show="!sidebarMini" x-transition.opacity
                        class="ml-auto flex h-5
                   min-w-5 items-center
                   justify-center rounded-full
                   bg-red-500 px-1.5
                   text-[9px] font-bold
                   text-white">

                        {{ $pendingReviews > 99 ? '99+' : $pendingReviews }}

                    </span>
                @endif

            </a>

            {{-- ========================================================= --}}
            {{-- PLUMBER SERVICE AREAS --}}
            {{-- ========================================================= --}}

            <a href="{{ route('maintenance-manager.service-areas.index') }}" title="Plumber Service Areas"
                class="group flex items-center gap-3
           rounded-xl px-3 py-2.5
           text-sm font-semibold
           transition

           {{ request()->routeIs('maintenance-manager.service-areas.*')
               ? 'bg-white text-sky-800 shadow-sm'
               : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini
                    ?
                    'lg:justify-center' :
                    ''">

                <span
                    class="flex h-8 w-8 shrink-0
               items-center justify-center
               rounded-lg

               {{ request()->routeIs('maintenance-manager.service-areas.*')
                   ? 'bg-sky-50 text-sky-700'
                   : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-map-location-dot"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>

                    Plumber Service Areas

                </span>

            </a>

        </nav>

    </div>



    {{-- ========================================================= --}}
    {{-- FOOTER --}}
    {{-- ========================================================= --}}

    <div class="shrink-0 border-t
               border-white/10 p-3"
        :class="sidebarMini
            ?
            'lg:px-2' :
            ''">

        <div class="rounded-xl bg-black/10
                   px-3 py-3"
            :class="sidebarMini
                ?
                'lg:flex lg:justify-center lg:px-2' :
                ''">


            <div x-show="!sidebarMini" x-transition.opacity>

                <p class="text-[11px] font-semibold text-white">
                    Sagay Water District
                </p>

                <p class="mt-0.5 text-[10px] text-sky-200">
                    Maintenance management
                </p>

            </div>


            <i x-show="sidebarMini" class="fas fa-building
                       text-sm text-sky-200">
            </i>

        </div>

    </div>

</aside>



{{-- ============================================================= --}}
{{-- MOBILE OVERLAY --}}
{{-- ============================================================= --}}

<div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
    class="fixed inset-0 z-40
           bg-slate-950/50
           backdrop-blur-[1px]
           lg:hidden">
</div>
