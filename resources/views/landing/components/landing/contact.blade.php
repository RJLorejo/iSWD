<section id="contact" class="scroll-mt-20 bg-slate-50 py-16 sm:py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/50">
            <div class="grid lg:grid-cols-[.95fr_1.05fr]">
                <div class="p-6 sm:p-8 lg:p-10 xl:p-12">
                    <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">
                        <span class="h-px w-8 bg-sky-500"></span>
                        Contact Information
                    </div>

                    <h2 class="mt-4 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
                        Sagay Water District
                    </h2>

                    <p class="mt-4 max-w-xl text-sm leading-6 text-slate-600 sm:text-base">
                        For official service concerns and consumer assistance, contact Sagay Water District during regular office hours or use the iSWD Consumer Portal.
                    </p>

                    <div class="mt-8 space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    Office Location
                                </p>
                                <p class="mt-1 text-sm font-medium text-slate-700">
                                    Sagay City, Negros Occidental
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <i class="fa-solid fa-envelope"></i>
                            </div>

                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    Email
                                </p>

                                <a
                                    href="mailto:info@sagaywaterdistrict.gov.ph"
                                    class="mt-1 block break-all text-sm font-medium text-sky-700 transition hover:text-sky-800"
                                >
                                    info@sagaywaterdistrict.gov.ph
                                </a>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <i class="fa-solid fa-clock"></i>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    Office Hours
                                </p>
                                <p class="mt-1 text-sm font-medium text-slate-700">
                                    Monday to Friday, 8:00 AM – 5:00 PM
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a
                            href="{{ route('consumer.login') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-800"
                        >
                            <i class="fa-solid fa-user"></i>
                            Consumer Portal
                        </a>

                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700"
                        >
                            <i class="fa-solid fa-user-shield"></i>
                            Employee Access
                        </a>
                    </div>
                </div>

                <div class="relative min-h-[320px] overflow-hidden bg-slate-100 sm:min-h-[400px] lg:min-h-full">
                    <img
                        src="{{ asset('images/logo/landing/contact.png') }}"
                        alt="Sagay Water District office"
                        class="absolute inset-0 h-full w-full object-cover"
                    >

                    <div class="absolute inset-0 bg-gradient-to-t from-sky-950/70 via-sky-950/10 to-transparent"></div>

                    <div class="absolute inset-x-0 bottom-0 p-6 sm:p-8">
                        <div class="max-w-md rounded-2xl border border-white/20 bg-sky-950/40 p-5 text-white backdrop-blur-md">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15">
                                    <i class="fa-solid fa-headset"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-semibold">
                                        Consumer Service Support
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-sky-100">
                                        Submit service concerns through iSWD for organized tracking and coordinated response.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
