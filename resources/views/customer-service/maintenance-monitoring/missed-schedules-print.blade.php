
@extends('layouts.report')

@section('title', 'Missed Maintenance Schedules Report')

@section('report-title', 'Missed Maintenance Schedules')

@section('report-subtitle', 'Finalized Maintenance Visits That Remain Unstarted')

@section('report-period')
    From: {{ $dateFrom ?: 'All dates' }}
    | To: {{ $dateTo ?: 'All dates' }}
    | Search: {{ $search ?: 'All complaints' }}
    | Page {{ $workOrders->currentPage() }} of {{ $workOrders->lastPage() }}
@endsection

@section('prepared-by-role', 'Customer Service')

@section('back-url', route('customer-service.missed-schedules.index'))

@section('report-content')

    <section class="report-section">
        <div class="report-section-heading-row">
            <h3 class="report-section-title">Missed Schedule Records</h3>
            <div class="report-record-count">
                Matching records: <strong>{{ $workOrders->total() }}</strong>
            </div>
        </div>

        <div class="report-table-wrapper">
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 16%">Complaint / Work Order</th>
                        <th style="width: 12%">Scheduled Date</th>
                        <th style="width: 16%">Complaint Type</th>
                        <th style="width: 20%">Maintenance Team</th>
                        <th style="width: 24%">Technician Explanation</th>
                        <th style="width: 12%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($workOrders as $order)
                        @php
                            $complaint = $order->complaint;
                            $scheduledDate = $order->schedule?->schedule_date;

                            $daysOverdue = $scheduledDate
                                ? \Carbon\Carbon::parse($scheduledDate)->startOfDay()
                                    ->diffInDays(\Carbon\Carbon::parse($today)->startOfDay())
                                : 0;
                        @endphp

                        <tr>
                            <td>
                                <strong>{{ $complaint?->complaint_no ?? 'Unknown' }}</strong>
                                <br>
                                WO #{{ $order->id }}
                                <br>
                                Visit #{{ $order->visit_number }}
                            </td>
                            <td>
                                {{ $scheduledDate
                                    ? \Carbon\Carbon::parse($scheduledDate)->format('M d, Y')
                                    : 'Not recorded' }}
                                <br><br>
                                {{ (int) $daysOverdue }}
                                {{ (int) $daysOverdue === 1 ? 'day' : 'days' }} overdue
                            </td>
                            <td>
                                {{ $complaint?->category?->name ?? 'Maintenance Concern' }}
                                <br><br>
                                {{ \Illuminate\Support\Str::limit($complaint?->description ?? 'No description', 120) }}
                                <br><br>
                                Address: {{ $complaint?->address ?? 'Not recorded' }}
                            </td>
                            <td>
                                {{ $order->technicians->pluck('full_name')->filter()->join(', ') ?: 'Not recorded' }}
                            </td>
                            <td>
                                @if ($order->missed_reported_at)
                                    {{ $order->missed_reason ?: 'No explanation recorded' }}
                                    <br><br>
                                    Reported:
                                    {{ $order->missed_reported_at->timezone('Asia/Manila')->format('M d, Y g:i A') }}
                                @else
                                    No technician explanation submitted.
                                @endif
                            </td>
                            <td>
                                {{ $order->status }}
                                <br>
                                {{ $order->schedule?->status ?? 'Unknown schedule status' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No missed schedules match the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="report-section">
        <h3 class="report-section-title">Report Summary</h3>

        <div class="report-grid">
            <div class="report-field">
                <div class="report-field-label">Total Overdue Work Orders</div>
                <div class="report-field-value">{{ $totalMissed }}</div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Records in This Report</div>
                <div class="report-field-value">{{ $workOrders->count() }}</div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Report Date</div>
                <div class="report-field-value">
                    {{ \Carbon\Carbon::parse($today)->format('F d, Y') }}
                </div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Review Status</div>
                <div class="report-field-value">For Maintenance Manager Review</div>
            </div>
        </div>
    </section>

@endsection
