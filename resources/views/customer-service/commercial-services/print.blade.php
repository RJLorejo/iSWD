@extends('layouts.report')

@section('title', 'Commercial Services Complaints Report')
@section('report-title', 'Verified Commercial Services Complaints')
@section('report-subtitle', 'Customer Service Division — Complaint Register')
@section('back-url', route('customer-service.commercial-services.index', request()->except('page')))

@section('report-period')
    Verified: {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') : 'All dates' }}
    — {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') : 'All dates' }}
@endsection

@section('report-content')
    <section class="report-section">
        <div class="report-grid">
            <div class="report-field">
                <div class="report-field-label">Division</div>
                <div class="report-field-value">Commercial Services</div>
            </div>
            <div class="report-field">
                <div class="report-field-label">Status Filter</div>
                <div class="report-field-value">{{ request('status', 'all') === 'all' ? 'All statuses' : request('status') }}</div>
            </div>
            <div class="report-field">
                <div class="report-field-label">Search Filter</div>
                <div class="report-field-value">{{ request('search') ?: 'None' }}</div>
            </div>
            <div class="report-field">
                <div class="report-field-label">Total Matching Complaints</div>
                <div class="report-field-value">{{ number_format($complaints->count()) }}</div>
            </div>
        </div>
    </section>

    <section class="report-section">
        <div class="report-section-heading-row">
            <h3 class="report-section-title">Verified Complaint Records</h3>
            <div class="report-record-count"><strong>{{ $complaints->count() }}</strong> records</div>
        </div>
        <div class="report-table-wrapper">
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width:7%">#</th>
                        <th style="width:18%">Complaint No.</th>
                        <th style="width:23%">Complainant</th>
                        <th style="width:22%">Complaint Type</th>
                        <th style="width:16%">Verified On</th>
                        <th style="width:14%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($complaints as $complaint)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $complaint->complaint_no }}</td>
                            <td>{{ $complaint->consumer?->full_name ?? $complaint->complainant_name ?? 'Unknown' }}</td>
                            <td>{{ $complaint->category?->name ?? 'Uncategorized' }}</td>
                            <td>{{ $complaint->verified_at?->timezone('Asia/Manila')->format('M d, Y') ?? '—' }}</td>
                            <td>{{ $complaint->status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">No matching verified commercial complaints.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
