
@extends('layouts.report')

@section('title', 'Maintenance Technician Schedule Report')

@section('prepared-by-name')
    {{ auth()->user()->full_name ?? auth()->user()->name ?? 'N/A' }}
@endsection

@section('prepared-by-role', 'Maintenance Technician')

@push('styles')
<style>
    @page {
        size: A4 landscape;
        margin: 9mm;
    }

    .technician-schedule-report {
        width: 100%;
        color: #1e293b;
    }

    .technician-schedule-report .schedule-summary {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 5mm;
        margin-bottom: 4mm;
    }

    .technician-schedule-report .schedule-heading {
        font-size: 11pt;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .technician-schedule-report .schedule-description {
        margin-top: 1.5mm;
        font-size: 7.5pt;
        color: #64748b;
        line-height: 1.5;
    }

    .technician-schedule-report .schedule-count {
        border: 1px solid #cbd5e1;
        border-radius: 3px;
        padding: 2mm 3mm;
        font-size: 8pt;
        color: #334155;
        white-space: nowrap;
    }

    .technician-schedule-report .schedule-filters {
        margin-bottom: 4mm;
        padding: 2.5mm 3mm;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-size: 7.5pt;
        line-height: 1.6;
    }

    .technician-schedule-report .schedule-filters strong {
        color: #334155;
    }

    .technician-schedule-report .report-table-wrapper {
        width: 100%;
        overflow: visible;
    }

    .technician-schedule-report .report-table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
        font-size: 7.5pt;
    }

    .technician-schedule-report .report-table th:nth-child(1) {
        width: 12%;
    }

    .technician-schedule-report .report-table th:nth-child(2) {
        width: 13%;
    }

    .technician-schedule-report .report-table th:nth-child(3) {
        width: 15%;
    }

    .technician-schedule-report .report-table th:nth-child(4) {
        width: 16%;
    }

    .technician-schedule-report .report-table th:nth-child(5) {
        width: 23%;
    }

    .technician-schedule-report .report-table th:nth-child(6) {
        width: 11%;
    }

    .technician-schedule-report .report-table th:nth-child(7) {
        width: 10%;
    }

    .technician-schedule-report .report-table th,
    .technician-schedule-report .report-table td {
        padding: 2mm 1.5mm;
        text-align: left;
        vertical-align: top;
        overflow-wrap: anywhere;
        word-break: normal;
        border: 1px solid #cbd5e1;
    }

    .technician-schedule-report .report-table thead th {
        background: #eaf3fa;
        color: #1e3a5f;
        font-size: 7pt;
        font-weight: 700;
    }

    .technician-schedule-report .report-table tbody tr:nth-child(even) {
        background: #f8fafc;
    }

    .technician-schedule-report .cell-primary {
        font-weight: 700;
        color: #0f172a;
    }

    .technician-schedule-report .cell-secondary {
        display: block;
        margin-top: 1mm;
        font-size: 6.5pt;
        color: #64748b;
    }

    .technician-schedule-report .status-text {
        font-weight: 700;
        color: #334155;
    }

    .technician-schedule-report .schedule-empty {
        padding: 10mm 4mm;
        border: 1px solid #e2e8f0;
        text-align: center;
        font-size: 9pt;
        color: #64748b;
    }

    @media screen and (max-width: 767px) {
        .technician-schedule-report .schedule-summary {
            flex-direction: column;
            gap: 2mm;
        }

        .technician-schedule-report .report-table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .technician-schedule-report .report-table {
            min-width: 850px;
        }

        .technician-schedule-report .schedule-count {
            white-space: normal;
        }
    }

    @media print {
        .technician-schedule-report {
            width: 100%;
        }

        .technician-schedule-report .report-table {
            min-width: 0;
            font-size: 7pt;
        }

        .technician-schedule-report .report-table th {
            font-size: 6.5pt;
        }

        .technician-schedule-report .report-table th,
        .technician-schedule-report .report-table td {
            padding: 1.5mm 1.3mm;
        }

        .technician-schedule-report thead {
            display: table-header-group;
        }

        .technician-schedule-report tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .technician-schedule-report .schedule-summary,
        .technician-schedule-report .schedule-filters {
            break-inside: avoid;
        }

        .technician-schedule-report .report-table-wrapper {
            overflow: visible !important;
        }

        .technician-schedule-report .report-table thead th {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
@endpush

@section('report-content')
@php
    $isHistory = $view === 'history';

    $dateFrom = !empty($validated['date_from'])
        ? \Carbon\Carbon::parse($validated['date_from'])->format('M d, Y')
        : 'All applicable dates';

    $dateTo = !empty($validated['date_to'])
        ? \Carbon\Carbon::parse($validated['date_to'])->format('M d, Y')
        : 'No end date';
@endphp

<div class="technician-schedule-report">
    <section class="report-section">

        {{-- REPORT HEADING --}}
        <div class="schedule-summary">
            <div>
                <h3 class="schedule-heading">
                    {{ $isHistory
                        ? 'Completed Maintenance Schedule History'
                        : 'Upcoming Maintenance Schedule' }}
                </h3>

                <p class="schedule-description">
                    Finalized maintenance visits assigned to the
                    Maintenance Technician.
                </p>
            </div>

            <div class="schedule-count">
                Total Records:
                <strong>{{ number_format($workOrders->count()) }}</strong>
            </div>
        </div>

        {{-- FILTER INFORMATION --}}
        <div class="schedule-filters">
            <strong>Schedule Period:</strong>
            {{ $dateFrom }} to {{ $dateTo }}

            <span style="margin: 0 2mm; color: #94a3b8;">|</span>

            <strong>Report Type:</strong>
            {{ $isHistory ? 'Completed History' : 'Upcoming Visits' }}

            @if (!empty($validated['search']))
                <span style="margin: 0 2mm; color: #94a3b8;">|</span>
                <strong>Search:</strong>
                {{ $validated['search'] }}
            @endif
        </div>

        {{-- REPORT TABLE --}}
        @if ($workOrders->isNotEmpty())
            <div class="report-table-wrapper">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Schedule Date</th>
                            <th>Complaint / Visit</th>
                            <th>Consumer</th>
                            <th>Complaint Type</th>
                            <th>Service Location</th>
                            <th>Status</th>
                            <th>Planned Start</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($workOrders as $workOrder)
                            @php
                                $complaint = $workOrder->complaint;

                                $scheduleDate = $workOrder->schedule?->schedule_date;

                                $plannedStart = $workOrder->planned_start_time;

                                $consumerName = $complaint?->consumer?->full_name
                                    ?? $complaint?->complainant_name
                                    ?? 'Unknown Consumer';
                            @endphp

                            <tr>
                                <td>
                                    <span class="cell-primary">
                                        {{ $scheduleDate
                                            ? \Carbon\Carbon::parse($scheduleDate)->format('M d, Y')
                                            : '—' }}
                                    </span>

                                    @if ($scheduleDate)
                                        <span class="cell-secondary">
                                            {{ \Carbon\Carbon::parse($scheduleDate)->format('l') }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="cell-primary">
                                        {{ $complaint?->complaint_no ?? 'N/A' }}
                                    </span>

                                    <span class="cell-secondary">
                                        Visit {{ $workOrder->visit_number }}
                                    </span>

                                    @if ($workOrder->dispatch_type === 'Emergency')
                                        <span class="cell-secondary">
                                            Emergency Dispatch
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <span class="cell-primary">
                                        {{ $consumerName }}
                                    </span>

                                    @if ($complaint?->consumer?->account_number)
                                        <span class="cell-secondary">
                                            Account:
                                            {{ $complaint->consumer->account_number }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $complaint?->category?->name ?? 'Uncategorized' }}
                                </td>

                                <td>
                                    {{ $complaint?->address ?: 'Address unavailable' }}
                                </td>

                                <td>
                                    <span class="status-text">
                                        {{ $workOrder->status }}
                                    </span>
                                </td>

                                <td>
                                    <span class="cell-primary">
                                        {{ $plannedStart
                                            ? \Carbon\Carbon::parse($plannedStart)->format('h:i A')
                                            : '—' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="schedule-empty">
                No maintenance schedules matched the selected filters.
            </div>
        @endif

    </section>
</div>
@endsection
