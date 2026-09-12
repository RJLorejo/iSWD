@extends('technician.layouts.app')

@section('title', 'Maintenance History')

@section('content')

    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Maintenance History
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    View completed maintenance activities and technician reports.
                </p>
            </div>

        </div>


        {{-- STATISTICS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- TOTAL --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-gray-500">
                            Completed Maintenance
                        </p>

                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ $totalCompleted }}
                        </p>
                    </div>

                    <div
                        class="w-12 h-12 rounded-xl bg-green-100
                            flex items-center justify-center">

                        <i class="fas fa-check-circle text-green-600 text-xl"></i>

                    </div>

                </div>

            </div>


            {{-- REPORTS --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-gray-500">
                            With Maintenance Report
                        </p>

                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ $withReports }}
                        </p>
                    </div>

                    <div
                        class="w-12 h-12 rounded-xl bg-blue-100
                            flex items-center justify-center">

                        <i class="fas fa-file-circle-check text-blue-600 text-xl"></i>

                    </div>

                </div>

            </div>


            {{-- WITHOUT REPORT --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-gray-500">
                            Without Report
                        </p>

                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ $withoutReports }}
                        </p>
                    </div>

                    <div
                        class="w-12 h-12 rounded-xl bg-amber-100
                            flex items-center justify-center">

                        <i class="fas fa-file-circle-exclamation text-amber-600 text-xl"></i>

                    </div>

                </div>

            </div>

        </div>


        {{-- FILTER --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">

            <form method="GET" action="{{ route('technician.maintenance-history.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">

                {{-- SEARCH --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Search
                    </label>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Complaint number, subject, address..."
                        class="w-full rounded-xl border-gray-300
                           focus:border-blue-500 focus:ring-blue-500">

                </div>


                {{-- FROM --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        From
                    </label>

                    <input type="date" name="from" value="{{ request('from') }}"
                        class="w-full rounded-xl border-gray-300
                           focus:border-blue-500 focus:ring-blue-500">

                </div>


                {{-- TO --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        To
                    </label>

                    <input type="date" name="to" value="{{ request('to') }}"
                        class="w-full rounded-xl border-gray-300
                           focus:border-blue-500 focus:ring-blue-500">

                </div>


                {{-- BUTTONS --}}
                <div class="md:col-span-4 flex gap-2">

                    <button type="submit"
                        class="inline-flex items-center gap-2
                           px-5 py-2.5 rounded-xl
                           bg-blue-600 text-white
                           hover:bg-blue-700">
                        <i class="fas fa-search"></i>
                        Search
                    </button>

                    <a href="{{ route('technician.maintenance-history.index') }}"
                        class="inline-flex items-center gap-2
                           px-5 py-2.5 rounded-xl
                           bg-gray-100 text-gray-700
                           hover:bg-gray-200">
                        <i class="fas fa-rotate-left"></i>
                        Reset
                    </a>

                </div>

            </form>

        </div>


        {{-- HISTORY TABLE --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200">

                <h2 class="font-semibold text-gray-900">
                    Completed Maintenance
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Your completed maintenance assignments.
                </p>

            </div>


            @if ($histories->count())

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 border-b border-gray-200">

                            <tr>

                                <th class="px-6 py-4 text-left font-semibold text-gray-600">
                                    Complaint
                                </th>

                                <th class="px-6 py-4 text-left font-semibold text-gray-600">
                                    Subject
                                </th>

                                <th class="px-6 py-4 text-left font-semibold text-gray-600">
                                    Category
                                </th>

                                <th class="px-6 py-4 text-left font-semibold text-gray-600">
                                    Priority
                                </th>

                                <th class="px-6 py-4 text-left font-semibold text-gray-600">
                                    Completed
                                </th>

                                <th class="px-6 py-4 text-left font-semibold text-gray-600">
                                    Report
                                </th>

                                <th class="px-6 py-4 text-center font-semibold text-gray-600">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @foreach ($histories as $history)
                                <tr class="hover:bg-gray-50">

                                    {{-- COMPLAINT --}}
                                    <td class="px-6 py-4">

                                        <div class="font-semibold text-gray-900">
                                            {{ $history->complaint_no }}
                                        </div>

                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $history->complainant_name }}
                                        </div>

                                    </td>


                                    {{-- SUBJECT --}}
                                    <td class="px-6 py-4 ">

                                        <div class="font-medium text-gray-800">
                                            {{ $history->subject }}
                                        </div>

                                        <div class="text-xs text-gray-500 ">
                                            {{ $history->address }}
                                        </div>

                                    </td>


                                    {{-- CATEGORY --}}
                                    <td class="px-6 py-4">

                                        {{ $history->category?->name ?? '—' }}

                                    </td>


                                    {{-- PRIORITY --}}
                                    <td class="px-6 py-4">

                                        @php

                                            $priorityClass = match ($history->priority) {
                                                'Critical' => 'bg-red-100 text-red-700',

                                                'High' => 'bg-orange-100 text-orange-700',

                                                'Medium' => 'bg-yellow-100 text-yellow-700',

                                                default => 'bg-gray-100 text-gray-700',
                                            };

                                        @endphp

                                        <span
                                            class="inline-flex px-2.5 py-1
                                                 rounded-full text-xs font-semibold
                                                 {{ $priorityClass }}">

                                            {{ $history->priority }}

                                        </span>

                                    </td>


                                    {{-- COMPLETED --}}
                                    <td class="px-6 py-4">

                                        @if ($history->completed_at)
                                            <div class="font-medium text-gray-800">

                                                {{ $history->completed_at->format('M d, Y') }}

                                            </div>

                                            <div class="text-xs text-gray-500">

                                                {{ $history->completed_at->format('h:i A') }}

                                            </div>
                                        @else
                                            —
                                        @endif

                                    </td>


                                    {{-- REPORT --}}
                                    <td class="px-6 py-4">

                                        @if ($history->maintenanceReport)
                                            <span
                                                class="inline-flex items-center gap-1.5
                                                     px-2.5 py-1 rounded-full
                                                     bg-green-100 text-green-700
                                                     text-xs font-semibold">

                                                <i class="fas fa-check"></i>

                                                Submitted

                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5
                                                     px-2.5 py-1 rounded-full
                                                     bg-gray-100 text-gray-600
                                                     text-xs font-semibold">

                                                <i class="fas fa-minus"></i>

                                                No Report

                                            </span>
                                        @endif

                                    </td>


                                    {{-- ACTION --}}
                                    <td class="px-6 py-4 text-right">

                                        <div class="flex items-center justify-end gap-2">

                                            {{-- COMPLAINT --}}
                                            <a href="{{ route('technician.complaints.show', $history) }}"
                                                class="inline-flex items-center gap-2
                                                   px-3 py-2 rounded-lg
                                                   bg-gray-100 text-gray-700
                                                   hover:bg-gray-200">

                                                <i class="fas fa-eye"></i>

                                                Complaint

                                            </a>


                                            {{-- MAINTENANCE REPORT --}}
                                            @if ($history->maintenanceReport)
                                                <a href="{{ route('technician.maintenance-reports.show', $history) }}"
                                                    class="inline-flex items-center gap-2
                                                       px-3 py-2 rounded-lg
                                                       bg-blue-600 text-white
                                                       hover:bg-blue-700">

                                                    <i class="fas fa-file-lines"></i>

                                                    Report

                                                </a>
                                            @endif

                                        </div>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- PAGINATION --}}
                <div class="px-6 py-4 border-t border-gray-200">

                    {{ $histories->links() }}

                </div>
            @else
                {{-- EMPTY --}}
                <div class="px-6 py-16 text-center">

                    <div
                        class="mx-auto w-16 h-16 rounded-2xl
                            bg-gray-100 flex items-center justify-center">

                        <i class="fas fa-clock-rotate-left
                              text-gray-400 text-2xl"></i>

                    </div>

                    <h3 class="mt-4 text-lg font-semibold text-gray-900">
                        No maintenance history found
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Completed maintenance assigned to you will appear here.
                    </p>

                </div>

            @endif

        </div>

    </div>

@endsection
