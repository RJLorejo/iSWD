<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Complaint Report - {{ $complaint->complaint_no }}
    </title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            background: #f3f4f6;
        }

        @media print {

            body {
                background: white !important;
            }

            .no-print {
                display: none !important;
            }

            .print-container {
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .report-card {
                box-shadow: none !important;
                border: none !important;
            }

        }
    </style>

</head>


<body>

    <div class="max-w-4xl mx-auto py-8 px-4 print-container">


        {{-- ========================================================= --}}
        {{-- PRINT CONTROLS --}}
        {{-- ========================================================= --}}

        <div class="no-print mb-5 flex items-center justify-between gap-3">

            <a href="{{ route('maintenance-manager.complaints.show', $complaint) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5
                   rounded-xl bg-white border border-gray-200
                   text-sm font-semibold text-gray-700 hover:bg-gray-50">

                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>

                Back

            </a>


            <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2.5
                   rounded-xl bg-blue-600 text-white
                   text-sm font-semibold hover:bg-blue-700">

                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6v-8z" />
                </svg>

                Print Report

            </button>

        </div>


        {{-- ========================================================= --}}
        {{-- REPORT --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 report-card overflow-hidden">


            {{-- ===================================================== --}}
            {{-- REPORT HEADER --}}
            {{-- ===================================================== --}}

            <div class="px-8 py-7 border-b border-gray-200">

                <div class="flex items-start justify-between gap-5">

                    <div>

                        <div class="flex items-center gap-3">

                            <div
                                class="w-12 h-12 rounded-xl bg-blue-600
                                    text-white flex items-center
                                    justify-center">

                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 10h8M8 14h5m-9 6h16a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>

                            </div>


                            <div>

                                <h1 class="text-xl font-bold text-gray-900">
                                    Sagay Water District
                                </h1>

                                <p class="text-sm text-gray-500">
                                    Maintenance Complaint Report
                                </p>

                            </div>

                        </div>

                    </div>


                    @php

                        $statusClasses = match ($complaint->status) {
                            'Verified' => 'bg-blue-50 text-blue-700 border-blue-200',

                            'Assigned' => 'bg-indigo-50 text-indigo-700 border-indigo-200',

                            'In Progress' => 'bg-amber-50 text-amber-700 border-amber-200',

                            'Completed' => 'bg-green-50 text-green-700 border-green-200',

                            'Closed' => 'bg-gray-100 text-gray-700 border-gray-200',

                            default => 'bg-gray-50 text-gray-600 border-gray-200',
                        };

                    @endphp


                    <span
                        class="inline-flex items-center gap-2 px-3 py-2
                             rounded-full border text-xs font-bold
                             {{ $statusClasses }}">

                        <span class="w-2 h-2 rounded-full bg-current"></span>

                        {{ $complaint->status }}

                    </span>

                </div>


                <div class="mt-6">

                    <p class="text-xs uppercase tracking-wider
                          font-semibold text-gray-400">
                        Complaint Number
                    </p>

                    <p class="mt-1 text-lg font-bold text-blue-600">
                        {{ $complaint->complaint_no }}
                    </p>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- COMPLAINT SUMMARY --}}
            {{-- ===================================================== --}}

            <div class="px-8 py-7 border-b border-gray-200">

                <h2 class="text-base font-bold text-gray-900">
                    Complaint Information
                </h2>


                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">

                    <div class="sm:col-span-2">

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Subject
                        </p>

                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $complaint->subject }}
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Category
                        </p>

                        <p class="mt-1 text-sm text-gray-800">
                            {{ $complaint->category?->name ?? 'Uncategorized' }}
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Priority
                        </p>

                        <p class="mt-1 text-sm text-gray-800">
                            {{ $complaint->priority ?? 'Not specified' }}
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Submitted
                        </p>

                        <p class="mt-1 text-sm text-gray-800">
                            {{ $complaint->created_at?->format('F d, Y h:i A') }}
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Last Updated
                        </p>

                        <p class="mt-1 text-sm text-gray-800">
                            {{ $complaint->updated_at?->format('F d, Y h:i A') }}
                        </p>

                    </div>

                </div>


                {{-- DESCRIPTION --}}
                <div class="mt-6">

                    <p class="text-xs uppercase tracking-wide
                          font-semibold text-gray-400">
                        Description
                    </p>

                    <div class="mt-2 p-4 rounded-xl bg-gray-50
                            border border-gray-100">

                        <p class="text-sm text-gray-700 leading-6 whitespace-pre-line">
                            {{ $complaint->description }}
                        </p>

                    </div>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- CONSUMER INFORMATION --}}
            {{-- ===================================================== --}}

            <div class="px-8 py-7 border-b border-gray-200">

                <h2 class="text-base font-bold text-gray-900">
                    Consumer Information
                </h2>


                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">

                    <div>

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Consumer Name
                        </p>

                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? '—') }}
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Account Number
                        </p>

                        <p class="mt-1 text-sm text-gray-800">
                            {{ $complaint->consumer?->account_number ??  '—' }}
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Contact Number
                        </p>

                        <p class="mt-1 text-sm text-gray-800">
                            {{ $complaint->complainant_phone ?? ($complaint->consumer?->phone ?? '—') }}
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Email
                        </p>

                        <p class="mt-1 text-sm text-gray-800">
                            {{ $complaint->consumer?->email ?? '—' }}
                        </p>

                    </div>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- LOCATION --}}
            {{-- ===================================================== --}}

            <div class="px-8 py-7 border-b border-gray-200">

                <h2 class="text-base font-bold text-gray-900">
                    Service / Complaint Location
                </h2>


                <div class="mt-5">

                    <p class="text-xs uppercase tracking-wide
                          font-semibold text-gray-400">
                        Address
                    </p>

                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ $complaint->address ?? '—' }}
                    </p>

                </div>


                @if ($complaint->landmark)
                    <div class="mt-4">

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Landmark
                        </p>

                        <p class="mt-1 text-sm text-gray-700">
                            {{ $complaint->landmark }}
                        </p>

                    </div>
                @endif


                @if ($complaint->latitude && $complaint->longitude)
                    <div class="mt-4 grid grid-cols-2 gap-4">

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  font-semibold text-gray-400">
                                Latitude
                            </p>

                            <p class="mt-1 text-sm text-gray-700">
                                {{ number_format($complaint->latitude, 6) }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                  font-semibold text-gray-400">
                                Longitude
                            </p>

                            <p class="mt-1 text-sm text-gray-700">
                                {{ number_format($complaint->longitude, 6) }}
                            </p>

                        </div>

                    </div>
                @endif

            </div>


            {{-- ===================================================== --}}
            {{-- VERIFICATION --}}
            {{-- ===================================================== --}}

            <div class="px-8 py-7 border-b border-gray-200">

                <h2 class="text-base font-bold text-gray-900">
                    Verification Information
                </h2>


                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">

                    <div>

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Verified By
                        </p>

                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $complaint->verifier?->full_name ?? ($complaint->verifiedBy?->full_name ?? '—') }}
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Verification Date
                        </p>

                        <p class="mt-1 text-sm text-gray-800">
                            {{ $complaint->verified_at?->format('F d, Y h:i A') ?? '—' }}
                        </p>

                    </div>

                </div>


                @if ($complaint->verification_reason)
                    <div class="mt-5">

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Verification Notes
                        </p>

                        <p class="mt-2 text-sm text-gray-700 leading-6">
                            {{ $complaint->verification_reason }}
                        </p>

                    </div>
                @endif

            </div>


            {{-- ===================================================== --}}
            {{-- MAINTENANCE TEAM --}}
            {{-- ===================================================== --}}

            <div class="px-8 py-7 border-b border-gray-200">

                <div class="flex items-center justify-between">

                    <div>

                        <h2 class="text-base font-bold text-gray-900">
                            Assigned Maintenance Team
                        </h2>

                        <p class="text-xs text-gray-500 mt-1">
                            Technicians assigned to this complaint
                        </p>

                    </div>


                    <span class="text-xs font-semibold text-gray-500">
                        {{ $complaint->technicians->count() }}
                        {{ $complaint->technicians->count() === 1 ? 'Technician' : 'Technicians' }}
                    </span>

                </div>


                @if ($complaint->technicians->count())

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">

                        @foreach ($complaint->technicians as $technician)
                            <div class="rounded-xl border border-gray-200
                p-4 bg-gray-50">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="w-10 h-10 rounded-xl
                        bg-blue-100 text-blue-700
                        flex items-center justify-center
                        text-xs font-bold shrink-0">

                                        {{ strtoupper(substr($technician->first_name ?? '', 0, 1) . substr($technician->last_name ?? '', 0, 1)) }}

                                    </div>

                                    <div class="min-w-0">

                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $technician->full_name }}
                                        </p>

                                        @if ($technician->employee_id)
                                            <p class="text-xs text-gray-500">
                                                Employee ID: {{ $technician->employee_id }}
                                            </p>
                                        @endif

                                    </div>

                                </div>

                                @if ($technician->pivot?->status)
                                    <div class="mt-3 pt-3 border-t border-gray-200">

                                        <p
                                            class="text-[10px] uppercase tracking-wide
                          font-semibold text-gray-400">
                                            Assignment Status
                                        </p>

                                        <p class="mt-1 text-xs font-semibold text-gray-700">
                                            {{ $technician->pivot->status }}
                                        </p>

                                    </div>
                                @endif

                            </div>
                        @endforeach

                    </div>
                @else
                    <div
                        class="mt-5 rounded-xl border border-dashed
                            border-gray-300 p-5 text-center">

                        <p class="text-sm text-gray-500">
                            No maintenance technician has been assigned.
                        </p>

                    </div>

                @endif

            </div>


            {{-- ===================================================== --}}
            {{-- MAINTENANCE REPORT --}}
            {{-- ===================================================== --}}

            <div class="px-8 py-7 border-b border-gray-200">

                <h2 class="text-base font-bold text-gray-900">
                    Maintenance Report
                </h2>


                @if ($complaint->maintenanceReport)
                    <div class="mt-5 rounded-xl border border-green-200
                            bg-green-50 p-4">

                        <div class="flex items-start gap-3">

                            <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>

                            <div>

                                <p class="text-sm font-semibold text-green-800">
                                    Maintenance report available
                                </p>

                                <p class="text-xs text-green-700 mt-1">
                                    A maintenance report has been submitted
                                    for this complaint.
                                </p>

                            </div>

                        </div>

                    </div>
                @else
                    <div
                        class="mt-5 rounded-xl border border-dashed
                            border-gray-300 p-5 text-center">

                        <p class="text-sm text-gray-500">
                            No maintenance report has been submitted yet.
                        </p>

                    </div>
                @endif

            </div>


            {{-- ===================================================== --}}
            {{-- REPORT FOOTER --}}
            {{-- ===================================================== --}}

            <div class="px-8 py-6 bg-gray-50">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                    <div>

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Report Generated
                        </p>

                        <p class="mt-1 text-sm text-gray-700">
                            {{ now()->format('F d, Y h:i A') }}
                        </p>

                    </div>


                    <div class="sm:text-right">

                        <p
                            class="text-xs uppercase tracking-wide
                              font-semibold text-gray-400">
                            Prepared By
                        </p>

                        <p class="mt-1 text-sm font-semibold text-gray-800">
                            {{ auth()->user()->full_name ?? auth()->user()->email }}
                        </p>

                        <p class="text-xs text-gray-500">
                            Maintenance Manager
                        </p>

                    </div>

                </div>


                <div class="mt-6 pt-5 border-t border-gray-200">

                    <p class="text-[11px] text-gray-400 leading-5">
                        This document is an official system-generated complaint
                        and maintenance monitoring report of Sagay Water District.
                        The information contained herein is based on records
                        maintained within the complaint management system.
                    </p>

                </div>

            </div>

        </div>

    </div>

</body>

</html>
