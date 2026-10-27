@extends('technician.layouts.app')

@section('title', 'Complaint Details')

@section('content')

    @php
        $currentTechnician = $complaint->technicians->firstWhere('id', auth()->id());
        $myAssignmentStatus = $currentTechnician?->pivot?->status;

        // The current visit's team, not historical complaint assignments, controls actions.
$isReassignedFromCurrentVisit = $isReassignedFromCurrentVisit ?? false;
$isCurrentWorkOrderTechnician = $workOrder && $workOrderTeam->contains('id', (int) auth()->id());
$myParticipation = $workOrder
    ? \App\Models\MaintenanceWorkOrderParticipation::query()
        ->where('maintenance_work_order_id', $workOrder->id)
        ->where('technician_id', (int) auth()->id())
        ->first()
    : null;
$isHandedOver = in_array($myParticipation?->status, ['Handed Over', 'Replaced'], true);
$canActOnCurrentVisit = !$isReassignedFromCurrentVisit && $isCurrentWorkOrderTechnician && !$isHandedOver;

$report = $complaint->maintenanceReport;
$commercialResolution = $complaint->commercialResolution;

$isEngineering = str_contains(strtolower($complaint->division?->name ?? ''), 'engineering');

$isForwardedRequest = !$isEngineering && !is_null($commercialResolution?->forwarded_to_maintenance_at);

$displayStatus = match ($complaint->status) {
    'Completed' => 'Accomplished',
    default => $complaint->status,
};

$statusClasses = match ($complaint->status) {
    'Assigned' => 'bg-blue-50 text-blue-700 border-blue-200',
    'In Progress' => 'bg-amber-50 text-amber-700 border-amber-200',
    'Completed' => 'bg-green-50 text-green-700 border-green-200',
    'Closed' => 'bg-gray-100 text-gray-700 border-gray-200',
    default => 'bg-gray-50 text-gray-700 border-gray-200',
};

