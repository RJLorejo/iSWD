<section class="relative overflow-hidden bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700 text-white">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -right-24 -top-32 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-24 h-96 w-96 rounded-full bg-cyan-300/10 blur-3xl"></div>

        <div
            class="absolute inset-0 opacity-[0.06]"
            style="background-image: linear-gradient(rgba(255,255,255,.8) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.8) 1px, transparent 1px); background-size: 48px 48px;"
        ></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8 lg:py-16 xl:py-20">
        <div class="grid items-center gap-10 lg:grid-cols-[1.05fr_.95fr] lg:gap-12">
            <div class="mx-auto max-w-2xl text-center lg:mx-0 lg:text-left">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-2 text-xs font-medium text-sky-50 backdrop-blur-sm">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/15">
                        <i class="fa-solid fa-droplet text-[10px]"></i>
                    </span>
                    Sagay Water District Digital Service Platform
                </div>

                <h1 class="mt-5 text-3xl font-semibold leading-[1.15] tracking-tight text-white sm:text-4xl lg:text-[2.75rem] xl:text-5xl">
                    AI-Assisted Consumer Complaint Management and Service Support
                </h1>

                <p class="mx-auto mt-5 max-w-xl text-sm font-normal leading-7 text-sky-50/90 sm:text-base lg:mx-0 lg:text-[17px]">
                    iSWD helps Sagay Water District receive, assess, route, monitor, and resolve consumer service concerns through a coordinated digital workflow supported by AI-assisted recommendations.
                </p>

                <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row lg:justify-start">
                    <a
                        href="{{ route('consumer.login') }}"
                        class="inline-flex items-center justify-center gap-2.5 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-sky-800 shadow-lg shadow-sky-950/10 transition hover:-translate-y-0.5 hover:bg-sky-50"
                    >
                        <i class="fa-solid fa-user"></i>
                        Access Consumer Portal
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>

                    <a
                        href="#workflow"
                        class="inline-flex items-center justify-center gap-2.5 rounded-xl border border-white/30 bg-white/10 px-5 py-3 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/15"
                    >
                        <i class="fa-solid fa-diagram-project"></i>
                        View Complaint Workflow
                    </a>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-3 border-t border-white/15 pt-6 sm:grid-cols-3">
                    <div class="flex items-center justify-center gap-2 text-xs font-medium text-sky-50/90 lg:justify-start">
                        <i class="fa-solid fa-circle-check text-cyan-200"></i>
                        Complaint Tracking
                    </div>

                    <div class="flex items-center justify-center gap-2 text-xs font-medium text-sky-50/90 lg:justify-start">
                        <i class="fa-solid fa-wand-magic-sparkles text-cyan-200"></i>
                        AI Assistance
                    </div>

                    <div class="flex items-center justify-center gap-2 text-xs font-medium text-sky-50/90 lg:justify-start">
                        <i class="fa-solid fa-screwdriver-wrench text-cyan-200"></i>
                        Service Coordination
                    </div>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-xl lg:mx-0 lg:ml-auto">
                <div class="absolute -inset-3 rounded-[2rem] bg-white/10 blur-xl"></div>

                <div class="relative overflow-hidden rounded-2xl border border-white/20 bg-white/10 p-2 shadow-2xl shadow-sky-950/25 backdrop-blur-sm sm:rounded-3xl sm:p-3">
                    <div class="relative overflow-hidden rounded-xl bg-slate-100 sm:rounded-2xl">
                        <img
                            src="{{ asset('images/logo/landing/hero.png') }}"
                            alt="iSWD consumer complaint management and service support platform"
                            class="h-[260px] w-full object-cover sm:h-[330px] lg:h-[360px] xl:h-[390px]"
                        >

                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/80 via-slate-950/35 to-transparent px-5 pb-5 pt-16">
                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wider text-sky-200">
                                        Integrated Service Support
                                    </p>
                                    <p class="mt-1 text-sm font-semibold text-white sm:text-base">
                                        From consumer report to service resolution
                                    </p>
                                </div>

                                <div class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/20 bg-white/15 text-white backdrop-blur sm:flex">
                                    <i class="fa-solid fa-water"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="absolute -bottom-4 -left-3 hidden items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-700 shadow-xl lg:flex">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>

                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                            Decision Support
                        </p>
                        <p class="text-xs font-semibold text-slate-700">
                            Human-reviewed AI assistance
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="relative border-t border-white/10 bg-sky-950/10">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-center gap-x-8 gap-y-3 px-4 py-4 text-xs font-medium text-sky-50/80 sm:px-6 lg:justify-between lg:px-8">
            <span class="inline-flex items-center gap-2">
                <i class="fa-solid fa-building"></i>
                Customer Service
            </span>

            <span class="inline-flex items-center gap-2">
                <i class="fa-solid fa-route"></i>
                Complaint Routing
            </span>

            <span class="inline-flex items-center gap-2">
                <i class="fa-solid fa-helmet-safety"></i>
                Field Service
            </span>

            <span class="inline-flex items-center gap-2">
                <i class="fa-solid fa-chart-line"></i>
                Monitoring & Reports
            </span>
        </div>
    </div>
</section>
