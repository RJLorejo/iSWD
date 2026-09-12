@extends('maintenance-manager.layouts.app')

@section('title', 'Maintenance Manager Dashboard')

@section('content')

    <div class="space-y-6">

        {{-- ========================================================= --}}
        {{-- PAGE HEADER --}}
        {{-- ========================================================= --}}

        <x-form.page-header title=""
            subtitle="Monitor maintenance operations, assign technicians, and manage verified complaints." />


        {{-- ========================================================= --}}
        {{-- MAINTENANCE OPERATIONS SUMMARY --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

            {{-- ===================================================== --}}
            {{-- VERIFIED COMPLAINTS --}}
            {{-- ===================================================== --}}

            <x-form.card>
                <div class="p-5">

                    <div class="flex items-center justify-between gap-3">

                        <div class="min-w-0">

                            <p class="text-sm text-gray-500">
                                Verified Complaints
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $verifiedComplaints }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                Ready for processing
                            </p>

                        </div>

                        <div
                            class="w-11 h-11 shrink-0 rounded-xl
                           bg-indigo-100 text-indigo-600
                           flex items-center justify-center">

                            <i class="fas fa-shield-check"></i>

                        </div>

                    </div>

                </div>
            </x-form.card>


            {{-- ===================================================== --}}
            {{-- UNASSIGNED CASES --}}
            {{-- ===================================================== --}}

            <x-form.card>
                <div class="p-5">

                    <div class="flex items-center justify-between gap-3">

                        <div class="min-w-0">

                            <p class="text-sm text-gray-500">
                                Unassigned Cases
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $unassignedCases }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                Awaiting technician
                            </p>

                        </div>

                        <div
                            class="w-11 h-11 shrink-0 rounded-xl
                           bg-amber-100 text-amber-600
                           flex items-center justify-center">

                            <i class="fas fa-user-clock"></i>

                        </div>

                    </div>

                </div>
            </x-form.card>


            {{-- ===================================================== --}}
            {{-- ASSIGNED CASES --}}
            {{-- ===================================================== --}}

            <x-form.card>
                <div class="p-5">

                    <div class="flex items-center justify-between gap-3">

                        <div class="min-w-0">

                            <p class="text-sm text-gray-500">
                                Assigned Cases
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $assignedCases }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                Assigned to technicians
                            </p>

                        </div>

                        <div
                            class="w-11 h-11 shrink-0 rounded-xl
                           bg-blue-100 text-blue-600
                           flex items-center justify-center">

                            <i class="fas fa-user-check"></i>

                        </div>

                    </div>

                </div>
            </x-form.card>


            {{-- ===================================================== --}}
            {{-- IN PROGRESS --}}
            {{-- ===================================================== --}}

            <x-form.card>
                <div class="p-5">

                    <div class="flex items-center justify-between gap-3">

                        <div class="min-w-0">

                            <p class="text-sm text-gray-500">
                                In Progress
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $inProgressComplaints }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                Active maintenance
                            </p>

                        </div>

                        <div
                            class="w-11 h-11 shrink-0 rounded-xl
                           bg-orange-100 text-orange-600
                           flex items-center justify-center">

                            <i class="fas fa-screwdriver-wrench"></i>

                        </div>

                    </div>

                </div>
            </x-form.card>


            {{-- ===================================================== --}}
            {{-- REPORTS FOR REVIEW --}}
            {{-- ===================================================== --}}

            <x-form.card>
                <div class="p-5">

                    <div class="flex items-center justify-between gap-3">

                        <div class="min-w-0">

                            <p class="text-sm text-gray-500">
                                Reports for Review
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $reportsForReview }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                Awaiting validation
                            </p>

                        </div>

                        <div
                            class="w-11 h-11 shrink-0 rounded-xl
                           bg-purple-100 text-purple-600
                           flex items-center justify-center">

                            <i class="fas fa-file-circle-check"></i>

                        </div>

                    </div>

                </div>
            </x-form.card>


            {{-- ===================================================== --}}
            {{-- COMPLETED TODAY --}}
            {{-- ===================================================== --}}

            <x-form.card>
                <div class="p-5">

                    <div class="flex items-center justify-between gap-3">

                        <div class="min-w-0">

                            <p class="text-sm text-gray-500">
                                Completed Today
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $completedToday }}
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                Successfully completed
                            </p>

                        </div>

                        <div
                            class="w-11 h-11 shrink-0 rounded-xl
                           bg-green-100 text-green-600
                           flex items-center justify-center">

                            <i class="fas fa-circle-check"></i>

                        </div>

                    </div>

                </div>
            </x-form.card>

        </div>

