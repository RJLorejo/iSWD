@extends('technician.layouts.app')

@section('title', 'Technician Dashboard')

@section('content')

<div class="space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

        <div>

            <div class="flex items-center gap-3">

                <div class="w-11 h-11 rounded-2xl
                            bg-indigo-100 text-indigo-600
                            flex items-center justify-center">

                    <i class="fas fa-screwdriver-wrench"></i>

                </div>

                <div>

                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        Technician Dashboard
                    </h1>

                    <p class="text-sm text-gray-500 mt-1">
                        Maintenance Department
                    </p>

                </div>

            </div>

            <p class="text-sm text-gray-500 mt-3 max-w-2xl">
                View your assigned complaints, coordinate with your maintenance
                team, perform field work, and submit maintenance reports.
            </p>

        </div>


        {{-- QUICK ACTION --}}

        <div class="flex flex-wrap gap-3">

            <a
                href="{{ route('technician.complaints.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5
                       rounded-xl bg-indigo-600 text-white
                       text-sm font-semibold
                       hover:bg-indigo-700 transition shadow-sm"
            >

                <i class="fas fa-clipboard-list"></i>

                My Maintenance Work

            </a>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- WORKFLOW NOTICE --}}
    {{-- ========================================================= --}}

    <div class="rounded-2xl border border-indigo-100
                bg-indigo-50 px-5 py-4">

        <div class="flex items-start gap-3">

            <div class="w-9 h-9 rounded-xl bg-white
                        text-indigo-600 flex items-center
                        justify-center shrink-0">

                <i class="fas fa-circle-info"></i>

            </div>

            <div>

                <p class="text-sm font-semibold text-indigo-900">
                    Your maintenance workflow
                </p>

                <p class="text-xs sm:text-sm text-indigo-700 mt-1">
                    Assigned complaints appear here. Open a complaint to review
                    the consumer information and assigned team, then start
                    maintenance when field work begins.
                </p>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- STATISTICS --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">


        {{-- ASSIGNED --}}

        <x-form.card>

            <div class="p-5">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <p class="text-sm text-gray-500">
                            Assigned Work
                        </p>

                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ $assignedCount }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            Waiting to start
                        </p>

                    </div>

                    <div class="w-12 h-12 shrink-0 rounded-xl
                                bg-blue-100 text-blue-600
                                flex items-center justify-center">

                        <i class="fas fa-clipboard-list text-lg"></i>

                    </div>

                </div>

            </div>

        </x-form.card>


        {{-- IN PROGRESS --}}

        <x-form.card>

            <div class="p-5">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <p class="text-sm text-gray-500">
                            In Progress
                        </p>

                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ $inProgressCount }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            Currently working
                        </p>

                    </div>

                    <div class="w-12 h-12 shrink-0 rounded-xl
                                bg-amber-100 text-amber-600
                                flex items-center justify-center">

                        <i class="fas fa-screwdriver-wrench text-lg"></i>

                    </div>

                </div>

            </div>

        </x-form.card>


        {{-- COMPLETED --}}

        <x-form.card>

            <div class="p-5">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <p class="text-sm text-gray-500">
                            Completed
                        </p>

                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ $completedCount }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            Completed maintenance
                        </p>

                    </div>

                    <div class="w-12 h-12 shrink-0 rounded-xl
                                bg-green-100 text-green-600
                                flex items-center justify-center">

                        <i class="fas fa-circle-check text-lg"></i>

                    </div>

                </div>

            </div>

        </x-form.card>


        {{-- URGENT --}}

        <x-form.card>

            <div class="p-5">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <p class="text-sm text-gray-500">
                            Urgent
                        </p>

                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ $urgentCount }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            High / Critical active work
                        </p>

                    </div>

                    <div class="w-12 h-12 shrink-0 rounded-xl
                                {{ $urgentCount > 0
                                    ? 'bg-red-100 text-red-600'
                                    : 'bg-gray-100 text-gray-500' }}
                                flex items-center justify-center">

                        <i class="fas fa-triangle-exclamation text-lg"></i>

                    </div>

                </div>

            </div>

        </x-form.card>

    </div>


    {{-- ========================================================= --}}
    {{-- QUICK OVERVIEW --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">


        {{-- ACTIVE WORK --}}

        <x-form.card>

            <div class="p-5">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-gray-500">
                            Active Work
                        </p>

                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            {{ $activeCount }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            Assigned + in progress
                        </p>

                    </div>

                    <div class="w-11 h-11 rounded-xl
                                bg-indigo-100 text-indigo-600
                                flex items-center justify-center">

                        <i class="fas fa-list-check"></i>

                    </div>

                </div>

            </div>

        </x-form.card>


        {{-- TOTAL WORK --}}

        <x-form.card>

            <div class="p-5">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-gray-500">
                            Total Assigned
                        </p>

                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            {{ $totalCount }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            All complaints assigned to you
                        </p>

                    </div>

                    <div class="w-11 h-11 rounded-xl
                                bg-gray-100 text-gray-600
                                flex items-center justify-center">

                        <i class="fas fa-layer-group"></i>

                    </div>

                </div>

            </div>

        </x-form.card>


        {{-- COMPLETION --}}

        <x-form.card>

            <div class="p-5">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-gray-500">
                            Completed Work
                        </p>

                        <p class="text-2xl font-bold text-green-600 mt-1">
                            {{ $completedCount }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            Successfully completed
                        </p>

                    </div>

                    <div class="w-11 h-11 rounded-xl
                                bg-green-100 text-green-600
                                flex items-center justify-center">

                        <i class="fas fa-chart-line"></i>

                    </div>

                </div>

            </div>

        </x-form.card>

    </div>


    {{-- ========================================================= --}}
    {{-- CURRENT WORK --}}
    {{-- ========================================================= --}}

    <x-form.card>

        <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

            <div class="flex flex-col sm:flex-row
                        sm:items-center sm:justify-between gap-3">

                <div>

                    <h2 class="font-semibold text-gray-900">

                        <i class="fas fa-clipboard-check
                                  text-indigo-600 mr-2"></i>

                        My Current Work

                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Active complaints currently assigned to you.
                    </p>

                </div>

                <div class="flex items-center gap-2">

                    @if($urgentCount > 0)

                        <span class="inline-flex items-center gap-1.5
                                     px-3 py-1.5 rounded-full
                                     bg-red-50 text-red-700
                                     text-xs font-semibold">

                            <i class="fas fa-triangle-exclamation"></i>

                            {{ $urgentCount }} Urgent

                        </span>

                    @endif

                    <span class="inline-flex items-center
                                 px-3 py-1.5 rounded-full
                                 bg-indigo-50 text-indigo-700
                                 text-xs font-semibold">

                        {{ $activeCount }} Active

                    </span>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- DESKTOP --}}
        {{-- ===================================================== --}}

        <div class="hidden md:block overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50 border-b border-gray-200">

                    <tr>

                        <th class="px-6 py-4 text-left font-semibold text-gray-600">
                            Complaint
                        </th>

                        <th class="px-6 py-4 text-left font-semibold text-gray-600">
                            Consumer
                        </th>

                        <th class="px-6 py-4 text-left font-semibold text-gray-600">
                            Location
                        </th>

                        <th class="px-6 py-4 text-left font-semibold text-gray-600">
                            Team
                        </th>

                        <th class="px-6 py-4 text-left font-semibold text-gray-600">
                            Priority
                        </th>

                        <th class="px-6 py-4 text-left font-semibold text-gray-600">
                            Status
                        </th>

                        <th class="px-6 py-4 text-right font-semibold text-gray-600">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100">

                    @forelse($currentComplaints as $complaint)

                        <tr class="hover:bg-gray-50 transition">


                            {{-- COMPLAINT --}}

                            <td class="px-6 py-4">

                                <p class="font-semibold text-gray-900">
                                    {{ $complaint->complaint_no }}
                                </p>

                                <p class="text-gray-500 mt-1">
                                    {{ Str::limit($complaint->subject, 42) }}
                                </p>

                            </td>


                            {{-- CONSUMER --}}

                            <td class="px-6 py-4">

                                <p class="font-medium text-gray-800">

                                    {{ $complaint->consumer?->full_name
                                        ?? $complaint->complainant_name
                                        ?? '—' }}

                                </p>

                                @if($complaint->consumer?->account_number)

                                    <p class="text-xs text-gray-400 mt-1">

                                        Account:
                                        {{ $complaint->consumer->account_number }}

                                    </p>

                                @endif

                            </td>


                            {{-- LOCATION --}}

                            <td class="px-6 py-4">

                                <div class="flex items-start gap-2">

                                    <i class="fas fa-location-dot
                                              text-gray-400 mt-1"></i>

                                    <div>

                                        <p class="text-gray-700">
                                            {{ Str::limit($complaint->address, 38) }}
                                        </p>

                                        @if(
                                            $complaint->latitude !== null &&
                                            $complaint->longitude !== null
                                        )

                                            <p class="text-xs text-green-600 mt-1">

                                                <i class="fas fa-map-location-dot mr-1"></i>

                                                Map location available

                                            </p>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- TEAM --}}

                            <td class="px-6 py-4">

                                @if($complaint->technicians->count())

                                    <div class="space-y-1.5">

                                        @foreach($complaint->technicians as $teamTechnician)

                                            <div class="flex items-center gap-2">

                                                <div class="w-7 h-7 rounded-lg
                                                            bg-indigo-100
                                                            text-indigo-700
                                                            flex items-center
                                                            justify-center
                                                            text-[10px]
                                                            font-bold shrink-0">

                                                    {{ strtoupper(
                                                        substr($teamTechnician->first_name ?? '', 0, 1)
                                                        .
                                                        substr($teamTechnician->last_name ?? '', 0, 1)
                                                    ) }}

                                                </div>

                                                <span class="text-xs font-medium
                                                             text-gray-700">

                                                    {{ $teamTechnician->full_name }}

                                                </span>

                                            </div>

                                        @endforeach

                                    </div>

                                @else

                                    <span class="text-xs text-gray-400">
                                        No team assigned
                                    </span>

                                @endif

                            </td>


                            {{-- PRIORITY --}}

                            <td class="px-6 py-4">

                                @php

                                    $priority = $complaint->priority ?? 'Low';

                                    $priorityClass = match($priority) {

                                        'Critical' =>
                                            'bg-red-100 text-red-700',

                                        'High' =>
                                            'bg-orange-100 text-orange-700',

                                        'Medium' =>
                                            'bg-yellow-100 text-yellow-700',

                                        default =>
                                            'bg-green-100 text-green-700',

                                    };

                                @endphp

                                <span class="inline-flex items-center gap-1.5
                                             px-2.5 py-1 rounded-full
                                             text-xs font-semibold
                                             {{ $priorityClass }}">

                                    @if($priority === 'Critical')

                                        <i class="fas fa-triangle-exclamation"></i>

                                    @elseif($priority === 'High')

                                        <i class="fas fa-arrow-up"></i>

                                    @endif

                                    {{ $priority }}

                                </span>

                            </td>


                            {{-- STATUS --}}

                            <td class="px-6 py-4">

                                @if($complaint->status === 'Assigned')

                                    <span class="inline-flex items-center gap-1.5
                                                 px-2.5 py-1 rounded-full
                                                 bg-blue-100 text-blue-700
                                                 text-xs font-semibold">

                                        <i class="fas fa-user-check"></i>

                                        Assigned

                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-1.5
                                                 px-2.5 py-1 rounded-full
                                                 bg-amber-100 text-amber-700
                                                 text-xs font-semibold">

                                        <i class="fas fa-spinner"></i>

                                        In Progress

                                    </span>

                                @endif

                            </td>


                            {{-- ACTION --}}

                            <td class="px-6 py-4 text-right">

                                <a
                                    href="{{ route('technician.complaints.show', $complaint) }}"
                                    class="inline-flex items-center gap-2
                                           px-3 py-2 rounded-lg
                                           bg-indigo-600 text-white
                                           hover:bg-indigo-700 transition
                                           text-xs font-semibold"
                                >

                                    <i class="fas fa-eye"></i>

                                    View Work

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="px-6 py-14 text-center">

                                <div class="w-14 h-14 mx-auto rounded-2xl
                                            bg-green-100 text-green-600
                                            flex items-center justify-center">

                                    <i class="fas fa-circle-check text-xl"></i>

                                </div>

                                <h3 class="font-semibold text-gray-900 mt-4">
                                    No Active Work
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    You currently have no assigned maintenance
                                    complaints requiring action.
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

        <div class="md:hidden divide-y divide-gray-100">

            @forelse($currentComplaints as $complaint)

                <div class="p-5 space-y-4">


                    {{-- HEADER --}}

                    <div class="flex items-start justify-between gap-3">

                        <div class="min-w-0">

                            <p class="font-semibold text-gray-900">
                                {{ $complaint->complaint_no }}
                            </p>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ Str::limit($complaint->subject, 65) }}
                            </p>

                        </div>


                        @php

                            $priority = $complaint->priority ?? 'Low';

                            $priorityClass = match($priority) {

                                'Critical' =>
                                    'bg-red-100 text-red-700',

                                'High' =>
                                    'bg-orange-100 text-orange-700',

                                'Medium' =>
                                    'bg-yellow-100 text-yellow-700',

                                default =>
                                    'bg-green-100 text-green-700',

                            };

                        @endphp

                        <span class="shrink-0 px-2.5 py-1 rounded-full
                                     text-xs font-semibold
                                     {{ $priorityClass }}">

                            {{ $priority }}

                        </span>

                    </div>


                    {{-- CONSUMER --}}

                    <div class="flex items-start gap-2 text-sm text-gray-600">

                        <i class="fas fa-user text-gray-400 mt-1"></i>

                        <div>

                            <p class="font-medium text-gray-800">

                                {{ $complaint->consumer?->full_name
                                    ?? $complaint->complainant_name
                                    ?? '—' }}

                            </p>

                            @if($complaint->consumer?->account_number)

                                <p class="text-xs text-gray-400 mt-0.5">

                                    Account:
                                    {{ $complaint->consumer->account_number }}

                                </p>

                            @endif

                        </div>

                    </div>


                    {{-- LOCATION --}}

                    <div class="flex items-start gap-2 text-sm text-gray-600">

                        <i class="fas fa-location-dot
                                  text-gray-400 mt-1"></i>

                        <span>
                            {{ $complaint->address }}
                        </span>

                    </div>


                    {{-- TEAM --}}

                    <div class="rounded-xl bg-gray-50
                                border border-gray-100 p-3">

                        <p class="text-[10px] uppercase
                                  tracking-wide font-bold
                                  text-gray-400 mb-2">

                            Assigned Team

                        </p>

                        <div class="space-y-2">

                            @foreach($complaint->technicians as $teamTechnician)

                                <div class="flex items-center gap-2">

                                    <div class="w-7 h-7 rounded-lg
                                                bg-indigo-100 text-indigo-700
                                                flex items-center
                                                justify-center
                                                text-[10px] font-bold">

                                        {{ strtoupper(
                                            substr($teamTechnician->first_name ?? '', 0, 1)
                                            .
                                            substr($teamTechnician->last_name ?? '', 0, 1)
                                        ) }}

                                    </div>

                                    <span class="text-xs font-medium
                                                 text-gray-700">

                                        {{ $teamTechnician->full_name }}

                                    </span>

                                </div>

                            @endforeach

                        </div>

                    </div>


                    {{-- STATUS + ACTION --}}

                    <div class="flex items-center justify-between gap-3">

                        <span class="text-xs font-semibold
                                     {{ $complaint->status === 'Assigned'
                                        ? 'text-blue-700'
                                        : 'text-amber-700' }}">

                            <i class="fas fa-circle text-[7px] mr-1"></i>

                            {{ $complaint->status }}

                        </span>


                        <a
                            href="{{ route('technician.complaints.show', $complaint) }}"
                            class="inline-flex items-center gap-2
                                   px-3 py-2 rounded-lg
                                   bg-indigo-600 text-white
                                   text-sm font-semibold
                                   hover:bg-indigo-700"
                        >

                            <i class="fas fa-eye"></i>

                            View Work

                        </a>

                    </div>

                </div>

            @empty

                <div class="p-10 text-center">

                    <div class="w-14 h-14 mx-auto rounded-2xl
                                bg-green-100 text-green-600
                                flex items-center justify-center">

                        <i class="fas fa-circle-check text-xl"></i>

                    </div>

                    <p class="font-semibold text-gray-900 mt-4">
                        No active work
                    </p>

                    <p class="text-sm text-gray-500 mt-1">
                        New assigned complaints will appear here.
                    </p>

                </div>

            @endforelse

        </div>

    </x-form.card>


    {{-- ========================================================= --}}
    {{-- RECENTLY COMPLETED --}}
    {{-- ========================================================= --}}

    <x-form.card>

        <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

            <div class="flex items-center justify-between gap-3">

                <div>

                    <h2 class="font-semibold text-gray-900">

                        <i class="fas fa-clock-rotate-left
                                  text-green-600 mr-2"></i>

                        Recently Completed

                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Your most recently completed maintenance work.
                    </p>

                </div>


                <a
                    href="{{ route(
                        'technician.complaints.index',
                        ['status' => 'Completed']
                    ) }}"
                    class="text-sm font-semibold text-indigo-600
                           hover:text-indigo-700"
                >

                    View All

                </a>

            </div>

        </div>


        <div class="divide-y divide-gray-100">

            @forelse($recentCompleted as $complaint)

                <div class="p-5 flex items-center
                            justify-between gap-4">

                    <div class="min-w-0">

                        <p class="font-semibold text-gray-900">
                            {{ $complaint->complaint_no }}
                        </p>

                        <p class="text-sm text-gray-500 truncate mt-1">
                            {{ $complaint->subject }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1">

                            <i class="fas fa-location-dot mr-1"></i>

                            {{ Str::limit($complaint->address, 55) }}

                        </p>

                    </div>


                    <div class="text-right shrink-0">

                        <span class="inline-flex items-center gap-1
                                     text-xs font-semibold text-green-700">

                            <i class="fas fa-circle-check"></i>

                            Completed

                        </span>

                        @if($complaint->completed_at)

                            <p class="text-xs text-gray-400 mt-1">

                                {{ $complaint->completed_at->format('M d, Y') }}

                            </p>

                        @endif

                    </div>

                </div>

            @empty

                <div class="p-10 text-center">

                    <i class="fas fa-clock-rotate-left
                              text-gray-300 text-3xl"></i>

                    <p class="text-sm text-gray-500 mt-3">
                        No completed maintenance work yet.
                    </p>

                </div>

            @endforelse

        </div>

    </x-form.card>


    {{-- ========================================================= --}}
    {{-- RECENT ACTIVITY --}}
    {{-- ========================================================= --}}

    <x-form.card>

        <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

            <div>

                <h2 class="font-semibold text-gray-900">

                    <i class="fas fa-clock
                              text-indigo-600 mr-2"></i>

                    Recent Activity

                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Recently updated complaints assigned to you.
                </p>

            </div>

        </div>


        <div class="divide-y divide-gray-100">

            @forelse($recentActivity as $complaint)

                <a
                    href="{{ route(
                        'technician.complaints.show',
                        $complaint
                    ) }}"
                    class="block p-5 hover:bg-gray-50 transition"
                >

                    <div class="flex items-start
                                justify-between gap-4">

                        <div class="min-w-0">

                            <div class="flex items-center gap-2">

                                <span class="font-semibold text-gray-900">

                                    {{ $complaint->complaint_no }}

                                </span>

                                @if($complaint->status === 'Assigned')

                                    <span class="px-2 py-0.5 rounded-full
                                                 bg-blue-50 text-blue-700
                                                 text-[10px] font-semibold">

                                        Assigned

                                    </span>

                                @elseif($complaint->status === 'In Progress')

                                    <span class="px-2 py-0.5 rounded-full
                                                 bg-amber-50 text-amber-700
                                                 text-[10px] font-semibold">

                                        In Progress

                                    </span>

                                @elseif($complaint->status === 'Completed')

                                    <span class="px-2 py-0.5 rounded-full
                                                 bg-green-50 text-green-700
                                                 text-[10px] font-semibold">

                                        Completed

                                    </span>

                                @else

                                    <span class="px-2 py-0.5 rounded-full
                                                 bg-gray-50 text-gray-600
                                                 text-[10px] font-semibold">

                                        {{ $complaint->status }}

                                    </span>

                                @endif

                            </div>


                            <p class="text-sm text-gray-600 mt-1 truncate">

                                {{ $complaint->subject }}

                            </p>

                            <p class="text-xs text-gray-400 mt-1">

                                {{ $complaint->category?->name
                                    ?? 'Uncategorized' }}

                                ·

                                {{ $complaint->updated_at?->diffForHumans() }}

                            </p>

                        </div>


                        <i class="fas fa-chevron-right
                                  text-gray-300 mt-1"></i>

                    </div>

                </a>

            @empty

                <div class="p-10 text-center">

                    <i class="fas fa-clock
                              text-gray-300 text-3xl"></i>

                    <p class="text-sm text-gray-500 mt-3">
                        No recent activity.
                    </p>

                </div>

            @endforelse

        </div>

    </x-form.card>

</div>

@endsection
