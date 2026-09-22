<!-- Hero Section -->
<section class="bg-gradient-to-r from-sky-800 via-blue-800 to-cyan-800 text-white">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 py-20 lg:py-28">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="text-center lg:text-left">
                <span class="inline-block uppercase tracking-widest text-xs font-bold bg-white/10 text-sky-200 px-4 py-1.5 rounded-full border border-white/20">
                    Sagay Water District
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold mt-6 leading-tight">
                    iSWD Support Platform
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-sky-100 leading-relaxed font-normal">
                    An AI-assisted consumer complaint management and service support system that enables natural-language reporting, automated triage, and intelligent plumber assignment for optimized field operations.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('consumer.login') }}" class="inline-flex items-center justify-center gap-2 bg-white text-sky-800 font-bold px-8 py-4 rounded-xl shadow-lg hover:bg-sky-50 transition duration-300">
                        <i class="fas fa-user-circle text-lg"></i>
                        Consumer Portal
                    </a>
                    <a href="#workflow" class="inline-flex items-center justify-center gap-2 border border-white/40 text-white font-semibold px-8 py-4 rounded-xl hover:bg-white/10 transition duration-300">
                        How It Works
                    </a>
                </div>
            </div>
            <div class="w-full flex justify-center">
                <img src="{{ asset('images/logo/landing/hero.png') }}" alt="iSWD Platform" class="rounded-3xl shadow-2xl w-full max-w-lg object-cover border border-white/10">
            </div>
        </div>
    </div>
</section>
