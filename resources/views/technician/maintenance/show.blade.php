@extends('technician.layouts.app')

@section('title', 'Accomplishment Report')

@section('content')

    @php
        $report = $complaint->maintenanceReport;

        $reviewClasses = match ($report?->review_status) {
            'Pending Review' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Returned' => 'bg-red-50 text-red-700 border-red-200',
            'Approved' => 'bg-green-50 text-green-700 border-green-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    @endphp


    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>

                <div class="flex items-center gap-2 text-sm text-gray-500">

                    <a href="{{ route('technician.complaints.show', $complaint) }}" class="hover:text-sky-600 transition">
                        Complaint Details
                    </a>

                    <i class="fas fa-chevron-right text-[10px]"></i>

                    <span>
                        {{ $complaint->complaint_no }}
                    </span>

                </div>


                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">
                    Service Accomplishment Report
                </h1>


                <p class="text-sm text-gray-500 mt-1">
                    {{ $complaint->category?->name ?? 'Water Service Concern' }}
                </p>

            </div>


            <div class="flex flex-wrap gap-2">

                <a href="{{ route('technician.complaints.show', $complaint) }}"
                    class="inline-flex items-center justify-center gap-2
                           px-4 py-2.5 rounded-xl
                           border border-gray-300
                           bg-white text-gray-700
                           font-semibold text-sm
                           hover:bg-gray-50 transition">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>


                <a href="{{ route('technician.maintenance-reports.print', $complaint) }}" target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2
                           px-4 py-2.5 rounded-xl
                           bg-gray-800 text-white
                           font-semibold text-sm
                           hover:bg-gray-900 transition">
                    <i class="fas fa-print"></i>
                    Print
                </a>

            </div>

        </div>


        {{-- Success Message --}}
        @if (session('success'))
            <div
                class="rounded-xl border border-green-200
                       bg-green-50 px-4 py-3
                       text-sm text-green-800
                       flex items-start gap-3">
                <i class="fas fa-circle-check mt-0.5"></i>

                <span>
                    {{ session('success') }}
                </span>
            </div>
        @endif


        {{-- Info Message --}}
        @if (session('info'))
            <div
                class="rounded-xl border border-blue-200
                       bg-blue-50 px-4 py-3
                       text-sm text-blue-800
                       flex items-start gap-3">
                <i class="fas fa-circle-info mt-0.5"></i>

                <span>
                    {{ session('info') }}
                </span>
            </div>
        @endif


        {{-- Review Status Alert --}}
        @if ($report?->review_status === 'Pending Review')

            <div class="rounded-2xl border border-amber-200
                       bg-amber-50 p-5">

                <div class="flex items-start gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                               bg-amber-100 text-amber-600
                               flex items-center justify-center shrink-0">
                        <i class="fas fa-clock"></i>
                    </div>


                    <div>

                        <p class="font-semibold text-amber-900">
                            Waiting for Manager Review
                        </p>

                        <p class="text-sm text-amber-800 mt-1">
                            Your accomplishment report has been submitted.
                            The Maintenance Manager will review the report before
                            the complaint can be finalized.
                        </p>

                    </div>

                </div>

            </div>
        @elseif ($report?->review_status === 'Returned')
            <div class="rounded-2xl border border-red-200
                       bg-red-50 p-5">

                <div class="flex flex-col sm:flex-row
                            sm:items-start sm:justify-between gap-4">

                    <div class="flex items-start gap-3">

                        <div
                            class="w-10 h-10 rounded-xl
                                   bg-red-100 text-red-600
                                   flex items-center justify-center shrink-0">
                            <i class="fas fa-rotate-left"></i>
                        </div>


                        <div>

                            <p class="font-semibold text-red-900">
                                Correction Required
                            </p>

                            <p class="text-sm text-red-800 mt-1">
                                The Maintenance Manager returned this
                                accomplishment report for correction.
                            </p>


                            @if ($report->review_remarks)
                                <div
                                    class="mt-3 rounded-xl
                                           border border-red-200
                                           bg-white/70 p-3">

                                    <p
                                        class="text-xs uppercase
                                               tracking-wide font-semibold
                                               text-red-700">
                                        Manager Remarks
                                    </p>

                                    <p
                                        class="text-sm text-red-900
                                               mt-1 whitespace-pre-line">
                                        {{ $report->review_remarks }}
                                    </p>

                                </div>
                            @endif

                        </div>

                    </div>


                    <a href="{{ route('technician.maintenance-reports.create', $complaint) }}"
                        class="inline-flex items-center justify-center gap-2
                               px-4 py-2.5 rounded-xl
                               bg-red-600 text-white
                               text-sm font-semibold
                               hover:bg-red-700 transition shrink-0">
                        <i class="fas fa-pen-to-square"></i>
                        Revise Report
                    </a>

                </div>

            </div>
        @elseif ($report?->review_status === 'Approved')
            <div class="rounded-2xl border border-green-200
                       bg-green-50 p-5">

                <div class="flex items-start gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                               bg-green-100 text-green-600
                               flex items-center justify-center shrink-0">
                        <i class="fas fa-circle-check"></i>
                    </div>


                    <div>

                        <p class="font-semibold text-green-900">
                            Accomplishment Report Approved
                        </p>

                        <p class="text-sm text-green-800 mt-1">
                            This report has been reviewed and approved
                            by the Maintenance Manager.
                        </p>


                        @if ($report->reviewed_at)
                            <p class="text-xs text-green-700 mt-2">
                                Reviewed
                                {{ $report->reviewed_at->format('M d, Y h:i A') }}
                            </p>
                        @endif

                    </div>

                </div>

            </div>

        @endif


        {{-- Complaint Summary --}}
        <div class="bg-white rounded-2xl
                   border border-gray-200 shadow-sm overflow-hidden">

            <div class="p-5 sm:p-6">

                <div class="flex flex-col md:flex-row
                           md:items-center md:justify-between gap-4">

                    <div>

                        <div class="flex flex-wrap items-center gap-2">

                            <span class="text-lg font-bold text-sky-700">
                                {{ $complaint->complaint_no }}
                            </span>


                            <span
                                class="inline-flex items-center
                                       px-2.5 py-1 rounded-full
                                       bg-green-50 text-green-700
                                       border border-green-200
                                       text-xs font-semibold">
                                Accomplished
                            </span>

                        </div>


                        <p class="font-semibold text-gray-900 mt-2">
                            {{ $complaint->category?->name ?? 'Water Service Concern' }}
                        </p>


                        <p class="text-sm text-gray-500 mt-1">
                            {{ $complaint->address ?: 'No service location recorded.' }}
                        </p>

                    </div>


                    <div class="md:text-right">

                        <p class="text-xs text-gray-500">
                            Report Status
                        </p>

                        <span
                            class="mt-1 inline-flex items-center
                                   px-3 py-1.5 rounded-full
                                   border text-xs font-semibold
                                   {{ $reviewClasses }}">
                            {{ $report?->review_status ?? 'Draft' }}
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main Report Content --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Diagnosis --}}
                <div
                    class="bg-white rounded-2xl
                           border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-sky-100 bg-sky-50/40">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-11 h-11 rounded-xl
                                       bg-sky-100 text-sky-700
                                       flex items-center justify-center shrink-0">
                                <i class="fas fa-stethoscope"></i>
                            </div>

                            <div>

                                <h2 class="font-semibold text-gray-900">
                                    Diagnosis / Findings
                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Findings identified during maintenance.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5 sm:p-6">

                        <p class="text-gray-700 leading-relaxed
                                   whitespace-pre-line">
                            {{ $report->diagnosis ?: 'No diagnosis or findings recorded.' }}
                        </p>

                    </div>

                </div>


                {{-- Root Cause --}}
                <div
                    class="bg-white rounded-2xl
                           border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-amber-100 bg-amber-50/40">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-11 h-11 rounded-xl
                                       bg-amber-100 text-amber-700
                                       flex items-center justify-center shrink-0">
                                <i class="fas fa-magnifying-glass"></i>
                            </div>

                            <div>

                                <h2 class="font-semibold text-gray-900">
                                    Root Cause
                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Identified cause of the reported problem.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5 sm:p-6">

                        <p class="text-gray-700 leading-relaxed
                                   whitespace-pre-line">
                            {{ $report->root_cause ?: 'No root cause recorded.' }}
                        </p>

                    </div>

                </div>


                {{-- Materials / Parts --}}
                <div
                    class="bg-white rounded-2xl
                           border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-orange-100 bg-orange-50/40">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-11 h-11 rounded-xl
                                       bg-orange-100 text-orange-700
                                       flex items-center justify-center shrink-0">
                                <i class="fas fa-toolbox"></i>
                            </div>

                            <div>

                                <h2 class="font-semibold text-gray-900">
                                    Materials / Parts
                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Materials or parts removed, replaced, or installed.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5 sm:p-6">

                        <p class="text-gray-700 leading-relaxed
                                   whitespace-pre-line">
                            {{ $report->materials_parts ?: 'None recorded.' }}
                        </p>

                    </div>

                </div>


                {{-- Plumber Notes --}}
                <div
                    class="bg-white rounded-2xl
                           border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-violet-100 bg-violet-50/40">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-11 h-11 rounded-xl
                                       bg-violet-100 text-violet-700
                                       flex items-center justify-center shrink-0">
                                <i class="fas fa-note-sticky"></i>
                            </div>

                            <div>

                                <h2 class="font-semibold text-gray-900">
                                    Plumber Notes
                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Additional notes recorded by the maintenance team.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5 sm:p-6">

                        <p class="text-gray-700 leading-relaxed
                                   whitespace-pre-line">
                            {{ $report->technician_notes ?: 'No additional notes.' }}
                        </p>

                    </div>

                </div>


                {{-- Photos --}}
                <div
                    class="bg-white rounded-2xl
                           border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-emerald-100 bg-emerald-50/40">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-11 h-11 rounded-xl
                                       bg-emerald-100 text-emerald-700
                                       flex items-center justify-center shrink-0">
                                <i class="fas fa-images"></i>
                            </div>

                            <div>

                                <h2 class="font-semibold text-gray-900">
                                    Before & After Photos
                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Photo documentation of the maintenance activity.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5 sm:p-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Before --}}
                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-500 mb-3">
                                    Before Maintenance
                                </p>


                                @if ($report->before_photo)
                                    <a href="{{ asset('storage/' . $report->before_photo) }}" target="_blank"
                                        rel="noopener noreferrer">

                                        <img src="{{ asset('storage/' . $report->before_photo) }}"
                                            alt="Before maintenance"
                                            class="w-full h-72
                                                   object-cover rounded-xl
                                                   border border-gray-200">

                                    </a>
                                @else
                                    <div
                                        class="h-72 rounded-xl
                                               bg-gray-50
                                               border border-gray-200
                                               flex flex-col items-center
                                               justify-center text-gray-400">
                                        <i class="fas fa-image text-2xl"></i>

                                        <span class="text-sm mt-2">
                                            No before photo
                                        </span>
                                    </div>
                                @endif

                            </div>


                            {{-- After --}}
                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-500 mb-3">
                                    After Maintenance
                                </p>


                                @if ($report->after_photo)
                                    <a href="{{ asset('storage/' . $report->after_photo) }}" target="_blank"
                                        rel="noopener noreferrer">

                                        <img src="{{ asset('storage/' . $report->after_photo) }}" alt="After maintenance"
                                            class="w-full h-72
                                                   object-cover rounded-xl
                                                   border border-gray-200">

                                    </a>
                                @else
                                    <div
                                        class="h-72 rounded-xl
                                               bg-gray-50
                                               border border-gray-200
                                               flex flex-col items-center
                                               justify-center text-gray-400">
                                        <i class="fas fa-image text-2xl"></i>

                                        <span class="text-sm mt-2">
                                            No after photo
                                        </span>
                                    </div>
                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Sidebar --}}
            <div class="space-y-6">

                {{-- Report Information --}}
                <div
                    class="bg-white rounded-2xl
                           border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-violet-100 bg-violet-50/40">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-11 h-11 rounded-xl
                                       bg-violet-100 text-violet-700
                                       flex items-center justify-center shrink-0">
                                <i class="fas fa-file-lines"></i>
                            </div>

                            <div>

                                <h2 class="font-semibold text-gray-900">
                                    Report Information
                                </h2>

                                <p class="text-xs text-gray-500 mt-1">
                                    Submission and maintenance report details.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5 space-y-5">

                        {{-- Submitted By --}}
                        <div>

                            <p class="text-xs text-gray-500">
                                Report Submitted By
                            </p>

                            <div class="flex items-center gap-3 mt-2">

                                <div
                                    class="w-9 h-9 rounded-full
                                           bg-violet-100 text-violet-700
                                           flex items-center justify-center
                                           font-bold shrink-0">
                                    {{ strtoupper(substr($report->technician?->first_name ?? 'P', 0, 1)) }}
                                </div>

                                <div class="min-w-0">

                                    <p class="font-semibold text-gray-900 truncate">
                                        {{ $report->technician?->full_name ?? 'N/A' }}
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        Maintenance Plumber
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="border-t border-gray-100 pt-4">

                            <p class="text-xs text-gray-500">
                                Maintenance Started
                            </p>

                            <p class="font-medium text-gray-900 mt-1">
                                {{ $report->started_at?->format('M d, Y h:i A') ?? 'N/A' }}
                            </p>

                        </div>


                        <div class="border-t border-gray-100 pt-4">

                            <p class="text-xs text-gray-500">
                                Report Submitted
                            </p>

                            <p class="font-medium text-gray-900 mt-1">
                                {{ $report->submitted_at?->format('M d, Y h:i A') ?? 'N/A' }}
                            </p>

                        </div>


                        @if ($report->resubmitted_at)
                            <div class="border-t border-gray-100 pt-4">

                                <p class="text-xs text-gray-500">
                                    Last Resubmitted
                                </p>

                                <p class="font-medium text-gray-900 mt-1">
                                    {{ $report->resubmitted_at->format('M d, Y h:i A') }}
                                </p>

                            </div>
                        @endif


                        @if (($report->revision_number ?? 0) > 0)
                            <div class="border-t border-gray-100 pt-4">

                                <p class="text-xs text-gray-500">
                                    Revision
                                </p>

                                <p class="font-medium text-gray-900 mt-1">
                                    {{ $report->revision_number }}
                                </p>

                            </div>
                        @endif

                    </div>

                </div>


                {{-- Maintenance Team --}}
                <div
                    class="bg-white rounded-2xl
                           border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-blue-100 bg-blue-50/40">

                        <div class="flex items-center justify-between gap-3">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-11 h-11 rounded-xl
                                           bg-blue-100 text-blue-700
                                           flex items-center justify-center shrink-0">
                                    <i class="fas fa-users-gear"></i>
                                </div>

                                <div>

                                    <h2 class="font-semibold text-gray-900">
                                        Maintenance Team
                                    </h2>

                                    <p class="text-xs text-gray-500 mt-1">
                                        Plumbers who worked on this maintenance activity.
                                    </p>

                                </div>

                            </div>


                            <span
                                class="inline-flex items-center justify-center
                                       min-w-9 h-9 px-2.5 rounded-full
                                       bg-blue-100 text-blue-700
                                       text-sm font-bold">
                                {{ $complaint->technicians->count() }}
                            </span>

                        </div>

                    </div>


                    <div class="p-5">

                        <div class="space-y-3">

                            @foreach ($complaint->technicians as $technician)
                                <div
                                    class="flex items-center gap-3 p-3 rounded-xl
                                           {{ $technician->id === auth()->id() ? 'bg-sky-50 border border-sky-200' : 'bg-gray-50 border border-gray-100' }}">

                                    <div
                                        class="w-10 h-10 rounded-full
                                               bg-sky-100 text-sky-700
                                               flex items-center justify-center
                                               font-bold shrink-0">
                                        {{ strtoupper(substr($technician->first_name ?? 'P', 0, 1)) }}
                                    </div>


                                    <div class="min-w-0 flex-1">

                                        <div class="flex flex-wrap items-center gap-2">

                                            <p class="font-semibold text-gray-900 truncate">
                                                {{ $technician->full_name }}
                                            </p>


                                            @if ($technician->id === auth()->id())
                                                <span
                                                    class="px-2 py-0.5 rounded-full
                                                           bg-sky-600 text-white
                                                           text-[10px] font-bold">
                                                    YOU
                                                </span>
                                            @endif


                                            @if ($report->technician_id === $technician->id)
                                                <span
                                                    class="px-2 py-0.5 rounded-full
                                                           bg-violet-100 text-violet-700
                                                           text-[10px] font-bold">
                                                    REPORT SUBMITTER
                                                </span>
                                            @endif

                                        </div>


                                        <p class="text-xs text-gray-500 mt-0.5">
                                            Maintenance Plumber
                                        </p>


                                        @if ($technician->pivot?->status)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $technician->pivot->status }}
                                            </p>
                                        @endif

                                    </div>

                                </div>
                            @endforeach

                        </div>

                    </div>

                </div>


                {{-- Management Review --}}
                @if ($report->review_status === 'Approved' || $report->reviewed_at)

                    <div
                        class="bg-white rounded-2xl
                               border border-gray-200 shadow-sm overflow-hidden">

                        <div class="px-5 py-5 border-b border-green-100 bg-green-50/40">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-11 h-11 rounded-xl
                                           bg-green-100 text-green-700
                                           flex items-center justify-center shrink-0">
                                    <i class="fas fa-user-shield"></i>
                                </div>

                                <div>

                                    <h2 class="font-semibold text-gray-900">
                                        Management Review
                                    </h2>

                                    <p class="text-xs text-gray-500 mt-1">
                                        Maintenance Manager review and decision.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="p-5 space-y-4">

                            <div>

                                <p class="text-xs text-gray-500">
                                    Review Result
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $report->review_status }}
                                </p>

                            </div>


                            @if ($report->reviewer)
                                <div class="border-t border-gray-100 pt-4">

                                    <p class="text-xs text-gray-500">
                                        Reviewed By
                                    </p>

                                    <p class="font-medium text-gray-900 mt-1">
                                        {{ $report->reviewer->full_name }}
                                    </p>

                                </div>
                            @endif


                            @if ($report->reviewed_at)
                                <div class="border-t border-gray-100 pt-4">

                                    <p class="text-xs text-gray-500">
                                        Reviewed
                                    </p>

                                    <p class="font-medium text-gray-900 mt-1">
                                        {{ $report->reviewed_at->format('M d, Y h:i A') }}
                                    </p>

                                </div>
                            @endif


                            @if ($report->review_remarks)
                                <div class="border-t border-gray-100 pt-4">

                                    <p class="text-xs text-gray-500">
                                        Manager Remarks
                                    </p>

                                    <p
                                        class="text-sm text-gray-700
                                               mt-1 whitespace-pre-line">
                                        {{ $report->review_remarks }}
                                    </p>

                                </div>
                            @endif

                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>

@endsection
