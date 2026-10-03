<aside
    :class="[
        sidebarMini ? 'lg:w-20' : 'lg:w-72',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col overflow-hidden bg-gradient-to-b from-sky-800 via-blue-800 to-cyan-800 text-white shadow-xl transition-all duration-300 ease-in-out">

    <div
        class="flex h-16 shrink-0 items-center border-b border-white/10 px-4 lg:h-[72px]">

        <div
            class="flex min-w-0 flex-1 items-center"
            :class="sidebarMini ? 'lg:justify-center' : 'gap-3'">

            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/10">

                <i class="fas fa-droplet text-lg text-white"></i>

            </div>

            <div
                x-show="!sidebarMini"
                x-transition.opacity
                class="min-w-0">

                <p class="text-lg font-bold leading-tight">
                    iSWD
                </p>

                <p class="mt-0.5 truncate text-[11px] font-medium text-sky-200">
                    Consumer Portal
                </p>

            </div>

        </div>

        <button
            type="button"
            @click="sidebarOpen = false"
            class="flex h-9 w-9 items-center justify-center rounded-xl text-sky-100 transition hover:bg-white/10 lg:hidden"
            aria-label="Close navigation">

            <i class="fas fa-xmark"></i>

        </button>

    </div>

    <div
        class="border-b border-white/10 p-4"
        :class="sidebarMini ? 'lg:px-3' : ''">

        <div
            class="flex items-center rounded-2xl bg-white/[0.07] p-3 ring-1 ring-white/10"
            :class="sidebarMini ? 'lg:justify-center lg:p-2' : 'gap-3'">

            <img
                class="h-10 w-10 shrink-0 rounded-xl object-cover ring-2 ring-white/20"
                src="{{ auth()->user()->avatar_url }}"
                alt="{{ auth()->user()->full_name ?? auth()->user()->name }}">

            <div
                x-show="!sidebarMini"
                x-transition.opacity
                class="min-w-0">

                <p class="truncate text-sm font-semibold text-white">
                    {{ auth()->user()->full_name ?? auth()->user()->name }}
                </p>

                <div class="mt-1 flex items-center gap-1.5">

                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>

                    <p class="text-[11px] font-medium text-sky-200">
                        Consumer Account
                    </p>

                </div>

            </div>

        </div>

    </div>

    <div
        class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-5">

        <div
            x-show="!sidebarMini"
            x-transition.opacity
            class="mb-2 px-3">

            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-sky-300">
                Main Menu
            </p>

        </div>

        <nav class="space-y-1.5">

            <a
                href="{{ route('consumer.dashboard') }}"
                @click="sidebarOpen = false"
                title="Dashboard"
                class="group relative flex items-center rounded-xl px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('consumer.dashboard')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-50 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : 'gap-3'">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('consumer.dashboard')
                        ? 'bg-sky-50 text-sky-700'
                        : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-house text-sm"></i>

                </span>

                <span
                    x-show="!sidebarMini"
                    x-transition.opacity
                    class="whitespace-nowrap">
                    Dashboard
                </span>

                @if (request()->routeIs('consumer.dashboard'))

                    <span
                        x-show="!sidebarMini"
                        class="ml-auto h-1.5 w-1.5 rounded-full bg-sky-600">
                    </span>

                @endif

            </a>

            <a
                href="{{ route('consumer.complaints.create') }}"
                @click="sidebarOpen = false"
                title="Submit Complaint"
                class="group relative flex items-center rounded-xl px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('consumer.complaints.create')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-50 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : 'gap-3'">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('consumer.complaints.create')
                        ? 'bg-sky-50 text-sky-700'
                        : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-file-circle-plus text-sm"></i>

                </span>

                <span
                    x-show="!sidebarMini"
                    x-transition.opacity
                    class="whitespace-nowrap">
                    Submit Complaint
                </span>

                @if (request()->routeIs('consumer.complaints.create'))

                    <span
                        x-show="!sidebarMini"
                        class="ml-auto h-1.5 w-1.5 rounded-full bg-sky-600">
                    </span>

                @endif

            </a>

            <a
                href="{{ route('consumer.complaints.index') }}"
                @click="sidebarOpen = false"
                title="My Complaints"
                class="group relative flex items-center rounded-xl px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('consumer.complaints.index') || request()->routeIs('consumer.complaints.show') || request()->routeIs('consumer.complaints.edit')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-50 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : 'gap-3'">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('consumer.complaints.index') || request()->routeIs('consumer.complaints.show') || request()->routeIs('consumer.complaints.edit')
                        ? 'bg-sky-50 text-sky-700'
                        : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-file-lines text-sm"></i>

                </span>

                <span
                    x-show="!sidebarMini"
                    x-transition.opacity
                    class="whitespace-nowrap">
                    My Complaints
                </span>

                @if (
                    request()->routeIs('consumer.complaints.index') ||
                    request()->routeIs('consumer.complaints.show') ||
                    request()->routeIs('consumer.complaints.edit')
                )

                    <span
                        x-show="!sidebarMini"
                        class="ml-auto h-1.5 w-1.5 rounded-full bg-sky-600">
                    </span>

                @endif

            </a>

            <a
                href="{{ route('consumer.announcements.index') }}"
                @click="sidebarOpen = false"
                title="Service Announcements"
                class="group relative flex items-center rounded-xl px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('consumer.announcements.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-50 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : 'gap-3'">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('consumer.announcements.*')
                        ? 'bg-sky-50 text-sky-700'
                        : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-bullhorn text-sm"></i>

                </span>

                <span
                    x-show="!sidebarMini"
                    x-transition.opacity
                    class="whitespace-nowrap">
                    Announcements
                </span>

                @if (request()->routeIs('consumer.announcements.*'))

                    <span
                        x-show="!sidebarMini"
                        class="ml-auto h-1.5 w-1.5 rounded-full bg-sky-600">
                    </span>

                @endif

            </a>

        </nav>

        {{-- <div
            x-show="!sidebarMini"
            x-transition.opacity
            class="mb-2 mt-7 px-3">

            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-sky-300">
                Assistance
            </p>

        </div>

        <nav class="space-y-1.5">

            <a
                href="{{ route('consumer.ai.index') }}"
                @click="sidebarOpen = false"
                title="AI Water Service Assistant"
                class="group relative flex items-center rounded-xl px-3 py-2.5 text-sm font-medium transition
                {{ request()->routeIs('consumer.ai.*')
                    ? 'bg-white text-sky-800 shadow-sm'
                    : 'text-sky-50 hover:bg-white/10 hover:text-white' }}"
                :class="sidebarMini ? 'lg:justify-center' : 'gap-3'">

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg
                    {{ request()->routeIs('consumer.ai.*')
                        ? 'bg-sky-50 text-sky-700'
                        : 'text-sky-200 group-hover:text-white' }}">

                    <i class="fas fa-robot text-sm"></i>

                </span>

                <span
                    x-show="!sidebarMini"
                    x-transition.opacity
                    class="whitespace-nowrap">
                    AI Assistant
                </span>

                @if (request()->routeIs('consumer.ai.*'))

                    <span
                        x-show="!sidebarMini"
                        class="ml-auto h-1.5 w-1.5 rounded-full bg-sky-600">
                    </span>

                @endif

            </a>

        </nav> --}}

    </div>

    <div
        class="shrink-0 border-t border-white/10 p-3"
        :class="sidebarMini ? 'lg:px-2' : ''">

        <div
            class="rounded-2xl bg-black/10 p-3"
            :class="sidebarMini ? 'lg:flex lg:justify-center lg:bg-transparent lg:p-1' : ''">

            <div
                class="flex items-start"
                :class="sidebarMini ? 'lg:justify-center' : 'gap-3'">

                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-sky-100">

                    <i class="fas fa-shield-halved text-xs"></i>

                </div>

                <div
                    x-show="!sidebarMini"
                    x-transition.opacity>

                    <p class="text-xs font-semibold text-white">
                        Secure Consumer Portal
                    </p>

                    <p class="mt-1 text-[10px] leading-4 text-sky-200">
                        Sagay Water District
                    </p>

                </div>

            </div>

        </div>

    </div>

</aside>

<div
    x-cloak
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-[1px] lg:hidden">
</div>
