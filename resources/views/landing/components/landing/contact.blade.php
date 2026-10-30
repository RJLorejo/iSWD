
{{-- resources/views/landing/components/landing/contact.blade.php --}}

<section id="contact" class="relative overflow-hidden bg-slate-50 py-16 sm:py-20 lg:py-24">
    <div class="pointer-events-none absolute -left-32 top-20 h-72 w-72 rounded-full bg-cyan-100/60 blur-3xl"></div>
    <div class="pointer-events-none absolute -right-32 bottom-10 h-72 w-72 rounded-full bg-blue-100/60 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Section heading --}}
        <div class="mx-auto mb-12 max-w-3xl text-center">
            <span class="inline-flex items-center gap-2 rounded-full border border-cyan-200 bg-white px-4 py-2 text-sm font-semibold text-cyan-700 shadow-sm">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 1 1 18 0Z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                Contact & Location
            </span>

            <h2 class="mt-5 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">
                We're Here to <span class="text-cyan-600">Help You</span>
            </h2>

            <p class="mt-4 text-base leading-7 text-slate-600 sm:text-lg">
                Need assistance with a water service concern? Contact Sagay Water District
                or visit our office for assistance.
            </p>
        </div>

        {{-- Contact and map cards --}}
        <div class="grid grid-cols-1 items-stretch gap-7 lg:grid-cols-2 lg:gap-8">

            {{-- LEFT: Office information --}}
            <div class="flex min-w-0 flex-col rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8 lg:p-9">

                <div class="mb-7 flex items-start gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6M9 9h.01M15 9h.01M9 12h.01M15 12h.01"/>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <h3 class="text-2xl font-bold text-slate-900">Sagay Water District</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Official office contact information
                        </p>
                    </div>
                </div>

                <div class="space-y-6">

                    {{-- Address --}}
                    <div class="flex min-w-0 items-start gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/>
                                <circle cx="12" cy="10" r="2.5"/>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900">Office Address</p>
                            <p class="mt-1 break-words text-sm leading-6 text-slate-600">
                                Corner Ma. Lopez and A.E. Marañon Streets,<br class="hidden sm:block">
                                Poblacion II, Sagay City,<br class="hidden sm:block">
                                Negros Occidental 6122, Philippines
                            </p>
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div class="flex min-w-0 items-start gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 11.2 18a19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 3.18 2 2 0 0 1 4.07 1h3a2 2 0 0 1 2 1.72c.12.96.36 1.9.7 2.8a2 2 0 0 1-.45 2.11L8.05 8.9a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.84.58 2.8.7A2 2 0 0 1 22 16.92Z"/>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900">Telephone</p>
                            <a href="tel:+63344880219"
                               class="mt-1 inline-block text-sm font-medium text-cyan-700 transition hover:text-cyan-900 hover:underline">
                                (034) 488-0219
                            </a>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="flex min-w-0 items-start gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path d="m3 7 9 6 9-6"/>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900">Email Address</p>
                            <a href="mailto:sagaywd@yahoo.com"
                               class="mt-1 inline-block max-w-full break-all text-sm font-medium text-cyan-700 transition hover:text-cyan-900 hover:underline">
                                sagaywd@yahoo.com
                            </a>
                        </div>
                    </div>

                    {{-- Office hours --}}
                    <div class="flex min-w-0 items-start gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v5l3 2"/>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-900">Office Hours</p>
                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                Monday – Friday<br>
                                8:00 AM – 5:00 PM
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Buttons stay in their own row to prevent overlapping the heading --}}
                <div class="mt-8 flex flex-col gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:flex-wrap">
                    <a href="https://www.google.com/maps/dir/?api=1&destination=Sagay+Water+District%2C+Maria+Lopez+corner+AE+Mara%C3%B1on+Street%2C+Sagay+City%2C+Negros+Occidental"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-cyan-600 px-5 py-3 text-center text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 sm:w-auto">

                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 21s7-5.5 7-12a7 7 0 1 0-14 0c0 6.5 7 12 7 12Z"/>
                            <circle cx="12" cy="9" r="2.5"/>
                        </svg>

                        Open in Google Maps
                    </a>

                    <a href="https://sagaywd.gov.ph/"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700 transition hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-800 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 sm:w-auto">

                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/>
                        </svg>

                        Official Website
                    </a>
                </div>

                <p class="mt-5 text-xs leading-5 text-slate-500">
                    For urgent concerns or other service requests, please contact the office directly.
                </p>
            </div>

            {{-- RIGHT: Map --}}
            <div class="flex min-w-0 flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                {{-- Map heading is separate from the map and its buttons --}}
                <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-start sm:justify-between sm:p-8">
                    <div class="flex min-w-0 items-start gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M3 6.5 8.5 4 15.5 7l5.5-2.5v13L15.5 20l-7-3-5.5 2.5v-13Z"/>
                                <path d="M8.5 4v13M15.5 7v13"/>
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <h3 class="text-xl font-bold text-slate-900">Find Our Office</h3>
                            <p class="mt-1 break-words text-sm leading-6 text-slate-500">
                                Sagay City, Negros Occidental
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Responsive map container --}}
                <div class="relative min-w-0 flex-1 px-4 pb-4 sm:px-6 sm:pb-6 lg:px-8 lg:pb-8">
                    <div class="h-72 min-w-0 overflow-hidden rounded-2xl bg-slate-100 sm:h-80 lg:h-full lg:min-h-[430px]">
                        <iframe
                            title="Map showing Sagay Water District office in Sagay City, Negros Occidental"
                            src="https://maps.google.com/maps?q=Sagay%20Water%20District%2C%20Maria%20Lopez%20corner%20AE%20Mara%C3%B1on%20Street%2C%20Sagay%20City%2C%20Negros%20Occidental&t=&z=16&ie=UTF8&iwloc=&output=embed"
                            class="h-full w-full border-0"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen>
                        </iframe>
                    </div>

                    <div class="mt-4 flex items-start gap-3 rounded-xl bg-slate-50 p-4">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-cyan-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 11v5M12 7h.01"/>
                        </svg>

                        <p class="text-sm leading-6 text-slate-600">
                            Use the map to view the office location. For turn-by-turn directions,
                            select <span class="font-semibold text-slate-800">Open in Google Maps</span>.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Consumer portal note --}}
        <div class="mt-8 rounded-2xl border border-cyan-100 bg-cyan-50/70 p-5 sm:p-6">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-6 w-6 shrink-0 text-cyan-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 11v5M12 7h.01"/>
                </svg>

                <div class="min-w-0">
                    <h4 class="font-semibold text-slate-900">About the iSWD Consumer Portal</h4>
                    <p class="mt-1 text-sm leading-6 text-slate-600">
                        The consumer portal supports water service complaints and related concerns.
                        New water connection applications are not processed through this portal.
                        Please contact or visit Sagay Water District for guidance on new connections.
                    </p>

                    <a href="{{ route('consumer.login') }}"
                       class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-cyan-800 transition hover:text-cyan-950 hover:underline">
                        Go to Consumer Portal
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M13 6l6 6-6 6"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>
