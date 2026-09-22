@extends('maintenance-manager.layouts.app')

@section('title', 'Complaint Details')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

        <div>

            <div class="flex flex-wrap items-center gap-2">

                <a
                    href="{{ route('maintenance-manager.complaints.index') }}"
                    class="inline-flex items-center gap-1.5 text-sm
                           text-gray-500 hover:text-blue-600 transition"
                >
                    <svg class="w-4 h-4"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M15 19l-7-7 7-7"/>
                    </svg>

                    Complaints
                </a>

                <span class="text-gray-300">/</span>

                <span class="text-sm text-gray-500">
                    {{ $complaint->complaint_no }}
                </span>

            </div>


            <div class="mt-3">

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    {{ $complaint->subject }}
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Complaint #{{ $complaint->complaint_no }}
                </p>

            </div>

        </div>


        {{-- STATUS --}}
        @php

            $statusClasses = match($complaint->status) {

                'Verified' =>
                    'bg-blue-50 text-blue-700 border-blue-200',

                'Assigned' =>
                    'bg-indigo-50 text-indigo-700 border-indigo-200',

                'In Progress' =>
                    'bg-amber-50 text-amber-700 border-amber-200',

                'Completed' =>
                    'bg-green-50 text-green-700 border-green-200',

                'Closed' =>
                    'bg-gray-100 text-gray-700 border-gray-200',

                default =>
                    'bg-gray-50 text-gray-600 border-gray-200',

            };

        @endphp


        <div class="flex items-center gap-2">

            <span class="inline-flex items-center gap-2 px-3 py-2
                         rounded-full border text-sm font-semibold
                         {{ $statusClasses }}">

                <span class="w-2 h-2 rounded-full bg-current"></span>

                {{ $complaint->status }}

            </span>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- MAIN GRID --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">


        {{-- ===================================================== --}}
        {{-- LEFT / MAIN CONTENT --}}
        {{-- ===================================================== --}}

        <div class="xl:col-span-2 space-y-6">


            {{-- ================================================= --}}
            {{-- COMPLAINT INFORMATION --}}
            {{-- ================================================= --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-100">

                    <h2 class="font-bold text-gray-900">
                        Complaint Information
                    </h2>

                    <p class="text-xs text-gray-500 mt-1">
                        Details submitted by the consumer
                    </p>

                </div>


                <div class="p-5 space-y-6">


                    {{-- DESCRIPTION --}}
                    <div>

                        <p class="text-xs uppercase tracking-wide
                                  font-semibold text-gray-400">
                            Description
                        </p>

                        <div class="mt-2 rounded-xl bg-gray-50 border border-gray-100 p-4">

                            <p class="text-sm leading-6 text-gray-700 whitespace-pre-line">
                                {{ $complaint->description }}
                            </p>

                        </div>

                    </div>


                    {{-- CATEGORY + PRIORITY --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        <div>

                            <p class="text-xs uppercase tracking-wide
                                      font-semibold text-gray-400">
                                Complaint Type
                            </p>

                            <p class="mt-1 text-sm font-semibold text-gray-800">
                                {{ $complaint->category?->name ?? 'Uncategorized' }}
                            </p>

                        </div>

                    </div>


                    {{-- LOCATION --}}
                    <div>

                        <p class="text-xs uppercase tracking-wide
                                  font-semibold text-gray-400">
                            Complaint Location
                        </p>

                        <div class="mt-2 p-4 rounded-xl bg-gray-50 border border-gray-100">

                            <p class="text-sm font-medium text-gray-800">
                                {{ $complaint->address }}
                            </p>

                            @if($complaint->landmark)

                                <p class="mt-1 text-xs text-gray-500">
                                    Landmark:
                                    {{ $complaint->landmark }}
                                </p>

                            @endif


                            @if($complaint->latitude && $complaint->longitude)

                                <div class="mt-3 flex flex-wrap gap-2">

                                    <span class="inline-flex items-center px-2.5 py-1
                                                 rounded-lg bg-white border border-gray-200
                                                 text-xs text-gray-500">
                                        Latitude:
                                        {{ number_format($complaint->latitude, 6) }}
                                    </span>

                                    <span class="inline-flex items-center px-2.5 py-1
                                                 rounded-lg bg-white border border-gray-200
                                                 text-xs text-gray-500">
                                        Longitude:
                                        {{ number_format($complaint->longitude, 6) }}
                                    </span>

                                </div>

                            @endif

                        </div>

                    </div>


                    {{-- PHOTO --}}
                    @if($complaint->photo)

                        <div>

                            <p class="text-xs uppercase tracking-wide
                                      font-semibold text-gray-400">
                                Submitted Photo
                            </p>

                            <div class="mt-2">

                                <img
                                    src="{{ asset('storage/' . $complaint->photo) }}"
                                    alt="Complaint photo"
                                    class="w-full max-h-[450px] object-cover
                                           rounded-xl border border-gray-200"
                                >

                            </div>

                        </div>

                    @endif

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- CONSUMER --}}
            {{-- ================================================= --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-100">

                    <h2 class="font-bold text-gray-900">
                        Consumer Information
                    </h2>

                </div>


                <div class="p-5">

                    <div class="flex items-center gap-4">

                        <div class="w-14 h-14 rounded-2xl bg-blue-100
                                    text-blue-700 flex items-center justify-center
                                    text-lg font-bold shrink-0">

                            {{ strtoupper(
                                substr($complaint->consumer?->first_name ?? $complaint->complainant_name ?? 'C', 0, 1)
                                .
                                substr($complaint->consumer?->last_name ?? '', 0, 1)
                            ) }}

                        </div>


                        <div>

                            <h3 class="font-bold text-gray-900">
                                {{ $complaint->consumer?->full_name
                                    ?? $complaint->complainant_name
                                    ?? 'Unknown Consumer' }}
                            </h3>

                            @if($complaint->consumer?->account_number)

                                <p class="text-sm text-blue-600 mt-0.5">
                                    Account No:
                                    {{ $complaint->consumer->account_number }}
                                </p>

                            @endif

                            @if($complaint->complainant_phone)

                                <p class="text-sm text-gray-500 mt-0.5">
                                    {{ $complaint->complainant_phone }}
                                </p>

                            @endif

                        </div>

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- VERIFICATION --}}
            {{-- ================================================= --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-100">

                    <h2 class="font-bold text-gray-900">
                        Verification
                    </h2>

                </div>


                <div class="p-5">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        <div>

                            <p class="text-xs uppercase tracking-wide
                                      font-semibold text-gray-400">
                                Verified By
                            </p>

                            <p class="mt-1 text-sm font-semibold text-gray-800">
                                {{ $complaint->verifier?->full_name
                                    ?? $complaint->verifiedBy?->full_name
                                    ?? 'Not available' }}
                            </p>

                        </div>


                        <div>

                            <p class="text-xs uppercase tracking-wide
                                      font-semibold text-gray-400">
                                Verified At
                            </p>

                            <p class="mt-1 text-sm font-semibold text-gray-800">
                                {{ $complaint->verified_at?->format('M d, Y h:i A')
                                    ?? 'Not available' }}
                            </p>

                        </div>

                    </div>


                    @if($complaint->verification_reason)

                        <div class="mt-5">

                            <p class="text-xs uppercase tracking-wide
                                      font-semibold text-gray-400">
                                Verification Notes
                            </p>

                            <p class="mt-2 text-sm text-gray-700 leading-6">
                                {{ $complaint->verification_reason }}
                            </p>

                        </div>

                    @endif

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- MAINTENANCE REPORT --}}
            {{-- ================================================= --}}

            @if($complaint->maintenanceReport)

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-100
                                flex items-center justify-between">

                        <div>

                            <h2 class="font-bold text-gray-900">
                                Maintenance Report
                            </h2>

                            <p class="text-xs text-gray-500 mt-1">
                                Plumber-submitted maintenance documentation
                            </p>

                        </div>

                    </div>


                    <div class="p-5">

                        <div class="rounded-xl bg-green-50 border border-green-100 p-4">

                            <div class="flex items-start gap-3">

                                <svg class="w-5 h-5 text-green-600 mt-0.5"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M5 13l4 4L19 7"/>
                                </svg>

                                <div>

                                    <p class="text-sm font-semibold text-green-800">
                                        Maintenance report submitted
                                    </p>

                                    <p class="mt-1 text-xs text-green-700">
                                        The maintenance team has submitted a report
                                        for this complaint.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            @endif

        </div>


        {{-- ===================================================== --}}
        {{-- RIGHT SIDEBAR --}}
        {{-- ===================================================== --}}

        <div class="space-y-6">


            {{-- ================================================= --}}
            {{-- ASSIGN TECHNICIANS --}}
            {{-- ================================================= --}}

            @if($complaint->status === 'Verified' && $complaint->technicians->isEmpty())

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-100">

                        <h2 class="font-bold text-gray-900">
                            Assign Maintenance Team
                        </h2>

                        <p class="text-xs text-gray-500 mt-1">
                            Select the technicians who will handle this complaint.
                        </p>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('maintenance-manager.complaints.assign', $complaint) }}"
                        id="assignmentForm"
                        class="p-5"
                    >

                        @csrf


                        {{-- SELECTED COUNTER --}}
                        <div class="mb-4 flex items-center justify-between
                                    rounded-xl bg-blue-50 border border-blue-100 p-3">

                            <div>

                                <p class="text-xs font-semibold text-blue-800">
                                    Maintenance Team
                                </p>

                                <p class="text-[11px] text-blue-600 mt-0.5">
                                    You can select up to 3 technicians.
                                </p>

                            </div>


                            <span
                                id="selectedCount"
                                class="inline-flex items-center justify-center
                                       min-w-8 h-8 px-2 rounded-full
                                       bg-blue-600 text-white text-xs font-bold"
                            >
                                0
                            </span>

                        </div>


                        {{-- TECHNICIANS --}}
                        <div class="space-y-3">

                            @forelse($technicians as $technician)

                                <label
                                    class="technician-card block cursor-pointer"
                                    data-technician-name="{{ $technician->full_name }}"
                                >

                                    <input
                                        type="checkbox"
                                        name="technician_ids[]"
                                        value="{{ $technician->id }}"
                                        class="technician-checkbox peer sr-only"
                                    >


                                    <div class="rounded-xl border-2 border-gray-200
                                                bg-white p-3 transition-all
                                                peer-checked:border-blue-500
                                                peer-checked:bg-blue-50
                                                hover:border-blue-300">

                                        <div class="flex items-center gap-3">

                                            {{-- AVATAR --}}
                                            <div class="w-10 h-10 rounded-xl
                                                        bg-blue-100 text-blue-700
                                                        flex items-center justify-center
                                                        text-xs font-bold shrink-0">

                                                {{ strtoupper(
                                                    substr($technician->first_name ?? '', 0, 1)
                                                    .
                                                    substr($technician->last_name ?? '', 0, 1)
                                                ) }}

                                            </div>


                                            {{-- NAME --}}
                                            <div class="min-w-0 flex-1">

                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $technician->full_name }}
                                                </p>

                                                @if($technician->employee_id)

                                                    <p class="text-[11px] text-gray-500">
                                                        Employee ID:
                                                        {{ $technician->employee_id }}
                                                    </p>

                                                @endif

                                                @if($technician->position?->name)

                                                    <p class="text-[11px] text-gray-400">
                                                        {{ $technician->position->name }}
                                                    </p>

                                                @endif

                                            </div>


                                            {{-- CHECK --}}
                                            <div class="w-6 h-6 rounded-full border-2
                                                        border-gray-300 flex items-center
                                                        justify-center shrink-0
                                                        peer-checked:bg-blue-600
                                                        peer-checked:border-blue-600">

                                                <svg
                                                    class="w-3.5 h-3.5 text-white opacity-0
                                                           peer-checked:opacity-100"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="3"
                                                          d="M5 13l4 4L19 7"/>
                                                </svg>

                                            </div>

                                        </div>

                                    </div>

                                </label>

                            @empty

                                <div class="rounded-xl border border-gray-200
                                            bg-gray-50 p-5 text-center">

                                    <p class="text-sm font-medium text-gray-700">
                                        No active technicians available.
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">
                                        Please check the technician accounts.
                                    </p>

                                </div>

                            @endforelse

                        </div>


                        {{-- ERROR --}}
                        @error('technician_ids')
                            <p class="mt-3 text-xs font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @enderror


                        {{-- SUBMIT --}}
                        <button
                            type="submit"
                            id="assignButton"
                            disabled
                            class="mt-5 w-full inline-flex items-center
                                   justify-center gap-2 px-4 py-3 rounded-xl
                                   bg-blue-600 text-white text-sm font-semibold
                                   hover:bg-blue-700 transition
                                   disabled:opacity-50 disabled:cursor-not-allowed"
                        >

                            <svg class="w-4 h-4"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M17 20h5V4H2v16h5M9 20v-6h6v6"/>
                            </svg>

                            Assign Maintenance Team

                        </button>

                    </form>

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- CURRENT TEAM --}}
            {{-- ================================================= --}}

            @if($complaint->technicians->count())

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-100">

                        <div class="flex items-center justify-between">

                            <div>

                                <h2 class="font-bold text-gray-900">
                                    Assigned Maintenance Team
                                </h2>

                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $complaint->technicians->count() }}
                                    {{ $complaint->technicians->count() === 1 ? 'technician' : 'technicians' }}
                                    assigned
                                </p>

                            </div>


                            <div class="w-9 h-9 rounded-xl bg-indigo-50
                                        flex items-center justify-center">

                                <svg class="w-5 h-5 text-indigo-600"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M17 20h5V4H2v16h5M9 20v-6h6v6"/>
                                </svg>

                            </div>

                        </div>

                    </div>


                    <div class="p-5 space-y-3">

                        @foreach($complaint->technicians as $technician)

                            <div class="flex items-center gap-3 rounded-xl
                                        border border-gray-100 bg-gray-50 p-3">

                                <div class="w-10 h-10 rounded-xl bg-blue-100
                                            text-blue-700 flex items-center
                                            justify-center text-xs font-bold shrink-0">

                                    {{ strtoupper(
                                        substr($technician->first_name ?? '', 0, 1)
                                        .
                                        substr($technician->last_name ?? '', 0, 1)
                                    ) }}

                                </div>


                                <div class="min-w-0 flex-1">

                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $technician->full_name }}
                                    </p>

                                    @if($technician->employee_id)

                                        <p class="text-[11px] text-gray-500">
                                            ID: {{ $technician->employee_id }}
                                        </p>

                                    @endif

                                    @if($technician->pivot?->assignment_role)

                                        <span class="inline-flex mt-1 px-2 py-0.5
                                                     rounded-md bg-white border
                                                     border-gray-200 text-[10px]
                                                     font-medium text-gray-500">

                                            {{ $technician->pivot->assignment_role }}

                                        </span>

                                    @endif

                                </div>


                                @if($technician->pivot?->status)

                                    <span class="text-[10px] font-semibold
                                                 text-gray-500 shrink-0">

                                        {{ $technician->pivot->status }}

                                    </span>

                                @endif

                            </div>

                        @endforeach

                    </div>

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- COMPLAINT TIMELINE --}}
            {{-- ================================================= --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                <div class="px-5 py-4 border-b border-gray-100">

                    <h2 class="font-bold text-gray-900">
                        Complaint Progress
                    </h2>

                </div>


                <div class="p-5">

                    <div class="relative space-y-6">


                        {{-- SUBMITTED --}}
                        <div class="flex gap-3">

                            <div class="w-8 h-8 rounded-full bg-blue-100
                                        text-blue-600 flex items-center
                                        justify-center shrink-0">

                                <svg class="w-4 h-4"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M5 13l4 4L19 7"/>
                                </svg>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-gray-900">
                                    Submitted
                                </p>

                                <p class="text-xs text-gray-500">
                                    {{ $complaint->created_at?->format('M d, Y h:i A') }}
                                </p>

                            </div>

                        </div>


                        {{-- VERIFIED --}}
                        @if($complaint->verified_at)

                            <div class="flex gap-3">

                                <div class="w-8 h-8 rounded-full bg-blue-100
                                            text-blue-600 flex items-center
                                            justify-center shrink-0">

                                    <svg class="w-4 h-4"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-gray-900">
                                        Verified
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        {{ $complaint->verified_at->format('M d, Y h:i A') }}
                                    </p>

                                    <p class="text-xs text-blue-600 mt-1">
                                        Verified by:
                                        <span class="font-semibold">
                                            {{ $complaint->verifier?->full_name
                                                ?? $complaint->verifiedBy?->full_name
                                                ?? 'Unknown' }}
                                        </span>
                                    </p>

                                </div>

                            </div>

                        @endif


                        {{-- ASSIGNED --}}
                        @if($complaint->technicians->count())

                            <div class="flex gap-3">

                                <div class="w-8 h-8 rounded-full bg-indigo-100
                                            text-indigo-600 flex items-center
                                            justify-center shrink-0">

                                    <svg class="w-4 h-4"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M17 20h5V4H2v16h5M9 20v-6h6v6"/>
                                    </svg>

                                </div>

                                <div class="min-w-0">

                                    <p class="text-sm font-semibold text-gray-900">
                                        Assigned
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        Maintenance team assigned
                                    </p>

                                    <div class="mt-2 space-y-1">

                                        @foreach($complaint->technicians as $technician)

                                            <p class="text-xs text-indigo-600">
                                                • {{ $technician->full_name }}
                                            </p>

                                        @endforeach

                                    </div>

                                </div>

                            </div>

                        @endif


                        {{-- IN PROGRESS --}}
                        @if(in_array($complaint->status, ['In Progress', 'Completed', 'Closed']))

                            <div class="flex gap-3">

                                <div class="w-8 h-8 rounded-full bg-amber-100
                                            text-amber-600 flex items-center
                                            justify-center shrink-0">

                                    <svg class="w-4 h-4"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M12 8v4l3 2"/>
                                    </svg>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-gray-900">
                                        In Progress
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        Maintenance work is underway.
                                    </p>

                                </div>

                            </div>

                        @endif


                        {{-- COMPLETED --}}
                        @if(in_array($complaint->status, ['Completed', 'Closed']))

                            <div class="flex gap-3">

                                <div class="w-8 h-8 rounded-full bg-green-100
                                            text-green-600 flex items-center
                                            justify-center shrink-0">

                                    <svg class="w-4 h-4"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-gray-900">
                                        Completed
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        Maintenance work has been completed.
                                    </p>

                                </div>

                            </div>

                        @endif


                        {{-- CLOSED --}}
                        @if($complaint->status === 'Closed')

                            <div class="flex gap-3">

                                <div class="w-8 h-8 rounded-full bg-gray-100
                                            text-gray-600 flex items-center
                                            justify-center shrink-0">

                                    <svg class="w-4 h-4"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-gray-900">
                                        Closed
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        Complaint case finalized.
                                    </p>

                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            </div>


            {{-- REPORT LINK --}}
            <a
                href="{{ route('maintenance-manager.complaints.report', $complaint) }}"
                target="_blank"
                class="w-full inline-flex items-center justify-center gap-2
                       px-4 py-3 rounded-xl bg-gray-900 text-white
                       text-sm font-semibold hover:bg-gray-800 transition"
            >

                <svg class="w-4 h-4"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2h-4.5L15 6H9L10.5 4H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>

                View / Print Report

            </a>

        </div>

    </div>

</div>


{{-- ============================================================= --}}
{{-- MULTI-TECHNICIAN SELECTION SCRIPT --}}
{{-- ============================================================= --}}

@if($complaint->status === 'Verified' && $complaint->technicians->isEmpty())

<script>

document.addEventListener('DOMContentLoaded', function () {

    const checkboxes = document.querySelectorAll('.technician-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const assignButton = document.getElementById('assignButton');
    const maxTechnicians = 3;

    function updateSelection() {

        const selected = Array.from(checkboxes)
            .filter(checkbox => checkbox.checked);

        const count = selected.length;

        selectedCount.textContent = count;

        assignButton.disabled = count === 0;

        checkboxes.forEach(function (checkbox) {

            if (!checkbox.checked && count >= maxTechnicians) {

                checkbox.disabled = true;

                checkbox
                    .closest('.technician-card')
                    ?.classList
                    .add('opacity-50', 'cursor-not-allowed');

            } else {

                checkbox.disabled = false;

                checkbox
                    .closest('.technician-card')
                    ?.classList
                    .remove('opacity-50', 'cursor-not-allowed');

            }

        });

        if (count > 0) {

            assignButton.innerHTML = `
                <svg class="w-4 h-4"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M17 20h5V4H2v16h5M9 20v-6h6v6"/>
                </svg>

                Assign ${count} Technician${count > 1 ? 's' : ''}
            `;

        } else {

            assignButton.innerHTML = `
                <svg class="w-4 h-4"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M17 20h5V4H2v16h5M9 20v-6h6v6"/>
                </svg>

                Assign Maintenance Team
            `;

        }

    }


    checkboxes.forEach(function (checkbox) {

        checkbox.addEventListener('change', updateSelection);

    });


    updateSelection();

});

</script>

@endif

@endsection
