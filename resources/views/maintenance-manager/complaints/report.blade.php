@extends('layouts.report')

@section('title', 'Complaint Report - ' . $complaint->complaint_no)

@section('report-title', 'Individual Complaint and Maintenance Report')
@section('report-subtitle', 'Complaint ' . $complaint->complaint_no . ' · Maintenance Management')
@section('report-period', 'Submitted: ' . ($complaint->created_at?->format('M d, Y h:i A') ?? 'Not recorded'))


@section('prepared-by-name', auth()->user()?->full_name ?? (auth()->user()?->email ?? 'Maintenance Manager'))

@section('prepared-by-role', 'Maintenance Manager')


@section('report-content')

    @php

        $report = $complaint->maintenanceReport;
        $completedWorkOrder =
            $completedWorkOrder ??
            \App\Models\MaintenanceWorkOrder::with('schedule')
                ->where('complaint_id', $complaint->id)
                ->where('status', 'Completed')
                ->orderByDesc('visit_number')
                ->first();
        $commercialResolution = $complaint->commercialResolution;
        $isCommercial = str_contains(strtolower((string) $complaint->division?->name), 'commercial');
        $serviceAddress =
            $isCommercial && $complaint->consumer?->address?->full_address
                ? $complaint->consumer->address->full_address
                : ($complaint->address ?:
                'Not recorded');

        $displayStatus = $complaint->status === 'Completed' ? 'Accomplished' : $complaint->status;
        $urgency = $complaint->aiAnalysis?->verified_urgency_level ?: $complaint->aiAnalysis?->urgency_level ?: null;
        $consumerName = $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'N/A');

        $maintenanceTeam = $complaint->technicians->pluck('full_name')->filter()->join(', ');

        $maintenanceTeam = $maintenanceTeam ?: 'N/A';

        $reviewerName = $report?->reviewer?->full_name ?? 'Maintenance Manager';

    @endphp


    @if (
        $commercialResolution &&
            ($commercialResolution->started_at ||
                $commercialResolution->forwarded_to_maintenance_at ||
                filled($commercialResolution->findings)))
        <section class="report-section avoid-break">
            <h3 class="report-section-title">Customer Service Processing / Maintenance Handoff</h3>
            <div class="report-grid">
                <div class="report-field">
                    <div class="report-field-label">Processed By</div>
                    <div class="report-field-value">{{ $commercialResolution->processor?->full_name ?? 'Customer Service' }}
                    </div>
                </div>
                <div class="report-field">
                    <div class="report-field-label">Forwarded to Maintenance</div>
                    <div class="report-field-value">
                        {{ $commercialResolution->forwarded_to_maintenance_at?->format('M d, Y h:i A') ?? 'Not forwarded' }}
                    </div>
                </div>
                <div class="report-field" style="grid-column:1/-1">
                    <div class="report-field-label">Findings</div>
                    <div class="report-field-value" style="white-space:pre-line">
                        {{ $commercialResolution->findings ?: 'Not recorded' }}</div>
                </div>
                <div class="report-field" style="grid-column:1/-1">
                    <div class="report-field-label">Resolution / Recommendation</div>
                    <div class="report-field-value" style="white-space:pre-line">
                        {{ $commercialResolution->resolution_remarks ?: 'Not recorded' }}</div>
                </div>
            </div>
        </section>
    @endif

    <section class="report-section avoid-break">

        <h3 class="report-section-title">
            Complaint Information
        </h3>

        <div class="report-grid">

            <div class="report-field">

                <div class="report-field-label">
                    Complaint Number
                </div>

                <div class="report-field-value">
                    {{ $complaint->complaint_no }}
                </div>

            </div>


            <div class="report-field">

                <div class="report-field-label">
                    Status
                </div>

                <div class="report-field-value">
                    {{ $displayStatus }}
                </div>

            </div>

            <div class="report-field">
                <div class="report-field-label">
                    Urgency
                </div>

                <div class="report-field-value"
                    style="font-weight:700; color: {{ $urgency === 'High' ? '#b91c1c' : ($urgency === 'Moderate' ? '#92400e' : ($urgency === 'Low' ? '#166534' : '#64748b')) }};">
                    {{ $urgency ?? 'Not Assessed' }}
                </div>
            </div>



            <div class="report-field">

                <div class="report-field-label">
                    Division
                </div>

                <div class="report-field-value">
                    {{ $complaint->division?->name ?? 'N/A' }}
                </div>

            </div>


            <div class="report-field">

                <div class="report-field-label">
                    Complaint Type
                </div>

                <div class="report-field-value">
                    {{ $complaint->category?->name ?? 'N/A' }}
                </div>

            </div>


            <div class="report-field">

                <div class="report-field-label">
                    Consumer
                </div>

                <div class="report-field-value">
                    {{ $consumerName }}
                </div>

            </div>


            <div class="report-field">

                <div class="report-field-label">
                    Account Number
                </div>

                <div class="report-field-value">
                    {{ $complaint->consumer?->account_number ?? 'N/A' }}
                </div>

            </div>


            <div class="report-field" style="grid-column: 1 / -1;">

                <div class="report-field-label">
                    Service Location
                </div>

                <div class="report-field-value">
                    {{ $serviceAddress }}
                </div>

            </div>


            @if ($complaint->landmark)
                <div class="report-field" style="grid-column: 1 / -1;">

                    <div class="report-field-label">
                        Landmark
                    </div>

                    <div class="report-field-value">
                        {{ $complaint->landmark }}
                    </div>

                </div>
            @endif

        </div>

    </section>


    <section class="report-section avoid-break">

        <h3 class="report-section-title">
            Maintenance Team
        </h3>

        <div class="report-field">

            <div class="report-field-label">
                Assigned Plumber(s)
            </div>

            <div class="report-field-value">
                {{ $maintenanceTeam }}
            </div>

        </div>

    </section>


    @if ($report && $report->submitted_at)
        <section class="report-section avoid-break">

            <h3 class="report-section-title">
                Diagnosis / Findings
            </h3>

            <div class="report-text-block">
                {{ $report->diagnosis ?: 'No diagnosis or findings recorded.' }}
            </div>

        </section>


        <section class="report-section avoid-break">

            <h3 class="report-section-title">
                Root Cause
            </h3>

            <div class="report-text-block">
                {{ $report->root_cause ?: 'No root cause recorded.' }}
            </div>

        </section>


        <section class="report-section avoid-break">

            <h3 class="report-section-title">
                Materials / Parts
            </h3>

            <div class="report-text-block">
                {{ $report->materials_parts ?: 'None recorded.' }}
            </div>

        </section>


        <section class="report-section avoid-break">

            <h3 class="report-section-title">
                Plumber Notes
            </h3>

            <div class="report-text-block">
                {{ $report->technician_notes ?: 'No additional notes.' }}
            </div>

        </section>


        <section class="report-section">

            <h3 class="report-section-title">
                Before & After Photos
            </h3>

            <div class="report-image-grid">

                <div class="report-image-box avoid-break">

                    <div class="report-image-label">
                        Before Maintenance
                    </div>

                    @if ($report->before_photo)
                        <img src="{{ asset('storage/' . $report->before_photo) }}" alt="Before maintenance photo"
                            class="report-image">
                    @else
                        <div class="report-empty-image">
                            No before photo available
                        </div>
                    @endif

                </div>


                <div class="report-image-box avoid-break">

                    <div class="report-image-label">
                        After Maintenance
                    </div>

                    @if ($report->after_photo)
                        <img src="{{ asset('storage/' . $report->after_photo) }}" alt="After maintenance photo"
                            class="report-image">
                    @else
                        <div class="report-empty-image">
                            No after photo available
                        </div>
                    @endif

                </div>

            </div>

        </section>


        <section class="report-section">
            <h3 class="report-section-title">Maintenance Visit History and Working Duration</h3>
            @php
                // Each recorded visit contributes only its own active maintenance interval.
                $maintenanceVisits = \App\Models\MaintenanceWorkOrder::with('schedule')
                    ->where('complaint_id', $complaint->id)
                    ->whereNotNull('started_at')
                    ->orderBy('visit_number')
                    ->get()
                    ->map(function ($order) {
                        $end = $order->status === 'Completed' ? $order->completed_at : $order->pending_recorded_at;
                        $seconds = $order->started_at && $end ? $order->started_at->diffInSeconds($end, false) : null;
                        $order->recorded_end = $end;
                        $order->recorded_seconds = $seconds !== null && $seconds >= 0 ? (int) round($seconds) : null;
                        return $order;
                    });
                $timedVisits = $maintenanceVisits->filter(fn($order) => $order->recorded_seconds !== null);
                $totalMaintenanceSeconds = $timedVisits->sum('recorded_seconds');
                $durationLabel = function ($seconds) {
                    return intdiv($seconds, 3600) .
                        ' hr ' .
                        intdiv($seconds % 3600, 60) .
                        ' min ' .
                        $seconds % 60 .
                        ' sec';
                };
            @endphp
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:9pt;">
                    <thead>
                        <tr style="background:#f1f5f9;">
                            @foreach (['Visit / Result', 'Scheduled Date', 'Maintenance Started At', 'Pending / Accomplished At', 'Working Duration'] as $heading)
                                <th style="border:1px solid #cbd5e1;padding:7px;text-align:left;">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($maintenanceVisits as $visit)
                            <tr style="page-break-inside:avoid;">
                                <td style="border:1px solid #cbd5e1;padding:7px;">Visit {{ $visit->visit_number }}<br>
                                    <small>{{ $visit->status === 'Completed' ? 'Accomplished' : $visit->status }}</small>
                                </td>
                                <td style="border:1px solid #cbd5e1;padding:7px;">
                                    {{ $visit->schedule?->schedule_date ? \Carbon\Carbon::parse($visit->schedule->schedule_date)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td style="border:1px solid #cbd5e1;padding:7px;">
                                    {{ $visit->started_at?->copy()->timezone('Asia/Manila')->format('M d, Y h:i:s A') ?? 'N/A' }}
                                </td>
                                <td style="border:1px solid #cbd5e1;padding:7px;">
                                    {{ $visit->recorded_end?->copy()->timezone('Asia/Manila')->format('M d, Y h:i:s A') ?? 'Not recorded' }}
                                </td>
                                <td style="border:1px solid #cbd5e1;padding:7px;">
                                    {{ $visit->recorded_seconds !== null ? $durationLabel($visit->recorded_seconds) : 'Not available' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="border:1px solid #cbd5e1;padding:12px;text-align:center;">No
                                    started maintenance visits recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="report-field" style="margin-top:3mm;padding:3mm;border:1px solid #cbd5e1;">
                <div class="report-field-label">Total Recorded Maintenance Working Duration</div>
                <div class="report-field-value" style="font-weight:700;">
                    {{ $timedVisits->isNotEmpty() ? $durationLabel($totalMaintenanceSeconds) : 'Not available' }}</div>
            </div>
            <p class="report-meta-secondary" style="margin-top:2mm;">Total is the sum of recorded Start-to-Pending and
                Start-to-Accomplished intervals. Time between visits is excluded. Incomplete or invalid intervals are not
                counted. Times shown in Philippine Time (PHT).</p>
        </section>


        @if ($report->submitted_at || $report->reviewed_at || $report->review_remarks)

            <section class="report-section avoid-break">

                <h3 class="report-section-title">
                    Management Review
                </h3>

                <div class="report-grid">

                    <div class="report-field">

                        <div class="report-field-label">
                            Report Submitted
                        </div>

                        <div class="report-field-value">

                            {{ $report->submitted_at?->timezone('Asia/Manila')->format('F d, Y h:i:s A') ?? 'N/A' }}

                        </div>

                    </div>


                    <div class="report-field">

                        <div class="report-field-label">
                            Review Result
                        </div>

                        <div class="report-field-value">

                            @if ($report->review_status === 'Approved')
                                Approved by
                                {{ $reviewerName }}
                            @elseif ($report->review_status === 'Returned')
                                Returned by
                                {{ $reviewerName }}
                            @else
                                {{ $report->review_status ?? 'Pending Review' }}
                            @endif

                        </div>


                        @if ($report->reviewed_at)
                            <div class="report-meta-secondary" style="margin-top: 1mm;">
                                {{ $report->reviewed_at->format('F d, Y h:i A') }}
                            </div>
                        @endif

                    </div>

                </div>


                @if ($report->review_remarks)
                    <div class="report-field" style="margin-top: 3mm;">

                        <div class="report-field-label">
                            Manager Remarks
                        </div>

                        <div class="report-text-block">
                            {{ $report->review_remarks }}
                        </div>

                    </div>
                @endif

            </section>

        @endif
    @else
        <section class="report-section avoid-break">
            <h3 class="report-section-title">Maintenance Accomplishment</h3>
            <p>No submitted maintenance accomplishment report is available.</p>
        </section>
    @endif
@endsection
