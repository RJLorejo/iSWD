<aside
    :class="[
        sidebarMini ? 'lg:w-20' : 'lg:w-72',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
    class="fixed left-0 top-0 z-50 h-screen
           bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700
           text-white transition-all duration-300">

    {{-- ========================================================= --}}
    {{-- LOGO --}}
    {{-- ========================================================= --}}

    <div class="h-20 flex items-center justify-center border-b border-sky-600">

        <div x-show="!sidebarMini">

            <h1 class="text-2xl font-bold text-center">
                iSWD
            </h1>

            <p class="text-xs text-sky-200 text-center">
                Administrator Panel
            </p>

        </div>

        <i x-show="sidebarMini" class="fa-solid fa-droplet text-3xl"></i>

    </div>


    {{-- ========================================================= --}}
    {{-- ADMIN PROFILE --}}
    {{-- ========================================================= --}}

    <div class="p-5 border-b border-sky-600">

        <div class="flex items-center gap-3">

            <img class="w-12 h-12 rounded-full object-cover shrink-0"
                src="https://ui-avatars.com/api/?background=ffffff&color=2563eb&name={{ urlencode(auth()->user()->full_name ?? (auth()->user()->name ?? 'Admin')) }}"
                alt="Administrator">

            <div x-show="!sidebarMini" class="min-w-0">

                <div class="font-semibold truncate">

                    {{ auth()->user()->full_name ?? (auth()->user()->name ?? 'Administrator') }}

                </div>

                <div class="text-xs text-sky-200">
                    Administrator
                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- NAVIGATION --}}
    {{-- ========================================================= --}}

    <nav class="mt-5 px-3 pb-6 space-y-2 overflow-y-auto">


        {{-- Dashboard --}}

        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl transition
                {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 shadow-sm' : 'hover:bg-white/20' }}">

            <i class="fa-solid fa-chart-line w-6 text-center"></i>

            <span x-show="!sidebarMini">
                Dashboard
            </span>

        </a>


        {{-- Users --}}

        <a href="{{ route('admin.users.index') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl transition
                {{ request()->routeIs('admin.users.*') ? 'bg-white/20 shadow-sm' : 'hover:bg-white/20' }}">

            <i class="fa-solid fa-users w-6 text-center"></i>

            <span x-show="!sidebarMini">
                Users
            </span>

        </a>


        {{-- Consumer Verifications --}}

        @php

            $pendingConsumerVerifications = \App\Models\Consumer::query()
                ->where('registration_source', 'Self Registration')
                ->where('verification_status', 'Pending Verification')
                ->count();

        @endphp

        <a href="{{ route('admin.consumer-verifications.index') }}"
            class="relative flex items-center gap-4 px-4 py-3 rounded-xl transition
                {{ request()->routeIs('admin.consumer-verifications.*') ? 'bg-white/20 shadow-sm' : 'hover:bg-white/20' }}">

            <div class="relative w-6 text-center shrink-0">

                <i class="fa-solid fa-user-check"></i>

                @if ($pendingConsumerVerifications > 0)
                    <span x-show="sidebarMini"
                        class="absolute -top-2 -right-2
                               min-w-5 h-5 px-1
                               rounded-full
                               bg-red-500 text-white
                               text-[10px] font-bold
                               flex items-center justify-center">
                        {{ $pendingConsumerVerifications > 99 ? '99+' : $pendingConsumerVerifications }}
                    </span>
                @endif

            </div>


            <span x-show="!sidebarMini" class="flex-1 flex items-center justify-between gap-3">

                <span>
                    Consumer Verifications
                </span>

                @if ($pendingConsumerVerifications > 0)
                    <span
                        class="min-w-6 h-6 px-2
                               rounded-full
                               bg-red-500 text-white
                               text-xs font-bold
                               flex items-center justify-center">
                        {{ $pendingConsumerVerifications > 99 ? '99+' : $pendingConsumerVerifications }}
                    </span>
                @endif

            </span>

        </a>


        {{-- Departments --}}

        <a href="{{ route('admin.departments.index') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl transition
                {{ request()->routeIs('admin.departments.*') ? 'bg-white/20 shadow-sm' : 'hover:bg-white/20' }}">

            <i class="fa-solid fa-building w-6 text-center"></i>

            <span x-show="!sidebarMini">
                Departments
            </span>

        </a>


        {{-- Positions --}}

        <a href="{{ route('admin.positions.index') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl transition
                {{ request()->routeIs('admin.positions.*') ? 'bg-white/20 shadow-sm' : 'hover:bg-white/20' }}">

            <i class="fa-solid fa-briefcase w-6 text-center"></i>

            <span x-show="!sidebarMini">
                Position
            </span>

        </a>


        {{-- Reports --}}

        <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-white/20 transition">

            <i class="fa-solid fa-chart-column w-6 text-center"></i>

            <span x-show="!sidebarMini">
                Reports
            </span>

        </a>


        {{-- Settings --}}

        <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-white/20 transition">

            <i class="fa-solid fa-gear w-6 text-center"></i>

            <span x-show="!sidebarMini">
                Settings
            </span>

        </a>

    </nav>

</aside>


{{-- ========================================================= --}}
{{-- MOBILE OVERLAY --}}
{{-- ========================================================= --}}

<div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>
