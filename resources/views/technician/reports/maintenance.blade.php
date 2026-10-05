@extends('technician.layouts.app')

@section('title', 'Work Summary')

@section('content')

    <div class="max-w-7xl mx-auto space-y-5">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>

                <div class="flex items-center gap-2 text-sm font-medium text-sky-600">

                    <i class="fas fa-chart-line"></i>

                    Maintenance Summary

                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">
                    Work Summary
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Review your maintenance workload, activity, and accomplishments for the selected period.
                </p>

            </div>

            <a href="{{ route('technician.reports.maintenance.print', request()->query()) }}" target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-2
                       px-4 py-2.5 rounded-xl
                       bg-slate-100 text-slate-700
                       text-sm font-semibold
                       hover:bg-slate-200 transition">

                <i class="fas fa-print"></i>

                Print Summary

            </a>

        </div>


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


        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">

            <form method="GET" action="{{ route('technician.reports.maintenance') }}" class="p-4 sm:p-5">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto] gap-3">

                    <div>

                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                            From
                        </label>

                        <input type="date" name="from" value="{{ request('from', $from->format('Y-m-d')) }}"
                            class="w-full rounded-xl border-gray-300
                                   text-sm text-gray-700
                                   focus:border-sky-500
                                   focus:ring-sky-500">

                    </div>

                    <div>

                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                            To
                        </label>

                        <input type="date" name="to" value="{{ request('to', $to->format('Y-m-d')) }}"
                            class="w-full rounded-xl border-gray-300
                                   text-sm text-gray-700
                                   focus:border-sky-500
                                   focus:ring-sky-500">

                    </div>

                    <div class="flex items-end gap-2">

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
                                   bg-gray-100 text-gray-600
                                   hover:bg-gray-200 transition"
                            title="Reset">

                            <i class="fas fa-rotate-left"></i>

                        </a>

                    </div>

                </div>

            </form>

        </div>


        <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-500">

            <i class="fas fa-calendar-days text-sky-600"></i>

            <span>
                Report period:
            </span>

            <span class="font-semibold text-gray-700">
                {{ $from->format('M d, Y') }}
            </span>

            <span>—</span>

            <span class="font-semibold text-gray-700">
                {{ $to->format('M d, Y') }}
            </span>

        </div>


        <div class="grid grid-cols-3 gap-2 sm:gap-4">

            <div
                class="bg-white rounded-xl sm:rounded-2xl
                       border border-gray-200 shadow-sm
                       p-3 sm:p-5">

                <div class="flex items-center justify-between gap-2">

                    <div>

                        <p class="text-[10px] sm:text-sm text-gray-500">
                            Assigned
                        </p>

                        <p class="text-xl sm:text-3xl font-bold text-gray-900 mt-1">
                            {{ $assignedCount }}
                        </p>

                    </div>

                    <div
                        class="hidden sm:flex w-10 h-10 rounded-xl
                               bg-blue-50 text-blue-600
                               items-center justify-center">

                        <i class="fas fa-clipboard-list"></i>

                    </div>

                </div>

                <p class="hidden sm:block text-xs text-gray-400 mt-2">
                    Waiting to start
                </p>

            </div>


            <div
                class="bg-white rounded-xl sm:rounded-2xl
                       border border-gray-200 shadow-sm
                       p-3 sm:p-5">

                <div class="flex items-center justify-between gap-2">

                    <div>

                        <p class="text-[10px] sm:text-sm text-gray-500">
                            In Progress
                        </p>

                        <p class="text-xl sm:text-3xl font-bold text-amber-600 mt-1">
                            {{ $inProgressCount }}
                        </p>

                    </div>

                    <div
                        class="hidden sm:flex w-10 h-10 rounded-xl
                               bg-amber-50 text-amber-600
                               items-center justify-center">

                        <i class="fas fa-screwdriver-wrench"></i>

                    </div>

                </div>

                <p class="hidden sm:block text-xs text-gray-400 mt-2">
                    Active field work
                </p>

            </div>


            <div
                class="bg-white rounded-xl sm:rounded-2xl
                       border border-gray-200 shadow-sm
                       p-3 sm:p-5">

                <div class="flex items-center justify-between gap-2">

                    <div>

                        <p class="text-[10px] sm:text-sm text-gray-500">
                            High Urgency
                        </p>

                        <p class="text-xl sm:text-3xl font-bold text-red-600 mt-1">
                            {{ $urgentCount }}
                        </p>

                    </div>

                    <div
                        class="hidden sm:flex w-10 h-10 rounded-xl
                               bg-red-50 text-red-600
                               items-center justify-center">

                        <i class="fas fa-triangle-exclamation"></i>

                    </div>

                </div>

                <p class="hidden sm:block text-xs text-gray-400 mt-2">
                    Needs attention
                </p>

            </div>

        </div>


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <p class="text-sm text-gray-500">
                            Accomplished
                        </p>

                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ $completedCount }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            Accomplishment reports submitted
                        </p>

                    </div>

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-green-50 text-green-600
                               flex items-center justify-center">

                        <i class="fas fa-circle-check"></i>

                    </div>

                </div>

            </div>


            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <p class="text-sm text-gray-500">
                            Closed
                        </p>

                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ $closedCount }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            Finalized by management
                        </p>

                    </div>

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-gray-100 text-gray-600
                               flex items-center justify-center">

                        <i class="fas fa-lock"></i>

                    </div>

                </div>

            </div>

        </div>


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-gray-500">
                            Completion Rate
                        </p>

                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ $completionRate }}%
                        </p>

                    </div>

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-sky-50 text-sky-600
                               flex items-center justify-center">

                        <i class="fas fa-chart-line"></i>

                    </div>

                </div>

                <div class="mt-4 h-2 bg-gray-100 rounded-full overflow-hidden">

                    <div class="h-full bg-sky-600 rounded-full" style="width: {{ min($completionRate, 100) }}%">
                    </div>

                </div>

            </div>


            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-gray-500">
                            Average Completion Time
                        </p>

                        <p class="text-3xl font-bold text-gray-900 mt-1">

                            {{ $averageCompletionHours }}

                            <span class="text-base font-medium text-gray-500">
                                hrs
                            </span>

                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            From complaint submission to accomplishment
                        </p>

                    </div>

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-violet-50 text-violet-600
                               flex items-center justify-center">

                        <i class="fas fa-stopwatch"></i>

                    </div>

                </div>

            </div>

        </div>


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-100">

                    <h2 class="font-semibold text-gray-900">

                        <i class="fas fa-triangle-exclamation text-sky-600 mr-2"></i>

                        AI Urgency Breakdown

                    </h2>

                    <p class="text-xs text-gray-500 mt-1">
                        Urgency generated by complaint analysis.
                    </p>

                </div>

                <div class="p-5 space-y-5">

                    @foreach ($urgencyBreakdown as $urgency => $count)
                        @php

                            $urgencyUpper = strtoupper($urgency);

                            $barClass = match ($urgencyUpper) {
                                'HIGH' => 'bg-red-500',
                                'MODERATE' => 'bg-amber-500',
                                'LOW' => 'bg-green-500',
                                default => 'bg-gray-400',
                            };

                            $textClass = match ($urgencyUpper) {
                                'HIGH' => 'text-red-700',
                                'MODERATE' => 'text-amber-700',
                                'LOW' => 'text-green-700',
                                default => 'text-gray-500',
                            };

                            $percentage = $totalAssigned > 0 ? ($count / $totalAssigned) * 100 : 0;

                        @endphp

                        <div>

                            <div class="flex items-center justify-between mb-2">

                                <span class="text-sm font-medium {{ $textClass }}">
                                    {{ $urgency }}
                                </span>

                                <span class="text-sm font-bold text-gray-900">
                                    {{ $count }}
                                </span>

                            </div>

                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">

                                <div class="h-full rounded-full {{ $barClass }}"
                                    style="width: {{ min($percentage, 100) }}%">
                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>

            </div>


            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-100">

                    <h2 class="font-semibold text-gray-900">

                        <i class="fas fa-list-check text-sky-600 mr-2"></i>

                        Work Status

                    </h2>

                    <p class="text-xs text-gray-500 mt-1">
                        Current maintenance workflow status.
                    </p>

                </div>

                <div class="p-5 grid grid-cols-2 gap-3">

                    @foreach ($statusBreakdown as $status => $count)
                        @php
                            $displayStatus = $status === 'Completed' ? 'Accomplished' : $status;
                        @endphp

                        <div class="rounded-xl bg-gray-50
                                   border border-gray-100 p-4">

                            <p class="text-xs text-gray-500">
                                {{ $displayStatus }}
                            </p>

                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ $count }}
                            </p>

                        </div>
                    @endforeach

                </div>

            </div>

        </div>


        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="px-5 sm:px-6 py-4 border-b border-gray-100">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

                    <div>

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-screwdriver-wrench text-sky-600 mr-2"></i>

                            Current Work

                        </h2>

                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                            Assigned and in-progress work that still requires action.
                        </p>

                    </div>

                    <span class="text-xs text-gray-500">
                        {{ $currentComplaints->count() }}
                        active
                    </span>

                </div>

            </div>


            <div class="hidden lg:block overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Complaint
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Consumer
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Complaint Type
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Status
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse ($currentComplaints as $complaint)
                            @php

                                $urgency = strtoupper(trim($complaint->aiAnalysis?->urgency_level ?? ''));

                                $urgencyClasses = match ($urgency) {
                                    'HIGH' => 'bg-red-50 text-red-700 border-red-200',
                                    'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'LOW' => 'bg-green-50 text-green-700 border-green-200',
                                    default => 'bg-gray-50 text-gray-500 border-gray-200',
                                };

                                $statusClasses = match ($complaint->status) {
                                    'Assigned' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'In Progress' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    default => 'bg-gray-50 text-gray-600 border-gray-200',
                                };

                            @endphp

                            <tr class="hover:bg-gray-50/70 transition">

                                <td class="px-5 py-4">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="w-10 h-10 rounded-xl
                                                   bg-sky-50 text-sky-600
                                                   flex items-center justify-center shrink-0">

                                            <i class="fas fa-file-lines"></i>

                                        </div>

                                        <div class="min-w-0">

                                            <div class="flex flex-wrap items-center gap-2">

                                                <p class="font-bold text-sky-700">
                                                    {{ $complaint->complaint_no }}
                                                </p>

                                                @if ($urgency)
                                                    <span
                                                        class="inline-flex items-center gap-1
                                                               px-2 py-0.5 rounded-full border
                                                               text-[10px] font-bold
                                                               {{ $urgencyClasses }}">

                                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                                        {{ $urgency }}

                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center
                                                               px-2 py-0.5 rounded-full border
                                                               bg-gray-50 border-gray-200
                                                               text-[10px] font-semibold text-gray-400">
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


                                <td class="px-5 py-4">

                                    <p class="font-medium text-gray-900">

                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}

                                    </p>

                                    @if ($complaint->consumer?->account_number)
                                        <p class="text-xs text-gray-500 mt-1">

                                            Account:
                                            {{ $complaint->consumer->account_number }}

                                        </p>
                                    @elseif ($complaint->complainant_phone)
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $complaint->complainant_phone }}
                                        </p>
                                    @endif

                                </td>


                                <td class="px-5 py-4">

                                    <span
                                        class="inline-flex px-2.5 py-1.5
                                               rounded-lg bg-gray-100
                                               text-gray-700 text-xs font-medium">

                                        {{ $complaint->category?->name ?? 'Uncategorized' }}

                                    </span>

                                </td>


                                <td class="px-5 py-4">

                                    <span
                                        class="inline-flex items-center gap-1.5
                                               px-2.5 py-1.5 rounded-full border
                                               text-xs font-semibold
                                               {{ $statusClasses }}">

                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                        {{ $complaint->status }}

                                    </span>

                                </td>


                                <td class="px-5 py-4 text-center">

                                    <a href="{{ route('technician.complaints.show', $complaint) }}"
                                        title="View Complaint" aria-label="View Complaint"
                                        class="inline-flex w-9 h-9
                                               items-center justify-center
                                               rounded-lg bg-sky-50 text-sky-600
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

                                <td colspan="5" class="px-6 py-12 text-center">

                                    <div
                                        class="w-14 h-14 mx-auto rounded-2xl
                                               bg-gray-100 text-gray-400
                                               flex items-center justify-center">

                                        <i class="fas fa-clipboard-check text-xl"></i>

                                    </div>

                                    <p class="font-semibold text-gray-900 mt-3">
                                        No active maintenance work
                                    </p>

                                    <p class="text-sm text-gray-500 mt-1">
                                        You have no assigned or in-progress maintenance work.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            <div class="lg:hidden divide-y divide-gray-100">

                @forelse ($currentComplaints as $complaint)
                    @php

                        $urgency = strtoupper(trim($complaint->aiAnalysis?->urgency_level ?? ''));

                        $urgencyClasses = match ($urgency) {
                            'HIGH' => 'bg-red-50 text-red-700 border-red-200',
                            'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'LOW' => 'bg-green-50 text-green-700 border-green-200',
                            default => 'bg-gray-50 text-gray-500 border-gray-200',
                        };

                        $statusClasses = match ($complaint->status) {
                            'Assigned' => 'bg-blue-50 text-blue-700 border-blue-100',
                            'In Progress' => 'bg-amber-50 text-amber-700 border-amber-100',
                            default => 'bg-gray-50 text-gray-600 border-gray-200',
                        };

                    @endphp

                    <div class="p-4 sm:p-5">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">

                                    <p class="font-bold text-sky-700">
                                        {{ $complaint->complaint_no }}
                                    </p>

                                    @if ($urgency)
                                        <span
                                            class="inline-flex items-center gap-1
                                                   px-2 py-0.5 rounded-full border
                                                   text-[10px] font-bold
                                                   {{ $urgencyClasses }}">

                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                            {{ $urgency }}

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex px-2 py-0.5
                                                   rounded-full border
                                                   bg-gray-50 border-gray-200
                                                   text-[10px] font-semibold
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
                                class="inline-flex items-center gap-1.5
                                       px-2.5 py-1 rounded-full border
                                       text-[10px] font-semibold shrink-0
                                       {{ $statusClasses }}">

                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                {{ $complaint->status }}

                            </span>

                        </div>


                        <div class="mt-4 grid grid-cols-2 gap-4">

                            <div>

                                <p class="text-[10px] uppercase tracking-wide font-semibold text-gray-400">
                                    Consumer
                                </p>

                                <p class="mt-1 text-sm font-semibold text-gray-800">

                                    {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}

                                </p>

                            </div>

                            <div>

                                <p class="text-[10px] uppercase tracking-wide font-semibold text-gray-400">
                                    Complaint Type
                                </p>

                                <p class="mt-1 text-sm font-medium text-gray-700">
                                    {{ $complaint->category?->name ?? 'Uncategorized' }}
                                </p>

                            </div>

                        </div>


                        <div class="mt-4 flex items-center justify-between">

                            <span class="text-xs text-gray-400">

                                Updated
                                {{ $complaint->updated_at?->diffForHumans() }}

                            </span>

                            <a href="{{ route('technician.complaints.show', $complaint) }}" title="View Complaint"
                                aria-label="View Complaint"
                                class="inline-flex w-9 h-9
                                       items-center justify-center
                                       rounded-lg bg-sky-50 text-sky-600
                                       border border-sky-100
                                       hover:bg-sky-600
                                       hover:text-white transition">

                                <i class="fas fa-eye"></i>

                            </a>

                        </div>

                    </div>

                @empty

                    <div class="p-10 text-center">

                        <div
                            class="w-14 h-14 mx-auto rounded-2xl
                                   bg-gray-100 text-gray-400
                                   flex items-center justify-center">

                            <i class="fas fa-clipboard-check text-xl"></i>

                        </div>

                        <p class="font-semibold text-gray-900 mt-3">
                            No active maintenance work
                        </p>

                    </div>
                @endforelse

            </div>

        </div>


        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="px-5 sm:px-6 py-4 border-b border-gray-100">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-clock-rotate-left text-green-600 mr-2"></i>

                            Accomplished Work

                        </h2>

                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                            Maintenance work with submitted accomplishment reports.
                        </p>

                    </div>

                    <span
                        class="inline-flex items-center justify-center
                               min-w-8 h-8 px-2 rounded-full
                               bg-green-50 text-green-700
                               text-sm font-bold">
                        {{ $completedComplaintsList->count() }}
                    </span>

                </div>

            </div>


            <div class="divide-y divide-gray-100">

                @forelse ($completedComplaintsList as $complaint)
                    @php

                        $urgency = strtoupper(trim($complaint->aiAnalysis?->urgency_level ?? ''));

                        $urgencyClasses = match ($urgency) {
                            'HIGH' => 'bg-red-50 text-red-700 border-red-200',
                            'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'LOW' => 'bg-green-50 text-green-700 border-green-200',
                            default => 'bg-gray-50 text-gray-500 border-gray-200',
                        };

                        $reportSubmitted =
                            $complaint->maintenanceReport &&
                            in_array(
                                $complaint->maintenanceReport->review_status,
                                ['Pending Review', 'Returned', 'Approved'],
                                true,
                            );

                    @endphp

                    <div
                        class="p-4 sm:p-5
                               flex flex-col lg:flex-row
                               lg:items-center lg:justify-between
                               gap-4">

                        <div class="min-w-0">

                            <div class="flex flex-wrap items-center gap-2">

                                <p class="font-bold text-sky-700">
                                    {{ $complaint->complaint_no }}
                                </p>

                                @if ($urgency)
                                    <span
                                        class="inline-flex items-center gap-1
                                               px-2 py-0.5 rounded-full border
                                               text-[10px] font-bold
                                               {{ $urgencyClasses }}">

                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                        {{ $urgency }}

                                    </span>
                                @else
                                    <span
                                        class="inline-flex px-2 py-0.5
                                               rounded-full border
                                               bg-gray-50 border-gray-200
                                               text-[10px] font-semibold
                                               text-gray-400">
                                        NOT ASSESSED
                                    </span>
                                @endif

                            </div>

                            <p class="text-sm font-medium text-gray-800 mt-2">
                                {{ $complaint->category?->name ?? 'Uncategorized' }}
                            </p>

                            <p class="text-sm text-gray-500 mt-1">

                                {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown Consumer') }}

                            </p>

                            <p class="text-xs text-gray-400 mt-1">

                                Accomplished:
                                {{ $complaint->completed_at?->format('M d, Y h:i A') ?? '—' }}

                            </p>

                        </div>


                        <div class="flex flex-wrap items-center gap-2">

                            @if ($reportSubmitted)
                                @php
                                    $reviewStatus = $complaint->maintenanceReport->review_status;

                                    $reviewClasses = match ($reviewStatus) {
                                        'Approved' => 'bg-green-50 text-green-700 border-green-200',
                                        'Returned' => 'bg-red-50 text-red-700 border-red-200',
                                        default => 'bg-violet-50 text-violet-700 border-violet-200',
                                    };
                                @endphp

                                <span
                                    class="inline-flex items-center
                                           px-2.5 py-1.5 rounded-full
                                           border text-xs font-semibold
                                           {{ $reviewClasses }}">

                                    {{ $reviewStatus }}

                                </span>

                                <a href="{{ route('technician.maintenance-reports.show', $complaint) }}"
                                    title="View Accomplishment Report" aria-label="View Accomplishment Report"
                                    class="inline-flex w-9 h-9
                                           items-center justify-center
                                           rounded-lg bg-sky-50 text-sky-600
                                           border border-sky-100
                                           hover:bg-sky-600
                                           hover:text-white
                                           transition">

                                    <i class="fas fa-eye"></i>

                                </a>
                            @endif

                        </div>

                    </div>

                @empty

                    <div class="p-10 text-center">

                        <div
                            class="w-14 h-14 mx-auto rounded-2xl
                                   bg-gray-100 text-gray-400
                                   flex items-center justify-center">

                            <i class="fas fa-inbox text-xl"></i>

                        </div>

                        <p class="font-semibold text-gray-900 mt-3">
                            No accomplished maintenance
                        </p>

                        <p class="text-sm text-gray-500 mt-1">
                            No maintenance work was accomplished during this reporting period.
                        </p>

                    </div>
                @endforelse

            </div>

        </div>

    </div>

@endsection
