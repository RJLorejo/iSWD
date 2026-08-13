<x-form.input label="Position Name" name="position_name" :value="old('position_name', optional($position)->position_name)" required />

<div>

    <label class="block font-semibold mb-2">
        Department
    </label>

    <select name="department_id" class="w-full rounded-xl border-gray-300 focus:ring-sky-500">

        <option value="">
            Select Department
        </option>

        @foreach ($departments as $department)
            <option value="{{ $department->id }}" @selected(old('department_id', optional($position)->department_id) == $department->id)>

                {{ $department->department_name }}

            </option>
        @endforeach

    </select>

    @error('department_id')
        <p class="text-red-500 text-sm mt-2">

            {{ $message }}

        </p>
    @enderror

</div>

<div class="col-span-2">

    <label class="block font-semibold mb-2">
        Description
    </label>

    <textarea name="description" rows="4" class="w-full rounded-xl border-gray-300 focus:ring-sky-500">{{ old('description', optional($position)->description) }}</textarea>

    @error('description')
        <p class="text-red-500 text-sm mt-2">

            {{ $message }}

        </p>
    @enderror

</div>
