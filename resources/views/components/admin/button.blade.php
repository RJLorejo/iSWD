@props([
    'type' => 'primary',
])

@php

$classes = match($type){

    'primary' =>
        'bg-gradient-to-br from-sky-700 via-blue-700 to-cyan-600hover:bg-[#0B3C68] text-white',

    'secondary' =>
        'bg-white border border-slate-300 hover:bg-slate-100 text-slate-700',

    'success' =>
        'bg-green-600 hover:bg-green-700 text-white',

    'danger' =>
        'bg-red-600 hover:bg-red-700 text-white',

    'warning' =>
        'bg-amber-500 hover:bg-amber-600 text-white',

    default =>
        'bg-[#0F4C81] hover:bg-[#0B3C68] text-white'

};

@endphp

<button
    {{ $attributes->merge([
        'class'=>"$classes
        px-5
        py-2.5
        rounded-xl
        font-medium
        transition
        duration-300
        shadow-sm
        hover:shadow-lg"
    ]) }}>

    {{ $slot }}

</button>
