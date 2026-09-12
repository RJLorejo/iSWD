<aside
    :class="[
        sidebarMini ? 'lg:w-20' : 'lg:w-72',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
    class="fixed left-0 top-0 z-50 h-screen bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700 text-white transition-all duration-300">

    <div class="h-20 flex items-center justify-center border-b border-sky-600">

        <div x-show="!sidebarMini">

            <h1 class="text-2xl font-bold text-center">

                iSWD

            </h1>

            <p class="text-xs text-sky-200 text-center">

                Manager Panel

            </p>

        </div>

        <i x-show="sidebarMini" class="fa-solid fa-droplet text-3xl"></i>

    </div>

    <div class="p-5 border-b border-sky-600">

        <div class="flex items-center gap-3">

            <img class="w-12 h-12 rounded-full" src="{{ auth()->user()->avatar_url }}" alt="User Avatar">

            <div x-show="!sidebarMini">

                <div class="font-semibold">

                    {{ auth()->user()->name }}

                </div>

                <div class="text-xs text-sky-200">

                    Maintenance Manager

                </div>

            </div>

        </div>

    </div>

    <nav class="mt-5 px-3 space-y-2">

        <a href="{{ route('maintenance-manager.dashboard') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-white/20">

            <i class="fa-solid fa-chart-line w-6"></i>

            <span x-show="!sidebarMini">Dashboard</span>

        </a>

        <a href="{{ route('maintenance-manager.complaints.index') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl
           hover:bg-white/20 transition">

            <i class="fa-solid fa-circle-exclamation w-6"></i>

            <span>
                Complaints
            </span>

            @php
                $isVerified = \App\Models\Complaint::where('status', 'Verified')->count();
            @endphp

            @if ($isVerified > 0)
                <span class="ml-auto px-2 py-0.5 text-xs rounded-full
                     bg-amber-100 text-amber-700">

                    {{ $isVerified }}

                </span>
            @endif

        </a>

        <a href="{{ route('maintenance-manager.maintenance-reviews.index') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl
           hover:bg-white/20 transition">
            <i class="fas fa-clipboard-check w-5"></i>

            <span>
                Maintenance Reviews
            </span>

            @php
                $pendingReviews = \App\Models\MaintenanceReport::where('review_status', 'Pending Review')->count();
            @endphp

            @if ($pendingReviews > 0)
                <span class="ml-auto px-2 py-0.5 text-xs rounded-full
                     bg-amber-100 text-amber-700">

                    {{ $pendingReviews }}

                </span>
            @endif

        </a>


        <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-white/20">

            <i class="fa-solid fa-screwdriver-wrench w-6"></i>

            <span x-show="!sidebarMini">Equipment</span>

        </a>

        <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-white/20">

            <i class="fa-solid fa-chart-column w-6"></i>

            <span x-show="!sidebarMini">Reports</span>

        </a>

        <a href="#" class="flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-white/20">

            <i class="fa-solid fa-gear w-6"></i>

            <span x-show="!sidebarMini">Settings</span>

        </a>

    </nav>

</aside>

<div x-show="sidebarOpen" @click="sidebarOpen=false" class="fixed inset-0 bg-black/50 lg:hidden">
</div>
