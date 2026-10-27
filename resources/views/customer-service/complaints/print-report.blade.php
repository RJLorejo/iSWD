@extends('layouts.report')

@section('title', 'Customer Service Complaint Report')

@section('back-url', route('customer-service.complaints.index'))

@section('report-title', 'Customer Service Complaint Report')

@section('report-subtitle', 'Complaint Records and Status Summary')

@section('prepared-by-role', 'Customer Service Personnel')

@section('report-content')

    @php
        $reportCount = $complaints->count();

        $search = request('search');
        $status = request('status');
        $urgencyFilter = request('urgency');
        $type = request('type');
        $division = request('division');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
    @endphp

    <section class="report-section">

        <h3 class="report-section-title">
            Report Criteria
        </h3>

        <div class="report-grid">

            <div class="report-field">
                <div class="report-field-label">Search</div>
                <div class="report-field-value">
                    {{ $search ?: 'All complaints' }}
                </div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Status</div>
                <div class="report-field-value">
                    {{ $status ?: 'All statuses' }}
                </div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Urgency</div>
                <div class="report-field-value">
                    @switch($urgencyFilter)
                        @case('High')
                            High
                            @break
                        @case('Moderate')
                            Moderate
                            @break
                        @case('Low')
                            Low
                            @break
                        @case('not_assessed')
                            Not assessed
                            @break
                        @default
                            All urgency levels
                    @endswitch
                </div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Complaint Type</div>
                <div class="report-field-value">
                    {{ $type ?: 'All complaint types' }}
                </div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Division</div>
                <div class="report-field-value">
                    {{ $division ?: 'All divisions' }}
                </div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Date Range</div>
                <div class="report-field-value">
                    {{ $dateFrom ?: 'Start' }}
                    &mdash;
                    {{ $dateTo ?: 'Present' }}
                </div>
            </div>

        </div>

    </section>

    <section class="report-section">

        <div class="report-section-heading-row">

            <h3 class="report-section-title">
                Complaint Records
            </h3>

            <div class="report-record-count">
                Total Records:
                <strong>{{ $reportCount }}</strong>
            </div>

        </div>

        <div class="report-table-wrapper">

            <table class="report-table">

                <colgroup>
                    <col style="width: 14%;">
                    <col style="width: 21%;">
                    <col style="width: 19%;">
                    <col style="width: 16%;">
                    <col style="width: 13%;">
                    <col style="width: 17%;">
                </colgroup>

                <thead>
                    <tr>
                        <th>Complaint No.</th>
                        <th>Consumer</th>
                        <th>Complaint Type</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Reported</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($complaints as $complaint)

                        @php
                            $analysis = $complaint->aiAnalysis;

                            // Show the CS-verified urgency first.
                            // Use the AI assessment only if CS has not verified it.
                            $verifiedUrgency = $analysis?->verified_urgency_level;
                            $aiUrgency = $analysis?->urgency_level;
                            $urgency = $verifiedUrgency ?: $aiUrgency;

                            $urgencyClass = match ($urgency) {
                                'High' => 'high',
                                'Moderate' => 'moderate',
                                'Low' => 'low',
                                default => '',
                            };

                            $consumerName = $complaint->consumer?->user?->full_name
                                ?? $complaint->consumer?->full_name
                                ?? 'N/A';

                            $complaintType = $complaint->complaint_type
                                ?? $complaint->type
                                ?? 'N/A';

                            $complaintNumber = $complaint->complaint_number
                                ?? ('CMP-' . str_pad($complaint->id, 6, '0', STR_PAD_LEFT));

                            $reportedAt = $complaint->created_at;
                        @endphp

                        <tr>

                            <td>
                                {{ $complaintNumber }}
                            </td>

                            <td>
                                {{ $consumerName }}
                            </td>

                            <td>
                                {{ $complaintType }}
                            </td>

                            <td>
                                @if ($urgency)
                                    <strong>{{ strtoupper($urgency) }}</strong>
                                @else
                                    <span style="color: #6b7280;">
                                        NOT ASSESSED
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ strtoupper(str_replace('_', ' ', $complaint->status ?? 'N/A')) }}
                            </td>

                            <td>
                                {{ $reportedAt?->format('M d, Y') ?? 'N/A' }}
                                @if ($reportedAt)
                                    <br>
                                    <span style="color: #6b7280;">
                                        {{ $reportedAt->format('h:i A') }}
                                    </span>
                                @endif
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="text-center">
                                No complaint records found for the selected criteria.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

@endsection
