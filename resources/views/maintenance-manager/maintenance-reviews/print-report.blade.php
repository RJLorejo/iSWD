@extends('layouts.report')

@section('title', 'Maintenance Accomplishment Reports')
@section('report-title', 'Maintenance Accomplishment Reports')
@section('report-subtitle', 'Maintenance Management · Submitted Accomplishment Register')
@section('report-period')
    Submitted: {{ !empty($filters['from']) ? \Carbon\Carbon::parse($filters['from'])->format('M d, Y') : 'Any date' }}
    — {{ !empty($filters['to']) ? \Carbon\Carbon::parse($filters['to'])->format('M d, Y') : 'Any date' }}
@endsection
@section('prepared-by-name', auth()->user()?->full_name ?? auth()->user()?->email ?? 'Maintenance Manager')
@section('prepared-by-role', 'Maintenance Manager')

@push('styles')
<style>
    @page { size: A4 landscape; margin: 11mm; }
    .review-print { color: #1e293b; font-size: 8.5pt; line-height: 1.45; }
    .review-print .summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin: 12px 0; }
    .review-print .summary > div { border: 1px solid #cbd5e1; padding: 9px 12px; border-radius: 5px; }
    .review-print .summary strong { display: block; font-size: 16pt; }
    .review-print .summary span { color: #64748b; font-size: 8pt; }
    .review-print table { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 7.6pt; }
    .review-print th, .review-print td { border: 1px solid #cbd5e1; padding: 7px; vertical-align: top; text-align: left; overflow-wrap: anywhere; }
    .review-print th { background: #f1f5f9; font-weight: 700; }
    .review-print tr { break-inside: avoid; page-break-inside: avoid; }
    .review-print thead { display: table-header-group; }
    .review-print .filters { padding: 8px 10px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 5px; margin: 12px 0; }
    .review-print .muted { color: #64748b; }
    .review-print .remarks { white-space: pre-line; }
    @media screen and (max-width: 640px) {
        .review-print .summary { grid-template-columns: repeat(2, 1fr); }
        .review-print .scroll { overflow-x: auto; }
        .review-print table { min-width: 950px; }
    }
    @media print {
        .review-print .summary { grid-template-columns: repeat(4, 1fr); }
        .review-print table { min-width: 0; }
        .review-print .scroll { overflow: visible; }
    }
</style>
@endpush

@section('report-content')
<div class="review-print">
    <div class="filters">
        <strong>Applied Filters:</strong>
        Review Status: {{ $status === 'All' ? 'All submitted reports' : $status }}
        &nbsp; | &nbsp; Search: {{ $filters['search'] ?? 'All complaints' }}
        &nbsp; | &nbsp; From: {{ $filters['from'] ?? 'Any' }}
        &nbsp; | &nbsp; To: {{ $filters['to'] ?? 'Any' }}
    </div>

    <div class="summary">
        <div><span>Matching Reports</span><strong>{{ $counts['total'] }}</strong></div>
        <div><span>Pending Review</span><strong>{{ $counts['pending'] }}</strong></div>
        <div><span>Returned</span><strong>{{ $counts['returned'] }}</strong></div>
        <div><span>Approved</span><strong>{{ $counts['approved'] }}</strong></div>
    </div>

    <div class="scroll">
        <table>
            <thead>
                <tr>
                    <th style="width:11%">Complaint / Type</th>
                    <th style="width:13%">Consumer / Account</th>
                    <th style="width:12%">Technician</th>
                    <th style="width:12%">Submitted</th>
                    <th style="width:10%">Review Status</th>
                    <th style="width:12%">Reviewed By / Date</th>
                    <th style="width:15%">Diagnosis / Root Cause</th>
                    <th style="width:15%">Review Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $report)
                    @php $complaint = $report->complaint; @endphp
                    <tr>
                        <td><strong>{{ $complaint?->complaint_no ?? '—' }}</strong><div class="muted">{{ $complaint?->category?->name ?? 'Uncategorized' }}</div></td>
                        <td>{{ $complaint?->consumer?->full_name ?? $complaint?->complainant_name ?? '—' }}<div class="muted">{{ $complaint?->consumer?->account_number ?? '—' }}</div></td>
                        <td>{{ $report->technician?->full_name ?? '—' }}</td>
                        <td>{{ $report->submitted_at?->format('M d, Y h:i A') ?? '—' }}</td>
                        <td><strong>{{ $report->review_status ?? '—' }}</strong></td>
                        <td>{{ $report->reviewer?->full_name ?? '—' }}<div class="muted">{{ $report->reviewed_at ? \Carbon\Carbon::parse($report->reviewed_at)->format('M d, Y h:i A') : '—' }}</div></td>
                        <td><strong>Diagnosis:</strong> {{ $report->diagnosis ?: '—' }}<div><strong>Root cause:</strong> {{ $report->root_cause ?: '—' }}</div></td>
                        <td class="remarks">{{ $report->review_remarks ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center; padding:20px">No submitted accomplishment reports match the selected filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="muted" style="margin-top:12px; font-size:7.5pt">
        Generated {{ now()->format('M d, Y h:i A') }}. Includes all matching submitted reports, regardless of on-screen pagination.
    </p>
</div>
@endsection
