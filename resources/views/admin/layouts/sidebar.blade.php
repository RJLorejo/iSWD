@php

    $pendingConsumerVerifications = \App\Models\Consumer::query()
        ->where('registration_source', 'Self Registration')
        ->where('verification_status', 'Pending Verification')
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

    <div class="flex h-16 lg:h-[72px]
               shrink-0 items-center
               border-b border-white/10 px-4"
        :class="sidebarMini
            ?
            'lg:justify-center lg:px-2' :
            ''">

        <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-3"
            :class="sidebarMini
                ?
                'lg:justify-center' :
                ''">

            <div
                class="flex h-10 w-10 shrink-0
                       items-center justify-center
                       rounded-xl bg-white/15
                       text-white ring-1 ring-white/10">

                <i class="fa-solid fa-droplet"></i>

            </div>

            <div x-show="!sidebarMini" x-transition.opacity class="min-w-0">

                <p class="text-lg font-bold tracking-tight">
                    iSWD
                </p>

                <p class="truncate text-[11px]
                          font-medium text-sky-200">
                    Administrator Portal
                </p>

            </div>

        </a>


        {{-- Mobile Close --}}

        <button type="button" @click="sidebarOpen = false"
            class="ml-auto flex h-9 w-9
                   items-center justify-center
                   rounded-xl text-sky-100
                   transition
                   hover:bg-white/10
                   hover:text-white
                   lg:hidden"
            aria-label="Close navigation">

            <i class="fa-solid fa-xmark"></i>

        </button>

    </div>


    {{-- ========================================================= --}}
    {{-- ADMIN PROFILE --}}
    {{-- ========================================================= --}}

    <div class="shrink-0
               border-b border-white/10 p-4"
        :class="sidebarMini
            ?
            'lg:px-3' :
            ''">

        <div class="flex items-center gap-3
                   rounded-2xl bg-white/10 p-3
                   ring-1 ring-white/5"
            :class="sidebarMini
                ?
                'lg:justify-center lg:p-2' :
                ''">

            @if (auth()->user()->avatar)
                <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                    alt="{{ auth()->user()->full_name ?? auth()->user()->name }}"
                    class="h-10 w-10 shrink-0
                           rounded-xl border border-white/20
                           object-cover">
            @else
                <div
                    class="flex h-10 w-10 shrink-0
                           items-center justify-center
                           rounded-xl bg-white/15
                           border border-white/20
                           text-sm font-bold text-white">

                    {{ strtoupper(substr(auth()->user()->first_name ?? (auth()->user()->name ?? 'A'), 0, 1)) }}

                </div>
            @endif


            <div x-show="!sidebarMini" x-transition.opacity class="min-w-0">

                <p class="truncate text-sm
                          font-bold text-white">

                    {{ auth()->user()->full_name ?? (auth()->user()->name ?? 'Administrator') }}

                </p>

                <p class="mt-0.5 truncate
                          text-[11px] text-sky-200">
                    System Administrator
                </p>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- NAVIGATION --}}
    {{-- ========================================================= --}}

    <div class="flex-1
                overflow-y-auto overflow-x-hidden
                px-3 py-5">

        <nav class="space-y-1.5">


            {{-- ===================================================== --}}
            {{-- OVERVIEW --}}
            {{-- ===================================================== --}}

            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2">

                <p
                    class="text-[10px] font-bold
                          uppercase tracking-[0.18em]
                          text-sky-300">
                    Overview
                </p>

            </div>


            {{-- Dashboard --}}

            <a href="{{ route('admin.dashboard') }}" title="Dashboard"
                class="group flex items-center gap-3
                       rounded-xl px-3 py-2.5
                       text-sm font-semibold transition
                       {{ request()->routeIs('admin.dashboard')
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
                           {{ request()->routeIs('admin.dashboard') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fa-solid fa-chart-line"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Dashboard
                </span>

            </a>


            {{-- ===================================================== --}}
            {{-- ACCOUNT MANAGEMENT --}}
            {{-- ===================================================== --}}

            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">

                <p
                    class="text-[10px] font-bold
                          uppercase tracking-[0.18em]
                          text-sky-300">
                    Account Management
                </p>

            </div>


            {{-- Employees --}}

            <a href="{{ route('admin.users.index') }}" title="Employees"
                class="group flex items-center gap-3
                       rounded-xl px-3 py-2.5
                       text-sm font-semibold transition
                       {{ request()->routeIs('admin.users.*')
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
                           {{ request()->routeIs('admin.users.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fa-solid fa-users"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Employees
                </span>

            </a>


            {{-- Consumer Verification --}}

            <a href="{{ route('admin.consumer-verifications.index') }}" title="Consumer Verification"
                class="group flex items-center gap-3
                       rounded-xl px-3 py-2.5
                       text-sm font-semibold transition
                       {{ request()->routeIs('admin.consumer-verifications.*')
                           ? 'bg-white text-sky-800 shadow-sm'
                           : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini
                    ?
                    'lg:justify-center' :
                    ''">

                <span
                    class="relative flex h-8 w-8 shrink-0
                           items-center justify-center
                           rounded-lg
                           {{ request()->routeIs('admin.consumer-verifications.*')
                               ? 'bg-sky-50 text-sky-700'
                               : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fa-solid fa-user-check"></i>


                    @if ($pendingConsumerVerifications > 0)
                        <span x-show="sidebarMini"
                            class="absolute -right-1 -top-1
                                   hidden h-4 min-w-4
                                   items-center justify-center
                                   rounded-full bg-red-500
                                   px-1 text-[8px]
                                   font-bold text-white
                                   ring-2 ring-sky-800
                                   lg:flex">

                            {{ $pendingConsumerVerifications > 9 ? '9+' : $pendingConsumerVerifications }}

                        </span>
                    @endif

                </span>


                <span x-show="!sidebarMini" x-transition.opacity class="min-w-0 flex-1">

                    Consumer Verification

                </span>


                @if ($pendingConsumerVerifications > 0)
                    <span x-show="!sidebarMini" x-transition.opacity
                        class="ml-auto flex h-5 min-w-5
                               items-center justify-center
                               rounded-full bg-red-500
                               px-1.5 text-[9px]
                               font-bold text-white">

                        {{ $pendingConsumerVerifications > 99 ? '99+' : $pendingConsumerVerifications }}

                    </span>
                @endif

            </a>


            {{-- ===================================================== --}}
            {{-- ORGANIZATION --}}
            {{-- ===================================================== --}}

            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">

                <p
                    class="text-[10px] font-bold
                          uppercase tracking-[0.18em]
                          text-sky-300">
                    Organization
                </p>

            </div>


            {{-- Departments --}}

            <a href="{{ route('admin.departments.index') }}" title="Departments"
                class="group flex items-center gap-3
                       rounded-xl px-3 py-2.5
                       text-sm font-semibold transition
                       {{ request()->routeIs('admin.departments.*')
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
                           {{ request()->routeIs('admin.departments.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fa-solid fa-building"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Departments
                </span>

            </a>


            {{-- Positions --}}

            <a href="{{ route('admin.positions.index') }}" title="Positions"
                class="group flex items-center gap-3
                       rounded-xl px-3 py-2.5
                       text-sm font-semibold transition
                       {{ request()->routeIs('admin.positions.*')
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
                           {{ request()->routeIs('admin.positions.*') ? 'bg-sky-50 text-sky-700' : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fa-solid fa-briefcase"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Positions
                </span>

            </a>


            {{-- ===================================================== --}}
            {{-- SYSTEM --}}
            {{-- ===================================================== --}}

            <div x-show="!sidebarMini" x-transition.opacity class="px-3 pb-2 pt-5">

                <p
                    class="text-[10px] font-bold
                          uppercase tracking-[0.18em]
                          text-sky-300">
                    System
                </p>

            </div>


            {{-- Reports --}}

            <a href="#" title="Reports"
                class="group flex items-center gap-3
                       rounded-xl px-3 py-2.5
                       text-sm font-semibold
                       text-sky-100 transition
                       hover:bg-white/10 hover:text-white"
                :class="sidebarMini
                    ?
                    'lg:justify-center' :
                    ''">

                <span
                    class="flex h-8 w-8 shrink-0
                           items-center justify-center
                           rounded-lg text-sky-200
                           group-hover:text-white">

                    <i class="fa-solid fa-chart-column"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Reports
                </span>

            </a>


            {{-- Settings --}}

            <a href="#" title="Settings"
                class="group flex items-center gap-3
                       rounded-xl px-3 py-2.5
                       text-sm font-semibold
                       text-sky-100 transition
                       hover:bg-white/10 hover:text-white"
                :class="sidebarMini
                    ?
                    'lg:justify-center' :
                    ''">

                <span
                    class="flex h-8 w-8 shrink-0
                           items-center justify-center
                           rounded-lg text-sky-200
                           group-hover:text-white">

                    <i class="fa-solid fa-gear"></i>

                </span>

                <span x-show="!sidebarMini" x-transition.opacity>
                    Settings
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
                    System administration
                </p>

            </div>

            <i x-show="sidebarMini" class="fa-solid fa-building
                       text-sm text-sky-200">
            </i>

        </div>

    </div>

</aside>


{{-- ========================================================= --}}
{{-- MOBILE OVERLAY --}}
{{-- ========================================================= --}}

<div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
    class="fixed inset-0 z-40
           bg-slate-950/50
           backdrop-blur-[1px]
           lg:hidden">
</div>
