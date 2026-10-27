@extends('layouts.report')

@section('title', 'Maintenance Technician Pending Workload Report')

@section('prepared-by-name')
    {{ auth()->user()->full_name ?? auth()->user()->name ?? 'N/A' }}
@endsection

@section('prepared-by-role', 'Maintenance Technician')

@push('styles')
<style>
    @page { size: A4 landscape; margin: 9mm; }
    .pending-workload-print .report-table { width: 100%; table-layout: fixed; }
    .pending-workload-print .report-table th:nth-child(1) { width: 12%; }
    .pending-workload-print .report-table th:nth-child(2) { width: 15%; }
    .pending-workload-print .report-table th:nth-child(3) { width: 13%; }
    .pending-workload-print .report-table th:nth-child(4) { width: 12%; }
    .pending-workload-print .report-table th:nth-child(5) { width: 22%; }
    .pending-workload-print .report-table th:nth-child(6) { width: 15%; }
    .pending-workload-print .report-table th:nth-child(7) { width: 11%; }
    .pending-workload-print .report-table td { overflow-wrap: anywhere; vertical-align: top; }
    @media print {
        .pending-workload-print .report-table { font-size: 7pt; }
        .pending-workload-print .report-table th { font-size: 6.5pt; }
        .pending-workload-print .report-table th,
        .pending-workload-print .report-table td { padding: 1.5mm 1.2mm; }
        .pending-workload-print tr { break-inside: avoid; page-break-inside: avoid; }
    }
</style>
@endpush

@section('report-content')
<div class="pending-workload-print">
    <section class="report-section">
        <div class="report-section-heading-row">
            <h3 class="report-section-title">Unresolved Pending Maintenance Visits</h3>
            <div class="report-record-count">Total Records: <strong>{{ $workOrders->count() }}</strong></div>
        </div>
        <div class="report-text-block" style="margin-bottom: 3mm; font-size: 8pt;">
            Follow-up Filter:
            <strong>{{ match ($filter) {
                'awaiting_followup' => 'Awaiting Reschedule',
                'followup_scheduled' => 'Follow-up Created',
                default => 'All Unresolved',
            } }}</strong>
            @if(request()->filled('search'))
                &nbsp; | &nbsp; Search: <strong>{{ request('search') }}</strong>
            @endif
        </div>
        @if($workOrders->isNotEmpty())
            <div class="report-table-wrapper">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Complaint / Visit</th>
                            <th>Consumer</th>
                            <th>Complaint Type / Location</th>
                            <th>Original Schedule / Pending Since</th>
                            <th>Reason for Pending</th>
                            <th>Follow-up Progress</th>
                            <th>Manager Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workOrders as $workOrder)
                            @php
                                $complaint = $workOrder->complaint;
                                $followup = $workOrder->latestFollowup;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $complaint?->complaint_no ?? 'N/A' }}</strong>
                                    <div>Visit {{ $workOrder->visit_number }}</div>
                                    @if($workOrder->dispatch_type === 'Emergency')
                                        <div><strong>Emergency</strong></div>
                                    @endif
                                </td>
                                <td>
                                    {{ $complaint?->consumer?->full_name ?? $complaint?->complainant_name ?? 'Unknown' }}
                                    @if($complaint?->consumer?->account_number)
                                        <div>{{ $complaint->consumer->account_number }}</div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $complaint?->category?->name ?? 'Uncategorized' }}</strong>
                                    <div>{{ $complaint?->address ?: 'Address unavailable' }}</div>
                                </td>
                                <td>
                                    {{ $workOrder->schedule?->schedule_date?->format('M d, Y') ?? 'N/A' }}
                                    <div style="margin-top: 1mm;">Pending: {{ $workOrder->pending_recorded_at?->format('M d, Y h:i A') ?? 'N/A' }}</div>
                                </td>
                                <td>{{ $workOrder->pending_reason ?: 'No reason recorded' }}</td>
                                <td>
                                    <strong>{{ $workOrder->followup_display_status }}</strong>
                                    @if($followup)
                                        <div>Visit {{ $followup->visit_number }} — {{ $followup->status }}</div>
                                        <div>{{ $followup->schedule?->schedule_date?->format('M d, Y') ?? 'Not finalized' }}</div>
                                    @endif
                                </td>
                                <td>{{ $workOrder->manager_remarks ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="report-text-block text-center">No unresolved pending visits matched the selected filters.</div>
        @endif
    </section>
</div>
@endsection
