<!-- Add this inside <head> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
<nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-sm" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">

            <!-- Brand Logo & Title -->
            <a href="/" class="flex items-center gap-3 text-decoration-none group">
                <img src="{{ asset('images/logo/logo.png') }}" alt="iSWD Logo"
                    class="w-11 h-11 object-contain transition group-hover:scale-105">

                <div>
                    <h1 class="text-2xl font-black text-sky-800 tracking-tight leading-none">
                        iSWD
                    </h1>
                    <p class="text-xs font-semibold text-slate-500 tracking-wider uppercase mt-1">
                        Sagay Water District
                    </p>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <div class="hidden md:flex items-center gap-8">
                <a href="#about"
                    class="text-slate-600 font-medium hover:text-sky-700 transition text-decoration-none text-sm">
                    About
                </a>
                <a href="#workflow"
                    class="text-slate-600 font-medium hover:text-sky-700 transition text-decoration-none text-sm">
                    Workflow
                </a>
                <a href="#features"
                    class="text-slate-600 font-medium hover:text-sky-700 transition text-decoration-none text-sm">
                    Features
                </a>
                <a href="#contact"
                    class="text-slate-600 font-medium hover:text-sky-700 transition text-decoration-none text-sm">
                    Contact
                </a>
            </div>

            <!-- Action Buttons & Mobile Toggle -->
            <div class="flex items-center gap-3">
                <!-- Staff / Employee Login -->
                <a href="{{ route('login') }}"
                    class="hidden sm:inline-flex items-center gap-2 bg-sky-800 text-white text-decoration-none px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-sky-900 shadow-sm hover:shadow transition duration-200">
                    <i class="fas fa-user-shield text-xs"></i>
                    Staff Login
                </a>

                <!-- Mobile Hamburger Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                    class="md:hidden inline-flex items-center justify-center p-2.5 rounded-xl text-slate-700 hover:text-sky-800 hover:bg-slate-100 transition"
                    aria-label="Toggle Navigation Menu">

                    <!-- Close Icon (shown when menu is open) -->
                    <svg x-show="mobileMenuOpen" class="w-7 h-7" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>

                    <!-- Hamburger Icon (shown when menu is closed) -->
                    <svg x-show="!mobileMenuOpen" class="w-7 h-7" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>

                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2" @click.away="mobileMenuOpen = false"
        class="md:hidden bg-white border-b border-slate-100 shadow-lg px-6 pt-2 pb-6 space-y-3">

        <a href="#about" @click="mobileMenuOpen = false"
            class="block py-2 text-slate-700 font-medium hover:text-sky-700 text-decoration-none">
            About
        </a>
        <a href="#workflow" @click="mobileMenuOpen = false"
            class="block py-2 text-slate-700 font-medium hover:text-sky-700 text-decoration-none">
            Workflow
        </a>
        <a href="#features" @click="mobileMenuOpen = false"
            class="block py-2 text-slate-700 font-medium hover:text-sky-700 text-decoration-none">
            Features
        </a>
        <a href="#contact" @click="mobileMenuOpen = false"
            class="block py-2 text-slate-700 font-medium hover:text-sky-700 text-decoration-none">
            Contact
        </a>

        <div class="pt-2 border-t border-slate-100 flex flex-col gap-2">
            <a href="{{ route('login') }}"
                class="flex items-center justify-center gap-2 bg-sky-800 text-white font-semibold py-3 rounded-xl text-sm shadow-sm">
                <i class="fas fa-user-shield text-xs"></i>
                Staff Login
            </a>
            <a href="{{ route('consumer.login') }}"
                class="flex items-center justify-center gap-2 bg-slate-100 text-sky-800 font-semibold py-3 rounded-xl text-sm">
                <i class="fas fa-user text-xs"></i>
                Consumer Portal
            </a>
        </div>
    </div>
</nav>
