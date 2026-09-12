<aside
    :class="[
        sidebarMini ? 'lg:w-20' : 'lg:w-72',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
    class="fixed left-0 top-0 z-50 h-screen
           bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700
           text-white transition-all duration-300">

    {{-- ========================================================= --}}
    {{-- BRAND --}}
    {{-- ========================================================= --}}

    <div class="h-20 flex items-center justify-center
                border-b border-sky-600">

        <div x-show="!sidebarMini" class="text-center">

            <h1 class="text-2xl font-bold">
                iSWD
            </h1>

            <p class="text-xs text-sky-200 mt-1">
                Maintenance Panel
            </p>

        </div>

        <i x-show="sidebarMini" class="fa-solid fa-droplet text-3xl">
        </i>

    </div>


    {{-- ========================================================= --}}
    {{-- USER PROFILE --}}
    {{-- ========================================================= --}}

    <div class="p-5 border-b border-sky-600">

        <div class="flex items-center gap-3">

            <img src="{{ auth()->user()->avatar_url }}" alt="User Avatar"
                class="w-11 h-11 rounded-full object-cover
                       ring-2 ring-white/20">

            <div x-show="!sidebarMini" class="min-w-0">

                <div class="font-semibold truncate">

                    {{ auth()->user()->name }}

                </div>

                <div class="text-xs text-sky-200 mt-0.5">

                    Maintenance Technician

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- NAVIGATION --}}
    {{-- ========================================================= --}}

    <nav class="mt-5 px-3 space-y-1.5">

        {{-- Dashboard --}}
        <a href="{{ route('technician.dashboard') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl
                   hover:bg-white/20 transition">

            <i class="fa-solid fa-chart-line w-6"></i>

            <span x-show="!sidebarMini">
                Dashboard
            </span>

        </a>


        {{-- My Work Orders --}}
        <a href="#"
            class="flex items-center gap-4 px-4 py-3 rounded-xl
                   hover:bg-white/20 transition">

            <i class="fa-solid fa-clipboard-list w-6"></i>

            <span x-show="!sidebarMini">
                My Work Orders
            </span>

        </a>


        {{-- Assigned Complaints --}}
        <a href="{{ route('technician.complaints.index') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl
                   hover:bg-white/20 transition">

            <i class="fa-solid fa-clipboard-check w-6"></i>

            <span x-show="!sidebarMini">
                My Complaints
            </span>

            @php
                $isReturned = \App\Models\MaintenanceReport::where('review_status', 'Returned')->count();
            @endphp

            @if ($isReturned > 0)
                <span class="ml-auto px-2 py-0.5 text-xs rounded-full
                     bg-amber-100 text-amber-700">

                    {{ $isReturned }}

                </span>
            @endif
            @php
                $isAssigned = \App\Models\Complaint::where('status', 'Assigned')->count();
            @endphp

            @if ($isAssigned > 0)
                <span class="ml-auto px-2 py-0.5 text-xs rounded-full
                     bg-amber-100 text-amber-700">

                    {{ $isAssigned }}

                </span>
            @endif

        </a>


        {{-- Maintenance Tasks --}}
        <a href="#"
            class="flex items-center gap-4 px-4 py-3 rounded-xl
                   hover:bg-white/20 transition">

            <i class="fa-solid fa-screwdriver-wrench w-6"></i>

            <span x-show="!sidebarMini">
                Maintenance Tasks
            </span>

        </a>


        {{-- Knowledge Base --}}
        <a href="#"
            class="flex items-center gap-4 px-4 py-3 rounded-xl
                   hover:bg-white/20 transition">

            <i class="fa-solid fa-book-open w-6"></i>

            <span x-show="!sidebarMini">
                Knowledge Base
            </span>

        </a>


        {{-- Maintenance History --}}
        <a href="{{ route('technician.maintenance-history.index') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl
                   hover:bg-white/20 transition">

            <i class="fa-solid fa-clock-rotate-left w-6"></i>

            <span x-show="!sidebarMini">
                Maintenance History
            </span>

        </a>


        {{-- Reports --}}
        <a href="{{ route('technician.reports.maintenance') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl
                   hover:bg-white/20 transition">

            <i class="fa-solid fa-chart-column w-6"></i>

            <span x-show="!sidebarMini">
                Maintenance Reports
            </span>

        </a>


        {{-- Divider --}}
        <div class="my-4 border-t border-white/10"></div>


        {{-- Profile --}}
        <a href="{{ route('profile.show') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl
                   hover:bg-white/20 transition">

            <i class="fa-solid fa-user w-6"></i>

            <span x-show="!sidebarMini">
                My Profile
            </span>

        </a>

    </nav>

</aside>


{{-- Mobile Overlay --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden">
</div>
