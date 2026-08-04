@props([
    'color' => 'green',
])

<span
    class="

px-3

py-1

rounded-full

text-xs

font-semibold

bg-{{ $color }}-100

text-{{ $color }}-700

">

    {{ $slot }}

</span>
