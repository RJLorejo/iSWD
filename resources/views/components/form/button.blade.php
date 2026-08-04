<button
    {{ $attributes->merge([
        'class' =>
            'px-6 py-3 rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 text-white hover:opacity-90 transition',
    ]) }}>

    {{ $slot }}

</button>
