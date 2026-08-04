@props(['label', 'name'])

<div class="mb-5">

    <label class="block mb-2 font-semibold">

        {{ $label }}

    </label>

    <textarea name="{{ $name }}" rows="4"
        {{ $attributes->merge([
            'class' => 'w-full rounded-xl border-gray-300 focus:ring-sky-500',
        ]) }}>{{ old($name) }}</textarea>

    @error($name)
        <p class="text-red-500 text-sm mt-2">

            {{ $message }}

        </p>
    @enderror

</div>
