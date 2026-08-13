@extends('technician.layouts.app')

@section('title', 'Technician Dashboard')

@section('content')

    <div class="space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

            <div>

                <p class="text-sm font-medium text-indigo-600">
                    Maintenance Operations
                </p>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">
                    Technician Dashboard
                </h1>

                <p class="text-sm text-gray-500 mt-2 max-w-2xl">
                    Manage your assigned complaints, perform maintenance work,
                    and monitor completed service requests.
                </p>

            </div>

            <div class="flex flex-wrap gap-3">

                <a href="{{ route('technician.complaints.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                       bg-indigo-600 text-white font-medium
                       hover:bg-indigo-700 transition shadow-sm">

                    <i class="fas fa-screwdriver-wrench"></i>

                    My Maintenance Work

                </a>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Assigned --}}
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

                        <div
                            class="w-12 h-12 shrink-0 rounded-xl
                                bg-blue-100 text-blue-600
                                flex items-center justify-center">

                            <i class="fas fa-clipboard-list text-lg"></i>

                        </div>

                    </div>

                </div>

            </x-form.card>


            {{-- In Progress --}}
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

                        <div
                            class="w-12 h-12 shrink-0 rounded-xl
                                bg-yellow-100 text-yellow-600
                                flex items-center justify-center">

                            <i class="fas fa-screwdriver-wrench text-lg"></i>

                        </div>

                    </div>

                </div>

            </x-form.card>


            {{-- Completed --}}
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
                                Completed repairs
                            </p>

                        </div>

                        <div
                            class="w-12 h-12 shrink-0 rounded-xl
                                bg-green-100 text-green-600
                                flex items-center justify-center">

                            <i class="fas fa-circle-check text-lg"></i>

                        </div>

                    </div>

                </div>

            </x-form.card>


            {{-- Urgent --}}
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

                        <div
                            class="w-12 h-12 shrink-0 rounded-xl
                                bg-red-100 text-red-600
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

            {{-- Active Work --}}
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

                        <div
                            class="w-11 h-11 rounded-xl
                                bg-indigo-100 text-indigo-600
                                flex items-center justify-center">

                            <i class="fas fa-list-check"></i>

                        </div>

                    </div>

                </div>

            </x-form.card>


            {{-- Total Work --}}
            <x-form.card>

                <div class="p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Total Work
                            </p>

                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ $totalCount }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                All assigned complaints
                            </p>

                        </div>

                        <div
                            class="w-11 h-11 rounded-xl
                                bg-gray-100 text-gray-600
                                flex items-center justify-center">

                            <i class="fas fa-layer-group"></i>

                        </div>

                    </div>

                </div>

            </x-form.card>


            {{-- Completion --}}
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

                        <div
                            class="w-11 h-11 rounded-xl
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

                <div class="flex flex-col sm:flex-row sm:items-center
                        sm:justify-between gap-3">

                    <div>

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-clipboard-check text-indigo-600 mr-2"></i>

                            My Current Work

                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Active complaints assigned to you.
                        </p>

                    </div>

                    <div class="flex items-center gap-2">

                        @if ($urgentCount > 0)
                            <span
                                class="inline-flex items-center gap-1.5
                                     px-3 py-1.5 rounded-full
                                     bg-red-50 text-red-700
                                     text-xs font-medium">

                                <i class="fas fa-triangle-exclamation"></i>

                                {{ $urgentCount }} Urgent

                            </span>
                        @endif

                        <span
                            class="inline-flex items-center
                                 px-3 py-1.5 rounded-full
                                 bg-indigo-50 text-indigo-700
                                 text-xs font-medium">

                            {{ $activeCount }} Active

                        </span>

                    </div>

                </div>

            </div>


            {{-- Desktop --}}
            <div class="hidden md:block overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>

                            <th class="px-6 py-4 text-left font-semibold text-gray-600">
                                Complaint
                            </th>

                            <th class="px-6 py-4 text-left font-semibold text-gray-600">
                                Location
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

                                <td class="px-6 py-4">

                                    <p class="font-semibold text-gray-900">
                                        {{ $complaint->complaint_no }}
                                    </p>

                                    <p class="text-gray-500 mt-1">
                                        {{ Str::limit($complaint->subject, 45) }}
                                    </p>

                                </td>


                                <td class="px-6 py-4">

                                    <div class="flex items-start gap-2">

                                        <i class="fas fa-location-dot text-gray-400 mt-1"></i>

                                        <div>

                                            <p class="text-gray-700">
                                                {{ Str::limit($complaint->address, 40) }}
                                            </p>

                                            @if ($complaint->latitude && $complaint->longitude)
                                                <p class="text-xs text-green-600 mt-1">
                                                    <i class="fas fa-map-location-dot mr-1"></i>
                                                    Map location available
                                                </p>
                                            @endif

                                        </div>

                                    </div>

                                </td>


                                <td class="px-6 py-4">

                                    @if ($complaint->priority === 'Critical')
                                        <span
                                            class="inline-flex items-center gap-1.5
                                                 px-2.5 py-1 rounded-full
                                                 bg-red-100 text-red-700
                                                 text-xs font-medium">

                                            <i class="fas fa-triangle-exclamation"></i>
                                            Critical

                                        </span>
                                    @elseif($complaint->priority === 'High')
                                        <span
                                            class="inline-flex items-center gap-1.5
                                                 px-2.5 py-1 rounded-full
                                                 bg-orange-100 text-orange-700
                                                 text-xs font-medium">

                                            <i class="fas fa-arrow-up"></i>
                                            High

                                        </span>
                                    @elseif($complaint->priority === 'Medium')
                                        <span
                                            class="inline-flex items-center gap-1.5
                                                 px-2.5 py-1 rounded-full
                                                 bg-yellow-100 text-yellow-700
                                                 text-xs font-medium">

                                            Medium

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center
                                                 px-2.5 py-1 rounded-full
                                                 bg-green-100 text-green-700
                                                 text-xs font-medium">

                                            Low

                                        </span>
                                    @endif

                                </td>


                                <td class="px-6 py-4">

                                    @if ($complaint->status === 'Assigned')
                                        <span
                                            class="inline-flex items-center gap-1.5
                                                 px-2.5 py-1 rounded-full
                                                 bg-blue-100 text-blue-700
                                                 text-xs font-medium">

                                            <i class="fas fa-user-check"></i>
                                            Assigned

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5
                                                 px-2.5 py-1 rounded-full
                                                 bg-yellow-100 text-yellow-700
                                                 text-xs font-medium">

                                            <i class="fas fa-spinner"></i>
                                            In Progress

                                        </span>
                                    @endif

                                </td>


                                <td class="px-6 py-4 text-right">

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

                                <td colspan="5" class="px-6 py-12 text-center">

                                    <div
                                        class="w-14 h-14 mx-auto rounded-2xl
                                            bg-green-100 text-green-600
                                            flex items-center justify-center">

                                        <i class="fas fa-circle-check text-xl"></i>

                                    </div>

                                    <h3 class="font-semibold text-gray-900 mt-4">
                                        No Active Work
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">
                                        You currently have no assigned maintenance work.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Mobile --}}
            <div class="md:hidden divide-y divide-gray-100">

                @forelse($currentComplaints as $complaint)
                    <div class="p-5 space-y-4">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">

                                <p class="font-semibold text-gray-900">
                                    {{ $complaint->complaint_no }}
                                </p>

                                <p class="text-sm text-gray-500 mt-1">
                                    {{ Str::limit($complaint->subject, 60) }}
                                </p>

                            </div>

                            <span
                                class="shrink-0 px-2.5 py-1 rounded-full
                                     text-xs font-medium
                                     {{ $complaint->priority === 'Critical'
                                         ? 'bg-red-100 text-red-700'
                                         : ($complaint->priority === 'High'
                                             ? 'bg-orange-100 text-orange-700'
                                             : ($complaint->priority === 'Medium'
                                                 ? 'bg-yellow-100 text-yellow-700'
                                                 : 'bg-green-100 text-green-700')) }}">

                                {{ $complaint->priority }}

                            </span>

                        </div>


                        <div class="text-sm text-gray-600">

                            <i class="fas fa-location-dot text-gray-400 mr-1"></i>

                            {{ $complaint->address }}

                        </div>


                        <div class="flex items-center justify-between">

                            <span
                                class="text-xs font-medium
                                     {{ $complaint->status === 'Assigned' ? 'text-blue-700' : 'text-yellow-700' }}">

                                <i class="fas fa-circle text-[7px] mr-1"></i>

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

                        <i class="fas fa-circle-check text-green-500 text-3xl"></i>

                        <p class="font-medium text-gray-900 mt-3">
                            No active work
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

                            <i class="fas fa-clock-rotate-left text-green-600 mr-2"></i>

                            Recently Completed

                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Your most recently completed maintenance work.
                        </p>

                    </div>

                    <a href="{{ route('technician.complaints.index', ['status' => 'Completed']) }}"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-700">

                        View All

                    </a>

                </div>

            </div>


            <div class="divide-y divide-gray-100">

                @forelse($recentCompleted as $complaint)
                    <div class="p-5 flex items-center justify-between gap-4">

                        <div class="min-w-0">

                            <p class="font-semibold text-gray-900">
                                {{ $complaint->complaint_no }}
                            </p>

                            <p class="text-sm text-gray-500 truncate mt-1">
                                {{ $complaint->subject }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">

                                <i class="fas fa-location-dot mr-1"></i>

                                {{ Str::limit($complaint->address, 50) }}

                            </p>

                        </div>


                        <div class="text-right shrink-0">

                            <span
                                class="inline-flex items-center gap-1
                                     text-xs font-medium text-green-700">

                                <i class="fas fa-circle-check"></i>

                                Completed

                            </span>

                            @if ($complaint->completed_at)
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $complaint->completed_at->format('M d, Y') }}
                                </p>
                            @endif

                        </div>

                    </div>

                @empty

                    <div class="p-8 text-center">

                        <i class="fas fa-clock-rotate-left
                              text-gray-300 text-3xl"></i>

                        <p class="text-sm text-gray-500 mt-3">
                            No completed maintenance work yet.
                        </p>

                    </div>
                @endforelse

            </div>

        </x-form.card>

    </div>

@endsection
