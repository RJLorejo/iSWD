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
                    Monitor verified complaints, manage maintenance assignments,
                    and track service progress.
                </p>

            </div>


            <div class="flex flex-wrap items-center gap-2">


                <a href="{{ route('maintenance-manager.complaints.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5
                           rounded-xl bg-white border border-gray-200
                           text-sm font-semibold text-gray-700
                           hover:bg-gray-50 transition">

                    <i class="fas fa-rotate"></i>

                    Refresh

                </a>

            </div>

        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-check text-green-600 mt-0.5"></i>

                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>

                </div>

            </div>
        @endif


        @if ($errors->any())

            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-exclamation text-red-600 mt-0.5"></i>

                    <div>

                        @foreach ($errors->all() as $error)
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

            <a href="{{ route('maintenance-manager.complaints.for-assignment') }}"
                class="bg-white rounded-2xl border border-gray-200
                       shadow-sm p-5 transition
                       hover:border-blue-300 hover:shadow-md">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                            For Assignment
                        </p>

                        <p class="mt-2 text-2xl font-bold text-gray-900">
                            {{ $verifiedCount ?? 0 }}
                        </p>

                        <p class="mt-1 text-xs text-gray-400">
                            Engineering only
                        </p>

                    </div>


                    <div
                        class="w-11 h-11 rounded-xl bg-blue-50
                                flex items-center justify-center">

                        <i class="fas fa-user-plus text-blue-600"></i>

                    </div>

                </div>

            </a>



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
                            Maintenance team assigned
                        </p>

                    </div>


                    <div
                        class="w-11 h-11 rounded-xl bg-indigo-50
                                flex items-center justify-center">

                        <i class="fas fa-users-gear text-indigo-600"></i>

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


                    <div
                        class="w-11 h-11 rounded-xl bg-amber-50
                                flex items-center justify-center">

                        <i class="fas fa-clock text-amber-600"></i>

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


                    <div
                        class="w-11 h-11 rounded-xl bg-green-50
                                flex items-center justify-center">

                        <i class="fas fa-circle-check text-green-600"></i>

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


                    <div
                        class="w-11 h-11 rounded-xl bg-gray-100
                                flex items-center justify-center">

                        <i class="fas fa-box-archive text-gray-600"></i>

                    </div>

                </div>

            </div>

        </div>



        {{-- ========================================================= --}}
        {{-- FILTERS --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">

            <form method="GET" action="{{ route('maintenance-manager.complaints.index') }}" class="p-4 sm:p-5">


                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3">


                    {{-- SEARCH --}}

                    <div class="relative md:col-span-2">

                        <i
                            class="fas fa-magnifying-glass
                                  absolute left-3 top-1/2
                                  -translate-y-1/2
                                  text-gray-400">
                        </i>


                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search complaint, consumer, type, division..."
                            class="w-full pl-10 pr-4 py-2.5
                                   rounded-xl border border-gray-200
                                   text-sm
                                   focus:ring-2 focus:ring-blue-500
                                   focus:border-blue-500">

                    </div>
                    <div>
                        <select name="status"
                            class="w-full px-3 py-2.5 rounded-xl
               border border-gray-200
               text-sm text-gray-700
               focus:ring-2 focus:ring-blue-500
               focus:border-blue-500">

                            <option value="">
                                All Statuses
                            </option>

                            <option value="For Assignment" {{ request('status') === 'For Assignment' ? 'selected' : '' }}>
                                For Assignment
                            </option>

                            <option value="For Maintenance"
                                {{ request('status') === 'For Maintenance' ? 'selected' : '' }}>
                                For Maintenance
                            </option>

                            <option value="Assigned" {{ request('status') === 'Assigned' ? 'selected' : '' }}>
                                Assigned
                            </option>

                            <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>
                                In Progress
                            </option>

                            <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>
                                Accomplished
                            </option>

                            <option value="Closed" {{ request('status') === 'Closed' ? 'selected' : '' }}>
                                Closed
                            </option>

                        </select>
                    </div>

                    {{-- URGENCY --}}

                    <div>

                        <select name="urgency"
                            class="w-full px-3 py-2.5 rounded-xl
                                   border border-gray-200
                                   text-sm text-gray-700
                                   focus:ring-2 focus:ring-blue-500
                                   focus:border-blue-500">

                            <option value="">
                                All Urgency
                            </option>

                            <option value="High" {{ request('urgency') === 'High' ? 'selected' : '' }}>
                                High
                            </option>

                            <option value="Moderate" {{ request('urgency') === 'Moderate' ? 'selected' : '' }}>
                                Moderate
                            </option>

                            <option value="Low" {{ request('urgency') === 'Low' ? 'selected' : '' }}>
                                Low
                            </option>

                            <option value="not_assessed" {{ request('urgency') === 'not_assessed' ? 'selected' : '' }}>
                                Not Assessed
                            </option>

                        </select>

                    </div>

                    <div>

                        <input type="date" name="date_from" value="{{ request('date_from') }}" title="Date From"
                            class="w-full px-3 py-2.5 rounded-xl
                                   border border-gray-200
                                   text-sm text-gray-700
                                   focus:ring-2 focus:ring-blue-500
                                   focus:border-blue-500">

                    </div>

                    <div>

                        <input type="date" name="date_to" value="{{ request('date_to') }}" title="Date To"
                            class="w-full px-3 py-2.5 rounded-xl
                                   border border-gray-200
                                   text-sm text-gray-700
                                   focus:ring-2 focus:ring-blue-500
                                   focus:border-blue-500">

                    </div>

                </div>

                <div class="mt-3 flex flex-wrap gap-2">

                    <button type="submit"
                        class="inline-flex items-center justify-center
                               gap-2 px-4 py-2.5 rounded-xl
                               bg-blue-600 text-white
                               text-sm font-semibold
                               hover:bg-blue-700 transition">

                        <i class="fas fa-filter"></i>

                        Apply Filters

                    </button>


                    @if (request()->hasAny(['search', 'status', 'urgency', 'date_from', 'date_to']))
                        <a href="{{ route('maintenance-manager.complaints.index') }}"
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


                    {{-- PRINT SAME FILTERS --}}

                    <a href="{{ route(
                        'maintenance-manager.complaints.print-report',
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
        {{-- COMPLAINT TABLE --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl border border-gray-200
                    shadow-sm overflow-hidden">


            <div
                class="px-5 py-4 border-b border-gray-100
                        flex flex-col gap-2
                        sm:flex-row sm:items-center
                        sm:justify-between">

                <div>

                    <h2 class="font-bold text-gray-900">
                        Complaints
                    </h2>

                    <p class="text-xs text-gray-500 mt-1">
                        Engineering and Commercial complaint monitoring
                    </p>

                </div>


                <span class="text-sm text-gray-500">
                    {{ $complaints->total() }} total
                </span>

            </div>



            @if ($complaints->count())


                {{-- ================================================= --}}
                {{-- DESKTOP TABLE --}}
                {{-- ================================================= --}}

                <div class="hidden lg:block overflow-x-auto">

                    <table class="min-w-full">

                        <thead class="bg-gray-50 border-b border-gray-100">

                            <tr>

                                <th
                                    class="px-5 py-3 text-left text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Complaint
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Consumer
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Complaint Type
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Status
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs
           font-semibold uppercase
           tracking-wider text-gray-500">
                                    Plumber Assignment
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Updated
                                </th>

                                <th
                                    class="px-5 py-3 text-center text-xs
                                           font-semibold uppercase
                                           tracking-wider text-gray-500">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @foreach ($complaints as $complaint)
                                @php
                                    $statusClasses = match ($complaint->status) {
                                        'Verified' => 'bg-blue-50 text-blue-700 border-blue-100',

                                        'For Maintenance' => 'bg-violet-50 text-violet-700 border-violet-100',

                                        'Assigned' => 'bg-indigo-50 text-indigo-700 border-indigo-100',

                                        'In Progress' => 'bg-amber-50 text-amber-700 border-amber-100',

                                        'Completed' => 'bg-green-50 text-green-700 border-green-100',

                                        'Closed' => 'bg-gray-100 text-gray-700 border-gray-200',

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

                                    $divisionName = $complaint->division?->name ?? '—';

                                    $normalizedDivision = strtolower(trim($divisionName));

                                    $isCommercial = str_contains($normalizedDivision, 'commercial');

                                    $isEngineering = str_contains($normalizedDivision, 'engineering');
                                @endphp


                                <tr class="hover:bg-gray-50/70 transition">


                                    {{-- ================================================= --}}
                                    {{-- COMPLAINT --}}
                                    {{-- ================================================= --}}

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


                                                {{-- NUMBER + URGENCY --}}

                                                <div class="flex flex-wrap items-center gap-2">

                                                    <p class="text-sm font-bold text-blue-600">

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
                                                            class="inline-flex items-center
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

                                        </div>

                                    </td>



                                    {{-- ================================================= --}}
                                    {{-- CONSUMER --}}
                                    {{-- ================================================= --}}

                                    <td class="px-5 py-4">

                                        <div>

                                            <p class="font-medium text-gray-900">

                                                {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? '—') }}

                                            </p>


                                            @if ($complaint->consumer?->account_number)
                                                <p class="text-xs text-gray-500 mt-0.5">

                                                    Account:
                                                    {{ $complaint->consumer->account_number }}

                                                </p>
                                            @endif


                                            @if ($complaint->complainant_phone)
                                                <p class="text-xs text-gray-400 mt-0.5">

                                                    {{ $complaint->complainant_phone }}

                                                </p>
                                            @endif

                                        </div>

                                    </td>



                                    {{-- ================================================= --}}
                                    {{-- COMPLAINT TYPE + DIVISION --}}
                                    {{-- ================================================= --}}

                                    <td class="px-5 py-4">

                                        <div class="space-y-1.5">

                                            <span
                                                class="inline-flex items-center
                                                       px-2.5 py-1 rounded-lg
                                                       bg-gray-100 text-gray-700
                                                       text-xs font-medium">

                                                {{ $complaint->category?->name ?? 'Uncategorized' }}

                                            </span>


                                            <div>

                                                @if ($isCommercial)
                                                    <span
                                                        class="inline-flex items-center
                                                               gap-1.5 px-2.5 py-1
                                                               rounded-lg
                                                               bg-violet-50
                                                               text-violet-700
                                                               text-xs font-medium">

                                                        <i class="fas fa-headset text-[10px]"></i>

                                                        Commercial

                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center
                                                               gap-1.5 px-2.5 py-1
                                                               rounded-lg
                                                               bg-blue-50
                                                               text-blue-700
                                                               text-xs font-medium">

                                                        <i class="fas fa-screwdriver-wrench text-[10px]"></i>

                                                        {{ $divisionName }}

                                                    </span>
                                                @endif

                                            </div>

                                        </div>

                                    </td>



                                    {{-- ================================================= --}}
                                    {{-- STATUS --}}
                                    {{-- ================================================= --}}

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

                                    <td class="px-5 py-4">

                                        @php
                                            $hasPlumbers = $complaint->technicians->isNotEmpty();

                                            $isForwardedToMaintenance =
                                                in_array(
                                                    $complaint->status,
                                                    ['For Maintenance', 'Assigned', 'In Progress', 'Completed'],
                                                    true,
                                                ) ||
                                                ($complaint->status === 'Closed' && $hasPlumbers);

                                            $isCommercialOnly =
                                                $isCommercial && !$isForwardedToMaintenance && !$hasPlumbers;
                                        @endphp

                                        @if ($isCommercialOnly)
                                            <div class="flex items-center gap-2">

                                                <div
                                                    class="w-8 h-8 rounded-lg
                       bg-violet-50 text-violet-600
                       flex items-center justify-center
                       shrink-0">

                                                    <i class="fas fa-store text-xs"></i>

                                                </div>

                                                <div>

                                                    <p class="text-sm font-semibold text-gray-600">
                                                        Not Applicable
                                                    </p>

                                                    <p class="text-[10px] text-violet-600">
                                                        Handled by Commercial Services
                                                    </p>

                                                </div>

                                            </div>
                                        @elseif ($hasPlumbers)
                                            <div class="space-y-2">

                                                @foreach ($complaint->technicians as $technician)
                                                    <div class="flex items-center gap-2">

                                                        <div
                                                            class="w-7 h-7 rounded-full
                               bg-blue-100 text-blue-700
                               flex items-center justify-center
                               text-[10px] font-bold
                               shrink-0">

                                                            {{ strtoupper(substr($technician->first_name ?? '', 0, 1) . substr($technician->last_name ?? '', 0, 1)) }}

                                                        </div>

                                                        <div class="min-w-0">

                                                            <p
                                                                class="text-sm font-medium
                                   text-gray-800 truncate
                                   max-w-[160px]">

                                                                {{ $technician->full_name }}

                                                            </p>

                                                            @if ($technician->pivot?->assignment_role)
                                                                <p class="text-[10px] text-gray-400">
                                                                    {{ $technician->pivot->assignment_role }}
                                                                </p>
                                                            @endif

                                                        </div>

                                                    </div>
                                                @endforeach

                                            </div>
                                        @else
                                            <div class="flex items-center gap-2">

                                                <div
                                                    class="w-8 h-8 rounded-lg
                       bg-gray-100 text-gray-400
                       flex items-center justify-center
                       shrink-0">

                                                    <i class="fas fa-user-clock text-xs"></i>

                                                </div>

                                                <div>

                                                    <p class="text-sm font-medium text-gray-500">
                                                        Not Assigned
                                                    </p>

                                                    <p class="text-[10px] text-gray-400">
                                                        Awaiting plumber assignment
                                                    </p>

                                                </div>

                                            </div>
                                        @endif

                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">

                                        <p class="text-sm text-gray-700">

                                            {{ $complaint->updated_at?->format('M d, Y') }}

                                        </p>

                                        <p class="text-xs text-gray-400 mt-0.5">

                                            {{ $complaint->updated_at?->format('h:i A') }}

                                        </p>

                                    </td>



                                    {{-- ================================================= --}}
                                    {{-- ACTION --}}
                                    {{-- ================================================= --}}

                                    <td class="px-5 py-4 text-center">

                                        <a href="{{ route('maintenance-manager.complaints.show', $complaint) }}"
                                            title="View Complaint" aria-label="View Complaint"
                                            class="inline-flex w-9 h-9
                                                   items-center justify-center
                                                   rounded-lg
                                                   bg-blue-50 text-blue-600
                                                   border border-blue-100
                                                   hover:bg-blue-600
                                                   hover:text-white
                                                   hover:border-blue-600
                                                   transition">

                                            <i class="fas fa-eye"></i>

                                        </a>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>



                {{-- ================================================= --}}
                {{-- MOBILE / TABLET CARDS --}}
                {{-- ================================================= --}}

                <div class="lg:hidden divide-y divide-gray-100">

                    @foreach ($complaints as $complaint)
                        @php
                            $statusClasses = match ($complaint->status) {
                                'Verified' => 'bg-blue-50 text-blue-700 border-blue-100',

                                'For Maintenance' => 'bg-violet-50 text-violet-700 border-violet-100',

                                'Assigned' => 'bg-indigo-50 text-indigo-700 border-indigo-100',

                                'In Progress' => 'bg-amber-50 text-amber-700 border-amber-100',

                                'Completed' => 'bg-green-50 text-green-700 border-green-100',

                                'Closed' => 'bg-gray-100 text-gray-700 border-gray-200',

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

                            $divisionName = $complaint->division?->name ?? '—';

                            $normalizedDivision = strtolower(trim($divisionName));

                            $isCommercial = str_contains($normalizedDivision, 'commercial');

                            $isEngineering = str_contains($normalizedDivision, 'engineering');
                        @endphp


                        <div class="p-5">


                            {{-- TOP --}}

                            <div class="flex items-start justify-between gap-3">

                                <div class="min-w-0">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <p class="text-sm font-bold text-blue-600">

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
                                                           rounded-full bg-current">
                                                </span>

                                                {{ strtoupper($urgency) }}

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
                                           text-[11px] font-semibold
                                           shrink-0
                                           {{ $statusClasses }}">

                                    <span
                                        class="w-1.5 h-1.5
                                                 rounded-full bg-current">
                                    </span>

                                    {{ $statusLabel }}
                                </span>

                            </div>



                            {{-- CONSUMER + TYPE --}}

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>

                                    <p
                                        class="text-[11px] uppercase
                                              tracking-wide font-semibold
                                              text-gray-400">
                                        Consumer
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-gray-800">

                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? '—') }}

                                    </p>


                                    @if ($complaint->consumer?->account_number)
                                        <p class="text-xs text-gray-500">

                                            {{ $complaint->consumer->account_number }}

                                        </p>
                                    @endif

                                </div>


                                <div>

                                    <p
                                        class="text-[11px] uppercase
                                              tracking-wide font-semibold
                                              text-gray-400">
                                        Complaint Type
                                    </p>

                                    <p class="mt-1 text-sm font-medium text-gray-700">

                                        {{ $complaint->category?->name ?? 'Uncategorized' }}

                                    </p>

                                    <p
                                        class="mt-1 text-xs font-semibold
                                               {{ $isCommercial ? 'text-violet-600' : 'text-blue-600' }}">

                                        {{ $divisionName }}

                                    </p>

                                </div>

                            </div>



                            {{-- RESPONSIBLE UNIT --}}

                            <div
                                class="mt-4 p-3 rounded-xl
                                        bg-gray-50 border border-gray-100">

                                <p class="text-[11px] uppercase tracking-wide
          font-semibold text-gray-400">
                                    Plumber Assignment
                                </p>


                                @php
                                    $hasPlumbers = $complaint->technicians->isNotEmpty();

                                    $isForwardedToMaintenance =
                                        in_array(
                                            $complaint->status,
                                            ['For Maintenance', 'Assigned', 'In Progress', 'Completed'],
                                            true,
                                        ) ||
                                        ($complaint->status === 'Closed' && $hasPlumbers);

                                    $isCommercialOnly = $isCommercial && !$isForwardedToMaintenance && !$hasPlumbers;
                                @endphp

                                @if ($isCommercialOnly)
                                    <div class="mt-2 flex items-center gap-2">

                                        <div
                                            class="w-8 h-8 rounded-lg
                   bg-violet-100 text-violet-700
                   flex items-center justify-center">

                                            <i class="fas fa-store text-xs"></i>

                                        </div>

                                        <div>

                                            <p class="text-sm font-semibold text-gray-600">
                                                Not Applicable
                                            </p>

                                            <p class="text-[10px] text-violet-600">
                                                Handled by Commercial Services
                                            </p>

                                        </div>

                                    </div>
                                @elseif ($hasPlumbers)
                                    <div class="mt-2 space-y-2">

                                        @foreach ($complaint->technicians as $technician)
                                            <div class="flex items-center gap-2">

                                                <div
                                                    class="w-7 h-7 rounded-full
                           bg-blue-100 text-blue-700
                           flex items-center justify-center
                           text-[10px] font-bold">

                                                    {{ strtoupper(substr($technician->first_name ?? '', 0, 1) . substr($technician->last_name ?? '', 0, 1)) }}

                                                </div>

                                                <div>

                                                    <p class="text-sm font-medium text-gray-800">
                                                        {{ $technician->full_name }}
                                                    </p>

                                                    @if ($technician->pivot?->assignment_role)
                                                        <p class="text-[10px] text-gray-400">
                                                            {{ $technician->pivot->assignment_role }}
                                                        </p>
                                                    @endif

                                                </div>

                                            </div>
                                        @endforeach

                                    </div>
                                @else
                                    <div class="mt-2 flex items-center gap-2">

                                        <div
                                            class="w-8 h-8 rounded-lg
                   bg-gray-100 text-gray-400
                   flex items-center justify-center">

                                            <i class="fas fa-user-clock text-xs"></i>

                                        </div>

                                        <div>

                                            <p class="text-sm font-medium text-gray-500">
                                                Not Assigned
                                            </p>

                                            <p class="text-[10px] text-gray-400">
                                                Awaiting plumber assignment
                                            </p>

                                        </div>

                                    </div>
                                @endif

                            </div>



                            {{-- BOTTOM --}}

                            <div class="mt-4 flex items-center justify-between">

                                <span class="text-xs text-gray-400">

                                    Updated
                                    {{ $complaint->updated_at?->diffForHumans() }}

                                </span>


                                <a href="{{ route('maintenance-manager.complaints.show', $complaint) }}"
                                    title="View Complaint" aria-label="View Complaint"
                                    class="inline-flex w-9 h-9
                                           items-center justify-center
                                           rounded-lg
                                           bg-blue-50 text-blue-600
                                           border border-blue-100
                                           hover:bg-blue-600
                                           hover:text-white
                                           hover:border-blue-600
                                           transition">

                                    <i class="fas fa-eye"></i>

                                </a>

                            </div>

                        </div>
                    @endforeach

                </div>
            @else
                <div class="px-6 py-16 text-center">

                    <div
                        class="mx-auto w-16 h-16 rounded-2xl
                               bg-gray-100 flex items-center
                               justify-center">

                        <i class="fas fa-file-circle-xmark
                                  text-2xl text-gray-400">
                        </i>

                    </div>


                    <h3 class="mt-4 text-base font-semibold text-gray-900">
                        No complaints found
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Try adjusting your search or filters.
                    </p>


                    @if (request()->hasAny(['search', 'status', 'urgency', 'date_from', 'date_to']))
                        <a href="{{ route('maintenance-manager.complaints.index') }}"
                            class="mt-4 inline-flex items-center gap-2
                                   px-4 py-2.5 rounded-xl
                                   bg-gray-100 text-gray-700
                                   text-sm font-semibold
                                   hover:bg-gray-200 transition">

                            <i class="fas fa-rotate-left"></i>

                            Clear Filters

                        </a>
                    @endif

                </div>

            @endif

            @if ($complaints->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">

                    {{ $complaints->withQueryString()->links() }}

                </div>
            @endif

        </div>

    </div>

@endsection
