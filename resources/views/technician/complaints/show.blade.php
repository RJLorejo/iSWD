@extends('technician.layouts.app')

@section('title', 'Maintenance Complaint')

@section('content')

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col lg:flex-row
                lg:items-center lg:justify-between gap-4">

            <div>

                <div class="flex items-center gap-2 text-sm text-gray-500">

                    <a href="{{ route('technician.complaints.index') }}" class="hover:text-indigo-600">

                        Maintenance Complaints

                    </a>

                    <i class="fas fa-chevron-right text-xs"></i>

                    <span>
                        {{ $complaint->complaint_no }}
                    </span>

                </div>

                <p class="text-sm font-medium text-indigo-600 mt-3">
                    Maintenance Work
                </p>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">
                    {{ $complaint->complaint_no }}
                </h1>

                <p class="text-sm text-gray-500 mt-2">
                    Review the complaint, inspect the location, and manage maintenance progress.
                </p>

            </div>


            <a href="{{ route('technician.complaints.index') }}"
                class="inline-flex items-center justify-center
                  gap-2 px-4 py-2.5 rounded-xl
                  border border-gray-300
                  text-gray-700
                  hover:bg-gray-50 transition">

                <i class="fas fa-arrow-left"></i>

                Back to Complaints

            </a>

        </div>


        {{-- ========================================================= --}}
        {{-- FLASH MESSAGES --}}
        {{-- ========================================================= --}}

        @if (session('success'))
            <div
                class="flex items-start gap-3 p-4 rounded-xl
                    bg-green-50 border border-green-200
                    text-green-800">

                <i class="fas fa-circle-check mt-0.5"></i>

                <div>

                    <p class="font-semibold">
                        Success
                    </p>

                    <p class="text-sm mt-0.5">
                        {{ session('success') }}
                    </p>

                </div>

            </div>
        @endif


        @if (session('error'))
            <div
                class="flex items-start gap-3 p-4 rounded-xl
                    bg-red-50 border border-red-200
                    text-red-800">

                <i class="fas fa-circle-exclamation mt-0.5"></i>

                <div>

                    <p class="font-semibold">
                        Unable to update complaint
                    </p>

                    <p class="text-sm mt-0.5">
                        {{ session('error') }}
                    </p>

                </div>

            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- STATUS / WORK CONTROL --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div class="p-5 sm:p-6">

                <div class="flex flex-col lg:flex-row
                        lg:items-center lg:justify-between gap-5">

                    {{-- Status --}}
                    <div class="flex items-center gap-4">

                        <div
                            class="w-14 h-14 rounded-2xl
                                flex items-center justify-center
                                @if ($complaint->status === 'Assigned') bg-blue-100 text-blue-600
                                @elseif($complaint->status === 'In Progress')
                                    bg-yellow-100 text-yellow-600
                                @elseif($complaint->status === 'Completed')
                                    bg-green-100 text-green-600
                                @else
                                    bg-gray-100 text-gray-600 @endif">

                            @if ($complaint->status === 'Assigned')
                                <i class="fas fa-clipboard-list text-xl"></i>
                            @elseif($complaint->status === 'In Progress')
                                <i class="fas fa-screwdriver-wrench text-xl"></i>
                            @elseif($complaint->status === 'Completed')
                                <i class="fas fa-circle-check text-xl"></i>
                            @else
                                <i class="fas fa-circle-info text-xl"></i>
                            @endif

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Current Status
                            </p>

                            <div class="flex flex-wrap items-center gap-2 mt-1">

                                <h2 class="text-xl font-bold text-gray-900">
                                    {{ $complaint->status }}
                                </h2>

                                @if ($complaint->priority === 'Critical')
                                    <span
                                        class="inline-flex items-center gap-1
                                             px-2.5 py-1 rounded-full
                                             bg-red-100 text-red-700
                                             text-xs font-semibold">

                                        <i class="fas fa-triangle-exclamation"></i>

                                        Critical

                                    </span>
                                @elseif($complaint->priority === 'High')
                                    <span
                                        class="inline-flex items-center
                                             px-2.5 py-1 rounded-full
                                             bg-orange-100 text-orange-700
                                             text-xs font-semibold">

                                        High

                                    </span>
                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- Action --}}
                    <div>

                        @if ($complaint->status === 'Assigned')
                            <form method="POST" action="{{ route('technician.complaints.start', $complaint) }}"
                                onsubmit="return confirm('Start maintenance work on this complaint?');">

                                @csrf

                                <button type="submit"
                                    class="inline-flex items-center gap-2
                                           px-5 py-3 rounded-xl
                                           bg-indigo-600 text-white
                                           font-medium
                                           hover:bg-indigo-700
                                           transition shadow-sm">

                                    <i class="fas fa-play"></i>

                                    Start Maintenance

                                </button>

                            </form>
                        @elseif($complaint->status === 'In Progress')
                            <form method="POST" action="{{ route('technician.complaints.complete', $complaint) }}"
                                onsubmit="return confirm('Are you sure this maintenance work is complete?');">

                                @csrf

                                <button type="submit"
                                    class="inline-flex items-center gap-2
                                           px-5 py-3 rounded-xl
                                           bg-green-600 text-white
                                           font-medium
                                           hover:bg-green-700
                                           transition shadow-sm">

                                    <i class="fas fa-circle-check"></i>

                                    Complete Maintenance

                                </button>

                            </form>
                        @elseif($complaint->status === 'Completed')
                            <div
                                class="inline-flex items-center gap-2
                                    px-5 py-3 rounded-xl
                                    bg-green-50 text-green-700
                                    border border-green-200">

                                <i class="fas fa-circle-check"></i>

                                Maintenance Completed

                            </div>
                        @endif

                    </div>

                </div>

            </div>

        </x-form.card>


        {{-- ========================================================= --}}
        {{-- MAIN GRID --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">


            {{-- ===================================================== --}}
            {{-- LEFT / MAIN --}}
            {{-- ===================================================== --}}

            <div class="xl:col-span-2 space-y-6">


                {{-- ================================================= --}}
                {{-- COMPLAINT DETAILS --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="px-5 sm:px-6 py-5
                            border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-file-lines
                                  text-indigo-600 mr-2"></i>

                            Complaint Details

                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Information submitted by the consumer.
                        </p>

                    </div>


                    <div class="p-5 sm:p-6 space-y-6">

                        {{-- Subject --}}
                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  text-gray-500 font-semibold">

                                Subject

                            </p>

                            <h3 class="text-xl font-bold
                                   text-gray-900 mt-1">

                                {{ $complaint->subject }}

                            </h3>

                        </div>


                        {{-- Description --}}
                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  text-gray-500 font-semibold">

                                Description

                            </p>

                            <div
                                class="mt-2 p-4 rounded-xl
                                    bg-gray-50 border border-gray-100">

                                <p class="text-gray-700 leading-relaxed whitespace-pre-line">

                                    {{ $complaint->description }}

                                </p>

                            </div>

                        </div>


                        {{-- Category / Priority --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold">

                                    Category

                                </p>

                                <div class="flex items-center gap-2 mt-2">

                                    <div
                                        class="w-9 h-9 rounded-lg
                                            bg-indigo-50 text-indigo-600
                                            flex items-center justify-center">

                                        <i class="fas fa-layer-group"></i>

                                    </div>

                                    <p class="font-semibold text-gray-900">

                                        {{ $complaint->category?->name ?? 'Uncategorized' }}

                                    </p>

                                </div>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold">

                                    Priority

                                </p>

                                @php

                                    $priorityClasses = match ($complaint->priority) {
                                        'Critical' => 'bg-red-100 text-red-700',

                                        'High' => 'bg-orange-100 text-orange-700',

                                        'Medium' => 'bg-yellow-100 text-yellow-700',

                                        default => 'bg-green-100 text-green-700',
                                    };

                                @endphp

                                <span
                                    class="inline-flex items-center gap-2
                                         px-3 py-1.5 rounded-full
                                         text-sm font-semibold
                                         {{ $priorityClasses }} mt-2">

                                    @if ($complaint->priority === 'Critical')
                                        <i class="fas fa-triangle-exclamation"></i>
                                    @endif

                                    {{ $complaint->priority }}

                                </span>

                            </div>

                        </div>


                        {{-- Complaint Photo --}}
                        @if ($complaint->photo)
                            <div class="border-t border-gray-100 pt-6">

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold mb-3">

                                    Complaint Photo

                                </p>

                                <div
                                    class="rounded-2xl overflow-hidden
                                        border border-gray-200 bg-gray-50">

                                    <img src="{{ asset('storage/' . $complaint->photo) }}" alt="Complaint photo"
                                        class="w-full max-h-[500px] object-contain">

                                </div>

                            </div>
                        @endif

                    </div>

                </x-form.card>


                {{-- ================================================= --}}
                {{-- LOCATION + MAP --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="px-5 sm:px-6 py-5
                            border-b border-gray-100">

                        <div
                            class="flex flex-col sm:flex-row
                                sm:items-center sm:justify-between gap-3">

                            <div>

                                <h2 class="font-semibold text-gray-900">

                                    <i
                                        class="fas fa-map-location-dot
                                          text-indigo-600 mr-2"></i>

                                    Service Location

                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Complaint location and navigation information.
                                </p>

                            </div>


                            @if ($complaint->latitude && $complaint->longitude)
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $complaint->latitude }},{{ $complaint->longitude }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center
                                       gap-2 px-4 py-2.5 rounded-xl
                                       bg-indigo-600 text-white
                                       hover:bg-indigo-700 transition">

                                    <i class="fas fa-route"></i>

                                    Get Directions

                                </a>
                            @endif

                        </div>

                    </div>


                    <div class="p-5 sm:p-6 space-y-5">

                        {{-- Address --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold">

                                    Address

                                </p>

                                <div class="flex items-start gap-3 mt-2">

                                    <div
                                        class="w-9 h-9 shrink-0 rounded-lg
                                            bg-gray-100 text-gray-500
                                            flex items-center justify-center">

                                        <i class="fas fa-location-dot"></i>

                                    </div>

                                    <p class="text-gray-700 leading-relaxed">

                                        {{ $complaint->address }}

                                    </p>

                                </div>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold">

                                    Landmark

                                </p>

                                <div class="flex items-start gap-3 mt-2">

                                    <div
                                        class="w-9 h-9 shrink-0 rounded-lg
                                            bg-gray-100 text-gray-500
                                            flex items-center justify-center">

                                        <i class="fas fa-landmark"></i>

                                    </div>

                                    <p class="text-gray-700">

                                        {{ $complaint->landmark ?? 'No landmark provided' }}

                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- Map --}}
                        @if ($complaint->latitude && $complaint->longitude)
                            <div>

                                <div id="complaint-map"
                                    class="w-full h-[380px] sm:h-[450px]
                                        rounded-2xl overflow-hidden
                                        border border-gray-200 z-0">
                                </div>

                            </div>


                            {{-- Coordinates --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                                <div
                                    class="p-3 rounded-xl bg-gray-50
                                        border border-gray-100">

                                    <p class="text-xs text-gray-500">
                                        Latitude
                                    </p>

                                    <p
                                        class="font-mono text-sm font-semibold
                                          text-gray-900 mt-1">

                                        {{ $complaint->latitude }}

                                    </p>

                                </div>


                                <div
                                    class="p-3 rounded-xl bg-gray-50
                                        border border-gray-100">

                                    <p class="text-xs text-gray-500">
                                        Longitude
                                    </p>

                                    <p
                                        class="font-mono text-sm font-semibold
                                          text-gray-900 mt-1">

                                        {{ $complaint->longitude }}

                                    </p>

                                </div>

                            </div>
                        @else
                            <div
                                class="rounded-2xl border border-yellow-200
                                    bg-yellow-50 p-5">

                                <div class="flex items-start gap-3">

                                    <i
                                        class="fas fa-map-location-dot
                                          text-yellow-600 mt-0.5"></i>

                                    <div>

                                        <p class="font-semibold text-yellow-900">
                                            Map location unavailable
                                        </p>

                                        <p class="text-sm text-yellow-800 mt-1">

                                            This complaint does not have latitude
                                            and longitude coordinates. Use the
                                            address and landmark to locate the
                                            service request.

                                        </p>

                                    </div>

                                </div>

                            </div>
                        @endif

                    </div>

                </x-form.card>


                {{-- ================================================= --}}
                {{-- VERIFICATION --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="px-5 sm:px-6 py-5
                            border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-shield-check
                                  text-green-600 mr-2"></i>

                            Verification Information

                        </h2>

                    </div>


                    <div class="p-5 sm:p-6">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                            {{-- Verified By --}}
                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold">

                                    Verified By

                                </p>

                                <div class="flex items-center gap-3 mt-2">

                                    <div
                                        class="w-10 h-10 rounded-full
                                            bg-green-100 text-green-600
                                            flex items-center justify-center">

                                        <i class="fas fa-user-check"></i>

                                    </div>

                                    <div>

                                        <p class="font-semibold text-gray-900">

                                            {{ $complaint->verifier?->last_name ??  'Not verified' }}

                                        </p>

                                        @if ($complaint->verified_at)
                                            <p class="text-xs text-gray-500 mt-0.5">

                                                {{ $complaint->verified_at->format('M d, Y h:i A') }}

                                            </p>
                                        @endif

                                    </div>

                                </div>

                            </div>


                            {{-- Verification Status --}}
                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold">

                                    Verification Status

                                </p>

                                @if ($complaint->verified_at)
                                    <span
                                        class="inline-flex items-center gap-2
                                             px-3 py-1.5 rounded-full
                                             bg-green-100 text-green-700
                                             text-sm font-semibold mt-2">

                                        <i class="fas fa-circle-check"></i>

                                        Verified

                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-2
                                             px-3 py-1.5 rounded-full
                                             bg-gray-100 text-gray-600
                                             text-sm font-semibold mt-2">

                                        <i class="fas fa-clock"></i>

                                        Not Verified

                                    </span>
                                @endif

                            </div>

                        </div>


                        {{-- Reason --}}
                        @if ($complaint->verification_reason)
                            <div class="mt-6 pt-5 border-t border-gray-100">

                                <p
                                    class="text-xs uppercase tracking-wide
                                      text-gray-500 font-semibold">

                                    Verification Notes

                                </p>

                                <div
                                    class="mt-2 p-4 rounded-xl
                                        bg-gray-50 border border-gray-100">

                                    <p class="text-gray-700 whitespace-pre-line">

                                        {{ $complaint->verification_reason }}

                                    </p>

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
                {{-- CONSUMER --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="px-5 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-user
                                  text-indigo-600 mr-2"></i>

                            Consumer Information

                        </h2>

                    </div>


                    <div class="p-5">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-12 h-12 rounded-full
                                    bg-indigo-100 text-indigo-600
                                    flex items-center justify-center">

                                <i class="fas fa-user text-lg"></i>

                            </div>

                            <div class="min-w-0">

                                <p class="font-semibold text-gray-900">

                                    {{ $complaint->consumer?->full_name ?? 'Walk-in Consumer' }}

                                </p>

                                <p class="text-xs text-gray-500 mt-1">

                                    {{ $complaint->consumer?->consumer_no ?? 'No consumer number' }}

                                </p>

                            </div>

                        </div>

                    </div>

                </x-form.card>


                {{-- ================================================= --}}
                {{-- WORK INFORMATION --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="px-5 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-clipboard-check
                                  text-indigo-600 mr-2"></i>

                            Work Information

                        </h2>

                    </div>


                    <div class="p-5 space-y-5">

                        {{-- Technician --}}
                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  text-gray-500 font-semibold">

                                Assigned Technician

                            </p>

                            <div class="flex items-center gap-2 mt-2">

                                <div
                                    class="w-8 h-8 rounded-lg
                                        bg-indigo-50 text-indigo-600
                                        flex items-center justify-center">

                                    <i class="fas fa-user-gear text-sm"></i>

                                </div>

                                <p class="font-semibold text-gray-900">

                                    {{ $complaint->technician?->last_name ?? '—' }}, {{ $complaint->technician?->first_name ?? '—' }}

                                </p>

                            </div>

                        </div>


                        {{-- Customer Service --}}
                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  text-gray-500 font-semibold">

                                Customer Service

                            </p>

                            <p class="font-medium text-gray-900 mt-1">

                                {{ $complaint->customerService?->last_name ?? '—' }} , {{ $complaint->customerService?->first_name ?? '—' }}

                            </p>

                        </div>


                        {{-- Created --}}
                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  text-gray-500 font-semibold">

                                Complaint Submitted

                            </p>

                            <p class="font-medium text-gray-900 mt-1">

                                {{ $complaint->created_at?->format('M d, Y h:i A') }}

                            </p>

                        </div>


                        {{-- Updated --}}
                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  text-gray-500 font-semibold">

                                Last Updated

                            </p>

                            <p class="font-medium text-gray-900 mt-1">

                                {{ $complaint->updated_at?->format('M d, Y h:i A') }}

                            </p>

                        </div>


                        {{-- Completed --}}
                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  text-gray-500 font-semibold">

                                Completed At

                            </p>

                            <p
                                class="font-medium mt-1
                            {{ $complaint->completed_at ? 'text-green-700' : 'text-gray-500' }}">

                                {{ $complaint->completed_at ? $complaint->completed_at->format('M d, Y h:i A') : 'Not completed' }}

                            </p>

                        </div>

                    </div>

                </x-form.card>


                {{-- ================================================= --}}
                {{-- WORK TIMELINE --}}
                {{-- ================================================= --}}

                <x-form.card>

                    <div class="px-5 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-timeline
                                  text-indigo-600 mr-2"></i>

                            Work Timeline

                        </h2>

                    </div>


                    <div class="p-5">

                        <div class="relative">

                            {{-- Vertical line --}}
                            <div
                                class="absolute left-4 top-3 bottom-3
                                    w-px bg-gray-200">
                            </div>


                            <div class="space-y-6">

                                {{-- Submitted --}}
                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                            bg-gray-100 text-gray-500
                                            flex items-center justify-center">

                                        <i class="fas fa-file-circle-plus text-xs"></i>

                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            Complaint Submitted
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">

                                            {{ $complaint->created_at?->format('M d, Y h:i A') }}

                                        </p>

                                    </div>

                                </div>


                                {{-- Verified --}}
                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                    {{ $complaint->verified_at ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                    flex items-center justify-center">

                                        <i class="fas fa-shield-check text-xs"></i>

                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            Complaint Verified
                                        </p>

                                        @if ($complaint->verified_at)
                                            <p class="text-xs text-green-600 mt-1">
                                                {{ $complaint->verified_at->format('M d, Y h:i A') }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Verification date unavailable
                                            </p>
                                        @endif

                                    </div>

                                </div>


                                {{-- Current --}}
                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                    @if ($complaint->status === 'Completed') bg-green-100 text-green-600
                                    @elseif($complaint->status === 'In Progress')
                                        bg-yellow-100 text-yellow-600
                                    @else
                                        bg-blue-100 text-blue-600 @endif
                                    flex items-center justify-center">

                                        <i class="fas fa-screwdriver-wrench text-xs"></i>

                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">

                                            {{ $complaint->status }}

                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">

                                            Current maintenance status

                                        </p>

                                    </div>

                                </div>


                                {{-- Completed --}}
                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                    {{ $complaint->completed_at ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                    flex items-center justify-center">

                                        <i class="fas fa-circle-check text-xs"></i>

                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            Maintenance Completed
                                        </p>

                                        @if ($complaint->completed_at)
                                            <p class="text-xs text-green-600 mt-1">

                                                {{ $complaint->completed_at->format('M d, Y h:i A') }}

                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Not completed yet
                                            </p>
                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </x-form.card>


                {{-- ================================================= --}}
                {{-- NAVIGATION CARD --}}
                {{-- ================================================= --}}

                @if ($complaint->latitude && $complaint->longitude)
                    <x-form.card>

                        <div class="p-5">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-10 h-10 shrink-0 rounded-xl
                                        bg-indigo-100 text-indigo-600
                                        flex items-center justify-center">

                                    <i class="fas fa-route"></i>

                                </div>

                                <div>

                                    <p class="font-semibold text-gray-900">
                                        Need directions?
                                    </p>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Open the complaint location in Google Maps.
                                    </p>

                                </div>

                            </div>


                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $complaint->latitude }},{{ $complaint->longitude }}"
                                target="_blank" rel="noopener noreferrer"
                                class="mt-4 w-full inline-flex items-center
                                   justify-center gap-2
                                   px-4 py-2.5 rounded-xl
                                   border border-indigo-200
                                   bg-indigo-50 text-indigo-700
                                   hover:bg-indigo-100 transition">

                                <i class="fas fa-map-marked-alt"></i>

                                Open Navigation

                            </a>

                        </div>

                    </x-form.card>
                @endif

            </div>

        </div>

    </div>


    {{-- ============================================================= --}}
    {{-- LEAFLET MAP --}}
    {{-- ============================================================= --}}

    @if ($complaint->latitude && $complaint->longitude)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>


        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const latitude = {{ (float) $complaint->latitude }};
                const longitude = {{ (float) $complaint->longitude }};

                const mapElement = document.getElementById('complaint-map');

                if (!mapElement) {
                    return;
                }

                const map = L.map('complaint-map', {
                    scrollWheelZoom: false
                }).setView(
                    [latitude, longitude],
                    16
                );


                /*
                |--------------------------------------------------------------------------
                | OpenStreetMap Tiles
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
                | Marker
                |--------------------------------------------------------------------------
                */

                const marker = L.marker([
                    latitude,
                    longitude
                ]).addTo(map);


                /*
                |--------------------------------------------------------------------------
                | Popup
                |--------------------------------------------------------------------------
                */

                marker.bindPopup(`
                <div style="min-width:220px">

                    <strong style="font-size:14px">
                        {{ $complaint->complaint_no }}
                    </strong>

                    <br>

                    <span style="font-size:13px">
                        {{ addslashes($complaint->subject) }}
                    </span>

                    <br><br>

                    <span style="font-size:12px;color:#6b7280">
                        {{ addslashes($complaint->address) }}
                    </span>

                </div>
            `).openPopup();


                /*
                |--------------------------------------------------------------------------
                | Resize Map
                |--------------------------------------------------------------------------
                */

                setTimeout(function() {

                    map.invalidateSize();

                }, 300);

            });
        </script>
    @endif

@endsection
