@extends('customer-service.layouts.app')

@section('title', 'Complaint Details')

@section('content')

    @php
        $isCommercial = str_contains(strtolower((string) $complaint->division?->name), 'commercial');

        $isEngineering = str_contains(strtolower((string) $complaint->division?->name), 'engineering');

        $canEdit = $complaint->status === 'Pending' && (int) $complaint->customer_service_id === (int) auth()->id();

        $displayAddress =
            $isCommercial && $complaint->consumer
                ? $complaint->consumer->address?->full_address ?? 'No registered account address recorded.'
                : ($complaint->address ?:
                'No address recorded.');

        $addressLabel = $isCommercial
            ? ($complaint->consumer
                ? 'Account Address'
                : 'Complainant Address')
            : 'Service Address';

        $commercialResolution = $complaint->commercialResolution;

        $aiAnalysis = $complaint->aiAnalysis;

        $consumerCategory = $aiAnalysis?->consumerCategory ?? $complaint->category;

        $predictedCategory = $aiAnalysis?->predictedCategory;

        $verifiedCategory = $aiAnalysis?->verifiedCategory;

        $urgencyLevel = $aiAnalysis?->urgency_level;

        $urgencyLabel = $urgencyLevel ?: 'Not Assessed';

        $urgencyClasses = match ($urgencyLevel) {
            'High' => 'bg-red-50 border-red-200 text-red-700',

            'Moderate' => 'bg-amber-50 border-amber-200 text-amber-700',

            'Low' => 'bg-green-50 border-green-200 text-green-700',

            default => 'bg-gray-50 border-gray-200 text-gray-600',
        };

        $urgencyIcon = match ($urgencyLevel) {
            'High' => 'fa-triangle-exclamation',
            'Moderate' => 'fa-circle-exclamation',
            'Low' => 'fa-circle-check',
            default => 'fa-circle-minus',
        };

    @endphp

    <div class="mx-3 space-y-6">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div class="flex items-center gap-3">

                <div
                    class="w-12 h-12 rounded-2xl
                    bg-blue-100 text-blue-600
                    flex items-center justify-center shrink-0">

                    <i class="fas fa-file-circle-exclamation text-xl"></i>

                </div>

                <div>

                    <p class="text-sm text-gray-500">
                        Customer Service
                    </p>

                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $isCommercial ? 'Commercial Services Complaint' : 'Engineering Operation Complaint' }}
                    </h1>

                    <p class="text-sm text-gray-500 mt-1">
                        Complaint {{ $complaint->complaint_no }}
                    </p>

                </div>

            </div>

            <div class="grid grid-cols-1 sm:flex sm:flex-wrap gap-3 w-full lg:w-auto">

                <a href="{{ route('customer-service.complaints.index') }}"
                    class="inline-flex items-center justify-center gap-2
                    px-4 py-2.5 rounded-xl
                    border border-gray-300
                    text-gray-700
                    hover:bg-gray-50 transition">

                    <i class="fas fa-arrow-left"></i>

                    Back to Complaints

                </a>

                @if ($canEdit)
                    <a href="{{ route('customer-service.complaints.edit', $complaint) }}"
                        class="inline-flex items-center justify-center gap-2
                        px-4 py-2.5 rounded-xl
                        bg-blue-600 text-white
                        hover:bg-blue-700 transition">

                        <i class="fas fa-pen-to-square"></i>

                        Edit

                    </a>
                @endif

            </div>

        </div>

        @if (session('error'))
            <div class="rounded-2xl border border-red-200
                bg-red-50 p-4">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-exclamation text-red-600 mt-0.5"></i>

                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </p>

                </div>

            </div>
        @endif

        @if ($errors->any())

            <div class="rounded-2xl border border-red-200
                bg-red-50 p-5">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-exclamation text-red-600 mt-0.5"></i>

                    <div>

                        <p class="font-semibold text-red-800">
                            Please correct the following:
                        </p>

                        <ul class="mt-2 list-disc list-inside text-sm text-red-700 space-y-1">

                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        @endif

        {{-- ========================================================= --}}
        {{-- COMPLAINT STATUS SUMMARY --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div class="p-5">

                <div
                    class="grid grid-cols-1
            md:grid-cols-3
            divide-y md:divide-y-0
            md:divide-x divide-gray-100">

                    {{-- ================================================= --}}
                    {{-- STATUS --}}
                    {{-- ================================================= --}}

                    <div class="pb-5 md:pb-0 md:pr-6">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-10 h-10 rounded-xl
                        flex items-center justify-center
                        shrink-0
                        @if ($complaint->status === 'Pending') bg-yellow-100 text-yellow-600
                        @elseif ($complaint->status === 'Verified')
                            bg-green-100 text-green-600
                        @elseif ($complaint->status === 'Assigned')
                            bg-indigo-100 text-indigo-600
                        @elseif ($complaint->status === 'In Progress')
                            bg-blue-100 text-blue-600
                        @elseif ($complaint->status === 'Accomplished')
                            bg-cyan-100 text-cyan-600
                        @elseif ($complaint->status === 'Completed')
                            bg-emerald-100 text-emerald-600
                        @elseif ($complaint->status === 'Closed')
                            bg-slate-200 text-slate-700
                        @elseif ($complaint->status === 'Rejected')
                            bg-red-100 text-red-600
                        @else
                            bg-gray-100 text-gray-600 @endif">

                                @if ($complaint->status === 'Pending')
                                    <i class="fas fa-clock"></i>
                                @elseif ($complaint->status === 'Verified')
                                    <i class="fas fa-circle-check"></i>
                                @elseif ($complaint->status === 'Assigned')
                                    <i class="fas fa-user-check"></i>
                                @elseif ($complaint->status === 'In Progress')
                                    <i class="fas fa-spinner"></i>
                                @elseif ($complaint->status === 'Accomplished')
                                    <i class="fas fa-clipboard-check"></i>
                                @elseif ($complaint->status === 'Completed')
                                    <i class="fas fa-check-double"></i>
                                @elseif ($complaint->status === 'Closed')
                                    <i class="fas fa-lock"></i>
                                @elseif ($complaint->status === 'Rejected')
                                    <i class="fas fa-circle-xmark"></i>
                                @else
                                    <i class="fas fa-circle-info"></i>
                                @endif

                            </div>


                            <div class="min-w-0">

                                <p
                                    class="text-xs font-medium
                            uppercase tracking-wide
                            text-gray-400">

                                    Status

                                </p>

                                <p class="mt-1 font-semibold
                            text-gray-900">

                                    {{ $complaint->status }}

                                </p>


                                <p class="mt-1 text-xs text-gray-500">

                                    @if ($complaint->status === 'Pending')
                                        Waiting for Customer Service review
                                    @elseif ($complaint->status === 'Verified')
                                        {{ $isCommercial ? 'Ready for Customer Service processing' : 'Ready for Engineering review' }}
                                    @elseif ($complaint->status === 'Assigned')
                                        Plumber assigned
                                    @elseif ($complaint->status === 'In Progress')
                                        {{ $isCommercial ? 'Concern is being processed' : 'Service work in progress' }}
                                    @elseif ($complaint->status === 'Accomplished')
                                        Awaiting accomplishment review
                                    @elseif ($complaint->status === 'Completed')
                                        {{ $isCommercial ? 'Commercial concern resolved' : 'Service work completed' }}
                                    @elseif ($complaint->status === 'Closed')
                                        Complaint closed
                                    @elseif ($complaint->status === 'Rejected')
                                        Complaint rejected
                                    @else
                                        Current complaint state
                                    @endif

                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- URGENCY --}}
                    {{-- ================================================= --}}

                    <div class="py-5 md:py-0
                md:px-6">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-10 h-10 rounded-xl
                        border
                        flex items-center justify-center
                        shrink-0
                        {{ $urgencyClasses }}">

                                <i class="fas
                            {{ $urgencyIcon }}">
                                </i>

                            </div>


                            <div class="min-w-0">

                                <p
                                    class="text-xs font-medium
                            uppercase tracking-wide
                            text-gray-400">

                                    Urgency

                                </p>

                                <p
                                    class="mt-1 font-semibold
                            @if ($urgencyLevel === 'High') text-red-700
                            @elseif ($urgencyLevel === 'Moderate')
                                text-amber-700
                            @elseif ($urgencyLevel === 'Low')
                                text-green-700
                            @else
                                text-gray-600 @endif">

                                    {{ $urgencyLabel }}

                                </p>


                                <p class="mt-1 text-xs text-gray-500">

                                    @if ($urgencyLevel === 'High')
                                        Requires prompt attention
                                    @elseif ($urgencyLevel === 'Moderate')
                                        Needs timely attention
                                    @elseif ($urgencyLevel === 'Low')
                                        No strong urgency indicators detected
                                    @else
                                        No urgency assessment available
                                    @endif

                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- SUBMITTED --}}
                    {{-- ================================================= --}}

                    <div class="pt-5 md:pt-0
                md:pl-6">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-10 h-10 rounded-xl
                        bg-gray-100 text-gray-600
                        flex items-center justify-center
                        shrink-0">

                                <i class="fas fa-calendar"></i>

                            </div>


                            <div class="min-w-0">

                                <p
                                    class="text-xs font-medium
                            uppercase tracking-wide
                            text-gray-400">

                                    Submitted

                                </p>

                                <p class="mt-1 font-semibold
                            text-gray-900">

                                    {{ $complaint->created_at->format('M d, Y') }}

                                </p>

                                <p class="mt-1 text-xs text-gray-500">

                                    {{ $complaint->created_at->format('h:i A') }}

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </x-form.card>


        {{-- ========================================================= --}}
        {{-- 1. COMPLAINT INFORMATION --}}
        {{-- ========================================================= --}}
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
                            General information submitted with this complaint.
                        </p>

                    </div>

                </div>

            </div>

            <div class="p-6 space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">

                    <div>

                        <p class="text-xs text-gray-500">
                            Complaint Number
                        </p>

                        <p class="font-semibold text-gray-900 mt-1">
                            {{ $complaint->complaint_no }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs text-gray-500">
                            Division
                        </p>

                        <p class="font-semibold text-gray-900 mt-1">
                            {{ $complaint->division?->name ?? '—' }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs text-gray-500">
                            Complaint Type
                        </p>

                        <p class="font-semibold text-gray-900 mt-1">
                            {{ $complaint->category?->name ?? '—' }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs text-gray-500">
                            Date Submitted
                        </p>

                        <p class="font-semibold text-gray-900 mt-1">
                            {{ $complaint->created_at?->format('M d, Y h:i A') ?? '—' }}
                        </p>

                    </div>

                    <div>
                        <p class="text-xs text-gray-500">
                            Created By
                        </p>

                        <p class="font-semibold text-gray-900 mt-1">
                            {{ $complaint->customerService?->full_name ?? 'Consumer Portal' }}
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            {{ $complaint->customer_service_id ? 'Customer Service' : 'Online Submission' }}
                        </p>
                    </div>




                </div>

                <div class="pt-6 border-t border-gray-100">

                    <p class="text-xs text-gray-500">
                        Complaint Description
                    </p>

                    <div
                        class="mt-2 rounded-xl bg-gray-50
                        border border-gray-200 p-4
                        text-sm text-gray-700 whitespace-pre-line">

                        {{ $complaint->description }}

                    </div>

                </div>

            </div>

        </x-form.card>


        {{-- ========================================================= --}}
        {{-- 2. COMPLAINANT & ACCOUNT --}}
        {{-- ========================================================= --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl
                            bg-sky-100 text-sky-600
                            flex items-center justify-center">

                            <i class="fas fa-user"></i>

                        </div>

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Person Reporting
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Contact information of the complainant.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="p-6 space-y-5">

                    <div>

                        <p class="text-xs text-gray-500">
                            Name
                        </p>

                        <p class="font-semibold text-gray-900 mt-1">
                            {{ $complaint->complainant_name }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs text-gray-500">
                            Contact Number
                        </p>

                        <p class="font-semibold text-gray-900 mt-1">
                            {{ $complaint->complainant_phone ?: '—' }}
                        </p>

                    </div>

                </div>

            </x-form.card>

            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl
                            bg-cyan-100 text-cyan-600
                            flex items-center justify-center">

                            <i class="fas fa-id-card"></i>

                        </div>

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Linked SWD Account
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                SWD consumer account associated with the complaint.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="p-6">

                    @if ($complaint->consumer)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <div>

                                <p class="text-xs text-gray-500">
                                    Account Number
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->consumer->account_number ?: '—' }}
                                </p>

                            </div>

                            <div>

                                <p class="text-xs text-gray-500">
                                    Account Holder
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->consumer->full_name }}
                                </p>

                            </div>

                            <div class="md:col-span-2">

                                <p class="text-xs text-gray-500">
                                    Account Address
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->consumer->address?->full_address ?? 'No registered account address recorded.' }}
                                </p>

                            </div>

                        </div>
                    @else
                        <div class="rounded-xl bg-gray-50
                            border border-gray-200 p-4">

                            <div class="flex items-start gap-3">

                                <i class="fas fa-circle-info text-gray-500 mt-0.5"></i>

                                <div>

                                    <p class="font-semibold text-gray-900">
                                        No SWD Account Linked
                                    </p>

                                    <p class="text-sm text-gray-500 mt-1">
                                        This complaint is not linked to an SWD consumer account.
                                    </p>

                                </div>

                            </div>

                        </div>
                    @endif

                </div>

            </x-form.card>

        </div>


        {{-- ========================================================= --}}
        {{-- 3. LOCATION & EVIDENCE --}}
        {{-- ========================================================= --}}
        <x-form.card>

            <div class="px-6 py-5 border-b border-gray-100">

                <div class="flex items-center gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                        bg-orange-100 text-orange-600
                        flex items-center justify-center">

                        <i class="fas fa-location-dot"></i>

                    </div>

                    <div>

                        <h3 class="font-semibold text-gray-900">
                            {{ $addressLabel }}
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">

                            @if ($isCommercial && $complaint->consumer)
                                Registered address of the linked SWD account.
                            @elseif ($isCommercial)
                                Address provided for this Commercial Services complaint.
                            @else
                                Reported location of the Engineering Operation concern.
                            @endif

                        </p>

                    </div>

                </div>

            </div>

            <div class="p-6">

                <div>

                    <p class="text-xs text-gray-500">
                        {{ $addressLabel }}
                    </p>

                    <p class="font-semibold text-gray-900 mt-1">
                        {{ $displayAddress }}
                    </p>

                </div>

                @if (!$isCommercial)

                    @if ($complaint->landmark)
                        <div class="mt-5">

                            <p class="text-xs text-gray-500">
                                Landmark
                            </p>

                            <p class="font-semibold text-gray-900 mt-1">
                                {{ $complaint->landmark }}
                            </p>

                        </div>
                    @endif

                    @if ($complaint->latitude && $complaint->longitude)
                        <div class="mt-6">

                            <div id="complaint-map"
                                class="w-full h-80 rounded-2xl
                                border border-gray-200 overflow-hidden">
                            </div>

                        </div>
                    @else
                        <div class="mt-6 rounded-xl bg-gray-50
                            border border-gray-200 p-4">

                            <p class="text-sm text-gray-500">
                                No map coordinates were recorded for this complaint.
                            </p>

                        </div>
                    @endif

                @endif

            </div>

        </x-form.card>

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
                        class="max-w-3xl w-full rounded-2xl
                        border border-gray-200">

                </div>

            </x-form.card>
        @endif


        {{-- ========================================================= --}}
        {{-- 4. AI-ASSISTED ANALYSIS --}}
        {{-- ========================================================= --}}
        {{-- ========================================================= --}}
        {{-- AI-ASSISTED ANALYSIS --}}
        {{-- ========================================================= --}}

        @if ($aiAnalysis)

            @php

                $rawAnalysis = is_array($aiAnalysis->raw_analysis) ? $aiAnalysis->raw_analysis : [];

                $supportingSummary = data_get($rawAnalysis, 'supporting_evidence.summary');

                $supportingIndicators = data_get($rawAnalysis, 'supporting_evidence.indicators', []);

                $urgencyReasons = data_get($rawAnalysis, 'urgency.reasons', []);

                $reviewReasons = $aiAnalysis->review_reasons ?? [];

                $humanReviewRecommended =
                    (bool) data_get($rawAnalysis, 'review.required', false) || (bool) $aiAnalysis->ambiguous;
            @endphp


            <x-form.card>

                {{-- Header --}}

                <div class="px-5 py-4
            border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-9 h-9 rounded-xl
                    bg-violet-100
                    text-violet-600
                    flex items-center
                    justify-center shrink-0">

                            <i class="fas
                        fa-wand-magic-sparkles">
                            </i>

                        </div>


                        <div>

                            <h3 class="font-semibold
                        text-gray-900">

                                AI-Assisted Analysis

                            </h3>

                            <p class="text-xs
                        text-gray-500 mt-0.5">

                                Supporting information for Customer Service review.

                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-5 space-y-5">


                    {{-- ================================================= --}}
                    {{-- CLASSIFICATION COMPARISON --}}
                    {{-- ================================================= --}}

                    <div class="grid grid-cols-1
                md:grid-cols-2 gap-4">


                        {{-- AI Suggested --}}

                        <div
                            class="rounded-xl
                    border border-violet-100
                    bg-violet-50/60
                    p-4">

                            <p class="text-xs
                        font-medium
                        text-violet-600">

                                AI Suggested Type

                            </p>

                            <p class="mt-1
                        font-semibold
                        text-gray-900">

                                {{ $predictedCategory?->name ?? ($aiAnalysis->predicted_type ?? 'Not available') }}

                            </p>

                        </div>


                        {{-- Consumer Submitted --}}

                        <div
                            class="rounded-xl
                    border border-sky-100
                    bg-sky-50/60
                    p-4">

                            <p class="text-xs
                        font-medium
                        text-sky-600">

                                Consumer Submitted

                            </p>

                            <p class="mt-1
                        font-semibold
                        text-gray-900">

                                {{ $consumerCategory?->name ?? 'Not available' }}

                            </p>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- WHY THIS WAS SUGGESTED --}}
                    {{-- ================================================= --}}

                    @if ($supportingSummary || !empty($supportingIndicators))

                        <div>

                            <p class="text-sm
                        font-semibold
                        text-gray-900">

                                Why This Was Suggested

                            </p>


                            @if ($supportingSummary)
                                <p
                                    class="mt-2
                            text-sm
                            leading-relaxed
                            text-gray-600">

                                    {{ $supportingSummary }}

                                </p>
                            @endif


                            @if (!empty($supportingIndicators))

                                <div class="mt-3 space-y-2">

                                    @foreach ($supportingIndicators as $indicator)
                                        <div
                                            class="flex
                                    items-start gap-2
                                    text-sm text-gray-600">

                                            <i
                                                class="fas
                                        fa-circle-check
                                        text-violet-500
                                        mt-0.5">
                                            </i>

                                            <span>
                                                {{ $indicator }}
                                            </span>

                                        </div>
                                    @endforeach

                                </div>

                            @endif

                        </div>

                    @endif


                    {{-- ================================================= --}}
                    {{-- URGENCY EXPLANATION --}}
                    {{-- ================================================= --}}

                    @if (!empty($urgencyReasons))

                        <div class="pt-4
                    border-t border-gray-100">

                            <div class="flex
                        items-center gap-2">

                                <i
                                    class="fas
                            {{ $urgencyIcon }}
                            @if ($urgencyLevel === 'High') text-red-500
                            @elseif ($urgencyLevel === 'Moderate')
                                text-amber-500
                            @elseif ($urgencyLevel === 'Low')
                                text-green-500
                            @else
                                text-gray-400 @endif">
                                </i>

                                <p
                                    class="text-sm
                            font-semibold
                            text-gray-900">

                                    Urgency Assessment

                                </p>

                            </div>


                            <div class="mt-2 space-y-2">

                                @foreach ($urgencyReasons as $reason)
                                    <div
                                        class="flex
                                items-start gap-2
                                text-sm text-gray-600">

                                        <span
                                            class="mt-2
                                    w-1.5 h-1.5
                                    rounded-full
                                    bg-gray-300 shrink-0">
                                        </span>

                                        <span>
                                            {{ $reason }}
                                        </span>

                                    </div>
                                @endforeach

                            </div>

                        </div>

                    @endif


                    {{-- ================================================= --}}
                    {{-- CONSUMER AI INTERACTION --}}
                    {{-- ================================================= --}}

                    @if (!is_null($aiAnalysis->consumer_accepted))

                        <div class="pt-4
                    border-t border-gray-100">

                            @if ($aiAnalysis->consumer_accepted)
                                <div
                                    class="flex
                            items-start gap-2
                            text-sm text-green-700">

                                    <i
                                        class="fas
                                fa-circle-check
                                mt-0.5">
                                    </i>

                                    <span>
                                        Consumer used and accepted the AI-suggested classification.
                                    </span>

                                </div>
                            @else
                                <div
                                    class="flex
                            items-start gap-2
                            text-sm text-amber-700">

                                    <i
                                        class="fas
                                fa-circle-exclamation
                                mt-0.5">
                                    </i>

                                    <span>
                                        Consumer used the AI assistant but submitted a different classification.
                                    </span>

                                </div>
                            @endif

                        </div>

                    @endif


                    {{-- ================================================= --}}
                    {{-- HUMAN REVIEW WARNING --}}
                    {{-- ================================================= --}}

                    @if ($humanReviewRecommended && !empty($reviewReasons))

                        <div
                            class="rounded-xl
                    border border-amber-200
                    bg-amber-50 p-4">

                            <div class="flex
                        items-start gap-3">

                                <i
                                    class="fas
                            fa-user-check
                            text-amber-600
                            mt-0.5">
                                </i>


                                <div>

                                    <p
                                        class="text-sm
                                font-semibold
                                text-amber-900">

                                        Human review recommended

                                    </p>


                                    <div class="mt-2
                                space-y-1.5">

                                        @foreach ($reviewReasons as $reason)
                                            <p class="text-sm
                                        text-amber-800">

                                                {{ $reason }}

                                            </p>
                                        @endforeach

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endif

                </div>

            </x-form.card>

        @endif


        {{-- ========================================================= --}}
        {{-- 5. RELATED COMPLAINTS --}}
        {{-- ========================================================= --}}
        @if ($similarComplaintError || ($similarComplaints['has_possible_related_complaints'] ?? false))
            <x-form.card>

                <div class="px-4 sm:px-6 py-5 border-b border-gray-100">

                    <div
                        class="flex flex-col sm:flex-row
                        sm:items-center sm:justify-between gap-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-xl
                                bg-violet-100 text-violet-600
                                flex items-center justify-center shrink-0">

                                <i class="fas fa-code-branch"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">
                                    Related Complaint Analysis
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    AI-assisted detection of complaints that may
                                    represent the same service incident.
                                </p>

                            </div>

                        </div>


                        @if (!$similarComplaintError && ($similarComplaints['count'] ?? 0) > 0)
                            <span
                                class="inline-flex items-center gap-2
                                self-start sm:self-auto
                                px-3 py-1.5 rounded-full
                                bg-violet-100 text-violet-700
                                text-sm font-semibold">

                                <i class="fas fa-link"></i>

                                {{ $similarComplaints['count'] }}

                                {{ Str::plural('possible match', $similarComplaints['count']) }}

                            </span>
                        @endif

                    </div>

                </div>


                <div class="p-4 sm:p-6">

                    @if ($similarComplaintError)

                        <div class="p-4 rounded-xl
                            bg-gray-50 border border-gray-200">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-9 h-9 rounded-lg
                                    bg-gray-100 text-gray-500
                                    flex items-center justify-center shrink-0">

                                    <i class="fas fa-triangle-exclamation"></i>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-gray-800">
                                        Related complaint analysis unavailable
                                    </p>

                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $similarComplaintError }}
                                    </p>

                                    <p class="text-xs text-gray-400 mt-2">
                                        This does not affect normal complaint
                                        processing.
                                    </p>

                                </div>

                            </div>

                        </div>
                    @else
                        <div
                            class="p-4 rounded-xl
                            bg-violet-50
                            border border-violet-100">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-9 h-9 rounded-lg
                                    bg-violet-100 text-violet-600
                                    flex items-center justify-center shrink-0">

                                    <i class="fas fa-wand-magic-sparkles"></i>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-violet-900">

                                        AI detected
                                        {{ $similarComplaints['count'] ?? 0 }}
                                        {{ Str::plural('complaint', $similarComplaints['count'] ?? 0) }}
                                        that may be related.

                                    </p>

                                    <p
                                        class="text-sm text-violet-700
                                        mt-1 leading-relaxed">

                                        These matches are based on complaint
                                        description, reported location,
                                        complaint classification, and reporting
                                        time. Customer Service should review
                                        each result before determining whether
                                        the complaints represent the same
                                        service incident.

                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="mt-5 space-y-4">

                            @foreach ($similarComplaints['matches'] ?? [] as $match)
                                @php
                                    $relationship = $match['relationship'] ?? 'Possibly Related';

                                    $isLikelyRelated = $relationship === 'Likely Related';

                                    $relationshipClasses = $isLikelyRelated
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-amber-100 text-amber-700';

                                    $score = number_format((float) ($match['score_percentage'] ?? 0), 2);

                                    $distance = $match['distance_km'] ?? null;
                                @endphp


                                <div
                                    class="rounded-2xl
                                    border border-gray-200
                                    bg-white overflow-hidden">

                                    <div
                                        class="p-4 sm:p-5
                                        flex flex-col lg:flex-row
                                        lg:items-start
                                        lg:justify-between gap-4">

                                        <div class="min-w-0">

                                            <div
                                                class="flex flex-wrap
                                                items-center gap-2">

                                                <a href="{{ route('customer-service.complaints.show', $match['complaint_id']) }}"
                                                    class="font-bold text-gray-900
                                                    hover:text-blue-600
                                                    transition">

                                                    {{ $match['complaint_no'] }}

                                                </a>


                                                <span
                                                    class="inline-flex
                                                    items-center gap-1.5
                                                    px-2.5 py-1 rounded-full
                                                    text-xs font-semibold
                                                    {{ $relationshipClasses }}">

                                                    <i
                                                        class="fas
                                                        {{ $isLikelyRelated ? 'fa-link' : 'fa-code-branch' }}">
                                                    </i>

                                                    {{ $relationship }}

                                                </span>


                                                @if (!empty($match['status']))
                                                    <span
                                                        class="inline-flex
                                                        items-center
                                                        px-2.5 py-1
                                                        rounded-full
                                                        bg-gray-100
                                                        text-gray-600
                                                        text-xs font-medium">

                                                        {{ $match['status'] }}

                                                    </span>
                                                @endif

                                            </div>


                                            <div
                                                class="mt-4 grid
                                                grid-cols-1 sm:grid-cols-2
                                                lg:grid-cols-3 gap-3">

                                                <div
                                                    class="p-3 rounded-xl
                                                    bg-gray-50
                                                    border border-gray-100">

                                                    <p
                                                        class="text-xs
                                                        uppercase
                                                        tracking-wide
                                                        text-gray-500
                                                        font-medium">

                                                        Match Score

                                                    </p>

                                                    <p
                                                        class="text-lg
                                                        font-bold
                                                        text-gray-900
                                                        mt-1">

                                                        {{ $score }}%

                                                    </p>

                                                </div>


                                                <div
                                                    class="p-3 rounded-xl
                                                    bg-gray-50
                                                    border border-gray-100">

                                                    <p
                                                        class="text-xs
                                                        uppercase
                                                        tracking-wide
                                                        text-gray-500
                                                        font-medium">

                                                        Complaint Type

                                                    </p>

                                                    <p
                                                        class="font-semibold
                                                        text-gray-900 mt-1">

                                                        {{ $match['same_complaint_type'] ?? false ? 'Same Type' : 'Different Type' }}

                                                    </p>

                                                </div>


                                                <div
                                                    class="p-3 rounded-xl
                                                    bg-gray-50
                                                    border border-gray-100">

                                                    <p
                                                        class="text-xs
                                                        uppercase
                                                        tracking-wide
                                                        text-gray-500
                                                        font-medium">

                                                        Distance

                                                    </p>

                                                    <p
                                                        class="font-semibold
                                                        text-gray-900 mt-1">

                                                        @if ($distance !== null)
                                                            @if ((float) $distance < 1)
                                                                {{ number_format((float) $distance * 1000, 0) }}
                                                                meters
                                                            @else
                                                                {{ number_format((float) $distance, 2) }}
                                                                km
                                                            @endif
                                                        @else
                                                            Not available
                                                        @endif

                                                    </p>

                                                </div>

                                            </div>

                                        </div>


                                        <div class="shrink-0">

                                            <a href="{{ route('customer-service.complaints.show', $match['complaint_id']) }}"
                                                class="inline-flex
                                                items-center justify-center
                                                gap-2 px-4 py-2.5
                                                rounded-xl
                                                border border-blue-200
                                                bg-blue-50
                                                text-blue-700
                                                text-sm font-medium
                                                hover:bg-blue-100
                                                transition">

                                                <i class="fas fa-eye"></i>

                                                View Complaint

                                            </a>

                                        </div>

                                    </div>


                                    @if (!empty($match['evidence']))
                                        <div
                                            class="px-4 sm:px-5 py-4
                                            border-t border-gray-100
                                            bg-gray-50/70">

                                            <p
                                                class="text-xs uppercase
                                                tracking-wide
                                                text-gray-500
                                                font-semibold">

                                                Why this complaint was matched

                                            </p>


                                            <div class="mt-3 space-y-2">

                                                @foreach ($match['evidence'] as $evidence)
                                                    <div
                                                        class="flex
                                                        items-start gap-2
                                                        text-sm text-gray-700">

                                                        <i
                                                            class="fas
                                                            fa-circle-check
                                                            text-violet-500
                                                            mt-0.5">
                                                        </i>

                                                        <span>
                                                            {{ $evidence }}
                                                        </span>

                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>
                                    @endif

                                </div>
                            @endforeach

                        </div>


                        <div class="mt-5 p-4 rounded-xl
                            bg-blue-50 border border-blue-100">

                            <div class="flex items-start gap-3">

                                <i class="fas fa-circle-info
                                    text-blue-600 mt-0.5">
                                </i>

                                <div>

                                    <p class="text-sm font-semibold
                                        text-blue-900">

                                        AI-Assisted Recommendation

                                    </p>

                                    <p
                                        class="text-sm text-blue-700
                                        mt-1 leading-relaxed">

                                        The match score is an AI-assisted
                                        similarity score, not a probability
                                        that the complaints are the same
                                        incident. Customer Service must verify
                                        the relationship before taking any
                                        operational action.

                                    </p>

                                </div>

                            </div>

                        </div>

                    @endif

                </div>

            </x-form.card>
        @endif


        {{-- ========================================================= --}}
        {{-- 6. REVIEW & DECISION --}}
        {{-- ========================================================= --}}
        {{-- ========================================================= --}}
        {{-- REVIEW & DECISION --}}
        {{-- ========================================================= --}}

        @if ($complaint->status === 'Pending')

            <x-form.card>

                {{-- Header --}}
                <div class="px-5 py-4
            border-b border-gray-100">

                    <div
                        class="flex flex-col
                sm:flex-row
                sm:items-center
                sm:justify-between
                gap-3">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-9 h-9 rounded-xl
                        bg-green-100
                        text-green-600
                        flex items-center
                        justify-center
                        shrink-0">

                                <i class="fas fa-user-check"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold
                            text-gray-900">

                                    Review & Decision

                                </h3>

                                <p class="text-xs
                            text-gray-500 mt-0.5">

                                    Confirm or correct the complaint classification before processing.

                                </p>

                            </div>

                        </div>


                        <span
                            class="inline-flex
                    items-center gap-1.5
                    self-start sm:self-auto
                    px-2.5 py-1
                    rounded-full
                    bg-yellow-50
                    border border-yellow-200
                    text-yellow-700
                    text-xs font-medium">

                            <i class="fas fa-clock"></i>

                            Pending Review

                        </span>

                    </div>

                </div>


                <div class="p-5">

                    {{-- ================================================= --}}
                    {{-- VERIFY FORM --}}
                    {{-- ================================================= --}}

                    <form id="verifyComplaintForm" method="POST"
                        action="{{ route('customer-service.complaints.verify', $complaint) }}"
                        onsubmit="return confirm('Verify this complaint with the selected division and complaint type?');">

                        @csrf


                        <div class="grid grid-cols-1
                    md:grid-cols-2 gap-5">

                            {{-- Division --}}
                            <div>

                                <label for="verification_division_id"
                                    class="block
                            text-sm font-medium
                            text-gray-700 mb-2">

                                    Verified Division

                                    <span class="text-red-500">
                                        *
                                    </span>

                                </label>


                                <select name="division_id" id="verification_division_id" required
                                    class="w-full
                            rounded-xl
                            border-gray-300
                            text-sm
                            focus:border-blue-500
                            focus:ring-blue-500">

                                    <option value="">
                                        Select division
                                    </option>

                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}" @selected((string) old('division_id', $complaint->division_id) === (string) $division->id)>

                                            {{ $division->name }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            {{-- Complaint Type --}}
                            <div>

                                <label for="verification_complaint_category_id"
                                    class="block
                            text-sm font-medium
                            text-gray-700 mb-2">

                                    Verified Complaint Type

                                    <span class="text-red-500">
                                        *
                                    </span>

                                </label>


                                <select name="complaint_category_id" id="verification_complaint_category_id" required
                                    class="w-full
                            rounded-xl
                            border-gray-300
                            text-sm
                            focus:border-blue-500
                            focus:ring-blue-500">

                                    <option value="">
                                        Select complaint type
                                    </option>

                                </select>

                            </div>

                        </div>


                        {{-- Verification Notes --}}
                        <div class="mt-5">

                            <label for="verification_reason"
                                class="block
                        text-sm font-medium
                        text-gray-700 mb-2">

                                Verification Notes

                                <span class="font-normal
                            text-gray-400">

                                    (optional)

                                </span>

                            </label>


                            <textarea name="verification_reason" id="verification_reason" rows="3" maxlength="2000"
                                placeholder="Add any notes about the verification or classification correction..."
                                class="w-full
                        rounded-xl
                        border-gray-300
                        text-sm
                        resize-none
                        focus:border-blue-500
                        focus:ring-blue-500">{{ old('verification_reason') }}</textarea>

                        </div>


                        {{-- ================================================= --}}
                        {{-- ACTIONS --}}
                        {{-- ================================================= --}}

                        <div
                            class="mt-6 pt-5
                    border-t border-gray-100
                    flex flex-col-reverse
                    sm:flex-row
                    sm:items-center
                    sm:justify-end
                    gap-3">

                            {{-- Reject --}}
                            <button type="button" id="showRejectComplaint"
                                class="inline-flex
                        items-center
                        justify-center
                        gap-2
                        px-4 py-2.5
                        rounded-xl
                        border border-red-200
                        bg-white
                        text-red-600
                        text-sm font-medium
                        hover:bg-red-50
                        transition">

                                <i class="fas fa-ban"></i>

                                Reject Complaint

                            </button>


                            {{-- Verify --}}
                            <button type="submit" id="verifyComplaintButton"
                                class="inline-flex
                        items-center
                        justify-center
                        gap-2
                        px-5 py-2.5
                        rounded-xl
                        bg-green-600
                        text-white
                        text-sm font-semibold
                        hover:bg-green-700
                        transition">

                                <i class="fas fa-circle-check"></i>

                                Verify Complaint

                            </button>

                        </div>

                    </form>


                    {{-- ================================================= --}}
                    {{-- REJECTION PANEL --}}
                    {{-- Hidden until Reject Complaint is clicked --}}
                    {{-- ================================================= --}}

                    <div id="rejectComplaintPanel"
                        class="hidden
                mt-5 pt-5
                border-t border-gray-100">

                        <div
                            class="rounded-xl
                    border border-red-200
                    bg-red-50 p-4">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-9 h-9
                            rounded-lg
                            bg-red-100
                            text-red-600
                            flex items-center
                            justify-center
                            shrink-0">

                                    <i class="fas
                                fa-circle-xmark">
                                    </i>

                                </div>


                                <div class="flex-1 min-w-0">

                                    <p
                                        class="text-sm
                                font-semibold
                                text-red-900">

                                        Reject Complaint

                                    </p>

                                    <p class="text-xs
                                text-red-700 mt-1">

                                        Provide the reason this complaint cannot proceed.

                                    </p>


                                    <form method="POST"
                                        action="{{ route('customer-service.complaints.reject', $complaint) }}"
                                        class="mt-4"
                                        onsubmit="return confirm('Reject this complaint? This action will mark the complaint as Rejected.');">

                                        @csrf


                                        <label for="rejection_reason"
                                            class="block
                                    text-sm font-medium
                                    text-red-900 mb-2">

                                            Rejection Reason

                                            <span class="text-red-600">
                                                *
                                            </span>

                                        </label>


                                        <textarea name="verification_reason" id="rejection_reason" rows="3" maxlength="2000" required
                                            placeholder="Explain why this complaint is being rejected..."
                                            class="w-full
                                    rounded-xl
                                    border-red-200
                                    bg-white
                                    text-sm
                                    resize-none
                                    focus:border-red-500
                                    focus:ring-red-500">{{ old('verification_reason') }}</textarea>


                                        <div
                                            class="mt-4
                                    flex flex-col-reverse
                                    sm:flex-row
                                    sm:items-center
                                    sm:justify-end
                                    gap-2">

                                            <button type="button" id="cancelRejectComplaint"
                                                class="inline-flex
                                        items-center
                                        justify-center
                                        px-4 py-2.5
                                        rounded-xl
                                        border border-gray-300
                                        bg-white
                                        text-gray-700
                                        text-sm font-medium
                                        hover:bg-gray-50
                                        transition">

                                                Cancel

                                            </button>


                                            <button type="submit"
                                                class="inline-flex
                                        items-center
                                        justify-center
                                        gap-2
                                        px-4 py-2.5
                                        rounded-xl
                                        bg-red-600
                                        text-white
                                        text-sm font-semibold
                                        hover:bg-red-700
                                        transition">

                                                <i class="fas fa-ban"></i>

                                                Confirm Rejection

                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </x-form.card>

        @endif


        {{-- ========================================================= --}}
        {{-- 7. VERIFICATION INFORMATION --}}
        {{-- ========================================================= --}}
        @if (in_array($complaint->status, [
                'Verified',
                'Rejected',
                'Assigned',
                'In Progress',
                'Accomplished',
                'Completed',
                'Closed',
            ]))

            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl
                            bg-gray-100 text-gray-600
                            flex items-center justify-center">

                            <i class="fas fa-user-check"></i>

                        </div>

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Verification Information
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Record of the Customer Service verification action.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <div>

                            <p class="text-xs text-gray-500">
                                Reviewed By
                            </p>

                            <p class="font-medium text-gray-900 mt-1">
                                {{ $complaint->verifier?->full_name ?? '—' }}
                            </p>

                        </div>

                        <div>

                            <p class="text-xs text-gray-500">
                                Reviewed At
                            </p>

                            <p class="font-medium text-gray-900 mt-1">
                                {{ $complaint->verified_at?->format('M d, Y h:i A') ?? '—' }}
                            </p>

                        </div>

                        <div>

                            <p class="text-xs text-gray-500">
                                Result
                            </p>

                            <p
                                class="font-medium mt-1
                                {{ $complaint->status === 'Rejected' ? 'text-red-600' : 'text-green-600' }}">

                                {{ $complaint->status === 'Rejected' ? 'Rejected' : 'Verified' }}

                            </p>

                        </div>

                    </div>

                    @if ($complaint->verification_reason)
                        <div class="mt-6 pt-6 border-t border-gray-100">

                            <p class="text-xs text-gray-500 mb-2">
                                Verification / Review Notes
                            </p>

                            <div
                                class="rounded-xl bg-gray-50
                                border border-gray-200 p-4
                                text-sm text-gray-700 whitespace-pre-line">

                                {{ $complaint->verification_reason }}

                            </div>

                        </div>
                    @endif

                </div>

            </x-form.card>

        @endif


        {{-- ========================================================= --}}
        {{-- 8. OPERATIONAL WORKFLOW --}}
        {{-- ========================================================= --}}
        @if ($isCommercial && $complaint->status === 'Verified')
            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <div
                        class="flex flex-col md:flex-row
                        md:items-center md:justify-between gap-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-11 h-11 rounded-xl
                                bg-sky-100 text-sky-700
                                flex items-center justify-center">

                                <i class="fas fa-headset"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">
                                    Commercial Services Processing
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    The complaint has been verified and is ready for Customer Service processing.
                                </p>

                            </div>

                        </div>

                        <span
                            class="inline-flex items-center gap-2
                            px-3 py-1.5 rounded-full
                            bg-green-100 text-green-700
                            text-sm font-medium">

                            <i class="fas fa-circle-check"></i>

                            Verified

                        </span>

                    </div>

                </div>

                <div class="p-6">

                    <div class="rounded-2xl border border-sky-200
                        bg-sky-50 p-5">

                        <div
                            class="flex flex-col md:flex-row
                            md:items-center md:justify-between gap-5">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-10 h-10 rounded-xl
                                    bg-sky-100 text-sky-700
                                    flex items-center justify-center shrink-0">

                                    <i class="fas fa-play"></i>

                                </div>

                                <div>

                                    <h4 class="font-semibold text-sky-900">
                                        Start Complaint Processing
                                    </h4>

                                    <p class="text-sm text-sky-700 mt-1 leading-relaxed">
                                        Start reviewing the consumer concern and prepare the findings and resolution.
                                    </p>

                                </div>

                            </div>

                            <form method="POST"
                                action="{{ route('customer-service.complaints.commercial.start', $complaint) }}"
                                onsubmit="return confirm('Start processing this Commercial Services complaint?');">

                                @csrf

                                <button type="submit"
                                    class="w-full md:w-auto
                                    inline-flex items-center justify-center gap-2
                                    px-5 py-3 rounded-xl
                                    bg-sky-700 text-white
                                    font-semibold hover:bg-sky-800 transition">

                                    <i class="fas fa-play"></i>

                                    Start Processing

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </x-form.card>
        @endif

        @if ($isCommercial && $complaint->status === 'In Progress')

            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <div
                        class="flex flex-col md:flex-row
                        md:items-center md:justify-between gap-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-11 h-11 rounded-xl
                                bg-blue-100 text-blue-700
                                flex items-center justify-center">

                                <i class="fas fa-clipboard-list"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">
                                    Commercial Resolution
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    Record the investigation and resolution of this concern.
                                </p>

                            </div>

                        </div>

                        <span
                            class="inline-flex items-center gap-2
                            px-3 py-1.5 rounded-full
                            bg-blue-100 text-blue-700
                            text-sm font-medium">

                            <i class="fas fa-spinner"></i>

                            In Progress

                        </span>

                    </div>

                </div>

                <form id="commercialResolutionForm" method="POST"
                    action="{{ route('customer-service.complaints.commercial.resolution', $complaint) }}">

                    @csrf
                    @method('PUT')

                    <div class="p-6 space-y-6">

                        @if ($commercialResolution)
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-5
                                rounded-2xl bg-slate-50
                                border border-slate-200 p-5">

                                <div>

                                    <p class="text-xs text-slate-500">
                                        Processing Started
                                    </p>

                                    <p class="text-sm font-semibold text-slate-900 mt-1">
                                        {{ $commercialResolution->started_at?->format('M d, Y h:i A') ?? '—' }}
                                    </p>

                                </div>

                                <div>

                                    <p class="text-xs text-slate-500">
                                        Started By
                                    </p>

                                    <p class="text-sm font-semibold text-slate-900 mt-1">
                                        {{ $commercialResolution->processor?->full_name ?? 'Customer Service' }}
                                    </p>

                                </div>

                            </div>
                        @endif

                        <div>

                            <label for="findings" class="block text-sm font-semibold text-slate-700 mb-2">

                                Findings
                                <span class="text-red-500">*</span>

                            </label>

                            <textarea name="findings" id="findings" rows="5"
                                class="w-full rounded-xl border-slate-300
                                focus:border-sky-500 focus:ring-sky-500"
                                placeholder="Record what Customer Service found while reviewing the complaint...">{{ old('findings', $commercialResolution?->findings) }}</textarea>

                            <p class="text-xs text-slate-500 mt-1">
                                You may save this as a draft while the complaint is still being investigated.
                            </p>

                        </div>


                        <div>

                            <label for="resolution_remarks" class="block text-sm font-semibold text-slate-700 mb-2">

                                Resolution Remarks
                                <span class="text-red-500">*</span>

                            </label>

                            <textarea name="resolution_remarks" id="resolution_remarks" rows="5"
                                class="w-full rounded-xl border-slate-300
                                focus:border-sky-500 focus:ring-sky-500"
                                placeholder="Explain how the complaint was resolved...">{{ old('resolution_remarks', $commercialResolution?->resolution_remarks) }}</textarea>

                        </div>

                        <div>

                            <div class="rounded-xl border border-amber-200
                            bg-amber-50 p-4">

                                <div class="flex items-start gap-3">

                                    <i class="fas fa-circle-info text-amber-600 mt-0.5"></i>

                                    <div>

                                        <p class="text-sm font-semibold text-amber-900">
                                            Before completing the complaint
                                        </p>

                                        <p class="text-sm text-amber-700 mt-1">
                                            Findings and resolution remarks,
                                            are all
                                            required when marking the complaint as completed.
                                            You can save incomplete information first and
                                            continue processing later.
                                        </p>

                                    </div>

                                </div>

                            </div>

                            <div
                                class="flex flex-col sm:flex-row
                            sm:items-center sm:justify-end gap-3
                            pt-2">

                                <button type="submit" onclick="prepareCommercialSave()"
                                    class="inline-flex items-center justify-center gap-2
                                px-5 py-3 rounded-xl
                                border border-sky-300
                                text-sky-700 bg-white
                                font-semibold hover:bg-sky-50 transition">

                                    <i class="fas fa-floppy-disk"></i>

                                    Save Resolution

                                </button>

                                <button type="submit" onclick="return prepareCommercialComplete()"
                                    class="inline-flex items-center justify-center gap-2
                                px-5 py-3 rounded-xl
                                bg-emerald-600 text-white
                                font-semibold hover:bg-emerald-700 transition">

                                    <i class="fas fa-circle-check"></i>

                                    Complete Complaint

                                </button>

                            </div>

                        </div>

                </form>

            </x-form.card>

        @endif

        @if ($isCommercial && in_array($complaint->status, ['Completed', 'Closed']) && $commercialResolution)

            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <div
                        class="flex flex-col md:flex-row
                        md:items-center md:justify-between gap-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-11 h-11 rounded-xl
                                bg-emerald-100 text-emerald-700
                                flex items-center justify-center">

                                <i class="fas fa-file-circle-check"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">
                                    Commercial Services Resolution
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    Final resolution recorded by Customer Service.
                                </p>

                            </div>

                        </div>

                        <span
                            class="inline-flex items-center gap-2
                            px-3 py-1.5 rounded-full
                            {{ $complaint->status === 'Closed' ? 'bg-slate-100 text-slate-700' : 'bg-emerald-100 text-emerald-700' }}
                            text-sm font-medium">

                            <i
                                class="fas
                                {{ $complaint->status === 'Closed' ? 'fa-lock' : 'fa-circle-check' }}"></i>

                            {{ $complaint->status }}

                        </span>

                    </div>

                </div>

                <div class="p-6 space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                        <div class="rounded-xl bg-slate-50
                            border border-slate-200 p-4">

                            <p class="text-xs text-slate-500">
                                Processed By
                            </p>

                            <p class="text-sm font-semibold text-slate-900 mt-1">
                                {{ $commercialResolution->processor?->full_name ?? 'Customer Service' }}
                            </p>

                        </div>

                        <div class="rounded-xl bg-slate-50
                            border border-slate-200 p-4">

                            <p class="text-xs text-slate-500">
                                Processing Started
                            </p>

                            <p class="text-sm font-semibold text-slate-900 mt-1">
                                {{ $commercialResolution->started_at?->format('M d, Y h:i A') ?? '—' }}
                            </p>

                        </div>

                        <div class="rounded-xl bg-slate-50
                            border border-slate-200 p-4">

                            <p class="text-xs text-slate-500">
                                Completed
                            </p>

                            <p class="text-sm font-semibold text-slate-900 mt-1">
                                {{ $commercialResolution->completed_at?->format('M d, Y h:i A') ?? '—' }}
                            </p>

                        </div>

                    </div>

                    <div>

                        <div class="flex items-center gap-2 mb-3">

                            <i class="fas fa-magnifying-glass text-blue-600"></i>

                            <h4 class="font-semibold text-gray-900">
                                Findings
                            </h4>

                        </div>

                        <div
                            class="rounded-xl bg-gray-50
                            border border-gray-200 p-4
                            text-sm text-gray-700 whitespace-pre-line">

                            {{ $commercialResolution->findings ?: 'No findings recorded.' }}

                        </div>

                    </div>


                    <div>

                        <div class="flex items-center gap-2 mb-3">

                            <i class="fas fa-circle-check text-emerald-600"></i>

                            <h4 class="font-semibold text-gray-900">
                                Resolution Remarks
                            </h4>

                        </div>

                        <div
                            class="rounded-xl bg-emerald-50
                            border border-emerald-100 p-4
                            text-sm text-gray-700 whitespace-pre-line">

                            {{ $commercialResolution->resolution_remarks ?: 'No resolution remarks recorded.' }}

                        </div>

                    </div>


                    @if ($complaint->status === 'Completed')
                        <div
                            class="pt-6 border-t border-gray-100
                            flex flex-col md:flex-row
                            md:items-center md:justify-between gap-4">

                            <div>

                                <h4 class="font-semibold text-gray-900">
                                    Finalize Complaint
                                </h4>

                                <p class="text-sm text-gray-500 mt-1">
                                    Close the complaint when no further Customer Service action is expected.
                                </p>

                            </div>

                            <form method="POST"
                                action="{{ route('customer-service.complaints.commercial.close', $complaint) }}"
                                onsubmit="return confirm('Close this Commercial Services complaint?');">

                                @csrf

                                <button type="submit"
                                    class="w-full md:w-auto
                                    inline-flex items-center justify-center gap-2
                                    px-5 py-3 rounded-xl
                                    bg-slate-800 text-white
                                    font-semibold hover:bg-slate-900 transition">

                                    <i class="fas fa-lock"></i>

                                    Close Complaint

                                </button>

                            </form>

                        </div>
                    @endif

                </div>

            </x-form.card>

        @endif

        @if (!$isCommercial)

            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl
                            bg-indigo-100 text-indigo-600
                            flex items-center justify-center">

                            <i class="fas fa-users-gear"></i>

                        </div>

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Plumber Assignment
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Plumbers assigned by Maintenance Management.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="p-6">

                    @if ($complaint->technicians->isNotEmpty())

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                            @foreach ($complaint->technicians as $plumber)
                                <div
                                    class="rounded-xl bg-gray-50
                                    border border-gray-200 p-4">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="w-10 h-10 rounded-full
                                            bg-indigo-100 text-indigo-600
                                            flex items-center justify-center shrink-0">

                                            <i class="fas fa-user-gear"></i>

                                        </div>

                                        <div class="min-w-0">

                                            <p class="font-semibold text-gray-900">
                                                {{ $plumber->full_name }}
                                            </p>

                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $plumber->pivot->status ?? 'Assigned' }}
                                            </p>

                                            @if ($plumber->pivot->assigned_at)
                                                <p class="text-xs text-gray-400 mt-1">
                                                    Assigned
                                                    {{ \Carbon\Carbon::parse($plumber->pivot->assigned_at)->format('M d, Y h:i A') }}
                                                </p>
                                            @endif

                                        </div>

                                    </div>

                                </div>
                            @endforeach

                        </div>
                    @else
                        <div class="rounded-xl bg-gray-50
                            border border-gray-200 p-4">

                            <div class="flex items-start gap-3">

                                <i class="fas fa-circle-info text-gray-500 mt-0.5"></i>

                                <p class="text-sm text-gray-600">
                                    No plumber has been assigned yet.
                                </p>

                            </div>

                        </div>

                    @endif

                </div>

            </x-form.card>

            @if ($complaint->maintenanceReport)

                <x-form.card>

                    <div class="px-6 py-5 border-b border-gray-100">

                        <div
                            class="flex flex-col md:flex-row
                            md:items-center md:justify-between gap-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="w-11 h-11 rounded-xl
                                    bg-indigo-100 text-indigo-600
                                    flex items-center justify-center">

                                    <i class="fas fa-screwdriver-wrench"></i>

                                </div>

                                <div>

                                    <h3 class="font-semibold text-gray-900">
                                        Accomplishment Report
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Field service information recorded by the assigned plumber.
                                    </p>

                                </div>

                            </div>

                            <span
                                class="inline-flex items-center gap-2
                                px-3 py-1.5 rounded-full
                                bg-emerald-100 text-emerald-700
                                text-sm font-medium">

                                <i class="fas fa-circle-check"></i>

                                Report Available

                            </span>

                        </div>

                    </div>

                    <div class="p-6 space-y-7">

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

                            <div class="p-4 rounded-xl
                                bg-gray-50 border border-gray-200">

                                <p class="text-xs uppercase tracking-wide text-gray-500 font-medium">
                                    Plumber
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->maintenanceReport->technician?->full_name ?? '—' }}
                                </p>

                            </div>

                            <div class="p-4 rounded-xl
                                bg-gray-50 border border-gray-200">

                                <p class="text-xs uppercase tracking-wide text-gray-500 font-medium">
                                    Review Status
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->maintenanceReport->review_status ?? '—' }}
                                </p>

                            </div>

                            <div class="p-4 rounded-xl
                                bg-gray-50 border border-gray-200">

                                <p class="text-xs uppercase tracking-wide text-gray-500 font-medium">
                                    Started
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->maintenanceReport->started_at?->format('M d, Y h:i A') ?? '—' }}
                                </p>

                            </div>

                            <div class="p-4 rounded-xl
                                bg-gray-50 border border-gray-200">

                                <p class="text-xs uppercase tracking-wide text-gray-500 font-medium">
                                    Submitted
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->maintenanceReport->submitted_at?->format('M d, Y h:i A') ?? '—' }}
                                </p>

                            </div>

                        </div>

                        <div>

                            <h4 class="font-semibold text-gray-900 mb-2">
                                Diagnosis
                            </h4>

                            <div
                                class="p-4 rounded-xl bg-gray-50
                                border border-gray-200
                                text-gray-700 whitespace-pre-line">

                                {{ $complaint->maintenanceReport->diagnosis ?: 'No diagnosis recorded.' }}

                            </div>

                        </div>

                        <div>

                            <h4 class="font-semibold text-gray-900 mb-2">
                                Root Cause
                            </h4>

                            <div
                                class="p-4 rounded-xl bg-gray-50
                                border border-gray-200
                                text-gray-700 whitespace-pre-line">

                                {{ $complaint->maintenanceReport->root_cause ?: 'No root cause recorded.' }}

                            </div>

                        </div>

                        <div>

                            <h4 class="font-semibold text-gray-900 mb-2">
                                Work Performed
                            </h4>

                            <div
                                class="p-4 rounded-xl bg-blue-50
                                border border-blue-100
                                text-gray-700 whitespace-pre-line">

                                {{ $complaint->maintenanceReport->work_performed ?: 'No work performed details recorded.' }}

                            </div>

                        </div>

                        <div>

                            <h4 class="font-semibold text-gray-900 mb-2">
                                Repair Procedure
                            </h4>

                            <div
                                class="p-4 rounded-xl bg-gray-50
                                border border-gray-200
                                text-gray-700 whitespace-pre-line">

                                {{ $complaint->maintenanceReport->repair_procedure ?: 'No repair procedure recorded.' }}

                            </div>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                            <div class="p-4 rounded-xl
                                bg-gray-50 border border-gray-200">

                                <p class="text-xs uppercase tracking-wide text-gray-500 font-medium">
                                    Materials Used
                                </p>

                                <div class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                                    {{ $complaint->maintenanceReport->materials_used ?: 'None recorded.' }}
                                </div>

                            </div>

                            <div class="p-4 rounded-xl
                                bg-gray-50 border border-gray-200">

                                <p class="text-xs uppercase tracking-wide text-gray-500 font-medium">
                                    Parts Replaced
                                </p>

                                <div class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                                    {{ $complaint->maintenanceReport->parts_replaced ?: 'None recorded.' }}
                                </div>

                            </div>

                            <div class="p-4 rounded-xl
                                bg-gray-50 border border-gray-200">

                                <p class="text-xs uppercase tracking-wide text-gray-500 font-medium">
                                    Tools Used
                                </p>

                                <div class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                                    {{ $complaint->maintenanceReport->tools_used ?: 'None recorded.' }}
                                </div>

                            </div>

                        </div>

                        <div>

                            <h4 class="font-semibold text-gray-900 mb-2">
                                Plumber Notes
                            </h4>

                            <div
                                class="p-4 rounded-xl bg-yellow-50
                                border border-yellow-100
                                text-gray-700 whitespace-pre-line">

                                {{ $complaint->maintenanceReport->technician_notes ?: 'No plumber notes recorded.' }}

                            </div>

                        </div>

                        <div>

                            <h4 class="font-semibold text-gray-900 mb-2">
                                Completion Remarks
                            </h4>

                            <div
                                class="p-4 rounded-xl bg-emerald-50
                                border border-emerald-100
                                text-gray-700 whitespace-pre-line">

                                {{ $complaint->maintenanceReport->completion_remarks ?: 'No completion remarks recorded.' }}

                            </div>

                        </div>

                        @if ($complaint->maintenanceReport->before_photo || $complaint->maintenanceReport->after_photo)

                            <div>

                                <h4 class="font-semibold text-gray-900 mb-4">
                                    Maintenance Photo Evidence
                                </h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    @if ($complaint->maintenanceReport->before_photo)
                                        <div>

                                            <p class="text-sm font-medium text-gray-700 mb-2">
                                                Before Maintenance
                                            </p>

                                            <img src="{{ asset('storage/' . $complaint->maintenanceReport->before_photo) }}"
                                                alt="Before maintenance"
                                                class="w-full h-72 object-cover
                                                rounded-2xl border border-gray-200">

                                        </div>
                                    @endif

                                    @if ($complaint->maintenanceReport->after_photo)
                                        <div>

                                            <p class="text-sm font-medium text-gray-700 mb-2">
                                                After Maintenance
                                            </p>

                                            <img src="{{ asset('storage/' . $complaint->maintenanceReport->after_photo) }}"
                                                alt="After maintenance"
                                                class="w-full h-72 object-cover
                                                rounded-2xl border border-gray-200">

                                        </div>
                                    @endif

                                </div>

                            </div>

                        @endif

                    </div>

                </x-form.card>
            @elseif (in_array($complaint->status, ['Verified', 'Assigned', 'In Progress', 'Accomplished', 'Completed']))
                <x-form.card>

                    <div class="p-6">

                        <div
                            class="flex items-start gap-4
                            p-5 rounded-2xl
                            bg-gray-50 border border-gray-200">

                            <div
                                class="w-11 h-11 rounded-xl
                                bg-gray-200 text-gray-600
                                flex items-center justify-center shrink-0">

                                <i class="fas fa-screwdriver-wrench"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">
                                    Accomplishment Report Not Available
                                </h3>

                                <p class="text-sm text-gray-600 mt-1 leading-relaxed">
                                    No accomplishment report has been submitted for this Engineering Operation complaint
                                    yet.
                                </p>

                            </div>

                        </div>

                    </div>

                </x-form.card>

            @endif

        @endif

        <x-form.card>

            <div class="px-6 py-5 border-b border-gray-100">

                <div class="flex items-center gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                        bg-gray-100 text-gray-600
                        flex items-center justify-center">

                        <i class="fas fa-clock-rotate-left"></i>

                    </div>

                    <div>

                        <h3 class="font-semibold text-gray-900">
                            Complaint Timeline
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Important dates recorded by the system.
                        </p>

                    </div>

                </div>

            </div>

            <div class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                    <div>

                        <p class="text-xs text-gray-500">
                            Submitted
                        </p>

                        <p class="font-medium text-gray-900 mt-1">
                            {{ $complaint->created_at?->format('M d, Y h:i A') ?? '—' }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs text-gray-500">
                            Verified / Reviewed
                        </p>

                        <p class="font-medium text-gray-900 mt-1">
                            {{ $complaint->verified_at?->format('M d, Y h:i A') ?? 'Not yet reviewed' }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs text-gray-500">
                            Completed
                        </p>

                        <p class="font-medium text-gray-900 mt-1">
                            {{ $complaint->completed_at?->format('M d, Y h:i A') ?? 'Not yet completed' }}
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

                @if ($isCommercial && $commercialResolution)
                    <div class="mt-6 pt-6 border-t border-gray-100">

                        <p
                            class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium mb-4">

                            Commercial Services Timeline

                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            <div>

                                <p class="text-xs text-gray-500">
                                    Processing Started
                                </p>

                                <p class="font-medium text-gray-900 mt-1">
                                    {{ $commercialResolution->started_at?->format('M d, Y h:i A') ?? '—' }}
                                </p>

                            </div>

                            <div>

                                <p class="text-xs text-gray-500">
                                    Resolution Completed
                                </p>

                                <p class="font-medium text-gray-900 mt-1">
                                    {{ $commercialResolution->completed_at?->format('M d, Y h:i A') ?? 'Not yet completed' }}
                                </p>

                            </div>

                        </div>

                    </div>
                @endif

                @if (!$isCommercial && $complaint->maintenanceReport)
                    <div class="mt-6 pt-6 border-t border-gray-100">

                        <p
                            class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium mb-4">

                            Engineering Service Timeline

                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>

                                <p class="text-xs text-gray-500">
                                    Service Started
                                </p>

                                <p class="font-medium text-gray-900 mt-1">
                                    {{ $complaint->maintenanceReport->started_at?->format('M d, Y h:i A') ?? '—' }}
                                </p>

                            </div>

                            <div>

                                <p class="text-xs text-gray-500">
                                    Accomplishment Report Submitted
                                </p>

                                <p class="font-medium text-gray-900 mt-1">
                                    {{ $complaint->maintenanceReport->submitted_at?->format('M d, Y h:i A') ?? '—' }}
                                </p>

                            </div>

                        </div>

                    </div>
                @endif

            </div>

        </x-form.card>

        @if ($canEdit)
            <x-form.card>

                <div class="p-6 flex flex-col md:flex-row
                    md:items-center md:justify-between gap-4">

                    <div>

                        <div class="flex items-center gap-2">

                            <i class="fas fa-trash-can text-red-600"></i>

                            <h3 class="font-semibold text-gray-900">
                                Delete Complaint
                            </h3>

                        </div>

                        <p class="text-sm text-gray-500 mt-1">
                            Only the Customer Service employee who created this pending complaint can delete it.
                        </p>

                    </div>

                    <form action="{{ route('customer-service.complaints.destroy', $complaint) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this complaint?');">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            class="w-full md:w-auto
                            px-4 py-2.5 rounded-xl
                            bg-red-600 text-white
                            hover:bg-red-700 transition">

                            <i class="fas fa-trash mr-2"></i>

                            Delete Complaint

                        </button>

                    </form>

                </div>

            </x-form.card>
        @endif

    </div>

    <script>
        function rejectComplaint() {

            const textarea =
                document.getElementById(
                    'rejection_reason'
                );

            const reason =
                textarea.value.trim();

            if (!reason) {

                alert(
                    'Please provide a rejection reason.'
                );

                textarea.focus();

                return;
            }

            const confirmed =
                confirm(
                    'Are you sure you want to reject this complaint?'
                );

            if (confirmed) {

                document
                    .getElementById(
                        'rejectComplaintForm'
                    )
                    .submit();

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Save Commercial Resolution
        |--------------------------------------------------------------------------
        */

        function prepareCommercialSave() {

            const form =
                document.getElementById(
                    'commercialResolutionForm'
                );

            if (!form) {
                return;
            }

            form.action =
                @json(route('customer-service.complaints.commercial.resolution', $complaint));

            const methodInput =
                form.querySelector(
                    'input[name="_method"]'
                );

            if (methodInput) {
                methodInput.value = 'PUT';
            }

        }


        /*
        |--------------------------------------------------------------------------
        | Complete Commercial Complaint
        |--------------------------------------------------------------------------
        */

        function prepareCommercialComplete() {

            const form =
                document.getElementById(
                    'commercialResolutionForm'
                );

            if (!form) {
                return false;
            }


            const requiredFields = [{
                    id: 'findings',
                    label: 'Findings'
                },
                {
                    id: 'resolution_remarks',
                    label: 'Resolution Remarks'
                }
            ];


            for (
                const fieldData of requiredFields
            ) {

                const field =
                    document.getElementById(
                        fieldData.id
                    );

                if (
                    !field ||
                    !field.value.trim()
                ) {

                    alert(
                        fieldData.label +
                        ' is required before completing the complaint.'
                    );

                    if (field) {
                        field.focus();
                    }

                    return false;
                }

            }


            const confirmed =
                confirm(
                    'Complete this Commercial Services complaint?\n\n' +
                    'The resolution will become final and the complaint status will change to Completed.'
                );

            if (!confirmed) {
                return false;
            }


            form.action =
                @json(route('customer-service.complaints.commercial.complete', $complaint));


            const methodInput =
                form.querySelector(
                    'input[name="_method"]'
                );


            if (methodInput) {
                methodInput.value = 'POST';
            }


            return true;

        }
    </script>


    {{-- ========================================================= --}}
    {{-- ENGINEERING MAP ONLY --}}
    {{-- ========================================================= --}}

    @if (!$isCommercial && $complaint->latitude && $complaint->longitude)
        @push('styles')
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        @endpush


        @push('scripts')
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


            <script>
                document.addEventListener(
                    'DOMContentLoaded',
                    function() {

                        const mapElement =
                            document.getElementById(
                                'complaint-map'
                            );


                        if (!mapElement) {
                            return;
                        }


                        const latitude =
                            {{ (float) $complaint->latitude }};

                        const longitude =
                            {{ (float) $complaint->longitude }};


                        const map =
                            L.map(
                                'complaint-map', {
                                    zoomControl: true,
                                    attributionControl: true
                                }
                            );


                        L.tileLayer(
                            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,
                                attribution: '&copy; OpenStreetMap contributors'
                            }
                        ).addTo(map);


                        map.setView(
                            [
                                latitude,
                                longitude
                            ],
                            17
                        );


                        const marker =
                            L.marker(
                                [
                                    latitude,
                                    longitude
                                ]
                            ).addTo(map);


                        marker.bindPopup(`
                        <div class="text-sm">

                            <div class="font-semibold text-gray-900">
                                {{ $complaint->complaint_no }}
                            </div>

                            <div class="text-gray-700 mt-1">
                                {{ addslashes($complaint->category?->name ?? 'Engineering Operation Complaint') }}
                            </div>

                            <div class="text-gray-500 mt-1">
                                {{ addslashes($complaint->address ?? '') }}
                            </div>

                        </div>
                    `).openPopup();


                        function refreshMap() {

                            map.invalidateSize({
                                animate: false,
                                pan: false
                            });

                        }


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

                            }
                        );


                        window.addEventListener(
                            'resize',
                            refreshMap
                        );

                    }
                );
            </script>
        @endpush
    @endif

    {{-- ========================================================= --}}
    {{-- VERIFICATION DIVISION / COMPLAINT TYPES --}}
    {{-- COMMERCIAL + ENGINEERING --}}
    {{-- ========================================================= --}}

    @php
        $verificationDivisions = $divisions
            ->map(function ($division) {
                return [
                    'id' => (int) $division->id,
                    'name' => $division->name,
                    'complaint_types' => $division->complaintTypes
                        ->map(function ($type) {
                            return [
                                'id' => (int) $type->id,
                                'name' => $type->name,
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    @endphp

    {{-- ========================================================= --}}
    {{-- VERIFICATION + REJECTION UI SCRIPT --}}
    {{-- ========================================================= --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* ---------------------------------------------------------
             | Verification division / complaint type dropdown
             * --------------------------------------------------------- */

            const divisionSelect = document.getElementById('verification_division_id');
            const categorySelect = document.getElementById('verification_complaint_category_id');

            if (divisionSelect && categorySelect) {
                const divisions = @json($verificationDivisions);
                const originalCategoryId = @json((string) old('complaint_category_id', $complaint->complaint_category_id));

                function loadComplaintTypes(divisionId, selectedCategoryId = null) {
                    categorySelect.innerHTML = '<option value="">Select complaint type</option>';

                    if (!divisionId) {
                        return;
                    }

                    const selectedDivision = divisions.find(function(division) {
                        return String(division.id) === String(divisionId);
                    });

                    if (!selectedDivision) {
                        return;
                    }

                    selectedDivision.complaint_types.forEach(function(type) {
                        const option = document.createElement('option');
                        option.value = type.id;
                        option.textContent = type.name;

                        if (
                            selectedCategoryId !== null &&
                            selectedCategoryId !== '' &&
                            String(type.id) === String(selectedCategoryId)
                        ) {
                            option.selected = true;
                        }

                        categorySelect.appendChild(option);
                    });
                }

                loadComplaintTypes(
                    divisionSelect.value,
                    originalCategoryId
                );

                divisionSelect.addEventListener('change', function() {
                    loadComplaintTypes(this.value);
                });
            }

            /* ---------------------------------------------------------
             | Reject complaint panel
             * --------------------------------------------------------- */

            const showRejectButton = document.getElementById('showRejectComplaint');
            const cancelRejectButton = document.getElementById('cancelRejectComplaint');
            const rejectPanel = document.getElementById('rejectComplaintPanel');
            const rejectionReason = document.getElementById('rejection_reason');

            if (showRejectButton && rejectPanel) {
                showRejectButton.addEventListener('click', function() {
                    rejectPanel.classList.remove('hidden');
                    showRejectButton.classList.add('hidden');

                    setTimeout(function() {
                        if (rejectionReason) {
                            rejectionReason.focus();
                        }
                    }, 100);
                });
            }

            if (cancelRejectButton && rejectPanel) {
                cancelRejectButton.addEventListener('click', function() {
                    rejectPanel.classList.add('hidden');

                    if (showRejectButton) {
                        showRejectButton.classList.remove('hidden');
                    }
                });
            }
        });
    </script>

@endsection
