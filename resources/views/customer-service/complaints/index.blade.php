@extends('customer-service.layouts.app')

@section('title', 'Complaints')

@section('content')

    <div class="max-w-7xl mx-auto space-y-5">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col sm:flex-row
                    sm:items-center sm:justify-between gap-4">

            <div>

                <p class="text-sm font-semibold text-blue-600">
                    Customer Service
                </p>

                <h1 class="text-2xl sm:text-3xl
                           font-bold text-gray-900 mt-1">

                    Complaints

                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Monitor all consumer complaints recorded in the system.
                </p>

            </div>


            <a href="{{ route('customer-service.complaints.create') }}"
                class="inline-flex items-center justify-center
                       gap-2 px-4 py-2.5 rounded-xl
                       bg-blue-600 text-white
                       text-sm font-semibold
                       hover:bg-blue-700 transition">

                <i class="fas fa-plus"></i>

                New Complaint

            </a>

        </div>


        {{-- ========================================================= --}}
        {{-- FLASH --}}
        {{-- ========================================================= --}}

        @if (session('success'))
            <div class="rounded-xl border border-green-200
                        bg-green-50 px-4 py-3">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-check
                              text-green-600 mt-0.5"></i>

                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>

                </div>

            </div>
        @endif


        @if (session('error'))
            <div class="rounded-xl border border-red-200
                        bg-red-50 px-4 py-3">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-exclamation
                              text-red-600 mt-0.5"></i>

                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </p>

                </div>

            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- MINIMAL STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-3 gap-2 sm:gap-4">

            {{-- TOTAL --}}

            <div class="bg-white rounded-xl sm:rounded-2xl
                        border border-gray-200 p-3 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-[10px] sm:text-sm text-gray-500">
                            Total
                        </p>

                        <p class="text-xl sm:text-3xl
                                  font-bold text-gray-900 mt-1">

                            {{ $totalComplaints }}

                        </p>

                    </div>

                    <div
                        class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-blue-50 text-blue-600
                                items-center justify-center">

                        <i class="fas fa-file-lines"></i>

                    </div>

                </div>

            </div>


            {{-- PENDING --}}

            <div class="bg-white rounded-xl sm:rounded-2xl
                        border border-gray-200 p-3 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-[10px] sm:text-sm text-gray-500">
                            Pending
                        </p>

                        <p class="text-xl sm:text-3xl
                                  font-bold text-amber-600 mt-1">

                            {{ $pendingCount }}

                        </p>

                    </div>

                    <div
                        class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-amber-50 text-amber-600
                                items-center justify-center">

                        <i class="fas fa-clock"></i>

                    </div>

                </div>

            </div>


            {{-- ACTIVE --}}

            <div class="bg-white rounded-xl sm:rounded-2xl
                        border border-gray-200 p-3 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-[10px] sm:text-sm text-gray-500">
                            Active
                        </p>

                        <p class="text-xl sm:text-3xl
                                  font-bold text-indigo-600 mt-1">

                            {{ $activeCount }}

                        </p>

                    </div>

                    <div
                        class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-indigo-50 text-indigo-600
                                items-center justify-center">

                        <i class="fas fa-spinner"></i>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- FILTERS --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl
                    border border-gray-200 shadow-sm">

            <form method="GET" action="{{ route('customer-service.complaints.index') }}" class="p-4 sm:p-5">

                <div class="grid grid-cols-1
            sm:grid-cols-2
            lg:grid-cols-6 gap-3">

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
                                   focus:ring-2 focus:ring-blue-500
                                   focus:border-blue-500">

                    </div>

                    {{-- STATUS / WORKFLOW STAGE --}}
                    <select name="status"
                        class="w-full px-3 py-2.5
           rounded-xl border border-gray-200
           text-sm text-gray-700
           focus:ring-2 focus:ring-blue-500">

                        <option value="">
                            All Statuses
                        </option>

                        <option value="Pending" @selected(request('status') === 'Pending')>
                            Pending
                        </option>

                        <option value="Verified" @selected(request('status') === 'Verified')>
                            Verified
                        </option>

                        <option value="CS Processing" @selected(request('status') === 'CS Processing')>
                            CS Processing
                        </option>

                        <option value="Initial Processing Completed" @selected(request('status') === 'Initial Processing Completed')>
                            Initial Processing Completed
                        </option>

                        <option value="For Maintenance" @selected(request('status') === 'For Maintenance')>
                            For Maintenance
                        </option>

                        <option value="Assigned" @selected(request('status') === 'Assigned')>
                            Assigned
                        </option>

                        <option value="In Progress" @selected(request('status') === 'In Progress')>
                            In Progress
                        </option>

                        <option value="Completed" @selected(request('status') === 'Completed')>
                            Accomplished
                        </option>

                        <option value="Closed" @selected(request('status') === 'Closed')>
                            Closed
                        </option>

                        <option value="Rejected" @selected(request('status') === 'Rejected')>
                            Rejected
                        </option>

                    </select>


                    {{-- URGENCY --}}

                    <select name="urgency"
                        class="w-full px-3 py-2.5
                               rounded-xl border border-gray-200
                               text-sm text-gray-700
                               focus:ring-2 focus:ring-blue-500">

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
                               text-sm text-gray-700
                               focus:ring-2 focus:ring-blue-500">


                    {{-- TO --}}

                    <input type="date" name="date_to" value="{{ request('date_to') }}" title="Date To"
                        class="w-full px-3 py-2.5
                               rounded-xl border border-gray-200
                               text-sm text-gray-700
                               focus:ring-2 focus:ring-blue-500">

                </div>


                {{-- FILTER ACTIONS --}}

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


                    @if (request()->hasAny(['search', 'status', 'urgency', 'date_from', 'date_to']))
                        <a href="{{ route('customer-service.complaints.index') }}"
                            class="inline-flex items-center
                                   justify-center gap-2
                                   px-4 py-2.5 rounded-xl
                                   bg-gray-100 text-gray-700
                                   text-sm font-semibold
                                   hover:bg-gray-200 transition">

                            <i class="fas fa-xmark"></i>

                            Clear

                        </a>
                    @endif


                    {{-- PRINT ONLY HERE --}}

                    <a href="{{ route(
                        'customer-service.complaints.print-report',
                        request()->only(['search', 'status', 'urgency', 'date_from', 'date_to']),
                    ) }}"
                        target="_blank"
                        class="inline-flex items-center
                               justify-center gap-2
                               px-4 py-2.5 rounded-xl
                               bg-slate-100 text-slate-700
                               text-sm font-semibold
                               hover:bg-slate-200 transition">

                        <i class="fas fa-print"></i>

                        Print Report

                    </a>

                </div>

            </form>

        </div>


        {{-- ========================================================= --}}
        {{-- COMPLAINT RECORDS --}}
        {{-- ========================================================= --}}

        <div
            class="bg-white rounded-2xl
                    border border-gray-200
                    shadow-sm overflow-hidden">

            <div
                class="px-5 py-4
                        border-b border-gray-100
                        flex flex-col sm:flex-row
                        sm:items-center
                        sm:justify-between gap-2">

                <div>

                    <h2 class="font-semibold text-gray-900">
                        Complaint Records
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        All consumer complaints currently recorded in the system.
                    </p>

                </div>

                <span class="text-sm text-gray-500">
                    {{ $complaints->total() }}
                    {{ \Illuminate\Support\Str::plural('complaint', $complaints->total()) }}
                </span>

            </div>


            {{-- ===================================================== --}}
            {{-- DESKTOP --}}
            {{-- ===================================================== --}}

            <div class="hidden lg:block overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-50
                                  border-b border-gray-100">

                        <tr>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       tracking-wider text-gray-500">
                                Complaint
                            </th>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       tracking-wider text-gray-500">
                                Consumer
                            </th>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       tracking-wider text-gray-500">
                                Complaint Type
                            </th>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       tracking-wider text-gray-500">
                                Division
                            </th>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       tracking-wider text-gray-500">
                                Status
                            </th>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       tracking-wider text-gray-500">
                                Reported
                            </th>

                            <th
                                class="px-5 py-3 text-center
                                       text-xs font-semibold uppercase
                                       tracking-wider text-gray-500">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($complaints as $complaint)

                            @php

                                $statusClasses = match ($complaint->status) {
                                    'Pending' => 'bg-amber-50 text-amber-700 border-amber-100',

                                    'Verified' => 'bg-blue-50 text-blue-700 border-blue-100',

                                    'CS Processing' => 'bg-cyan-50 text-cyan-700 border-cyan-100',

                                    'For Maintenance' => 'bg-violet-50 text-violet-700 border-violet-100',

                                    'Assigned' => 'bg-indigo-50 text-indigo-700 border-indigo-100',

                                    'In Progress' => 'bg-orange-50 text-orange-700 border-orange-100',

                                    'Completed' => 'bg-green-50 text-green-700 border-green-100',

                                    'Closed' => 'bg-gray-100 text-gray-700 border-gray-200',

                                    'Rejected' => 'bg-red-50 text-red-700 border-red-100',

                                    default => 'bg-gray-50 text-gray-600 border-gray-100',
                                };

                                $statusLabel = match ($complaint->status) {
                                    'Completed' => 'Accomplished',
                                    default => $complaint->status,
                                };

                                $urgency = $complaint->aiAnalysis?->urgency_level;

                                $urgencyClasses = match ($urgency) {
                                    'High' => 'bg-red-50 text-red-700 border-red-200',

                                    'Moderate' => 'bg-amber-50 text-amber-700 border-amber-200',

                                    'Low' => 'bg-green-50 text-green-700 border-green-200',

                                    default => 'bg-gray-50 text-gray-500 border-gray-200',
                                };

                                $canEdit =
                                    $complaint->status === 'Pending' &&
                                    (int) $complaint->customer_service_id === (int) auth()->id();
                            @endphp


                            <tr class="hover:bg-gray-50/70 transition">

                                {{-- COMPLAINT --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="w-10 h-10 rounded-xl
                                                    bg-blue-50 text-blue-600
                                                    flex items-center
                                                    justify-center shrink-0">

                                            <i class="fas fa-file-lines"></i>

                                        </div>


                                        <div class="min-w-0">

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


                                            <p
                                                class="text-xs
                                                      text-gray-400 mt-1">

                                                {{ $complaint->created_at?->format('M d, Y h:i A') }}

                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- CONSUMER --}}

                                <td class="px-5 py-4">

                                    <p class="font-medium text-gray-900">

                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? '—') }}

                                    </p>


                                    @if ($complaint->consumer?->account_number)
                                        <p class="text-xs text-gray-500 mt-1">

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

                                    <p class="text-sm font-medium text-gray-700">

                                        {{ $complaint->division?->name ?? '—' }}

                                    </p>

                                </td>


                                {{-- STATUS --}}

                                <td class="px-5 py-4">

                                    <span
                                        class="inline-flex items-center
                                               gap-1.5 px-2.5 py-1.5
                                               rounded-full border
                                               text-xs font-semibold
                                               {{ $statusClasses }}">

                                        <span
                                            class="w-1.5 h-1.5
                                                     rounded-full bg-current">
                                        </span>

                                        {{ $statusLabel }}
                                    </span>

                                </td>


                                {{-- REPORTED --}}

                                <td class="px-5 py-4 whitespace-nowrap">

                                    <p class="text-sm text-gray-700">

                                        {{ $complaint->created_at?->format('M d, Y') }}

                                    </p>

                                    <p class="text-xs text-gray-400 mt-0.5">

                                        {{ $complaint->created_at?->format('h:i A') }}

                                    </p>

                                </td>


                                {{-- ACTION --}}

                                <td class="px-5 py-4">

                                    <div
                                        class="flex items-center
                                                justify-center gap-2">

                                        <a href="{{ route('customer-service.complaints.show', $complaint) }}"
                                            title="View Complaint"
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


                                        @if ($canEdit)
                                            <a href="{{ route('customer-service.complaints.edit', $complaint) }}"
                                                title="Edit Complaint"
                                                class="inline-flex w-9 h-9
                                                       items-center justify-center
                                                       rounded-lg
                                                       bg-amber-50
                                                       text-amber-600
                                                       border border-amber-100
                                                       hover:bg-amber-600
                                                       hover:text-white
                                                       transition">

                                                <i class="fas fa-pen-to-square"></i>

                                            </a>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="px-6 py-16 text-center">

                                    <div
                                        class="w-14 h-14 mx-auto
                                                rounded-2xl bg-gray-100
                                                text-gray-400
                                                flex items-center
                                                justify-center">

                                        <i
                                            class="fas fa-file-circle-xmark
                                                  text-xl"></i>

                                    </div>

                                    <h3
                                        class="font-semibold
                                               text-gray-900 mt-4">
                                        No complaints found
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Try adjusting your search or filters.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ===================================================== --}}
            {{-- MOBILE --}}
            {{-- ===================================================== --}}

            <div class="lg:hidden divide-y divide-gray-100">

                @forelse ($complaints as $complaint)

                    @php

                        $statusClasses = match ($complaint->status) {
                            'Pending' => 'bg-amber-50 text-amber-700 border-amber-100',

                            'Verified' => 'bg-blue-50 text-blue-700 border-blue-100',

                            'CS Processing' => 'bg-cyan-50 text-cyan-700 border-cyan-100',

                            'For Maintenance' => 'bg-violet-50 text-violet-700 border-violet-100',

                            'Assigned' => 'bg-indigo-50 text-indigo-700 border-indigo-100',

                            'In Progress' => 'bg-orange-50 text-orange-700 border-orange-100',

                            'Completed' => 'bg-green-50 text-green-700 border-green-100',

                            'Closed' => 'bg-gray-100 text-gray-700 border-gray-200',

                            'Rejected' => 'bg-red-50 text-red-700 border-red-100',

                            default => 'bg-gray-50 text-gray-600 border-gray-100',
                        };

                        $statusLabel = match ($complaint->status) {
                            'Completed' => 'Accomplished',
                            default => $complaint->status,
                        };

                        $urgency = $complaint->aiAnalysis?->urgency_level;

                        $urgencyClasses = match ($urgency) {
                            'High' => 'bg-red-50 text-red-700 border-red-200',

                            'Moderate' => 'bg-amber-50 text-amber-700 border-amber-200',

                            'Low' => 'bg-green-50 text-green-700 border-green-200',

                            default => 'bg-gray-50 text-gray-500 border-gray-200',
                        };

                        $canEdit =
                            $complaint->status === 'Pending' &&
                            (int) $complaint->customer_service_id === (int) auth()->id();
                    @endphp


                    <div class="p-4 sm:p-5">

                        {{-- TOP --}}

                        <div class="flex items-start
                                    justify-between gap-3">

                            <div class="min-w-0">

                                <div class="flex flex-wrap
                                            items-center gap-2">

                                    <p class="font-bold text-blue-600">

                                        {{ $complaint->complaint_no }}

                                    </p>


                                    @if ($urgency)
                                        <span
                                            class="inline-flex items-center
                                                   gap-1 px-2 py-0.5
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


                                <p class="text-xs text-gray-400 mt-1">

                                    {{ $complaint->created_at?->format('M d, Y h:i A') }}

                                </p>

                            </div>


                            <span
                                class="inline-flex items-center
                                       gap-1.5 px-2.5 py-1
                                       rounded-full border
                                       text-[10px] font-semibold
                                       shrink-0
                                       {{ $statusClasses }}">

                                <span
                                    class="w-1.5 h-1.5
                                             rounded-full bg-current">
                                </span>

                                {{ $statusLabel }}
                            </span>

                        </div>


                        {{-- DETAILS --}}

                        <div class="mt-4 grid grid-cols-2 gap-4">

                            <div>

                                <p
                                    class="text-[10px] uppercase
                                          tracking-wide font-semibold
                                          text-gray-400">
                                    Consumer
                                </p>

                                <p
                                    class="mt-1 text-sm
                                          font-semibold text-gray-800">

                                    {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? '—') }}

                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-[10px] uppercase
                                          tracking-wide font-semibold
                                          text-gray-400">
                                    Complaint Type
                                </p>

                                <p
                                    class="mt-1 text-sm
                                          font-medium text-gray-700">

                                    {{ $complaint->category?->name ?? 'Uncategorized' }}

                                </p>

                            </div>

                        </div>


                        {{-- DIVISION --}}

                        <div
                            class="mt-4 p-3 rounded-xl
                                    bg-gray-50 border border-gray-100">

                            <p
                                class="text-[10px] uppercase
                                      tracking-wide font-semibold
                                      text-gray-400">
                                Division
                            </p>

                            <p class="text-sm font-medium
                                      text-gray-700 mt-1">

                                {{ $complaint->division?->name ?? '—' }}

                            </p>

                        </div>


                        {{-- BOTTOM --}}

                        <div class="mt-4 flex items-center
                                    justify-between">

                            <span class="text-xs text-gray-400">

                                Reported
                                {{ $complaint->created_at?->diffForHumans() }}

                            </span>


                            <div class="flex items-center gap-2">

                                <a href="{{ route('customer-service.complaints.show', $complaint) }}"
                                    title="View Complaint"
                                    class="inline-flex w-9 h-9
                                           items-center justify-center
                                           rounded-lg
                                           bg-blue-50 text-blue-600
                                           border border-blue-100">

                                    <i class="fas fa-eye"></i>

                                </a>


                                @if ($canEdit)
                                    <a href="{{ route('customer-service.complaints.edit', $complaint) }}"
                                        title="Edit Complaint"
                                        class="inline-flex w-9 h-9
                                               items-center justify-center
                                               rounded-lg
                                               bg-amber-50 text-amber-600
                                               border border-amber-100">

                                        <i class="fas fa-pen-to-square"></i>

                                    </a>
                                @endif

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="p-12 text-center">

                        <i class="fas fa-file-circle-xmark
                                  text-3xl text-gray-300"></i>

                        <p class="font-semibold text-gray-900 mt-3">
                            No complaints found
                        </p>

                        <p class="text-sm text-gray-500 mt-1">
                            Try adjusting your search or filters.
                        </p>

                    </div>
                @endforelse

            </div>


            {{-- PAGINATION --}}

            @if ($complaints->hasPages())
                <div class="px-5 py-4
                            border-t border-gray-100">

                    {{ $complaints->links() }}

                </div>
            @endif

        </div>

    </div>

@endsection
