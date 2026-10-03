<section id="workflow" class="scroll-mt-20 bg-slate-50 py-16 sm:py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <div class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3.5 py-1.5 text-xs font-semibold uppercase tracking-[0.14em] text-sky-700">
                <i class="fa-solid fa-diagram-project"></i>
                Complaint Service Workflow
            </div>

            <h2 class="mt-4 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
                From consumer concern to service resolution
            </h2>

            <p class="mt-4 text-base leading-7 text-slate-600">
                iSWD connects consumers, Customer Service, responsible divisions, management, and field personnel through a structured complaint lifecycle.
            </p>
        </div>

        @php
            $steps = [
                [
                    'icon' => 'fa-file-circle-plus',
                    'title' => 'Complaint Submission',
                    'description' => 'The consumer submits a service concern with the relevant complaint details, location, and supporting information.'
                ],
                [
                    'icon' => 'fa-wand-magic-sparkles',
                    'title' => 'AI-Assisted Assessment',
                    'description' => 'The system analyzes complaint information and provides recommendations for classification, priority, urgency, and routing.'
                ],
                [
                    'icon' => 'fa-user-check',
                    'title' => 'Customer Service Review',
                    'description' => 'Customer Service verifies the complaint and reviews the AI recommendations before confirming the appropriate action.'
                ],
                [
                    'icon' => 'fa-route',
                    'title' => 'Division Routing & Assignment',
                    'description' => 'Verified concerns are routed to the appropriate division. Field personnel may be assigned when on-site service is required.'
                ],
                [
                    'icon' => 'fa-screwdriver-wrench',
                    'title' => 'Service & Accomplishment',
                    'description' => 'Assigned personnel perform the required service and document the work through a Service Accomplishment Report.'
                ],
                [
                    'icon' => 'fa-circle-check',
                    'title' => 'Review & Resolution',
                    'description' => 'Management reviews the accomplishment, the complaint is completed after approval, and the service case proceeds to final resolution.'
                ]
            ];
        @endphp

        <div class="relative mt-12 lg:mt-16">
            <div class="absolute left-1/2 top-7 hidden h-px w-[82%] -translate-x-1/2 bg-sky-200 lg:block"></div>

            <div class="relative grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($steps as $index => $step)
                    <div class="group relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-sky-200 hover:shadow-lg">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700 text-white shadow-sm">
                                <i class="fa-solid {{ $step['icon'] }} text-base"></i>
                            </div>

                            <span class="text-3xl font-semibold tracking-tight text-slate-100 transition group-hover:text-sky-100">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>

                        <h3 class="mt-5 text-base font-semibold text-slate-900">
                            {{ $step['title'] }}
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            {{ $step['description'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8 flex justify-center">
            <div class="inline-flex max-w-3xl items-start gap-3 rounded-xl border border-sky-100 bg-white px-5 py-4 shadow-sm">
                <i class="fa-solid fa-shield-halved mt-0.5 text-sky-700"></i>

                <p class="text-sm leading-6 text-slate-600">
                    <span class="font-semibold text-slate-800">AI-assisted, personnel-controlled:</span>
                    system recommendations support assessment and coordination, while authorized personnel remain responsible for verification, assignment, review, and approval.
                </p>
            </div>
        </div>
    </div>
</section>
