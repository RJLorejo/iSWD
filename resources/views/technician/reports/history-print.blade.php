@extends('layouts.report')

@section('title', 'Maintenance Report History')
@section('report-title', 'Maintenance Report History')
@section('report-subtitle', 'Technician Service Accomplishment Report Register')
@section('report-period')
    @if (!empty($validated['from']) || !empty($validated['to']))
        Submission Period: {{ $validated['from'] ?? 'Beginning' }} — {{ $validated['to'] ?? 'Present' }}
    @else
        Submission Period: All Dates
    @endif
    @if (!empty($validated['status']) && $validated['status'] !== 'all')
        | Review Status: {{ $validated['status'] }}
    @endif
    @if (!empty($validated['search']))
        | Search: {{ $validated['search'] }}
    @endif
@endsection
@section('prepared-by-name')
    {{ auth()->user()->full_name ?? auth()->user()->name ?? 'N/A' }}
@endsection
@section('prepared-by-role', 'Maintenance Technician')

@push('styles')
<style>
    @page { size: A4 landscape; margin: 9mm; }
    .history-summary { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 3mm; margin-bottom: 5mm; }
    .history-stat { border: 1px solid #d1d5db; padding: 3mm; break-inside: avoid; }
    .history-stat-label { font-size: 7pt; font-weight: 700; color: #64748b; text-transform: uppercase; }
    .history-stat-value { font-size: 14pt; font-weight: 700; margin-top: 1mm; }
    .history-print .report-table { width: 100%; table-layout: fixed; font-size: 7pt; }
    .history-print .report-table th, .history-print .report-table td { padding: 1.5mm 1.3mm; vertical-align: top; overflow-wrap: anywhere; }
    .history-print .report-table th { font-size: 6.5pt; }
    .history-print .report-table th:nth-child(1) { width: 12%; }
    .history-print .report-table th:nth-child(2) { width: 15%; }
    .history-print .report-table th:nth-child(3) { width: 14%; }
    .history-print .report-table th:nth-child(4) { width: 14%; }
    .history-print .report-table th:nth-child(5) { width: 13%; }
    .history-print .report-table th:nth-child(6) { width: 9%; }
    .history-print .report-table th:nth-child(7) { width: 23%; }
    .history-muted { font-size: 6.5pt; color: #64748b; margin-top: 1mm; }
    @media print { .history-stat { break-inside: avoid; } .history-print tr { break-inside: avoid; } }
</style>
@endpush

@section('report-content')
<div class="history-print">
    <section class="report-section">
        <div class="history-summary">
            @foreach ([
                'Total Submitted' => $counts['total'],
                'Pending Review' => $counts['pending'],
                'Approved' => $counts['approved'],
                'Returned' => $counts['returned'],
            ] as $label => $count)
                <div class="history-stat">
                    <div class="history-stat-label">{{ $label }}</div>
                    <div class="history-stat-value">{{ number_format($count) }}</div>
                </div>
            @endforeach
        </div>
        <div class="report-section-heading-row">
            <h3 class="report-section-title">Submitted Accomplishment Reports</h3>
            <div class="report-record-count">Total Records: <strong>{{ $reports->count() }}</strong></div>
        </div>
        @if ($reports->isNotEmpty())
            <div class="report-table-wrapper">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Complaint / Report</th>
                            <th>Complaint Type</th>
                            <th>Submitted</th>
                            <th>Last Resubmitted</th>
                            <th>Review Status</th>
                            <th>Revisions</th>
                            <th>Manager Remarks / Review Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>
                                    <strong>{{ $report->complaint?->complaint_no ?? 'Unavailable' }}</strong>
                                    <div class="history-muted">Report #{{ $report->id }}</div>
                                </td>
                                <td>{{ $report->complaint?->category?->name ?? 'Uncategorized' }}</td>
                                <td>{{ $report->submitted_at ? \Carbon\Carbon::parse($report->submitted_at)->format('M d, Y h:i A') : '—' }}</td>
                                <td>{{ $report->resubmitted_at ? \Carbon\Carbon::parse($report->resubmitted_at)->format('M d, Y h:i A') : '—' }}</td>
                                <td><strong>{{ $report->review_status }}</strong></td>
                                <td>{{ (int) ($report->revision_number ?? 0) }}</td>
                                <td>
                                    {{ filled($report->review_remarks) ? $report->review_remarks : '—' }}
                                    @if ($report->reviewed_at)
                                        <div class="history-muted">Reviewed: {{ \Carbon\Carbon::parse($report->reviewed_at)->format('M d, Y h:i A') }}</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="report-text-block text-center">No submitted reports match the selected filters.</div>
        @endif
    </section>
</div>
@endsection
