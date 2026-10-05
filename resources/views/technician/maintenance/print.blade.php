@extends('layouts.report')

@section(
    'title',
    'Maintenance Accomplishment Report - ' .
    $complaint->complaint_no
)

@section('report-title', 'Maintenance Accomplishment Report')


@section('prepared-by-name')
    {{ $complaint->maintenanceReport?->technician?->full_name
        ?? auth()->user()->full_name
        ?? auth()->user()->name
        ?? 'N/A' }}
@endsection

@section('prepared-by-role', 'Maintenance Technician')


@section('report-content')

    @php

        $report =
            $complaint->maintenanceReport;

        $displayStatus =
            $complaint->status === 'Completed'
                ? 'Accomplished'
                : $complaint->status;

        $consumerName =
            $complaint->consumer?->full_name
            ?? (
                $complaint->complainant_name
                ?? 'N/A'
            );

        $maintenanceTeam =
            $complaint
                ->technicians
                ->pluck('full_name')
                ->filter()
                ->join(', ');

        $maintenanceTeam =
            $maintenanceTeam ?: 'N/A';

        $reviewerName =
            $report?->reviewer?->full_name
            ?? 'Maintenance Manager';

    @endphp


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


            <div
                class="report-field"
                style="grid-column: 1 / -1;"
            >

                <div class="report-field-label">
                    Service Location
                </div>

                <div class="report-field-value">
                    {{ $complaint->address ?: 'N/A' }}
                </div>

            </div>


            @if ($complaint->landmark)

                <div
                    class="report-field"
                    style="grid-column: 1 / -1;"
                >

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


    <section class="report-section avoid-break">

        <h3 class="report-section-title">
            Diagnosis / Findings
        </h3>

        <div class="report-text-block">
            {{ $report->diagnosis
                ?: 'No diagnosis or findings recorded.' }}
        </div>

    </section>


    <section class="report-section avoid-break">

        <h3 class="report-section-title">
            Root Cause
        </h3>

        <div class="report-text-block">
            {{ $report->root_cause
                ?: 'No root cause recorded.' }}
        </div>

    </section>


    <section class="report-section avoid-break">

        <h3 class="report-section-title">
            Materials / Parts
        </h3>

        <div class="report-text-block">
            {{ $report->materials_parts
                ?: 'None recorded.' }}
        </div>

    </section>


    <section class="report-section avoid-break">

        <h3 class="report-section-title">
            Plumber Notes
        </h3>

        <div class="report-text-block">
            {{ $report->technician_notes
                ?: 'No additional notes.' }}
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

                    <img
                        src="{{ asset(
                            'storage/' .
                            $report->before_photo
                        ) }}"
                        alt="Before maintenance photo"
                        class="report-image"
                    >

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

                    <img
                        src="{{ asset(
                            'storage/' .
                            $report->after_photo
                        ) }}"
                        alt="After maintenance photo"
                        class="report-image"
                    >

                @else

                    <div class="report-empty-image">
                        No after photo available
                    </div>

                @endif

            </div>

        </div>

    </section>


    <section class="report-section avoid-break">

        <h3 class="report-section-title">
            Maintenance Record
        </h3>

        <div class="report-grid">

            <div class="report-field">

                <div class="report-field-label">
                    Maintenance Started
                </div>

                <div class="report-field-value">

                    {{ $report->started_at
                        ?->format('F d, Y h:i A')
                        ?? 'N/A' }}

                </div>

            </div>


            <div class="report-field">

                <div class="report-field-label">
                    Accomplished
                </div>

                <div class="report-field-value">

                    {{ $complaint->completed_at
                        ?->format('F d, Y h:i A')
                        ?? (
                            $report->submitted_at
                                ?->format('F d, Y h:i A')
                            ?? 'N/A'
                        ) }}

                </div>

            </div>

        </div>

    </section>


    @if (
        $report->submitted_at ||
        $report->reviewed_at ||
        $report->review_remarks
    )

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

                        {{ $report->submitted_at
                            ?->format('F d, Y h:i A')
                            ?? 'N/A' }}

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

                            {{ $report->review_status
                                ?? 'Pending Review' }}

                        @endif

                    </div>


                    @if ($report->reviewed_at)

                        <div
                            class="report-meta-secondary"
                            style="margin-top: 1mm;"
                        >
                            {{ $report->reviewed_at
                                ->format('F d, Y h:i A') }}
                        </div>

                    @endif

                </div>

            </div>


            @if ($report->review_remarks)

                <div
                    class="report-field"
                    style="margin-top: 3mm;"
                >

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

@endsection
