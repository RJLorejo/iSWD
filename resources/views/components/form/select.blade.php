@props(['label', 'name', 'options' => [], 'selected' => '', 'required' => false])

<div class="mb-5">

    <label class="block mb-2 font-semibold">
        {{ $label }}

        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    <select name="{{ $name }}" id="{{ $name }}" {{ $required ? 'required' : '' }}
        {{ $attributes->merge([
            'class' => 'w-full rounded-xl border-gray-300 focus:ring-sky-500 focus:border-sky-500',
        ]) }}>

        <option value="">
            Select {{ $label }}
        </option>

        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected(old($name, $selected) == $value)>

                {{ $text }}

            </option>
        @endforeach

    </select>

    @error($name)
        <p class="mt-2 text-sm text-red-600">

            {{ $message }}

        </p>
    @enderror

</div>
