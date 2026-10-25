@extends('customer-service.layouts.app')

@section('title', 'Create Division')

@section('content')
<div class="mx-auto w-full max-w-4xl space-y-5 sm:space-y-6">
    {{-- Page heading --}}
    <div class="space-y-3">
        <a href="{{ route('customer-service.divisions.index') }}"
           class="inline-flex min-h-10 items-center gap-2 rounded-lg text-sm font-medium text-gray-600 transition hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            <span>Back to Divisions</span>
        </a>

        <div class="flex items-start gap-3 sm:gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700 ring-1 ring-blue-100 sm:h-12 sm:w-12">
                <i class="fas fa-building text-lg" aria-hidden="true"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">Division Management</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Create Division</h1>
                <p class="mt-1.5 max-w-2xl text-sm leading-6 text-gray-600">Add a division to help organize complaint types and the teams responsible for handling them.</p>
            </div>
        </div>
    </div>

    {{-- Validation summary --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 sm:p-5" role="alert" aria-labelledby="validation-summary-title">
            <div class="flex items-start gap-3">
                <i class="fas fa-circle-exclamation mt-0.5 text-red-600" aria-hidden="true"></i>
                <div class="min-w-0">
                    <h2 id="validation-summary-title" class="font-semibold text-red-900">Please check the information below</h2>
                    <p class="mt-1 text-sm text-red-800">We couldn't save the division because {{ $errors->count() === 1 ? 'one field needs attention.' : 'some fields need attention.' }}</p>
                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-800">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('customer-service.divisions.store') }}" class="space-y-4" novalidate>
        @csrf

        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-4 py-4 sm:px-6 sm:py-5">
                <h2 class="font-semibold text-gray-900">Division information</h2>
                <p class="mt-1 text-sm text-gray-500">Fields marked with <span class="font-semibold text-red-600">*</span> are required.</p>
            </div>

            <div class="space-y-6 p-4 sm:p-6">
                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold text-gray-800">
                        Division name <span class="text-red-600" aria-hidden="true">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           required maxlength="255" autocomplete="organization-title"
                           placeholder="e.g., Engineering Operations"
                           aria-describedby="name-help name-error"
                           @class([
                               'block w-full rounded-xl border bg-white px-4 py-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 transition focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10',
                               'border-red-300' => $errors->has('name'),
                               'border-gray-300' => !$errors->has('name'),
                           ])>
                    <p id="name-help" class="mt-2 text-xs leading-5 text-gray-500">Use a clear name that staff will recognize.</p>
                    @error('name')
                        <p id="name-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="mb-2 block text-sm font-semibold text-gray-800">Description <span class="font-normal text-gray-500">(optional)</span></label>
                    <textarea name="description" id="description" rows="4" maxlength="1000"
                              placeholder="Describe the responsibilities or complaint types handled by this division."
                              aria-describedby="description-help description-error"
                              @class([
                                  'block w-full resize-y rounded-xl border bg-white px-4 py-3 text-sm leading-6 text-gray-900 shadow-sm placeholder:text-gray-400 transition focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10',
                                  'border-red-300' => $errors->has('description'),
                                  'border-gray-300' => !$errors->has('description'),
                              ])>{{ old('description') }}</textarea>
                    <div class="mt-2 flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                        <p id="description-help" class="text-xs leading-5 text-gray-500">A short summary helps staff understand this division's role.</p>
                        <p class="shrink-0 text-xs text-gray-400">Up to 1,000 characters</p>
                    </div>
                    @error('description')
                        <p id="description-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-xl border border-emerald-200 bg-emerald-50/70 p-4">
                    <div class="flex items-start gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-emerald-700 ring-1 ring-emerald-100">
                            <i class="fas fa-circle-check" aria-hidden="true"></i>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-emerald-900">Active by default</p>
                            <p class="mt-1 text-sm leading-5 text-emerald-800">New divisions are automatically active and available when creating complaint types.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 bg-gray-50/80 px-4 py-4 sm:px-6">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('customer-service.divisions.index') }}"
                       class="inline-flex min-h-11 items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-floppy-disk" aria-hidden="true"></i>
                        Create Division
                    </button>
                </div>
            </div>
        </section>
    </form>
</div>
@endsection
