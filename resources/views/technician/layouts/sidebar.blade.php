@php
    $myActiveComplaintCount = \App\Models\Complaint::query()
        ->whereHas('technicians', function ($query) {
            $query->where('users.id', auth()->id());
        })
        ->whereIn('status', ['Assigned', 'In Progress'])
        ->count();

    $dashboardActive = request()->routeIs('technician.dashboard');

    $complaintsActive = request()->routeIs('technician.complaints.*')
        || request()->routeIs('technician.maintenance-reports.*');

    $reportsActive = request()->routeIs('technician.reports.*');
@endphp


<aside
    :class="[
        sidebarMini ? 'lg:w-20' : 'lg:w-72',
        sidebarOpen
            ? 'translate-x-0'
            : '-translate-x-full lg:translate-x-0'
    ]"
    class="fixed left-0 top-0 z-50
           h-screen
           bg-gradient-to-b
           from-sky-700 via-blue-700 to-cyan-700
           text-white
           transition-all duration-300
           flex flex-col"
>

    <div
        class="h-20 shrink-0
               flex items-center
               border-b border-white/10
               px-4"
    >

        <a
            href="{{ route('technician.dashboard') }}"
            class="flex items-center gap-3
                   w-full min-w-0"
        >

            <div
                class="w-10 h-10 shrink-0
                       rounded-xl
                       bg-white/15
                       flex items-center
                       justify-center"
            >

                <i class="fa-solid fa-droplet text-lg"></i>

            </div>


            <div
                x-show="!sidebarMini"
                x-transition
                class="min-w-0"
            >

                <h1 class="text-xl font-bold leading-tight">
                    iSWD
                </h1>

                <p class="text-[11px] text-sky-100 truncate mt-0.5">
                    Maintenance Technician Portal
                </p>

            </div>

        </a>

    </div>


    <div class="p-3 shrink-0 border-b border-white/10">

        <div
            class="flex items-center gap-3
                   p-3 rounded-xl
                   bg-white/10"
        >

            <img
                src="{{ auth()->user()->avatar_url }}"
                alt="User Avatar"
                class="w-11 h-11
                       rounded-xl
                       object-cover
                       ring-2 ring-white/20
                       shrink-0"
            >


            <div
                x-show="!sidebarMini"
                x-transition
                class="min-w-0"
            >

                <p class="font-semibold text-sm truncate">
                    {{ auth()->user()->full_name ?? auth()->user()->name }}
                </p>

                <p class="text-[11px] text-sky-100 mt-0.5 truncate">
                    Maintenance Technician
                </p>

            </div>

        </div>

    </div>


    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-5">

        <div>

            <p
                x-show="!sidebarMini"
                class="px-3 mb-2
                       text-[10px]
                       font-bold uppercase
                       tracking-[0.16em]
                       text-sky-200"
            >
                Overview
            </p>


            <a
                href="{{ route('technician.dashboard') }}"
                title="Dashboard"
                class="flex items-center
                       gap-3 px-3 py-2.5
                       rounded-xl transition
                       {{ $dashboardActive
                            ? 'bg-white text-sky-700 shadow-sm'
                            : 'text-white hover:bg-white/10' }}"
            >

                <div
                    class="w-8 h-8
                           rounded-lg
                           flex items-center
                           justify-center
                           shrink-0
                           {{ $dashboardActive
                                ? 'bg-sky-50'
                                : 'bg-white/10' }}"
                >

                    <i class="fa-solid fa-chart-line text-sm"></i>

                </div>


                <span
                    x-show="!sidebarMini"
                    x-transition
                    class="font-semibold text-sm"
                >
                    Dashboard
                </span>

            </a>

        </div>


        <div>

            <p
                x-show="!sidebarMini"
                class="px-3 mb-2
                       text-[10px]
                       font-bold uppercase
                       tracking-[0.16em]
                       text-sky-200"
            >
                Maintenance
            </p>


            <a
                href="{{ route('technician.complaints.index') }}"
                title="My Complaints"
                class="relative flex items-center
                       gap-3 px-3 py-2.5
                       rounded-xl transition
                       {{ $complaintsActive
                            ? 'bg-white text-sky-700 shadow-sm'
                            : 'text-white hover:bg-white/10' }}"
            >

                <div
                    class="w-8 h-8
                           rounded-lg
                           flex items-center
                           justify-center
                           shrink-0
                           {{ $complaintsActive
                                ? 'bg-sky-50'
                                : 'bg-white/10' }}"
                >

                    <i class="fa-solid fa-clipboard-check text-sm"></i>

                </div>


                <span
                    x-show="!sidebarMini"
                    x-transition
                    class="font-semibold text-sm"
                >
                    My Complaints
                </span>


                @if ($myActiveComplaintCount > 0)

                    <span
                        x-show="!sidebarMini"
                        class="ml-auto
                               min-w-[22px]
                               h-[22px]
                               px-1.5
                               rounded-full
                               bg-amber-400
                               text-amber-950
                               text-[10px]
                               font-bold
                               flex items-center
                               justify-center"
                    >

                        {{ $myActiveComplaintCount > 99
                            ? '99+'
                            : $myActiveComplaintCount }}

                    </span>


                    <span
                        x-show="sidebarMini"
                        class="absolute
                               right-1.5
                               w-2 h-2
                               rounded-full
                               bg-amber-400"
                    >
                    </span>

                @endif

            </a>

        </div>


        <div>

            <p
                x-show="!sidebarMini"
                class="px-3 mb-2
                       text-[10px]
                       font-bold uppercase
                       tracking-[0.16em]
                       text-sky-200"
            >
                Reports
            </p>


            <a
                href="{{ route('technician.reports.maintenance') }}"
                title="Work Summary"
                class="flex items-center
                       gap-3 px-3 py-2.5
                       rounded-xl transition
                       {{ $reportsActive
                            ? 'bg-white text-sky-700 shadow-sm'
                            : 'text-white hover:bg-white/10' }}"
            >

                <div
                    class="w-8 h-8
                           rounded-lg
                           flex items-center
                           justify-center
                           shrink-0
                           {{ $reportsActive
                                ? 'bg-sky-50'
                                : 'bg-white/10' }}"
                >

                    <i class="fa-solid fa-chart-column text-sm"></i>

                </div>


                <span
                    x-show="!sidebarMini"
                    x-transition
                    class="font-semibold text-sm"
                >
                    Work Summary
                </span>

            </a>

        </div>

    </nav>


    <div class="shrink-0 p-3 border-t border-white/10">

        <div class="rounded-xl bg-black/10 px-3 py-3">

            <div class="flex items-center gap-3">

                <div
                    class="w-8 h-8
                           rounded-lg
                           bg-white/10
                           flex items-center
                           justify-center
                           shrink-0"
                >

                    <i class="fa-solid fa-building text-xs"></i>

                </div>


                <div
                    x-show="!sidebarMini"
                    x-transition
                    class="min-w-0"
                >

                    <p class="text-[11px] font-semibold truncate">
                        Sagay Water District
                    </p>

                    <p class="text-[9px] text-sky-200 mt-0.5 truncate">
                        Field maintenance operations
                    </p>

                </div>

            </div>

        </div>

    </div>

</aside>


<div
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
    class="fixed inset-0 z-40
           bg-black/50
           lg:hidden"
>
</div>
