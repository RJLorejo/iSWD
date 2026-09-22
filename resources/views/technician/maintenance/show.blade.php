@extends('technician.layouts.app')

@section('content')
    @php
        $report = $complaint->maintenanceReport;
    @endphp

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER --}}

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">

            <div>

                <div class="flex items-center gap-3 mb-2">

                    <a href="{{ route('technician.complaints.show', $complaint) }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left"></i>
                    </a>

                    <h1 class="text-2xl font-bold text-gray-900">
                        Maintenance Report
                    </h1>

                </div>

                <p class="text-gray-500">
                    {{ $complaint->complaint_no }}
                    ·
                    {{ $complaint->subject }}
                </p>

            </div>


            <div class="flex gap-3">

                <a href="{{ route('technician.maintenance-reports.print', $complaint) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-gray-800 text-white font-semibold hover:bg-gray-900">
                    <i class="fas fa-print"></i>
                    Print Report
                </a>

            </div>


        </div>

        @if ($complaint->maintenanceReport->review_status === 'Pending Review')
            <div class="px-4 py-3 rounded-xl bg-amber-50 border border-amber-200">

                <div class="flex items-center gap-3">

                    <i class="fas fa-clock text-amber-600"></i>

                    <div>

                        <p class="font-semibold text-amber-800">
                            Report Submitted for Review
                        </p>

                        <p class="text-sm text-amber-700">
                            Your maintenance report is currently waiting for
                            Maintenance Manager / Supervisor validation.
                        </p>

                    </div>

                </div>

            </div>
        @elseif($complaint->maintenanceReport->review_status === 'Returned')
            <div class="px-4 py-4 rounded-xl bg-red-50 border border-red-200">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-exclamation text-red-600 mt-1"></i>

                    <div>

                        <p class="font-semibold text-red-800">
                            Correction Required
                        </p>

                        <p class="text-sm text-red-700 mt-1">
                            The Maintenance Manager / Supervisor returned this
                            report for correction.
                        </p>

                        @if ($complaint->maintenanceReport->review_remarks)
                            <div class="mt-3 p-3 bg-white rounded-lg border border-red-100">

                                <p class="text-xs font-semibold text-red-600 uppercase">
                                    Manager Remarks
                                </p>

                                <p class="mt-1 text-sm text-slate-700 whitespace-pre-line">
                                    {{ $complaint->maintenanceReport->review_remarks }}
                                </p>

                            </div>
                        @endif

                        <a href="{{ route('technician.maintenance-reports.create', $complaint) }}"
                            class="inline-flex items-center gap-2 mt-4 px-4 py-2.5 rounded-xl bg-red-600 text-white font-medium hover:bg-red-700">

                            <i class="fas fa-pen"></i>

                            Correct & Resubmit

                        </a>

                    </div>

                </div>

            </div>
        @elseif($complaint->maintenanceReport->review_status === 'Approved')
            <div class="px-4 py-3 rounded-xl bg-green-50 border border-green-200">

                <div class="flex items-center gap-3">

                    <i class="fas fa-circle-check text-green-600"></i>

                    <div>

                        <p class="font-semibold text-green-800">
                            Maintenance Report Approved
                        </p>

                        <p class="text-sm text-green-700">
                            This maintenance case has been validated by management.
                        </p>

                    </div>

                </div>

            </div>
        @endif


        {{-- STATUS --}}

        <div class="bg-green-50 border border-green-200 rounded-2xl p-5 mb-6">

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">

                    <i class="fas fa-check text-green-600"></i>

                </div>

                <div>

                    <p class="font-bold text-green-800">
                        Maintenance Work Completed
                    </p>

                    <p class="text-sm text-green-700">
                        Maintenance report submitted for management review
                        {{ $report->submitted_at?->format('M d, Y h:i A') }}
                    </p>

                </div>

            </div>

        </div>


        {{-- COMPLAINT INFORMATION --}}

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6">

            <h2 class="text-lg font-bold text-gray-900 mb-5">
                Complaint Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">

                <div>
                    <p class="text-gray-500">Complaint Number</p>
                    <p class="font-semibold text-gray-900">
                        {{ $complaint->complaint_no }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Complaint Type</p>
                    <p class="font-semibold text-gray-900">
                        {{ $complaint->category?->name ?? 'N/A' }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Location</p>
                    <p class="font-semibold text-gray-900">
                        {{ $complaint->address }}
                    </p>
                </div>

            </div>

        </div>

        {{-- ASSIGNED MAINTENANCE TEAM --}}

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 mb-6">

            <h2 class="text-lg font-bold text-gray-900 mb-5">
                Assigned Maintenance Team
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                @foreach ($complaint->technicians as $technician)
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-gray-50 border border-gray-200">

                        <img src="{{ $technician->avatar_url }}" alt="{{ $technician->full_name }}"
                            class="w-10 h-10 rounded-full object-cover">

                        <div class="min-w-0">

                            <p class="font-semibold text-gray-900 truncate">
                                {{ $technician->full_name }}

                                @if ($technician->id === auth()->id())
                                    <span class="ml-1 text-xs text-blue-600">
                                        (You)
                                    </span>
                                @endif
                            </p>

                            <p class="text-xs text-gray-500">
                                Maintenance Technician
                            </p>

                        </div>

                    </div>
                @endforeach

            </div>

        </div>


        {{-- REPORT DETAILS --}}

        <div class="space-y-6">


            {{-- DIAGNOSIS --}}

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">

                <h2 class="text-lg font-bold text-gray-900 mb-5">
                    Diagnosis & Root Cause
                </h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <div>

                        <p class="text-sm font-semibold text-gray-500 mb-2">
                            Diagnosis
                        </p>

                        <div class="bg-gray-50 rounded-xl p-4 whitespace-pre-line text-gray-700">
                            {{ $report->diagnosis }}
                        </div>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-gray-500 mb-2">
                            Root Cause
                        </p>

                        <div class="bg-gray-50 rounded-xl p-4 whitespace-pre-line text-gray-700">
                            {{ $report->root_cause }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- WORK --}}

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">

                <h2 class="text-lg font-bold text-gray-900 mb-5">
                    Work & Repair
                </h2>

                <div class="space-y-6">

                    <div>

                        <p class="text-sm font-semibold text-gray-500 mb-2">
                            Work Performed
                        </p>

                        <div class="bg-gray-50 rounded-xl p-4 whitespace-pre-line text-gray-700">
                            {{ $report->work_performed }}
                        </div>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-gray-500 mb-2">
                            Repair Procedure
                        </p>

                        <div class="bg-gray-50 rounded-xl p-4 whitespace-pre-line text-gray-700">
                            {{ $report->repair_procedure }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- RESOURCES --}}

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">

                <h2 class="text-lg font-bold text-gray-900 mb-5">
                    Parts Removed
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                    <div>

                        <div class="bg-gray-50 rounded-xl p-4 whitespace-pre-line text-gray-700">
                            {{ $report->parts_replaced ?: 'None recorded' }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- NOTES --}}

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">

                <h2 class="text-lg font-bold text-gray-900 mb-5">
                    Plumber Notes & Completion
                </h2>

                <div class="space-y-5">

                    <div>

                        <p class="text-sm font-semibold text-gray-500 mb-2">
                            Completion Remarks
                        </p>

                        <div class="bg-gray-50 rounded-xl p-4 whitespace-pre-line text-gray-700">
                            {{ $report->completion_remarks }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- PHOTOS --}}

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">

                <h2 class="text-lg font-bold text-gray-900 mb-5">
                    Before & After Photos
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>

                        <p class="text-sm font-semibold text-gray-500 mb-2">
                            Before Maintenance
                        </p>

                        @if ($report->before_photo)
                            <img src="{{ asset('storage/' . $report->before_photo) }}"
                                class="w-full rounded-xl border object-cover">
                        @else
                            <div class="h-48 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400">
                                No before photo
                            </div>
                        @endif

                    </div>


                    <div>

                        <p class="text-sm font-semibold text-gray-500 mb-2">
                            After Maintenance
                        </p>

                        @if ($report->after_photo)
                            <img src="{{ asset('storage/' . $report->after_photo) }}"
                                class="w-full rounded-xl border object-cover">
                        @else
                            <div class="h-48 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400">
                                No after photo
                            </div>
                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
