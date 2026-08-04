<section id="workflow" class="py-24 bg-white">

    <div class="max-w-7xl mx-auto px-8">

        <h2 class="text-center text-4xl font-bold mb-16">

            System Workflow

        </h2>

        <div class="grid lg:grid-cols-9 gap-4 text-center">

            @php

                $steps = [
                    'Consumer',

                    'Complaint',

                    'Verification',

                    'Work Order',

                    'Technician',

                    'AI',

                    'Repair',

                    'Knowledge',

                    'Report',
                ];

            @endphp

            @foreach ($steps as $step)
                <div>

                    <div
                        class="rounded-full w-20 h-20 bg-sky-700 text-white flex items-center justify-center mx-auto font-bold">

                        {{ $loop->iteration }}

                    </div>

                    <p class="mt-4 font-semibold">

                        {{ $step }}

                    </p>

                </div>
            @endforeach

        </div>

    </div>

</section>
