
@extends('layouts.report')

@section('title', 'Consumer Feedback Monitoring Report')

@section('report-title', 'Consumer Feedback Monitoring Report')

@section('report-subtitle', 'Administrator Read-Only Monitoring Report')

@section('back-url', route('admin.consumer-feedback.index'))

@section('report-period')
    Feedback records:
    {{ $feedback->isNotEmpty() ? $feedback->last()->created_at?->format('M d, Y') : 'No records' }}
    @if ($feedback->isNotEmpty() && $feedback->first()->created_at?->toDateString() !== $feedback->last()->created_at?->toDateString())
        – {{ $feedback->first()->created_at?->format('M d, Y') }}
    @endif
@endsection

@section('report-content')
@php
    $filteredAverageRating = $feedback->whereNotNull('overall_rating')->avg('overall_rating');
@endphp

<section class="report-section">
    <h3 class="report-section-title">Report Summary</h3>

    <div class="report-grid">
        <div class="report-field">
            <div class="report-field-label">Total Feedback Records</div>
            <div class="report-field-value">{{ number_format($totalFeedback) }}</div>
        </div>

        <div class="report-field">
            <div class="report-field-label">New Feedback</div>
            <div class="report-field-value">{{ number_format($newCount) }}</div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Needs Attention</div>
            <div class="report-field-value">{{ number_format($needsAttentionCount) }}</div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Resolved Records</div>
            <div class="report-field-value">{{ number_format($resolvedCount) }}</div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Average Rating of Filtered Records</div>
            <div class="report-field-value">
                {{ $filteredAverageRating !== null ? number_format($filteredAverageRating, 1) . ' / 5' : 'No ratings available' }}
            </div>
        </div>
    </div>
</section>

<section class="report-section">
    <h3 class="report-section-title">Applied Filters</h3>

    <div class="report-grid">
        <div class="report-field">
            <div class="report-field-label">Search</div>
            <div class="report-field-value">{{ request('search') ?: 'All' }}</div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Rating</div>
            <div class="report-field-value">
                {{ request('rating') ? request('rating') . ' star(s)' : 'All ratings' }}
            </div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Consumer Resolution</div>
            <div class="report-field-value">{{ request('resolution_status') ?: 'All resolutions' }}</div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Handling Status</div>
            <div class="report-field-value">{{ request('handling_status') ?: 'All statuses' }}</div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Submitted From</div>
            <div class="report-field-value">{{ request('from') ?: 'Any date' }}</div>
        </div>

        <div class="report-field">
            <div class="report-field-label">Submitted To</div>
            <div class="report-field-value">{{ request('to') ?: 'Any date' }}</div>
        </div>
    </div>
</section>

<section class="report-section">
    <div class="report-section-heading-row">
        <h3 class="report-section-title">Feedback Records</h3>

        <div class="report-record-count">
            Total: <strong>{{ number_format($totalFeedback) }}</strong>
        </div>
    </div>

    <div class="report-table-wrapper">
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 13%;">Complaint No.</th>
                    <th style="width: 18%;">Consumer</th>
                    <th style="width: 11%;">Rating</th>
                    <th style="width: 15%;">Resolution</th>
                    <th style="width: 16%;">Handling Status</th>
                    <th style="width: 22%;">Submitted</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($feedback as $item)
                    @php
                        $consumer = $item->consumer;

                        $consumerName = $consumer
                            ? trim(implode(' ', array_filter([
                                $consumer->first_name ?? null,
                                $consumer->middle_name ?? null,
                                $consumer->last_name ?? null,
                            ])))
                            : '';

                        $consumerName = $consumerName ?: 'Unknown consumer';
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ data_get($item, 'complaint.complaint_no', '—') }}
                        </td>

                        <td>
                            {{ $consumerName }}
                            @if ($consumer?->account_number)
                                <br>
                                <span style="color:#6b7280;">
                                    {{ $consumer->account_number }}
                                </span>
                            @endif
                        </td>

                        <td>
                            {{ $item->overall_rating !== null
                                ? $item->overall_rating . ' / 5'
                                : 'Not rated' }}
                        </td>

                        <td>{{ $item->resolution_status ?: 'Not specified' }}</td>

                        <td>{{ $item->handling_status ?: 'Not specified' }}</td>

                        <td>{{ $item->created_at?->format('M d, Y h:i A') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            No feedback records match the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="report-section">
    <h3 class="report-section-title">Report Notes</h3>
    <div class="report-text-block">This report is generated from consumer feedback records stored in the system. The report is intended for administrative monitoring only. Feedback handling and response actions remain under the Customer Service workflow.</div>
</section>
@endsection
