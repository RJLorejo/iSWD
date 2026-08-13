
<x-form.input label="Category Name" name="name" :value="old('name', optional($complaintCategory)->name)" required />

<div class="col-span-2">

    <label class="block font-semibold mb-2">
        Description
    </label>

    <textarea name="description" rows="4" class="w-full rounded-xl border-gray-300 focus:ring-sky-500">{{ old('description', optional($complaintCategory)->description) }}</textarea>

</div>
