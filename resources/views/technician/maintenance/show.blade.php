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


        {{-- STATUS --}}

        <div class="bg-green-50 border border-green-200 rounded-2xl p-5 mb-6">

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">

                    <i class="fas fa-check text-green-600"></i>

                </div>

                <div>

                    <p class="font-bold text-green-800">
                        Maintenance Completed
                    </p>

                    <p class="text-sm text-green-700">
                        Report submitted
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
                    <p class="text-gray-500">Priority</p>
                    <p class="font-semibold text-gray-900">
                        {{ $complaint->priority }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Category</p>
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
                    Materials, Parts & Tools
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                    <div>

                        <p class="text-sm font-semibold text-gray-500 mb-2">
                            Materials
                        </p>

                        <div class="bg-gray-50 rounded-xl p-4 whitespace-pre-line text-gray-700">
                            {{ $report->materials_used ?: 'None recorded' }}
                        </div>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-gray-500 mb-2">
                            Parts
                        </p>

                        <div class="bg-gray-50 rounded-xl p-4 whitespace-pre-line text-gray-700">
                            {{ $report->parts_replaced ?: 'None recorded' }}
                        </div>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-gray-500 mb-2">
                            Tools
                        </p>

                        <div class="bg-gray-50 rounded-xl p-4 whitespace-pre-line text-gray-700">
                            {{ $report->tools_used ?: 'None recorded' }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- NOTES --}}

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">

                <h2 class="text-lg font-bold text-gray-900 mb-5">
                    Technician Notes & Completion
                </h2>

                <div class="space-y-5">

                    <div>

                        <p class="text-sm font-semibold text-gray-500 mb-2">
                            Technician Notes
                        </p>

                        <div class="bg-gray-50 rounded-xl p-4 whitespace-pre-line text-gray-700">
                            {{ $report->technician_notes ?: 'None recorded' }}
                        </div>

                    </div>

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
