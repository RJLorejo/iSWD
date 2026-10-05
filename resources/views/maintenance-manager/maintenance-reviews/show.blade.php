@extends('maintenance-manager.layouts.app')

@section('title', 'Review Accomplishment Report')

@section('content')

    @php
        $complaint = $maintenanceReport->complaint;

        $urgency = strtoupper(trim($complaint?->aiAnalysis?->urgency_level ?? ''));

        $urgencyClasses = match ($urgency) {
            'HIGH' => 'bg-red-50 text-red-700 border-red-200',

            'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',

            'LOW' => 'bg-green-50 text-green-700 border-green-200',

            default => 'bg-slate-50 text-slate-500 border-slate-200',
        };

        $reviewClasses = match ($maintenanceReport->review_status) {
            'Pending Review' => 'bg-amber-50 text-amber-700 border-amber-200',

            'Returned' => 'bg-red-50 text-red-700 border-red-200',

            'Approved' => 'bg-green-50 text-green-700 border-green-200',

            default => 'bg-slate-50 text-slate-600 border-slate-200',
        };

        $isApproved = $maintenanceReport->review_status === 'Approved';

        $isClosed = $complaint?->status === 'Closed';
    @endphp


    <div class="max-w-6xl mx-auto space-y-5">

        {{-- Header --}}
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

            <div>

                <div class="flex items-center gap-3">

                    <a href="{{ route('maintenance-manager.maintenance-reviews.index') }}"
                        class="inline-flex items-center justify-center
                               w-9 h-9 rounded-xl
                               bg-white border border-slate-200
                               text-slate-500
                               hover:text-sky-700
                               hover:border-sky-200 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>


                    <div>

                        <div class="flex flex-wrap items-center gap-2">

                            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">
                                Service Accomplishment Review
                            </h1>

                            <span
                                class="inline-flex items-center gap-1.5
                                       px-2.5 py-1 rounded-full
                                       border text-xs font-semibold
                                       {{ $reviewClasses }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                {{ $maintenanceReport->review_status }}
                            </span>

                        </div>


                        <div class="flex flex-wrap items-center gap-2 mt-2">

                            <span class="font-semibold text-sky-700">
                                {{ $complaint?->complaint_no ?? '—' }}
                            </span>

                            @if ($urgency)
                                <span
                                    class="inline-flex items-center gap-1
                                           px-2 py-0.5 rounded-full
                                           border text-[10px] font-bold
                                           {{ $urgencyClasses }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                    {{ $urgency }} URGENCY
                                </span>
                            @endif

                        </div>

                    </div>

                </div>

            </div>


            @if ($isClosed)
                <span
                    class="inline-flex items-center gap-2
                           px-3 py-2 rounded-xl
                           bg-slate-100 text-slate-700
                           text-sm font-semibold">
                    <i class="fas fa-lock"></i>
                    Complaint Closed
                </span>
            @endif

        </div>


        {{-- Messages --}}
        @if (session('success'))
            <div
                class="rounded-xl border border-green-200
                       bg-green-50 px-4 py-3
                       text-sm text-green-800">
                <i class="fas fa-circle-check mr-2"></i>
                {{ session('success') }}
            </div>
        @endif


        @if (session('error'))
            <div
                class="rounded-xl border border-red-200
                       bg-red-50 px-4 py-3
                       text-sm text-red-800">
                <i class="fas fa-circle-exclamation mr-2"></i>
                {{ session('error') }}
            </div>
        @endif


        @if ($errors->any())

            <div
                class="rounded-xl border border-red-200
                       bg-red-50 px-4 py-3
                       text-sm text-red-800">

                <p class="font-semibold">
                    Please correct the following:
                </p>

                <ul class="list-disc list-inside mt-2 space-y-1">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif


        {{-- Complaint Overview --}}
        <div class="bg-white rounded-2xl
                   border border-slate-200 shadow-sm overflow-hidden">

            <div
                class="px-5 sm:px-6 py-5
                       border-b border-sky-100
                       bg-sky-50/40">

                <div class="flex items-start gap-3">

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-sky-100 text-sky-700
                               flex items-center justify-center shrink-0">
                        <i class="fas fa-file-lines"></i>
                    </div>

                    <div>

                        <h2 class="font-semibold text-slate-900">
                            Complaint Overview
                        </h2>

                        <p class="text-xs text-slate-500 mt-1">
                            Complaint and consumer information related to this maintenance work.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-5 sm:p-6
                       grid grid-cols-2
                       lg:grid-cols-4 gap-5">

                <div>

                    <p class="text-xs text-slate-500">
                        Complaint Type
                    </p>

                    <p class="font-semibold text-slate-900 mt-1">
                        {{ $complaint?->category?->name ?? 'Uncategorized' }}
                    </p>

                </div>


                <div>

                    <p class="text-xs text-slate-500">
                        Division
                    </p>

                    <p class="font-semibold text-slate-900 mt-1">
                        {{ $complaint?->division?->name ?? '—' }}
                    </p>

                </div>


                <div>

                    <p class="text-xs text-slate-500">
                        Consumer
                    </p>

                    <p class="font-semibold text-slate-900 mt-1">

                        {{ $complaint?->consumer?->full_name ?? ($complaint?->complainant_name ?? 'Unknown') }}

                    </p>

                </div>


                <div>

                    <p class="text-xs text-slate-500">
                        Maintenance Status
                    </p>

                    <p class="font-semibold text-slate-900 mt-1">

                        {{ $complaint?->status === 'Completed' ? 'Accomplished' : $complaint?->status ?? '—' }}

                    </p>

                </div>

            </div>


            <div class="px-5 sm:px-6 pb-6">

                <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">

                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Concern
                    </p>

                    <p class="text-sm text-slate-700 whitespace-pre-line mt-2">
                        {{ $complaint?->description ?: 'No complaint description recorded.' }}
                    </p>

                </div>


                <div class="rounded-xl bg-slate-50 border border-slate-100 p-4 mt-3">

                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Service Location
                    </p>

                    <p class="text-sm text-slate-700 mt-2">
                        {{ $complaint?->address ?: 'No address recorded.' }}
                    </p>

                    @if ($complaint?->landmark)
                        <p class="text-xs text-slate-500 mt-1">
                            Landmark:
                            {{ $complaint->landmark }}
                        </p>
                    @endif

                </div>

            </div>

        </div>


        {{-- Customer Service Result --}}
        @if ($complaint?->commercialResolution)
            <div class="bg-white rounded-2xl
                       border border-slate-200 shadow-sm overflow-hidden">

                <div
                    class="px-5 sm:px-6 py-5
                           border-b border-cyan-100
                           bg-cyan-50/40">

                    <div class="flex items-start gap-3">

                        <div
                            class="w-11 h-11 rounded-xl
                                   bg-cyan-100 text-cyan-700
                                   flex items-center justify-center shrink-0">
                            <i class="fas fa-headset"></i>
                        </div>

                        <div>

                            <h2 class="font-semibold text-slate-900">
                                Customer Service Processing Result
                            </h2>

                            <p class="text-xs text-slate-500 mt-1">
                                Information recorded before this request was forwarded to maintenance.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-5 sm:p-6 grid md:grid-cols-2 gap-5">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Findings
                        </p>

                        <p class="text-sm text-slate-700 whitespace-pre-line mt-2">
                            {{ $complaint->commercialResolution->findings ?: 'No findings recorded.' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Resolution / Recommendation
                        </p>

                        <p class="text-sm text-slate-700 whitespace-pre-line mt-2">
                            {{ $complaint->commercialResolution->resolution ?: 'No recommendation recorded.' }}
                        </p>

                    </div>

                </div>

            </div>
        @endif


        {{-- Maintenance Team --}}
        <div class="bg-white rounded-2xl
                   border border-slate-200 shadow-sm overflow-hidden">

            <div
                class="px-5 sm:px-6 py-5
                       border-b border-blue-100
                       bg-blue-50/40">

                <div class="flex items-center justify-between gap-3">

                    <div class="flex items-start gap-3">

                        <div
                            class="w-11 h-11 rounded-xl
                                   bg-blue-100 text-blue-700
                                   flex items-center justify-center shrink-0">
                            <i class="fas fa-users-gear"></i>
                        </div>

                        <div>

                            <h2 class="font-semibold text-slate-900">
                                Maintenance Team
                            </h2>

                            <p class="text-xs text-slate-500 mt-1">
                                Plumbers assigned to this maintenance activity.
                            </p>

                        </div>

                    </div>


                    <span
                        class="inline-flex items-center justify-center
                               min-w-9 h-9 px-2.5 rounded-full
                               bg-blue-100 text-blue-700
                               text-sm font-bold">
                        {{ $complaint?->technicians?->count() ?? 0 }}
                    </span>

                </div>

            </div>


            <div class="p-5 sm:p-6">

                @if ($complaint?->technicians?->count())

                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">

                        @foreach ($complaint->technicians as $technician)
                            <div
                                class="flex items-center gap-3
                                       p-3 rounded-xl
                                       bg-slate-50
                                       border border-slate-100">

                                <div
                                    class="w-10 h-10 rounded-full
                                           bg-blue-100 text-blue-700
                                           flex items-center justify-center
                                           font-bold shrink-0">
                                    {{ strtoupper(substr($technician->first_name ?? 'P', 0, 1)) }}
                                </div>


                                <div class="min-w-0 flex-1">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <p class="font-semibold text-slate-900 truncate">
                                            {{ $technician->full_name }}
                                        </p>


                                        @if ($maintenanceReport->technician_id === $technician->id)
                                            <span
                                                class="inline-flex px-2 py-0.5
                                                       rounded-full
                                                       bg-violet-100 text-violet-700
                                                       text-[10px] font-bold">
                                                REPORT SUBMITTER
                                            </span>
                                        @endif

                                    </div>


                                    @if ($technician->employee_id)
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            {{ $technician->employee_id }}
                                        </p>
                                    @else
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            Maintenance Plumber
                                        </p>
                                    @endif


                                    @if ($technician->pivot?->status)
                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ $technician->pivot->status === 'Completed' ? 'Accomplished' : $technician->pivot->status }}
                                        </p>
                                    @endif

                                </div>

                            </div>
                        @endforeach

                    </div>
                @else
                    <div class="text-center py-6 text-sm text-slate-500">
                        No maintenance team recorded.
                    </div>

                @endif

            </div>

        </div>


        {{-- Report Information --}}
        <div class="bg-white rounded-2xl
                   border border-slate-200 shadow-sm overflow-hidden">

            <div
                class="px-5 sm:px-6 py-5
                       border-b border-violet-100
                       bg-violet-50/40">

                <div class="flex items-start gap-3">

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-violet-100 text-violet-700
                               flex items-center justify-center shrink-0">
                        <i class="fas fa-file-signature"></i>
                    </div>

                    <div>

                        <h2 class="font-semibold text-slate-900">
                            Report Information
                        </h2>

                        <p class="text-xs text-slate-500 mt-1">
                            Submission details for the shared accomplishment report.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-5 sm:p-6 grid grid-cols-2 lg:grid-cols-4 gap-5">

                <div>

                    <p class="text-xs text-slate-500">
                        Report Submitted By
                    </p>

                    <p class="text-sm font-semibold text-slate-900 mt-1">
                        {{ $maintenanceReport->technician?->full_name ?? '—' }}
                    </p>

                    @if ($maintenanceReport->technician?->employee_id)
                        <p class="text-xs text-slate-500 mt-1">
                            {{ $maintenanceReport->technician->employee_id }}
                        </p>
                    @endif

                </div>


                <div>

                    <p class="text-xs text-slate-500">
                        Maintenance Started
                    </p>

                    <p class="text-sm font-semibold text-slate-800 mt-1">
                        {{ $maintenanceReport->started_at?->format('M d, Y h:i A') ?? '—' }}
                    </p>

                </div>


                <div>

                    <p class="text-xs text-slate-500">
                        Report Submitted
                    </p>

                    <p class="text-sm font-semibold text-slate-800 mt-1">
                        {{ $maintenanceReport->submitted_at?->format('M d, Y h:i A') ?? '—' }}
                    </p>

                </div>


                <div>

                    <p class="text-xs text-slate-500">
                        Revision
                    </p>

                    <p class="text-sm font-semibold text-slate-800 mt-1">
                        {{ $maintenanceReport->revision_number ?? 0 }}
                    </p>

                    @if ($maintenanceReport->resubmitted_at)
                        <p class="text-xs text-slate-500 mt-1">
                            Resubmitted
                            {{ $maintenanceReport->resubmitted_at->format('M d, Y h:i A') }}
                        </p>
                    @endif

                </div>

            </div>

        </div>


        {{-- Accomplishment Report --}}
        <div class="bg-white rounded-2xl
                   border border-slate-200 shadow-sm overflow-hidden">

            <div
                class="px-5 sm:px-6 py-5
                       border-b border-emerald-100
                       bg-emerald-50/40">

                <div class="flex items-start gap-3">

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-emerald-100 text-emerald-700
                               flex items-center justify-center shrink-0">
                        <i class="fas fa-file-circle-check"></i>
                    </div>

                    <div>

                        <h2 class="font-semibold text-slate-900">
                            Service Accomplishment Report
                        </h2>

                        <p class="text-xs text-slate-500 mt-1">
                            Review the maintenance findings and submitted documentation.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-5 sm:p-6 space-y-6">

                <div class="grid md:grid-cols-2 gap-6">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Diagnosis / Findings
                        </p>

                        <p class="text-sm text-slate-700 whitespace-pre-line mt-2">
                            {{ $maintenanceReport->diagnosis ?: 'No diagnosis recorded.' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Root Cause
                        </p>

                        <p class="text-sm text-slate-700 whitespace-pre-line mt-2">
                            {{ $maintenanceReport->root_cause ?: 'No root cause recorded.' }}
                        </p>

                    </div>

                </div>


                <div class="border-t border-slate-100 pt-5">

                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Materials / Parts
                    </p>

                    <p class="text-sm text-slate-700 whitespace-pre-line mt-2">
                        {{ $maintenanceReport->materials_parts ?: 'No materials or parts recorded.' }}
                    </p>

                </div>


                <div class="border-t border-slate-100 pt-5">

                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Plumber Notes
                    </p>

                    <p class="text-sm text-slate-700 whitespace-pre-line mt-2">
                        {{ $maintenanceReport->technician_notes ?: 'No additional plumber notes.' }}
                    </p>

                </div>

            </div>

        </div>


        {{-- Maintenance Evidence --}}
        <div class="bg-white rounded-2xl
                   border border-slate-200 shadow-sm overflow-hidden">

            <div
                class="px-5 sm:px-6 py-5
                       border-b border-amber-100
                       bg-amber-50/40">

                <div class="flex items-start gap-3">

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-amber-100 text-amber-700
                               flex items-center justify-center shrink-0">
                        <i class="fas fa-images"></i>
                    </div>

                    <div>

                        <h2 class="font-semibold text-slate-900">
                            Maintenance Evidence
                        </h2>

                        <p class="text-xs text-slate-500 mt-1">
                            Before and after photos submitted with the accomplishment report.
                        </p>

                    </div>

                </div>

            </div>


            <div class="grid md:grid-cols-2 gap-5 p-5 sm:p-6">

                <div>

                    <p class="text-sm font-semibold text-slate-800 mb-3">
                        Before Maintenance
                    </p>

                    @if ($maintenanceReport->before_photo)
                        <a href="{{ asset('storage/' . $maintenanceReport->before_photo) }}" target="_blank"
                            rel="noopener noreferrer">

                            <img src="{{ asset('storage/' . $maintenanceReport->before_photo) }}"
                                alt="Before maintenance"
                                class="w-full h-72
                                       object-contain
                                       rounded-xl border
                                       bg-slate-50">

                        </a>
                    @else
                        <div
                            class="h-52 rounded-xl border
                                   bg-slate-50
                                   flex items-center justify-center
                                   text-sm text-slate-400">
                            No before photo submitted.
                        </div>
                    @endif

                </div>


                <div>

                    <p class="text-sm font-semibold text-slate-800 mb-3">
                        After Maintenance
                    </p>

                    @if ($maintenanceReport->after_photo)
                        <a href="{{ asset('storage/' . $maintenanceReport->after_photo) }}" target="_blank"
                            rel="noopener noreferrer">

                            <img src="{{ asset('storage/' . $maintenanceReport->after_photo) }}" alt="After maintenance"
                                class="w-full h-72
                                       object-contain
                                       rounded-xl border
                                       bg-slate-50">

                        </a>
                    @else
                        <div
                            class="h-52 rounded-xl border
                                   bg-slate-50
                                   flex items-center justify-center
                                   text-sm text-slate-400">
                            No after photo submitted.
                        </div>
                    @endif

                </div>

            </div>

        </div>


        {{-- Previous Review --}}
        @if ($maintenanceReport->review_remarks)

            <div
                class="rounded-2xl border
                       {{ $maintenanceReport->review_status === 'Returned'
                           ? 'border-red-200 bg-red-50'
                           : 'border-green-200 bg-green-50' }}
                       p-5 sm:p-6">

                <div class="flex items-start gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                               {{ $maintenanceReport->review_status === 'Returned' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}
                               flex items-center justify-center shrink-0">

                        <i
                            class="fas
                                   {{ $maintenanceReport->review_status === 'Returned' ? 'fa-rotate-left' : 'fa-clipboard-check' }}"></i>

                    </div>


                    <div class="min-w-0">

                        <h2
                            class="font-semibold
                                   {{ $maintenanceReport->review_status === 'Returned' ? 'text-red-800' : 'text-green-800' }}">
                            {{ $maintenanceReport->review_status === 'Returned' ? 'Correction Remarks' : 'Review Remarks' }}
                        </h2>


                        <p
                            class="text-sm whitespace-pre-line mt-2
                                   {{ $maintenanceReport->review_status === 'Returned' ? 'text-red-700' : 'text-green-700' }}">
                            {{ $maintenanceReport->review_remarks }}
                        </p>


                        @if ($maintenanceReport->reviewer)

                            <p
                                class="text-xs mt-3
                                       {{ $maintenanceReport->review_status === 'Returned' ? 'text-red-600' : 'text-green-600' }}">

                                Reviewed by
                                {{ $maintenanceReport->reviewer->full_name }}

                                @if ($maintenanceReport->reviewed_at)
                                    •
                                    {{ $maintenanceReport->reviewed_at->format('M d, Y h:i A') }}
                                @endif

                            </p>

                        @endif

                    </div>

                </div>

            </div>

        @endif


        {{-- Pending Review Actions --}}
        @if ($maintenanceReport->review_status === 'Pending Review')
            <div class="grid lg:grid-cols-2 gap-5">

                {{-- Approve --}}
                <div
                    class="bg-white rounded-2xl
                           border border-green-200
                           shadow-sm overflow-hidden">

                    <div
                        class="px-5 sm:px-6 py-5
                               border-b border-green-100
                               bg-green-50/50">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-10 h-10 rounded-xl
                                       bg-green-100 text-green-600
                                       flex items-center justify-center shrink-0">
                                <i class="fas fa-circle-check"></i>
                            </div>

                            <div>

                                <h2 class="font-semibold text-green-800">
                                    Approve Accomplishment
                                </h2>

                                <p class="text-sm text-slate-500 mt-1">
                                    Confirm that the maintenance work and submitted documentation are acceptable.
                                </p>

                            </div>

                        </div>

                    </div>


                    <form method="POST"
                        action="{{ route('maintenance-manager.maintenance-reviews.approve', $maintenanceReport) }}"
                        class="p-5 sm:p-6"
                        onsubmit="return confirm('Approve this accomplishment report? The complaint will remain Accomplished until it is finalized.');">
                        @csrf

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Review Remarks

                            <span class="font-normal text-slate-400">
                                (Optional)
                            </span>
                        </label>

                        <textarea name="review_remarks" rows="4"
                            class="w-full rounded-xl
                                   border-slate-300 text-sm
                                   focus:border-green-500
                                   focus:ring-green-500"
                            placeholder="Optional remarks about the accomplishment...">{{ old('review_remarks') }}</textarea>

                        <button type="submit"
                            class="mt-4 w-full
                                   inline-flex items-center justify-center
                                   gap-2 px-5 py-3 rounded-xl
                                   bg-green-600 text-white
                                   font-semibold
                                   hover:bg-green-700 transition">
                            <i class="fas fa-check"></i>
                            Approve Report
                        </button>

                    </form>

                </div>


                {{-- Return --}}
                <div
                    class="bg-white rounded-2xl
                           border border-red-200
                           shadow-sm overflow-hidden">

                    <div
                        class="px-5 sm:px-6 py-5
                               border-b border-red-100
                               bg-red-50/50">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-10 h-10 rounded-xl
                                       bg-red-100 text-red-600
                                       flex items-center justify-center shrink-0">
                                <i class="fas fa-rotate-left"></i>
                            </div>

                            <div>

                                <h2 class="font-semibold text-red-800">
                                    Return for Correction
                                </h2>

                                <p class="text-sm text-slate-500 mt-1">
                                    Return only the report for correction. The maintenance work remains accomplished.
                                </p>

                            </div>

                        </div>

                    </div>


                    <form method="POST"
                        action="{{ route('maintenance-manager.maintenance-reviews.return', $maintenanceReport) }}"
                        class="p-5 sm:p-6"
                        onsubmit="return confirm('Return this accomplishment report for correction?');">
                        @csrf

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Correction Required
                        </label>

                        <textarea name="review_remarks" rows="4" required
                            class="w-full rounded-xl
                                   border-slate-300 text-sm
                                   focus:border-red-500
                                   focus:ring-red-500"
                            placeholder="Describe what needs to be corrected...">{{ old('review_remarks') }}</textarea>

                        <button type="submit"
                            class="mt-4 w-full
                                   inline-flex items-center justify-center
                                   gap-2 px-5 py-3 rounded-xl
                                   bg-red-600 text-white
                                   font-semibold
                                   hover:bg-red-700 transition">
                            <i class="fas fa-rotate-left"></i>
                            Return Report
                        </button>

                    </form>

                </div>

            </div>


            {{-- Returned --}}
        @elseif ($maintenanceReport->review_status === 'Returned')
            <div class="rounded-2xl border border-red-200
                       bg-red-50 p-5 sm:p-6">

                <div class="flex items-start gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                               bg-red-100 text-red-600
                               flex items-center justify-center shrink-0">
                        <i class="fas fa-clock-rotate-left"></i>
                    </div>

                    <div>

                        <h2 class="font-semibold text-red-800">
                            Waiting for Report Correction
                        </h2>

                        <p class="text-sm text-red-700 mt-1">
                            This accomplishment report was returned for correction.
                            Any assigned plumber can revise and resubmit the shared report.
                            The complaint remains Accomplished while the report is being corrected.
                        </p>

                    </div>

                </div>

            </div>


            {{-- Approved but not Closed --}}
        @elseif ($isApproved && !$isClosed)
            <div class="rounded-2xl border border-green-200
                       bg-green-50 p-5 sm:p-6">

                <div class="flex flex-col lg:flex-row
                           lg:items-center lg:justify-between gap-5">

                    <div class="flex items-start gap-3">

                        <div
                            class="w-10 h-10 rounded-xl
                                   bg-green-100 text-green-600
                                   flex items-center justify-center shrink-0">
                            <i class="fas fa-circle-check"></i>
                        </div>

                        <div>

                            <h2 class="font-semibold text-green-800">
                                Accomplishment Approved
                            </h2>

                            <p class="text-sm text-green-700 mt-1">
                                The accomplishment report has been approved.
                                The complaint remains Accomplished until you finalize and close the maintenance workflow.
                            </p>

                        </div>

                    </div>


                    <form method="POST"
                        action="{{ route('maintenance-manager.maintenance-reviews.close', $maintenanceReport) }}"
                        onsubmit="return confirm('Finalize and close this complaint? This will complete the maintenance workflow.');"
                        class="shrink-0">
                        @csrf

                        <button type="submit"
                            class="inline-flex items-center justify-center
                                   gap-2 px-5 py-3 rounded-xl
                                   bg-slate-900 text-white
                                   text-sm font-semibold
                                   hover:bg-slate-800 transition">
                            <i class="fas fa-lock"></i>
                            Finalize & Close Complaint
                        </button>

                    </form>

                </div>

            </div>


            {{-- Closed --}}
        @elseif ($isApproved && $isClosed)
            <div class="rounded-2xl border border-slate-200
                       bg-slate-50 p-5 sm:p-6">

                <div class="flex items-start gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                               bg-white border border-slate-200
                               text-slate-600
                               flex items-center justify-center shrink-0">
                        <i class="fas fa-lock"></i>
                    </div>

                    <div>

                        <h2 class="font-semibold text-slate-800">
                            Maintenance Workflow Closed
                        </h2>

                        <p class="text-sm text-slate-600 mt-1">
                            The accomplishment report was approved and this complaint has been finalized by management.
                        </p>

                    </div>

                </div>

            </div>
        @endif

    </div>

@endsection
