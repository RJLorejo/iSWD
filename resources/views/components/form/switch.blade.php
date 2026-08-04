@props(['label', 'name'])

<label class="flex items-center gap-3">

    <input type="checkbox" name="{{ $name }}" value="1" class="rounded text-sky-700"
        @checked(old($name))>

    <span>

        {{ $label }}

    </span>

</label>
