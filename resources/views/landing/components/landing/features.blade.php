<section id="features" class="scroll-mt-20 bg-white py-16 sm:py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">
                    <span class="h-px w-8 bg-sky-500"></span>
                    System Capabilities
                </div>

                <h2 class="mt-4 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
                    Core features supporting complaint and service operations
                </h2>
            </div>

            <p class="max-w-xl text-sm leading-6 text-slate-600 sm:text-base">
                Designed to support consumers and Sagay Water District personnel throughout complaint intake, assessment, service coordination, monitoring, and resolution.
            </p>
        </div>

        @php
            $features = [
                [
                    'icon' => 'fa-file-pen',
                    'title' => 'Digital Complaint Submission',
                    'description' => 'Consumers can submit and monitor water service concerns with complaint details, location information, and supporting photos.'
                ],
                [
                    'icon' => 'fa-wand-magic-sparkles',
                    'title' => 'AI-Assisted Assessment',
                    'description' => 'Provides personnel with recommendations for complaint classification, priority, urgency, and appropriate service routing.'
                ],
                [
                    'icon' => 'fa-building-circle-arrow-right',
                    'title' => 'Division-Based Routing',
                    'description' => 'Organizes verified complaints under the responsible service division for structured handling and coordination.'
                ],
                [
                    'icon' => 'fa-users-gear',
                    'title' => 'Field Service Coordination',
                    'description' => 'Supports assignment and tracking of service personnel for complaints that require field inspection or repair.'
                ],
                [
                    'icon' => 'fa-clipboard-check',
                    'title' => 'Service Accomplishment',
                    'description' => 'Records diagnosis, work performed, materials, service evidence, and accomplishment details for management review.'
                ],
                [
                    'icon' => 'fa-chart-line',
                    'title' => 'Monitoring & Reporting',
                    'description' => 'Provides dashboards and operational records for tracking complaint progress, service activity, and resolution performance.'
                ]
            ];
        @endphp

        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($features as $feature)
                <article class="group flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-6 transition duration-300 hover:-translate-y-1 hover:border-sky-200 hover:shadow-xl hover:shadow-slate-200/60">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-700 transition duration-300 group-hover:bg-sky-700 group-hover:text-white">
                        <i class="fa-solid {{ $feature['icon'] }} text-base"></i>
                    </div>

                    <h3 class="mt-5 text-base font-semibold text-slate-900">
                        {{ $feature['title'] }}
                    </h3>

                    <p class="mt-2 flex-1 text-sm leading-6 text-slate-600">
                        {{ $feature['description'] }}
                    </p>

                    <div class="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4 text-xs font-semibold text-sky-700">
                        <span>iSWD Service Support</span>
                        <i class="fa-solid fa-arrow-right-long transition-transform group-hover:translate-x-1"></i>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10 overflow-hidden rounded-2xl bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700">
            <div class="grid items-center gap-6 px-6 py-7 sm:px-8 lg:grid-cols-[1fr_auto] lg:px-10">
                <div class="flex items-start gap-4">
                    <div class="hidden h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white sm:flex">
                        <i class="fa-solid fa-droplet"></i>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-white">
                            Need to report a water service concern?
                        </h3>

                        <p class="mt-1 max-w-2xl text-sm leading-6 text-sky-100">
                            Access the Consumer Portal to submit a complaint and monitor its progress through the service workflow.
                        </p>
                    </div>
                </div>

                <a
                    href="{{ route('consumer.login') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-sky-800 shadow-sm transition hover:bg-sky-50"
                >
                    Open Consumer Portal
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</section>
