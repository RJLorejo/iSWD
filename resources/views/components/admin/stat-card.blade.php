@props([
    'title',
    'value',
    'subtitle' => null,
    'icon' => 'fa-solid fa-chart-column'
])

<div
    class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300">

    <div class="p-6 flex items-center justify-between">

        <div>

            <p class="text-sm text-slate-500">

                {{ $title }}

            </p>

            <h2 class="mt-2 text-4xl font-bold text-slate-800">

                {{ $value }}

            </h2>

            @if($subtitle)

                <p class="mt-2 text-sm text-slate-400">

                    {{ $subtitle }}

                </p>

            @endif

        </div>

        <div
            class="w-16 h-16 rounded-2xl bg-gradient-to-r from-blue-700 via-sky-600 to-cyan-500 flex items-center justify-center shadow-lg">

            <i class="{{ $icon }} text-white text-2xl"></i>

        </div>

    </div>

</div>
