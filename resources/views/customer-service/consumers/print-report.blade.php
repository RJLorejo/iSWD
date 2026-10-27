@extends('layouts.report')

@section('title', 'Consumer Report - iSWD')
@section('report-title', 'Consumer Report')
@section('report-subtitle', 'Customer Service Assistant')
@section('report-period')
    Consumer account listing
@endsection

@section('report-content')
<style>
    .consumer-print-actions{display:flex;justify-content:flex-end;gap:.5rem;margin-bottom:1rem}
    .consumer-print-button{display:inline-block;border:1px solid #cbd5e1;border-radius:6px;background:#2563eb;color:#fff;padding:9px 14px;cursor:pointer;font-size:12px;font-weight:600;text-decoration:none}
    .consumer-print-button.secondary{background:#e5e7eb;color:#374151}
    .consumer-filter-summary{margin-bottom:16px;padding:10px 12px;border:1px solid #e5e7eb;background:#f9fafb;font-size:11px}
    .consumer-filter-item{display:inline-block;margin:4px 20px 0 0}
    .consumer-record-count{margin-bottom:8px;font-weight:600;color:#374151}
    .consumer-report-table{width:100%;border-collapse:collapse;table-layout:fixed;font-size:9pt}
    .consumer-report-table th{padding:8px;border:1px solid #d1d5db;background:#f3f4f6;text-align:left;font-size:9px;text-transform:uppercase}
    .consumer-report-table td{padding:8px;border:1px solid #d1d5db;vertical-align:top;font-size:10px;overflow-wrap:anywhere}
    .consumer-name{font-weight:600}
    .consumer-secondary{margin-top:3px;color:#6b7280;font-size:9px;overflow-wrap:anywhere}
    .consumer-status{font-weight:600}
    .consumer-no-records{padding:30px!important;text-align:center;color:#6b7280}
    @media print{
        .consumer-print-actions{display:none!important}
        .consumer-report-table thead{display:table-header-group}
        .consumer-report-table tr{page-break-inside:avoid;break-inside:avoid}
        .consumer-filter-summary,.consumer-report-table thead th{print-color-adjust:exact;-webkit-print-color-adjust:exact}
        @page{size:landscape;margin:12mm}
    }
</style>



@if (request()->filled('search') || request()->filled('status'))
    <div class="consumer-filter-summary">
        <strong>Applied Filters:</strong>
        @if (request()->filled('search'))
            <span class="consumer-filter-item">Search: <strong>{{ request('search') }}</strong></span>
        @endif
        @if (request()->filled('status'))
            <span class="consumer-filter-item">
                Status: <strong>{{ request('status') === '1' ? 'Active' : 'Inactive' }}</strong>
            </span>
        @endif
    </div>
@endif

<div class="consumer-record-count">Total Records: {{ number_format($consumers->count()) }}</div>

<table class="consumer-report-table">
    <thead>
        <tr>
            <th style="width:22%">Consumer</th>
            <th style="width:13%">Account Number</th>
            <th style="width:12%">Contact</th>
            <th style="width:29%">Service Address</th>
            <th style="width:10%">Status</th>
            <th style="width:14%">Registered</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($consumers as $consumer)
            <tr>
                <td>
                    <div class="consumer-name">{{ $consumer->full_name }}</div>
                    @if ($consumer->email)
                        <div class="consumer-secondary">{{ $consumer->email }}</div>
                    @endif
                    <div class="consumer-secondary">
                        @if ($consumer->user && $consumer->user->is_active)
                            Portal: Active
                        @elseif ($consumer->user)
                            Portal: Inactive
                        @else
                            Portal: Missing
                        @endif
                    </div>
                </td>
                <td>{{ $consumer->account_number ?: '—' }}</td>
                <td>{{ $consumer->phone ?: '—' }}</td>
                <td>
                    @if ($consumer->address)
                        {{ collect([
                            $consumer->address->house_no,
                            $consumer->address->street,
                            $consumer->address->purok,
                            $consumer->address->barangay,
                            $consumer->address->municipality,
                            $consumer->address->province,
                        ])->filter()->implode(', ') ?: '—' }}
                    @else
                        —
                    @endif
                </td>
                <td><span class="consumer-status">{{ $consumer->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td>
                    {{ optional($consumer->created_at)->format('M d, Y') }}
                    <div class="consumer-secondary">{{ optional($consumer->created_at)->format('h:i A') }}</div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="consumer-no-records">No consumers found for the selected filters.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
