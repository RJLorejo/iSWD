@extends('admin.layouts.app')

@section('title', 'Edit Employee')

@section('content')

    <div class="mx-auto max-w-5xl space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div
                    class="mb-2 inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700">
                    <i class="fa-solid fa-user-pen"></i>
                    Employee Management
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Edit Employee
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Update employee information, organization details, and account status.
                </p>
            </div>

            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Employees
            </a>
        </div>

        {{-- FORM CARD --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-5 sm:px-7">
                <div class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <i class="fa-solid fa-user-gear text-lg"></i>
                    </span>

                    <div>
                        <h2 class="font-bold text-slate-900">
                            Employee Account Settings
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Review and update the employee's registered information.
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="space-y-8 p-5 sm:p-7">

                    @include('admin.users.partials.form')

                    {{-- ACCOUNT STATUS --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                        <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-5">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </span>

                                <div>
                                    <h3 class="font-bold text-slate-900">
                                        Account Status
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Control whether this employee can access the system.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5">
                            <label for="is_active"
                                class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-sky-300 hover:bg-sky-50/50">

                                <input id="is_active" type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                    class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">

                                <span>
                                    <span class="block text-sm font-semibold text-slate-800">
                                        Active Employee Account
                                    </span>
                                    <span class="mt-1 block text-xs leading-5 text-slate-500">
                                        When enabled, this employee is permitted to access the system according to their
                                        assigned role and permissions.
                                    </span>
                                </span>
                            </label>
                        </div>

                    </section>

                </div>

                {{-- FORM ACTIONS --}}
                <div
                    class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50/50 px-5 py-5 sm:flex-row sm:justify-end sm:px-7">

                    <a href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Cancel
                    </a>

                    <button id="save-employee-button" type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-700 to-cyan-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:from-sky-800 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70">
                        <i id="save-employee-icon" class="fa-solid fa-floppy-disk"></i>

                        <span id="save-employee-text">Save Changes</span>
                    </button>

                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {

                        const form = document.querySelector('form[action="{{ route('admin.users.update', $user) }}"]');
                        const saveButton = document.getElementById('save-employee-button');
                        const saveIcon = document.getElementById('save-employee-icon');
                        const saveText = document.getElementById('save-employee-text');

                        if (!form || !saveButton) return;

                        let isSubmitting = false;

                        form.addEventListener('submit', function(event) {

                            if (event.defaultPrevented) return;

                            // Prevent duplicate submission.
                            if (isSubmitting) {
                                event.preventDefault();
                                return;
                            }

                            // Confirmation.
                            const confirmed = window.confirm(
                                'Are you sure you want to save these employee changes?'
                            );

                            if (!confirmed) {
                                event.preventDefault();
                                return;
                            }

                            // Show loading state only after confirmation.
                            isSubmitting = true;

                            saveButton.disabled = true;
                            saveButton.setAttribute('aria-busy', 'true');

                            saveIcon.className = 'fa-solid fa-spinner fa-spin';
                            saveText.textContent = 'Saving Changes...';

                        });

                    });
                </script>


            </form>
        </div>
    </div>

@endsection
