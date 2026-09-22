@extends('technician.layouts.app')

@section('title', 'Assigned Complaints')

@section('content')

    <div class="space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>

                <div class="flex items-center gap-2 text-sm font-medium text-indigo-600">
                    <i class="fas fa-screwdriver-wrench"></i>
                    Maintenance Operations
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">
                    Assigned Complaints
                </h1>

                <p class="text-sm text-gray-500 mt-2">
                    Review complaints assigned to your maintenance team and manage field work.
                </p>

            </div>

            {{-- Active Work Indicator --}}
            @if ($activeCount > 0)

                <div class="inline-flex items-center gap-2
                            px-4 py-2.5 rounded-xl
                            bg-indigo-50 border border-indigo-100
                            text-indigo-700 text-sm font-medium">

                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full
                                     rounded-full bg-indigo-400 opacity-75"></span>

                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-indigo-600"></span>
                    </span>

                    {{ $activeCount }} active case{{ $activeCount !== 1 ? 's' : '' }}

                </div>

            @endif

        </div>


        {{-- ========================================================= --}}
        {{-- FLASH MESSAGES --}}
        {{-- ========================================================= --}}

        @if (session('success'))

            <div class="rounded-xl border border-green-200 bg-green-50
                        px-4 py-3 text-sm text-green-800 flex items-start gap-3">

                <i class="fas fa-circle-check mt-0.5"></i>

                <span>{{ session('success') }}</span>

            </div>

        @endif


        @if (session('error'))

            <div class="rounded-xl border border-red-200 bg-red-50
                        px-4 py-3 text-sm text-red-800 flex items-start gap-3">

                <i class="fas fa-circle-exclamation mt-0.5"></i>

                <span>{{ session('error') }}</span>

            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-4">

            {{-- Assigned --}}
            <x-form.card>

                <div class="p-5">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm text-gray-500">
                                Assigned
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $assignedCount }}
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-xl
                                    bg-blue-100 text-blue-600
                                    flex items-center justify-center">

                            <i class="fas fa-clipboard-list"></i>

                        </div>

                    </div>

                    <p class="text-xs text-gray-400 mt-3">
                        Waiting to start
                    </p>

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
                        </div>

                        <div class="w-11 h-11 rounded-xl
                                    bg-yellow-100 text-yellow-600
                                    flex items-center justify-center">

                            <i class="fas fa-screwdriver-wrench"></i>

                        </div>

                    </div>

                    <p class="text-xs text-gray-400 mt-3">
                        Active field work
                    </p>

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
                        </div>

                        <div class="w-11 h-11 rounded-xl
                                    bg-green-100 text-green-600
                                    flex items-center justify-center">

                            <i class="fas fa-circle-check"></i>

                        </div>

                    </div>

                    <p class="text-xs text-gray-400 mt-3">
                        Field work completed
                    </p>

                </div>

            </x-form.card>


            {{-- Urgent --}}
            <x-form.card>

                <div class="p-5">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm text-gray-500">
                                Urgent
                            </p>

                            <p class="text-3xl font-bold text-red-600 mt-1">
                                {{ $urgentCount }}
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-xl
                                    bg-red-100 text-red-600
                                    flex items-center justify-center">

                            <i class="fas fa-triangle-exclamation"></i>

                        </div>

                    </div>

                    <p class="text-xs text-gray-400 mt-3">
                        High or critical
                    </p>

                </div>

            </x-form.card>


            {{-- Active --}}
            <x-form.card>

                <div class="p-5">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm text-gray-500">
                                Active Cases
                            </p>

                            <p class="text-3xl font-bold text-indigo-600 mt-1">
                                {{ $activeCount }}
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-xl
                                    bg-indigo-100 text-indigo-600
                                    flex items-center justify-center">

                            <i class="fas fa-person-digging"></i>

                        </div>

                    </div>

                    <p class="text-xs text-gray-400 mt-3">
                        Assigned + in progress
                    </p>

                </div>

            </x-form.card>


            {{-- Total --}}
            <x-form.card>

                <div class="p-5">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm text-gray-500">
                                Total Cases
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $totalCount }}
                            </p>
                        </div>

                        <div class="w-11 h-11 rounded-xl
                                    bg-gray-100 text-gray-600
                                    flex items-center justify-center">

                            <i class="fas fa-layer-group"></i>

                        </div>

                    </div>

                    <p class="text-xs text-gray-400 mt-3">
                        Assigned to you
                    </p>

                </div>

            </x-form.card>

        </div>


        {{-- ========================================================= --}}
        {{-- WORKFLOW INFORMATION --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div class="p-5 sm:p-6">

                <div class="flex items-start gap-4">

                    <div class="w-11 h-11 shrink-0 rounded-xl
                                bg-indigo-100 text-indigo-600
                                flex items-center justify-center">

                        <i class="fas fa-route"></i>

                    </div>

                    <div class="min-w-0">

                        <h2 class="font-semibold text-gray-900">
                            Maintenance workflow
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Complaints are verified and assigned by the Maintenance Manager.
                            Assigned technicians perform the field work, submit the maintenance
                            report, and the manager reviews the completed case.
                        </p>

                        <div class="flex flex-wrap items-center gap-2 mt-4 text-xs">

                            <span class="px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 font-medium">
                                1. Assigned
                            </span>

                            <i class="fas fa-arrow-right text-gray-300"></i>

                            <span class="px-3 py-1.5 rounded-full bg-yellow-50 text-yellow-700 font-medium">
                                2. In Progress
                            </span>

                            <i class="fas fa-arrow-right text-gray-300"></i>

                            <span class="px-3 py-1.5 rounded-full bg-green-50 text-green-700 font-medium">
                                3. Completed
                            </span>

                            <i class="fas fa-arrow-right text-gray-300"></i>

                            <span class="px-3 py-1.5 rounded-full bg-purple-50 text-purple-700 font-medium">
                                4. Manager Review
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </x-form.card>


        {{-- ========================================================= --}}
        {{-- FILTERS --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <form method="GET"
                  action="{{ route('technician.complaints.index') }}"
                  class="p-5">

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">

                    {{-- Search --}}
                    <div class="relative xl:col-span-2">

                        <i class="fas fa-search absolute left-4 top-1/2
                                  -translate-y-1/2 text-gray-400"></i>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search complaint, consumer, location..."
                            class="w-full pl-11 pr-4 py-2.5 rounded-xl
                                   border-gray-300
                                   focus:border-indigo-500
                                   focus:ring-indigo-500">

                    </div>


                    {{-- Status --}}
                    <select
                        name="status"
                        class="rounded-xl border-gray-300
                               focus:border-indigo-500
                               focus:ring-indigo-500">

                        <option value="">
                            All Statuses
                        </option>

                        <option value="Assigned"
                            @selected(request('status') === 'Assigned')>
                            Assigned
                        </option>

                        <option value="In Progress"
                            @selected(request('status') === 'In Progress')>
                            In Progress
                        </option>

                        <option value="Completed"
                            @selected(request('status') === 'Completed')>
                            Completed
                        </option>

                    </select>


                    {{-- Buttons --}}
                    <div class="flex gap-2">

                        <button
                            type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2
                                   px-4 py-2.5 rounded-xl
                                   bg-indigo-600 text-white
                                   hover:bg-indigo-700 transition">

                            <i class="fas fa-search"></i>

                            Search

                        </button>


                        @if (
                            request()->filled('search') ||
                            request()->filled('status')
                        )

                            <a
                                href="{{ route('technician.complaints.index') }}"
                                title="Clear filters"
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
        {{-- COMPLAINTS --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

                <div class="flex flex-col sm:flex-row
                            sm:items-center sm:justify-between gap-3">

                    <div>

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-clipboard-list
                                      text-indigo-600 mr-2"></i>

                            Maintenance Work

                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Complaints assigned to you as part of the maintenance team.
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
            {{-- DESKTOP TABLE --}}
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
                                Team
                            </th>

                            <th class="px-5 sm:px-6 py-4 text-left">
                                Status
                            </th>

                            <th class="px-5 sm:px-6 py-4 text-right">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($complaints as $complaint)

                            @php

                                $statusClasses = match ($complaint->status) {
                                    'Assigned' => 'bg-blue-100 text-blue-700',
                                    'In Progress' => 'bg-yellow-100 text-yellow-700',
                                    'Completed' => 'bg-green-100 text-green-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };

                            @endphp

                            <tr class="hover:bg-gray-50 transition">


                                {{-- Complaint --}}
                                <td class="px-5 sm:px-6 py-4">

                                    <p class="font-semibold text-gray-900">
                                        {{ $complaint->complaint_no }}
                                    </p>

                                    <p class="text-gray-500 mt-1 max-w-xs">
                                        {{ Str::limit($complaint->subject, 45) }}
                                    </p>

                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $complaint->created_at?->format('M d, Y h:i A') }}
                                    </p>

                                </td>


                                {{-- Consumer --}}
                                <td class="px-5 sm:px-6 py-4">

                                    @if ($complaint->consumer)

                                        <div class="font-medium text-gray-900">
                                            {{ $complaint->consumer->full_name }}
                                        </div>

                                        <div class="text-xs text-gray-500 mt-1">
                                            Account:
                                            {{ $complaint->consumer->account_number }}
                                        </div>

                                    @else

                                        <div class="font-medium text-gray-900">
                                            {{ $complaint->complainant_name ?? 'Unknown' }}
                                        </div>

                                        @if ($complaint->complainant_phone)

                                            <div class="text-xs text-gray-500 mt-1">
                                                <i class="fas fa-phone mr-1"></i>
                                                {{ $complaint->complainant_phone }}
                                            </div>

                                        @endif

                                    @endif

                                </td>


                                {{-- Location --}}
                                <td class="px-5 sm:px-6 py-4">

                                    <div class="flex items-start gap-2">

                                        <i class="fas fa-location-dot
                                                  text-gray-400 mt-1"></i>

                                        <div>

                                            <p class="text-gray-700 max-w-xs">
                                                {{ Str::limit($complaint->address, 40) }}
                                            </p>

                                            @if (
                                                !is_null($complaint->latitude) &&
                                                !is_null($complaint->longitude)
                                            )

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


                                {{-- Team --}}
                                <td class="px-5 sm:px-6 py-4">

                                    <div class="space-y-1.5">

                                        @foreach ($complaint->technicians as $technician)

                                            <div class="flex items-center gap-2">

                                                <div class="w-7 h-7 rounded-full
                                                            bg-indigo-100 text-indigo-700
                                                            flex items-center justify-center
                                                            text-xs font-semibold shrink-0">

                                                    {{ strtoupper(substr($technician->first_name, 0, 1)) }}
                                                    {{ strtoupper(substr($technician->last_name, 0, 1)) }}

                                                </div>

                                                <div class="min-w-0">

                                                    <p class="text-xs font-medium text-gray-800 truncate max-w-[150px]">

                                                        @if ($technician->id === auth()->id())
                                                            You
                                                        @else
                                                            {{ $technician->full_name }}
                                                        @endif

                                                    </p>

                                                </div>

                                            </div>

                                        @endforeach

                                    </div>

                                    <p class="text-xs text-gray-400 mt-2">

                                        {{ $complaint->technicians_count }}
                                        technician{{ $complaint->technicians_count !== 1 ? 's' : '' }}

                                    </p>

                                </td>


                                {{-- Status --}}
                                <td class="px-5 sm:px-6 py-4">

                                    <span class="inline-flex items-center gap-1.5
                                                 px-2.5 py-1 rounded-full
                                                 text-xs font-medium
                                                 {{ $statusClasses }}">

                                        <i class="fas fa-circle text-[6px]"></i>

                                        {{ $complaint->status }}

                                    </span>

                                </td>


                                {{-- Action --}}
                                <td class="px-5 sm:px-6 py-4 text-right">

                                    <a
                                        href="{{ route('technician.complaints.show', $complaint) }}"
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

                                    <div class="w-14 h-14 mx-auto rounded-2xl
                                                bg-gray-100 text-gray-400
                                                flex items-center justify-center">

                                        <i class="fas fa-clipboard-check text-xl"></i>

                                    </div>

                                    <h3 class="font-semibold text-gray-900 mt-4">
                                        No Assigned Complaints
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">
                                        No maintenance complaints match your current filters.
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

                @forelse ($complaints as $complaint)

                    @php

                        $statusClasses = match ($complaint->status) {
                            'Assigned' => 'bg-blue-100 text-blue-700',
                            'In Progress' => 'bg-yellow-100 text-yellow-700',
                            'Completed' => 'bg-green-100 text-green-700',
                            default => 'bg-gray-100 text-gray-700',
                        };

                    @endphp

                    <div class="p-5 space-y-4">


                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">

                                <p class="font-semibold text-gray-900">
                                    {{ $complaint->complaint_no }}
                                </p>

                                <p class="text-sm text-gray-500 mt-1">
                                    {{ Str::limit($complaint->subject, 60) }}
                                </p>

                            </div>

                        </div>


                        {{-- Consumer --}}
                        <div class="text-sm text-gray-600">

                            <i class="fas fa-user text-gray-400 mr-1"></i>

                            {{ $complaint->consumer?->full_name
                                ?? $complaint->complainant_name
                                ?? 'Unknown consumer' }}

                        </div>


                        {{-- Account --}}
                        @if ($complaint->consumer)

                            <div class="text-xs text-gray-500">

                                <i class="fas fa-id-card text-gray-400 mr-1"></i>

                                Account:
                                {{ $complaint->consumer->account_number }}

                            </div>

                        @endif


                        {{-- Location --}}
                        <div class="text-sm text-gray-600">

                            <i class="fas fa-location-dot text-gray-400 mr-1"></i>

                            {{ Str::limit($complaint->address, 80) }}

                        </div>


                        {{-- Team --}}
                        <div>

                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">

                                Assigned Team

                            </p>

                            <div class="flex flex-wrap gap-2">

                                @foreach ($complaint->technicians as $technician)

                                    <span class="inline-flex items-center gap-1.5
                                                 px-2.5 py-1.5 rounded-lg
                                                 bg-indigo-50 text-indigo-700
                                                 text-xs font-medium">

                                        <i class="fas fa-user-gear"></i>

                                        @if ($technician->id === auth()->id())
                                            You
                                        @else
                                            {{ $technician->full_name }}
                                        @endif

                                    </span>

                                @endforeach

                            </div>

                        </div>


                        {{-- Status --}}
                        <div class="flex items-center justify-between">

                            <span class="inline-flex items-center gap-1.5
                                         px-2.5 py-1 rounded-full
                                         text-xs font-medium
                                         {{ $statusClasses }}">

                                <i class="fas fa-circle text-[6px]"></i>

                                {{ $complaint->status }}

                            </span>


                            <a
                                href="{{ route('technician.complaints.show', $complaint) }}"
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

                        <p class="text-sm text-gray-500 mt-1">
                            No assigned maintenance cases match your filters.
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
