@extends('customer-service.layouts.app')

@section('title', 'Edit Complaint Type')

@section('content')
<div class="mx-auto w-full max-w-4xl space-y-5 sm:space-y-6">
    <x-form.page-header
        title="Edit Complaint Type"
        subtitle="Update the division and details for this complaint type."
    />

    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <div class="flex items-start gap-2">
                <i class="fas fa-circle-exclamation mt-0.5"></i>
                <div>
                    <p class="font-semibold">Please review the highlighted fields.</p>
                    <p class="mt-1">Correct the errors below and save your changes again.</p>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('customer-service.complaint-categories.update', $complaintCategory) }}" method="POST" class="space-y-5 sm:space-y-6">
        @csrf
        @method('PUT')

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-start gap-3 border-b border-slate-100 px-4 py-4 sm:px-6 sm:py-5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <i class="fas fa-pen-to-square"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-base font-semibold text-slate-900">Complaint type information</h2>
                    <p class="mt-1 text-sm leading-5 text-slate-500">Review the current details and make the necessary updates.</p>
                </div>
            </div>

            <div class="space-y-5 p-4 sm:space-y-6 sm:p-6">
                <div>
                    <label for="code" class="mb-2 block text-sm font-medium text-slate-700">Complaint type code</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"><i class="fas fa-hashtag"></i></span>
                        <input id="code" type="text" value="{{ $complaintCategory->code }}" disabled
                            class="block min-h-11 w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-3.5 text-sm font-medium text-slate-500">
                    </div>
                    <p class="mt-1.5 text-xs text-slate-500">This system-generated code cannot be changed.</p>
                </div>

                <div>
                    <label for="division_id" class="mb-2 block text-sm font-medium text-slate-700">
                        Division <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <select id="division_id" name="division_id" required
                        class="block min-h-11 w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 @error('division_id') border-red-400 focus:border-red-500 focus:ring-red-500/10 @enderror">
                        <option value="">Choose a division</option>
                        @foreach ($divisions as $division)
                            <option value="{{ $division->id }}" @selected(old('division_id', $complaintCategory->division_id) == $division->id)>{{ $division->name }}</option>
                        @endforeach
                    </select>
                    @error('division_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
                        Complaint type name <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <input id="name" name="name" type="text" value="{{ old('name', $complaintCategory->name) }}" required maxlength="255"
                        placeholder="Enter complaint type name"
                        class="block min-h-11 w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 @error('name') border-red-400 focus:border-red-500 focus:ring-red-500/10 @enderror">
                    @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="mb-2 block text-sm font-medium text-slate-700">Description <span class="font-normal text-slate-400">(optional)</span></label>
                    <textarea id="description" name="description" rows="4" maxlength="2000"
                        placeholder="Describe what this complaint type covers..."
                        class="block w-full resize-y rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm leading-6 text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 @error('description') border-red-400 focus:border-red-500 focus:ring-red-500/10 @enderror">{{ old('description', $complaintCategory->description) }}</textarea>
                    @error('description') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="is_active" class="mb-2 block text-sm font-medium text-slate-700">
                        Status <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <select id="is_active" name="is_active" required
                        class="block min-h-11 w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 @error('is_active') border-red-400 focus:border-red-500 focus:ring-red-500/10 @enderror">
                        <option value="1" @selected((string) old('is_active', (int) $complaintCategory->is_active) === '1')>Active</option>
                        <option value="0" @selected((string) old('is_active', (int) $complaintCategory->is_active) === '0')>Inactive</option>
                    </select>
                    @error('is_active') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1.5 text-xs text-slate-500">Inactive complaint types may be unavailable for new complaint submissions, depending on your application rules.</p>
                </div>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-end">
            <a href="{{ route('customer-service.complaint-categories.index') }}"
                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-200 sm:w-auto">
                <i class="fas fa-arrow-left"></i> Cancel
            </a>
            <button type="submit"
                class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-500/20 sm:w-auto">
                <i class="fas fa-floppy-disk"></i> Save changes
            </button>
        </div>
    </form>
</div>
@endsection
