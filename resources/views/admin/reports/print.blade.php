 @extends('layouts.report')

@section('title', 'iSWD Administrative System Report')

@section('report-title', 'Administrative System Report')

@section('report-subtitle', 'Consumer Services, Complaint Management, AI Analysis and Maintenance Operations')

@section('report-period')
    Reporting Period:
    {{ $filters['date_from'] ?: 'Beginning' }}
    to
    {{ $filters['date_to'] ?: 'Present' }}
@endsection

@section('prepared-by-role', 'System Administrator')

@section('back-url', route('admin.reports.index', $filters))

@section('report-content')

    {{-- 1. Consumer Overview --}}
    <section class="report-section">
        <h3 class="report-section-title">1. Consumer Overview</h3>

        <div class="report-grid">
            @foreach ([
                ['Total Consumers', $consumerStats['total']],
                ['Active Accounts', $consumerStats['active']],
                ['Inactive Accounts', $consumerStats['inactive']],
                ['Verified', $consumerStats['verified']],
                ['Pending Verification', $consumerStats['pending']],
                ['Rejected', $consumerStats['rejected']],
            ] as [$label, $value])
                <div class="report-field">
                    <div class="report-field-label">{{ $label }}</div>
                    <div class="report-field-value">{{ number_format($value) }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 2. Complaint Workflow --}}
    <section class="report-section">
        <h3 class="report-section-title">2. Complaint Workflow Summary</h3>

        <div class="report-grid">
            @foreach ([
                ['Total Complaints', 'total'],
                ['Pending', 'pending'],
                ['Verified', 'verified'],
                ['Assigned', 'assigned'],
                ['In Progress', 'in_progress'],
                ['Completed', 'completed'],
                ['Closed', 'closed'],
                ['Rejected', 'rejected'],
            ] as [$label, $key])
                <div class="report-field">
                    <div class="report-field-label">{{ $label }}</div>
                    <div class="report-field-value">
                        {{ number_format($complaintStats[$key]) }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="report-section">
            <h4 class="report-section-title">Complaints by Category</h4>

            <div class="report-table-wrapper">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 70%;">Complaint Category</th>
                            <th class="text-right" style="width: 30%;">Total Complaints</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($complaintsByCategory as $category)
                            <tr>
                                <td>{{ $category->category_name }}</td>
                                <td class="text-right">{{ number_format($category->total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">No category data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- 3. AI Classification --}}
    <section class="report-section">
        <h3 class="report-section-title">3. AI Classification and Urgency Analysis</h3>

        <div class="report-grid">
            @foreach ([
                ['Total AI Analyses', $aiStats['total']],
                ['High Urgency', $aiStats['high']],
                ['Medium Urgency', $aiStats['medium']],
                ['Low Urgency', $aiStats['low']],
                ['Ambiguous Cases', $aiStats['ambiguous']],
                ['Average Confidence', number_format($aiStats['average_confidence'] * 100, 2) . '%'],
                ['Average Urgency Score', number_format($aiStats['average_urgency_score'], 2)],
            ] as [$label, $value])
                <div class="report-field">
                    <div class="report-field-label">{{ $label }}</div>
                    <div class="report-field-value">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        <div class="report-section">
            <h4 class="report-section-title">Recorded Urgency Categories</h4>

            <div class="report-table-wrapper">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 70%;">Urgency Level</th>
                            <th class="text-right" style="width: 30%;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($aiUrgencyBreakdown as $item)
                            <tr>
                                <td>{{ $item->urgency }}</td>
                                <td class="text-right">{{ number_format($item->total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">No AI urgency data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <p class="report-disclaimer">
            AI confidence assumes values are stored between 0 and 1.
            Urgency categories reflect recorded labels and are not independently recalculated.
        </p>
    </section>

    {{-- 4. Consumer Feedback --}}
    <section class="report-section">
        <h3 class="report-section-title">4. Consumer Feedback and Satisfaction</h3>

        <div class="report-grid">
            @foreach ([
                ['Feedback Responses', $feedbackStats['total']],
                ['Overall Rating', number_format($feedbackStats['average_overall'], 2) . ' / 5'],
                ['Service Quality', number_format($feedbackStats['average_service_quality'], 2) . ' / 5'],
                ['Response Time', number_format($feedbackStats['average_response_time'], 2) . ' / 5'],
                ['Personnel Courtesy', number_format($feedbackStats['average_personnel_courtesy'], 2) . ' / 5'],
                ['Resolved', $feedbackStats['resolved']],
                ['Partially Resolved', $feedbackStats['partially_resolved']],
                ['Not Resolved', $feedbackStats['not_resolved']],
            ] as [$label, $value])
                <div class="report-field">
                    <div class="report-field-label">{{ $label }}</div>
                    <div class="report-field-value">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        <div class="report-section">
            <h4 class="report-section-title">Recent Feedback Records</h4>

            <div class="report-table-wrapper">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Complaint No.</th>
                            <th style="width: 20%;">Consumer</th>
                            <th style="width: 12%;">Rating</th>
                            <th style="width: 18%;">Resolution</th>
                            <th style="width: 35%;">Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestFeedback as $feedback)
                            <tr>
                                <td>{{ $feedback->complaint?->complaint_no ?? 'N/A' }}</td>
                                <td>{{ $feedback->consumer?->full_name ?? 'Unavailable' }}</td>
                                <td>{{ $feedback->overall_rating }} / 5</td>
                                <td>{{ $feedback->resolution_status ?: 'Not specified' }}</td>
                                <td>{{ $feedback->comments ?: 'No comments' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No feedback records available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- 5. Maintenance Work Orders --}}
    <section class="report-section">
        <h3 class="report-section-title">5. Maintenance Work-Order Performance</h3>

        <div class="report-grid">
            @foreach ([
                ['Total Work Orders', 'total'],
                ['Completed', 'completed'],
                ['In Progress', 'in_progress'],
                ['Scheduled', 'scheduled'],
                ['Pending Workload', 'pending_workload'],
                ['Rescheduled', 'rescheduled'],
                ['Cancelled', 'cancelled'],
                ['Emergency Dispatches', 'emergency'],
                ['Regular Dispatches', 'regular'],
                ['Late Additions', 'late_additions'],
                ['Approved', 'approved'],
            ] as [$label, $key])
                <div class="report-field">
                    <div class="report-field-label">{{ $label }}</div>
                    <div class="report-field-value">
                        {{ number_format($workOrderStats[$key]) }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="report-section">
            <div class="report-section-heading-row">
                <h4 class="report-section-title">Work-Order Register</h4>
                <div class="report-record-count">
                    Records: <strong>{{ number_format($workOrders->count()) }}</strong>
                </div>
            </div>

            <div class="report-table-wrapper">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 17%;">Complaint No.</th>
                            <th style="width: 12%;">Dispatch</th>
                            <th style="width: 13%;">Status</th>
                            <th style="width: 13%;">Approval</th>
                            <th style="width: 27%;">Technicians</th>
                            <th style="width: 18%;">Completed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($workOrders as $workOrder)
                            <tr>
                                <td>{{ $workOrder->complaint?->complaint_no ?? 'N/A' }}</td>
                                <td>{{ $workOrder->dispatch_type ?: 'N/A' }}</td>
                                <td>{{ $workOrder->status ?: 'N/A' }}</td>
                                <td>{{ $workOrder->approval_status ?: 'N/A' }}</td>
                                <td>
                                    {{ $workOrder->technicians->pluck('name')->filter()->join(', ') ?: 'Not assigned' }}
                                </td>
                                <td>
                                    {{ $workOrder->completed_at?->format('M d, Y h:i A') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No work orders recorded for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

@endsection