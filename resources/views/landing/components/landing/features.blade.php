
<!-- System Features -->
<section id="features" class="py-20 lg:py-28 bg-white">
    <div class="max-w-7xl mx-auto px-6 sm:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-slate-800">
                Core System Modules
            </h2>
            <p class="text-slate-500 mt-3 text-base sm:text-lg">
                Key features designed for consumers, customer service, and field management.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @php
                $features = [
                    ['Consumer Assistant', 'Interactive natural language intake with interactive pin-drop map support.'],
                    ['AI Triage & Priority', 'Automated recommendations for urgency, division routing, and effort estimation.'],
                    ['Smart Assignment', 'Recommends field personnel based on distance, active tasks, and work areas.'],
                    ['Work Order Tracking', 'Monitor field activity from assignment to accomplishment reporting.'],
                    ['Similar Case Detection', 'Detects duplicate or related complaints to prevent redundant field deployments.'],
                    ['Analytics & Insights', 'Visual dashboards tracking response efficiency, urgency trends, and resolution times.']
                ];
            @endphp

            @foreach ($features as $feature)
                <div class="bg-slate-50 rounded-2xl p-8 border border-slate-100 hover:shadow-lg transition duration-300">
                    <h3 class="font-bold text-xl text-slate-800">{{ $feature[0] }}</h3>
                    <p class="mt-3 text-slate-600 text-sm leading-relaxed">{{ $feature[1] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
