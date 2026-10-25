
@extends('layouts.report')

@section('title', 'Pending Final Closure Report - iSWD')

@section('report-title', 'Pending Final Closure Report')

@section('report-subtitle', 'Customer Service Assistant')

@section('report-period')
    @php
        $statusLabels = [
            'all' => 'All Pending',
            'ready' => 'Ready to Close',
            'pending' => 'Awaiting Review',
            'returned' => 'Returned',
        ];
    @endphp

    Review Status: {{ $statusLabels[$reviewStatus] ?? 'All Pending' }}
    @if (!empty($filters['date_from']) || !empty($filters['date_to']))
        | Accomplished:
        {{ !empty($filters['date_from']) ? \Illuminate\Support\Carbon::parse($filters['date_from'])->format('M d, Y') : 'Any date' }}
        to
        {{ !empty($filters['date_to']) ? \Illuminate\Support\Carbon::parse($filters['date_to'])->format('M d, Y') : 'Any date' }}
    @else
        | Accomplishment date: All dates
    @endif
@endsection

@section('report-content')
<style>
    .closure-print-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-bottom: 16px;
    }

    .closure-print-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 15px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        color: #334155;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .closure-filter-summary {
        margin-bottom: 14px;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        font-size: 11px;
        overflow-wrap: anywhere;
    }

    .closure-report-count {
        margin-bottom: 10px;
        font-size: 11px;
        font-weight: 700;
        color: #334155;
    }

    .closure-report-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 9pt;
    }

    .closure-report-table th {
        padding: 8px;
        border: 1px solid #cbd5e1;
        background: #f1f5f9;
        color: #334155;
        text-align: left;
        font-size: 9px;
        text-transform: uppercase;
    }

    .closure-report-table td {
        padding: 8px;
        border: 1px solid #cbd5e1;
        vertical-align: top;
        overflow-wrap: anywhere;
    }

    .closure-secondary {
        margin-top: 3px;
        color: #64748b;
        font-size: 9px;
    }

    .closure-status {
        font-weight: 700;
    }

    .closure-empty {
        padding: 24px !important;
        text-align: center;
        color: #64748b;
    }

    @media print {
        .closure-print-actions {
            display: none !important;
        }

        .closure-report-table thead {
            display: table-header-group;
        }

        .closure-report-table tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .closure-report-table th {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        @page {
            size: landscape;
            margin: 12mm;
        }
    }
</style>


<div class="closure-report-count">
    Total Records: {{ number_format($complaints->count()) }}
</div>

<table class="closure-report-table">
    <thead>
        <tr>
            <th style="width: 16%;">Complaint</th>
            <th style="width: 19%;">Complainant / Account</th>
            <th style="width: 16%;">Division / Type</th>
            <th style="width: 15%;">Accomplished</th>
            <th style="width: 17%;">Manager Review</th>
            <th style="width: 17%;">Report Submitted</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($complaints as $complaint)
            @php
                $report = $complaint->maintenanceReport;
                $reportStatus = $report?->review_status ?? 'Draft';

                $isReady =
                    $report?->submitted_at !== null &&
                    $reportStatus === 'Approved';

                $statusLabel = match (true) {
                    $isReady => 'Ready to Close',
                    $reportStatus === 'Returned' => 'Returned',
                    default => 'Awaiting Review',
                };
            @endphp

            <tr>
                <td>
                    <strong>{{ $complaint->complaint_no }}</strong>
                    <div class="closure-secondary">
                        {{ $complaint->description ?: 'No description' }}
                    </div>
                </td>

                <td>
                    <strong>
                        {{ $complaint->complainant_name ?: ($complaint->consumer?->full_name ?? 'N/A') }}
                    </strong>
                    <div class="closure-secondary">
                        {{ $complaint->consumer?->account_number ?? 'Walk-in / No linked account' }}
                    </div>
                </td>

                <td>
                    {{ $complaint->division?->name ?? 'N/A' }}
                    <div class="closure-secondary">
                        {{ $complaint->category?->name ?? 'N/A' }}
                    </div>
                </td>

                <td>
                    {{ $complaint->completed_at
                        ? \Illuminate\Support\Carbon::parse($complaint->completed_at)->format('M d, Y')
                        : 'Not recorded' }}

                    <div class="closure-secondary">
                        {{ $complaint->completed_at
                            ? \Illuminate\Support\Carbon::parse($complaint->completed_at)->format('h:i A')
                            : '—' }}
                    </div>
                </td>

                <td>
                    <span class="closure-status">{{ $statusLabel }}</span>
                    <div class="closure-secondary">
                        Report status: {{ $reportStatus }}
                    </div>
                </td>

                <td>
                    {{ $report?->submitted_at
                        ? \Illuminate\Support\Carbon::parse($report->submitted_at)->format('M d, Y h:i A')
                        : 'Not submitted' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="closure-empty">
                    No complaints found for the selected filters.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
