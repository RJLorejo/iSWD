@extends('layouts.report')

@section('report-title', 'Complaint Types Report')
@section('report-subtitle', 'Sagay Water District • Complaint Category Directory')
@section('back-url', route('customer-service.complaint-categories.index'))

@section('report-period')
    <div>Generated: {{ now()->format('F d, Y h:i A') }}</div>
    <div>Matching records: {{ number_format($categories->count()) }}</div>
@endsection

@section('report-content')
<style>
    @page { size: A4 landscape; margin: 10mm; }
    .ct-report-filters { margin: 0 0 12px; color: #4b5563; font-size: 8pt; }
    .ct-report-table { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 8.5pt; }
    .ct-report-table th, .ct-report-table td { border: 1px solid #d1d5db; padding: 7px 8px; text-align: left; vertical-align: top; overflow-wrap: anywhere; }
    .ct-report-table th { background: #f3f4f6; color: #374151; font-size: 7.5pt; text-transform: uppercase; }
    .ct-report-table tr { break-inside: avoid; page-break-inside: avoid; }
    .ct-status { font-weight: 700; }
    @media print { .ct-report-table thead { display: table-header-group; } }
</style>

<div class="ct-report-filters">
    <strong>Applied filters:</strong>
    @if(request()->filled('search'))
        Search: {{ request('search') }}
    @endif
    @if(request()->filled('status'))
        @if(request()->filled('search')) · @endif
        Status: {{ request('status') === '1' ? 'Active' : 'Inactive' }}
    @endif
    @unless(request()->filled('search') || request()->filled('status'))
        All complaint types
    @endunless
</div>

<table class="ct-report-table">
    <thead>
        <tr>
            <th style="width: 22%">Division</th>
            <th style="width: 25%">Complaint Type</th>
            <th style="width: 38%">Description</th>
            <th style="width: 15%">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($categories as $category)
            <tr>
                <td>{{ $category->division?->name ?? 'No division assigned' }}</td>
                <td><strong>{{ $category->name }}</strong></td>
                <td>{{ $category->description ?: 'No description provided.' }}</td>
                <td class="ct-status">{{ $category->is_active ? 'Active' : 'Inactive' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center;padding:18px">
                    No complaint types match the selected filters.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
