@extends('technician.layouts.app')

@section('content')

    <div class="p-6 max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>

                <div class="flex items-center gap-3">

                    <a href="{{ route('technician.maintenance-history.index') }}"
                        class="w-10 h-10 rounded-xl border border-gray-200 flex items-center justify-center hover:bg-gray-50">
                        <i class="fas fa-arrow-left"></i>
                    </a>

                    <div>

                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ $complaint->complaint_no }}
                        </h1>

                        <p class="text-sm text-gray-500">
                            Maintenance History & Timeline
                        </p>

                    </div>

                </div>

            </div>


            @if ($complaint->maintenanceReport)
                <a href="{{ route('technician.maintenance-reports.show', $complaint) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white font-medium hover:bg-blue-700">

                    <i class="fas fa-file-lines"></i>

                    View Maintenance Report

                </a>
            @endif

        </div>


        {{-- COMPLAINT SUMMARY --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">

            <div class="flex items-center justify-between mb-5">

                <div>

                    <h2 class="text-lg font-bold text-gray-900">
                        Complaint Information
                    </h2>

                    <p class="text-sm text-gray-500">
                        Original maintenance request
                    </p>

                </div>

                <span class="px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 text-sm font-semibold">
                    {{ $complaint->status }}
                </span>

            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                <div>

                    <p class="text-xs font-semibold uppercase text-gray-400">
                        Subject
                    </p>

                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ $complaint->subject }}
                    </p>

                </div>


                <div>

                    <p class="text-xs font-semibold uppercase text-gray-400">
                        Complainant
                    </p>

                    <p class="mt-1 text-sm text-gray-700">
                        {{ $complaint->complainant_name }}
                    </p>

                </div>


                <div>

                    <p class="text-xs font-semibold uppercase text-gray-400">
                        Phone
                    </p>

                    <p class="mt-1 text-sm text-gray-700">
                        {{ $complaint->complainant_phone ?? '—' }}
                    </p>

                </div>


                <div>

                    <p class="text-xs font-semibold uppercase text-gray-400">
                        Category
                    </p>

                    <p class="mt-1 text-sm text-gray-700">
                        {{ $complaint->category?->name ?? '—' }}
                    </p>

                </div>


                <div>

                    <p class="text-xs font-semibold uppercase text-gray-400">
                        Priority
                    </p>

                    <p class="mt-1 text-sm text-gray-700">
                        {{ $complaint->priority }}
                    </p>

                </div>


                <div>

                    <p class="text-xs font-semibold uppercase text-gray-400">
                        Address
                    </p>

                    <p class="mt-1 text-sm text-gray-700">
                        {{ $complaint->address }}
                    </p>

                </div>

            </div>

        </div>


        {{-- TIMELINE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">

            <div class="mb-8">

                <h2 class="text-lg font-bold text-gray-900">
                    Maintenance Timeline
                </h2>

                <p class="text-sm text-gray-500">
                    Chronological record of actions performed on this case.
                </p>

            </div>


            @if ($history->count())
                <div class="relative">

                    {{-- Vertical line --}}

                    <div class="absolute left-5 top-2 bottom-2 w-px bg-gray-200"></div>


                    <div class="space-y-8">

                        @foreach ($history as $event)
                            <div class="relative flex gap-5">

                                {{-- ICON --}}

                                @php

                                    $icon = match ($event->event_type) {
                                        'complaint_submitted' => 'fa-file-circle-plus',

                                        'complaint_verified' => 'fa-circle-check',

                                        'technician_assigned' => 'fa-user-check',

                                        'technician_assignment_changed' => 'fa-user-gear',

                                        'maintenance_started' => 'fa-play',

                                        'maintenance_report_created' => 'fa-file-pen',

                                        'maintenance_report_submitted' => 'fa-file-circle-check',

                                        'maintenance_completed' => 'fa-check-double',

                                        'complaint_closed' => 'fa-lock',

                                        'complaint_rejected' => 'fa-circle-xmark',

                                        default => 'fa-clock-rotate-left',
                                    };

                                @endphp


                                <div
                                    class="relative z-10 w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm">

                                    <i class="fas {{ $icon }}"></i>

                                </div>


                                {{-- EVENT --}}

                                <div class="flex-1">

                                    <div class="bg-gray-50 rounded-2xl border border-gray-100 p-5">

                                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-2">

                                            <div>

                                                <h3 class="font-semibold text-gray-900">

                                                    {{ $event->title }}

                                                </h3>

                                                @if ($event->user)
                                                    <p class="text-xs text-gray-500 mt-1">

                                                        By
                                                        <span class="font-medium">
                                                            {{ $event->user->name }}
                                                        </span>

                                                    </p>
                                                @endif

                                            </div>


                                            <time class="text-xs text-gray-500">

                                                {{ $event->event_at->format('M d, Y h:i A') }}

                                            </time>

                                        </div>


                                        @if ($event->description)
                                            <p class="text-sm text-gray-600 mt-3">

                                                {{ $event->description }}

                                            </p>
                                        @endif


                                        @if ($event->old_status || $event->new_status)
                                            <div class="flex items-center gap-2 mt-4 text-xs">

                                                @if ($event->old_status)
                                                    <span class="px-2.5 py-1 rounded-lg bg-gray-200 text-gray-700">
                                                        {{ $event->old_status }}
                                                    </span>
                                                @endif

                                                @if ($event->old_status && $event->new_status)
                                                    <i class="fas fa-arrow-right text-gray-400"></i>
                                                @endif

                                                @if ($event->new_status)
                                                    <span class="px-2.5 py-1 rounded-lg bg-blue-100 text-blue-700">
                                                        {{ $event->new_status }}
                                                    </span>
                                                @endif

                                            </div>
                                        @endif

                                    </div>

                                </div>

                            </div>
                        @endforeach

                    </div>

                </div>
            @else
                <div class="py-12 text-center text-gray-500">

                    <i class="fas fa-clock-rotate-left text-4xl text-gray-300 mb-4"></i>

                    <p class="font-medium">
                        No history records available.
                    </p>

                </div>
            @endif

        </div>


        {{-- MAINTENANCE REPORT --}}
        @if ($complaint->maintenanceReport)

            @php
                $report = $complaint->maintenanceReport;
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-gray-900">
                        Maintenance Report Summary
                    </h2>

                    <p class="text-sm text-gray-500">
                        Technician's recorded maintenance information.
                    </p>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                    <div>

                        <p class="text-xs font-semibold uppercase text-gray-400">
                            Diagnosis
                        </p>

                        <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                            {{ $report->diagnosis ?? 'Not provided' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold uppercase text-gray-400">
                            Root Cause
                        </p>

                        <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                            {{ $report->root_cause ?? 'Not provided' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold uppercase text-gray-400">
                            Work Performed
                        </p>

                        <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                            {{ $report->work_performed ?? 'Not provided' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold uppercase text-gray-400">
                            Repair Procedure
                        </p>

                        <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                            {{ $report->repair_procedure ?? 'Not provided' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold uppercase text-gray-400">
                            Materials
                        </p>

                        <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                            {{ $report->materials_used ?? 'None recorded' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold uppercase text-gray-400">
                            Parts Replaced
                        </p>

                        <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                            {{ $report->parts_replaced ?? 'None recorded' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold uppercase text-gray-400">
                            Tools
                        </p>

                        <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                            {{ $report->tools_used ?? 'None recorded' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-semibold uppercase text-gray-400">
                            Completion Remarks
                        </p>

                        <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">
                            {{ $report->completion_remarks ?? 'None recorded' }}
                        </p>

                    </div>

                </div>


                {{-- PHOTOS --}}

                @if ($report->before_photo || $report->after_photo)
                    <div class="mt-8 pt-6 border-t border-gray-100">

                        <h3 class="font-semibold text-gray-900 mb-4">
                            Maintenance Evidence
                        </h3>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            @if ($report->before_photo)
                                <div>

                                    <p class="text-sm font-medium text-gray-700 mb-2">
                                        Before Maintenance
                                    </p>

                                    <img src="{{ Storage::url($report->before_photo) }}" alt="Before maintenance"
                                        class="w-full h-64 object-cover rounded-2xl border border-gray-200">

                                </div>
                            @endif


                            @if ($report->after_photo)
                                <div>

                                    <p class="text-sm font-medium text-gray-700 mb-2">
                                        After Maintenance
                                    </p>

                                    <img src="{{ Storage::url($report->after_photo) }}" alt="After maintenance"
                                        class="w-full h-64 object-cover rounded-2xl border border-gray-200">

                                </div>
                            @endif

                        </div>

                    </div>
                @endif

            </div>

        @endif

    </div>

@endsection
