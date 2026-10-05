@extends('technician.layouts.app')

@section('title', 'Work Summary')

@section('content')

    @php
        $workStatusTotal = $statusBreakdown->sum();
        $urgencyTotal = $urgencyBreakdown->sum();
    @endphp

    <div class="max-w-7xl mx-auto space-y-5">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Work Summary
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Overview of your assigned maintenance work and accomplishments.
                </p>
            </div>

            <a href="{{ route('technician.reports.maintenance.print', request()->query()) }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-2
                       px-4 py-2.5 rounded-xl
                       border border-slate-200 bg-white
                       text-sm font-semibold text-slate-700
                       shadow-sm hover:bg-slate-50 transition">

                <i class="fas fa-print text-sky-600"></i>

                Print Summary
            </a>

        </div>


        {{-- Date Filter --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">

            <form method="GET"
                action="{{ route('technician.reports.maintenance') }}"
                class="flex flex-col lg:flex-row lg:items-end gap-3">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-1">

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">
                            From Date
                        </label>

                        <input type="date"
                            name="from"
                            value="{{ request('from', $from->format('Y-m-d')) }}"
                            class="w-full rounded-xl border-slate-300
                                   text-sm text-slate-700
                                   focus:border-sky-500 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">
                            To Date
                        </label>

                        <input type="date"
                            name="to"
                            value="{{ request('to', $to->format('Y-m-d')) }}"
                            class="w-full rounded-xl border-slate-300
                                   text-sm text-slate-700
                                   focus:border-sky-500 focus:ring-sky-500">
                    </div>

                </div>

                <div class="flex gap-2">

                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2
                               px-4 py-2.5 rounded-xl
                               bg-sky-600 text-white
                               text-sm font-semibold
                               hover:bg-sky-700 transition">

                        <i class="fas fa-filter"></i>

                        Apply
                    </button>

                    <a href="{{ route('technician.reports.maintenance') }}"
                        class="inline-flex items-center justify-center
                               w-10 h-10 rounded-xl
                               border border-slate-200
                               bg-white text-slate-500
                               hover:bg-slate-50 transition"
                        title="Reset">

                        <i class="fas fa-rotate-left"></i>
                    </a>

                </div>

            </form>

        </div>


        {{-- Report Period --}}
        <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">

            <i class="fas fa-calendar-days text-sky-600"></i>

            <span>
                Report period:
            </span>

            <span class="font-semibold text-slate-700">
                {{ $from->format('M d, Y') }}
            </span>

            <span class="text-slate-300">
                —
            </span>

            <span class="font-semibold text-slate-700">
                {{ $to->format('M d, Y') }}
            </span>

        </div>


        {{-- Performance Overview --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-5 py-4 border-b border-slate-100">

                <h2 class="font-semibold text-slate-900">
                    Performance Overview
                </h2>

                <p class="text-xs text-slate-500 mt-1">
                    Maintenance performance for the selected reporting period.
                </p>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3">

                {{-- Accomplished --}}
                <div class="p-5 border-b sm:border-b-0 sm:border-r border-slate-100">

                    <div class="flex items-center justify-between gap-3">

                        <div>

                            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">

                                <span class="w-7 h-7 rounded-lg bg-green-50 text-green-600
                                             flex items-center justify-center">

                                    <i class="fas fa-circle-check"></i>

                                </span>

                                Accomplished

                            </div>

                            <p class="text-3xl font-bold text-slate-900 mt-3">
                                {{ $accomplishedCount }}
                            </p>

                            <p class="text-xs text-slate-400 mt-1">
                                Completed maintenance work
                            </p>

                        </div>

                    </div>

                </div>


                {{-- Completion Rate --}}
                <div class="p-5 border-b sm:border-b-0 sm:border-r border-slate-100">

                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500">

                        <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600
                                     flex items-center justify-center">

                            <i class="fas fa-chart-line"></i>

                        </span>

                        Completion Rate

                    </div>

                    <p class="text-3xl font-bold text-slate-900 mt-3">
                        {{ $completionRate }}%
                    </p>

                    <p class="text-xs text-slate-400 mt-1">
                        Accomplished assigned work
                    </p>

                </div>


                {{-- Average Completion --}}
                <div class="p-5">

                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500">

                        <span class="w-7 h-7 rounded-lg bg-violet-50 text-violet-600
                                     flex items-center justify-center">

                            <i class="fas fa-stopwatch"></i>

                        </span>

                        Avg. Completion Time

                    </div>

                    <div class="flex items-end gap-1 mt-3">

                        <p class="text-3xl font-bold text-slate-900">
                            {{ $averageCompletionHours }}
                        </p>

                        <span class="text-sm font-medium text-slate-400 mb-1">
                            hrs
                        </span>

                    </div>

                    <p class="text-xs text-slate-400 mt-1">
                        Average time to accomplish work
                    </p>

                </div>

            </div>

        </div>


        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">


            {{-- AI Urgency --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-slate-100">

                    <div class="flex items-start justify-between gap-3">

                        <div>

                            <h2 class="font-semibold text-slate-900">
                                AI Urgency Overview
                            </h2>

                            <p class="text-xs text-slate-500 mt-1">
                                Urgency distribution of current maintenance work.
                            </p>

                        </div>

                        <span class="inline-flex items-center justify-center
                                     min-w-8 h-8 px-2 rounded-full
                                     bg-slate-100 text-slate-600
                                     text-xs font-bold">

                            {{ $urgencyTotal }}

                        </span>

                    </div>

                </div>


                <div class="p-5 space-y-5">

                    @foreach ($urgencyBreakdown as $urgency => $count)

                        @php
                            $urgencyUpper = strtoupper($urgency);

                            $barClass = match ($urgencyUpper) {
                                'HIGH' => 'bg-red-500',
                                'MODERATE' => 'bg-amber-500',
                                'LOW' => 'bg-green-500',
                                default => 'bg-slate-400',
                            };

                            $dotClass = match ($urgencyUpper) {
                                'HIGH' => 'bg-red-500',
                                'MODERATE' => 'bg-amber-500',
                                'LOW' => 'bg-green-500',
                                default => 'bg-slate-400',
                            };

                            $textClass = match ($urgencyUpper) {
                                'HIGH' => 'text-red-600',
                                'MODERATE' => 'text-amber-600',
                                'LOW' => 'text-green-600',
                                default => 'text-slate-600',
                            };

                            $percentage = $urgencyTotal > 0
                                ? ($count / $urgencyTotal) * 100
                                : 0;
                        @endphp

                        <div>

                            <div class="flex items-center justify-between gap-4 mb-2">

                                <div class="flex items-center gap-2">

                                    <span class="w-2 h-2 rounded-full {{ $dotClass }}"></span>

                                    <span class="text-sm font-medium {{ $textClass }}">
                                        {{ $urgency }}
                                    </span>

                                </div>

                                <div class="flex items-center gap-3">

                                    <span class="text-xs text-slate-400">
                                        {{ number_format($percentage, 0) }}%
                                    </span>

                                    <span class="text-sm font-bold text-slate-900 min-w-5 text-right">
                                        {{ $count }}
                                    </span>

                                </div>

                            </div>

                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">

                                <div class="h-full rounded-full {{ $barClass }} transition-all"
                                    style="width: {{ min($percentage, 100) }}%">
                                </div>

                            </div>

                        </div>

                    @endforeach

                    @if ($urgencyTotal === 0)

                        <div class="py-4 text-center">

                            <div class="w-10 h-10 mx-auto rounded-xl
                                        bg-slate-100 text-slate-400
                                        flex items-center justify-center">

                                <i class="fas fa-chart-bar"></i>

                            </div>

                            <p class="text-sm font-semibold text-slate-700 mt-3">
                                No current work
                            </p>

                            <p class="text-xs text-slate-400 mt-1">
                                Urgency data will appear when work is assigned.
                            </p>

                        </div>

                    @endif

                </div>

            </div>


            {{-- Work Status --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-slate-100">

                    <h2 class="font-semibold text-slate-900">
                        Work Status
                    </h2>

                    <p class="text-xs text-slate-500 mt-1">
                        Distribution of your assigned maintenance work.
                    </p>

                </div>


                <div class="p-5">

                    <div class="grid grid-cols-1 sm:grid-cols-2 items-center gap-6">


                        {{-- Doughnut --}}
                        <div class="flex items-center justify-center">

                            <div class="relative w-48 h-48">

                                <canvas id="workStatusChart"></canvas>

                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">

                                    <div class="text-center">

                                        <p class="text-3xl font-bold text-slate-900">
                                            {{ $workStatusTotal }}
                                        </p>

                                        <p class="text-[11px] font-medium text-slate-400 uppercase tracking-wide">
                                            Total Work
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Legend --}}
                        <div class="space-y-3">

                            <div class="flex items-center justify-between
                                        rounded-xl border border-slate-100
                                        bg-slate-50/60 px-4 py-3">

                                <div class="flex items-center gap-3">

                                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>

                                    <span class="text-sm text-slate-600">
                                        Assigned
                                    </span>

                                </div>

                                <span class="text-lg font-bold text-slate-900">
                                    {{ $assignedCount }}
                                </span>

                            </div>


                            <div class="flex items-center justify-between
                                        rounded-xl border border-slate-100
                                        bg-slate-50/60 px-4 py-3">

                                <div class="flex items-center gap-3">

                                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>

                                    <span class="text-sm text-slate-600">
                                        In Progress
                                    </span>

                                </div>

                                <span class="text-lg font-bold text-slate-900">
                                    {{ $inProgressCount }}
                                </span>

                            </div>


                            <div class="flex items-center justify-between
                                        rounded-xl border border-slate-100
                                        bg-slate-50/60 px-4 py-3">

                                <div class="flex items-center gap-3">

                                    <span class="w-3 h-3 rounded-full bg-green-500"></span>

                                    <span class="text-sm text-slate-600">
                                        Accomplished
                                    </span>

                                </div>

                                <span class="text-lg font-bold text-slate-900">
                                    {{ $accomplishedCount }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Current Work --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-5 sm:px-6 py-4 border-b border-slate-100">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <h2 class="font-semibold text-slate-900">
                            Current Work
                        </h2>

                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Assigned and in-progress maintenance requiring action.
                        </p>

                    </div>

                    <span class="inline-flex items-center justify-center
                                 min-w-8 h-8 px-2 rounded-full
                                 bg-sky-50 text-sky-700
                                 text-sm font-bold">

                        {{ $currentComplaints->count() }}

                    </span>

                </div>

            </div>


            {{-- Desktop --}}
            <div class="hidden lg:block overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-slate-50/80 border-b border-slate-200">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Complaint
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Consumer
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Complaint Type
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse ($currentComplaints as $complaint)

                            @php
                                $urgency = strtoupper(
                                    trim(
                                        $complaint->aiAnalysis?->urgency_level ?? ''
                                    )
                                );

                                $urgencyClasses = match ($urgency) {
                                    'HIGH' => 'bg-red-50 text-red-700 border-red-200',
                                    'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'LOW' => 'bg-green-50 text-green-700 border-green-200',
                                    default => 'bg-slate-50 text-slate-500 border-slate-200',
                                };

                                $statusClasses = match ($complaint->status) {
                                    'Assigned' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'In Progress' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    default => 'bg-slate-50 text-slate-600 border-slate-200',
                                };
                            @endphp

                            <tr class="hover:bg-slate-50/60 transition">

                                <td class="px-5 py-4">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <span class="font-bold text-sky-700">
                                            {{ $complaint->complaint_no }}
                                        </span>

                                        <span class="inline-flex items-center
                                                     px-2 py-0.5 rounded-full border
                                                     text-[10px] font-bold
                                                     {{ $urgencyClasses }}">

                                            {{ $urgency ?: 'NOT ASSESSED' }}

                                        </span>

                                    </div>

                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ $complaint->created_at?->format('M d, Y h:i A') }}
                                    </p>

                                </td>


                                <td class="px-5 py-4">

                                    <p class="font-medium text-slate-900">
                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}
                                    </p>

                                    @if ($complaint->consumer?->account_number)

                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ $complaint->consumer->account_number }}
                                        </p>

                                    @endif

                                </td>


                                <td class="px-5 py-4 text-slate-700">
                                    {{ $complaint->category?->name ?? 'Uncategorized' }}
                                </td>


                                <td class="px-5 py-4">

                                    <span class="inline-flex items-center gap-1.5
                                                 px-2.5 py-1 rounded-full border
                                                 text-xs font-semibold
                                                 {{ $statusClasses }}">

                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                        {{ $complaint->status }}

                                    </span>

                                </td>


                                <td class="px-5 py-4 text-center">

                                    <a href="{{ route('technician.complaints.show', $complaint) }}"
                                        title="View Complaint"
                                        class="inline-flex w-9 h-9 items-center justify-center
                                               rounded-lg bg-sky-50 text-sky-600
                                               border border-sky-100
                                               hover:bg-sky-600 hover:text-white transition">

                                        <i class="fas fa-eye"></i>

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="px-6 py-12 text-center">

                                    <div class="w-12 h-12 mx-auto rounded-xl
                                                bg-slate-100 text-slate-400
                                                flex items-center justify-center">

                                        <i class="fas fa-clipboard-check"></i>

                                    </div>

                                    <p class="font-semibold text-slate-900 mt-3">
                                        No active maintenance work
                                    </p>

                                    <p class="text-sm text-slate-500 mt-1">
                                        You have no assigned or in-progress maintenance.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Mobile --}}
            <div class="lg:hidden divide-y divide-slate-100">

                @forelse ($currentComplaints as $complaint)

                    @php
                        $urgency = strtoupper(
                            trim(
                                $complaint->aiAnalysis?->urgency_level ?? ''
                            )
                        );

                        $urgencyClasses = match ($urgency) {
                            'HIGH' => 'bg-red-50 text-red-700 border-red-200',
                            'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'LOW' => 'bg-green-50 text-green-700 border-green-200',
                            default => 'bg-slate-50 text-slate-500 border-slate-200',
                        };

                        $statusClasses = match ($complaint->status) {
                            'Assigned' => 'bg-blue-50 text-blue-700 border-blue-100',
                            'In Progress' => 'bg-amber-50 text-amber-700 border-amber-100',
                            default => 'bg-slate-50 text-slate-600 border-slate-200',
                        };
                    @endphp

                    <div class="p-4">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">

                                    <span class="font-bold text-sky-700">
                                        {{ $complaint->complaint_no }}
                                    </span>

                                    <span class="inline-flex px-2 py-0.5 rounded-full
                                                 border text-[10px] font-bold
                                                 {{ $urgencyClasses }}">

                                        {{ $urgency ?: 'NOT ASSESSED' }}

                                    </span>

                                </div>

                                <p class="text-xs text-slate-400 mt-1">
                                    {{ $complaint->created_at?->format('M d, Y h:i A') }}
                                </p>

                            </div>


                            <span class="inline-flex items-center gap-1.5
                                         px-2 py-1 rounded-full border
                                         text-[10px] font-semibold shrink-0
                                         {{ $statusClasses }}">

                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                {{ $complaint->status }}

                            </span>

                        </div>


                        <div class="grid grid-cols-2 gap-4 mt-4">

                            <div>

                                <p class="text-[10px] uppercase tracking-wide font-semibold text-slate-400">
                                    Consumer
                                </p>

                                <p class="text-sm font-semibold text-slate-800 mt-1">
                                    {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}
                                </p>

                            </div>

                            <div>

                                <p class="text-[10px] uppercase tracking-wide font-semibold text-slate-400">
                                    Complaint Type
                                </p>

                                <p class="text-sm text-slate-700 mt-1">
                                    {{ $complaint->category?->name ?? 'Uncategorized' }}
                                </p>

                            </div>

                        </div>


                        <div class="flex items-center justify-between mt-4">

                            <span class="text-xs text-slate-400">
                                Updated {{ $complaint->updated_at?->diffForHumans() }}
                            </span>

                            <a href="{{ route('technician.complaints.show', $complaint) }}"
                                title="View Complaint"
                                class="inline-flex w-9 h-9 items-center justify-center
                                       rounded-lg bg-sky-50 text-sky-600
                                       border border-sky-100">

                                <i class="fas fa-eye"></i>

                            </a>

                        </div>

                    </div>

                @empty

                    <div class="p-10 text-center">

                        <div class="w-12 h-12 mx-auto rounded-xl
                                    bg-slate-100 text-slate-400
                                    flex items-center justify-center">

                            <i class="fas fa-clipboard-check"></i>

                        </div>

                        <p class="font-semibold text-slate-900 mt-3">
                            No active maintenance work
                        </p>

                    </div>

                @endforelse

            </div>

        </div>


        {{-- Accomplished Work --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-5 sm:px-6 py-4 border-b border-slate-100">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <h2 class="font-semibold text-slate-900">
                            Accomplished Work
                        </h2>

                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Completed maintenance work during the selected reporting period.
                        </p>

                    </div>

                    <span class="inline-flex items-center justify-center
                                 min-w-8 h-8 px-2 rounded-full
                                 bg-green-50 text-green-700
                                 text-sm font-bold">

                        {{ $completedComplaintsList->count() }}

                    </span>

                </div>

            </div>


            {{-- Desktop --}}
            <div class="hidden lg:block overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-slate-50/80 border-b border-slate-200">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Complaint
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Consumer
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Complaint Type
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Accomplished
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Review
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse ($completedComplaintsList as $complaint)

                            @php
                                $report = $complaint->maintenanceReport;

                                $reviewStatus = $report?->review_status ?? 'Not Submitted';

                                $reviewClasses = match ($reviewStatus) {
                                    'Approved' => 'bg-green-50 text-green-700 border-green-200',
                                    'Pending Review' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'Returned' => 'bg-red-50 text-red-700 border-red-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-200',
                                };

                                $urgency = strtoupper(
                                    trim(
                                        $complaint->aiAnalysis?->urgency_level ?? ''
                                    )
                                );

                                $urgencyClasses = match ($urgency) {
                                    'HIGH' => 'bg-red-50 text-red-700 border-red-200',
                                    'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'LOW' => 'bg-green-50 text-green-700 border-green-200',
                                    default => 'bg-slate-50 text-slate-500 border-slate-200',
                                };
                            @endphp

                            <tr class="hover:bg-slate-50/60 transition">

                                <td class="px-5 py-4">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <span class="font-bold text-sky-700">
                                            {{ $complaint->complaint_no }}
                                        </span>

                                        <span class="inline-flex px-2 py-0.5 rounded-full
                                                     border text-[10px] font-bold
                                                     {{ $urgencyClasses }}">

                                            {{ $urgency ?: 'NOT ASSESSED' }}

                                        </span>

                                    </div>

                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ $complaint->status === 'Closed' ? 'Closed' : 'Completed' }}
                                    </p>

                                </td>


                                <td class="px-5 py-4">

                                    <p class="font-medium text-slate-900">
                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}
                                    </p>

                                    @if ($complaint->consumer?->account_number)

                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ $complaint->consumer->account_number }}
                                        </p>

                                    @endif

                                </td>


                                <td class="px-5 py-4 text-slate-700">
                                    {{ $complaint->category?->name ?? 'Uncategorized' }}
                                </td>


                                <td class="px-5 py-4">

                                    <p class="font-medium text-slate-800">
                                        {{ $complaint->completed_at?->format('M d, Y') ?? '—' }}
                                    </p>

                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ $complaint->completed_at?->format('h:i A') ?? '' }}
                                    </p>

                                </td>


                                <td class="px-5 py-4">

                                    <span class="inline-flex px-2.5 py-1 rounded-full
                                                 border text-xs font-semibold
                                                 {{ $reviewClasses }}">

                                        {{ $reviewStatus }}

                                    </span>

                                </td>


                                <td class="px-5 py-4 text-center">

                                    <a href="{{ route('technician.complaints.show', $complaint) }}"
                                        title="View Complaint"
                                        class="inline-flex w-9 h-9 items-center justify-center
                                               rounded-lg bg-sky-50 text-sky-600
                                               border border-sky-100
                                               hover:bg-sky-600 hover:text-white transition">

                                        <i class="fas fa-eye"></i>

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="px-6 py-14 text-center">

                                    <div class="w-12 h-12 mx-auto rounded-xl
                                                bg-slate-100 text-slate-400
                                                flex items-center justify-center">

                                        <i class="fas fa-file-circle-check"></i>

                                    </div>

                                    <p class="font-semibold text-slate-900 mt-3">
                                        No accomplished work
                                    </p>

                                    <p class="text-sm text-slate-500 mt-1">
                                        No maintenance work was accomplished during this period.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Mobile --}}
            <div class="lg:hidden divide-y divide-slate-100">

                @forelse ($completedComplaintsList as $complaint)

                    @php
                        $report = $complaint->maintenanceReport;

                        $reviewStatus = $report?->review_status ?? 'Not Submitted';

                        $reviewClasses = match ($reviewStatus) {
                            'Approved' => 'bg-green-50 text-green-700 border-green-200',
                            'Pending Review' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'Returned' => 'bg-red-50 text-red-700 border-red-200',
                            default => 'bg-slate-50 text-slate-600 border-slate-200',
                        };
                    @endphp

                    <div class="p-4">

                        <div class="flex items-start justify-between gap-3">

                            <div>

                                <p class="font-bold text-sky-700">
                                    {{ $complaint->complaint_no }}
                                </p>

                                <p class="text-xs text-slate-400 mt-1">
                                    Accomplished
                                    {{ $complaint->completed_at?->format('M d, Y h:i A') ?? '—' }}
                                </p>

                            </div>

                            <span class="inline-flex px-2 py-1 rounded-full
                                         border text-[10px] font-semibold
                                         {{ $reviewClasses }}">

                                {{ $reviewStatus }}

                            </span>

                        </div>


                        <div class="grid grid-cols-2 gap-4 mt-4">

                            <div>

                                <p class="text-[10px] uppercase font-semibold tracking-wide text-slate-400">
                                    Consumer
                                </p>

                                <p class="text-sm font-semibold text-slate-800 mt-1">
                                    {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}
                                </p>

                            </div>


                            <div>

                                <p class="text-[10px] uppercase font-semibold tracking-wide text-slate-400">
                                    Complaint Type
                                </p>

                                <p class="text-sm text-slate-700 mt-1">
                                    {{ $complaint->category?->name ?? 'Uncategorized' }}
                                </p>

                            </div>

                        </div>


                        <div class="flex items-center justify-between mt-4">

                            <span class="inline-flex items-center gap-1.5 text-xs text-green-600 font-medium">

                                <i class="fas fa-circle-check"></i>

                                {{ $complaint->status === 'Closed' ? 'Closed' : 'Accomplished' }}

                            </span>

                            <a href="{{ route('technician.complaints.show', $complaint) }}"
                                class="inline-flex w-9 h-9 items-center justify-center
                                       rounded-lg bg-sky-50 text-sky-600
                                       border border-sky-100">

                                <i class="fas fa-eye"></i>

                            </a>

                        </div>

                    </div>

                @empty

                    <div class="p-12 text-center">

                        <div class="w-12 h-12 mx-auto rounded-xl
                                    bg-slate-100 text-slate-400
                                    flex items-center justify-center">

                            <i class="fas fa-file-circle-check"></i>

                        </div>

                        <p class="font-semibold text-slate-900 mt-3">
                            No accomplished work
                        </p>

                        <p class="text-sm text-slate-500 mt-1">
                            No maintenance work was accomplished during this period.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>

    </div>


    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const canvas = document.getElementById('workStatusChart');

            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            const assigned = {{ (int) $assignedCount }};
            const inProgress = {{ (int) $inProgressCount }};
            const accomplished = {{ (int) $accomplishedCount }};

            const hasData =
                assigned > 0 ||
                inProgress > 0 ||
                accomplished > 0;

            new Chart(canvas, {
                type: 'doughnut',

                data: {
                    labels: hasData
                        ? [
                            'Assigned',
                            'In Progress',
                            'Accomplished'
                        ]
                        : [
                            'No Work'
                        ],

                    datasets: [{
                        data: hasData
                            ? [
                                assigned,
                                inProgress,
                                accomplished
                            ]
                            : [
                                1
                            ],

                        backgroundColor: hasData
                            ? [
                                '#3b82f6',
                                '#f59e0b',
                                '#22c55e'
                            ]
                            : [
                                '#e2e8f0'
                            ],

                        borderWidth: 0,
                        hoverOffset: hasData ? 4 : 0
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            enabled: hasData,

                            callbacks: {
                                label: function(context) {
                                    return ' ' +
                                        context.label +
                                        ': ' +
                                        context.raw;
                                }
                            }
                        }
                    }
                }
            });

        });
    </script>

@endsection
