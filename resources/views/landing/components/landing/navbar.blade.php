<nav x-data="{ mobileMenuOpen: false }" @keydown.escape.window="mobileMenuOpen = false"
    class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 shadow-sm backdrop-blur-xl">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between lg:h-[72px]">

            {{-- Brand --}}
            <a href="/"
                class="flex min-w-0 items-center gap-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">

                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 ring-1 ring-sky-100 sm:h-11 sm:w-11">
                    <img src="{{ asset('images/logo/logo.png') }}" alt="iSWD Logo"
                        class="h-8 w-8 object-contain sm:h-9 sm:w-9">
                </div>

                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-extrabold tracking-tight text-sky-800">
                            iSWD
                        </span>

                        <span
                            class="hidden rounded-full bg-sky-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-sky-700 sm:inline-flex">
                            Service Support
                        </span>
                    </div>

                    <p
                        class="truncate text-[10px] font-medium uppercase tracking-[0.12em] text-slate-500 sm:text-[11px]">
                        Sagay Water District
                    </p>
                </div>
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden items-center gap-1 lg:flex">

                <a href="#about"
                    class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-sky-50 hover:text-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    About
                </a>

                <a href="#workflow"
                    class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-sky-50 hover:text-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    Workflow
                </a>

                <a href="#features"
                    class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-sky-50 hover:text-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    Features
                </a>

                <a href="#water-application"
                    class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-sky-50 hover:text-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    Water Services
                </a>

                <a href="#contact"
                    class="inline-flex items-center gap-2 rounded-lg bg-sky-50 px-3 py-2.5 text-sm font-bold text-sky-800 transition hover:bg-sky-100 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <i class="fa-solid fa-location-dot text-sky-600"></i>
                    Contact & Location
                </a>
            </div>

            {{-- Desktop Portal Buttons + Mobile Toggle --}}
            <div class="flex shrink-0 items-center gap-2">

                <a href="{{ route('consumer.login') }}"
                    class="hidden items-center gap-2 rounded-xl border border-sky-200 bg-white px-3.5 py-2.5 text-sm font-bold text-sky-700 transition hover:border-sky-300 hover:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-sky-500 sm:inline-flex">
                    <i class="fa-solid fa-user text-xs"></i>
                    Consumer
                </a>

                <a href="{{ route('login') }}"
                    class="hidden items-center gap-2 rounded-xl bg-sky-700 px-3.5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 sm:inline-flex">
                    <i class="fa-solid fa-user-shield text-xs"></i>
                    Staff Login
                </a>

                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen"
                    :aria-expanded="mobileMenuOpen.toString()" aria-controls="mobile-navigation"
                    :aria-label="mobileMenuOpen ? 'Close navigation menu' : 'Open navigation menu'"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 lg:hidden">

                    <i class="fa-solid text-lg" :class="mobileMenuOpen ? 'fa-xmark' : 'fa-bars'" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Navigation --}}
    <div id="mobile-navigation" x-cloak x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2" @click.outside="mobileMenuOpen = false"
        class="border-t border-slate-100 bg-white shadow-lg lg:hidden">

        <div class="mx-auto max-w-7xl space-y-1 px-4 py-4 sm:px-6">

            <a href="#about" @click="mobileMenuOpen = false"
                class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
                <i class="fa-solid fa-circle-info w-5 text-center text-sky-600"></i>
                About iSWD
            </a>

            <a href="#workflow" @click="mobileMenuOpen = false"
                class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
                <i class="fa-solid fa-diagram-project w-5 text-center text-sky-600"></i>
                Complaint Workflow
            </a>

            <a href="#features" @click="mobileMenuOpen = false"
                class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
                <i class="fa-solid fa-layer-group w-5 text-center text-sky-600"></i>
                System Features
            </a>

            <a href="#water-application" @click="mobileMenuOpen = false"
                class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
                <i class="fa-solid fa-file-lines w-5 text-center text-sky-600"></i>
                Water Service Information
            </a>

            <a href="#contact" @click="mobileMenuOpen = false"
                class="flex items-center gap-3 rounded-xl bg-sky-50 px-4 py-3 text-sm font-bold text-sky-800 transition hover:bg-sky-100">
                <i class="fa-solid fa-map-location-dot w-5 text-center text-sky-600"></i>
                Contact & Location
            </a>

            {{-- Mobile Portal Buttons --}}
            <div class="mt-3 grid gap-2 border-t border-slate-100 pt-4 sm:grid-cols-2">

                <a href="{{ route('consumer.login') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-white px-4 py-3 text-sm font-bold text-sky-700 transition hover:bg-sky-50">
                    <i class="fa-solid fa-user"></i>
                    Consumer Portal
                </a>

                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-sky-800">
                    <i class="fa-solid fa-user-shield"></i>
                    Staff Login
                </a>
            </div>
        </div>
    </div>
</nav>
