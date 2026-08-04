@props(['label', 'name', 'type' => 'text', 'required' => false])

<div class="mb-5">

    <label class="block mb-2 font-semibold text-gray-700">

        {{ $label }}

        @if ($required)
            <span class="text-red-500">*</span>
        @endif

    </label>

    <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $attributes->get('value')) }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge([
            'class' => 'w-full rounded-xl border-gray-300 focus:border-sky-500 focus:ring-sky-500',
        ]) }}>

    @error($name)
        <p class="text-red-500 text-sm mt-2">

            {{ $message }}

        </p>
    @enderror

</div>
