<x-form.input label="Department Name" name="department_name" :value="old('department_name', optional($department)->department_name)" required />

<div class="col-span-2">

    <label class="block font-semibold mb-2">
        Description
    </label>

    <textarea name="description" rows="4" class="w-full rounded-xl border-gray-300 focus:ring-sky-500">{{ old('description', optional($department)->description) }}</textarea>

</div>
