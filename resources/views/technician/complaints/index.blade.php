@extends('technician.layouts.app')

@section('title', 'My Complaints')

@section('content')

    <div class="max-w-7xl mx-auto space-y-5">


        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col lg:flex-row
                    lg:items-center lg:justify-between gap-4">

            <div>

                <div class="flex items-center gap-2
                            text-sm font-medium text-sky-600">

                    <i class="fas fa-screwdriver-wrench"></i>

                    Maintenance Operations

                </div>


                <h1 class="text-2xl sm:text-3xl
                           font-bold text-gray-900 mt-1">

                    My Complaints

                </h1>


                <p class="text-sm text-gray-500 mt-1">

                    View and manage maintenance complaints
                    assigned to you.

                </p>

            </div>


            {{-- ACTIVE INDICATOR --}}

            @if ($activeCount > 0)
                <div
                    class="inline-flex items-center gap-2
                            px-3.5 py-2 rounded-xl
                            bg-sky-50 border border-sky-100
                            text-sky-700 text-sm font-medium">

                    <span class="relative flex h-2.5 w-2.5">

                        <span
                            class="animate-ping absolute inline-flex
                                   h-full w-full rounded-full
                                   bg-sky-400 opacity-75">
                        </span>

                        <span
                            class="relative inline-flex rounded-full
                                   h-2.5 w-2.5 bg-sky-600">
                        </span>

                    </span>

                    {{ $activeCount }}

                    active
                    {{ $activeCount === 1 ? 'complaint' : 'complaints' }}

                </div>
            @endif

        </div>



        {{-- ========================================================= --}}
        {{-- FLASH --}}
        {{-- ========================================================= --}}

        @if (session('success'))
            <div
                class="rounded-xl border border-green-200
                        bg-green-50 px-4 py-3
                        text-sm text-green-800
                        flex items-start gap-3">

                <i class="fas fa-circle-check mt-0.5"></i>

                <span>
                    {{ session('success') }}
                </span>

            </div>
        @endif


        @if (session('error'))
            <div
                class="rounded-xl border border-red-200
                        bg-red-50 px-4 py-3
                        text-sm text-red-800
                        flex items-start gap-3">

                <i class="fas fa-circle-exclamation mt-0.5"></i>

                <span>
                    {{ session('error') }}
                </span>

            </div>
        @endif



        {{-- ========================================================= --}}
        {{-- MINIMAL STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-3 gap-2 sm:gap-4">


            {{-- ASSIGNED --}}

            <div
                class="bg-white rounded-xl sm:rounded-2xl
                        border border-gray-200
                        shadow-sm p-3 sm:p-5">

                <div class="flex items-center
                            justify-between gap-2">

                    <div>

                        <p class="text-[10px] sm:text-sm
                                  text-gray-500">

                            Assigned

                        </p>

                        <p
                            class="text-xl sm:text-3xl
                                  font-bold text-gray-900
                                  mt-1">

                            {{ $assignedCount }}

                        </p>

                    </div>


                    <div
                        class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-blue-50 text-blue-600
                                items-center justify-center">

                        <i class="fas fa-clipboard-list"></i>

                    </div>

                </div>


                <p class="hidden sm:block
                          text-xs text-gray-400 mt-2">

                    Waiting to start

                </p>

            </div>



            {{-- IN PROGRESS --}}

            <div
                class="bg-white rounded-xl sm:rounded-2xl
                        border border-gray-200
                        shadow-sm p-3 sm:p-5">

                <div class="flex items-center
                            justify-between gap-2">

                    <div>

                        <p class="text-[10px] sm:text-sm
                                  text-gray-500">

                            In Progress

                        </p>

                        <p
                            class="text-xl sm:text-3xl
                                  font-bold text-amber-600
                                  mt-1">

                            {{ $inProgressCount }}

                        </p>

                    </div>


                    <div
                        class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-amber-50 text-amber-600
                                items-center justify-center">

                        <i class="fas fa-screwdriver-wrench"></i>

                    </div>

                </div>


                <p class="hidden sm:block
                          text-xs text-gray-400 mt-2">

                    Active field work

                </p>

            </div>



            {{-- HIGH URGENCY --}}

            <div
                class="bg-white rounded-xl sm:rounded-2xl
                        border border-gray-200
                        shadow-sm p-3 sm:p-5">

                <div class="flex items-center
                            justify-between gap-2">

                    <div>

                        <p class="text-[10px] sm:text-sm
                                  text-gray-500">

                            High Urgency

                        </p>

                        <p
                            class="text-xl sm:text-3xl
                                  font-bold text-red-600
                                  mt-1">

                            {{ $urgentCount }}

                        </p>

                    </div>


                    <div
                        class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-red-50 text-red-600
                                items-center justify-center">

                        <i class="fas fa-triangle-exclamation"></i>

                    </div>

                </div>


                <p class="hidden sm:block
                          text-xs text-gray-400 mt-2">

                    Needs attention

                </p>

            </div>

        </div>



        {{-- ========================================================= --}}
        {{-- ACTIVE / ALL SWITCH --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl
                    border border-gray-200 shadow-sm p-1.5">

            <div class="grid grid-cols-2 gap-1">


                {{-- ACTIVE WORK --}}

                <a href="{{ route(
                    'technician.complaints.index',
                    array_merge(request()->except(['page', 'status', 'scope']), [
                        'scope' => 'active',
                    ]),
                ) }}"
                    class="flex items-center justify-center
                           gap-2 px-4 py-2.5 rounded-xl
                           text-sm font-semibold transition
                           {{ $scope === 'active' ? 'bg-sky-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">

                    <i class="fas fa-person-digging"></i>

                    <span>
                        Active Work
                    </span>

                    @if ($activeCount > 0)
                        <span
                            class="px-2 py-0.5 rounded-full
                                   text-[10px]
                                   {{ $scope === 'active' ? 'bg-white/20 text-white' : 'bg-sky-50 text-sky-700' }}">

                            {{ $activeCount }}

                        </span>
                    @endif

                </a>



                {{-- ALL MY COMPLAINTS --}}

                <a href="{{ route(
                    'technician.complaints.index',
                    array_merge(request()->except(['page', 'status', 'scope']), [
                        'scope' => 'all',
                    ]),
                ) }}"
                    class="flex items-center justify-center
                           gap-2 px-4 py-2.5 rounded-xl
                           text-sm font-semibold transition
                           {{ $scope === 'all' ? 'bg-sky-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">

                    <i class="fas fa-list-check"></i>

                    <span>
                        All My Complaints
                    </span>

                </a>

            </div>

        </div>



        {{-- ========================================================= --}}
        {{-- FILTERS --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl
                    border border-gray-200 shadow-sm">

            <form method="GET" action="{{ route('technician.complaints.index') }}" class="p-4 sm:p-5">


                <input type="hidden" name="scope" value="{{ $scope }}">


                <div
                    class="grid grid-cols-1
                            md:grid-cols-2
                            xl:grid-cols-6 gap-3">


                    {{-- SEARCH --}}

                    <div class="relative md:col-span-2">

                        <i
                            class="fas fa-magnifying-glass
                                  absolute left-3.5 top-1/2
                                  -translate-y-1/2
                                  text-gray-400">
                        </i>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search complaint, consumer, type..."
                            class="w-full pl-10 pr-4 py-2.5
                                   rounded-xl border-gray-300
                                   text-sm
                                   focus:border-sky-500
                                   focus:ring-sky-500">

                    </div>



                    {{-- STATUS --}}

                    <select name="status"
                        class="rounded-xl border-gray-300
                               text-sm text-gray-700
                               focus:border-sky-500
                               focus:ring-sky-500">

                        <option value="">
                            All Statuses
                        </option>

                        <option value="Assigned" @selected(request('status') === 'Assigned')>

                            Assigned

                        </option>

                        <option value="In Progress" @selected(request('status') === 'In Progress')>

                            In Progress

                        </option>


                        @if ($scope === 'all')
                            <option value="Completed" @selected(request('status') === 'Completed')>

                                Completed

                            </option>

                            <option value="Closed" @selected(request('status') === 'Closed')>

                                Closed

                            </option>
                        @endif

                    </select>



                    {{-- URGENCY --}}

                    <select name="urgency"
                        class="rounded-xl border-gray-300
                               text-sm text-gray-700
                               focus:border-sky-500
                               focus:ring-sky-500">

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



                    {{-- DATE FROM --}}

                    <div>

                        <label
                            class="block xl:hidden
                                   text-xs font-medium
                                   text-gray-500 mb-1">

                            From

                        </label>

                        <input type="date" name="date_from" value="{{ request('date_from') }}" title="Date From"
                            class="w-full rounded-xl
                                   border-gray-300
                                   text-sm text-gray-700
                                   focus:border-sky-500
                                   focus:ring-sky-500">

                    </div>



                    {{-- DATE TO --}}

                    <div>

                        <label
                            class="block xl:hidden
                                   text-xs font-medium
                                   text-gray-500 mb-1">

                            To

                        </label>

                        <input type="date" name="date_to" value="{{ request('date_to') }}" title="Date To"
                            class="w-full rounded-xl
                                   border-gray-300
                                   text-sm text-gray-700
                                   focus:border-sky-500
                                   focus:ring-sky-500">

                    </div>

                </div>



                {{-- ACTIONS --}}

                <div class="mt-3 flex flex-wrap gap-2">


                    <button type="submit"
                        class="inline-flex items-center
                               justify-center gap-2
                               px-4 py-2.5 rounded-xl
                               bg-sky-600 text-white
                               text-sm font-semibold
                               hover:bg-sky-700 transition">

                        <i class="fas fa-filter"></i>

                        Apply Filters

                    </button>



                    @if (request()->filled('search') ||
                            request()->filled('status') ||
                            request()->filled('urgency') ||
                            request()->filled('date_from') ||
                            request()->filled('date_to'))
                        <a href="{{ route('technician.complaints.index', [
                            'scope' => $scope,
                        ]) }}"
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



                    {{-- PRINT --}}

                    <a href="{{ route(
                        'technician.complaints.print-report',
                        request()->only(['scope', 'search', 'status', 'urgency', 'date_from', 'date_to']),
                    ) }}"
                        target="_blank"
                        class="inline-flex items-center
                               justify-center gap-2
                               px-4 py-2.5 rounded-xl
                               bg-slate-100 text-slate-700
                               text-sm font-semibold
                               hover:bg-slate-200 transition">

                        <i class="fas fa-print"></i>

                        Print

                    </a>

                </div>

            </form>

        </div>



        {{-- ========================================================= --}}
        {{-- COMPLAINT LIST --}}
        {{-- ========================================================= --}}

        <div
            class="bg-white rounded-2xl
                    border border-gray-200
                    shadow-sm overflow-hidden">


            {{-- LIST HEADER --}}

            <div class="px-5 sm:px-6 py-4
                        border-b border-gray-100">

                <div
                    class="flex flex-col sm:flex-row
                            sm:items-center
                            sm:justify-between gap-2">

                    <div>

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-clipboard-list
                                      text-sky-600 mr-2">
                            </i>

                            {{ $scope === 'active' ? 'Active Work' : 'All My Complaints' }}

                        </h2>


                        <p class="text-xs sm:text-sm
                                  text-gray-500 mt-1">

                            @if ($scope === 'active')
                                Assigned and in-progress complaints
                                that still require your attention.
                            @else
                                All maintenance complaints
                                assigned to you.
                            @endif

                        </p>

                    </div>


                    <span class="text-xs text-gray-500">

                        Showing
                        {{ $complaints->firstItem() ?? 0 }}

                        –

                        {{ $complaints->lastItem() ?? 0 }}

                        of

                        {{ $complaints->total() }}

                    </span>

                </div>

            </div>



            {{-- ===================================================== --}}
            {{-- DESKTOP TABLE --}}
            {{-- ===================================================== --}}

            <div class="hidden lg:block overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50
                               border-b border-gray-200">

                        <tr>

                            <th
                                class="px-5 py-3
                                       text-left text-xs
                                       font-semibold uppercase
                                       tracking-wide text-gray-500">

                                Complaint

                            </th>

                            <th
                                class="px-5 py-3
                                       text-left text-xs
                                       font-semibold uppercase
                                       tracking-wide text-gray-500">

                                Consumer

                            </th>

                            <th
                                class="px-5 py-3
                                       text-left text-xs
                                       font-semibold uppercase
                                       tracking-wide text-gray-500">

                                Complaint Type

                            </th>

                            <th
                                class="px-5 py-3
                                       text-left text-xs
                                       font-semibold uppercase
                                       tracking-wide text-gray-500">

                                Location

                            </th>

                            <th
                                class="px-5 py-3
                                       text-left text-xs
                                       font-semibold uppercase
                                       tracking-wide text-gray-500">

                                Status

                            </th>

                            <th
                                class="px-5 py-3
                                       text-center text-xs
                                       font-semibold uppercase
                                       tracking-wide text-gray-500">

                                Action

                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($complaints as $complaint)
                            @php

                                /*
                                |--------------------------------------------------------------------------
                                | STATUS
                                |--------------------------------------------------------------------------
                                */

                                $statusClasses = match ($complaint->status) {
                                    'Assigned' => 'bg-blue-50 text-blue-700 border-blue-100',

                                    'In Progress' => 'bg-amber-50 text-amber-700 border-amber-100',

                                    'Completed' => 'bg-green-50 text-green-700 border-green-100',

                                    'Closed' => 'bg-gray-100 text-gray-700 border-gray-200',

                                    default => 'bg-gray-50 text-gray-600 border-gray-200',
                                };

                                /*
                                |--------------------------------------------------------------------------
                                | AI URGENCY
                                |--------------------------------------------------------------------------
                                */

                                $urgency = strtoupper(trim($complaint->aiAnalysis?->urgency_level ?? ''));

                                $urgencyClasses = match ($urgency) {
                                    'HIGH' => 'bg-red-50 text-red-700 border-red-200',

                                    'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',

                                    'LOW' => 'bg-green-50 text-green-700 border-green-200',

                                    default => 'bg-gray-50 text-gray-500 border-gray-200',
                                };

                            @endphp


                            <tr class="hover:bg-gray-50/70 transition">


                                {{-- ================================= --}}
                                {{-- COMPLAINT + URGENCY --}}
                                {{-- ================================= --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="w-10 h-10 rounded-xl
                                                   bg-sky-50 text-sky-600
                                                   flex items-center
                                                   justify-center shrink-0">

                                            <i class="fas fa-file-lines"></i>

                                        </div>


                                        <div class="min-w-0">


                                            {{-- NUMBER + URGENCY --}}

                                            <div
                                                class="flex flex-wrap
                                                       items-center gap-2">

                                                <p
                                                    class="font-bold
                                                           text-sky-700">

                                                    {{ $complaint->complaint_no }}

                                                </p>


                                                @if ($urgency)
                                                    <span
                                                        class="inline-flex
                                                               items-center gap-1
                                                               px-2 py-0.5
                                                               rounded-full border
                                                               text-[10px]
                                                               font-bold
                                                               {{ $urgencyClasses }}">

                                                        <span
                                                            class="w-1.5 h-1.5
                                                                   rounded-full
                                                                   bg-current">
                                                        </span>

                                                        {{ $urgency }}

                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex
                                                               items-center
                                                               px-2 py-0.5
                                                               rounded-full
                                                               border
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



                                {{-- ================================= --}}
                                {{-- CONSUMER --}}
                                {{-- ================================= --}}

                                <td class="px-5 py-4">

                                    <p class="font-medium
                                               text-gray-900">

                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}

                                    </p>


                                    @if ($complaint->consumer?->account_number)
                                        <p
                                            class="text-xs
                                                   text-gray-500 mt-1">

                                            Account:

                                            {{ $complaint->consumer->account_number }}

                                        </p>
                                    @elseif ($complaint->complainant_phone)
                                        <p
                                            class="text-xs
                                                   text-gray-500 mt-1">

                                            {{ $complaint->complainant_phone }}

                                        </p>
                                    @endif

                                </td>



                                {{-- ================================= --}}
                                {{-- COMPLAINT TYPE --}}
                                {{-- ================================= --}}

                                <td class="px-5 py-4">

                                    <span
                                        class="inline-flex
                                               px-2.5 py-1.5
                                               rounded-lg
                                               bg-gray-100
                                               text-gray-700
                                               text-xs font-medium">

                                        {{ $complaint->category?->name ?? 'Uncategorized' }}

                                    </span>

                                </td>



                                {{-- ================================= --}}
                                {{-- LOCATION --}}
                                {{-- ================================= --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-start gap-2">

                                        <i
                                            class="fas fa-location-dot
                                                   text-gray-400 mt-1">
                                        </i>

                                        <div>

                                            <p
                                                class="text-gray-700
                                                       max-w-[240px]">

                                                {{ \Illuminate\Support\Str::limit($complaint->address, 50) }}

                                            </p>


                                            @if (!is_null($complaint->latitude) && !is_null($complaint->longitude))
                                                <p
                                                    class="text-[10px]
                                                           text-green-600
                                                           mt-1">

                                                    <i
                                                        class="fas
                                                               fa-map-location-dot
                                                               mr-1">
                                                    </i>

                                                    GPS available

                                                </p>
                                            @endif

                                        </div>

                                    </div>

                                </td>



                                {{-- ================================= --}}
                                {{-- STATUS --}}
                                {{-- ================================= --}}

                                <td class="px-5 py-4">

                                    <span
                                        class="inline-flex
                                               items-center gap-1.5
                                               px-2.5 py-1.5
                                               rounded-full border
                                               text-xs font-semibold
                                               {{ $statusClasses }}">

                                        <span
                                            class="w-1.5 h-1.5
                                                   rounded-full
                                                   bg-current">
                                        </span>

                                        {{ $complaint->status }}

                                    </span>

                                </td>



                                {{-- ================================= --}}
                                {{-- ACTION --}}
                                {{-- ================================= --}}

                                <td class="px-5 py-4 text-center">

                                    <a href="{{ route('technician.complaints.show', $complaint) }}"
                                        title="View Complaint" aria-label="View Complaint"
                                        class="inline-flex
                                               w-9 h-9
                                               items-center
                                               justify-center
                                               rounded-lg
                                               bg-sky-50
                                               text-sky-600
                                               border border-sky-100
                                               hover:bg-sky-600
                                               hover:text-white
                                               hover:border-sky-600
                                               transition">

                                        <i class="fas fa-eye"></i>

                                    </a>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="6"
                                    class="px-6 py-14
                                           text-center">

                                    <div
                                        class="w-14 h-14 mx-auto
                                               rounded-2xl
                                               bg-gray-100
                                               text-gray-400
                                               flex items-center
                                               justify-center">

                                        <i
                                            class="fas
                                                   fa-clipboard-check
                                                   text-xl">
                                        </i>

                                    </div>


                                    <h3
                                        class="font-semibold
                                               text-gray-900 mt-4">

                                        No complaints found

                                    </h3>


                                    <p class="text-sm
                                               text-gray-500 mt-1">

                                        No assigned maintenance
                                        complaints match your filters.

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
                            'Assigned' => 'bg-blue-50 text-blue-700 border-blue-100',

                            'In Progress' => 'bg-amber-50 text-amber-700 border-amber-100',

                            'Completed' => 'bg-green-50 text-green-700 border-green-100',

                            'Closed' => 'bg-gray-100 text-gray-700 border-gray-200',

                            default => 'bg-gray-50 text-gray-600 border-gray-200',
                        };

                        $urgency = strtoupper(trim($complaint->aiAnalysis?->urgency_level ?? ''));

                        $urgencyClasses = match ($urgency) {
                            'HIGH' => 'bg-red-50 text-red-700 border-red-200',

                            'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',

                            'LOW' => 'bg-green-50 text-green-700 border-green-200',

                            default => 'bg-gray-50 text-gray-500 border-gray-200',
                        };

                    @endphp


                    <div class="p-4 sm:p-5">


                        {{-- TOP --}}

                        <div class="flex items-start
                                   justify-between gap-3">

                            <div class="min-w-0">

                                <div class="flex flex-wrap
                                           items-center gap-2">

                                    <p class="font-bold
                                               text-sky-700">

                                        {{ $complaint->complaint_no }}

                                    </p>


                                    @if ($urgency)
                                        <span
                                            class="inline-flex
                                                   items-center gap-1
                                                   px-2 py-0.5
                                                   rounded-full border
                                                   text-[10px]
                                                   font-bold
                                                   {{ $urgencyClasses }}">

                                            <span
                                                class="w-1.5 h-1.5
                                                       rounded-full
                                                       bg-current">
                                            </span>

                                            {{ $urgency }}

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex
                                                   px-2 py-0.5
                                                   rounded-full
                                                   border
                                                   bg-gray-50
                                                   border-gray-200
                                                   text-[10px]
                                                   font-semibold
                                                   text-gray-400">

                                            NOT ASSESSED

                                        </span>
                                    @endif

                                </div>


                                <p class="text-xs
                                           text-gray-400 mt-1">

                                    {{ $complaint->created_at?->format('M d, Y h:i A') }}

                                </p>

                            </div>


                            <span
                                class="inline-flex
                                       items-center gap-1.5
                                       px-2.5 py-1
                                       rounded-full border
                                       text-[10px]
                                       font-semibold
                                       shrink-0
                                       {{ $statusClasses }}">

                                <span
                                    class="w-1.5 h-1.5
                                           rounded-full bg-current">
                                </span>

                                {{ $complaint->status }}

                            </span>

                        </div>



                        {{-- DETAILS --}}

                        <div class="mt-4 grid
                                   grid-cols-2 gap-4">


                            {{-- CONSUMER --}}

                            <div>

                                <p
                                    class="text-[10px]
                                           uppercase
                                           tracking-wide
                                           font-semibold
                                           text-gray-400">

                                    Consumer

                                </p>

                                <p
                                    class="mt-1 text-sm
                                           font-semibold
                                           text-gray-800">

                                    {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}

                                </p>

                            </div>



                            {{-- TYPE --}}

                            <div>

                                <p
                                    class="text-[10px]
                                           uppercase
                                           tracking-wide
                                           font-semibold
                                           text-gray-400">

                                    Complaint Type

                                </p>

                                <p
                                    class="mt-1 text-sm
                                           font-medium
                                           text-gray-700">

                                    {{ $complaint->category?->name ?? 'Uncategorized' }}

                                </p>

                            </div>

                        </div>



                        {{-- LOCATION --}}

                        <div
                            class="mt-4 p-3
                                   rounded-xl bg-gray-50
                                   border border-gray-100">

                            <div class="flex items-start gap-2">

                                <i
                                    class="fas fa-location-dot
                                           text-gray-400 mt-0.5">
                                </i>

                                <p class="text-sm
                                           text-gray-600">

                                    {{ \Illuminate\Support\Str::limit($complaint->address, 90) }}

                                </p>

                            </div>

                        </div>



                        {{-- ACTION --}}

                        <div class="mt-4 flex items-center
                                   justify-between">

                            <span class="text-xs
                                       text-gray-400">

                                Updated

                                {{ $complaint->updated_at?->diffForHumans() }}

                            </span>


                            <a href="{{ route('technician.complaints.show', $complaint) }}"
                                title="View Complaint" aria-label="View Complaint"
                                class="inline-flex
                                       w-9 h-9
                                       items-center
                                       justify-center
                                       rounded-lg
                                       bg-sky-50
                                       text-sky-600
                                       border border-sky-100
                                       hover:bg-sky-600
                                       hover:text-white
                                       transition">

                                <i class="fas fa-eye"></i>

                            </a>

                        </div>

                    </div>


                @empty

                    <div class="p-10 text-center">

                        <div
                            class="w-14 h-14 mx-auto
                                   rounded-2xl
                                   bg-gray-100
                                   text-gray-400
                                   flex items-center
                                   justify-center">

                            <i
                                class="fas
                                       fa-clipboard-check
                                       text-xl">
                            </i>

                        </div>


                        <p class="font-semibold
                                   text-gray-900 mt-3">

                            No complaints found

                        </p>


                        <p class="text-sm
                                   text-gray-500 mt-1">

                            No assigned maintenance
                            complaints match your filters.

                        </p>

                    </div>
                @endforelse

            </div>

            @if ($complaints->hasPages())
                <div class="px-5 sm:px-6 py-4
                           border-t border-gray-100">

                    {{ $complaints->links() }}

                </div>
            @endif

        </div>

    </div>

@endsection
