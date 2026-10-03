@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="max-w-7xl mx-auto space-y-5">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col sm:flex-row
                    sm:items-center sm:justify-between gap-4">

            <div>

                <p class="text-sm font-semibold text-sky-600">
                    Administration
                </p>

                <h1 class="text-2xl sm:text-3xl
                           font-bold text-slate-900 mt-1">
                    Dashboard
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Manage employee accounts, organization records,
                    and consumer registrations.
                </p>

            </div>

            <div
                class="inline-flex items-center gap-2
                        self-start sm:self-auto
                        px-4 py-2.5 rounded-xl
                        bg-white border border-slate-200
                        text-sm text-slate-600">

                <i class="fa-regular fa-calendar text-sky-600"></i>

                {{ now()->format('F d, Y') }}

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- PRIMARY STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4">

            {{-- Employees --}}

            <a href="{{ route('admin.users.index') }}"
                class="group bg-white rounded-xl sm:rounded-2xl
                       border border-slate-200
                       p-3 sm:p-5
                       hover:border-sky-200
                       hover:shadow-sm transition">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-[10px] sm:text-sm text-slate-500">
                            Employees
                        </p>

                        <p class="text-xl sm:text-3xl
                                  font-bold text-slate-900 mt-1">

                            {{ number_format($totalUsers ?? 0) }}

                        </p>

                    </div>

                    <div
                        class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-sky-50 text-sky-600
                                items-center justify-center">

                        <i class="fa-solid fa-users"></i>

                    </div>

                </div>

                <p class="hidden sm:block text-xs text-slate-400 mt-2">
                    {{ number_format($activeUsers ?? 0) }} active accounts
                </p>

            </a>


            {{-- Departments --}}

            <a href="{{ route('admin.departments.index') }}"
                class="group bg-white rounded-xl sm:rounded-2xl
                       border border-slate-200
                       p-3 sm:p-5
                       hover:border-indigo-200
                       hover:shadow-sm transition">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-[10px] sm:text-sm text-slate-500">
                            Departments
                        </p>

                        <p class="text-xl sm:text-3xl
                                  font-bold text-indigo-600 mt-1">

                            {{ number_format($totalDepartments ?? 0) }}

                        </p>

                    </div>

                    <div
                        class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-indigo-50 text-indigo-600
                                items-center justify-center">

                        <i class="fa-solid fa-building"></i>

                    </div>

                </div>

                <p class="hidden sm:block text-xs text-slate-400 mt-2">
                    {{ number_format($activeDepartments ?? 0) }} active departments
                </p>

            </a>


            {{-- Positions --}}

            <a href="{{ route('admin.positions.index') }}"
                class="group bg-white rounded-xl sm:rounded-2xl
                       border border-slate-200
                       p-3 sm:p-5
                       hover:border-violet-200
                       hover:shadow-sm transition">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-[10px] sm:text-sm text-slate-500">
                            Positions
                        </p>

                        <p class="text-xl sm:text-3xl
                                  font-bold text-violet-600 mt-1">

                            {{ number_format($totalPositions ?? 0) }}

                        </p>

                    </div>

                    <div
                        class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-violet-50 text-violet-600
                                items-center justify-center">

                        <i class="fa-solid fa-briefcase"></i>

                    </div>

                </div>

                <p class="hidden sm:block text-xs text-slate-400 mt-2">
                    {{ number_format($activePositions ?? 0) }} active positions
                </p>

            </a>


            {{-- Verification --}}

            <a href="{{ route('admin.consumer-verifications.index') }}"
                class="group bg-white rounded-xl sm:rounded-2xl
                       border border-slate-200
                       p-3 sm:p-5
                       hover:border-amber-200
                       hover:shadow-sm transition">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-[10px] sm:text-sm text-slate-500">
                            Pending
                        </p>

                        <p class="text-xl sm:text-3xl
                                  font-bold text-amber-600 mt-1">

                            {{ number_format($pendingVerifications ?? 0) }}

                        </p>

                    </div>

                    <div
                        class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-amber-50 text-amber-600
                                items-center justify-center">

                        <i class="fa-solid fa-user-clock"></i>

                    </div>

                </div>

                <p class="hidden sm:block text-xs text-slate-400 mt-2">
                    Consumer verifications
                </p>

            </a>

        </div>


        {{-- ========================================================= --}}
        {{-- VERIFICATION NOTICE --}}
        {{-- ========================================================= --}}

        @if (($pendingVerifications ?? 0) > 0)
            <div
                class="bg-white rounded-2xl
                        border border-amber-200
                        overflow-hidden">

                <div
                    class="p-4 sm:p-5
                            flex flex-col sm:flex-row
                            sm:items-center sm:justify-between
                            gap-4">

                    <div class="flex items-start sm:items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl
                                    bg-amber-50 text-amber-600
                                    flex items-center justify-center
                                    shrink-0">

                            <i class="fa-solid fa-user-check"></i>

                        </div>

                        <div>

                            <p class="text-sm font-semibold text-slate-900">
                                Consumer registrations need review
                            </p>

                            <p class="text-xs sm:text-sm text-slate-500 mt-1">

                                {{ $pendingVerifications }}

                                {{ $pendingVerifications == 1 ? 'registration is' : 'registrations are' }}

                                currently waiting for verification.

                            </p>

                        </div>

                    </div>

                    <a href="{{ route('admin.consumer-verifications.index') }}"
                        class="inline-flex items-center
                               justify-center gap-2
                               px-4 py-2.5 rounded-xl
                               bg-sky-700 text-white
                               text-sm font-semibold
                               hover:bg-sky-800 transition">

                        Review

                        <i class="fa-solid fa-arrow-right text-xs"></i>

                    </a>

                </div>

            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- DASHBOARD CONTENT --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">


            {{-- ===================================================== --}}
            {{-- RECENT REGISTRATIONS --}}
            {{-- ===================================================== --}}

            <div
                class="xl:col-span-2
                        bg-white rounded-2xl
                        border border-slate-200
                        overflow-hidden">

                {{-- Header --}}

                <div
                    class="px-4 sm:px-5 py-4
                            border-b border-slate-100
                            flex items-center
                            justify-between gap-4">

                    <div>

                        <h2 class="font-semibold text-slate-900">
                            Recent Consumer Registrations
                        </h2>

                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Latest self-registered consumer accounts.
                        </p>

                    </div>

                    <a href="{{ route('admin.consumer-verifications.index') }}"
                        class="text-xs sm:text-sm
                               font-semibold text-sky-700
                               hover:text-sky-800 whitespace-nowrap">

                        View All

                    </a>

                </div>


                {{-- Records --}}

                <div class="divide-y divide-slate-100">

                    @forelse (($recentRegistrations ?? collect()) as $consumer)

                        @php

                            $consumerName = trim(
                                ($consumer->first_name ?? '') .
                                    ' ' .
                                    ($consumer->middle_name ?? '') .
                                    ' ' .
                                    ($consumer->last_name ?? ''),
                            );

                            $verificationClasses = match ($consumer->verification_status) {
                                'Verified' => 'bg-green-50 text-green-700 border-green-100',

                                'Rejected' => 'bg-red-50 text-red-700 border-red-100',

                                default => 'bg-amber-50 text-amber-700 border-amber-100',
                            };
                        @endphp

                        <a href="{{ route('admin.consumer-verifications.show', $consumer) }}"
                            class="flex items-center gap-3
                                   px-4 sm:px-5 py-4
                                   hover:bg-slate-50/70
                                   transition">

                            {{-- Avatar --}}

                            <div
                                class="w-10 h-10 rounded-xl
                                        bg-sky-50 text-sky-700
                                        flex items-center justify-center
                                        font-bold text-sm shrink-0">

                                {{ strtoupper(substr($consumer->first_name ?? 'C', 0, 1)) }}

                                {{ strtoupper(substr($consumer->last_name ?? '', 0, 1)) }}

                            </div>


                            {{-- Details --}}

                            <div class="min-w-0 flex-1">

                                <p
                                    class="text-sm font-semibold
                                          text-slate-900 truncate">

                                    {{ $consumerName ?: 'Consumer' }}

                                </p>

                                <div
                                    class="flex flex-wrap
                                            items-center gap-x-2 gap-y-1 mt-1">

                                    <p class="text-xs text-slate-500 truncate">
                                        {{ $consumer->account_number ?? 'No account number' }}
                                    </p>

                                    <span class="hidden sm:inline text-slate-300">
                                        •
                                    </span>

                                    <p
                                        class="hidden sm:block
                                              text-xs text-slate-400">

                                        {{ $consumer->created_at?->format('M d, Y') }}

                                    </p>

                                </div>

                            </div>


                            {{-- Status --}}

                            <span
                                class="hidden sm:inline-flex
                                         px-2.5 py-1 rounded-full
                                         border text-[10px]
                                         font-bold whitespace-nowrap
                                         {{ $verificationClasses }}">

                                {{ $consumer->verification_status }}

                            </span>


                            <i
                                class="fa-solid fa-chevron-right
                                      text-xs text-slate-300 shrink-0"></i>

                        </a>

                    @empty

                        <div class="px-5 py-12 text-center">

                            <div
                                class="w-11 h-11 mx-auto
                                        rounded-xl bg-slate-100
                                        text-slate-400
                                        flex items-center justify-center">

                                <i class="fa-solid fa-user-check"></i>

                            </div>

                            <p class="text-sm font-semibold
                                      text-slate-700 mt-3">

                                No recent registrations

                            </p>

                            <p class="text-xs text-slate-400 mt-1">
                                New self-registered consumers will appear here.
                            </p>

                        </div>
                    @endforelse

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- SYSTEM OVERVIEW --}}
            {{-- ===================================================== --}}

            <div
                class="bg-white rounded-2xl
                        border border-slate-200
                        overflow-hidden">

                <div class="px-5 py-4 border-b border-slate-100">

                    <h2 class="font-semibold text-slate-900">
                        System Overview
                    </h2>

                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Current administration records.
                    </p>

                </div>


                <div class="p-5 space-y-1">

                    {{-- Active Employees --}}

                    <div class="flex items-center
                                justify-between gap-4 py-3">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-9 h-9 rounded-xl
                                        bg-green-50 text-green-600
                                        flex items-center justify-center">

                                <i class="fa-solid fa-user-check text-sm"></i>

                            </div>

                            <span class="text-sm text-slate-600">
                                Active Employees
                            </span>

                        </div>

                        <span class="text-sm font-bold text-slate-900">
                            {{ number_format($activeUsers ?? 0) }}
                        </span>

                    </div>


                    {{-- Inactive --}}

                    <div
                        class="flex items-center
                                justify-between gap-4 py-3
                                border-t border-slate-100">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-9 h-9 rounded-xl
                                        bg-red-50 text-red-500
                                        flex items-center justify-center">

                                <i class="fa-solid fa-user-xmark text-sm"></i>

                            </div>

                            <span class="text-sm text-slate-600">
                                Inactive Employees
                            </span>

                        </div>

                        <span class="text-sm font-bold text-slate-900">
                            {{ number_format($inactiveUsers ?? 0) }}
                        </span>

                    </div>


                    {{-- Consumers --}}

                    <div
                        class="flex items-center
                                justify-between gap-4 py-3
                                border-t border-slate-100">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-9 h-9 rounded-xl
                                        bg-sky-50 text-sky-600
                                        flex items-center justify-center">

                                <i class="fa-solid fa-house-user text-sm"></i>

                            </div>

                            <span class="text-sm text-slate-600">
                                Consumers
                            </span>

                        </div>

                        <span class="text-sm font-bold text-slate-900">
                            {{ number_format($totalConsumers ?? 0) }}
                        </span>

                    </div>


                    {{-- Departments --}}

                    <div
                        class="flex items-center
                                justify-between gap-4 py-3
                                border-t border-slate-100">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-9 h-9 rounded-xl
                                        bg-indigo-50 text-indigo-600
                                        flex items-center justify-center">

                                <i class="fa-solid fa-building text-sm"></i>

                            </div>

                            <span class="text-sm text-slate-600">
                                Departments
                            </span>

                        </div>

                        <span class="text-sm font-bold text-slate-900">
                            {{ number_format($totalDepartments ?? 0) }}
                        </span>

                    </div>


                    {{-- Positions --}}

                    <div
                        class="flex items-center
                                justify-between gap-4 py-3
                                border-t border-slate-100">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-9 h-9 rounded-xl
                                        bg-violet-50 text-violet-600
                                        flex items-center justify-center">

                                <i class="fa-solid fa-briefcase text-sm"></i>

                            </div>

                            <span class="text-sm text-slate-600">
                                Positions
                            </span>

                        </div>

                        <span class="text-sm font-bold text-slate-900">
                            {{ number_format($totalPositions ?? 0) }}
                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- QUICK MANAGEMENT --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl
                    border border-slate-200
                    overflow-hidden">

            <div class="px-5 py-4 border-b border-slate-100">

                <h2 class="font-semibold text-slate-900">
                    Quick Management
                </h2>

                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Access frequently used administration modules.
                </p>

            </div>


            <div class="grid grid-cols-2 lg:grid-cols-4">

                {{-- Employees --}}

                <a href="{{ route('admin.users.index') }}"
                    class="p-4 sm:p-5
                           border-b border-r border-slate-100
                           lg:border-b-0
                           hover:bg-slate-50 transition">

                    <div
                        class="w-9 h-9 rounded-xl
                                bg-sky-50 text-sky-600
                                flex items-center justify-center">

                        <i class="fa-solid fa-users"></i>

                    </div>

                    <p class="text-sm font-semibold text-slate-900 mt-3">
                        Employees
                    </p>

                    <p class="hidden sm:block text-xs text-slate-500 mt-1">
                        Manage employee accounts
                    </p>

                </a>


                {{-- Verification --}}

                <a href="{{ route('admin.consumer-verifications.index') }}"
                    class="p-4 sm:p-5
                           border-b border-slate-100
                           lg:border-b-0 lg:border-r
                           hover:bg-slate-50 transition">

                    <div
                        class="relative w-9 h-9 rounded-xl
                                bg-amber-50 text-amber-600
                                flex items-center justify-center">

                        <i class="fa-solid fa-user-check"></i>

                        @if (($pendingVerifications ?? 0) > 0)
                            <span
                                class="absolute -top-1.5 -right-1.5
                                         min-w-4 h-4 px-1
                                         rounded-full bg-red-500
                                         text-white text-[8px] font-bold
                                         flex items-center justify-center">

                                {{ $pendingVerifications > 99 ? '99+' : $pendingVerifications }}

                            </span>
                        @endif

                    </div>

                    <p class="text-sm font-semibold text-slate-900 mt-3">
                        Verification
                    </p>

                    <p class="hidden sm:block text-xs text-slate-500 mt-1">
                        Review registrations
                    </p>

                </a>


                {{-- Departments --}}

                <a href="{{ route('admin.departments.index') }}"
                    class="p-4 sm:p-5
                           border-r border-slate-100
                           hover:bg-slate-50 transition">

                    <div
                        class="w-9 h-9 rounded-xl
                                bg-indigo-50 text-indigo-600
                                flex items-center justify-center">

                        <i class="fa-solid fa-building"></i>

                    </div>

                    <p class="text-sm font-semibold text-slate-900 mt-3">
                        Departments
                    </p>

                    <p class="hidden sm:block text-xs text-slate-500 mt-1">
                        Manage departments
                    </p>

                </a>


                {{-- Positions --}}

                <a href="{{ route('admin.positions.index') }}"
                    class="p-4 sm:p-5
                           hover:bg-slate-50 transition">

                    <div
                        class="w-9 h-9 rounded-xl
                                bg-violet-50 text-violet-600
                                flex items-center justify-center">

                        <i class="fa-solid fa-briefcase"></i>

                    </div>

                    <p class="text-sm font-semibold text-slate-900 mt-3">
                        Positions
                    </p>

                    <p class="hidden sm:block text-xs text-slate-500 mt-1">
                        Manage job positions
                    </p>

                </a>

            </div>

        </div>

    </div>

@endsection
