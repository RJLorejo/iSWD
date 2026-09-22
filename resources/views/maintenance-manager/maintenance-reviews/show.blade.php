@extends('maintenance-manager.layouts.app')

@section('title', 'Review Maintenance Report')

@section('content')

    @php
        $complaint = $maintenanceReport->complaint;
    @endphp

    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>

                <div class="flex items-center gap-3">

                    <a href="{{ route('maintenance-manager.maintenance-reviews.index') }}"
                        class="text-slate-500 hover:text-sky-700">
                        <i class="fas fa-arrow-left"></i>
                    </a>

                    <h1 class="text-2xl font-bold text-slate-800">
                        Service Accomplishment Review
                    </h1>

                </div>

                <p class="text-sm text-slate-500 mt-1">
                    {{ $complaint?->complaint_no }}
                </p>

            </div>

            @if (!$maintenanceReport->submitted_at)
                <span class="px-4 py-2 rounded-full bg-amber-100 text-amber-700 font-semibold text-sm">
                    Not Yet Submitted
                </span>
            @elseif($maintenanceReport->review_status === 'Pending Review')
                <span class="px-4 py-2 rounded-full bg-amber-100 text-amber-700 font-semibold text-sm">
                    Pending Review
                </span>
            @elseif($maintenanceReport->review_status === 'Returned')
                <span class="px-4 py-2 rounded-full bg-red-100 text-red-700 font-semibold text-sm">
                    Returned
                </span>
            @elseif($maintenanceReport->review_status === 'Approved')
                <span class="px-4 py-2 rounded-full bg-green-100 text-green-700 font-semibold text-sm">
                    Approved
                </span>
            @endif

        </div>


        {{-- COMPLAINT INFORMATION --}}
        <div class="bg-white border rounded-2xl shadow-sm">

            <div class="p-6 border-b">

                <h2 class="text-lg font-bold text-slate-800">
                    Complaint Information
                </h2>

            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">

                <div>
                    <p class="text-sm text-slate-500">Complaint No.</p>
                    <p class="font-semibold">
                        {{ $complaint?->complaint_no }}
                    </p>
                </div>


                <div>
                    <p class="text-sm text-slate-500">Complaint Type</p>
                    <p class="font-semibold">
                        {{ $complaint?->complaint_type?->name ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Complainant</p>
                    <p class="font-semibold">
                        {{ $complaint?->complainant_name }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Address</p>
                    <p class="font-semibold">
                        {{ $complaint?->address }}
                    </p>
                </div>

                <div class="md:col-span-2 lg:col-span-3">

                    <p class="text-sm text-slate-500">
                        Complaint Description
                    </p>

                    <p class="mt-1 text-slate-700">
                        {{ $complaint?->description }}
                    </p>

                </div>

            </div>

        </div>


        {{-- TECHNICIAN --}}
        <div class="bg-white border rounded-2xl shadow-sm">

            <div class="p-6 border-b">

                <h2 class="text-lg font-bold">
                    Plumber
                </h2>

            </div>

            <div class="p-6">

                <p class="font-semibold">

                    {{ $maintenanceReport->technician?->first_name }}
                    {{ $maintenanceReport->technician?->last_name }}

                </p>

                <p class="text-sm text-slate-500">

                    {{ $maintenanceReport->technician?->employee_id }}

                </p>

            </div>

        </div>


        {{-- MAINTENANCE DETAILS --}}
        <div class="bg-white border rounded-2xl shadow-sm">

            <div class="p-6 border-b">

                <h2 class="text-lg font-bold">
                    Accomplishment Details
                </h2>

            </div>


            <div class="p-6 space-y-7">

                <div>

                    <h3 class="font-semibold text-slate-800">
                        Diagnosis
                    </h3>

                    <p class="mt-2 text-slate-600 whitespace-pre-line">
                        {{ $maintenanceReport->diagnosis }}
                    </p>

                </div>


                <div>

                    <h3 class="font-semibold text-slate-800">
                        Root Cause
                    </h3>

                    <p class="mt-2 text-slate-600 whitespace-pre-line">
                        {{ $maintenanceReport->root_cause }}
                    </p>

                </div>

                <div class="grid md:grid-cols-3 gap-6">



                    <div>

                        <h3 class="font-semibold">
                            Parts Removed
                        </h3>

                        <p class="mt-2 text-slate-600 whitespace-pre-line">
                            {{ $maintenanceReport->parts_replaced ?: 'None recorded.' }}
                        </p>

                    </div>
                </div>


                <div>

                    <h3 class="font-semibold">
                        Completion Remarks
                    </h3>

                    <p class="mt-2 text-slate-600 whitespace-pre-line">
                        {{ $maintenanceReport->completion_remarks }}
                    </p>

                </div>

            </div>

        </div>


        {{-- PHOTOS --}}
        <div class="bg-white border rounded-2xl shadow-sm">

            <div class="p-6 border-b">

                <h2 class="text-lg font-bold">
                    Service Accomplishment Evidence
                </h2>

            </div>


            <div class="grid md:grid-cols-2 gap-6 p-6">

                <div>

                    <h3 class="font-semibold mb-3">
                        Before Maintenance
                    </h3>

                    @if ($maintenanceReport->before_photo)
                        <img src="{{ asset('storage/' . $maintenanceReport->before_photo) }}"
                            class="w-full max-h-96 object-contain rounded-xl border bg-slate-50">
                    @else
                        <div class="p-10 text-center text-slate-400 border rounded-xl">
                            No before photo.
                        </div>
                    @endif

                </div>


                <div>

                    <h3 class="font-semibold mb-3">
                        After Maintenance
                    </h3>

                    @if ($maintenanceReport->after_photo)
                        <img src="{{ asset('storage/' . $maintenanceReport->after_photo) }}"
                            class="w-full max-h-96 object-contain rounded-xl border bg-slate-50">
                    @else
                        <div class="p-10 text-center text-slate-400 border rounded-xl">
                            No after photo.
                        </div>
                    @endif

                </div>

            </div>

        </div>


        {{-- PREVIOUS REVIEW --}}
        @if ($maintenanceReport->review_remarks)

            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">

                <h2 class="font-bold text-amber-800">
                    Previous Review Remarks
                </h2>

                <p class="mt-2 text-amber-900 whitespace-pre-line">
                    {{ $maintenanceReport->review_remarks }}
                </p>

                @if ($maintenanceReport->reviewer)

                    <p class="mt-3 text-sm text-amber-700">

                        Reviewed by:
                        {{ $maintenanceReport->reviewer->first_name }}
                        {{ $maintenanceReport->reviewer->last_name }}

                        @if ($maintenanceReport->reviewed_at)
                            —
                            {{ $maintenanceReport->reviewed_at->format('M d, Y h:i A') }}
                        @endif

                    </p>

                @endif

            </div>

        @endif


        {{-- REVIEW ACTIONS --}}

        @if (!$maintenanceReport->submitted_at)
            {{-- REPORT NOT YET SUBMITTED --}}
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">

                <div class="flex items-start gap-4">

                    <div class="flex-shrink-0">

                        <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center">

                            <i class="fas fa-clock text-amber-600 text-xl"></i>

                        </div>

                    </div>

                    <div>

                        <h2 class="font-bold text-amber-800 text-lg">
                            Maintenance Report Not Yet Submitted
                        </h2>

                        <p class="text-sm text-amber-700 mt-1">
                            The technician has not finished and submitted the maintenance
                            report yet. Approval and return actions will become available
                            after the technician submits the report.
                        </p>

                        <div class="mt-3 text-sm text-amber-700">

                            <i class="fas fa-info-circle mr-1"></i>

                            Waiting for:
                            <span class="font-semibold">
                                {{ $maintenanceReport->technician?->first_name }}
                                {{ $maintenanceReport->technician?->last_name }}
                            </span>

                            to complete the report.

                        </div>

                    </div>

                </div>

            </div>
        @elseif ($maintenanceReport->review_status !== 'Approved')
            {{-- REPORT HAS BEEN SUBMITTED --}}
            <div class="grid lg:grid-cols-2 gap-6">

                {{-- APPROVE --}}
                <div class="bg-white border rounded-2xl shadow-sm p-6">

                    <h2 class="text-lg font-bold text-green-700">
                        Approve Accomplishment Report
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Confirm that the service work and accomplishment documentation are valid.
                    </p>

                    <form method="POST"
                        action="{{ route('maintenance-manager.maintenance-reviews.approve', $maintenanceReport) }}"
                        class="mt-5">

                        @csrf

                        <label class="block text-sm font-medium mb-2">
                            Approval Remarks
                        </label>

                        <textarea name="review_remarks" rows="4" class="w-full rounded-xl border-slate-300"
                            placeholder="Optional approval remarks..."></textarea>

                        <button type="submit"
                            class="mt-4 w-full px-5 py-3 rounded-xl bg-green-600 text-white font-semibold hover:bg-green-700 transition">

                            <i class="fas fa-check-circle mr-2"></i>

                            Approve Accomplishment

                        </button>

                    </form>

                </div>


                {{-- RETURN --}}
                <div class="bg-white border rounded-2xl shadow-sm p-6">

                    <h2 class="text-lg font-bold text-red-700">
                        Return for Correction
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">

                        Specify what information in the accomplishment report needs correction.
                    </p>

                    <form method="POST"
                        action="{{ route('maintenance-manager.maintenance-reviews.return', $maintenanceReport) }}"
                        class="mt-5">

                        @csrf

                        <label class="block text-sm font-medium mb-2">
                            Correction Required
                        </label>

                        <textarea name="review_remarks" rows="4" required class="w-full rounded-xl border-slate-300"
                            placeholder="Example: Please provide a clearer root cause and update the parts used."></textarea>

                        <button type="submit"
                            class="mt-4 w-full px-5 py-3 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition">

                            <i class="fas fa-rotate-left mr-2"></i>

                            Return for Correction

                        </button>

                    </form>

                </div>

            </div>
        @else
            {{-- REPORT ALREADY APPROVED --}}
            <div class="bg-green-50 border border-green-200 rounded-2xl p-6">

                <div class="flex items-center gap-3">

                    <i class="fas fa-circle-check text-green-600 text-2xl"></i>

                    <div>

                        <h2 class="font-bold text-green-800">
                            Accomplishment Approved
                        </h2>

                        <p class="text-sm text-green-700 mt-1">
                            The service accomplishment has been validated by management.
                            The complaint is now marked as Completed.
                        </p>

                    </div>

                </div>

            </div>
        @endif

    </div>

@endsection