// Display Customer Service's finalized urgency only.
        $urgency = strtoupper(trim((string) ($complaint->aiAnalysis?->verified_urgency_level ?? '')));

        $urgencyClasses = match ($urgency) {
            'HIGH' => 'bg-red-50 text-red-700 border-red-200',
            'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',
            'LOW' => 'bg-green-50 text-green-700 border-green-200',
            default => 'bg-gray-50 text-gray-500 border-gray-200',
        };

        $assignmentDate = $complaint->technicians->pluck('pivot.assigned_at')->filter()->sort()->first();

        $startedDate = $complaint->technicians->pluck('pivot.started_at')->filter()->sort()->first();

        $completedDate = $complaint->technicians->pluck('pivot.completed_at')->filter()->sort()->first();

        $assignedNames = $currentMaintenanceTeam
            ->map(fn($technician) => $technician->full_name)
            ->filter()
            ->implode(', ');

        $reportSubmitted =
            $report && in_array($report->review_status, ['Pending Review', 'Returned', 'Approved'], true);

        $serviceAddress = $complaint->address;
        $serviceLandmark = $complaint->landmark;
        $serviceLatitude = $complaint->latitude;
        $serviceLongitude = $complaint->longitude;

        if (!$isEngineering && $complaint->consumer?->address) {
            $consumerAddress = $complaint->consumer->address;

            $serviceAddress = $consumerAddress->full_address ?? ($consumerAddress->address ?? $complaint->address);

            $serviceLandmark = $consumerAddress->landmark ?? $complaint->landmark;

            $serviceLatitude = $consumerAddress->latitude ?? $complaint->latitude;

            $serviceLongitude = $consumerAddress->longitude ?? $complaint->longitude;
        }

        $hasCoordinates = !is_null($serviceLatitude) && !is_null($serviceLongitude);

        $hasCsProcessing = !$isEngineering && $commercialResolution;

        $initialProcessingStarted = $commercialResolution?->created_at;

        $initialProcessingCompleted = $commercialResolution?->initial_processing_completed_at;

        $forwardedAt = $commercialResolution?->forwarded_to_maintenance_at;

        $forwarder = $commercialResolution?->forwarder;

        $processor = $commercialResolution?->processor;
    @endphp


    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

            <div>

                <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">

                    <a href="{{ route('technician.complaints.index') }}" class="hover:text-sky-600 transition">
                        My Complaints
                    </a>

                    <i class="fas fa-chevron-right text-[10px]"></i>

                    <span>
                        {{ $complaint->complaint_no }}
                    </span>

                </div>


                <div class="flex flex-wrap items-center gap-3 mt-2">

                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        {{ $complaint->category?->name ?? 'Service Complaint' }}
                    </h1>

                    @if ($urgency)
                        <span
                            class="inline-flex items-center gap-1.5
                                   px-2.5 py-1 rounded-full border
                                   text-xs font-bold
                                   {{ $urgencyClasses }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                            {{ $urgency }} URGENCY
                        </span>
                    @else
                        <span
                            class="inline-flex items-center
                                   px-2.5 py-1 rounded-full border
                                   bg-gray-50 border-gray-200
                                   text-xs font-semibold text-gray-400">
                            NOT ASSESSED
                        </span>
                    @endif

                </div>


                <p class="text-sm text-gray-500 mt-2">

                    {{ $complaint->complaint_no }}

                    @if ($complaint->division?->name)
                        · {{ $complaint->division->name }}
                    @endif

                </p>

            </div>


            <a href="{{ route('technician.complaints.index') }}"
                class="inline-flex items-center justify-center gap-2
                       px-4 py-2.5 rounded-xl
                       border border-gray-300
                       bg-white text-gray-700
                       hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left"></i>

                Back
            </a>

        </div>


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


        @if (session('error'))
            <div
                class="rounded-xl border border-red-200
                       bg-red-50 px-4 py-3
                       text-sm text-red-800
                       flex items-start gap-3">
                <i class="fas fa-circle-exclamation mt-0.5"></i>

                <span>
                    {{ session('error') }}
                </span>
            </div>
        @endif

        @if ($errors->has('maintenance'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <i class="fas fa-circle-exclamation mr-2"></i>
                {{ $errors->first('maintenance') }}
            </div>
        @endif

        @if ($errors->has('pending_reason'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <i class="fas fa-circle-exclamation mr-2"></i>
                {{ $errors->first('pending_reason') }}
            </div>
        @endif


        <div
            class="bg-white rounded-2xl
                   border border-gray-200
                   shadow-sm overflow-hidden">

            <div class="p-5 sm:p-6">

                <div class="flex flex-col xl:flex-row
                           xl:items-center xl:justify-between gap-5">

                    <div class="flex items-start gap-4">

                        <div
                            class="w-12 h-12 rounded-xl shrink-0
                                   flex items-center justify-center
                                   @if ($complaint->status === 'Assigned') bg-blue-50 text-blue-600
                                   @elseif ($complaint->status === 'In Progress')
                                       bg-amber-50 text-amber-600
                                   @elseif ($complaint->status === 'Completed')
                                       bg-green-50 text-green-600
                                   @else
                                       bg-gray-100 text-gray-600 @endif">

                            @if ($complaint->status === 'Assigned')
                                <i class="fas fa-user-clock"></i>
                            @elseif ($complaint->status === 'In Progress')
                                <i class="fas fa-screwdriver-wrench"></i>
                            @elseif ($complaint->status === 'Completed')
                                <i class="fas fa-clipboard-check"></i>
                            @else
                                <i class="fas fa-circle-check"></i>
                            @endif

                        </div>


                        <div>

                            <p class="text-xs uppercase tracking-wide font-semibold text-gray-400">
                                Current Status
                            </p>

                            <div class="flex flex-wrap items-center gap-2 mt-1">

                                <span
                                    class="inline-flex items-center gap-1.5
                                           px-3 py-1.5 rounded-full border
                                           text-sm font-semibold
                                           {{ $statusClasses }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                    {{ $displayStatus }}
                                </span>

                            </div>


                            <p class="text-sm text-gray-500 mt-2">

                                @if ($complaint->status === 'Assigned')
                                    Maintenance has been assigned and is waiting to start.
                                @elseif ($complaint->status === 'In Progress')
                                    Maintenance work is currently in progress.
                                @elseif ($complaint->status === 'Completed' && $report?->review_status === 'Returned')
                                    The accomplishment report was returned for correction.
                                @elseif ($complaint->status === 'Completed' && $report?->review_status === 'Pending Review')
                                    Maintenance is accomplished and waiting for Manager review.
                                @elseif ($complaint->status === 'Completed' && $report?->review_status === 'Approved')
                                    The accomplishment report has been approved.
                                @elseif ($complaint->status === 'Closed')
                                    The complaint has been finalized and closed.
                                @else
                                    Current maintenance status.
                                @endif

                            </p>

                        </div>

                    </div>


                    <div class="flex flex-wrap gap-2">


                        @if (
                            $workOrder &&
                                in_array($complaint->status, ['Assigned', 'In Progress'], true) &&
                                ($scheduleState['can_start'] ?? false) &&
                                $workOrder->approval_status === 'Approved' &&
                                $workOrder->schedule?->status === 'Finalized')

                            <form method="POST"
                                action="{{ route('technician.work-orders.start', [
                                    'complaint' => $complaint,
                                    'workOrder' => $workOrder,
                                ]) }}">
                                @csrf

                                <button type="submit"
                                    class="inline-flex items-center gap-2
                                           px-5 py-3 rounded-xl
                                           bg-sky-600 text-white
                                           font-semibold
                                           hover:bg-sky-700 transition">
                                    <i class="fas fa-play"></i>

                                    Start Maintenance
                                </button>

                            </form>
                        @elseif ($canActOnCurrentVisit && $canContinueAccomplishment)
                            <a href="{{ route('technician.maintenance-reports.create', $complaint) }}"
                                class="inline-flex items-center gap-2
                                       px-5 py-3 rounded-xl
                                       bg-sky-600 text-white
                                       font-semibold
                                       hover:bg-sky-700 transition">
                                <i class="fas fa-file-pen"></i>

                                Continue Accomplishment Report
                            </a>
                        @elseif (in_array($complaint->status, ['Completed', 'Closed'], true) &&
                                $report?->review_status === 'Returned' &&
                                (int) $report->technician_id === (int) auth()->id())
                            <a href="{{ route('technician.maintenance-reports.create', $complaint) }}"
                                class="inline-flex items-center gap-2
                                       px-5 py-3 rounded-xl
                                       bg-red-600 text-white
                                       font-semibold
                                       hover:bg-red-700 transition">
                                <i class="fas fa-pen-to-square"></i>
                                Revise Accomplishment Report
                            </a>

                            @if ($reportSubmitted)
                                <a href="{{ route('technician.maintenance-reports.show', $complaint) }}"
                                    class="inline-flex items-center gap-2
                                           px-5 py-3 rounded-xl
                                           border border-gray-300
                                           bg-white text-gray-700
                                           font-semibold
                                           hover:bg-gray-50 transition">
                                    <i class="fas fa-eye"></i>

                                    View Returned Report
                                </a>
                            @endif
                        @elseif (in_array($complaint->status, ['Completed', 'Closed'], true) && $reportSubmitted)
                            <a href="{{ route('technician.maintenance-reports.show', $complaint) }}"
                                class="inline-flex items-center gap-2
           px-5 py-3 rounded-xl
           bg-green-600 text-white
           font-semibold
           hover:bg-green-700 transition">
                                @if ($report?->review_status === 'Pending Review')
                                    <i class="fas fa-eye"></i>
                                    View Submitted Report
                                @elseif ($report?->review_status === 'Approved')
                                    <i class="fas fa-circle-check"></i>
                                    View Approved Report
                                @else
                                    <i class="fas fa-file-circle-check"></i>
                                    View Accomplishment Report
                                @endif
                            </a>


                            <a href="{{ route('technician.maintenance-reports.print', $complaint) }}" target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2
                                       px-5 py-3 rounded-xl
                                       bg-gray-800 text-white
                                       font-semibold
                                       hover:bg-gray-900 transition">
                                <i class="fas fa-print"></i>

                                Print
                            </a>

                        @endif

                    </div>

                </div>

            </div>


            @php
                $reportStatus = $report?->review_status;

                $needsReportReview = in_array($reportStatus, ['Pending Review', 'Approved', 'Returned'], true);

                $today = now('Asia/Manila')->toDateString();

                $workOrderSchedule = $workOrder?->schedule;

                $workOrderScheduleDate = $workOrderSchedule?->schedule_date
                    ? \Carbon\CarbonImmutable::parse($workOrderSchedule->schedule_date, 'Asia/Manila')->toDateString()
                    : null;

                $hasValidWorkOrderSchedule =
                    $workOrderSchedule?->status === 'Finalized' &&
                    $workOrder?->approval_status === 'Approved' &&
                    $workOrderScheduleDate !== null &&
                    $workOrderScheduleDate === $today;

                $hasStartedWorkOrder = !is_null($workOrder?->started_at);

                // A newer follow-up (even if still Draft) supersedes the old visit.
                // Match the backend resumeWorkOrder() eligibility check.
                $newerFollowUp = $workOrder
                    ? \App\Models\MaintenanceWorkOrder::query()
                        ->with('technicians')
                        ->where('complaint_id', $complaint->id)
                        ->where(function ($query) use ($workOrder) {
                            $query
                                ->where('visit_number', '>', $workOrder->visit_number)
                                ->orWhere(function ($sameVisit) use ($workOrder) {
                                    $sameVisit
                                        ->where('visit_number', $workOrder->visit_number)
                                        ->where('id', '>', $workOrder->id);
                                });
                        })
                        ->whereIn('status', ['Scheduled', 'In Progress', 'Completed', 'Pending Workload'])
                        ->orderByDesc('visit_number')
                        ->orderByDesc('id')
                        ->first()
                    : null;

                $reassignedToAnotherPlumber =
                    $newerFollowUp && !$newerFollowUp->technicians->contains('id', (int) auth()->id());

                $canRecordOrderPending =
                    in_array($complaint->status, ['Assigned', 'In Progress'], true) &&
                    $workOrder !== null &&
                    in_array($workOrder->status, ['Scheduled', 'In Progress'], true) &&
                    $hasValidWorkOrderSchedule &&
                    $canActOnCurrentVisit &&
                    !$needsReportReview;

                $canResumeSameDay =
                    in_array($complaint->status, ['Assigned', 'In Progress'], true) &&
                    $workOrder !== null &&
                    $workOrder->status === 'Pending Workload' &&
                    !$newerFollowUp &&
                    $canActOnCurrentVisit &&
                    $hasValidWorkOrderSchedule &&
                    $canActOnCurrentVisit &&
                    !$needsReportReview;

            @endphp


            @if ($canActOnCurrentVisit && in_array($complaint->status, ['Assigned', 'In Progress'], true))

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                            <i class="fas fa-clock"></i>
                        </div>

                        <div>
                            <h3 class="font-semibold text-slate-900">
                                Unfinished Maintenance
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Record a reason if maintenance cannot start or must be interrupted.
                            </p>
                        </div>

                    </div>

                    @if ($needsReportReview)
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <p class="text-sm font-semibold text-amber-900">
                                <i class="fas fa-circle-info mr-1"></i>
                                Report Review Required
                            </p>

                            <p class="mt-1 text-sm text-amber-800">
                                This complaint already has a maintenance report
                                marked {{ $reportStatus }}.
                                Please contact the Maintenance Manager before
                                recording Pending Workload.
                            </p>
                        </div>
                    @elseif ($workOrder?->status === 'Pending Workload')
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <p class="text-sm font-semibold text-amber-900">
                                <i class="fas fa-hourglass-half mr-1"></i>
                                Pending Workload Recorded
                            </p>

                            <p class="mt-2 text-sm text-amber-800">
                                {{ $workOrder->pending_reason }}
                            </p>

                            @if ($reassignedToAnotherPlumber)
                                <p class="mt-3 text-sm font-medium text-slate-700">
                                    <i class="fas fa-user-check mr-1"></i>
                                    Maintenance reassigned to another plumber. No further action is required for this visit.
                                </p>
                            @elseif ($newerFollowUp)
                                <p class="mt-3 text-sm font-medium text-slate-700">
                                    A newer follow-up visit has been arranged. Continue only through the new work order.
                                </p>
                            @elseif ($canResumeSameDay)
                                <form method="POST"
                                    action="{{ route('technician.work-orders.resume', [$complaint, $workOrder]) }}"
                                    class="mt-3">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white hover:bg-sky-800">
                                        <i class="fas fa-play"></i>
                                        {{ $hasStartedWorkOrder ? 'Resume Maintenance' : 'Start After Interruption' }}
                                    </button>
                                </form>
                            @else
                                <p class="mt-3 text-xs text-amber-700">
                                    Resumption is unavailable. Contact the Maintenance Manager if follow-up is needed.
                                </p>
                            @endif
                        </div>
                    @elseif ($canRecordOrderPending)
                        <form method="POST"
                            action="{{ route('technician.work-orders.pending', [$complaint, $workOrder]) }}"
                            class="space-y-3">
                            @csrf

                            <div>
                                <label for="pending_reason" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Reason for Unfinished Maintenance
                                    <span class="text-red-500">*</span>
                                </label>

                                <textarea id="pending_reason" name="pending_reason" rows="3" minlength="10" maxlength="2000" required
                                    placeholder="Example: Repair could not be completed because additional pipe fittings are required."
                                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-100">{{ old('pending_reason') }}</textarea>

                                @error('pending_reason')
                                    <p class="mt-1 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3 text-xs text-slate-600">
                                <i class="fas fa-circle-info mr-1"></i>
                                This will not complete the complaint. You can resume today;
                                otherwise the manager can arrange a follow-up visit.
                            </div>

                            <button type="submit"
                                class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl bg-amber-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-amber-700">
                                <i class="fas fa-clipboard-check"></i>
                                Record Pending Workload
                            </button>
                        </form>
                    @else
                        <p class="text-sm text-slate-500">
                            No eligible maintenance activity is available
                            for Pending Workload recording.
                        </p>
                    @endif

                </div>

            @endif



            @if ($complaint->status === 'Completed' && $report?->review_status === 'Pending Review')

                <div class="border-t border-violet-100
                           bg-violet-50 px-5 sm:px-6 py-4">

                    <div class="flex items-start gap-3">

                        <i class="fas fa-clock text-violet-600 mt-0.5"></i>

                        <div>

                            <p class="font-semibold text-violet-900">
                                Waiting for Manager Review
                            </p>

                            <p class="text-sm text-violet-800 mt-1">
                                Your accomplishment report has been submitted.
                                No further action is required unless it is returned.
                            </p>

                        </div>

                    </div>

                </div>
            @elseif ($complaint->status === 'Completed' && $report?->review_status === 'Returned')
                <div class="border-t border-red-100
                           bg-red-50 px-5 sm:px-6 py-4">

                    <div class="flex items-start gap-3">

                        <i class="fas fa-rotate-left text-red-600 mt-0.5"></i>

                        <div class="flex-1">

                            <p class="font-semibold text-red-900">
                                Report Returned for Correction
                            </p>

                            <p class="text-sm text-red-800 mt-1">
                                Review the Manager remarks, correct the report,
                                and submit it again.
                            </p>


                            @if ($report?->review_remarks)
                                <div
                                    class="mt-3 p-3 rounded-xl
                                           bg-white border border-red-200">

                                    <p
                                        class="text-xs uppercase tracking-wide
                                               font-semibold text-red-700">
                                        Manager Remarks
                                    </p>

                                    <p
                                        class="text-sm text-red-900
                                               whitespace-pre-line mt-1">
                                        {{ $report->review_remarks }}
                                    </p>

                                </div>
                            @endif

                        </div>

                    </div>

                </div>
            @elseif (in_array($complaint->status, ['Completed', 'Closed'], true) && $report?->review_status === 'Approved')
                <div class="border-t border-green-100
                           bg-green-50 px-5 sm:px-6 py-4">

                    <div class="flex items-start gap-3">

                        <i class="fas fa-circle-check text-green-600 mt-0.5"></i>

                        <div>

                            <p class="font-semibold text-green-900">
                                Accomplishment Report Approved
                            </p>

                            <p class="text-sm text-green-800 mt-1">
                                The Manager has reviewed and approved
                                the maintenance accomplishment report.
                            </p>

                            @if ($report?->reviewed_at)
                                <p class="text-xs text-green-700 mt-2">
                                    {{ $report->reviewed_at->format('M d, Y h:i A') }}
                                </p>
                            @endif

                        </div>

                    </div>

                </div>

            @endif

        </div>

        @if ($isReassignedFromCurrentVisit || $isHandedOver)
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm" role="status">
                <div class="flex items-start gap-3">
                    <i class="fas fa-user-shield mt-1 text-indigo-700"></i>
                    <div>
                        <h2 class="font-semibold text-indigo-950">Maintenance Reassigned to Another Plumber</h2>
                        <p class="mt-1 text-sm leading-6 text-indigo-800">
                            Your assignment to this maintenance visit has ended. You can review the complaint
                            and previous maintenance records, but you cannot start, resume, or record
                            unfinished work for the current visit. Contact the Maintenance Manager for clarification.
                        </p>
                        @if ($currentMaintenanceTeam->isNotEmpty())
                            <p class="mt-2 text-xs font-semibold text-indigo-800">
                                Current team: {{ $assignedNames }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Approval is specific to the work order, not only its parent schedule. --}}
        @if ($workOrder && $workOrder->approval_status !== 'Approved')
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <i class="fas fa-clock mt-1 text-amber-700"></i>
                    <div>
                        <p class="font-semibold text-amber-900">Draft Maintenance Assignment — Awaiting Manager Approval
                        </p>
                        <p class="mt-1 text-sm text-amber-800">This visit is visible for advance planning only. Maintenance
                            cannot begin until the Manager approves this work order, even when the daily schedule is
                            finalized.</p>
                        @if ($workOrder->is_late_addition && filled($workOrder->late_addition_reason))
                            <p class="mt-2 whitespace-pre-line text-sm text-amber-900"><strong>Early scheduling
                                    reason:</strong> {{ $workOrder->late_addition_reason }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Manager-approved maintenance work schedule --}}
        @include('technician.complaints.partials.work-order-schedule', [
            'scheduleState' => $scheduleState,
            'workOrder' => $workOrder,
            'workOrderTeam' => $workOrderTeam,
        ])

        {{-- ========================================================= --}}
        {{-- MAINTENANCE SCHEDULING HISTORY - PLUMBER --}}
        {{-- ========================================================= --}}

        @php
            $plumberWorkOrders = \App\Models\MaintenanceWorkOrder::query()
                ->with('schedule')
                ->where('complaint_id', $complaint->id)
                ->whereHas('technicians', function ($query) {
                    $query->where('users.id', auth()->id());
                })
                ->orderBy('visit_number')
                ->orderBy('id')
                ->get();
        @endphp

        {{-- Prominent historical unfinished-visit reason. Keep the original
             work order as the source of truth, even after follow-up scheduling. --}}
        @php
            $latestUnfinishedVisit = $plumberWorkOrders
                ->filter(fn($order) => filled($order->pending_reason))
                ->sortByDesc(
                    fn($order) => $order->pending_recorded_at
                        ? \Carbon\Carbon::parse($order->pending_recorded_at)->timestamp
                        : $order->id,
                )
                ->first();
        @endphp

        @if ($latestUnfinishedVisit)
            <div class="overflow-hidden rounded-2xl border border-orange-200 bg-orange-50 shadow-sm">
                <div class="flex flex-col gap-3 p-5 sm:flex-row sm:items-start sm:justify-between sm:p-6">
                    <div class="flex min-w-0 items-start gap-3">
                        <span
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-100 text-orange-700">
                            <i class="fas fa-circle-exclamation"></i>
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-base font-bold text-orange-950">Unfinished Maintenance Reason</h2>
                            <p class="mt-1 text-xs text-orange-800">
                                Visit {{ $latestUnfinishedVisit->visit_number }} · Work Order
                                #{{ $latestUnfinishedVisit->id }}
                                @if ($latestUnfinishedVisit->schedule?->schedule_date)
                                    ·
                                    {{ \Carbon\Carbon::parse($latestUnfinishedVisit->schedule->schedule_date)->format('M d, Y') }}
                                @endif
                            </p>
                            <p class="mt-3 whitespace-pre-line break-words text-sm leading-6 text-orange-950">
                                {{ $latestUnfinishedVisit->pending_reason }}</p>
                            @if ($latestUnfinishedVisit->pending_recorded_at)
                                <p class="mt-3 text-xs text-orange-800">
                                    Recorded
                                    {{ \Carbon\Carbon::parse($latestUnfinishedVisit->pending_recorded_at)->format('M d, Y h:i A') }}
                                    PHT
                                </p>
                            @endif
                            <p class="mt-2 text-xs text-orange-800">
                                This reason belongs to the original unfinished visit and remains visible after a follow-up
                                is scheduled.
                            </p>
                        </div>
                    </div>
                    <span
                        class="shrink-0 self-start rounded-full border border-orange-200 bg-white px-3 py-1 text-xs font-semibold text-orange-800">
                        {{ $latestUnfinishedVisit->status === 'Pending Workload' ? 'Pending Workload' : 'Historical Reason' }}
                    </span>
                </div>
            </div>
        @endif

        @if ($plumberWorkOrders->isNotEmpty())

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-100 px-5 py-4 sm:px-6">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center
                            rounded-xl bg-sky-100 text-sky-700">
                            <i class="fas fa-calendar-check"></i>
                        </div>

                        <div>
                            <h2 class="font-semibold text-gray-900">
                                Maintenance Scheduling History
                            </h2>

                            <p class="mt-1 text-sm text-gray-500">
                                Your assigned maintenance visits and scheduling decisions
                                recorded by the Maintenance Manager.
                            </p>
                        </div>

                    </div>

                </div>

                <div class="space-y-4 p-5 sm:p-6">

                    @foreach ($plumberWorkOrders as $order)
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">

                            <div
                                class="flex flex-col gap-3 sm:flex-row
                                sm:items-start sm:justify-between">

                                <div>

                                    <p class="text-sm font-bold text-gray-900">
                                        Visit {{ $order->visit_number }}
                                        · Work Order #{{ $order->id }}
                                    </p>

                                    <p class="mt-2 text-sm text-gray-600">

                                        <i class="far fa-calendar mr-1 text-sky-600"></i>

                                        @if ($order->schedule?->schedule_date)
                                            {{ \Carbon\Carbon::parse($order->schedule->schedule_date)->format('M d, Y') }}
                                        @else
                                            No scheduled date recorded
                                        @endif

                                    </p>

                                </div>

                                <span
                                    class="inline-flex self-start items-center
                                     rounded-full border border-gray-200
                                     bg-white px-3 py-1
                                     text-xs font-semibold text-gray-700">

                                    {{ $order->status }}
                                    ·
                                    {{ $order->approval_status === 'Approved' ? 'Approved' : 'Draft — Awaiting Approval' }}

                                </span>

                            </div>

                            {{-- Pending Workload reason for this visit (retained in history) --}}
                            @if ($order->status === 'Pending Workload' || filled($order->pending_reason))
                                <div class="mt-4 rounded-xl border border-orange-200 bg-orange-50 p-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p class="flex items-center gap-2 text-sm font-bold text-orange-900">
                                            <i class="fas fa-circle-exclamation text-orange-700"></i>
                                            Reason for Unfinished Maintenance
                                        </p>
                                        <span
                                            class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-orange-700 border border-orange-200">
                                            Pending Workload
                                        </span>
                                    </div>

                                    <p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-orange-900">
                                        {{ filled($order->pending_reason) ? $order->pending_reason : 'No reason was recorded for this visit.' }}
                                    </p>

                                    @if ($order->pending_recorded_at)
                                        <p class="mt-3 text-xs text-orange-700">
                                            <i class="far fa-clock mr-1"></i>
                                            Recorded
                                            {{ \Carbon\Carbon::parse($order->pending_recorded_at)->format('M d, Y h:i A') }}
                                            PHT
                                        </p>
                                    @endif

                                    @if ($order->status === 'Pending Workload')
                                        <p class="mt-2 text-xs text-orange-700">
                                            Awaiting the Maintenance Manager's follow-up scheduling decision.
                                        </p>
                                    @endif
                                </div>
                            @endif

                            {{-- Manager's Early Scheduling Justification --}}
                            @if ($order->is_late_addition && filled($order->late_addition_reason))
                                <div
                                    class="mt-4 rounded-xl border border-amber-200
                                    bg-amber-50 p-4">

                                    <div class="flex items-center gap-2">

                                        <i class="fas fa-clipboard-check text-amber-700"></i>

                                        <h3 class="text-sm font-bold text-amber-900">
                                            Early Scheduling Justification
                                        </h3>

                                    </div>

                                    <p
                                        class="mt-2 whitespace-pre-line break-words
                                      text-sm leading-6 text-amber-900">
                                        {{ $order->late_addition_reason }}
                                    </p>

                                    <p class="mt-3 text-xs text-amber-700">
                                        <i class="fas fa-user-shield mr-1"></i>
                                        Recorded by Maintenance Management
                                    </p>

                                </div>
                            @endif

                            {{-- Plumber Replacement History --}}
                            @php
                                $replacementHistories = $plumberReplacementHistories->get($order->id, collect());

                                $replacementHistoryId = 'replacement-history-' . $order->id;
                            @endphp

                            @if ($replacementHistories->isNotEmpty())
                                <div class="mt-4 overflow-hidden rounded-xl border border-indigo-200 bg-white">

                                    {{-- Clickable show/hide header --}}
                                    <button type="button"
                                        onclick="const panel = document.getElementById(@js($replacementHistoryId)); const icon = this.querySelector('[data-toggle-icon]'); const expanded = this.getAttribute('aria-expanded') === 'true'; this.setAttribute('aria-expanded', String(!expanded)); panel.hidden = expanded; icon.classList.toggle('rotate-180', !expanded);"
                                        aria-expanded="false" aria-controls="{{ $replacementHistoryId }}"
                                        class="flex w-full items-center justify-between gap-3 bg-indigo-50 p-4 text-left transition hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                                                <i class="fas fa-user-gear"></i>
                                            </div>

                                            <div class="min-w-0">
                                                <h3 class="text-sm font-bold text-indigo-950">
                                                    Plumber Replacement History
                                                </h3>

                                                <p class="mt-1 text-xs leading-5 text-indigo-800">
                                                    View the reason and details of previous plumber replacements.
                                                </p>

                                                <span
                                                    class="mt-2 inline-flex rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                                    {{ $replacementHistories->count() }}
                                                    {{ $replacementHistories->count() === 1 ? 'record' : 'records' }}
                                                </span>
                                            </div>
                                        </div>

                                        <i data-toggle-icon
                                            class="fas fa-chevron-down shrink-0 text-indigo-600 transition-transform duration-200"></i>
                                    </button>

                                    {{-- Hidden until the user expands the section --}}
                                    <div id="{{ $replacementHistoryId }}" hidden>
                                        <div class="space-y-4 p-4">

                                            @foreach ($replacementHistories as $replacementHistory)
                                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">

                                                    <div class="flex items-start gap-3">
                                                        <div
                                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                                            <i class="fas fa-user"></i>
                                                        </div>

                                                        <div class="min-w-0 flex-1">
                                                            <h4 class="text-sm font-bold text-gray-900">
                                                                Replacement Record {{ $loop->iteration }}
                                                            </h4>

                                                            @if ($replacementHistory->created_at)
                                                                <p class="mt-1 text-xs text-gray-500">
                                                                    {{ \Carbon\Carbon::parse($replacementHistory->created_at)->timezone('Asia/Manila')->format('M d, Y · h:i A') }}
                                                                    PHT
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Previous and replacement plumber names --}}
                                                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">

                                                        <div class="rounded-lg border border-gray-200 bg-white p-3">
                                                            <p
                                                                class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                                Previous Plumber
                                                            </p>

                                                            <p
                                                                class="mt-2 break-words text-sm font-semibold text-gray-900">
                                                                <i class="fas fa-user-minus mr-1 text-rose-500"></i>
                                                                {{ $replacementHistory->previous_plumber_name }}
                                                            </p>
                                                        </div>

                                                        <div class="rounded-lg border border-gray-200 bg-white p-3">
                                                            <p
                                                                class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                                Replacement Plumber
                                                            </p>

                                                            <p
                                                                class="mt-2 break-words text-sm font-semibold text-gray-900">
                                                                <i class="fas fa-user-check mr-1 text-emerald-600"></i>
                                                                {{ $replacementHistory->replacement_plumber_name }}
                                                            </p>
                                                        </div>

                                                    </div>

                                                    {{-- Replacement reason --}}
                                                    <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
                                                        <p
                                                            class="text-xs font-bold uppercase tracking-wide text-amber-800">
                                                            <i class="fas fa-clipboard-list mr-1"></i>
                                                            Reason for Replacement
                                                        </p>

                                                        <p
                                                            class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-gray-800">
                                                            {{ $replacementHistory->reason ?: 'No replacement reason was recorded.' }}
                                                        </p>
                                                    </div>

                                                    {{-- Audit information --}}
                                                    <div class="mt-3 rounded-lg border border-gray-200 bg-white p-3">
                                                        <p class="text-xs text-gray-500">
                                                            <span class="font-semibold text-gray-700">Recorded by:</span>
                                                            {{ $replacementHistory->performer?->full_name ?? ($replacementHistory->performer?->name ?? 'Maintenance Manager (name unavailable)') }}
                                                        </p>

                                                        @if ($replacementHistory->created_at)
                                                            <p class="mt-2 text-xs text-gray-500">
                                                                <span class="font-semibold text-gray-700">Date
                                                                    recorded:</span>
                                                                {{ \Carbon\Carbon::parse($replacementHistory->created_at)->timezone('Asia/Manila')->format('M d, Y h:i A') }}
                                                                PHT
                                                            </p>
                                                        @endif
                                                    </div>

                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                </div>
                            @endif


                        </div>
                    @endforeach

                </div>

            </div>

        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div class="xl:col-span-2 space-y-6">

                <div
                    class="bg-white rounded-2xl
                           border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-gray-100 bg-slate-50/60">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-11 h-11 rounded-xl
                   bg-sky-100 text-sky-700
                   flex items-center justify-center shrink-0">
                                <i class="fas fa-file-lines"></i>
                            </div>

                            <div>

                                <h2 class="font-semibold text-gray-900">
                                    Complaint Information
                                </h2>

                                <p class="text-sm text-gray-500 mt-1">
                                    Consumer and complaint details for field maintenance.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5 sm:p-6 space-y-6">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Consumer
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Walk-in / Unregistered') }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Account Number
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->consumer?->account_number ?? '—' }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Contact Number
                                </p>

                                <p class="font-medium text-gray-900 mt-1">
                                    {{ $complaint->complainant_phone ?? ($complaint->consumer?->phone ?? '—') }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Division
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->division?->name ?? 'Not specified' }}
                                </p>

                            </div>


                            <div class="sm:col-span-2">

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Complaint Type
                                </p>

                                <p class="font-semibold text-gray-900 mt-1">
                                    {{ $complaint->category?->name ?? 'Uncategorized' }}
                                </p>

                            </div>

                        </div>


                        <div class="border-t border-gray-100 pt-5">

                            <p
                                class="text-xs uppercase tracking-wide
                                       font-semibold text-gray-400">
                                Consumer Concern
                            </p>

                            <div
                                class="mt-2 p-4 rounded-xl
                                       bg-gray-50 border border-gray-100">

                                <p
                                    class="text-gray-700 leading-relaxed
                                           whitespace-pre-line">
                                    {{ $complaint->description ?: 'No description provided.' }}
                                </p>

                            </div>

                        </div>


                        {{-- Supporting Photos --}}
                        @php
                            $supportingPhotos = $complaint->photos ?? collect();

                            /*
    |--------------------------------------------------------------------------
    | Temporary Legacy Photo Fallback
    |--------------------------------------------------------------------------
    | This can be removed after old complaints.photo records have been
    | migrated into complaint_photos.
    */
                            $legacyPhoto = $supportingPhotos->isEmpty() ? $complaint->photo : null;

                            $supportingPhotoCount = $supportingPhotos->count() + ($legacyPhoto ? 1 : 0);
                        @endphp

                        @if ($supportingPhotoCount > 0)

                            <div class="border-t border-gray-100 pt-5">

                                <div
                                    class="mb-4 flex flex-col gap-2
                   sm:flex-row sm:items-center sm:justify-between">

                                    <div>

                                        <p
                                            class="text-xs uppercase tracking-wide
                           font-semibold text-gray-400">
                                            Supporting Photos
                                        </p>

                                        <p class="mt-1 text-xs text-gray-500">
                                            Photos submitted with this complaint.
                                        </p>

                                    </div>

                                    <span
                                        class="inline-flex w-fit items-center gap-1.5
                       rounded-full border border-gray-200
                       bg-gray-50 px-2.5 py-1
                       text-xs font-semibold text-gray-600">

                                        <i class="fas fa-images"></i>

                                        {{ $supportingPhotoCount }}
                                        {{ $supportingPhotoCount === 1 ? 'Photo' : 'Photos' }}

                                    </span>

                                </div>


                                <div
                                    class="grid grid-cols-1 gap-4
                   sm:grid-cols-2
                   lg:grid-cols-3">

                                    @foreach ($supportingPhotos as $photo)
                                        <a href="{{ asset('storage/' . $photo->photo) }}" target="_blank"
                                            rel="noopener noreferrer"
                                            class="group relative block overflow-hidden
                           rounded-xl border border-gray-200
                           bg-gray-50">

                                            <img src="{{ asset('storage/' . $photo->photo) }}"
                                                alt="Supporting photo {{ $loop->iteration }}" loading="lazy"
                                                class="h-56 w-full object-cover
                               transition duration-300
                               group-hover:scale-[1.02]">

                                            <div
                                                class="absolute inset-x-0 bottom-0
                               flex items-center justify-between
                               bg-gradient-to-t from-black/70
                               to-transparent px-3 pb-3 pt-8">

                                                <span class="text-xs font-semibold text-white">
                                                    Photo {{ $loop->iteration }}
                                                </span>

                                                <span
                                                    class="flex h-8 w-8 items-center
                                   justify-center rounded-lg
                                   bg-white/20 text-white
                                   backdrop-blur-sm">

                                                    <i class="fas fa-up-right-from-square text-xs"></i>

                                                </span>

                                            </div>

                                        </a>
                                    @endforeach


                                    @if ($legacyPhoto)
                                        <a href="{{ asset('storage/' . $legacyPhoto) }}" target="_blank"
                                            rel="noopener noreferrer"
                                            class="group relative block overflow-hidden
                           rounded-xl border border-gray-200
                           bg-gray-50">

                                            <img src="{{ asset('storage/' . $legacyPhoto) }}" alt="Supporting photo"
                                                loading="lazy"
                                                class="h-56 w-full object-cover
                               transition duration-300
                               group-hover:scale-[1.02]">

                                            <div
                                                class="absolute inset-x-0 bottom-0
                               flex items-center justify-between
                               bg-gradient-to-t from-black/70
                               to-transparent px-3 pb-3 pt-8">

                                                <span class="text-xs font-semibold text-white">
                                                    Photo 1
                                                </span>

                                                <span
                                                    class="flex h-8 w-8 items-center
                                   justify-center rounded-lg
                                   bg-white/20 text-white
                                   backdrop-blur-sm">

                                                    <i class="fas fa-up-right-from-square text-xs"></i>

                                                </span>

                                            </div>

                                        </a>
                                    @endif

                                </div>

                            </div>

                        @endif

                    </div>

                </div>


                @if ($isForwardedRequest && $commercialResolution)

                    <div
                        class="bg-white rounded-2xl
                               border border-gray-200 shadow-sm overflow-hidden">

                        <div class="px-5 sm:px-6 py-5 border-b border-cyan-100 bg-cyan-50/50">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-11 h-11 rounded-xl
                   bg-cyan-100 text-cyan-700
                   flex items-center justify-center shrink-0">
                                    <i class="fas fa-headset"></i>
                                </div>

                                <div>

                                    <h2 class="font-semibold text-gray-900">
                                        Customer Service Processing Result
                                    </h2>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Findings and recommendation recorded before maintenance.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="p-5 sm:p-6 space-y-5">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Findings
                                </p>

                                <div
                                    class="mt-2 p-4 rounded-xl
                                           bg-gray-50 border border-gray-100">
                                    <p
                                        class="text-sm text-gray-700
                                               whitespace-pre-line leading-relaxed">
                                        {{ $commercialResolution->findings ?: 'No findings recorded.' }}
                                    </p>
                                </div>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Resolution / Recommendation
                                </p>

                                <div
                                    class="mt-2 p-4 rounded-xl
                                           bg-sky-50 border border-sky-100">
                                    <p
                                        class="text-sm text-sky-900
                                               whitespace-pre-line leading-relaxed">
                                        {{ $commercialResolution->resolution ?: 'No recommendation recorded.' }}
                                    </p>
                                </div>

                            </div>


                            <div class="grid grid-cols-1 sm:grid-cols-2
                                       gap-4 pt-2">

                                <div>

                                    <p class="text-xs text-gray-400">
                                        Processed By
                                    </p>

                                    <p class="text-sm font-semibold text-gray-800 mt-1">
                                        {{ $processor?->full_name ?? 'Customer Service' }}
                                    </p>

                                </div>


                                <div>

                                    <p class="text-xs text-gray-400">
                                        Forwarded to Maintenance
                                    </p>

                                    <p class="text-sm font-semibold text-gray-800 mt-1">

                                        @if ($forwardedAt)
                                            {{ $forwardedAt->format('M d, Y h:i A') }}
                                        @else
                                            —
                                        @endif

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>
                @elseif ($isEngineering)
                    <div
                        class="bg-white rounded-2xl
                               border border-gray-200 shadow-sm overflow-hidden">

                        <div class="px-5 sm:px-6 py-5 border-b border-green-100 bg-green-50/40">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-11 h-11 rounded-xl
                   bg-green-100 text-green-700
                   flex items-center justify-center shrink-0">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>

                                <div>

                                    <h2 class="font-semibold text-gray-900">
                                        Complaint Assessment
                                    </h2>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Verification result before maintenance assignment.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div
                                class="p-4 rounded-xl
                                       bg-green-50 border border-green-100">

                                <p class="text-xs uppercase tracking-wide font-semibold text-green-700">
                                    Verification Result
                                </p>

                                <p class="text-sm text-green-900 mt-2">
                                    This complaint was verified by Customer Service.
                                </p>

                            </div>


                            <div
                                class="p-4 rounded-xl
                                       bg-sky-50 border border-sky-100">

                                <p class="text-xs uppercase tracking-wide font-semibold text-sky-700">
                                    Next Step
                                </p>

                                <p class="text-sm text-sky-900 mt-2">
                                    The complaint was forwarded for maintenance
                                    and plumber assignment.
                                </p>

                            </div>

                        </div>

                    </div>

                @endif


                <div
                    class="bg-white rounded-2xl
                           border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 sm:px-6 py-5 border-b border-emerald-100 bg-emerald-50/40">

                        <div
                            class="flex flex-col sm:flex-row
                                   sm:items-center sm:justify-between gap-3">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-11 h-11 rounded-xl
               bg-emerald-100 text-emerald-700
               flex items-center justify-center shrink-0">
                                    <i class="fas fa-map-location-dot"></i>
                                </div>

                                <div>

                                    <h2 class="font-semibold text-gray-900">
                                        Service Location
                                    </h2>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Address, landmark, and map for field maintenance.
                                    </p>

                                </div>

                            </div>


                            @if ($hasCoordinates)
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $serviceLatitude }},{{ $serviceLongitude }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center gap-2
                                           px-4 py-2.5 rounded-xl
                                           bg-sky-600 text-white
                                           text-sm font-semibold
                                           hover:bg-sky-700 transition">
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
                                           font-semibold text-gray-400">
                                    Address
                                </p>

                                <p class="text-gray-700 mt-2 leading-relaxed">
                                    {{ $serviceAddress ?: 'No address provided' }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Landmark
                                </p>

                                <p class="text-gray-700 mt-2">
                                    {{ $serviceLandmark ?: 'No landmark provided' }}
                                </p>

                            </div>

                        </div>


                        @if ($hasCoordinates)
                            <div id="complaint-map"
                                class="w-full h-[350px] sm:h-[430px]
                                       rounded-2xl overflow-hidden
                                       border border-gray-200 z-0">
                            </div>
                        @else
                            <div
                                class="rounded-xl border border-amber-200
                                       bg-amber-50 p-4">

                                <div class="flex items-start gap-3">

                                    <i class="fas fa-map-location-dot text-amber-600 mt-0.5"></i>

                                    <div>

                                        <p class="font-semibold text-amber-900">
                                            Map location unavailable
                                        </p>

                                        <p class="text-sm text-amber-800 mt-1">
                                            Use the recorded service address and landmark
                                            to locate the service point.
                                        </p>

                                    </div>

                                </div>

                            </div>
                        @endif

                    </div>

                </div>


                @if ($reportSubmitted)

                    <div
                        class="bg-white rounded-2xl
                               border border-gray-200 shadow-sm overflow-hidden">

                        <div class="px-5 sm:px-6 py-5 border-b border-violet-100 bg-violet-50/40">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-11 h-11 rounded-xl
                   bg-violet-100 text-violet-700
                   flex items-center justify-center shrink-0">
                                    <i class="fas fa-file-circle-check"></i>
                                </div>

                                <div>

                                    <h2 class="font-semibold text-gray-900">
                                        Maintenance Result
                                    </h2>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Submitted maintenance accomplishment information.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="p-5 sm:p-6 space-y-5">

                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Diagnosis / Findings
                                </p>

                                <p
                                    class="text-sm text-gray-700
                                           whitespace-pre-line mt-2">
                                    {{ $report->diagnosis ?: '—' }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Root Cause
                                </p>

                                <p
                                    class="text-sm text-gray-700
                                           whitespace-pre-line mt-2">
                                    {{ $report->root_cause ?: '—' }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-400">
                                    Materials / Parts
                                </p>

                                <p
                                    class="text-sm text-gray-700
                                           whitespace-pre-line mt-2">
                                    {{ $report->materials_parts ?: 'None recorded.' }}
                                </p>

                            </div>


                            @if ($report->technician_notes)
                                <div>

                                    <p
                                        class="text-xs uppercase tracking-wide
                                               font-semibold text-gray-400">
                                        Plumber Notes
                                    </p>

                                    <p
                                        class="text-sm text-gray-700
                                               whitespace-pre-line mt-2">
                                        {{ $report->technician_notes }}
                                    </p>

                                </div>
                            @endif


                            @if ($report->before_photo || $report->after_photo)

                                <div
                                    class="grid grid-cols-1 md:grid-cols-2
                                           gap-4 pt-2">

                                    @if ($report->before_photo)
                                        <div>

                                            <p class="text-xs font-semibold text-gray-500 mb-2">
                                                Before Photo
                                            </p>

                                            <img src="{{ asset('storage/' . $report->before_photo) }}"
                                                alt="Before maintenance"
                                                class="w-full h-56 object-cover
                                                       rounded-xl border border-gray-200">

                                        </div>
                                    @endif


                                    @if ($report->after_photo)
                                        <div>

                                            <p class="text-xs font-semibold text-gray-500 mb-2">
                                                After Photo
                                            </p>

                                            <img src="{{ asset('storage/' . $report->after_photo) }}"
                                                alt="After maintenance"
                                                class="w-full h-56 object-cover
                                                       rounded-xl border border-gray-200">

                                        </div>
                                    @endif

                                </div>

                            @endif

                        </div>

                    </div>

                @endif

            </div>


            <div class="space-y-6">

                <div
                    class="bg-white rounded-2xl
                           border border-gray-200 shadow-sm overflow-hidden">

                    <div class="px-5 py-5 border-b border-indigo-100 bg-indigo-50/40">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-11 h-11 rounded-xl
                   bg-indigo-100 text-indigo-700
                   flex items-center justify-center shrink-0">
                                <i class="fas fa-timeline"></i>
                            </div>

                            <div>

                                <h2 class="font-semibold text-gray-900">
                                    Complaint Tracking
                                </h2>

                                <p class="text-xs text-gray-500 mt-1">
                                    Complete maintenance status and activity history.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5">

                        <div class="relative">

                            <div
                                class="absolute left-[15px] top-4 bottom-4
                                       w-px bg-gray-200">
                            </div>


                            <div class="space-y-6">

                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               bg-green-100 text-green-600
                                               flex items-center justify-center shrink-0">
                                        <i class="fas fa-check text-xs"></i>
                                    </div>

                                    <div class="pt-0.5">

                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $isForwardedRequest ? 'Request Submitted' : 'Complaint Submitted' }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $complaint->created_at?->format('M d, Y • h:i A') }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            Submitted by
                                            {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Consumer') }}
                                        </p>

                                    </div>

                                </div>


                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               {{ $complaint->verified_at ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                               flex items-center justify-center shrink-0">
                                        <i class="fas fa-check text-xs"></i>
                                    </div>

                                    <div class="pt-0.5">

                                        <p class="text-sm font-semibold text-gray-900">
                                            Verified
                                        </p>

                                        @if ($complaint->verified_at)

                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $complaint->verified_at->format('M d, Y • h:i A') }}
                                            </p>

                                            @if ($complaint->verifier)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Verified by
                                                    {{ $complaint->verifier->full_name }}
                                                </p>
                                            @endif
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Pending
                                            </p>

                                        @endif

                                    </div>

                                </div>


                                @if ($hasCsProcessing)

                                    <div class="relative flex gap-4">

                                        <div
                                            class="relative z-10 w-8 h-8 rounded-full
                                                   {{ $initialProcessingStarted ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                                   flex items-center justify-center shrink-0">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>

                                        <div class="pt-0.5">

                                            <p class="text-sm font-semibold text-gray-900">
                                                Initial Processing Started
                                            </p>

                                            @if ($initialProcessingStarted)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $initialProcessingStarted->format('M d, Y • h:i A') }}
                                                </p>

                                                <p class="text-xs text-gray-500 mt-1">
                                                    Started by
                                                    {{ $processor?->full_name ?? 'Customer Service' }}
                                                </p>
                                            @endif

                                        </div>

                                    </div>


                                    <div class="relative flex gap-4">

                                        <div
                                            class="relative z-10 w-8 h-8 rounded-full
                                                   {{ $initialProcessingCompleted ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                                   flex items-center justify-center shrink-0">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>

                                        <div class="pt-0.5">

                                            <p class="text-sm font-semibold text-gray-900">
                                                Initial Processing Completed
                                            </p>

                                            @if ($initialProcessingCompleted)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $initialProcessingCompleted->format('M d, Y • h:i A') }}
                                                </p>

                                                <p class="text-xs text-gray-500 mt-1">
                                                    Processed by
                                                    {{ $processor?->full_name ?? 'Customer Service' }}
                                                </p>
                                            @else
                                                <p class="text-xs text-gray-400 mt-1">
                                                    Pending
                                                </p>
                                            @endif

                                        </div>

                                    </div>


                                    <div class="relative flex gap-4">

                                        <div
                                            class="relative z-10 w-8 h-8 rounded-full
                                                   {{ $forwardedAt ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                                   flex items-center justify-center shrink-0">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>

                                        <div class="pt-0.5">

                                            <p class="text-sm font-semibold text-gray-900">
                                                Forwarded to Maintenance
                                            </p>

                                            @if ($forwardedAt)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $forwardedAt->format('M d, Y • h:i A') }}
                                                </p>

                                                <p class="text-xs text-gray-500 mt-1">
                                                    Forwarded by
                                                    {{ $forwarder?->full_name ?? 'Customer Service' }}
                                                </p>
                                            @else
                                                <p class="text-xs text-gray-400 mt-1">
                                                    Pending
                                                </p>
                                            @endif

                                        </div>

                                    </div>

                                @endif


                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               {{ $currentMaintenanceTeam->isNotEmpty() ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                               flex items-center justify-center shrink-0">
                                        <i class="fas fa-check text-xs"></i>
                                    </div>

                                    <div class="pt-0.5">

                                        <p class="text-sm font-semibold text-gray-900">
                                            Plumber Assigned
                                        </p>

                                        @if ($currentMaintenanceTeam->isNotEmpty())

                                            @if ($currentVisit?->created_at)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Current work order created
                                                    {{ $currentVisit->created_at->timezone('Asia/Manila')->format('M d, Y • h:i A') }}
                                                </p>
                                            @endif

                                            <p class="text-xs text-gray-500 mt-1">
                                                Assigned to {{ $assignedNames }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Pending
                                            </p>

                                        @endif

                                    </div>

                                </div>


                                <div class="relative flex gap-4">

                                    @php
                                        $maintenanceStarted =
                                            !is_null($startedDate) ||
                                            in_array($complaint->status, ['In Progress', 'Completed', 'Closed'], true);
                                    @endphp

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               {{ $maintenanceStarted ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                               flex items-center justify-center shrink-0">
                                        @if ($complaint->status === 'In Progress')
                                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                        @else
                                            <i class="fas fa-check text-xs"></i>
                                        @endif
                                    </div>

                                    <div class="pt-0.5">

                                        <p class="text-sm font-semibold text-gray-900">
                                            Maintenance Started
                                        </p>

                                        @if ($startedDate)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ \Carbon\Carbon::parse($startedDate)->format('M d, Y • h:i A') }}
                                            </p>

                                            <p class="text-xs text-gray-500 mt-1">
                                                Handled by {{ $assignedNames }}
                                            </p>
                                        @elseif ($complaint->status === 'Assigned')
                                            <p class="text-xs text-gray-400 mt-1">
                                                Waiting to start
                                            </p>
                                        @endif

                                    </div>

                                </div>


                                <div class="relative flex gap-4">

                                    @php
                                        $maintenanceAccomplished =
                                            $reportSubmitted ||
                                            in_array($complaint->status, ['Completed', 'Closed'], true);
                                    @endphp

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               {{ $maintenanceAccomplished ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                               flex items-center justify-center shrink-0">
                                        @if ($complaint->status === 'Completed' && $report?->review_status === 'Pending Review')
                                            <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                        @else
                                            <i class="fas fa-check text-xs"></i>
                                        @endif
                                    </div>

                                    <div class="pt-0.5">

                                        <p class="text-sm font-semibold text-gray-900">
                                            Maintenance Accomplished
                                        </p>

                                        @if ($report?->submitted_at)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $report->submitted_at->format('M d, Y • h:i A') }}
                                            </p>

                                            <p class="text-xs text-gray-500 mt-1">
                                                Accomplished by {{ $assignedNames }}
                                            </p>
                                        @elseif ($completedDate)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $completedDate->format('M d, Y • h:i A') }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Pending
                                            </p>
                                        @endif

                                    </div>

                                </div>


                                <div class="relative flex gap-4">

                                    @php
                                        $reviewCompleted = $report?->review_status === 'Approved';

                                        $reviewReturned = $report?->review_status === 'Returned';

                                        $reviewPending = $report?->review_status === 'Pending Review';
                                    @endphp

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               @if ($reviewCompleted) bg-green-100 text-green-600
                                               @elseif ($reviewReturned)
                                                   bg-red-100 text-red-600
                                               @elseif ($reviewPending)
                                                   bg-violet-100 text-violet-600
                                               @else
                                                   bg-gray-100 text-gray-400 @endif
                                               flex items-center justify-center shrink-0">

                                        @if ($reviewCompleted)
                                            <i class="fas fa-check text-xs"></i>
                                        @elseif ($reviewReturned)
                                            <i class="fas fa-rotate-left text-xs"></i>
                                        @elseif ($reviewPending)
                                            <span class="w-2.5 h-2.5 rounded-full bg-violet-500"></span>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                        @endif

                                    </div>

                                    <div class="pt-0.5">

                                        <p class="text-sm font-semibold text-gray-900">
                                            Manager Review
                                        </p>

                                        @if ($reviewCompleted)

                                            <p class="text-xs text-green-600 mt-1">
                                                Approved
                                            </p>

                                            @if ($report?->reviewed_at)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $report->reviewed_at->format('M d, Y • h:i A') }}
                                                </p>
                                            @endif
                                        @elseif ($reviewReturned)
                                            <p class="text-xs text-red-600 mt-1">
                                                Returned for correction
                                            </p>
                                        @elseif ($reviewPending)
                                            <p class="text-xs text-violet-600 mt-1">
                                                Pending review
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Pending
                                            </p>

                                        @endif

                                    </div>

                                </div>


                                <div class="relative flex gap-4">

                                    <div
                                        class="relative z-10 w-8 h-8 rounded-full
                                               {{ $complaint->status === 'Closed' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}
                                               flex items-center justify-center shrink-0">
                                        @if ($complaint->status === 'Closed')
                                            <i class="fas fa-check text-xs"></i>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                                        @endif
                                    </div>

                                    <div class="pt-0.5">

                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $isForwardedRequest ? 'Request Closed' : 'Complaint Closed' }}
                                        </p>

                                        @if ($complaint->status === 'Closed')
                                            <p class="text-xs text-green-600 mt-1">
                                                Finalized by Maintenance Management
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-400 mt-1">
                                                Pending
                                            </p>
                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


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
                                        Plumbers assigned to work on this complaint.
                                    </p>

                                </div>

                            </div>

                            <span
                                class="inline-flex items-center justify-center
                   min-w-9 h-9 px-2.5 rounded-full
                   bg-blue-100 text-blue-700
                   text-sm font-bold">
                                {{ $currentMaintenanceTeam->count() }}
                            </span>

                        </div>

                    </div>


                    <div class="p-5">

                        <div class="space-y-3">

                            @foreach ($currentMaintenanceTeam as $technician)
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

                                        </div>


                                        <p class="text-xs text-gray-500 mt-0.5">
                                            Maintenance Plumber
                                        </p>


                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $currentVisit?->approval_status === 'Approved' ? 'Approved Work Order' : 'Draft — Awaiting Manager Approval' }}
                                        </p>

                                    </div>

                                </div>
                            @endforeach

                            @if ($previousMaintenanceTeam->isNotEmpty())
                                <details class="rounded-xl border border-gray-200 bg-white p-3">
                                    <summary class="cursor-pointer text-xs font-semibold text-gray-600">
                                        View Previous Complaint Assignments
                                        ({{ $previousMaintenanceTeam->count() }})
                                    </summary>

                                    <div class="mt-3 space-y-2">
                                        @foreach ($previousMaintenanceTeam as $previousTechnician)
                                            <div class="rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-600">
                                                <i class="fas fa-clock-rotate-left mr-1"></i>
                                                {{ $previousTechnician->full_name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    @if ($hasCoordinates)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const latitude = {{ (float) $serviceLatitude }};
                const longitude = {{ (float) $serviceLongitude }};

                const mapElement =
                    document.getElementById('complaint-map');

                if (!mapElement) {
                    return;
                }

                const map = L.map(
                    'complaint-map', {
                        scrollWheelZoom: false
                    }
                ).setView(
                    [latitude, longitude],
                    16
                );

                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
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
                        <strong>
                            {{ addslashes($complaint->complaint_no) }}
                        </strong>

                        <br>

                        <span>
                            {{ addslashes($complaint->category?->name ?? 'Service Complaint') }}
                        </span>

                        <br><br>

                        <span style="font-size:12px;color:#6b7280">
                            {{ addslashes($serviceAddress ?? '') }}
                        </span>
                    </div>
                `);

                setTimeout(function() {
                    map.invalidateSize();
                }, 300);

            });
        </script>
    @endif

@endsection
