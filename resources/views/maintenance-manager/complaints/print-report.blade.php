@extends('layouts.report')

@section('title', 'Complaint Management Report')
@section('report-title', 'Complaint Management Report')
@section('report-subtitle', 'Maintenance Manager · Engineering and Commercial Complaint Register')
@section('report-period')
    Reported: {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') : 'Beginning' }} —
    {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') : 'Present' }}
@endsection
@section('prepared-by-name', auth()->user()?->full_name ?? (auth()->user()?->email ?? 'Maintenance Manager'))
@section('prepared-by-role', 'Maintenance Manager')

@push('styles')
    <style>
        @page {
            size: A4 landscape;
            margin: 9mm;
        }

        .manager-report {
            width: 100%;
            color: #1e293b;
        }

        .manager-report .criteria {
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-size: 8pt;
        }

        .manager-report .criteria-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 7px 12px;
        }

        .manager-report .criteria-label {
            color: #64748b;
            font-weight: 700;
        }

        .manager-report .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 7px;
            margin-bottom: 14px;
        }

        .manager-report .summary-item {
            border: 1px solid #cbd5e1;
            padding: 9px;
            border-radius: 5px;
        }

        .manager-report .summary-item strong {
            display: block;
            font-size: 16pt;
            color: #0f172a;
        }

        .manager-report .summary-item span {
            font-size: 7pt;
            color: #475569;
        }

        .manager-report .report-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 7.3pt;
        }

        .manager-report .report-table th,
        .manager-report .report-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 5px;
            vertical-align: top;
            text-align: left;
            overflow-wrap: anywhere;
        }

        .manager-report .report-table th {
            background: #f1f5f9;
            color: #334155;
            font-size: 7pt;
        }

        .manager-report .report-table thead {
            display: table-header-group;
        }

        .manager-report .report-table tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .manager-report .report-table th:nth-child(1) {
            width: 14%;
        }

        .manager-report .report-table th:nth-child(2) {
            width: 16%;
        }

        .manager-report .report-table th:nth-child(3) {
            width: 12%;
        }

        .manager-report .report-table th:nth-child(4) {
            width: 15%;
        }

        .manager-report .report-table th:nth-child(5) {
            width: 11%;
        }

        .manager-report .report-table th:nth-child(6) {
            width: 16%;
        }

        .manager-report .report-table th:nth-child(7) {
            width: 16%;
        }

        .manager-report .muted {
            display: block;
            color: #64748b;
            font-size: 6.6pt;
            margin-top: 3px;
        }

        .manager-report .urgent-high {
            color: #b91c1c;
            font-weight: 700;
        }

        .manager-report .urgent-moderate {
            color: #92400e;
            font-weight: 700;
        }

        .manager-report .urgent-low {
            color: #166534;
            font-weight: 700;
        }

        .manager-report .footnote {
            margin-top: 12px;
            font-size: 7pt;
            color: #64748b;
        }

        @media screen and (max-width:720px) {
            .manager-report .criteria-grid {
                grid-template-columns: 1fr 1fr;
            }

            .manager-report .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .manager-report .report-table {
                min-width: 850px;
            }

            .manager-report .table-scroll {
                overflow-x: auto;
            }
        }

        @media print {
            .manager-report .summary-grid {
                grid-template-columns: repeat(5, minmax(0, 1fr));
            }

            .manager-report .table-scroll {
                overflow: visible;
            }

            .manager-report .report-table {
                min-width: 0;
            }
        }
    </style>
@endpush