{{-- ========================================================= --}}
{{-- OPERATIONAL ANALYTICS --}}
{{-- ========================================================= --}}

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    {{-- ===================================================== --}}
    {{-- CASE STATUS --}}
    {{-- ===================================================== --}}

    <x-form.card>

        <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

            <div>

                <h3 class="font-semibold text-gray-900">

                    <i class="fas fa-chart-column text-indigo-600 mr-2"></i>

                    Maintenance Case Status

                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Current distribution of maintenance cases.
                </p>

            </div>

        </div>


        <div class="p-5">

            <div
                id="maintenanceCaseStatusChart"
                class="w-full">
            </div>

        </div>

    </x-form.card>


    {{-- ===================================================== --}}
    {{-- WORKFLOW OVERVIEW --}}
    {{-- ===================================================== --}}

    <x-form.card>

        <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

            <div>

                <h3 class="font-semibold text-gray-900">

                    <i class="fas fa-chart-pie text-blue-600 mr-2"></i>

                    Workflow Overview

                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Current maintenance workload distribution.
                </p>

            </div>

        </div>


        <div class="p-5">

            <div
                id="maintenanceWorkflowChart"
                class="w-full">
            </div>

        </div>

    </x-form.card>

</div>
<script>
    window.maintenanceDashboard = {
        verified: @json($verifiedComplaints),
        unassigned: @json($unassignedCases),
        assigned: @json($assignedCases),
        inProgress: @json($inProgressComplaints),
        review: @json($reportsForReview),
        completed: @json($completedToday),
    };
