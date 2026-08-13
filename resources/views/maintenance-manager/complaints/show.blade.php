@extends('maintenance-manager.layouts.app')

@section('title', 'Complaint Details')

@section('content')

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>

                <div class="flex items-center gap-3">

                    <div
                        class="w-12 h-12 rounded-2xl
                    bg-blue-100 text-blue-600
                    flex items-center justify-center">

                        <i class="fas fa-file-circle-exclamation text-xl"></i>

                    </div>

                    <div>

                        <p class="text-sm text-gray-500">
                            Maintenance Management
                        </p>

                        <h1 class="text-2xl font-bold text-gray-900">
                            Complaint {{ $complaint->complaint_no }}
                        </h1>

                        <p class="text-sm text-gray-500 mt-1">
                            Review the verified complaint and assign it to a maintenance technician.
                        </p>

                    </div>

                </div>

            </div>


            <div class="flex flex-wrap gap-3">

                <a href="{{ route('maintenance-manager.complaints.index') }}"
                    class="inline-flex items-center gap-2
                px-4 py-2.5 rounded-xl
                border border-gray-300
                text-gray-700
                hover:bg-gray-50 transition">

                    <i class="fas fa-arrow-left"></i>

                    Back to Complaints

                </a>

            </div>

        </div>



        {{-- ========================================================= --}}
        {{-- STATUS OVERVIEW --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">


                    {{-- STATUS --}}

                    <div class="flex items-center gap-3">

                        <div
                            class="w-11 h-11 rounded-xl
                        bg-blue-100 text-blue-600
                        flex items-center justify-center">

                            <i class="fas fa-circle-info"></i>

                        </div>

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Status

                            </p>

                            <p class="font-semibold text-gray-900 mt-1">

                                {{ $complaint->status }}

                            </p>

                        </div>

                    </div>



                    {{-- PRIORITY --}}

                    <div class="flex items-center gap-3">

                        <div
                            class="w-11 h-11 rounded-xl
                        bg-orange-100 text-orange-600
                        flex items-center justify-center">

                            <i class="fas fa-flag"></i>

                        </div>

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Priority

                            </p>

                            <p
                                class="font-semibold mt-1

                            @if ($complaint->priority === 'Critical') text-red-600
                            @elseif ($complaint->priority === 'High')
                                text-orange-600
                            @elseif ($complaint->priority === 'Medium')
                                text-yellow-600
                            @else
                                text-green-600 @endif">

                                {{ $complaint->priority }}

                            </p>

                        </div>

                    </div>



                    {{-- VERIFICATION --}}

                    <div class="flex items-center gap-3">

                        <div
                            class="w-11 h-11 rounded-xl
                        bg-green-100 text-green-600
                        flex items-center justify-center">

                            <i class="fas fa-circle-check"></i>

                        </div>

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Verification

                            </p>

                            <p class="font-semibold text-gray-900 mt-1">

                                @if ($complaint->verified_at)
                                    Verified
                                @else
                                    Not Verified
                                @endif

                            </p>

                        </div>

                    </div>



                    {{-- REPORTED --}}

                    <div class="flex items-center gap-3">

                        <div
                            class="w-11 h-11 rounded-xl
                        bg-purple-100 text-purple-600
                        flex items-center justify-center">

                            <i class="fas fa-calendar"></i>

                        </div>

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Reported

                            </p>

                            <p class="font-semibold text-gray-900 mt-1">

                                {{ $complaint->created_at?->format('M d, Y') ?? '—' }}

                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </x-form.card>



        {{-- ========================================================= --}}
        {{-- MAIN CONTENT --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">


            {{-- ===================================================== --}}
            {{-- LEFT / MAIN --}}
            {{-- ===================================================== --}}

            <div class="xl:col-span-2 space-y-6">


                {{-- ================================================= --}}
                {{-- COMPLAINT INFORMATION --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="px-6 py-5 border-b border-gray-100">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-xl
                            bg-blue-100 text-blue-600
                            flex items-center justify-center">

                                <i class="fas fa-file-lines"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">

                                    Complaint Information

                                </h3>

                                <p class="text-sm text-gray-500 mt-1">

                                    Complete reported problem information.

                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-6 space-y-6">


                        {{-- SUBJECT --}}

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Complaint Subject

                            </p>

                            <h2 class="text-xl font-bold text-gray-900 mt-1">

                                {{ $complaint->subject }}

                            </h2>

                        </div>



                        {{-- DESCRIPTION --}}

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Problem Description

                            </p>

                            <div
                                class="mt-2 p-5 rounded-xl
                            bg-gray-50 border border-gray-200
                            text-gray-700 whitespace-pre-line
                            leading-relaxed">

                                {{ $complaint->description }}

                            </div>

                        </div>



                        {{-- CATEGORY --}}

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div class="p-4 rounded-xl
                            bg-gray-50 border border-gray-200">

                                <p class="text-xs text-gray-500">

                                    Complaint Category

                                </p>

                                <p class="font-semibold text-gray-900 mt-1">

                                    @if ($complaint->category)
                                        {{ $complaint->category->code }}

                                        —

                                        {{ $complaint->category->name }}
                                    @else
                                        —
                                    @endif

                                </p>

                            </div>


                            <div class="p-4 rounded-xl
                            bg-gray-50 border border-gray-200">

                                <p class="text-xs text-gray-500">

                                    Complaint Number

                                </p>

                                <p class="font-semibold text-gray-900 mt-1">

                                    {{ $complaint->complaint_no }}

                                </p>

                            </div>

                        </div>

                    </div>

                </x-form.card>



                {{-- ================================================= --}}
                {{-- CONSUMER INFORMATION --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="px-6 py-5 border-b border-gray-100">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-xl
                            bg-cyan-100 text-cyan-600
                            flex items-center justify-center">

                                <i class="fas fa-user"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">

                                    Consumer Information

                                </h3>

                                <p class="text-sm text-gray-500 mt-1">

                                    Customer associated with this complaint.

                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-6">

                        @if ($complaint->consumer)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                                <div>

                                    <p class="text-xs text-gray-500">

                                        Consumer Name

                                    </p>

                                    <p class="font-semibold text-gray-900 mt-1">

                                        {{ $complaint->consumer->full_name }}

                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs text-gray-500">

                                        Consumer Number

                                    </p>

                                    <p class="font-semibold text-gray-900 mt-1">

                                        {{ $complaint->consumer->consumer_no ?? '—' }}

                                    </p>

                                </div>

                            </div>
                        @else
                            <div
                                class="p-4 rounded-xl
                            bg-gray-50 border border-gray-200
                            text-gray-500">

                                <i class="fas fa-user-slash mr-2"></i>

                                Walk-in Consumer / No registered consumer record.

                            </div>
                        @endif

                    </div>

                </x-form.card>



                {{-- ================================================= --}}
                {{-- COMPLETE LOCATION --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="px-6 py-5 border-b border-gray-100">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-xl
                            bg-green-100 text-green-600
                            flex items-center justify-center">

                                <i class="fas fa-location-dot"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">

                                    Complete Problem Location

                                </h3>

                                <p class="text-sm text-gray-500 mt-1">

                                    Exact location where maintenance intervention is required.

                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-6 space-y-6">


                        {{-- ADDRESS --}}

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Complete Address

                            </p>

                            <div class="mt-2 p-4 rounded-xl
                            bg-gray-50 border border-gray-200">

                                <div class="flex items-start gap-3">

                                    <i
                                        class="fas fa-location-dot
                                    text-green-600 mt-1"></i>

                                    <p class="font-medium text-gray-900">

                                        {{ $complaint->address ?: 'No address recorded.' }}

                                    </p>

                                </div>

                            </div>

                        </div>



                        {{-- LANDMARK --}}

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Landmark

                            </p>

                            <p class="font-medium text-gray-900 mt-1">

                                {{ $complaint->landmark ?: 'No landmark provided.' }}

                            </p>

                        </div>



                        {{-- COORDINATES --}}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div class="p-4 rounded-xl
                            bg-gray-50 border border-gray-200">

                                <p class="text-xs text-gray-500">

                                    Latitude

                                </p>

                                <p class="font-mono font-medium
                                text-gray-900 mt-1">

                                    {{ $complaint->latitude ?? 'Not recorded' }}

                                </p>

                            </div>


                            <div class="p-4 rounded-xl
                            bg-gray-50 border border-gray-200">

                                <p class="text-xs text-gray-500">

                                    Longitude

                                </p>

                                <p class="font-mono font-medium
                                text-gray-900 mt-1">

                                    {{ $complaint->longitude ?? 'Not recorded' }}

                                </p>

                            </div>

                        </div>



                        {{-- ================================================= --}}
                        {{-- MAP --}}
                        {{-- ================================================= --}}

                        @if ($complaint->latitude !== null && $complaint->longitude !== null)
                            <div>

                                <div
                                    class="flex flex-col sm:flex-row
                                sm:items-center
                                sm:justify-between
                                gap-3 mb-3">

                                    <div>

                                        <p
                                            class="text-sm font-semibold
                                        text-gray-900">

                                            Reported Problem Location

                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">

                                            OpenStreetMap location of the reported complaint.

                                        </p>

                                    </div>


                                    <a href="https://www.google.com/maps?q={{ $complaint->latitude }},{{ $complaint->longitude }}"
                                        target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center
                                    justify-center gap-2
                                    px-3 py-2 rounded-lg
                                    border border-gray-300
                                    text-sm text-gray-700
                                    hover:bg-gray-50 transition">

                                        <i class="fas fa-external-link-alt"></i>

                                        Open External Map

                                    </a>

                                </div>


                                {{-- IMPORTANT: MAP CONTAINER --}}

                                <div id="complaint-map"
                                    class="w-full h-[430px]
                                rounded-2xl
                                border border-gray-300
                                overflow-hidden
                                relative z-0
                                bg-gray-100">
                                </div>

                            </div>
                        @else
                            <div
                                class="p-5 rounded-xl
                            bg-gray-50 border border-gray-200
                            text-gray-500">

                                <i class="fas fa-map-location-dot mr-2"></i>

                                No GPS coordinates were recorded for this complaint.

                            </div>
                        @endif

                    </div>

                </x-form.card>



                {{-- ================================================= --}}
                {{-- PHOTO EVIDENCE --}}
                {{-- ================================================= --}}

                @if ($complaint->photo)
                    <x-form.card>

                        <div class="px-6 py-5 border-b border-gray-100">

                            <div class="flex items-center gap-3">

                                <div
                                    class="w-10 h-10 rounded-xl
                                bg-purple-100 text-purple-600
                                flex items-center justify-center">

                                    <i class="fas fa-camera"></i>

                                </div>

                                <div>

                                    <h3 class="font-semibold text-gray-900">

                                        Photo Evidence

                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">

                                        Photo submitted with the complaint.

                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="p-6">

                            <img src="{{ asset('storage/' . $complaint->photo) }}" alt="Complaint photo"
                                class="w-full max-w-4xl
                            rounded-2xl
                            border border-gray-200
                            shadow-sm">

                        </div>

                    </x-form.card>
                @endif



                {{-- ================================================= --}}
                {{-- CUSTOMER SERVICE VERIFICATION --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="px-6 py-5 border-b border-gray-100">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-xl
                            bg-emerald-100 text-emerald-600
                            flex items-center justify-center">

                                <i class="fas fa-shield"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">

                                    Customer Service Verification

                                </h3>

                                <p class="text-sm text-gray-500 mt-1">

                                    Verification record before maintenance processing.

                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                            {{-- VERIFIED BY --}}

                            <div>

                                <p class="text-xs text-gray-500">

                                    Verified By

                                </p>

                                <p class="font-semibold text-gray-900 mt-1">

                                    @if ($complaint->verifier)
                                        {{ $complaint->verifier->first_name }}
                                        {{ $complaint->verifier->last_name }}
                                    @else
                                        —
                                    @endif

                                </p>

                            </div>



                            {{-- VERIFIED AT --}}

                            <div>

                                <p class="text-xs text-gray-500">

                                    Verified At

                                </p>

                                <p class="font-semibold text-gray-900 mt-1">

                                    {{ $complaint->verified_at?->format('M d, Y h:i A') ?? 'Not yet verified' }}

                                </p>

                            </div>

                        </div>



                        {{-- VERIFICATION REASON --}}

                        @if ($complaint->verification_reason)
                            <div class="mt-5">

                                <p class="text-xs text-gray-500">

                                    Verification Notes

                                </p>

                                <div
                                    class="mt-2 p-4 rounded-xl
                                bg-gray-50 border border-gray-200
                                text-gray-700 whitespace-pre-line">

                                    {{ $complaint->verification_reason }}

                                </div>

                            </div>
                        @endif

                    </div>

                </x-form.card>

            </div>



            {{-- ===================================================== --}}
            {{-- RIGHT SIDEBAR --}}
            {{-- ===================================================== --}}

            <div class="space-y-6">


                {{-- ================================================= --}}
                {{-- TECHNICIAN ASSIGNMENT --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="px-5 py-5 border-b border-gray-100">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-xl
                            bg-indigo-100 text-indigo-600
                            flex items-center justify-center">

                                <i class="fas fa-wrench"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">

                                    Technician Assignment

                                </h3>

                                <p class="text-sm text-gray-500 mt-1">

                                    Assign maintenance personnel.

                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5">

                        @if ($complaint->status === 'Verified')


                            <form method="POST"
                                action="{{ route('maintenance-manager.complaints.assign', $complaint) }}"
                                class="space-y-5">

                                @csrf


                                {{-- TECHNICIAN --}}

                                <div>

                                    <label for="technician_id"
                                        class="block text-sm font-semibold
                                    text-gray-700 mb-2">

                                        Maintenance Technician

                                    </label>


                                    <select id="technician_id" name="technician_id" required
                                        class="w-full rounded-xl
                                    border-gray-300
                                    focus:border-indigo-500
                                    focus:ring-indigo-500">

                                        <option value="">
                                            Select technician
                                        </option>


                                        @foreach ($technicians as $technician)
                                            <option value="{{ $technician->id }}">

                                                {{ $technician->first_name }}
                                                {{ $technician->last_name }}

                                                @if ($technician->employee_no)
                                                    — {{ $technician->employee_no }}
                                                @endif

                                            </option>
                                        @endforeach

                                    </select>


                                    @error('technician_id')
                                        <p class="text-sm text-red-600 mt-2">

                                            {{ $message }}

                                        </p>
                                    @enderror

                                </div>



                                {{-- WARNING --}}

                                <div
                                    class="rounded-xl
                                bg-blue-50
                                border border-blue-100
                                p-4">

                                    <div class="flex gap-3">

                                        <i
                                            class="fas fa-circle-info
                                        text-blue-600 mt-0.5"></i>

                                        <p class="text-sm text-blue-700">

                                            Assigning this complaint will change
                                            its status from
                                            <strong>Verified</strong>
                                            to
                                            <strong>Assigned</strong>.

                                        </p>

                                    </div>

                                </div>



                                {{-- ASSIGN BUTTON --}}

                                <button type="submit"
                                    onclick="return confirm(
                                    'Assign this complaint to the selected technician?'
                                )"
                                    class="w-full inline-flex
                                items-center justify-center
                                gap-2
                                px-4 py-3
                                rounded-xl
                                bg-indigo-600
                                text-white
                                font-medium
                                hover:bg-indigo-700
                                transition">

                                    <i class="fas fa-user-check"></i>

                                    Assign Technician

                                </button>

                            </form>
                        @elseif ($complaint->status === 'Assigned')
                            {{-- ALREADY ASSIGNED --}}

                            <div class="space-y-4">


                                <div
                                    class="rounded-xl
                                bg-green-50
                                border border-green-100
                                p-4">

                                    <div class="flex gap-3">

                                        <div
                                            class="w-9 h-9 rounded-lg
                                        bg-green-100
                                        text-green-600
                                        flex items-center
                                        justify-center
                                        shrink-0">

                                            <i class="fas fa-user-check"></i>

                                        </div>

                                        <div>

                                            <p
                                                class="font-semibold
                                            text-green-900">

                                                Technician Assigned

                                            </p>

                                            <p class="text-sm text-green-700 mt-1">

                                                This complaint has already
                                                been assigned.

                                            </p>

                                        </div>

                                    </div>

                                </div>



                                @if ($complaint->technician)

                                    <div class="border border-gray-200
                                    rounded-xl p-4">

                                        <p
                                            class="text-xs uppercase
                                        tracking-wide text-gray-500">

                                            Assigned Technician

                                        </p>


                                        <div class="flex items-center
                                        gap-3 mt-3">

                                            <div
                                                class="w-11 h-11 rounded-full
                                            bg-indigo-100
                                            text-indigo-600
                                            flex items-center
                                            justify-center">

                                                <i class="fas fa-user-wrench"></i>

                                            </div>


                                            <div>

                                                <p
                                                    class="font-semibold
                                                text-gray-900">

                                                    {{ $complaint->technician->first_name }}
                                                    {{ $complaint->technician->last_name }}

                                                </p>


                                                <p
                                                    class="text-xs
                                                text-gray-500">

                                                    Maintenance Technician

                                                </p>


                                                @if ($complaint->technician->employee_no)
                                                    <p
                                                        class="text-xs
                                                    text-gray-500 mt-1">

                                                        Employee No:
                                                        {{ $complaint->technician->employee_no }}

                                                    </p>
                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                @endif

                            </div>
                        @else
                            <div
                                class="p-4 rounded-xl
                            bg-gray-50
                            border border-gray-200">

                                <p class="text-sm text-gray-600">

                                    <i class="fas fa-circle-info mr-2"></i>

                                    Technician assignment is unavailable
                                    for the current complaint status.

                                </p>

                            </div>

                        @endif

                    </div>

                </x-form.card>



                {{-- ================================================= --}}
                {{-- CUSTOMER SERVICE --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="px-5 py-5 border-b border-gray-100">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-xl
                            bg-cyan-100 text-cyan-600
                            flex items-center justify-center">

                                <i class="fas fa-headset"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">

                                    Customer Service

                                </h3>

                                <p class="text-sm text-gray-500 mt-1">

                                    Complaint intake personnel.

                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5">

                        @if ($complaint->customerService)
                            <div class="flex items-center gap-3">

                                <div
                                    class="w-11 h-11 rounded-full
                                bg-cyan-100
                                text-cyan-600
                                flex items-center
                                justify-center">

                                    <i class="fas fa-headset"></i>

                                </div>


                                <div>

                                    <p class="font-semibold text-gray-900">

                                        {{ $complaint->customerService->first_name }}
                                        {{ $complaint->customerService->last_name }}

                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">

                                        Customer Service Representative

                                    </p>

                                </div>

                            </div>
                        @else
                            <p class="text-gray-400">

                                No Customer Service representative recorded.

                            </p>
                        @endif

                    </div>

                </x-form.card>



                {{-- ================================================= --}}
                {{-- TIMELINE --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="px-5 py-5 border-b border-gray-100">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-xl
                            bg-gray-100
                            text-gray-600
                            flex items-center
                            justify-center">

                                <i class="fas fa-clock-rotate-left"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">

                                    Complaint Timeline

                                </h3>

                                <p class="text-sm text-gray-500 mt-1">

                                    Important complaint events.

                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5 space-y-5">


                        {{-- CREATED --}}

                        <div class="flex gap-3">

                            <div
                                class="w-8 h-8 rounded-full
                            bg-gray-100
                            text-gray-600
                            flex items-center
                            justify-center
                            shrink-0">

                                <i class="fas fa-plus text-xs"></i>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-gray-900">

                                    Complaint Created

                                </p>

                                <p class="text-xs text-gray-500 mt-1">

                                    {{ $complaint->created_at?->format('M d, Y h:i A') ?? '—' }}

                                </p>

                            </div>

                        </div>



                        {{-- VERIFIED --}}

                        <div class="flex gap-3">

                            <div
                                class="w-8 h-8 rounded-full
                            @if ($complaint->verified_at) bg-green-100 text-green-600
                            @else
                                bg-gray-100 text-gray-400 @endif
                            flex items-center
                            justify-center
                            shrink-0">

                                <i class="fas fa-check text-xs"></i>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-gray-900">

                                    Complaint Verified

                                </p>

                                <p class="text-xs text-gray-500 mt-1">

                                    {{ $complaint->verified_at?->format('M d, Y h:i A') ?? 'Not yet verified' }}

                                </p>

                            </div>

                        </div>



                        {{-- ASSIGNED --}}

                        <div class="flex gap-3">

                            <div
                                class="w-8 h-8 rounded-full
                            @if ($complaint->assigned_to) bg-indigo-100 text-indigo-600
                            @else
                                bg-gray-100 text-gray-400 @endif
                            flex items-center
                            justify-center
                            shrink-0">

                                <i class="fas fa-user-check text-xs"></i>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-gray-900">

                                    Technician Assignment

                                </p>

                                <p class="text-xs text-gray-500 mt-1">

                                    @if ($complaint->assigned_to)
                                        Technician assigned
                                    @else
                                        Waiting for technician assignment
                                    @endif

                                </p>

                            </div>

                        </div>



                        {{-- COMPLETED --}}

                        <div class="flex gap-3">

                            <div
                                class="w-8 h-8 rounded-full
                            @if ($complaint->completed_at) bg-green-100 text-green-600
                            @else
                                bg-gray-100 text-gray-400 @endif
                            flex items-center
                            justify-center
                            shrink-0">

                                <i class="fas fa-flag-checkered text-xs"></i>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-gray-900">

                                    Completed

                                </p>

                                <p class="text-xs text-gray-500 mt-1">

                                    {{ $complaint->completed_at?->format('M d, Y h:i A') ?? 'Not yet completed' }}

                                </p>

                            </div>

                        </div>

                    </div>

                </x-form.card>



                {{-- ================================================= --}}
                {{-- DATABASE INFORMATION --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="p-5">

                        <div class="grid grid-cols-2 gap-4">

                            <div>

                                <p class="text-xs text-gray-500">

                                    Complaint ID

                                </p>

                                <p class="font-mono font-semibold
                                text-gray-900 mt-1">

                                    #{{ $complaint->id }}

                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-500">

                                    Last Updated

                                </p>

                                <p class="font-medium text-gray-900 mt-1">

                                    {{ $complaint->updated_at?->format('M d, Y h:i A') ?? '—' }}

                                </p>

                            </div>

                        </div>

                    </div>

                </x-form.card>

            </div>

        </div>

    </div>



    {{-- ============================================================= --}}
    {{-- LEAFLET / OPENSTREETMAP --}}
    {{-- ============================================================= --}}

    @if ($complaint->latitude !== null && $complaint->longitude !== null)
        @push('styles')
            {{-- Leaflet CSS --}}

            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

            <style>
                #complaint-map {
                    min-height: 430px;
                    width: 100%;
                    z-index: 0;
                }

                #complaint-map .leaflet-container {
                    font-family: inherit;
                }
            </style>
        @endpush


        @push('scripts')
            {{-- Leaflet JS --}}

            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


            <script>
                document.addEventListener(
                    'DOMContentLoaded',
                    function() {

                        const mapElement =
                            document.getElementById('complaint-map');


                        if (!mapElement) {

                            console.warn(
                                'Complaint map element not found.'
                            );

                            return;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | COORDINATES
                        |--------------------------------------------------------------------------
                        */

                        const latitude =
                            Number(@json((float) $complaint->latitude));

                        const longitude =
                            Number(@json((float) $complaint->longitude));


                        /*
                        |--------------------------------------------------------------------------
                        | VALIDATE COORDINATES
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !Number.isFinite(latitude) ||
                            !Number.isFinite(longitude)
                        ) {

                            console.error(
                                'Invalid complaint coordinates:',
                                latitude,
                                longitude
                            );

                            return;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | CHECK LEAFLET
                        |--------------------------------------------------------------------------
                        */

                        if (typeof L === 'undefined') {

                            console.error(
                                'Leaflet failed to load.'
                            );

                            return;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | CREATE MAP
                        |--------------------------------------------------------------------------
                        */

                        const map =
                            L.map(
                                'complaint-map', {
                                    zoomControl: true,
                                    attributionControl: true
                                }
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | OPENSTREETMAP TILE LAYER
                        |--------------------------------------------------------------------------
                        */

                        L.tileLayer(
                            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,

                                attribution: '&copy; OpenStreetMap contributors'
                            }
                        ).addTo(map);


                        /*
                        |--------------------------------------------------------------------------
                        | CENTER MAP
                        |--------------------------------------------------------------------------
                        */

                        map.setView(
                            [
                                latitude,
                                longitude
                            ],
                            17
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | MARKER
                        |--------------------------------------------------------------------------
                        */

                        const marker =
                            L.marker(
                                [
                                    latitude,
                                    longitude
                                ]
                            ).addTo(map);


                        /*
                        |--------------------------------------------------------------------------
                        | POPUP
                        |--------------------------------------------------------------------------
                        */

                        const complaintNumber =
                            @json($complaint->complaint_no);

                        const subject =
                            @json($complaint->subject);

                        const address =
                            @json($complaint->address ?? '');

                        const landmark =
                            @json($complaint->landmark ?? '');


                        marker.bindPopup(`

                        <div style="min-width:220px">

                            <div
                                style="
                                    font-weight:600;
                                    color:#111827;
                                    margin-bottom:6px;
                                "
                            >

                                <i
                                    class="fas fa-file-circle-exclamation"
                                    style="color:#2563eb"
                                ></i>

                                ${complaintNumber}

                            </div>


                            <div
                                style="
                                    font-weight:500;
                                    color:#1f2937;
                                    margin-bottom:5px;
                                "
                            >

                                ${subject}

                            </div>


                            <div
                                style="
                                    color:#4b5563;
                                    font-size:13px;
                                    margin-bottom:4px;
                                "
                            >

                                <strong>Address:</strong>
                                ${address}

                            </div>


                            ${
                                landmark
                                    ? `
                                                    <div
                                                        style="
                                                            color:#6b7280;
                                                            font-size:13px;
                                                            margin-bottom:4px;
                                                        "
                                                    >

                                                        <strong>Landmark:</strong>
                                                        ${landmark}

                                                    </div>
                                                  `
                                    : ''
                            }


                            <div
                                style="
                                    color:#9ca3af;
                                    font-size:12px;
                                "
                            >

                                ${latitude},
                                ${longitude}

                            </div>

                        </div>

                    `).openPopup();


                        /*
                        |--------------------------------------------------------------------------
                        | FIX MAP SIZE
                        |--------------------------------------------------------------------------
                        */

                        function refreshMap() {

                            map.invalidateSize({
                                animate: false,
                                pan: false
                            });

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | INITIAL REFRESH
                        |--------------------------------------------------------------------------
                        */

                        requestAnimationFrame(
                            function() {

                                refreshMap();

                                setTimeout(
                                    refreshMap,
                                    100
                                );

                                setTimeout(
                                    refreshMap,
                                    300
                                );

                                setTimeout(
                                    refreshMap,
                                    600
                                );

                                setTimeout(
                                    refreshMap,
                                    1000
                                );

                            }
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | WINDOW RESIZE
                        |--------------------------------------------------------------------------
                        */

                        window.addEventListener(
                            'resize',
                            refreshMap
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | TAB / VISIBILITY CHANGE
                        |--------------------------------------------------------------------------
                        */

                        document.addEventListener(
                            'visibilitychange',
                            function() {

                                if (
                                    document.visibilityState ===
                                    'visible'
                                ) {

                                    setTimeout(
                                        refreshMap,
                                        200
                                    );

                                }

                            }
                        );

                    }
                );
            </script>
        @endpush
    @endif

@endsection
