@props(['title', 'subtitle' => ''])

<div class="flex justify-between items-center mb-8">

    <div>

        <h1 class="text-3xl font-bold">

            {{ $title }}

        </h1>

        <p class="text-gray-500">

            {{ $subtitle }}

        </p>

    </div>

    <div>

        {{ $actions ?? '' }}

    </div>

</div>
