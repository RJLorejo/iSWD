<footer class="bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700 text-white">
    <div class="border-b border-white/10">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-14">
            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                <div class="sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-white/15 bg-white">
                            <img
                                src="{{ asset('images/logo/logo.png') }}"
                                alt="iSWD Logo"
                                class="h-9 w-9 object-contain"
                            >
                        </div>

                        <div>
                            <h2 class="text-xl font-semibold tracking-tight">
                                iSWD
                            </h2>
                            <p class="text-xs font-medium text-sky-100">
                                Sagay Water District
                            </p>
                        </div>
                    </div>

                    <p class="mt-5 max-w-sm text-sm leading-6 text-sky-100/90">
                        AI-Assisted Consumer Complaint Management and Service Support System for coordinated complaint handling, service response, monitoring, and resolution.
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-white">
                        Quick Links
                    </h3>

                    <ul class="mt-4 space-y-3">
                        <li>
                            <a href="#about" class="inline-flex items-center gap-2 text-sm text-sky-100/80 transition hover:text-white">
                                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                                About iSWD
                            </a>
                        </li>

                        <li>
                            <a href="#workflow" class="inline-flex items-center gap-2 text-sm text-sky-100/80 transition hover:text-white">
                                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                                Complaint Workflow
                            </a>
                        </li>

                        <li>
                            <a href="#features" class="inline-flex items-center gap-2 text-sm text-sky-100/80 transition hover:text-white">
                                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                                System Features
                            </a>
                        </li>

                        <li>
                            <a href="#contact" class="inline-flex items-center gap-2 text-sm text-sky-100/80 transition hover:text-white">
                                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                                Contact Information
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-white">
                        Portal Access
                    </h3>

                    <ul class="mt-4 space-y-3">
                        <li>
                            <a
                                href="{{ route('consumer.login') }}"
                                class="inline-flex items-center gap-2 text-sm text-sky-100/80 transition hover:text-white"
                            >
                                <i class="fa-solid fa-user w-4"></i>
                                Consumer Portal
                            </a>
                        </li>

                        <li>
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex items-center gap-2 text-sm text-sky-100/80 transition hover:text-white"
                            >
                                <i class="fa-solid fa-user-shield w-4"></i>
                                Employee Login
                            </a>
                        </li>
                    </ul>

                    <div class="mt-5 rounded-xl border border-white/10 bg-white/10 p-3.5">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-300 opacity-50"></span>
                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-300"></span>
                            </span>

                            <span class="text-xs font-semibold text-white">
                                Web Service Available
                            </span>
                        </div>

                        <p class="mt-1.5 text-[11px] leading-5 text-sky-100/75">
                            Portal access is available for authorized users and registered consumers.
                        </p>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-white">
                        Service Information
                    </h3>

                    <div class="mt-4 space-y-4 text-sm text-sky-100/80">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot mt-1 w-4 text-center text-sky-200"></i>
                            <span>Sagay City, Negros Occidental</span>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-clock mt-1 w-4 text-center text-sky-200"></i>
                            <span>
                                Monday – Friday<br>
                                8:00 AM – 5:00 PM
                            </span>
                        </div>

                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-droplet mt-1 w-4 text-center text-sky-200"></i>
                            <span>Consumer complaint and service support</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-sky-950/20">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-5 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <div class="text-center md:text-left">
                <p class="text-xs text-sky-100/80">
                    &copy; {{ date('Y') }} Sagay Water District. All rights reserved.
                </p>

                <p class="mt-1 text-[11px] text-sky-100/60">
                    iSWD &middot; AI-Assisted Consumer Complaint Management &amp; Service Support System
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-[11px] text-sky-100/70 md:justify-end">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-lock text-[10px]"></i>
                    Secure Access
                </span>

                <span class="inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-[10px]"></i>
                    Authorized Users
                </span>

                <span class="inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-code-branch text-[10px]"></i>
                    iSWD Platform
                </span>
            </div>
        </div>
    </div>
</footer>
