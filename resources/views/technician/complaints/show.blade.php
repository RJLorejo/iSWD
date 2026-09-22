@extends('technician.layouts.app')

@section('title', 'Complaint Details')

@section('content')

    @php
        $currentTechnician = $complaint->technicians->firstWhere(
            'id',
            auth()->id()
        );

        $myAssignmentStatus = $currentTechnician?->pivot?->status;

        $statusClasses = match ($complaint->status) {
            'Assigned' => 'bg-blue-100 text-blue-700 border-blue-200',
            'In Progress' => 'bg-amber-100 text-amber-700 border-amber-200',
            'Accomplished' => 'bg-violet-100 text-violet-700 border-violet-200',
            'Completed' => 'bg-green-100 text-green-700 border-green-200',
            'Closed' => 'bg-gray-100 text-gray-700 border-gray-200',
            default => 'bg-gray-100 text-gray-700 border-gray-200',
        };

        $assignmentDate = $complaint->technicians
            ->pluck('pivot.assigned_at')
            ->filter()
            ->sort()
            ->first();

        $report = $complaint->maintenanceReport;
    @endphp

    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>

                <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">

                    <a
                        href="{{ route('technician.complaints.index') }}"
                        class="hover:text-indigo-600 transition"
                    >
                        My Complaints
                    </a>

                    <i class="fas fa-chevron-right text-[10px]"></i>

                    <span class="font-medium text-gray-700">
                        {{ $complaint->complaint_no }}
                    </span>

                </div>

                <div class="flex flex-wrap items-center gap-3 mt-3">

                    <span class="text-sm font-semibold text-indigo-600">
                        Service Work
                    </span>

                    <span class="text-gray-300">
                        •
                    </span>

                    <span class="text-sm text-gray-500">
                        {{ $complaint->category?->name ?? 'Uncategorized' }}
                    </span>

                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">
                    {{ $complaint->category?->name ?? 'Service Complaint' }}
                </h1>

                <p class="text-sm text-gray-500 mt-2">
                    Complaint {{ $complaint->complaint_no }}
                    · Review the service request and perform the assigned work.
                </p>

            </div>

            <a
                href="{{ route('technician.complaints.index') }}"
                class="inline-flex items-center justify-center gap-2
                       px-4 py-2.5 rounded-xl
                       border border-gray-300
                       bg-white text-gray-700
                       hover:bg-gray-50 transition"
            >
                <i class="fas fa-arrow-left"></i>
                Back to My Complaints
            </a>

        </div>

        @if (session('success'))

            <div
                class="flex items-start gap-3 p-4 rounded-xl
                       bg-green-50 border border-green-200 text-green-800"
            >

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
                       bg-red-50 border border-red-200 text-red-800"
            >

                <i class="fas fa-circle-exclamation mt-0.5"></i>

                <div>

                    <p class="font-semibold">
                        Action unavailable
                    </p>

                    <p class="text-sm mt-0.5">
                        {{ session('error') }}
                    </p>

                </div>

            </div>

        @endif

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="p-5 sm:p-6">

                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-6">

                    <div class="flex items-start gap-4">

                        <div
                            class="w-14 h-14 shrink-0 rounded-2xl
                                   flex items-center justify-center
                                   @if ($complaint->status === 'Assigned')
                                       bg-blue-100 text-blue-600
                                   @elseif ($complaint->status === 'In Progress')
                                       bg-amber-100 text-amber-600
                                   @elseif ($complaint->status === 'Accomplished')
                                       bg-violet-100 text-violet-600
                                   @elseif ($complaint->status === 'Completed')
                                       bg-green-100 text-green-600
                                   @else
                                       bg-gray-100 text-gray-600
                                   @endif"
                        >

                            @if ($complaint->status === 'Assigned')

                                <i class="fas fa-user-clock text-xl"></i>

                            @elseif ($complaint->status === 'In Progress')

                                <i class="fas fa-screwdriver-wrench text-xl"></i>

                            @elseif ($complaint->status === 'Accomplished')

                                <i class="fas fa-clipboard-check text-xl"></i>

                            @elseif ($complaint->status === 'Completed')

                                <i class="fas fa-circle-check text-xl"></i>

                            @else

                                <i class="fas fa-circle-info text-xl"></i>

                            @endif

                        </div>

                        <div>

                            <p class="text-sm text-gray-500">
                                Service Status
                            </p>

                            <div class="flex flex-wrap items-center gap-2 mt-1">

                                <span
                                    class="inline-flex items-center px-3 py-1.5
                                           rounded-full border
                                           text-sm font-semibold
                                           {{ $statusClasses }}"
                                >
                                    {{ $complaint->status }}
                                </span>

                            </div>

                            @if ($myAssignmentStatus)

                                <p class="text-sm text-gray-500 mt-3">

                                    Your assignment status:

                                    <span class="font-semibold text-gray-800">
                                        {{ $myAssignmentStatus }}
                                    </span>

                                </p>

                            @endif

                            <p class="text-sm text-gray-500 mt-2">

                                @if ($complaint->status === 'Assigned')

                                    Waiting for service work to start.

                                @elseif ($complaint->status === 'In Progress')

                                    Service work is currently in progress.

                                @elseif (
                                    $complaint->status === 'Accomplished' &&
                                    $report?->review_status === 'Returned'
                                )

                                    Service is accomplished, but the report requires correction.

                                @elseif ($complaint->status === 'Accomplished')

                                    Service accomplished and submitted for management review.

                                @elseif ($complaint->status === 'Completed')

                                    Service accomplishment approved by management.

                                @else

                                    Current service status.

                                @endif

                            </p>

                        </div>

                    </div>

                    <div class="flex flex-wrap gap-3">

                        @if (
                            $complaint->status === 'Assigned' &&
                            $myAssignmentStatus === 'Assigned'
                        )

                            <form
                                method="POST"
                                action="{{ route('technician.maintenance-reports.start', $complaint) }}"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2
                                           px-5 py-3 rounded-xl
                                           bg-blue-600 text-white
                                           font-semibold
                                           hover:bg-blue-700
                                           transition shadow-sm"
                                >
                                    <i class="fas fa-play"></i>
                                    Start Service
                                </button>

                            </form>

                        @elseif ($complaint->status === 'In Progress')

                            <a
                                href="{{ route('technician.maintenance-reports.create', $complaint) }}"
                                class="inline-flex items-center gap-2
                                       px-5 py-3 rounded-xl
                                       bg-indigo-600 text-white
                                       font-semibold
                                       hover:bg-indigo-700
                                       transition shadow-sm"
                            >
                                <i class="fas fa-file-pen"></i>
                                Continue Accomplishment Report
                            </a>

                        @elseif (
                            $complaint->status === 'Accomplished' &&
                            $report?->review_status === 'Returned'
                        )

                            <a
                                href="{{ route('technician.maintenance-reports.create', $complaint) }}"
                                class="inline-flex items-center gap-2
                                       px-5 py-3 rounded-xl
                                       bg-red-600 text-white
                                       font-semibold
                                       hover:bg-red-700
                                       transition shadow-sm"
                            >
                                <i class="fas fa-pen-to-square"></i>
                                Correct Accomplishment Report
                            </a>

                            <a
                                href="{{ route('technician.maintenance-reports.show', $complaint) }}"
                                class="inline-flex items-center gap-2
                                       px-5 py-3 rounded-xl
                                       border border-gray-300
                                       bg-white text-gray-700
                                       font-semibold
                                       hover:bg-gray-50 transition"
                            >
                                <i class="fas fa-eye"></i>
                                View Report
                            </a>

                        @elseif (
                            in_array(
                                $complaint->status,
                                ['Accomplished', 'Completed'],
                                true
                            ) &&
                            $report
                        )

                            <a
                                href="{{ route('technician.maintenance-reports.show', $complaint) }}"
                                class="inline-flex items-center gap-2
                                       px-5 py-3 rounded-xl
                                       bg-green-600 text-white
                                       font-semibold
                                       hover:bg-green-700
                                       transition shadow-sm"
                            >
                                <i class="fas fa-file-circle-check"></i>
                                View Accomplishment Report
                            </a>

                            <a
                                href="{{ route('technician.maintenance-reports.print', $complaint) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2
                                       px-5 py-3 rounded-xl
                                       bg-gray-800 text-white
                                       font-semibold
                                       hover:bg-gray-900
                                       transition shadow-sm"
                            >
                                <i class="fas fa-print"></i>
                                Print
                            </a>

                        @endif

                    </div>

                </div>

            </div>

            @if (
                $complaint->status === 'Accomplished' &&
                $report?->review_status === 'Pending Review'
            )

                <div class="border-t border-violet-100 bg-violet-50 px-5 sm:px-6 py-4">

                    <div class="flex items-start gap-3">

                        <div
                            class="w-9 h-9 rounded-lg bg-violet-100
                                   text-violet-600 flex items-center
                                   justify-center shrink-0"
                        >
                            <i class="fas fa-user-shield"></i>
                        </div>

                        <div>

                            <p class="font-semibold text-violet-900">
                                Service accomplished
                            </p>

                            <p class="text-sm text-violet-800 mt-1">
                                The accomplishment report has been submitted
                                and is currently waiting for management review.
                            </p>

                        </div>

                    </div>

                </div>

            @elseif (
                $complaint->status === 'Accomplished' &&
                $report?->review_status === 'Returned'
            )

                <div class="border-t border-red-100 bg-red-50 px-5 sm:px-6 py-4">

                    <div class="flex items-start gap-3">

                        <div
                            class="w-9 h-9 rounded-lg bg-red-100
                                   text-red-600 flex items-center
                                   justify-center shrink-0"
                        >
                            <i class="fas fa-rotate-left"></i>
                        </div>

                        <div>

                            <p class="font-semibold text-red-900">
                                Accomplishment report returned
                            </p>

                            <p class="text-sm text-red-800 mt-1">
                                Management requested corrections to the
                                accomplishment report. Review the remarks,
                                make the necessary corrections, and resubmit it.
                            </p>

                            @if ($report?->review_remarks)

                                <div class="mt-3 rounded-xl border border-red-200 bg-white/70 p-3">

                                    <p class="text-xs uppercase tracking-wide font-semibold text-red-700">
                                        Manager Remarks
                                    </p>

                                    <p class="text-sm text-red-900 mt-1 whitespace-pre-line">
                                        {{ $report->review_remarks }}
                                    </p>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

            @elseif (
                $complaint->status === 'Completed' &&
                $report?->review_status === 'Approved'
            )

                <div class="border-t border-green-100 bg-green-50 px-5 sm:px-6 py-4">

                    <div class="flex items-start gap-3">

                        <div
                            class="w-9 h-9 rounded-lg bg-green-100
                                   text-green-600 flex items-center
                                   justify-center shrink-0"
                        >
                            <i class="fas fa-circle-check"></i>
                        </div>

                        <div>

                            <p class="font-semibold text-green-900">
                                Service completed
                            </p>

                            <p class="text-sm text-green-800 mt-1">
                                The accomplishment report has been reviewed
                                and approved by management.
                            </p>

                            @if ($report?->reviewed_at)

                                <p class="text-xs text-green-700 mt-2">
                                    Approved
                                    {{ $report->reviewed_at->format('M d, Y h:i A') }}
                                </p>

                            @endif

                        </div>

                    </div>

                </div>

            @endif

        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div class="xl:col-span-2 space-y-6">

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-file-lines text-indigo-600 mr-2"></i>

                            Complaint Details

                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Information submitted by the consumer.
                        </p>

                    </div>

                    <div class="p-5 sm:p-6 space-y-6">

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                       text-gray-500 font-semibold"
                            >
                                Description
                            </p>

                            <div
                                class="mt-2 p-4 rounded-xl
                                       bg-gray-50 border border-gray-100"
                            >

                                <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                                    {{ $complaint->description }}
                                </p>

                            </div>

                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           text-gray-500 font-semibold"
                                >
                                    Division
                                </p>

                                <div class="flex items-center gap-3 mt-2">

                                    <div
                                        class="w-10 h-10 rounded-xl
                                               bg-blue-50 text-blue-600
                                               flex items-center justify-center"
                                    >
                                        <i class="fas fa-building"></i>
                                    </div>

                                    <div>

                                        <p class="font-semibold text-gray-900">
                                            {{ $complaint->division?->name ?? 'Not specified' }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           text-gray-500 font-semibold"
                                >
                                    Complaint Type
                                </p>

                                <div class="flex items-center gap-3 mt-2">

                                    <div
                                        class="w-10 h-10 rounded-xl
                                               bg-indigo-50 text-indigo-600
                                               flex items-center justify-center"
                                    >
                                        <i class="fas fa-layer-group"></i>
                                    </div>

                                    <div>

                                        <p class="font-semibold text-gray-900">
                                            {{ $complaint->category?->name ?? 'Uncategorized' }}
                                        </p>

                                        @if ($complaint->category?->description)

                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $complaint->category->description }}
                                            </p>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>

                        @if ($complaint->photo)

                            <div class="border-t border-gray-100 pt-6">

                                <p
                                    class="text-xs uppercase tracking-wide
                                           text-gray-500 font-semibold mb-3"
                                >
                                    Consumer-Submitted Photo
                                </p>

                                <div
                                    class="rounded-2xl overflow-hidden
                                           border border-gray-200 bg-gray-50"
                                >

                                    <img
                                        src="{{ asset('storage/' . $complaint->photo) }}"
                                        alt="Consumer submitted complaint photo"
                                        class="w-full max-h-[500px] object-contain"
                                    >

                                </div>

                            </div>

                        @endif

                    </div>

                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                            <div>

                                <h2 class="font-semibold text-gray-900">

                                    <i class="fas fa-map-location-dot text-indigo-600 mr-2"></i>

                                    Service Location

                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Use the address and map to locate the service point.
                                </p>

                            </div>

                            @if ($complaint->latitude && $complaint->longitude)

                                <a
                                    href="https://www.google.com/maps/dir/?api=1&destination={{ $complaint->latitude }},{{ $complaint->longitude }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center
                                           gap-2 px-4 py-2.5 rounded-xl
                                           bg-indigo-600 text-white
                                           hover:bg-indigo-700 transition"
                                >
                                    <i class="fas fa-route"></i>
                                    Get Directions
                                </a>

                            @endif

                        </div>

                    </div>

                    <div class="p-5 sm:p-6 space-y-5">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           text-gray-500 font-semibold"
                                >
                                    Address
                                </p>

                                <div class="flex items-start gap-3 mt-2">

                                    <div
                                        class="w-10 h-10 shrink-0 rounded-xl
                                               bg-gray-100 text-gray-500
                                               flex items-center justify-center"
                                    >
                                        <i class="fas fa-location-dot"></i>
                                    </div>

                                    <p class="text-gray-700 leading-relaxed">
                                        {{ $complaint->address ?: 'No address provided' }}
                                    </p>

                                </div>

                            </div>

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           text-gray-500 font-semibold"
                                >
                                    Landmark
                                </p>

                                <div class="flex items-start gap-3 mt-2">

                                    <div
                                        class="w-10 h-10 shrink-0 rounded-xl
                                               bg-gray-100 text-gray-500
                                               flex items-center justify-center"
                                    >
                                        <i class="fas fa-landmark"></i>
                                    </div>

                                    <p class="text-gray-700">
                                        {{ $complaint->landmark ?: 'No landmark provided' }}
                                    </p>

                                </div>

                            </div>

                        </div>

                        @if ($complaint->latitude && $complaint->longitude)

                            <div
                                id="complaint-map"
                                class="w-full h-[350px] sm:h-[430px]
                                       rounded-2xl overflow-hidden
                                       border border-gray-200 z-0"
                            >
                            </div>

                        @else

                            <div
                                class="rounded-xl border border-yellow-200
                                       bg-yellow-50 p-4"
                            >

                                <div class="flex items-start gap-3">

                                    <i
                                        class="fas fa-map-location-dot
                                               text-yellow-600 mt-0.5"
                                    ></i>

                                    <div>

                                        <p class="font-semibold text-yellow-900">
                                            Map location unavailable
                                        </p>

                                        <p class="text-sm text-yellow-800 mt-1">
                                            No map coordinates were provided.
                                            Use the address and landmark to locate
                                            the service point.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        @endif

                    </div>

                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-shield-check text-green-600 mr-2"></i>

                            Customer Service Verification

                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Verification information recorded before service assignment.
                        </p>

                    </div>

                    <div class="p-5 sm:p-6">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           text-gray-500 font-semibold"
                                >
                                    Verified By
                                </p>

                                <div class="flex items-center gap-3 mt-2">

                                    <div
                                        class="w-10 h-10 rounded-full
                                               bg-green-100 text-green-600
                                               flex items-center justify-center"
                                    >
                                        <i class="fas fa-user-check"></i>
                                    </div>

                                    <div>

                                        <p class="font-semibold text-gray-900">
                                            {{ $complaint->verifier?->full_name ?? 'Not verified' }}
                                        </p>

                                        @if ($complaint->verified_at)

                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $complaint->verified_at->format('M d, Y h:i A') }}
                                            </p>

                                        @endif

                                    </div>

                                </div>

                            </div>

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           text-gray-500 font-semibold"
                                >
                                    Verification Status
                                </p>

                                @if ($complaint->verified_at)

                                    <span
                                        class="inline-flex items-center gap-2
                                               px-3 py-1.5 rounded-full
                                               bg-green-100 text-green-700
                                               text-sm font-semibold mt-2"
                                    >
                                        <i class="fas fa-circle-check"></i>
                                        Verified
                                    </span>

                                @else

                                    <span
                                        class="inline-flex items-center gap-2
                                               px-3 py-1.5 rounded-full
                                               bg-gray-100 text-gray-600
                                               text-sm font-semibold mt-2"
                                    >
                                        <i class="fas fa-clock"></i>
                                        Not Verified
                                    </span>

                                @endif

                            </div>

                        </div>

                        @if ($complaint->verification_reason)

                            <div class="mt-6 pt-5 border-t border-gray-100">

                                <p
                                    class="text-xs uppercase tracking-wide
                                           text-gray-500 font-semibold"
                                >
                                    Verification Notes
                                </p>

                                <div
                                    class="mt-2 p-4 rounded-xl
                                           bg-gray-50 border border-gray-100"
                                >

                                    <p class="text-gray-700 whitespace-pre-line">
                                        {{ $complaint->verification_reason }}
                                    </p>

                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            </div>

            <div class="space-y-6">

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-user text-indigo-600 mr-2"></i>

                            Consumer Information

                        </h2>

                    </div>

                    <div class="p-5 space-y-5">

                        <div>

                            <p class="text-xs text-gray-500">
                                Consumer
                            </p>

                            <p class="font-semibold text-gray-900 mt-1">
                                {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Walk-in / Unregistered') }}
                            </p>

                        </div>

                        <div>

                            <p class="text-xs text-gray-500">
                                Account Number
                            </p>

                            <p class="font-semibold text-gray-900 mt-1">
                                {{ $complaint->consumer?->account_number ?? 'Walk-in / Unregistered' }}
                            </p>

                        </div>

                        <div>

                            <p class="text-xs text-gray-500">
                                Contact Number
                            </p>

                            <p class="font-medium text-gray-900 mt-1">
                                {{ $complaint->complainant_phone ?? ($complaint->consumer?->phone ?? '—') }}
                            </p>

                        </div>

                        <div class="pt-4 border-t border-gray-100">

                            <p class="text-xs text-gray-500">
                                Complaint Submitted
                            </p>

                            <p class="font-medium text-gray-900 mt-1">
                                {{ $complaint->created_at?->format('M d, Y h:i A') ?? '—' }}
                            </p>

                        </div>

                    </div>

                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-gray-100">

                        <div class="flex items-center justify-between gap-3">

                            <div>

                                <h2 class="font-semibold text-gray-900">

                                    <i class="fas fa-users-gear text-indigo-600 mr-2"></i>

                                    Service Team

                                </h2>

                                <p class="text-xs text-gray-500 mt-1">
                                    Technicians assigned to this complaint.
                                </p>

                            </div>

                            <span
                                class="inline-flex items-center justify-center
                                       min-w-8 h-8 px-2 rounded-full
                                       bg-indigo-50 text-indigo-700
                                       text-sm font-bold"
                            >
                                {{ $complaint->technicians->count() }}
                            </span>

                        </div>

                    </div>

                    <div class="p-5">

                        @if ($complaint->technicians->count())

                            <div class="space-y-3">

                                @foreach ($complaint->technicians as $technician)

                                    <div
                                        class="flex items-center gap-3 p-3 rounded-xl
                                               {{ $technician->id === auth()->id()
                                                   ? 'bg-indigo-50 border border-indigo-200'
                                                   : 'bg-gray-50 border border-gray-100' }}"
                                    >

                                        <div
                                            class="w-10 h-10 rounded-full
                                                   bg-indigo-100 text-indigo-700
                                                   flex items-center justify-center
                                                   font-bold shrink-0"
                                        >
                                            {{ strtoupper(substr($technician->first_name ?? 'T', 0, 1)) }}
                                        </div>

                                        <div class="min-w-0 flex-1">

                                            <div class="flex flex-wrap items-center gap-2">

                                                <p class="font-semibold text-gray-900 truncate">
                                                    {{ $technician->full_name }}
                                                </p>

                                                @if ($technician->id === auth()->id())

                                                    <span
                                                        class="px-2 py-0.5 rounded-full
                                                               bg-indigo-600 text-white
                                                               text-[10px] font-bold"
                                                    >
                                                        YOU
                                                    </span>

                                                @endif

                                            </div>

                                            <p class="text-xs text-gray-500 mt-0.5">
                                                Maintenance Technician
                                            </p>

                                            @if ($technician->pivot?->status)

                                                <p class="text-xs text-gray-600 mt-1">

                                                    Status:

                                                    <span class="font-semibold">
                                                        {{ $technician->pivot->status }}
                                                    </span>

                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        @else

                            <div class="text-center py-6">

                                <div
                                    class="w-12 h-12 mx-auto rounded-xl
                                           bg-gray-100 text-gray-400
                                           flex items-center justify-center"
                                >
                                    <i class="fas fa-users-slash"></i>
                                </div>

                                <p class="font-semibold text-gray-700 mt-3">
                                    No service team assigned
                                </p>

                            </div>

                        @endif

                    </div>

                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-headset text-indigo-600 mr-2"></i>

                            Customer Service

                        </h2>

                    </div>

                    <div class="p-5">

                        <p class="text-xs text-gray-500">
                            Verified / handled by
                        </p>

                        <p class="font-semibold text-gray-900 mt-1">
                            {{ $complaint->verifier?->full_name ?? $complaint->customerService?->full_name ?? '—' }}
                        </p>

                    </div>

                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-clipboard-check text-indigo-600 mr-2"></i>

                            Work Information

                        </h2>

                    </div>

                    <div class="p-5 space-y-5">

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                       text-gray-500 font-semibold"
                            >
                                Assigned Date
                            </p>

                            <p class="font-medium text-gray-900 mt-1">
                                {{ $assignmentDate ? \Carbon\Carbon::parse($assignmentDate)->format('M d, Y h:i A') : '—' }}
                            </p>

                        </div>

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                       text-gray-500 font-semibold"
                            >
                                Your Work Status
                            </p>

                            <p class="font-semibold text-gray-900 mt-1">
                                {{ $myAssignmentStatus ?? '—' }}
                            </p>

                        </div>

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                       text-gray-500 font-semibold"
                            >
                                Last Updated
                            </p>

                            <p class="font-medium text-gray-900 mt-1">
                                {{ $complaint->updated_at?->format('M d, Y h:i A') ?? '—' }}
                            </p>

                        </div>

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                                       text-gray-500 font-semibold"
                            >
                                Accomplishment Report
                            </p>

                            @if ($report)

                                @php
                                    $reportStatusClasses = match ($report->review_status) {
                                        'Pending Review' => 'bg-violet-100 text-violet-700',
                                        'Returned' => 'bg-red-100 text-red-700',
                                        'Approved' => 'bg-green-100 text-green-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp

                                <span
                                    class="inline-flex items-center gap-2
                                           px-3 py-1.5 mt-2 rounded-full
                                           text-xs font-semibold
                                           {{ $reportStatusClasses }}"
                                >
                                    <i class="fas fa-file-circle-check"></i>

                                    {{ $report->review_status ?? 'Draft' }}
                                </span>

                            @else

                                <span
                                    class="inline-flex items-center gap-2
                                           px-3 py-1.5 mt-2 rounded-full
                                           bg-gray-100 text-gray-600
                                           text-xs font-semibold"
                                >
                                    <i class="fas fa-file"></i>
                                    No Report Yet
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-timeline text-indigo-600 mr-2"></i>

                            Work Progress

                        </h2>

                    </div>

                    <div class="p-5">

                        <div class="relative">

                            <div
                                class="absolute left-4 top-3 bottom-3
                                       w-px bg-gray-200"
                            >
                            </div>

                            <div class="space-y-6">

                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               bg-green-100 text-green-600
                                               flex items-center justify-center"
                                    >
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

                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               {{ $complaint->verified_at
                                                   ? 'bg-green-100 text-green-600'
                                                   : 'bg-gray-100 text-gray-400' }}
                                               flex items-center justify-center"
                                    >
                                        <i class="fas fa-shield-check text-xs"></i>
                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            Verified by Customer Service
                                        </p>

                                        @if ($complaint->verified_at)

                                            <p class="text-xs text-green-600 mt-1">
                                                {{ $complaint->verified_at->format('M d, Y h:i A') }}
                                            </p>

                                            @if ($complaint->verifier)

                                                <p class="text-xs text-gray-500 mt-1">

                                                    By

                                                    <span class="font-medium">
                                                        {{ $complaint->verifier->full_name }}
                                                    </span>

                                                </p>

                                            @endif

                                        @else

                                            <p class="text-xs text-gray-400 mt-1">
                                                Not verified
                                            </p>

                                        @endif

                                    </div>

                                </div>

                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               {{ $complaint->technicians->count()
                                                   ? 'bg-blue-100 text-blue-600'
                                                   : 'bg-gray-100 text-gray-400' }}
                                               flex items-center justify-center"
                                    >
                                        <i class="fas fa-users-gear text-xs"></i>
                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            Service Team Assigned
                                        </p>

                                        @if ($complaint->technicians->count())

                                            <p class="text-xs text-blue-600 mt-1">
                                                {{ $complaint->technicians->count() }}
                                                technician(s) assigned
                                            </p>

                                            @if ($assignmentDate)

                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ \Carbon\Carbon::parse($assignmentDate)->format('M d, Y h:i A') }}
                                                </p>

                                            @endif

                                        @else

                                            <p class="text-xs text-gray-400 mt-1">
                                                No service team assigned
                                            </p>

                                        @endif

                                    </div>

                                </div>

                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               @if (in_array($complaint->status, ['In Progress', 'Accomplished', 'Completed'], true))
                                                   bg-amber-100 text-amber-600
                                               @else
                                                   bg-gray-100 text-gray-400
                                               @endif
                                               flex items-center justify-center"
                                    >
                                        <i class="fas fa-play text-xs"></i>
                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            Service Started
                                        </p>

                                        @if ($currentTechnician?->pivot?->started_at)

                                            <p class="text-xs text-amber-600 mt-1">
                                                {{ \Carbon\Carbon::parse($currentTechnician->pivot->started_at)->format('M d, Y h:i A') }}
                                            </p>

                                        @elseif ($complaint->status === 'Assigned')

                                            <p class="text-xs text-gray-400 mt-1">
                                                Waiting for service work to start
                                            </p>

                                        @else

                                            <p class="text-xs text-gray-500 mt-1">
                                                Service work has started
                                            </p>

                                        @endif

                                    </div>

                                </div>

                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               @if ($complaint->status === 'Accomplished')
                                                   bg-violet-100 text-violet-600
                                               @elseif ($complaint->status === 'Completed')
                                                   bg-green-100 text-green-600
                                               @else
                                                   bg-gray-100 text-gray-400
                                               @endif
                                               flex items-center justify-center"
                                    >
                                        <i class="fas fa-clipboard-check text-xs"></i>
                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            Service Accomplished
                                        </p>

                                        @if ($report?->submitted_at)

                                            <p class="text-xs text-violet-600 mt-1">
                                                {{ $report->submitted_at->format('M d, Y h:i A') }}
                                            </p>

                                            <p class="text-xs text-gray-500 mt-1">
                                                Accomplishment report submitted
                                            </p>

                                        @else

                                            <p class="text-xs text-gray-400 mt-1">
                                                Accomplishment not submitted yet
                                            </p>

                                        @endif

                                    </div>

                                </div>

                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               @if ($report?->review_status === 'Approved')
                                                   bg-green-100 text-green-600
                                               @elseif ($report?->review_status === 'Returned')
                                                   bg-red-100 text-red-600
                                               @elseif ($report?->review_status === 'Pending Review')
                                                   bg-violet-100 text-violet-600
                                               @else
                                                   bg-gray-100 text-gray-400
                                               @endif
                                               flex items-center justify-center"
                                    >
                                        <i class="fas fa-user-shield text-xs"></i>
                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            Management Review
                                        </p>

                                        @if ($report?->review_status === 'Approved')

                                            <p class="text-xs text-green-600 mt-1">
                                                Accomplishment approved
                                            </p>

                                            @if ($report->reviewed_at)

                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $report->reviewed_at->format('M d, Y h:i A') }}
                                                </p>

                                            @endif

                                        @elseif ($report?->review_status === 'Returned')

                                            <p class="text-xs text-red-600 mt-1">
                                                Returned for correction
                                            </p>

                                        @elseif ($report?->review_status === 'Pending Review')

                                            <p class="text-xs text-violet-600 mt-1">
                                                Pending management review
                                            </p>

                                        @else

                                            <p class="text-xs text-gray-400 mt-1">
                                                Waiting for accomplishment submission
                                            </p>

                                        @endif

                                    </div>

                                </div>

                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               {{ in_array($complaint->status, ['Completed', 'Closed'], true)
                                                   ? 'bg-green-100 text-green-600'
                                                   : 'bg-gray-100 text-gray-400' }}
                                               flex items-center justify-center"
                                    >
                                        <i class="fas fa-circle-check text-xs"></i>
                                    </div>

                                    <div class="pt-1">

                                        <p class="font-semibold text-gray-900">
                                            Completed
                                        </p>

                                        @if ($complaint->status === 'Completed')

                                            <p class="text-xs text-green-600 mt-1">
                                                Service accomplishment approved by management
                                            </p>

                                            @if ($complaint->completed_at)

                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $complaint->completed_at->format('M d, Y h:i A') }}
                                                </p>

                                            @endif

                                        @elseif ($complaint->status === 'Closed')

                                            <p class="text-xs text-green-600 mt-1">
                                                Complaint completed and closed
                                            </p>

                                        @else

                                            <p class="text-xs text-gray-400 mt-1">
                                                Waiting for management approval
                                            </p>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                @if ($complaint->latitude && $complaint->longitude)

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">

                        <div class="p-5">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-10 h-10 shrink-0 rounded-xl
                                           bg-indigo-100 text-indigo-600
                                           flex items-center justify-center"
                                >
                                    <i class="fas fa-route"></i>
                                </div>

                                <div>

                                    <p class="font-semibold text-gray-900">
                                        Navigate to Service Location
                                    </p>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Open the recorded complaint location for field service.
                                    </p>

                                </div>

                            </div>

                            <a
                                href="https://www.google.com/maps/dir/?api=1&destination={{ $complaint->latitude }},{{ $complaint->longitude }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-4 w-full inline-flex items-center
                                       justify-center gap-2
                                       px-4 py-2.5 rounded-xl
                                       border border-indigo-200
                                       bg-indigo-50 text-indigo-700
                                       hover:bg-indigo-100 transition"
                            >
                                <i class="fas fa-map-marked-alt"></i>
                                Open Navigation
                            </a>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>

    @if ($complaint->latitude && $complaint->longitude)

        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            crossorigin=""
        />

        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            crossorigin=""
        ></script>

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

                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }
                ).addTo(map);

                const marker = L.marker([
                    latitude,
                    longitude
                ]).addTo(map);

                marker.bindPopup(`
                    <div style="min-width:220px">

                        <strong style="font-size:14px">
                            {{ addslashes($complaint->complaint_no) }}
                        </strong>

                        <br>

                        <span style="font-size:13px">
                            {{ addslashes($complaint->category?->name ?? 'Service Complaint') }}
                        </span>

                        <br><br>

                        <span style="font-size:12px;color:#6b7280">
                            {{ addslashes($complaint->address ?? '') }}
                        </span>

                    </div>
                `).openPopup();

                setTimeout(function() {
                    map.invalidateSize();
                }, 300);

            });
        </script>

    @endif

@endsection
