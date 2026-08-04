@props(['label', 'value'])

<div>

    <label class="block text-sm text-gray-500 mb-1">

        {{ $label }}

    </label>

    <div class="rounded-xl bg-slate-100 px-4 py-3 font-semibold">

        {{ $value ?: '-' }}

    </div>

</div>
