
@extends('layouts.report')

@section('title', 'Employee Activity Logs Report')

@section('back-url', route('admin.audit-logs.index', request()->query()))

@section('report-title', 'Employee Activity Logs Report')

@section('report-subtitle', 'Internal office employee activities and recorded system changes')

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

    {{-- REPORT SUMMARY --}}
    <section class="report-section">
        <div class="report-grid">

            <div class="report-field">
                <div class="report-field-label">Total Employee Activities</div>
                <div class="report-field-value">
                    {{ number_format($activities->count()) }}
                </div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Report Coverage</div>
                <div class="report-field-value">
                    Internal Employee Monitoring
                </div>
            </div>

            @if (!empty($filters['role']))
                <div class="report-field">
                    <div class="report-field-label">Employee Role</div>
                    <div class="report-field-value">{{ $filters['role'] }}</div>
                </div>
            @endif

            @if (!empty($filters['module']))
                <div class="report-field">
                    <div class="report-field-label">Module</div>
                    <div class="report-field-value">{{ $filters['module'] }}</div>
                </div>
            @endif

            @if (!empty($filters['action']))
                <div class="report-field">
                    <div class="report-field-label">Activity Type</div>
                    <div class="report-field-value">{{ $filters['action'] }}</div>
                </div>
            @endif

            @if (!empty($filters['search']))
                <div class="report-field">
                    <div class="report-field-label">Search Keyword</div>
                    <div class="report-field-value">{{ $filters['search'] }}</div>
                </div>
            @endif

            <div class="report-field">
                <div class="report-field-label">Report Generated</div>
                <div class="report-field-value">
                    {{ $reportGeneratedAt->format('M d, Y h:i A') }}
                </div>
            </div>

            <div class="report-field">
                <div class="report-field-label">Prepared By</div>
                <div class="report-field-value">
                    {{ $reportPreparedBy?->name ?? 'Authorized Administrator' }}
                </div>
            </div>

        </div>
    </section>

    {{-- EMPLOYEE ACTIVITY RECORDS --}}
    <section class="report-section">

        <div class="report-section-heading-row">
            <h3 class="report-section-title">Employee Activity Records</h3>

            <div class="report-record-count">
                Records:
                <strong>{{ number_format($activities->count()) }}</strong>
            </div>
        </div>

        @if ($activities->isEmpty())

            <p>No employee activity records match the selected filters.</p>

        @else

            <div class="report-table-wrapper">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width: 14%;">Date / Time</th>
                            <th style="width: 19%;">Employee</th>
                            <th style="width: 13%;">Module</th>
                            <th style="width: 16%;">Activity</th>
                            <th style="width: 38%;">Description</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($activities as $activity)
                            <tr>
                                <td>
                                    <strong>
                                        {{ $activity->created_at?->format('M d, Y') ?? '—' }}
                                    </strong>
                                    <br>
                                    {{ $activity->created_at?->format('h:i A') ?? '—' }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $activity->user_name ?: 'Unknown employee' }}
                                    </strong>
                                    <br>
                                    <span>
                                        {{ $activity->user_role ?: 'Unknown role' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $activity->module ?: 'General' }}
                                </td>

                                <td>
                                    {{ $activity->action ?: 'Activity recorded' }}
                                </td>

                                <td>
                                    {{ $activity->description ?: 'No description provided.' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endif

    </section>

    {{-- REPORT FOOTNOTE --}}
    <section class="report-section">
        <p class="text-sm">
            This report contains recorded activities performed by internal iSWD employees.
            It is intended for authorized administrative review and internal monitoring.
            The report reflects the records available at the time it was generated.
        </p>
    </section>

@endsection
