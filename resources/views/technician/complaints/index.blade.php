@extends('technician.layouts.app')

@section('title', 'My Maintenance Complaints')

@section('content')

    <div class="space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div>

            <p class="text-sm font-medium text-indigo-600">
                Maintenance Operations
            </p>

            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">
                My Maintenance Complaints
            </h1>

            <p class="text-sm text-gray-500 mt-2">
                View, search, and manage complaints assigned to you.
            </p>

        </div>


        {{-- ========================================================= --}}
        {{-- STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            <x-form.card>

                <div class="p-5 flex items-center justify-between">

                    <div>

                        <p class="text-sm text-gray-500">
                            Assigned
                        </p>

                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ $assignedCount }}
                        </p>

                    </div>

                    <div
                        class="w-11 h-11 rounded-xl
                            bg-blue-100 text-blue-600
                            flex items-center justify-center">

                        <i class="fas fa-clipboard-list"></i>

                    </div>

                </div>

            </x-form.card>


            <x-form.card>

                <div class="p-5 flex items-center justify-between">

                    <div>

                        <p class="text-sm text-gray-500">
                            In Progress
                        </p>

                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ $inProgressCount }}
                        </p>

                    </div>

                    <div
                        class="w-11 h-11 rounded-xl
                            bg-yellow-100 text-yellow-600
                            flex items-center justify-center">

                        <i class="fas fa-screwdriver-wrench"></i>

                    </div>

                </div>

            </x-form.card>


            <x-form.card>

                <div class="p-5 flex items-center justify-between">

                    <div>

                        <p class="text-sm text-gray-500">
                            Completed
                        </p>

                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ $completedCount }}
                        </p>

                    </div>

                    <div
                        class="w-11 h-11 rounded-xl
                            bg-green-100 text-green-600
                            flex items-center justify-center">

                        <i class="fas fa-circle-check"></i>

                    </div>

                </div>

            </x-form.card>


            <x-form.card>

                <div class="p-5 flex items-center justify-between">

                    <div>

                        <p class="text-sm text-gray-500">
                            Urgent
                        </p>

                        <p class="text-3xl font-bold text-red-600 mt-1">
                            {{ $urgentCount }}
                        </p>

                    </div>

                    <div
                        class="w-11 h-11 rounded-xl
                            bg-red-100 text-red-600
                            flex items-center justify-center">

                        <i class="fas fa-triangle-exclamation"></i>

                    </div>

                </div>

            </x-form.card>

        </div>


        {{-- ========================================================= --}}
        {{-- FILTERS --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <form method="GET" action="{{ route('technician.complaints.index') }}" class="p-5">

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">

                    {{-- Search --}}
                    <div class="relative xl:col-span-2">

                        <i
                            class="fas fa-search absolute left-4 top-1/2
                              -translate-y-1/2 text-gray-400"></i>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search complaint, subject, consumer..."
                            class="w-full pl-11 pr-4 py-2.5 rounded-xl
                               border-gray-300
                               focus:border-indigo-500
                               focus:ring-indigo-500">

                    </div>


                    {{-- Status --}}
                    <select name="status"
                        class="rounded-xl border-gray-300
                           focus:border-indigo-500
                           focus:ring-indigo-500">

                        <option value="">
                            All Statuses
                        </option>

                        <option value="Assigned" @selected(request('status') === 'Assigned')>
                            Assigned
                        </option>

                        <option value="In Progress" @selected(request('status') === 'In Progress')>
                            In Progress
                        </option>

                        <option value="Completed" @selected(request('status') === 'Completed')>
                            Completed
                        </option>

                    </select>


                    {{-- Priority --}}
                    <select name="priority"
                        class="rounded-xl border-gray-300
                           focus:border-indigo-500
                           focus:ring-indigo-500">

                        <option value="">
                            All Priorities
                        </option>

                        <option value="Critical" @selected(request('priority') === 'Critical')>
                            Critical
                        </option>

                        <option value="High" @selected(request('priority') === 'High')>
                            High
                        </option>

                        <option value="Medium" @selected(request('priority') === 'Medium')>
                            Medium
                        </option>

                        <option value="Low" @selected(request('priority') === 'Low')>
                            Low
                        </option>

                    </select>


                    {{-- Buttons --}}
                    <div class="flex gap-2">

                        <button type="submit"
                            class="flex-1 inline-flex items-center
                               justify-center gap-2
                               px-4 py-2.5 rounded-xl
                               bg-indigo-600 text-white
                               hover:bg-indigo-700 transition">

                            <i class="fas fa-search"></i>

                            Search

                        </button>


                        @if (request()->filled('search') || request()->filled('status') || request()->filled('priority'))
                            <a href="{{ route('technician.complaints.index') }}" title="Clear filters"
                                class="inline-flex items-center justify-center
                                  px-4 py-2.5 rounded-xl
                                  border border-gray-300
                                  text-gray-700
                                  hover:bg-gray-50">

                                <i class="fas fa-xmark"></i>

                            </a>
                        @endif

                    </div>

                </div>

            </form>

        </x-form.card>


        {{-- ========================================================= --}}
        {{-- COMPLAINTS TABLE --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

                <div class="flex flex-col sm:flex-row
                        sm:items-center sm:justify-between gap-3">

                    <div>

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-screwdriver-wrench
                                  text-indigo-600 mr-2"></i>

                            Maintenance Work

                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Complaints assigned to your technician account.
                        </p>

                    </div>

                    <span class="text-xs text-gray-500">

                        Showing {{ $complaints->firstItem() ?? 0 }}
                        –
                        {{ $complaints->lastItem() ?? 0 }}
                        of {{ $complaints->total() }}

                    </span>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- DESKTOP --}}
            {{-- ===================================================== --}}

            <div class="hidden md:block overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>

                            <th class="px-5 sm:px-6 py-4 text-left">
                                Complaint
                            </th>

                            <th class="px-5 sm:px-6 py-4 text-left">
                                Consumer
                            </th>

                            <th class="px-5 sm:px-6 py-4 text-left">
                                Location
                            </th>

                            <th class="px-5 sm:px-6 py-4 text-left">
                                Priority
                            </th>

                            <th class="px-5 sm:px-6 py-4 text-left">
                                Status
                            </th>

                            <th class="px-5 sm:px-6 py-4 text-left">
                                Updated
                            </th>

                            <th class="px-5 sm:px-6 py-4 text-right">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse($complaints as $complaint)
                            <tr class="hover:bg-gray-50 transition">

                                {{-- Complaint --}}
                                <td class="px-5 sm:px-6 py-4">

                                    <p class="font-semibold text-gray-900">
                                        {{ $complaint->complaint_no }}
                                    </p>

                                    <p class="text-gray-500 mt-1">
                                        {{ Str::limit($complaint->subject, 42) }}
                                    </p>

                                </td>


                                {{-- Consumer --}}
                                <td class="px-5 sm:px-6 py-4">

                                    <p class="font-medium text-gray-900">

                                        {{ $complaint->consumer?->full_name ?? 'Walk-in Consumer' }}

                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">

                                        {{ $complaint->consumer?->consumer_no ?? '—' }}

                                    </p>

                                </td>


                                {{-- Location --}}
                                <td class="px-5 sm:px-6 py-4">

                                    <div class="flex items-start gap-2">

                                        <i
                                            class="fas fa-location-dot
                                              text-gray-400 mt-1"></i>

                                        <div>

                                            <p class="text-gray-700">
                                                {{ Str::limit($complaint->address, 35) }}
                                            </p>

                                            @if ($complaint->latitude && $complaint->longitude)
                                                <p class="text-xs text-green-600 mt-1">

                                                    <i class="fas fa-map-location-dot mr-1"></i>

                                                    GPS available

                                                </p>
                                            @else
                                                <p class="text-xs text-gray-400 mt-1">
                                                    No GPS coordinates
                                                </p>
                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- Priority --}}
                                <td class="px-5 sm:px-6 py-4">

                                    @php

                                        $priorityClasses = match ($complaint->priority) {
                                            'Critical' => 'bg-red-100 text-red-700',

                                            'High' => 'bg-orange-100 text-orange-700',

                                            'Medium' => 'bg-yellow-100 text-yellow-700',

                                            default => 'bg-green-100 text-green-700',
                                        };

                                    @endphp

                                    <span
                                        class="inline-flex items-center gap-1.5
                                             px-2.5 py-1 rounded-full
                                             text-xs font-medium
                                             {{ $priorityClasses }}">

                                        @if ($complaint->priority === 'Critical')
                                            <i class="fas fa-triangle-exclamation"></i>
                                        @endif

                                        {{ $complaint->priority }}

                                    </span>

                                </td>


                                {{-- Status --}}
                                <td class="px-5 sm:px-6 py-4">

                                    @php

                                        $statusClasses = match ($complaint->status) {
                                            'Assigned' => 'bg-blue-100 text-blue-700',

                                            'In Progress' => 'bg-yellow-100 text-yellow-700',

                                            'Completed' => 'bg-green-100 text-green-700',

                                            default => 'bg-gray-100 text-gray-700',
                                        };

                                    @endphp

                                    <span
                                        class="inline-flex items-center gap-1.5
                                             px-2.5 py-1 rounded-full
                                             text-xs font-medium
                                             {{ $statusClasses }}">

                                        <i class="fas fa-circle text-[6px]"></i>

                                        {{ $complaint->status }}

                                    </span>

                                </td>


                                {{-- Updated --}}
                                <td class="px-5 sm:px-6 py-4 text-gray-500">

                                    {{ $complaint->updated_at?->format('M d, Y') }}

                                    <p class="text-xs text-gray-400 mt-1">

                                        {{ $complaint->updated_at?->format('h:i A') }}

                                    </p>

                                </td>


                                {{-- Action --}}
                                <td class="px-5 sm:px-6 py-4 text-right">

                                    <a href="{{ route('technician.complaints.show', $complaint) }}"
                                        class="inline-flex items-center gap-2
                                          px-3 py-2 rounded-lg
                                          bg-indigo-600 text-white
                                          hover:bg-indigo-700 transition">

                                        <i class="fas fa-eye"></i>

                                        View

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="px-6 py-14 text-center">

                                    <div
                                        class="w-14 h-14 mx-auto rounded-2xl
                                            bg-gray-100 text-gray-400
                                            flex items-center justify-center">

                                        <i class="fas fa-clipboard-check text-xl"></i>

                                    </div>

                                    <h3 class="font-semibold text-gray-900 mt-4">
                                        No Maintenance Complaints
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">
                                        No complaints match your current filters.
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

                @forelse($complaints as $complaint)
                    @php

                        $priorityClasses = match ($complaint->priority) {
                            'Critical' => 'bg-red-100 text-red-700',

                            'High' => 'bg-orange-100 text-orange-700',

                            'Medium' => 'bg-yellow-100 text-yellow-700',

                            default => 'bg-green-100 text-green-700',
                        };

                    @endphp

                    <div class="p-5 space-y-4">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">

                                <p class="font-semibold text-gray-900">
                                    {{ $complaint->complaint_no }}
                                </p>

                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $complaint->subject }}
                                </p>

                            </div>

                            <span
                                class="shrink-0 px-2.5 py-1 rounded-full
                                     text-xs font-medium
                                     {{ $priorityClasses }}">

                                {{ $complaint->priority }}

                            </span>

                        </div>


                        <div class="text-sm text-gray-600">

                            <i class="fas fa-user text-gray-400 mr-1"></i>

                            {{ $complaint->consumer?->full_name ?? 'Walk-in Consumer' }}

                        </div>


                        <div class="text-sm text-gray-600">

                            <i class="fas fa-location-dot text-gray-400 mr-1"></i>

                            {{ $complaint->address }}

                        </div>


                        <div class="flex items-center justify-between">

                            <span
                                class="text-xs font-medium
                                     {{ $complaint->status === 'Assigned'
                                         ? 'text-blue-700'
                                         : ($complaint->status === 'In Progress'
                                             ? 'text-yellow-700'
                                             : 'text-green-700') }}">

                                <i class="fas fa-circle text-[6px] mr-1"></i>

                                {{ $complaint->status }}

                            </span>


                            <a href="{{ route('technician.complaints.show', $complaint) }}"
                                class="inline-flex items-center gap-2
                                  px-3 py-2 rounded-lg
                                  bg-indigo-600 text-white text-sm">

                                <i class="fas fa-eye"></i>

                                View

                            </a>

                        </div>

                    </div>

                @empty

                    <div class="p-8 text-center">

                        <i class="fas fa-clipboard-check
                              text-gray-300 text-3xl"></i>

                        <p class="font-medium text-gray-900 mt-3">
                            No complaints found
                        </p>

                    </div>
                @endforelse

            </div>


            {{-- Pagination --}}
            @if ($complaints->hasPages())
                <div class="px-5 sm:px-6 py-4 border-t border-gray-100">

                    {{ $complaints->links() }}

                </div>
            @endif

        </x-form.card>

    </div>

@endsection
