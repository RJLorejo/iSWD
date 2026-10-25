@extends('layouts.report')
@section('report-title', 'Consumer Feedback Report')
@section('report-subtitle', 'Sagay Water District • Consumer Experience')
@section('report-period')
    <div>Generated: {{ now()->format('F d, Y h:i A') }}</div>
    <div>Matching records: {{ number_format($feedback->count()) }}</div>
@endsection
@section('report-content')
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm
        }

        .feedback-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            margin: 12px 0 18px
        }

        .feedback-stat {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 9px 11px
        }

        .feedback-stat small {
            display: block;
            color: #64748b;
            font-size: 8pt;
            text-transform: uppercase
        }

        .feedback-stat strong {
            display: block;
            margin-top: 4px;
            font-size: 16pt
        }

        .feedback-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt
        }

        .feedback-table th,
        .feedback-table td {
            border: 1px solid #cbd5e1;
            padding: 6px;
            text-align: left;
            vertical-align: top;
            overflow-wrap: anywhere
        }

        .feedback-table th {
            background: #f1f5f9
        }

        .feedback-table tr {
            break-inside: avoid
        }

        .feedback-filters {
            margin: 8px 0 12px;
            color: #475569;
            font-size: 9pt
        }

        @media print {
            .feedback-table thead {
                display: table-header-group
            }
        }
    </style>
    <div class="feedback-filters"><strong>Applied filters:</strong>
        @if (request()->filled('search'))
            Keyword: {{ request('search') }} ·
        @endif
        @if (request()->filled('resolution_status'))
            Resolution: {{ request('resolution_status') }} ·
        @endif
        @if (request()->filled('handling_status'))
            Handling: {{ request('handling_status') }} ·
        @endif
        @if (request()->filled('rating'))
            Rating: {{ request('rating') }}/5 ·
        @endif
        @if (request()->filled('from'))
            From: {{ request('from') }} ·
        @endif
        @if (request()->filled('to'))
            To: {{ request('to') }} ·
        @endif
        @if (request()->boolean('needs_attention'))
            Needs follow-up
        @endif
    </div>
    <div class="feedback-summary">
        <div class="feedback-stat"><small>Records in report</small><strong>{{ number_format($feedback->count()) }}</strong>
        </div>
        <div class="feedback-stat"><small>Average rating</small><strong>{{ number_format((float) ($averageRating ?? 0), 1) }} /
                5</strong></div>
        <div class="feedback-stat"><small>New feedback</small><strong>{{ number_format($newCount ?? 0) }}</strong></div>
        <div class="feedback-stat"><small>Needs
                follow-up</small><strong>{{ number_format($needsAttentionCount ?? 0) }}</strong></div>
    </div>
    <table class="feedback-table">
        <thead>
            <tr>
                <th>Complaint No.</th>
                <th>Consumer / Account</th>
                <th>Service / Division</th>
                <th>Rating</th>
                <th>Consumer Resolution</th>
                <th>Handling Status</th>
                <th>Submitted</th>
            </tr>
        </thead>
        <tbody>
            @forelse($feedback as $item)
                <tr>
                    <td><strong>{{ $item->complaint?->complaint_no ?? '—' }}</strong><br>{{ $item->complaint?->status ?? '—' }}
                    </td>
                    <td>{{ $item->consumer?->full_name ?? '—' }}<br><small>{{ $item->consumer?->account_number ?? 'No account number' }}</small>
                    </td>
                    <td>{{ $item->complaint?->category?->name ?? '—' }}<br><small>{{ $item->complaint?->division?->name ?? 'No division' }}</small>
                    </td>
                    <td>{{ $item->overall_rating ?? 0 }} / 5</td>
                    <td>{{ $item->resolution_status ?? 'Not Resolved' }}</td>
                    <td>{{ $item->handling_status ?? 'New' }}</td>
                    <td>{{ $item->created_at?->format('M d, Y h:i A') ?? '—' }}</td>
            </tr>@empty<tr>
                    <td colspan="7" style="text-align:center;padding:18px">No feedback records match the selected filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
