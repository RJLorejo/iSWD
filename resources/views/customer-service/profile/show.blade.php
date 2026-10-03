@extends('customer-service.layouts.app')

@section('title', 'My Profile')

@section('content')

    <div class="mx-auto max-w-6xl space-y-6">

        <div>

            <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-600">
                Account
            </p>

            <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                My Profile
            </h2>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                View your Customer Service account, employee information, and sign-in activity.
            </p>

        </div>

        <div class="grid gap-6 lg:grid-cols-[320px_minmax(0,1fr)]">

            <aside class="space-y-6">

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="h-24 bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600"></div>

                    <div class="px-6 pb-6">

                        <img
                            src="{{ $user->avatar_url }}"
                            alt="{{ $user->full_name }}"
                            class="-mt-12 h-24 w-24 rounded-2xl border-4 border-white bg-white object-cover shadow-md">

                        <div class="mt-4">

                            <h2 class="text-xl font-bold text-slate-900">
                                {{ $user->full_name }}
                            </h2>

                            <p class="mt-1 text-sm font-medium text-sky-700">
                                {{ $user->getRoleNames()->implode(', ') ?: 'Customer Service' }}
                            </p>

                        </div>

                        <div class="mt-5 flex flex-wrap gap-2">

                            @if ($user->is_active)

                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">

                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                    Active Account

                                </span>

                            @else

                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700">

                                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>

                                    Inactive Account

                                </span>

                            @endif

                        </div>

                        <a
                            href="{{ route('customer-service.profile.edit') }}"
                            class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-sky-800">

                            <i class="fas fa-pen"></i>

                            Edit Profile

                        </a>

                    </div>

                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <h3 class="text-sm font-bold text-slate-800">
                        Account Activity
                    </h3>

                    <div class="mt-4 space-y-4">

                        <div class="flex gap-3">

                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600">

                                <i class="fas fa-clock text-xs"></i>

                            </span>

                            <div>

                                <p class="text-xs font-semibold text-slate-400">
                                    Last Login
                                </p>

                                <p class="mt-1 text-sm font-medium text-slate-700">
                                    {{ $user->last_login_at
                                        ? $user->last_login_at->format('M d, Y h:i A')
                                        : 'No login recorded' }}
                                </p>

                            </div>

                        </div>

                        <div class="flex gap-3">

                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500">

                                <i class="fas fa-network-wired text-xs"></i>

                            </span>

                            <div class="min-w-0">

                                <p class="text-xs font-semibold text-slate-400">
                                    Last Login IP
                                </p>

                                <p class="mt-1 break-all text-sm font-medium text-slate-700">
                                    {{ $user->last_login_ip ?: 'Not available' }}
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </aside>

            <div class="space-y-6">

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-5 py-5 sm:px-6">

                        <div class="flex items-center gap-3">

                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600">

                                <i class="fas fa-id-card"></i>

                            </span>

                            <div>

                                <h2 class="font-bold text-slate-900">
                                    Employee Information
                                </h2>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Your employee and organizational details
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="grid gap-x-8 sm:grid-cols-2">

                        @php
                            $employeeDetails = [
                                [
                                    'label' => 'Employee ID',
                                    'value' => $user->employee_id ?: 'Not assigned',
                                    'icon' => 'fa-id-badge',
                                ],
                                [
                                    'label' => 'Department',
                                    'value' => optional($user->department)->department_name ?: 'Not assigned',
                                    'icon' => 'fa-building',
                                ],
                                [
                                    'label' => 'Position',
                                    'value' => optional($user->position)->position_name ?: 'Not assigned',
                                    'icon' => 'fa-briefcase',
                                ],
                                [
                                    'label' => 'System Role',
                                    'value' => $user->getRoleNames()->implode(', ') ?: 'Not assigned',
                                    'icon' => 'fa-user-shield',
                                ],
                            ];
                        @endphp

                        @foreach ($employeeDetails as $detail)

                            <div class="flex gap-3 border-b border-slate-100 px-5 py-5 sm:px-6">

                                <span
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-slate-400">

                                    <i class="fas {{ $detail['icon'] }} text-xs"></i>

                                </span>

                                <div class="min-w-0">

                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        {{ $detail['label'] }}
                                    </p>

                                    <p class="mt-1 break-words text-sm font-semibold text-slate-700">
                                        {{ $detail['value'] }}
                                    </p>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </section>

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-5 py-5 sm:px-6">

                        <div class="flex items-center gap-3">

                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600">

                                <i class="fas fa-address-card"></i>

                            </span>

                            <div>

                                <h2 class="font-bold text-slate-900">
                                    Contact Information
                                </h2>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Contact details associated with your account
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="grid sm:grid-cols-2">

                        <div class="border-b border-slate-100 px-5 py-5 sm:border-r sm:px-6">

                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Email Address
                            </p>

                            <p class="mt-2 break-all text-sm font-semibold text-slate-700">
                                {{ $user->email }}
                            </p>

                        </div>

                        <div class="border-b border-slate-100 px-5 py-5 sm:px-6">

                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                Phone Number
                            </p>

                            <p class="mt-2 text-sm font-semibold text-slate-700">
                                {{ $user->phone ?: 'Not provided' }}
                            </p>

                        </div>

                    </div>

                </section>

                <div class="rounded-2xl border border-sky-100 bg-sky-50 p-5">

                    <div class="flex items-start gap-3">

                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-sky-600">

                            <i class="fas fa-circle-info"></i>

                        </span>

                        <div>

                            <p class="text-sm font-bold text-sky-900">
                                Employee records
                            </p>

                            <p class="mt-1 text-xs leading-5 text-sky-700">
                                Department, position, role, and employee ID are managed as
                                organizational records. Use Edit Profile to update your personal
                                contact information and profile picture.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
