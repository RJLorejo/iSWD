@extends('customer-service.layouts.app')

@section('title', 'Add Complaint Type')

@section('content')
<div class="mx-auto w-full max-w-4xl space-y-5 sm:space-y-6">
    <x-form.page-header
        title="Add Complaint Type"
        subtitle="Create a complaint type and assign it to the appropriate division."
    />

    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <div class="flex items-start gap-2">
                <i class="fas fa-circle-exclamation mt-0.5"></i>
                <div>
                    <p class="font-semibold">Please review the highlighted fields.</p>
                    <p class="mt-1">Correct the errors below and submit the form again.</p>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('customer-service.complaint-categories.store') }}" method="POST" class="space-y-5 sm:space-y-6">
        @csrf

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-start gap-3 border-b border-slate-100 px-4 py-4 sm:px-6 sm:py-5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <i class="fas fa-file-circle-plus"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-base font-semibold text-slate-900">Complaint type information</h2>
                    <p class="mt-1 text-sm leading-5 text-slate-500">Provide the details used to categorize consumer complaints.</p>
                </div>
            </div>

            <div class="space-y-5 p-4 sm:space-y-6 sm:p-6">
                <div>
                    <label for="division_id" class="mb-2 block text-sm font-medium text-slate-700">
                        Division <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <select id="division_id" name="division_id" required
                        class="block min-h-11 w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 @error('division_id') border-red-400 focus:border-red-500 focus:ring-red-500/10 @enderror">
                        <option value="">Choose a division</option>
                        @foreach ($divisions as $division)
                            <option value="{{ $division->id }}" @selected(old('division_id') == $division->id)>{{ $division->name }}</option>
                        @endforeach
                    </select>
                    @error('division_id') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1.5 text-xs text-slate-500">Choose the division responsible for handling this complaint type.</p>
                </div>

                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
                        Complaint type name <span class="text-red-500" aria-hidden="true">*</span>
                    </label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255"
                        placeholder="e.g. No Water, Low Water Pressure"
                        class="block min-h-11 w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 @error('name') border-red-400 focus:border-red-500 focus:ring-red-500/10 @enderror">
                    @error('name') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1.5 text-xs text-slate-500">Use a short, clear name that consumers can easily understand.</p>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label for="description" class="block text-sm font-medium text-slate-700">Description <span class="font-normal text-slate-400">(optional)</span></label>
                        <span class="text-xs text-slate-400">Additional details</span>
                    </div>
                    <textarea id="description" name="description" rows="4" maxlength="2000"
                        placeholder="Briefly explain what this complaint type covers..."
                        class="block w-full resize-y rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm leading-6 text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 @error('description') border-red-400 focus:border-red-500 focus:ring-red-500/10 @enderror">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
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
                <i class="fas fa-check"></i> Create complaint type
            </button>
        </div>
    </form>
</div>
@endsection
