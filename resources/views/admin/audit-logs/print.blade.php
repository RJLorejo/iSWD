
@extends('layouts.report')

@section('title', 'System Activity Log Report')

@section('back-url', route('admin.audit-logs.index', request()->query()))

@section('report-title', 'System Activity Log Report')

@section('report-subtitle', 'Recorded activities across the iSWD system')

@section('report-period')
    @if (!empty($filters['from']) || !empty($filters['to']))
        Period:
        {{ !empty($filters['from']) ? \Illuminate\Support\Carbon::parse($filters['from'])->format('M d, Y') : 'Beginning' }}
        to
        {{ !empty($filters['to']) ? \Illuminate\Support\Carbon::parse($filters['to'])->format('M d, Y') : 'Present' }}
    @else
        All recorded dates
    @endif
@endsection

@section('report-content')
    <section class="report-section">
        <div class="report-grid">
            <div class="report-field">
                <div class="report-field-label">Total Matching Records</div>
                <div class="report-field-value">{{ number_format($activities->count()) }}</div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Generated For</div>
                <div class="report-field-value">System activity monitoring</div>
            </div>

            @if (!empty($filters['module']))
                <div class="report-field">
                    <div class="report-field-label">Module Filter</div>
                    <div class="report-field-value">{{ $filters['module'] }}</div>
                </div>
            @endif

            @if (!empty($filters['role']))
                <div class="report-field">
                    <div class="report-field-label">Role Filter</div>
                    <div class="report-field-value">{{ $filters['role'] }}</div>
                </div>
            @endif

            @if (!empty($filters['action']))
                <div class="report-field">
                    <div class="report-field-label">Action Filter</div>
                    <div class="report-field-value">
                        {{ \Illuminate\Support\Str::headline($filters['action']) }}
                    </div>
                </div>
            @endif

            @if (!empty($filters['search']))
                <div class="report-field">
                    <div class="report-field-label">Search Term</div>
                    <div class="report-field-value">{{ $filters['search'] }}</div>
                </div>
            @endif
        </div>
    </section>

    <section class="report-section">
        <div class="report-section-heading-row">
            <h3 class="report-section-title">Activity Records</h3>
            <div class="report-record-count">
                Records: <strong>{{ number_format($activities->count()) }}</strong>
            </div>
        </div>

        @if ($activities->isEmpty())
            <p>No audit records match the selected filters.</p>
        @else
            <div class="report-table-wrapper">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 16%;">Date / Time</th>
                            <th style="width: 17%;">User / Role</th>
                            <th style="width: 13%;">Module</th>
                            <th style="width: 13%;">Action</th>
                            <th style="width: 29%;">Description</th>
                            <th style="width: 12%;">Record</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activities as $activity)
                            <tr>
                                <td>
                                    {{ $activity->created_at?->format('M d, Y') ?? '—' }}<br>
                                    {{ $activity->created_at?->format('h:i:s A') ?? '—' }}
                                </td>
                                <td>
                                    {{ $activity->user_name ?: 'System / Guest' }}<br>
                                    <span>{{ $activity->user_role ?: 'Unknown role' }}</span>
                                </td>
                                <td>{{ $activity->module }}</td>
                                <td>{{ \Illuminate\Support\Str::headline($activity->action) }}</td>
                                <td>{{ $activity->description }}</td>
                                <td>
                                    {{ $activity->subject_type ?: '—' }}
                                    @if ($activity->subject_id)
                                        #{{ $activity->subject_id }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
