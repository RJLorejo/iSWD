@props(['label', 'value'])

<div class="flex justify-between items-center border-b pb-3">

    <div class="font-semibold text-gray-600">

        {{ $label }}

    </div>

    <div class="text-right text-slate-700">

        {{ $value }}

    </div>

</div>
