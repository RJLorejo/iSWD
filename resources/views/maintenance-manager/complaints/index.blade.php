@extends('maintenance-manager.layouts.app')

@section('title', 'Complaint Management')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                Complaint Management
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Monitor verified complaints, manage technician assignments,
                and track service progress.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('maintenance-manager.complaints.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                       bg-white border border-gray-200 text-sm font-semibold
                       text-gray-700 hover:bg-gray-50 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h5M20 20v-5h-5M5.64 18.36A9 9 0 0018.36 5.64M18.36 18.36A9 9 0 015.64 5.64"/>
                </svg>

                Refresh
            </a>
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SUCCESS / ERROR MESSAGES --}}
    {{-- ========================================================= --}}

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7"/>
                </svg>

                <p class="text-sm font-medium text-green-800">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 mt-0.5 shrink-0"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M10.29 3.86l-8.82 15a2 2 0 001.71 2.64h17.64a2 2 0 001.71-2.64l-8.82-15a2 2 0 00-3.42 0z"/>
                </svg>

                <div>
                    @foreach($errors->all() as $error)
                        <p class="text-sm font-medium text-red-800">
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif


    {{-- ========================================================= --}}
    {{-- STATISTICS --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">

        {{-- FOR ASSIGNMENT --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        For Assignment
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ $verifiedCount ?? 0 }}
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Verified and unassigned
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>

            </div>
        </div>


        {{-- ASSIGNED --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Assigned
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ $assignedCount ?? 0 }}
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Technician team assigned
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5V4H2v16h5M9 20v-6h6v6M6 8h.01M10 8h.01M14 8h.01"/>
                    </svg>
                </div>

            </div>
        </div>


        {{-- IN PROGRESS --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        In Progress
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ $inProgressCount ?? 0 }}
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Currently being handled
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

            </div>
        </div>


        {{-- COMPLETED --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Completed
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ $completedCount ?? 0 }}
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Maintenance completed
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

            </div>
        </div>


        {{-- CLOSED --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Closed
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ $closedCount ?? 0 }}
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Finalized complaints
                    </p>
                </div>

                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

            </div>
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- FILTERS --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">

        <form
            method="GET"
            action="{{ route('maintenance-manager.complaints.index') }}"
            class="p-4 sm:p-5"
        >

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">

                {{-- SEARCH --}}
                <div class="lg:col-span-2 relative">

                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35m2.35-5.65a8 8 0 11-16 0 8 8 0 0116 0z"/>
                    </svg>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search complaint, consumer, account..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200
                               text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >

                </div>


                {{-- STATUS --}}
                <div>
                    <select
                        name="status"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200
                               text-sm text-gray-700 focus:ring-2 focus:ring-blue-500
                               focus:border-blue-500"
                    >

                        <option value="">All Statuses</option>

                        <option value="For Assignment"
                            {{ request('status') === 'For Assignment' ? 'selected' : '' }}>
                            For Assignment
                        </option>

                        <option value="Verified"
                            {{ request('status') === 'Verified' ? 'selected' : '' }}>
                            Verified
                        </option>

                        <option value="Assigned"
                            {{ request('status') === 'Assigned' ? 'selected' : '' }}>
                            Assigned
                        </option>

                        <option value="In Progress"
                            {{ request('status') === 'In Progress' ? 'selected' : '' }}>
                            In Progress
                        </option>

                        <option value="Completed"
                            {{ request('status') === 'Completed' ? 'selected' : '' }}>
                            Completed
                        </option>

                        <option value="Closed"
                            {{ request('status') === 'Closed' ? 'selected' : '' }}>
                            Closed
                        </option>

                    </select>
                </div>


                {{-- DATE FROM --}}
                <div>
                    <input
                        type="date"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200
                               text-sm text-gray-700 focus:ring-2 focus:ring-blue-500
                               focus:border-blue-500"
                    >
                </div>


                {{-- DATE TO --}}
                <div>
                    <input
                        type="date"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200
                               text-sm text-gray-700 focus:ring-2 focus:ring-blue-500
                               focus:border-blue-500"
                    >
                </div>

            </div>


            <div class="mt-3 flex flex-wrap gap-2">

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2
                           px-4 py-2.5 rounded-xl bg-blue-600 text-white
                           text-sm font-semibold hover:bg-blue-700 transition"
                >
                    <svg class="w-4 h-4"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35m2.35-5.65a8 8 0 11-16 0 8 8 0 0116 0z"/>
                    </svg>

                    Apply Filters
                </button>

                @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                    <a
                        href="{{ route('maintenance-manager.complaints.index') }}"
                        class="inline-flex items-center justify-center px-4 py-2.5
                               rounded-xl bg-gray-100 text-gray-700 text-sm font-semibold
                               hover:bg-gray-200 transition"
                    >
                        Clear
                    </a>
                @endif

            </div>

        </form>

    </div>


    {{-- ========================================================= --}}
    {{-- COMPLAINT TABLE --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">

            <div>
                <h2 class="font-bold text-gray-900">
                    Complaints
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Verified complaints and maintenance assignments
                </p>
            </div>

            <span class="text-sm text-gray-500">
                {{ $complaints->total() }} total
            </span>

        </div>


        @if($complaints->count())

            {{-- DESKTOP TABLE --}}
            <div class="hidden lg:block overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-50 border-b border-gray-100">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-gray-500">
                                Complaint
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-gray-500">
                                Consumer
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-gray-500">
                                Category
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-gray-500">
                                Status
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-gray-500">
                                Technicians
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-gray-500">
                                Updated
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold
                                       uppercase tracking-wider text-gray-500">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @foreach($complaints as $complaint)

                            @php

                                $statusClasses = match($complaint->status) {

                                    'Verified' =>
                                        'bg-blue-50 text-blue-700 border-blue-100',

                                    'Assigned' =>
                                        'bg-indigo-50 text-indigo-700 border-indigo-100',

                                    'In Progress' =>
                                        'bg-amber-50 text-amber-700 border-amber-100',

                                    'Completed' =>
                                        'bg-green-50 text-green-700 border-green-100',

                                    'Closed' =>
                                        'bg-gray-100 text-gray-700 border-gray-200',

                                    default =>
                                        'bg-gray-50 text-gray-600 border-gray-100',
                                };

                            @endphp


                            <tr class="hover:bg-gray-50/70 transition">

                                {{-- COMPLAINT --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-start gap-3">

                                        <div class="w-10 h-10 rounded-xl bg-blue-50
                                                    flex items-center justify-center shrink-0">

                                            <svg class="w-5 h-5 text-blue-600"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M8 10h8M8 14h5m-9 6h16a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>

                                        </div>

                                        <div class="min-w-0">

                                            <p class="font-semibold text-gray-900 truncate max-w-[220px]">
                                                {{ $complaint->subject }}
                                            </p>

                                            <p class="text-xs text-blue-600 font-medium mt-0.5">
                                                {{ $complaint->complaint_no }}
                                            </p>

                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $complaint->created_at?->format('M d, Y h:i A') }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- CONSUMER --}}
                                <td class="px-5 py-4">

                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ $complaint->consumer?->full_name
                                                ?? $complaint->complainant_name
                                                ?? '—' }}
                                        </p>

                                        @if($complaint->consumer?->account_number)
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                Account:
                                                {{ $complaint->consumer->account_number }}
                                            </p>
                                        @endif

                                        @if($complaint->complainant_phone)
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ $complaint->complainant_phone }}
                                            </p>
                                        @endif
                                    </div>

                                </td>


                                {{-- CATEGORY --}}
                                <td class="px-5 py-4">

                                    <span class="inline-flex items-center px-2.5 py-1
                                                 rounded-lg bg-gray-100 text-gray-700
                                                 text-xs font-medium">

                                        {{ $complaint->category?->name ?? 'Uncategorized' }}

                                    </span>

                                </td>


                                {{-- STATUS --}}
                                <td class="px-5 py-4">

                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5
                                                 rounded-full border text-xs font-semibold
                                                 {{ $statusClasses }}">

                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                        {{ $complaint->status }}

                                    </span>

                                </td>


                                {{-- TECHNICIANS --}}
                                <td class="px-5 py-4">

                                    @if($complaint->technicians->count())

                                        <div class="space-y-2">

                                            @foreach($complaint->technicians as $technician)

                                                <div class="flex items-center gap-2">

                                                    <div class="w-7 h-7 rounded-full bg-blue-100
                                                                text-blue-700 flex items-center
                                                                justify-center text-[10px]
                                                                font-bold shrink-0">

                                                        {{ strtoupper(
                                                            substr($technician->full_name ?? '', 0, 1)
                                                            .
                                                            substr($technician->last_name ?? '', 0, 1)
                                                        ) }}

                                                    </div>

                                                    <div class="min-w-0">

                                                        <p class="text-sm font-medium text-gray-800 truncate max-w-[150px]">
                                                            {{ $technician->full_name }}
                                                        </p>

                                                        @if($technician->pivot?->assignment_role)
                                                            <p class="text-[10px] text-gray-400">
                                                                {{ $technician->pivot->assignment_role }}
                                                            </p>
                                                        @endif

                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>

                                    @else

                                        <span class="text-sm text-gray-400">
                                            Not assigned
                                        </span>

                                    @endif

                                </td>


                                {{-- UPDATED --}}
                                <td class="px-5 py-4 whitespace-nowrap">

                                    <p class="text-sm text-gray-700">
                                        {{ $complaint->updated_at?->format('M d, Y') }}
                                    </p>

                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $complaint->updated_at?->format('h:i A') }}
                                    </p>

                                </td>


                                {{-- ACTION --}}
                                <td class="px-5 py-4 text-right">

                                    <a
                                        href="{{ route('maintenance-manager.complaints.show', $complaint) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-2
                                               rounded-lg bg-gray-100 text-gray-700
                                               text-xs font-semibold hover:bg-blue-50
                                               hover:text-blue-700 transition"
                                    >

                                        View

                                        <svg class="w-4 h-4"
                                             fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M9 5l7 7-7 7"/>
                                        </svg>

                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- MOBILE / TABLET CARDS --}}
            <div class="lg:hidden divide-y divide-gray-100">

                @foreach($complaints as $complaint)

                    @php

                        $statusClasses = match($complaint->status) {

                            'Verified' =>
                                'bg-blue-50 text-blue-700 border-blue-100',

                            'Assigned' =>
                                'bg-indigo-50 text-indigo-700 border-indigo-100',

                            'In Progress' =>
                                'bg-amber-50 text-amber-700 border-amber-100',

                            'Completed' =>
                                'bg-green-50 text-green-700 border-green-100',

                            'Closed' =>
                                'bg-gray-100 text-gray-700 border-gray-200',

                            default =>
                                'bg-gray-50 text-gray-600 border-gray-100',
                        };

                    @endphp


                    <div class="p-5">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">

                                <p class="text-xs font-semibold text-blue-600">
                                    {{ $complaint->complaint_no }}
                                </p>

                                <h3 class="mt-1 font-bold text-gray-900 truncate">
                                    {{ $complaint->subject }}
                                </h3>

                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $complaint->created_at?->format('M d, Y h:i A') }}
                                </p>

                            </div>


                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1
                                         rounded-full border text-[11px] font-semibold
                                         shrink-0 {{ $statusClasses }}">

                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                {{ $complaint->status }}

                            </span>

                        </div>


                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- CONSUMER --}}
                            <div>

                                <p class="text-[11px] uppercase tracking-wide
                                          font-semibold text-gray-400">
                                    Consumer
                                </p>

                                <p class="mt-1 text-sm font-semibold text-gray-800">
                                    {{ $complaint->consumer?->full_name
                                        ?? $complaint->complainant_name
                                        ?? '—' }}
                                </p>

                                @if($complaint->consumer?->account_number)
                                    <p class="text-xs text-gray-500">
                                        {{ $complaint->consumer->account_number }}
                                    </p>
                                @endif

                            </div>


                            {{-- CATEGORY --}}
                            <div>

                                <p class="text-[11px] uppercase tracking-wide
                                          font-semibold text-gray-400">
                                    Category
                                </p>

                                <p class="mt-1 text-sm font-medium text-gray-700">
                                    {{ $complaint->category?->name ?? 'Uncategorized' }}
                                </p>

                            </div>

                        </div>


                        {{-- TEAM --}}
                        <div class="mt-4 p-3 rounded-xl bg-gray-50 border border-gray-100">

                            <p class="text-[11px] uppercase tracking-wide
                                      font-semibold text-gray-400">
                                Maintenance Team
                            </p>

                            @if($complaint->technicians->count())

                                <div class="mt-2 space-y-2">

                                    @foreach($complaint->technicians as $technician)

                                        <div class="flex items-center gap-2">

                                            <div class="w-7 h-7 rounded-full bg-blue-100
                                                        text-blue-700 flex items-center
                                                        justify-center text-[10px] font-bold">

                                                {{ strtoupper(
                                                    substr($technician->first_name ?? '', 0, 1)
                                                    .
                                                    substr($technician->last_name ?? '', 0, 1)
                                                ) }}

                                            </div>

                                            <div>
                                                <p class="text-sm font-medium text-gray-800">
                                                    {{ $technician->full_name }}
                                                </p>

                                                @if($technician->pivot?->assignment_role)
                                                    <p class="text-[10px] text-gray-400">
                                                        {{ $technician->pivot->assignment_role }}
                                                    </p>
                                                @endif
                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            @else

                                <p class="mt-1 text-sm text-gray-400">
                                    No technician assigned
                                </p>

                            @endif

                        </div>


                        <div class="mt-4 flex items-center justify-between">

                            <span class="text-xs text-gray-400">
                                Updated {{ $complaint->updated_at?->diffForHumans() }}
                            </span>

                            <a
                                href="{{ route('maintenance-manager.complaints.show', $complaint) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-2
                                       rounded-lg bg-blue-600 text-white text-xs
                                       font-semibold hover:bg-blue-700 transition"
                            >
                                View Details

                                <svg class="w-4 h-4"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M9 5l7 7-7 7"/>
                                </svg>

                            </a>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            {{-- EMPTY STATE --}}
            <div class="px-6 py-16 text-center">

                <div class="mx-auto w-16 h-16 rounded-2xl bg-gray-100
                            flex items-center justify-center">

                    <svg class="w-8 h-8 text-gray-400"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="1.5"
                              d="M8 10h8M8 14h5m-9 6h16a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>

                </div>

                <h3 class="mt-4 text-base font-semibold text-gray-900">
                    No complaints found
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Try adjusting your search or filters.
                </p>

            </div>

        @endif


        {{-- PAGINATION --}}
        @if($complaints->hasPages())

            <div class="px-5 py-4 border-t border-gray-100">
                {{ $complaints->withQueryString()->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
