@extends('layouts.report')

@section('title', 'Technician Work Summary')

@section('report-title', 'Technician Work Summary')

@section('report-subtitle', 'Field Maintenance Accomplishment Summary')

@section('report-period')
    Reporting Period:
    {{ $from->format('F d, Y') }}
    —
    {{ $to->format('F d, Y') }}
@endsection

@section('prepared-by-name')
    {{ auth()->user()->full_name ?? (auth()->user()->name ?? 'N/A') }}
@endsection

@section('prepared-by-role', 'Maintenance Technician')

@push('styles')
    <style>
        .work-summary-cards {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 4mm;
            margin-bottom: 5mm;
        }

        .work-summary-card {
            border: 1px solid #d1d5db;
            padding: 3mm 4mm;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .work-summary-label {
            color: #6b7280;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
        }

        .work-summary-value {
            margin-top: 1mm;
            color: #111827;
            font-size: 14pt;
            font-weight: 700;
        }

        .technician-work-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .technician-work-table th:nth-child(1) {
            width: 15%;
        }

        .technician-work-table th:nth-child(2) {
            width: 18%;
        }

        .technician-work-table th:nth-child(3) {
            width: 20%;
        }

        .technician-work-table th:nth-child(4) {
            width: 12%;
        }

        .technician-work-table th:nth-child(5) {
            width: 15%;
        }

        .technician-work-table th:nth-child(6) {
            width: 20%;
        }

        .complaint-number {
            font-weight: 700;
        }

        .table-secondary {
            margin-top: 0.8mm;
            color: #6b7280;
            font-size: 6.5pt;
            line-height: 1.3;
        }

        .urgency-value {
            font-weight: 700;
        }

        @page {
            size: A4 landscape;
            margin: 9mm;
        }

        @media print {
            .work-summary-cards {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 4mm !important;
            }

            .work-summary-card {
                padding: 2.5mm 3mm !important;
            }

            .work-summary-value {
                font-size: 12pt !important;
            }

            .technician-work-table {
                width: 100% !important;
                table-layout: fixed !important;
                font-size: 7pt !important;
            }

            .technician-work-table th {
                font-size: 6.5pt !important;
            }

            .technician-work-table th,
            .technician-work-table td {
                padding: 1.7mm 1.5mm !important;
                vertical-align: top;
                overflow-wrap: anywhere;
            }
        }
    </style>
@endpush

@section('report-content')

    @php
        /*
        |--------------------------------------------------------------------------
        | Compatible with current print controller
        |--------------------------------------------------------------------------
        |
        | The current print() method may still provide $complaints,
        | $total and $averageCompletionHours.
        |
        | If $completedWorkOrders is supplied by the updated print()
        | method, the table uses actual maintenance visit records.
        |
        */

        $hasVisitRecords = isset($completedWorkOrders);

        $records = $hasVisitRecords ? $completedWorkOrders : $complaints ?? collect();

        $recordCount = $records->count();

        $averageMinutes =
            $averageCompletionMinutes ??
            (isset($averageCompletionHours) && $averageCompletionHours !== null ? $averageCompletionHours * 60 : null);

        $averageDisplay =
            $averageMinutes === null
                ? 'N/A'
                : ($averageMinutes < 60
                    ? number_format($averageMinutes, 1) . ' mins'
                    : number_format($averageMinutes / 60, 2) . ' hrs');
    @endphp

    {{-- ===================================================== --}}
    {{-- SUMMARY --}}
    {{-- ===================================================== --}}

    <section class="report-section avoid-break">

        <div class="work-summary-cards">

            <div class="work-summary-card">
                <div class="work-summary-label">
                    Accomplished {{ $hasVisitRecords ? 'Visits' : 'Complaints' }}
                </div>

                <div class="work-summary-value">
                    {{ $recordCount }}
                </div>
            </div>

            <div class="work-summary-card">
                <div class="work-summary-label">
                    Average Completion Time
                </div>

                <div class="work-summary-value">
                    {{ $averageDisplay }}
                </div>
            </div>

        </div>

    </section>

    {{-- ===================================================== --}}
    {{-- ACCOMPLISHED WORK --}}
    {{-- ===================================================== --}}

    <section class="report-section">

        <div class="report-section-heading-row">

            <h3 class="report-section-title">
                Accomplished Maintenance Work
            </h3>

            <div class="report-record-count">
                Total Records:
                <strong>{{ $recordCount }}</strong>
            </div>

        </div>

        @if ($recordCount > 0)

            <div class="report-table-wrapper">

                <table class="report-table technician-work-table">

                    <thead>
                        <tr>
                            <th>Complaint / Visit</th>
                            <th>Consumer</th>
                            <th>Complaint Type</th>
                            <th>Urgency</th>
                            <th>Duration</th>
                            <th>Accomplished</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($records as $record)
                            @php
                                $complaint = $hasVisitRecords ? $record->complaint : $record;

                                $urgency = strtoupper(
                                    trim((string) ($complaint?->aiAnalysis?->verified_urgency_level ?? '')),
                                );

                                $startedAt = $hasVisitRecords ? $record->started_at : null;

                                $completedAt = $hasVisitRecords ? $record->completed_at : $complaint?->completed_at;

                                $durationDisplay = 'N/A';

                                if ($startedAt && $completedAt) {
                                    $seconds = $startedAt->diffInSeconds($completedAt, false);

                                    if ($seconds >= 0) {
                                        $durationDisplay =
                                            $seconds < 3600
                                                ? number_format($seconds / 60, 1) . ' mins'
                                                : number_format($seconds / 3600, 2) . ' hrs';
                                    }
                                }
                            @endphp

                            <tr>

                                <td>
                                    <div class="complaint-number">
                                        {{ $complaint?->complaint_no ?? 'N/A' }}
                                    </div>

                                    @if ($hasVisitRecords)
                                        <div class="table-secondary">
                                            Visit {{ $record->visit_number ?? '—' }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    @if ($complaint?->consumer)
                                        <div>
                                            {{ $complaint->consumer->full_name }}
                                        </div>

                                        @if ($complaint->consumer->account_number)
                                            <div class="table-secondary">
                                                Account:
                                                {{ $complaint->consumer->account_number }}
                                            </div>
                                        @endif
                                    @elseif ($complaint?->complainant_name)
                                        <div>
                                            {{ $complaint->complainant_name }}
                                        </div>
                                    @else
                                        <span class="table-secondary">
                                            No complainant information
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $complaint?->category?->name ?? 'Uncategorized' }}
                                </td>

                                <td>
                                    <span class="urgency-value">
                                        {{ in_array($urgency, ['HIGH', 'MODERATE', 'LOW'], true) ? $urgency : 'NOT ASSESSED' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $durationDisplay }}
                                </td>

                                <td>
                                    {{ $completedAt?->format('M d, Y') ?? '—' }}
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>
        @else
            <div class="report-text-block text-center">
                No accomplished maintenance work was found
                for the selected reporting period.
            </div>

        @endif

    </section>

@endsection