@section('report-content')
    @php
        $statuses = $complaints->countBy('status');
        $filterStatus = request('status') ?: 'All Statuses';
        $filterUrgency =
            request('urgency') === 'not_assessed' ? 'Not Assessed' : (request('urgency') ?: 'All Urgency Levels');
    @endphp
    <div class="manager-report">
        <section class="report-section">
            <div class="criteria">
                <div style="font-weight:700;margin-bottom:8px;">Applied Report Filters</div>
                <div class="criteria-grid">
                    <div><span class="criteria-label">Status:</span> {{ $filterStatus }}</div>
                    <div><span class="criteria-label">Urgency:</span> {{ $filterUrgency }}</div>
                    <div><span class="criteria-label">From:</span> {{ request('date_from') ?: 'Beginning' }}</div>
                    <div><span class="criteria-label">To:</span> {{ request('date_to') ?: 'Present' }}</div>
                    <div><span class="criteria-label">Search:</span> {{ request('search') ?: 'None' }}</div>
                </div>
            </div>
            <div class="summary-grid">
                @foreach ([['Matching Records', $complaints->count()], ['For Planning', $complaints->filter(fn($c) => in_array($c->status, ['Verified', 'For Maintenance'], true) && $c->technicians->isEmpty())->count()], ['Assigned', $statuses->get('Assigned', 0)], ['In Progress', $statuses->get('In Progress', 0)], ['Accomplished / Closed', $statuses->get('Completed', 0) + $statuses->get('Closed', 0)]] as [$label, $number])
                    <div class="summary-item"><strong>{{ number_format($number) }}</strong><span>{{ $label }}</span>
                    </div>
                @endforeach
            </div>
            <h3 class="report-section-title" style="margin-bottom:8px;">Complaint Records</h3>
            <div class="table-scroll">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Complaint / Urgency</th>
                            <th>Consumer / Account</th>
                            <th>Division</th>
                            <th>Complaint Type</th>
                            <th>Status</th>
                            <th>Responsible Team</th>
                            <th>Reported / Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($complaints as $complaint)
                            @php
                                $urgency =
                                    $complaint->aiAnalysis?->verified_urgency_level ?:
                                    $complaint->aiAnalysis?->urgency_level ?:
                                    null;
                                $isCommercial = str_contains(
                                    strtolower((string) $complaint->division?->name),
                                    'commercial',
                                );
                                $isMaintenance =
                                    in_array(
                                        $complaint->status,
                                        ['For Maintenance', 'Assigned', 'In Progress', 'Completed'],
                                        true,
                                    ) ||
                                    ($complaint->status === 'Closed' && $complaint->technicians->isNotEmpty());
                            @endphp
                            <tr>
                                <td><strong>{{ $complaint->complaint_no }}</strong><span
                                        class="muted {{ $urgency === 'High' ? 'urgent-high' : ($urgency === 'Moderate' ? 'urgent-moderate' : ($urgency === 'Low' ? 'urgent-low' : '')) }}">
                                        {{ $urgency ?? 'Not Assessed' }}
                                    </span></td>
                                <td>{{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? '—') }}
                                    @if ($complaint->consumer?->account_number)
                                        <span class="muted">Account: {{ $complaint->consumer->account_number }}</span>
                                    @endif
                                </td>
                                <td>{{ $complaint->division?->name ?? '—' }}</td>
                                <td>{{ $complaint->category?->name ?? 'Uncategorized' }}</td>
                                <td>{{ $complaint->status === 'Completed' ? 'Accomplished' : $complaint->status }}</td>
                                <td>
                                    @if ($complaint->technicians->isNotEmpty())
                                        {{ $complaint->technicians->pluck('full_name')->filter()->implode(', ') }}
                                    @elseif($isCommercial && !$isMaintenance)
                                        Commercial Services
                                    @else
                                        Not Assigned
                                    @endif
                                </td>
                                <td>{{ $complaint->created_at?->format('M d, Y h:i A') ?? '—' }}<span
                                        class="muted">Completed:
                                        {{ $complaint->completed_at?->format('M d, Y h:i A') ?? '—' }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;padding:18px;">No complaints matched the
                                    selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <p class="footnote">Generated {{ now()->format('M d, Y h:i A') }} · {{ $complaints->count() }} matching
                records. AI urgency is decision support and remains subject to staff verification. Summary counts describe
                the filtered records, not district-wide totals.</p>
        </section>
    </div>
@endsection
