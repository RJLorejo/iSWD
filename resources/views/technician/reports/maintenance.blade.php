@extends('technician.layouts.app')

@section('title', 'Maintenance Report')

@section('content')

    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <p class="text-sm text-gray-500">
                    Maintenance Operations
                </p>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">
                    Maintenance Report
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Review your maintenance performance and completed repair work.
                </p>
            </div>

            <a href="{{ route('technician.reports.maintenance.print', request()->query()) }}" target="_blank"
                class="inline-flex items-center justify-center gap-2
                  px-4 py-2.5 rounded-xl
                  bg-gray-900 text-white
                  hover:bg-gray-800 transition">

                <i class="fas fa-print"></i>

                Print Report

            </a>

        </div>


        {{-- FILTER --}}
        <x-form.card>

            <form method="GET" class="p-5">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            From
                        </label>

                        <input type="date" name="from" value="{{ request('from', $from->format('Y-m-d')) }}"
                            class="w-full rounded-xl border-gray-300
                               focus:border-indigo-500
                               focus:ring-indigo-500">

                    </div>


                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            To
                        </label>

                        <input type="date" name="to" value="{{ request('to', $to->format('Y-m-d')) }}"
                            class="w-full rounded-xl border-gray-300
                               focus:border-indigo-500
                               focus:ring-indigo-500">

                    </div>


                    <div class="flex items-end gap-2">

                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2
                               px-5 py-2.5 rounded-xl
                               bg-indigo-600 text-white
                               hover:bg-indigo-700 transition">

                            <i class="fas fa-filter"></i>

                            Apply Filter

                        </button>

                        <a href="{{ route('technician.reports.maintenance') }}"
                            class="inline-flex items-center justify-center gap-2
                               px-5 py-2.5 rounded-xl
                               border border-gray-300
                               text-gray-700
                               hover:bg-gray-50">

                            <i class="fas fa-rotate-left"></i>

                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </x-form.card>


        {{-- PERIOD --}}
        <div class="flex items-center gap-2 text-sm text-gray-500">

            <i class="fas fa-calendar-days"></i>

            Report period:

            <span class="font-medium text-gray-700">
                {{ $from->format('M d, Y') }}
            </span>

            <span>—</span>

            <span class="font-medium text-gray-700">
                {{ $to->format('M d, Y') }}
            </span>

        </div>


        {{-- STATISTICS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Total --}}
            <x-form.card>

                <div class="p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Total Work
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $totalAssigned }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                Assigned complaints
                            </p>

                        </div>

                        <div
                            class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600
                                flex items-center justify-center">

                            <i class="fas fa-clipboard-list"></i>

                        </div>

                    </div>

                </div>

            </x-form.card>


            {{-- In Progress --}}
            <x-form.card>

                <div class="p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                In Progress
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $inProgressCount }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                Currently active
                            </p>

                        </div>

                        <div
                            class="w-12 h-12 rounded-xl bg-yellow-100 text-yellow-600
                                flex items-center justify-center">

                            <i class="fas fa-screwdriver-wrench"></i>

                        </div>

                    </div>

                </div>

            </x-form.card>


            {{-- Completed --}}
            <x-form.card>

                <div class="p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Completed
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $completedCount }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                Completed repairs
                            </p>

                        </div>

                        <div
                            class="w-12 h-12 rounded-xl bg-green-100 text-green-600
                                flex items-center justify-center">

                            <i class="fas fa-circle-check"></i>

                        </div>

                    </div>

                </div>

            </x-form.card>


            {{-- Urgent --}}
            <x-form.card>

                <div class="p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Urgent Work
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $urgentCount }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                High / Critical
                            </p>

                        </div>

                        <div
                            class="w-12 h-12 rounded-xl bg-red-100 text-red-600
                                flex items-center justify-center">

                            <i class="fas fa-triangle-exclamation"></i>

                        </div>

                    </div>

                </div>

            </x-form.card>

        </div>


        {{-- PERFORMANCE --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <x-form.card>

                <div class="p-5">

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
                            class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600
                                flex items-center justify-center">

                            <i class="fas fa-chart-line"></i>

                        </div>

                    </div>

                    <div class="mt-4 h-2 bg-gray-100 rounded-full overflow-hidden">

                        <div class="h-full bg-indigo-600 rounded-full" style="width: {{ min($completionRate, 100) }}%">
                        </div>

                    </div>

                </div>

            </x-form.card>


            <x-form.card>

                <div class="p-5">

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
                                From complaint creation to completion
                            </p>

                        </div>

                        <div
                            class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600
                                flex items-center justify-center">

                            <i class="fas fa-stopwatch"></i>

                        </div>

                    </div>

                </div>

            </x-form.card>

        </div>


        {{-- BREAKDOWN --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- PRIORITY --}}
            <x-form.card>

                <div class="px-5 py-5 border-b border-gray-100">

                    <h3 class="font-semibold text-gray-900">

                        <i class="fas fa-layer-group text-indigo-600 mr-2"></i>

                        Priority Breakdown

                    </h3>

                </div>

                <div class="p-5 space-y-4">

                    @foreach ($priorityBreakdown as $priority => $count)
                        <div>

                            <div class="flex items-center justify-between mb-1">

                                <span class="text-sm text-gray-600">
                                    {{ $priority }}
                                </span>

                                <span class="text-sm font-semibold text-gray-900">
                                    {{ $count }}
                                </span>

                            </div>

                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">

                                <div class="h-full rounded-full
                                @if ($priority === 'Critical') bg-red-500
                                @elseif($priority === 'High') bg-orange-500
                                @elseif($priority === 'Medium') bg-yellow-500
                                @else bg-green-500 @endif"
                                    style="width:
                                    {{ $totalAssigned > 0 ? ($count / $totalAssigned) * 100 : 0 }}%">
                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>

            </x-form.card>


            {{-- STATUS --}}
            <x-form.card>

                <div class="px-5 py-5 border-b border-gray-100">

                    <h3 class="font-semibold text-gray-900">

                        <i class="fas fa-list-check text-indigo-600 mr-2"></i>

                        Status Breakdown

                    </h3>

                </div>

                <div class="p-5 grid grid-cols-2 gap-3">

                    @foreach ($statusBreakdown as $status => $count)
                        <div class="rounded-xl bg-gray-50 p-4">

                            <p class="text-xs text-gray-500">
                                {{ $status }}
                            </p>

                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ $count }}
                            </p>

                        </div>
                    @endforeach

                </div>

            </x-form.card>

        </div>


        {{-- CURRENT WORK --}}
        <x-form.card>

            <div class="px-5 py-5 border-b border-gray-100">

                <h3 class="font-semibold text-gray-900">

                    <i class="fas fa-screwdriver-wrench text-indigo-600 mr-2"></i>

                    Current Maintenance Work

                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Work that still requires action.
                </p>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-5 py-4 text-left">
                                Complaint
                            </th>

                            <th class="px-5 py-4 text-left">
                                Subject
                            </th>

                            <th class="px-5 py-4 text-left">
                                Priority
                            </th>

                            <th class="px-5 py-4 text-left">
                                Status
                            </th>

                            <th class="px-5 py-4 text-right">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse($currentComplaints as $complaint)
                            <tr class="hover:bg-gray-50">

                                <td class="px-5 py-4 font-semibold">
                                    {{ $complaint->complaint_no }}
                                </td>

                                <td class="px-5 py-4 text-gray-600">
                                    {{ Str::limit($complaint->subject, 45) }}
                                </td>

                                <td class="px-5 py-4">

                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium
                                    @if ($complaint->priority === 'Critical') bg-red-100 text-red-700
                                    @elseif($complaint->priority === 'High')
                                        bg-orange-100 text-orange-700
                                    @elseif($complaint->priority === 'Medium')
                                        bg-yellow-100 text-yellow-700
                                    @else
                                        bg-green-100 text-green-700 @endif">

                                        {{ $complaint->priority }}

                                    </span>

                                </td>

                                <td class="px-5 py-4">

                                    <span
                                        class="text-sm font-medium
                                    @if ($complaint->status === 'Assigned') text-blue-700
                                    @else
                                        text-yellow-700 @endif">

                                        {{ $complaint->status }}

                                    </span>

                                </td>

                                <td class="px-5 py-4 text-right">

                                    <a href="{{ route('technician.complaints.show', $complaint) }}"
                                        class="inline-flex items-center gap-2
                                           px-3 py-2 rounded-lg
                                           bg-indigo-600 text-white
                                           hover:bg-indigo-700">

                                        <i class="fas fa-eye"></i>

                                        View

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="px-5 py-10 text-center">

                                    <i class="fas fa-circle-check text-green-500 text-3xl"></i>

                                    <p class="font-medium text-gray-900 mt-3">
                                        No active maintenance work
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </x-form.card>


        {{-- COMPLETED WORK --}}
        <x-form.card>

            <div class="px-5 py-5 border-b border-gray-100">

                <h3 class="font-semibold text-gray-900">

                    <i class="fas fa-clock-rotate-left text-green-600 mr-2"></i>

                    Completed Maintenance

                </h3>

            </div>


            <div class="divide-y divide-gray-100">

                @forelse($completedComplaintsList as $complaint)
                    <div
                        class="p-5 flex flex-col sm:flex-row
                            sm:items-center sm:justify-between gap-4">

                        <div class="min-w-0">

                            <p class="font-semibold text-gray-900">
                                {{ $complaint->complaint_no }}
                            </p>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ $complaint->subject }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">

                                {{ $complaint->category?->name ?? 'Uncategorized' }}


                            </p>

                            @if ($complaint->consumer)
                                <div class="font-medium text-gray-900">
                                    {{ $complaint->consumer->full_name }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $complaint->consumer->consumer_no }}
                                </div>
                            @elseif ($complaint->complainant_name)
                                <div class="font-medium text-gray-900">
                                    {{ $complaint->complainant_name }}
                                </div>

                                <div class="text-xs text-amber-600">
                                    <i class="fas fa-person-walking mr-1"></i>
                                    Walk-in / Unregistered
                                </div>

                                @if ($complaint->complainant_phone)
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-phone mr-1"></i>
                                        {{ $complaint->complainant_phone }}
                                    </div>
                                @endif
                            @else
                                <span class="text-gray-400">
                                    No complainant information
                                </span>
                            @endif

                        </div>

                        <div class="text-left sm:text-right">

                            <p class="text-sm font-medium text-green-700">

                                <i class="fas fa-circle-check mr-1"></i>

                                Completed

                            </p>

                            <p class="text-xs text-gray-400 mt-1">

                                {{ $complaint->completed_at?->format('M d, Y h:i A') }}

                            </p>

                        </div>

                    </div>

                @empty

                    <div class="p-10 text-center">

                        <i class="fas fa-inbox text-gray-300 text-3xl"></i>

                        <p class="text-sm text-gray-500 mt-3">
                            No completed maintenance work in this period.
                        </p>

                    </div>
                @endforelse

            </div>

        </x-form.card>

    </div>

@endsection
