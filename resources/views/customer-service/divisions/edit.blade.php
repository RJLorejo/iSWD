@extends('customer-service.layouts.app')

@section('title', 'Edit Division')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- PAGE HEADER --}}
        <div class="space-y-3">
            <a
                href="{{ route('customer-service.divisions.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-blue-600"
            >
                <i class="fas fa-arrow-left"></i>
                Back to Divisions
            </a>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                        Edit Division
                    </h1>
                    <p class="mt-1 text-sm leading-6 text-gray-500">
                        Update the division's details and availability.
                    </p>
                </div>

                <span class="inline-flex w-fit items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold {{ $division->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200' : 'bg-gray-100 text-gray-600 ring-1 ring-inset ring-gray-200' }}">
                    <span class="h-2 w-2 rounded-full {{ $division->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                    {{ $division->is_active ? 'Currently Active' : 'Currently Inactive' }}
                </span>
            </div>
        </div>

        {{-- VALIDATION SUMMARY --}}
        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 sm:p-5" role="alert" aria-labelledby="division-errors-title">
                <div class="flex items-start gap-3">
                    <i class="fas fa-circle-exclamation mt-0.5 text-red-600" aria-hidden="true"></i>
                    <div class="min-w-0">
                        <h2 id="division-errors-title" class="font-semibold text-red-800">
                            Please correct the following {{ $errors->count() === 1 ? 'error' : 'errors' }}:
                        </h2>
                        <ul class="mt-2 list-inside list-disc space-y-1 break-words text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- USAGE SUMMARY --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-medium text-gray-500">Complaint Types</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fas fa-tags" aria-hidden="true"></i>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold tabular-nums text-gray-900">
                    {{ $division->complaint_types_count ?? 0 }}
                </p>
                <p class="mt-1 text-xs text-gray-500">Complaint types assigned to this division</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-medium text-gray-500">Complaints</p>
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                        <i class="fas fa-clipboard-list" aria-hidden="true"></i>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-bold tabular-nums text-gray-900">
                    {{ $division->complaints_count ?? 0 }}
                </p>
                <p class="mt-1 text-xs text-gray-500">Complaint records associated with this division</p>
            </div>
        </div>

        <form
            method="POST"
            action="{{ route('customer-service.divisions.update', $division) }}"
            class="space-y-0"
        >
            @csrf
            @method('PUT')

            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-5 sm:px-6">
                    <div class="flex items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <i class="fas fa-building" aria-hidden="true"></i>
                        </span>
                        <div class="min-w-0">
                            <h2 class="font-semibold text-gray-900">Division Information</h2>
                            <p class="mt-1 break-words text-sm text-gray-500">
                                Update the information for <span class="font-medium text-gray-700">{{ $division->name }}</span>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 p-5 sm:p-6">
                    {{-- NAME --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">
                            Division Name <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $division->name) }}"
                            required
                            maxlength="255"
                            autocomplete="organization"
                            placeholder="Example: Engineering Operation"
                            aria-describedby="name-help name-error"
                            class="block w-full rounded-xl border px-4 py-3 text-sm text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }}"
                        >
                        <p id="name-help" class="mt-2 text-xs text-gray-500">Use a clear name that staff can easily recognize.</p>
                        @error('name')
                            <p id="name-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- DESCRIPTION --}}
                    <div>
                        <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">
                            Description <span class="font-normal text-gray-400">(Optional)</span>
                        </label>
                        <textarea
                            name="description"
                            id="description"
                            rows="5"
                            maxlength="1000"
                            placeholder="Describe the responsibilities or complaint types handled by this division."
                            aria-describedby="description-help description-error"
                            class="block w-full resize-y rounded-xl border px-4 py-3 text-sm leading-6 text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 {{ $errors->has('description') ? 'border-red-400' : 'border-gray-300' }}"
                        >{{ old('description', $division->description) }}</textarea>
                        <div class="mt-2 flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                            <p id="description-help" class="text-xs text-gray-500">Briefly explain what this division handles.</p>
                            <p class="text-xs text-gray-400">Maximum 1,000 characters</p>
                        </div>
                        @error('description')
                            <p id="description-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- STATUS --}}
                    <div>
                        <label for="is_active" class="mb-2 block text-sm font-semibold text-gray-700">
                            Division Status <span class="text-red-500" aria-hidden="true">*</span>
                        </label>
                        <select
                            name="is_active"
                            id="is_active"
                            required
                            aria-describedby="status-help status-error"
                            class="block w-full rounded-xl border px-4 py-3 text-sm text-gray-900 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 {{ $errors->has('is_active') ? 'border-red-400' : 'border-gray-300' }}"
                        >
                            <option value="1" @selected((string) old('is_active', $division->is_active ? '1' : '0') === '1')>
                                Active
                            </option>
                            <option value="0" @selected((string) old('is_active', $division->is_active ? '1' : '0') === '0')>
                                Inactive
                            </option>
                        </select>
                        <p id="status-help" class="mt-2 text-xs leading-5 text-gray-500">
                            Inactive divisions will not be available when creating new complaint records or complaint types.
                        </p>
                        @error('is_active')
                            <p id="status-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- IN-USE NOTICE --}}
                    @if (($division->complaint_types_count ?? 0) > 0 || ($division->complaints_count ?? 0) > 0)
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-triangle-exclamation mt-0.5 text-amber-600" aria-hidden="true"></i>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-amber-900">Division is currently in use</p>
                                    <p class="mt-1 text-sm leading-5 text-amber-800">
                                        Existing records will remain associated with this division even if you mark it inactive.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- FORM ACTIONS --}}
                <div class="border-t border-gray-100 bg-gray-50 px-5 py-5 sm:px-6">
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <a
                            href="{{ route('customer-service.divisions.index') }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto"
                        >
                            <i class="fas fa-xmark" aria-hidden="true"></i>
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto"
                        >
                            <i class="fas fa-floppy-disk" aria-hidden="true"></i>
                            Save Changes
                        </button>
                    </div>
                </div>
            </section>
        </form>
    </div>
@endsection
