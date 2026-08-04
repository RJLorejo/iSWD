<section id="features" class="py-24 bg-slate-50">

    <div class="max-w-7xl mx-auto px-8">

        <div class="text-center mb-16">

            <h2 class="text-4xl font-bold">

                System Features

            </h2>

            <p class="text-slate-500 mt-4">

                Enterprise modules designed for the Maintenance Department.

            </p>

        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

            @php

                $features = [
                    ['Consumer Complaint', 'Secure complaint submission with map location.'],

                    ['Work Orders', 'Assign and monitor maintenance work orders.'],

                    ['Equipment Management', 'Manage pumps, valves, reservoirs, and assets.'],

                    ['Knowledge Repository', 'Store repair history, SOPs, manuals, and documents.'],

                    ['AI Recommendation', 'Suggest similar repair cases using semantic search.'],

                    ['Analytics Dashboard', 'Visualize trends, reports, and maintenance statistics.'],
                ];

            @endphp

            @foreach ($features as $feature)
                <div class="bg-white rounded-3xl shadow-lg p-8 hover:-translate-y-2 transition">

                    <h3 class="font-bold text-2xl">

                        {{ $feature[0] }}

                    </h3>

                    <p class="mt-4 text-slate-600">

                        {{ $feature[1] }}

                    </p>

                </div>
            @endforeach

        </div>

    </div>

</section>
