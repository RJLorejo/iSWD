
@extends('layouts.report')

@section('title', 'Pending Workload Report')

@section('report-title', 'Pending Workload Report')

@section('report-subtitle', 'Unfinished Maintenance Reports and Follow-up Status')

@section('report-period')
    Status: {{ $status === 'all' ? 'All Reports' : ($status === 'awaiting' ? 'Awaiting Follow-up' : 'Follow-up Planned') }}
    | Search: {{ $search ?: 'All' }}
    | Page {{ $workOrders->currentPage() }} of {{ $workOrders->lastPage() }}
@endsection

@section('prepared-by-role', 'Customer Service')

@section('back-url', route('customer-service.pending-workloads.index'))

@section('report-content')

    <section class="report-section">


        <div class="report-table-wrapper">
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 13%">Complaint / Visit</th>
                        <th style="width: 17%">Category / Address</th>
                        <th style="width: 24%">Pending Reason</th>
                        <th style="width: 17%">Maintenance Team</th>
                        <th style="width: 12%">Reported On</th>
                        <th style="width: 17%">Follow-up Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($workOrders as $order)
                        @php
                            $followUp = $order->getRelation('displayFollowUp');

                            $followUpStatus = $followUp
                                ? ($followUp->schedule?->status === 'Finalized'
                                    ? 'Follow-up Finalized'
                                    : 'Draft Follow-up')
                                : 'Awaiting Follow-up';
                        @endphp

                        <tr>
                            <td>
                                <strong>{{ $order->complaint?->complaint_no ?? 'Unknown' }}</strong>
                                <br>
                                Visit #{{ $order->visit_number }}
                                <br>
                                WO #{{ $order->id }}
                            </td>
                            <td>
                                {{ $order->complaint?->category?->name ?? 'Maintenance Concern' }}
                                <br><br>
                                {{ $order->complaint?->address ?? 'Not recorded' }}
                            </td>
                            <td>{{ $order->pending_reason ?: 'Not recorded' }}</td>
                            <td>
                                {{ $order->technicians->pluck('full_name')->filter()->join(', ') ?: 'Not recorded' }}
                            </td>
                            <td>
                                {{ $order->pending_recorded_at
                                    ? $order->pending_recorded_at->timezone('Asia/Manila')->format('M d, Y g:i A')
                                    : 'Not recorded' }}
                            </td>
                            <td>
                                {{ $followUpStatus }}

                                @if ($followUp)
                                    <br><br>
                                    Visit #{{ $followUp->visit_number }}
                                    <br>
                                    Date:
                                    {{ $followUp->schedule?->schedule_date
                                        ? \Carbon\Carbon::parse($followUp->schedule->schedule_date)->format('M d, Y')
                                        : 'Unavailable' }}
                                    <br>
                                    Work order: {{ $followUp->status }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No pending workload records match the selected filters.
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
                <div class="report-field-label">All Pending Workloads</div>
                <div class="report-field-value">{{ $counts['all'] }}</div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Awaiting Follow-up</div>
                <div class="report-field-value">{{ $counts['awaiting'] }}</div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Follow-up Planned</div>
                <div class="report-field-value">{{ $counts['planned'] }}</div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Records in This Report</div>
                <div class="report-field-value">{{ $workOrders->count() }}</div>
            </div>
        </div>
    </section>

@endsection