</script>

        {{-- ========================================================= --}}
        {{-- QUICK ACTIONS --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div class="p-5">

                <div class="flex flex-col sm:flex-row
                        sm:items-center sm:justify-between gap-4">

                    <div>

                        <h3 class="font-semibold text-gray-900">
                            Maintenance Operations
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Manage verified complaints and technician assignments.
                        </p>

                    </div>

                    <div class="flex flex-wrap gap-2">

                        <a href="#"
                            class="inline-flex items-center justify-center
                               px-4 py-2.5 rounded-xl
                               bg-indigo-600 text-white
                               hover:bg-indigo-700 transition">

                            <i class="fas fa-list-check mr-2"></i>

                            Verified Complaints

                        </a>

                        <a href="#"
                            class="inline-flex items-center justify-center
                               px-4 py-2.5 rounded-xl
                               border border-gray-300
                               text-gray-700
                               hover:bg-gray-50 transition">

                            <i class="fas fa-clipboard-list mr-2"></i>

                            Work Orders

                        </a>

                    </div>

                </div>

            </div>

        </x-form.card>


        {{-- ========================================================= --}}
        {{-- MAIN DASHBOARD --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">


            {{-- ===================================================== --}}
            {{-- VERIFIED COMPLAINTS --}}
            {{-- ===================================================== --}}

            <div class="xl:col-span-2">

                <x-form.card>

                    <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

                        <div
                            class="flex flex-col sm:flex-row
                                sm:items-center
                                sm:justify-between gap-3">

                            <div>

                                <h3 class="font-semibold text-gray-900">

                                    <i
                                        class="fas fa-shield-check
                                         text-indigo-600 mr-2"></i>

                                    Complaints Ready for Assignment

                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    Verified complaints waiting for a technician.
                                </p>

                            </div>

                            <span
                                class="self-start sm:self-auto
                                     px-3 py-1.5 rounded-full
                                     bg-indigo-100 text-indigo-700
                                     text-xs font-semibold">

                                {{ $verifiedComplaints }} Ready

                            </span>

                        </div>

                    </div>


                    <div class="divide-y divide-gray-100">

                        @forelse ($recentVerifiedComplaints as $complaint)
                            <div class="p-5 hover:bg-gray-50 transition">

                                <div
                                    class="flex flex-col sm:flex-row
                                        sm:items-center
                                        sm:justify-between gap-4">

                                    <div class="min-w-0">

                                        <div class="flex flex-wrap items-center gap-2">

                                            <span class="font-semibold text-gray-900">

                                                {{ $complaint->complaint_no }}

                                            </span>

                                            <span
                                                class="px-2 py-1 rounded-full
                                                   text-xs font-medium
                                                   @if ($complaint->priority === 'Critical') bg-red-100 text-red-700
                                                   @elseif ($complaint->priority === 'High')
                                                       bg-orange-100 text-orange-700
                                                   @elseif ($complaint->priority === 'Medium')
                                                       bg-yellow-100 text-yellow-700
                                                   @else
                                                       bg-green-100 text-green-700 @endif">

                                                {{ $complaint->priority }}

                                            </span>

                                        </div>

                                        <p class="text-sm text-gray-700 mt-2">

                                            {{ Str::limit($complaint->subject, 70) }}

                                        </p>

                                        <div
                                            class="flex flex-wrap gap-x-4 gap-y-1
                                                text-xs text-gray-500 mt-2">

                                            <span>
                                                <i class="fas fa-tag mr-1"></i>
                                                {{ $complaint->category?->name ?? '—' }}
                                            </span>

                                            <span>
                                                <i class="fas fa-location-dot mr-1"></i>
                                                {{ Str::limit($complaint->address, 35) }}
                                            </span>

                                        </div>

                                    </div>


                                    <a href="#"
                                        class="shrink-0 inline-flex items-center
                                           justify-center
                                           px-4 py-2.5 rounded-xl
                                           bg-indigo-600 text-white
                                           hover:bg-indigo-700 transition">

                                        <i class="fas fa-user-plus mr-2"></i>

                                        Assign

                                    </a>

                                </div>

                            </div>

                        @empty

                            <div class="p-10 text-center">

                                <div
                                    class="w-14 h-14 mx-auto
                                        rounded-2xl
                                        bg-green-100 text-green-600
                                        flex items-center justify-center">

                                    <i class="fas fa-circle-check text-xl"></i>

                                </div>

                                <h4 class="font-semibold text-gray-900 mt-4">
                                    No Complaints Ready for Assignment
                                </h4>

                                <p class="text-sm text-gray-500 mt-1">
                                    Verified complaints will appear here.
                                </p>

                            </div>
                        @endforelse

                    </div>

                </x-form.card>

            </div>


            {{-- ===================================================== --}}
            {{-- TECHNICIAN WORKLOAD --}}
            {{-- ===================================================== --}}

            <div>

                <x-form.card>

                    <div class="px-5 py-5 border-b border-gray-100">

                        <h3 class="font-semibold text-gray-900">

                            <i class="fas fa-users-gear
                                 text-blue-600 mr-2"></i>

                            Technician Workload

                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Current active assignments.
                        </p>

                    </div>


                    <div class="divide-y divide-gray-100">

                        @forelse ($technicians as $technician)
                            <div class="p-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="w-10 h-10 rounded-full
                                            bg-blue-100 text-blue-600
                                            flex items-center justify-center
                                            font-semibold">

                                        {{ strtoupper(substr($technician->name ?? 'T', 0, 1)) }}

                                    </div>

                                    <div class="flex-1 min-w-0">

                                        <p class="font-medium text-gray-900 truncate">

                                            {{ $technician->name }}

                                        </p>

                                        <p class="text-xs text-gray-500">

                                            Maintenance Technician

                                        </p>

                                    </div>

                                    <span
                                        class="shrink-0
                                           px-2.5 py-1 rounded-full
                                           bg-gray-100 text-gray-700
                                           text-xs font-semibold">

                                        {{ $technician->active_complaints_count }}

                                    </span>

                                </div>

                            </div>

                        @empty

                            <div class="p-8 text-center text-sm text-gray-500">

                                No technicians available.

                            </div>
                        @endforelse

                    </div>


                    <div class="p-4 border-t border-gray-100">

                        <a href="#"
                            class="block text-center
                               text-sm font-medium
                               text-blue-600
                               hover:text-blue-700">

                            View Technician Workload
                            <i class="fas fa-arrow-right ml-1"></i>

                        </a>

                    </div>

                </x-form.card>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- ACTIVE MAINTENANCE --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

                <div>

                    <h3 class="font-semibold text-gray-900">

                        <i class="fas fa-screwdriver-wrench
                             text-orange-600 mr-2"></i>

                        Active Maintenance

                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Complaints currently assigned or being worked on.
                    </p>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="min-w-[800px] w-full text-sm">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>

                            <th class="px-5 py-4 text-left font-semibold text-gray-600">
                                Complaint
                            </th>

                            <th class="px-5 py-4 text-left font-semibold text-gray-600">
                                Consumer
                            </th>

                            <th class="px-5 py-4 text-left font-semibold text-gray-600">
                                Technician
                            </th>

                            <th class="px-5 py-4 text-left font-semibold text-gray-600">
                                Status
                            </th>

                            <th class="px-5 py-4 text-right font-semibold text-gray-600">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($activeComplaints as $complaint)
                            <tr class="hover:bg-gray-50 transition">

                                <td class="px-5 py-4">

                                    <p class="font-semibold text-gray-900">

                                        {{ $complaint->complaint_no }}

                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">

                                        {{ Str::limit($complaint->subject, 40) }}

                                    </p>

                                </td>


                                <td class="px-5 py-4">

                                    {{ $complaint->consumer?->full_name ?? 'Walk-in Consumer' }}

                                </td>


                                <td class="px-5 py-4">

                                    @if ($complaint->technician)
                                        <span class="font-medium text-gray-900">

                                            {{ $complaint->technician->name }}

                                        </span>
                                    @else
                                        <span class="text-gray-400">
                                            Unassigned
                                        </span>
                                    @endif

                                </td>


                                <td class="px-5 py-4">

                                    <span
                                        class="px-2.5 py-1 rounded-full
                                           text-xs font-medium
                                           @if ($complaint->status === 'In Progress') bg-orange-100 text-orange-700
                                           @else
                                               bg-blue-100 text-blue-700 @endif">

                                        {{ $complaint->status }}

                                    </span>

                                </td>


                                <td class="px-5 py-4 text-right">

                                    <a href="#"
                                        class="inline-flex items-center
                                           px-3 py-2 rounded-lg
                                           border border-gray-300
                                           text-gray-700
                                           hover:bg-gray-50">

                                        <i class="fas fa-eye mr-2"></i>

                                        View

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="px-5 py-10 text-center
                                       text-sm text-gray-500">

                                    No active maintenance complaints.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </x-form.card>


    </div>



@endsection
