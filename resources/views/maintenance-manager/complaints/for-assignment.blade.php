@extends('maintenance-manager.layouts.app')

@section('title', 'For Assignment')

@section('content')

    <div class="max-w-7xl mx-auto space-y-6">


        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div
            class="flex flex-col gap-4
                   lg:flex-row lg:items-center
                   lg:justify-between">

            <div>

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-12 w-12 shrink-0
                               items-center justify-center
                               rounded-2xl bg-blue-100
                               text-blue-700">

                        <i class="fas fa-user-plus text-lg"></i>

                    </div>


                    <div>

                        <h1
                            class="text-2xl font-bold
                                   text-gray-900
                                   sm:text-3xl">

                            For Assignment

                        </h1>

                        <p class="mt-1 text-sm
                                   text-gray-500">

                            Verified Engineering complaints waiting
                            for maintenance team assignment.

                        </p>

                    </div>

                </div>

            </div>


            <div class="flex flex-wrap items-center gap-2">

                <a href="{{ route('maintenance-manager.complaints.index') }}"
                    class="inline-flex items-center gap-2
                           rounded-xl border border-gray-200
                           bg-white px-4 py-2.5
                           text-sm font-semibold text-gray-700
                           transition hover:bg-gray-50">

                    <i class="fas fa-list"></i>

                    All Complaints

                </a>


                <a href="{{ route('maintenance-manager.complaints.for-assignment') }}"
                    class="inline-flex items-center gap-2
                           rounded-xl bg-blue-600
                           px-4 py-2.5
                           text-sm font-semibold text-white
                           transition hover:bg-blue-700">

                    <i class="fas fa-rotate"></i>

                    Refresh

                </a>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-2 gap-4
                   lg:grid-cols-4">


            {{-- TOTAL --}}

            <div
                class="rounded-2xl border
                       border-gray-200 bg-white
                       p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>

                        <p
                            class="text-xs font-semibold
                                   uppercase tracking-wide
                                   text-gray-500">

                            Waiting

                        </p>

                        <p class="mt-2 text-2xl
                                   font-bold text-gray-900">

                            {{ $totalForAssignment ?? 0 }}

                        </p>

                        <p class="mt-1 text-xs
                                   text-gray-400">

                            For assignment

                        </p>

                    </div>


                    <div
                        class="flex h-11 w-11
                               items-center justify-center
                               rounded-xl bg-blue-50
                               text-blue-600">

                        <i class="fas fa-clipboard-list"></i>

                    </div>

                </div>

            </div>



            {{-- HIGH --}}

            <div
                class="rounded-2xl border
                       border-gray-200 bg-white
                       p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>

                        <p
                            class="text-xs font-semibold
                                   uppercase tracking-wide
                                   text-gray-500">

                            High Urgency

                        </p>

                        <p class="mt-2 text-2xl
                                   font-bold text-red-600">

                            {{ $highUrgencyCount ?? 0 }}

                        </p>

                        <p class="mt-1 text-xs
                                   text-gray-400">

                            Needs attention

                        </p>

                    </div>


                    <div
                        class="flex h-11 w-11
                               items-center justify-center
                               rounded-xl bg-red-50
                               text-red-600">

                        <i class="fas fa-triangle-exclamation"></i>

                    </div>

                </div>

            </div>



            {{-- MODERATE --}}

            <div
                class="rounded-2xl border
                       border-gray-200 bg-white
                       p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>

                        <p
                            class="text-xs font-semibold
                                   uppercase tracking-wide
                                   text-gray-500">

                            Moderate

                        </p>

                        <p class="mt-2 text-2xl
                                   font-bold text-amber-600">

                            {{ $moderateUrgencyCount ?? 0 }}

                        </p>

                        <p class="mt-1 text-xs
                                   text-gray-400">

                            Normal priority

                        </p>

                    </div>


                    <div
                        class="flex h-11 w-11
                               items-center justify-center
                               rounded-xl bg-amber-50
                               text-amber-600">

                        <i class="fas fa-clock"></i>

                    </div>

                </div>

            </div>



            {{-- LOW --}}

            <div
                class="rounded-2xl border
                       border-gray-200 bg-white
                       p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>

                        <p
                            class="text-xs font-semibold
                                   uppercase tracking-wide
                                   text-gray-500">

                            Low Urgency

                        </p>

                        <p class="mt-2 text-2xl
                                   font-bold text-green-600">

                            {{ $lowUrgencyCount ?? 0 }}

                        </p>

                        <p class="mt-1 text-xs
                                   text-gray-400">

                            Standard queue

                        </p>

                    </div>


                    <div
                        class="flex h-11 w-11
                               items-center justify-center
                               rounded-xl bg-green-50
                               text-green-600">

                        <i class="fas fa-circle-check"></i>

                    </div>

                </div>

            </div>

        </div>



        {{-- ========================================================= --}}
        {{-- FILTERS --}}
        {{-- ========================================================= --}}

        <div class="rounded-2xl border
                   border-gray-200 bg-white
                   shadow-sm">

            <form method="GET" action="{{ route('maintenance-manager.complaints.for-assignment') }}" class="p-4 sm:p-5">


                <div
                    class="grid grid-cols-1 gap-3
                           md:grid-cols-2
                           lg:grid-cols-5">


                    {{-- SEARCH --}}

                    <div class="relative lg:col-span-2">

                        <i
                            class="fas fa-magnifying-glass
                                   absolute left-3 top-1/2
                                   -translate-y-1/2
                                   text-sm text-gray-400">
                        </i>


                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search complaint, consumer, account..."
                            class="w-full rounded-xl
                                   border border-gray-200
                                   py-2.5 pl-10 pr-4
                                   text-sm
                                   focus:border-blue-500
                                   focus:ring-2
                                   focus:ring-blue-500">

                    </div>



                    {{-- URGENCY --}}

                    <div>

                        <select name="urgency"
                            class="w-full rounded-xl
                                   border border-gray-200
                                   px-3 py-2.5
                                   text-sm text-gray-700
                                   focus:border-blue-500
                                   focus:ring-2
                                   focus:ring-blue-500">

                            <option value="">
                                All Urgency
                            </option>

                            <option value="High"
                                {{ request('urgency') === 'High' ? 'selected' : '' }}>

                                High

                            </option>

                            <option value="Moderate"
                                {{ request('urgency') === 'Moderate' ? 'selected' : '' }}>

                                Moderate

                            </option>

                            <option value="Low"
                                {{ request('urgency') === 'Low' ? 'selected' : '' }}>

                                Low

                            </option>

                        </select>

                    </div>



                    {{-- DATE FROM --}}

                    <div>

                        <input type="date" name="date_from" value="{{ request('date_from') }}" title="Verified from"
                            class="w-full rounded-xl
                                   border border-gray-200
                                   px-3 py-2.5
                                   text-sm text-gray-700
                                   focus:border-blue-500
                                   focus:ring-2
                                   focus:ring-blue-500">

                    </div>



                    {{-- DATE TO --}}

                    <div>

                        <input type="date" name="date_to" value="{{ request('date_to') }}" title="Verified to"
                            class="w-full rounded-xl
                                   border border-gray-200
                                   px-3 py-2.5
                                   text-sm text-gray-700
                                   focus:border-blue-500
                                   focus:ring-2
                                   focus:ring-blue-500">

                    </div>

                </div>



                <div class="mt-3 flex flex-wrap gap-2">

                    <button type="submit"
                        class="inline-flex items-center
                               justify-center gap-2
                               rounded-xl bg-blue-600
                               px-4 py-2.5
                               text-sm font-semibold
                               text-white transition
                               hover:bg-blue-700">

                        <i class="fas fa-filter"></i>

                        Apply Filters

                    </button>


                    @if (request()->hasAny(['search', 'urgency', 'date_from', 'date_to']))
                        <a href="{{ route('maintenance-manager.complaints.for-assignment') }}"
                            class="inline-flex items-center
                                   justify-center rounded-xl
                                   bg-gray-100 px-4 py-2.5
                                   text-sm font-semibold
                                   text-gray-700 transition
                                   hover:bg-gray-200">

                            Clear

                        </a>
                    @endif

                </div>

            </form>

        </div>



        {{-- ========================================================= --}}
        {{-- ASSIGNMENT QUEUE --}}
        {{-- ========================================================= --}}

        <div
            class="overflow-hidden rounded-2xl
                   border border-gray-200
                   bg-white shadow-sm">


            {{-- TABLE HEADER --}}

            <div
                class="flex flex-col gap-2
                       border-b border-gray-100
                       px-5 py-4
                       sm:flex-row sm:items-center
                       sm:justify-between">

                <div>

                    <h2 class="font-bold text-gray-900">
                        Engineering Complaints
                    </h2>

                    <p class="mt-1 text-xs text-gray-500">
                        Verified complaints waiting for a maintenance team
                    </p>

                </div>


                <span class="text-sm text-gray-500">

                    {{ $complaints->total() }}
                    {{ $complaints->total() === 1 ? 'complaint' : 'complaints' }}

                </span>

            </div>



            @if ($complaints->count())


                {{-- ================================================= --}}
                {{-- DESKTOP TABLE --}}
                {{-- ================================================= --}}

                <div class="hidden overflow-x-auto lg:block">

                    <table class="min-w-full">

                        <thead
                            class="border-b
                                   border-gray-100
                                   bg-gray-50">

                            <tr>

                                <th
                                    class="px-5 py-3 text-left
                                           text-xs font-semibold
                                           uppercase tracking-wider
                                           text-gray-500">

                                    Complaint

                                </th>

                                <th
                                    class="px-5 py-3 text-left
                                           text-xs font-semibold
                                           uppercase tracking-wider
                                           text-gray-500">

                                    Consumer

                                </th>

                                <th
                                    class="px-5 py-3 text-left
                                           text-xs font-semibold
                                           uppercase tracking-wider
                                           text-gray-500">

                                    Complaint Type

                                </th>

                                <th
                                    class="px-5 py-3 text-left
                                           text-xs font-semibold
                                           uppercase tracking-wider
                                           text-gray-500">

                                    Urgency

                                </th>

                                <th
                                    class="px-5 py-3 text-left
                                           text-xs font-semibold
                                           uppercase tracking-wider
                                           text-gray-500">

                                    Location

                                </th>

                                <th
                                    class="px-5 py-3 text-left
                                           text-xs font-semibold
                                           uppercase tracking-wider
                                           text-gray-500">

                                    Verified

                                </th>

                                <th
                                    class="px-5 py-3 text-right
                                           text-xs font-semibold
                                           uppercase tracking-wider
                                           text-gray-500">

                                    Action

                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @foreach ($complaints as $complaint)
                                @php

                                    $urgency = $complaint->aiAnalysis?->urgency_level ?? 'Not Analyzed';

                                    $urgencyClasses = match ($urgency) {
                                        'High' => 'bg-red-50 text-red-700 border-red-200',

                                        'Moderate' => 'bg-amber-50 text-amber-700 border-amber-200',

                                        'Low' => 'bg-green-50 text-green-700 border-green-200',

                                        default => 'bg-gray-50 text-gray-600 border-gray-200',
                                    };

                                @endphp


                                <tr class="transition
                                           hover:bg-gray-50/70">


                                    {{-- COMPLAINT --}}

                                    <td class="px-5 py-4">

                                        <div class="flex items-start gap-3">

                                            <div
                                                class="flex h-10 w-10
                                                       shrink-0 items-center
                                                       justify-center
                                                       rounded-xl bg-blue-50
                                                       text-blue-600">

                                                <i
                                                    class="fas
                                                           fa-file-circle-exclamation">
                                                </i>

                                            </div>


                                            <div class="min-w-0">

                                                <p
                                                    class="text-sm font-bold
                                                           text-gray-900">

                                                    {{ $complaint->complaint_no }}

                                                </p>

                                                <p
                                                    class="mt-1 max-w-[220px]
                                                           truncate text-xs
                                                           text-gray-500">

                                                    {{ $complaint->description }}

                                                </p>

                                            </div>

                                        </div>

                                    </td>



                                    {{-- CONSUMER --}}

                                    <td class="px-5 py-4">

                                        <p
                                            class="font-medium
                                                   text-gray-900">

                                            {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? '—') }}

                                        </p>


                                        @if ($complaint->consumer?->account_number)
                                            <p
                                                class="mt-0.5
                                                       text-xs text-gray-500">

                                                Account:
                                                {{ $complaint->consumer->account_number }}

                                            </p>
                                        @endif

                                    </td>



                                    {{-- TYPE --}}

                                    <td class="px-5 py-4">

                                        <div class="space-y-1.5">

                                            <span
                                                class="inline-flex
                                                       rounded-lg
                                                       bg-gray-100
                                                       px-2.5 py-1
                                                       text-xs font-medium
                                                       text-gray-700">

                                                {{ $complaint->category?->name ?? 'Uncategorized' }}

                                            </span>


                                            <div>

                                                <span
                                                    class="inline-flex
                                                           rounded-lg
                                                           bg-blue-50
                                                           px-2.5 py-1
                                                           text-xs font-medium
                                                           text-blue-700">

                                                    Engineering

                                                </span>

                                            </div>

                                        </div>

                                    </td>



                                    {{-- URGENCY --}}

                                    <td class="px-5 py-4">

                                        <span
                                            class="inline-flex
                                                   items-center gap-1.5
                                                   rounded-full border
                                                   px-2.5 py-1.5
                                                   text-xs font-semibold
                                                   {{ $urgencyClasses }}">

                                            <span
                                                class="h-1.5 w-1.5
                                                       rounded-full
                                                       bg-current">
                                            </span>

                                            {{ $urgency }}

                                        </span>

                                    </td>



                                    {{-- LOCATION --}}

                                    <td class="px-5 py-4">

                                        <div class="max-w-[220px]">

                                            <p
                                                class="line-clamp-2
                                                       text-sm
                                                       text-gray-700">

                                                {{ $complaint->address ?: 'No address recorded' }}

                                            </p>


                                            @if ($complaint->latitude !== null && $complaint->longitude !== null)
                                                <p
                                                    class="mt-1 text-[11px]
                                                           font-medium
                                                           text-green-600">

                                                    <i
                                                        class="fas
                                                               fa-location-dot
                                                               mr-1">
                                                    </i>

                                                    Location available

                                                </p>
                                            @else
                                                <p
                                                    class="mt-1 text-[11px]
                                                           text-gray-400">

                                                    No map location

                                                </p>
                                            @endif

                                        </div>

                                    </td>



                                    {{-- VERIFIED --}}

                                    <td class="whitespace-nowrap
                                               px-5 py-4">

                                        <p
                                            class="text-sm
                                                   text-gray-700">

                                            {{ $complaint->verified_at?->format('M d, Y') ?? '—' }}

                                        </p>

                                        <p
                                            class="mt-0.5
                                                   text-xs text-gray-400">

                                            {{ $complaint->verified_at?->format('h:i A') ?? '' }}

                                        </p>

                                    </td>



                                    {{-- ACTION --}}

                                    <td class="px-5 py-4
                                               text-right">

                                        <a href="{{ route('maintenance-manager.complaints.show', $complaint) }}"
                                            class="inline-flex
                                                   items-center gap-2
                                                   rounded-xl
                                                   bg-blue-600
                                                   px-3.5 py-2.5
                                                   text-xs font-semibold
                                                   text-white transition
                                                   hover:bg-blue-700">

                                            <i
                                                class="fas
                                                       fa-user-plus">
                                            </i>

                                            Review & Assign

                                        </a>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>



                {{-- ================================================= --}}
                {{-- MOBILE / TABLET --}}
                {{-- ================================================= --}}

                <div class="divide-y divide-gray-100
                           lg:hidden">

                    @foreach ($complaints as $complaint)
                        @php

                            $urgency = $complaint->aiAnalysis?->urgency_level ?? 'Not Analyzed';

                            $urgencyClasses = match ($urgency) {
                                'High' => 'bg-red-50 text-red-700 border-red-200',

                                'Moderate' => 'bg-amber-50 text-amber-700 border-amber-200',

                                'Low' => 'bg-green-50 text-green-700 border-green-200',

                                default => 'bg-gray-50 text-gray-600 border-gray-200',
                            };

                        @endphp


                        <div class="p-5">


                            {{-- TOP --}}

                            <div class="flex items-start
                                       justify-between gap-3">

                                <div class="min-w-0">

                                    <p
                                        class="text-sm font-bold
                                               text-blue-600">

                                        {{ $complaint->complaint_no }}

                                    </p>

                                    <p class="mt-1 text-xs
                                               text-gray-400">

                                        Verified
                                        {{ $complaint->verified_at?->format('M d, Y h:i A') ?? '—' }}

                                    </p>

                                </div>


                                <span
                                    class="inline-flex shrink-0
                                           items-center gap-1.5
                                           rounded-full border
                                           px-2.5 py-1
                                           text-[11px]
                                           font-semibold
                                           {{ $urgencyClasses }}">

                                    <span
                                        class="h-1.5 w-1.5
                                               rounded-full
                                               bg-current">
                                    </span>

                                    {{ $urgency }}

                                </span>

                            </div>



                            {{-- INFORMATION --}}

                            <div
                                class="mt-4 grid
                                       grid-cols-1 gap-4
                                       sm:grid-cols-2">


                                {{-- CONSUMER --}}

                                <div>

                                    <p
                                        class="text-[11px]
                                               font-semibold uppercase
                                               tracking-wide
                                               text-gray-400">

                                        Consumer

                                    </p>

                                    <p
                                        class="mt-1 text-sm
                                               font-semibold
                                               text-gray-800">

                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? '—') }}

                                    </p>


                                    @if ($complaint->consumer?->account_number)
                                        <p
                                            class="text-xs
                                                   text-gray-500">

                                            {{ $complaint->consumer->account_number }}

                                        </p>
                                    @endif

                                </div>



                                {{-- TYPE --}}

                                <div>

                                    <p
                                        class="text-[11px]
                                               font-semibold uppercase
                                               tracking-wide
                                               text-gray-400">

                                        Complaint Type

                                    </p>

                                    <p
                                        class="mt-1 text-sm
                                               font-medium
                                               text-gray-700">

                                        {{ $complaint->category?->name ?? 'Uncategorized' }}

                                    </p>

                                    <p
                                        class="mt-0.5 text-xs
                                               font-medium
                                               text-blue-600">

                                        Engineering

                                    </p>

                                </div>

                            </div>



                            {{-- DESCRIPTION --}}

                            <div
                                class="mt-4 rounded-xl
                                       border border-gray-100
                                       bg-gray-50 p-3">

                                <p
                                    class="text-[11px]
                                           font-semibold uppercase
                                           tracking-wide
                                           text-gray-400">

                                    Complaint

                                </p>

                                <p
                                    class="mt-1 line-clamp-2
                                           text-sm text-gray-700">

                                    {{ $complaint->description }}

                                </p>

                            </div>



                            {{-- LOCATION --}}

                            <div class="mt-4">

                                <p
                                    class="text-[11px]
                                           font-semibold uppercase
                                           tracking-wide
                                           text-gray-400">

                                    Location

                                </p>

                                <p class="mt-1 text-sm
                                           text-gray-700">

                                    {{ $complaint->address ?: 'No address recorded' }}

                                </p>


                                @if ($complaint->latitude !== null && $complaint->longitude !== null)
                                    <p
                                        class="mt-1 text-xs
                                               font-medium
                                               text-green-600">

                                        <i
                                            class="fas
                                                   fa-location-dot
                                                   mr-1">
                                        </i>

                                        Map location available

                                    </p>
                                @endif

                            </div>



                            {{-- ACTION --}}

                            <div class="mt-5 flex
                                       items-center justify-end">

                                <a href="{{ route('maintenance-manager.complaints.show', $complaint) }}"
                                    class="inline-flex
                                           items-center gap-2
                                           rounded-xl bg-blue-600
                                           px-4 py-2.5
                                           text-sm font-semibold
                                           text-white transition
                                           hover:bg-blue-700">

                                    <i class="fas fa-user-plus"></i>

                                    Review & Assign

                                </a>

                            </div>

                        </div>
                    @endforeach

                </div>
            @else
                {{-- ================================================= --}}
                {{-- EMPTY STATE --}}
                {{-- ================================================= --}}

                <div class="px-6 py-16 text-center">

                    <div
                        class="mx-auto flex h-16 w-16
                               items-center justify-center
                               rounded-2xl bg-green-50
                               text-green-600">

                        <i class="fas fa-circle-check
                                   text-2xl">
                        </i>

                    </div>


                    <h3 class="mt-4 text-base
                               font-semibold text-gray-900">

                        No complaints waiting for assignment

                    </h3>


                    <p class="mx-auto mt-1 max-w-md
                               text-sm text-gray-500">

                        There are currently no verified Engineering
                        complaints waiting for a maintenance team.

                    </p>


                    @if (request()->hasAny(['search', 'urgency', 'date_from', 'date_to']))
                        <a href="{{ route('maintenance-manager.complaints.for-assignment') }}"
                            class="mt-4 inline-flex
                                   items-center gap-2
                                   rounded-xl bg-gray-100
                                   px-4 py-2.5
                                   text-sm font-semibold
                                   text-gray-700 transition
                                   hover:bg-gray-200">

                            <i class="fas fa-rotate-left"></i>

                            Clear Filters

                        </a>
                    @endif

                </div>

            @endif


            @if ($complaints->hasPages())
                <div class="border-t border-gray-100
                           px-5 py-4">

                    {{ $complaints->withQueryString()->links() }}

                </div>
            @endif

        </div>

    </div>

@endsection
