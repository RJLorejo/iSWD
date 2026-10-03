@extends('customer-service.layouts.app')

@section('title', 'Complaint Verification')

@section('content')

    <div class="max-w-7xl mx-auto space-y-5">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div>

            <p class="text-sm font-semibold text-blue-600">
                Customer Service
            </p>

            <h1 class="text-2xl sm:text-3xl
                       font-bold text-gray-900 mt-1">

                Complaint Verification

            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Review complaints waiting for Customer Service verification.
            </p>

        </div>


        {{-- ========================================================= --}}
        {{-- MINIMAL STATISTIC --}}
        {{-- ========================================================= --}}

        <div class="max-w-sm">

            <div class="bg-white rounded-2xl
                        border border-gray-200 p-5">

                <div class="flex items-center
                            justify-between gap-4">

                    <div>

                        <p class="text-sm text-gray-500">
                            Awaiting Verification
                        </p>

                        <p class="text-3xl font-bold
                                  text-amber-600 mt-1">

                            {{ $pendingCount }}

                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            Complaints requiring review
                        </p>

                    </div>


                    <div
                        class="w-11 h-11 rounded-xl
                                bg-amber-50 text-amber-600
                                flex items-center
                                justify-center">

                        <i class="fas fa-shield-halved"></i>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- FILTERS --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl
                    border border-gray-200 shadow-sm">

            <form method="GET"
                action="{{ route('customer-service.complaint-verification.index') }}"
                class="p-4 sm:p-5">

                <div
                    class="grid grid-cols-1
                            sm:grid-cols-2
                            lg:grid-cols-3
                            xl:grid-cols-6 gap-3">

                    {{-- SEARCH --}}

                    <div class="relative sm:col-span-2">

                        <i
                            class="fas fa-magnifying-glass
                                  absolute left-3.5 top-1/2
                                  -translate-y-1/2
                                  text-gray-400"></i>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search complaint, consumer, type, division..."
                            class="w-full pl-10 pr-4 py-2.5
                                   rounded-xl border border-gray-200
                                   text-sm
                                   focus:ring-2 focus:ring-blue-500">

                    </div>


                    {{-- URGENCY --}}

                    <select name="urgency"
                        class="w-full px-3 py-2.5
                               rounded-xl border border-gray-200
                               text-sm text-gray-700">

                        <option value="">
                            All Urgency
                        </option>

                        <option value="High" @selected(request('urgency') === 'High')>
                            High
                        </option>

                        <option value="Moderate" @selected(request('urgency') === 'Moderate')>
                            Moderate
                        </option>

                        <option value="Low" @selected(request('urgency') === 'Low')>
                            Low
                        </option>

                        <option value="not_assessed" @selected(request('urgency') === 'not_assessed')>
                            Not Assessed
                        </option>

                    </select>

                    <input type="date" name="date_from" value="{{ request('date_from') }}" title="Date From"
                        class="w-full px-3 py-2.5
                               rounded-xl border border-gray-200
                               text-sm text-gray-700">


                    {{-- TO --}}

                    <input type="date" name="date_to" value="{{ request('date_to') }}" title="Date To"
                        class="w-full px-3 py-2.5
                               rounded-xl border border-gray-200
                               text-sm text-gray-700">

                </div>


                <div class="mt-3 flex flex-wrap gap-2">

                    <button type="submit"
                        class="inline-flex items-center
                               justify-center gap-2
                               px-4 py-2.5 rounded-xl
                               bg-blue-600 text-white
                               text-sm font-semibold
                               hover:bg-blue-700 transition">

                        <i class="fas fa-filter"></i>

                        Apply Filters

                    </button>


                    @if (request()->hasAny(['search', 'urgency', 'complaint_type', 'division_id', 'date_from', 'date_to']))
                        <a href="{{ route('customer-service.complaint-verification.index') }}"
                            class="inline-flex items-center
                                   justify-center gap-2
                                   px-4 py-2.5 rounded-xl
                                   bg-gray-100 text-gray-700
                                   text-sm font-semibold">

                            <i class="fas fa-xmark"></i>

                            Clear

                        </a>
                    @endif

                    {{-- NO PRINT HERE --}}

                </div>

            </form>

        </div>


        {{-- ========================================================= --}}
        {{-- VERIFICATION QUEUE --}}
        {{-- ========================================================= --}}

        <div
            class="bg-white rounded-2xl
                    border border-gray-200
                    shadow-sm overflow-hidden">

            <div
                class="px-5 py-4
                        border-b border-gray-100
                        flex items-center
                        justify-between gap-4">

                <div>

                    <h2 class="font-semibold text-gray-900">
                        Complaints Awaiting Verification
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Pending complaints requiring Customer Service review.
                    </p>

                </div>

                <span class="text-sm text-gray-500">
                    {{ $pendingComplaints->total() }}
                </span>

            </div>


            {{-- DESKTOP --}}

            <div class="hidden lg:block overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-50
                                  border-b border-gray-100">

                        <tr>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       text-gray-500">
                                Complaint
                            </th>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       text-gray-500">
                                Consumer
                            </th>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       text-gray-500">
                                Complaint Type
                            </th>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       text-gray-500">
                                Division
                            </th>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       text-gray-500">
                                Reported
                            </th>

                            <th
                                class="px-5 py-3 text-center
                                       text-xs font-semibold uppercase
                                       text-gray-500">
                                Review
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($pendingComplaints as $complaint)
                            @php

                                $urgency = $complaint->aiAnalysis?->urgency_level;

                                $urgencyClasses = match ($urgency) {
                                    'High' => 'bg-red-50 text-red-700 border-red-200',

                                    'Moderate' => 'bg-amber-50 text-amber-700 border-amber-200',

                                    'Low' => 'bg-green-50 text-green-700 border-green-200',

                                    default => 'bg-gray-50 text-gray-500 border-gray-200',
                                };

                            @endphp


                            <tr class="hover:bg-gray-50/70">

                                {{-- COMPLAINT --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="w-10 h-10
                                                    rounded-xl
                                                    bg-amber-50
                                                    text-amber-600
                                                    flex items-center
                                                    justify-center">

                                            <i class="fas fa-shield-halved"></i>

                                        </div>


                                        <div>

                                            <div
                                                class="flex flex-wrap
                                                        items-center gap-2">

                                                <p
                                                    class="text-sm font-bold
                                                          text-blue-600">

                                                    {{ $complaint->complaint_no }}

                                                </p>


                                                @if ($urgency)
                                                    <span
                                                        class="inline-flex
                                                               items-center gap-1
                                                               px-2 py-0.5
                                                               rounded-full border
                                                               text-[10px] font-bold
                                                               {{ $urgencyClasses }}">

                                                        <span
                                                            class="w-1.5 h-1.5
                                                                   rounded-full
                                                                   bg-current">
                                                        </span>

                                                        {{ strtoupper($urgency) }}

                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex
                                                               px-2 py-0.5
                                                               rounded-full border
                                                               bg-gray-50
                                                               border-gray-200
                                                               text-[10px]
                                                               font-semibold
                                                               text-gray-400">

                                                        NOT ASSESSED

                                                    </span>
                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- CONSUMER --}}

                                <td class="px-5 py-4">

                                    <p class="font-medium text-gray-900">

                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? '—') }}

                                    </p>


                                    @if ($complaint->consumer?->account_number)
                                        <p
                                            class="text-xs
                                                  text-gray-500 mt-1">

                                            Account:
                                            {{ $complaint->consumer->account_number }}

                                        </p>
                                    @endif

                                </td>


                                {{-- TYPE --}}

                                <td class="px-5 py-4">

                                    <span
                                        class="inline-flex
                                                 px-2.5 py-1
                                                 rounded-lg
                                                 bg-gray-100
                                                 text-gray-700
                                                 text-xs font-medium">

                                        {{ $complaint->category?->name ?? 'Uncategorized' }}

                                    </span>

                                </td>


                                {{-- DIVISION --}}

                                <td class="px-5 py-4">

                                    <p
                                        class="text-sm font-medium
                                              text-gray-700">

                                        {{ $complaint->division?->name ?? '—' }}

                                    </p>

                                </td>


                                {{-- REPORTED --}}

                                <td class="px-5 py-4">

                                    <p class="text-sm text-gray-700">

                                        {{ $complaint->created_at?->format('M d, Y') }}

                                    </p>

                                    <p class="text-xs text-gray-400 mt-1">

                                        {{ $complaint->created_at?->format('h:i A') }}

                                    </p>

                                </td>


                                {{-- REVIEW --}}

                                <td class="px-5 py-4 text-center">

                                    <a href="{{ route('customer-service.complaints.show', $complaint) }}"
                                        title="Review Complaint"
                                        class="inline-flex w-9 h-9
                                               items-center justify-center
                                               rounded-lg
                                               bg-blue-50 text-blue-600
                                               border border-blue-100
                                               hover:bg-blue-600
                                               hover:text-white
                                               transition">

                                        <i class="fas fa-eye"></i>

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="px-6 py-16 text-center">

                                    <div
                                        class="w-14 h-14 mx-auto
                                                rounded-2xl
                                                bg-green-50
                                                text-green-600
                                                flex items-center
                                                justify-center">

                                        <i
                                            class="fas fa-circle-check
                                                  text-xl"></i>

                                    </div>

                                    <h3
                                        class="font-semibold
                                               text-gray-900 mt-4">

                                        No complaints awaiting verification

                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">

                                        There are currently no pending
                                        complaints matching your filters.

                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- MOBILE --}}

            <div class="lg:hidden divide-y divide-gray-100">

                @forelse ($pendingComplaints as $complaint)
                    @php

                        $urgency = $complaint->aiAnalysis?->urgency_level;

                        $urgencyClasses = match ($urgency) {
                            'High' => 'bg-red-50 text-red-700 border-red-200',

                            'Moderate' => 'bg-amber-50 text-amber-700 border-amber-200',

                            'Low' => 'bg-green-50 text-green-700 border-green-200',

                            default => 'bg-gray-50 text-gray-500 border-gray-200',
                        };

                    @endphp


                    <div class="p-4 sm:p-5">

                        <div class="flex items-start
                                    justify-between gap-3">

                            <div>

                                <div class="flex flex-wrap
                                            items-center gap-2">

                                    <p class="font-bold text-blue-600">

                                        {{ $complaint->complaint_no }}

                                    </p>


                                    @if ($urgency)
                                        <span
                                            class="inline-flex items-center
                                                   px-2 py-0.5
                                                   rounded-full border
                                                   text-[10px] font-bold
                                                   {{ $urgencyClasses }}">

                                            {{ strtoupper($urgency) }}

                                        </span>
                                    @endif

                                </div>


                                <p class="text-xs text-gray-400 mt-1">

                                    {{ $complaint->created_at?->format('M d, Y h:i A') }}

                                </p>

                            </div>


                            <span
                                class="inline-flex
                                         px-2.5 py-1
                                         rounded-full
                                         bg-amber-50
                                         border border-amber-100
                                         text-[10px]
                                         font-semibold
                                         text-amber-700">

                                PENDING

                            </span>

                        </div>


                        <div class="mt-4 grid grid-cols-2 gap-4">

                            <div>

                                <p
                                    class="text-[10px] uppercase
                                          font-semibold text-gray-400">
                                    Consumer
                                </p>

                                <p
                                    class="text-sm font-semibold
                                          text-gray-800 mt-1">

                                    {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? '—') }}

                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-[10px] uppercase
                                          font-semibold text-gray-400">
                                    Complaint Type
                                </p>

                                <p class="text-sm text-gray-700 mt-1">

                                    {{ $complaint->category?->name ?? 'Uncategorized' }}

                                </p>

                            </div>

                        </div>


                        <div
                            class="mt-4 p-3 rounded-xl
                                    bg-gray-50 border border-gray-100">

                            <p
                                class="text-[10px] uppercase
                                      font-semibold text-gray-400">
                                Division
                            </p>

                            <p class="text-sm font-medium
                                      text-gray-700 mt-1">

                                {{ $complaint->division?->name ?? '—' }}

                            </p>

                        </div>


                        <div class="mt-4 flex items-center
                                    justify-between">

                            <span class="text-xs text-gray-400">

                                Reported
                                {{ $complaint->created_at?->diffForHumans() }}

                            </span>


                            <a href="{{ route('customer-service.complaints.show', $complaint) }}"
                                title="Review Complaint"
                                class="inline-flex w-9 h-9
                                       items-center justify-center
                                       rounded-lg
                                       bg-blue-50 text-blue-600
                                       border border-blue-100">

                                <i class="fas fa-eye"></i>

                            </a>

                        </div>

                    </div>

                @empty

                    <div class="p-12 text-center">

                        <i class="fas fa-circle-check
                                  text-3xl text-green-500"></i>

                        <p class="font-semibold text-gray-900 mt-3">
                            No complaints awaiting verification
                        </p>

                    </div>
                @endforelse

            </div>


            @if ($pendingComplaints->hasPages())
                <div class="px-5 py-4
                            border-t border-gray-100">

                    {{ $pendingComplaints->links() }}

                </div>
            @endif

        </div>

    </div>

@endsection
