@extends('admin.layouts.app')

@section('title', 'Register Employee')

@section('content')

    <div class="mx-auto max-w-5xl space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div
                    class="mb-2 inline-flex items-center gap-2 rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-sky-700">
                    <i class="fa-solid fa-user-plus"></i>
                    Employee Management
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Register Employee
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Create a new employee account and assign their system access.
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
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                        <i class="fa-solid fa-clipboard-user text-lg"></i>
                    </span>

                    <div>
                        <h2 class="font-bold text-slate-900">
                            Employee Registration Form
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Complete the employee's information below.
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">

                @csrf

                <div class="p-5 sm:p-7">
                    @include('admin.users.partials.form')
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
                        <i id="save-employee-icon" class="fa-solid fa-user-plus"></i>
                        <span id="save-employee-text">Register Employee</span>
                    </button>

                </div>

            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.querySelector(
                'form[action="{{ route('admin.users.store') }}"]'
            );

            const saveButton = document.getElementById('save-employee-button');
            const saveIcon = document.getElementById('save-employee-icon');
            const saveText = document.getElementById('save-employee-text');

            if (!form || !saveButton || !saveIcon || !saveText) return;

            let isSubmitting = false;

            form.addEventListener('submit', function(event) {

                if (isSubmitting) {
                    event.preventDefault();
                    return;
                }

                const confirmed = window.confirm(
                    'Are you sure you want to register this new employee?'
                );

                if (!confirmed) {
                    event.preventDefault();
                    return;
                }

                isSubmitting = true;

                saveButton.disabled = true;
                saveButton.setAttribute('aria-busy', 'true');

                saveIcon.className = 'fa-solid fa-spinner fa-spin';
                saveText.textContent = 'Registering Employee...';

            });

        });
    </script>

@endsection
