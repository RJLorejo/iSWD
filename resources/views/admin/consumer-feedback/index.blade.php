```blade
@extends('admin.layouts.app')

@section('content')
@php
    $handlingClass = static fn($status) => match ($status) {
        'New' => 'cf-status-new',
        'Reviewed' => 'cf-status-reviewed',
        'Follow-up Required', 'Follow-up In Progress' => 'cf-status-attention',
        'Resolved' => 'cf-status-resolved',
        default => 'cf-status-neutral',
    };

    $resolutionClass = static fn($status) => match ($status) {
        'Resolved' => 'cf-status-resolved',
        'Partially Resolved' => 'cf-status-attention',
        'Not Resolved' => 'cf-status-danger',
        default => 'cf-status-neutral',
    };

    $hasFilters = request()->filled('search')
        || request()->filled('rating')
        || request()->filled('resolution_status')
        || request()->filled('handling_status')
        || request()->filled('from')
        || request()->filled('to');
@endphp

<style>
    .cf-page {
        --cf-ink: #172b4d;
        --cf-muted: #718096;
        --cf-border: #e3eaf3;
        --cf-blue: #1769d2;
        width: 100%;
        max-width: 1600px;
        min-width: 0;
        margin: 0 auto;
        padding: clamp(12px, 2.2vw, 26px);
        color: var(--cf-ink);
        font-family: inherit;
        font-size: 14px;
    }

    .cf-page,
    .cf-page * {
        box-sizing: border-box;
        min-width: 0;
    }

    .cf-page a,
    .cf-page button,
    .cf-page input,
    .cf-page select {
        -webkit-tap-highlight-color: transparent;
    }

    .cf-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 22px;
    }

    .cf-page-header > div {
        flex: 1 1 240px;
    }

    .cf-eyebrow {
        color: #3275c9;
        font-size: 10px;
        letter-spacing: 1.3px;
        font-weight: 800;
    }

    .cf-page-header h1 {
        margin: 6px 0;
        color: #182b49;
        font-size: clamp(22px, 3vw, 28px);
        font-weight: 800;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .cf-page-header p,
    .cf-panel-heading p,
    .cf-records-heading p {
        margin: 0;
        color: var(--cf-muted);
        line-height: 1.55;
        font-size: 13px;
    }

    .cf-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 10px 15px;
        border: 1px solid transparent;
        border-radius: 9px;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.35;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: background .15s ease, border-color .15s ease;
    }

    .cf-button:focus-visible,
    .cf-view-link:focus-visible {
        outline: 3px solid #93c5fd;
        outline-offset: 3px;
    }

    .cf-button-primary {
        color: #fff;
        background: #1769d2;
        border-color: #1769d2;
    }

    .cf-button-primary:hover {
        color: #fff;
        background: #1256b0;
    }

    .cf-button-secondary {
        color: #34445d;
        background: #fff;
        border-color: #d6dfeb;
    }

    .cf-button-secondary:hover {
        color: #172b4d;
        background: #f5f8fc;
    }

    .cf-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .cf-summary-card,
    .cf-panel {
        background: #fff;
        border: 1px solid var(--cf-border);
        border-radius: 14px;
        box-shadow: 0 3px 12px rgb(24 48 82 / 4%);
    }

    .cf-summary-card {
        padding: 18px;
    }

    .cf-summary-top {
        display: flex;
        align-items: center;
        gap: 11px;
        margin-bottom: 14px;
    }

    .cf-summary-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 40px;
        width: 40px;
        height: 40px;
        border-radius: 11px;
        font-size: 19px;
        font-weight: 800;
    }

    .cf-icon-blue { color: #1769d2; background: #eaf3ff; }
    .cf-icon-purple { color: #7046c7; background: #f0eaff; }
    .cf-icon-amber { color: #a96900; background: #fff4d8; }
    .cf-icon-green { color: #16805a; background: #e5f7ee; }

    .cf-summary-label {
        color: #66758b;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.4;
    }

    .cf-summary-value {
        color: #182b49;
        font-size: clamp(24px, 2.4vw, 30px);
        font-weight: 800;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .cf-summary-note {
        margin-top: 7px;
        color: #8793a5;
        font-size: 11px;
        line-height: 1.5;
    }

    .cf-rating-denominator {
        color: #7c899b;
        font-size: 14px;
        font-weight: 600;
    }

    .cf-panel {
        margin-bottom: 22px;
        overflow: hidden;
    }

    .cf-panel-heading,
    .cf-records-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding: 19px 22px;
        border-bottom: 1px solid #edf1f6;
    }

    .cf-panel-heading h2,
    .cf-records-heading h2 {
        margin: 0 0 5px;
        color: #203452;
        font-size: 16px;
        font-weight: 800;
        line-height: 1.4;
    }

    .cf-filter-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) repeat(2, minmax(0, 1fr));
        gap: 16px;
        padding: 20px 22px;
    }

    .cf-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
        min-width: 0;
    }

    .cf-field-search {
        grid-column: 1 / -1;
    }

    .cf-field label {
        color: #4b5d76;
        font-size: 12px;
        font-weight: 700;
    }

    .cf-field input,
    .cf-field select {
        display: block;
        width: 100%;
        max-width: 100%;
        min-height: 44px;
        padding: 10px 12px;
        border: 1px solid #d8e1ed;
        border-radius: 9px;
        background: #fff;
        color: #293d58;
        font: inherit;
        font-size: 13px;
        outline: none;
    }

    .cf-field input:focus,
    .cf-field select:focus {
        border-color: #4389e5;
        box-shadow: 0 0 0 3px rgb(23 105 210 / 10%);
    }

    .cf-field input::placeholder {
        color: #9aa7b8;
    }

    .cf-filter-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding: 0 22px 21px;
    }

    .cf-filter-actions .cf-button {
        min-width: 120px;
    }

    .cf-record-count {
        color: #728198;
        font-size: 12px;
        white-space: nowrap;
    }

    .cf-record-count strong {
        color: #203452;
        font-size: 16px;
    }

    /* Desktop table */
    .cf-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .cf-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .cf-table th {
        padding: 13px 15px;
        background: #f7f9fc;
        color: #687992;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .4px;
        text-transform: uppercase;
        white-space: nowrap;
        border-bottom: 1px solid #e7edf5;
    }

    .cf-table td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #edf1f6;
    }

    .cf-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .cf-table tbody tr:hover {
        background: #fbfdff;
    }

    .cf-primary-text {
        color: #263a57;
        font-size: 12px;
        font-weight: 750;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .cf-secondary-text {
        margin-top: 4px;
        color: #8592a5;
        font-size: 11px;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .cf-dot {
        padding: 0 3px;
        color: #b1bdcb;
    }

    .cf-rating {
        color: #a96a00;
        font-size: 13px;
        font-weight: 800;
        white-space: nowrap;
    }

    .cf-rating-muted {
        color: #98a4b5;
        font-size: 11px;
        font-weight: 600;
    }

    .cf-muted {
        color: #8b97a8;
        font-size: 12px;
    }

    .cf-status {
        display: inline-flex;
        align-items: center;
        max-width: 100%;
        padding: 5px 9px;
        border-radius: 7px;
        font-size: 10px;
        line-height: 1.45;
        font-weight: 750;
        overflow-wrap: anywhere;
    }

    .cf-status-new { color: #1559b7; background: #e9f2ff; }
    .cf-status-reviewed { color: #475b78; background: #edf1f6; }
    .cf-status-attention { color: #986000; background: #fff3d5; }
    .cf-status-resolved { color: #14764f; background: #e3f7ed; }
    .cf-status-danger { color: #b42332; background: #ffeaec; }
    .cf-status-neutral { color: #59677b; background: #eef1f5; }

    .cf-action-column {
        text-align: right;
        white-space: nowrap;
    }

    .cf-view-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        min-height: 36px;
        padding: 7px 10px;
        border-radius: 8px;
        color: #1769d2;
        font-size: 12px;
        font-weight: 750;
        text-decoration: none;
    }

    .cf-view-link:hover {
        color: #104d9c;
        background: #eff6ff;
    }

    /* Mobile feedback cards: hidden on desktop */
    .cf-mobile-list {
        display: none;
    }

    .cf-feedback-card {
        min-width: 0;
        padding: 15px;
        border: 1px solid #e4eaf2;
        border-radius: 12px;
        background: #fff;
    }

    .cf-feedback-card + .cf-feedback-card {
        margin-top: 12px;
    }

    .cf-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        padding-bottom: 13px;
        border-bottom: 1px solid #edf1f6;
    }

    .cf-card-complaint {
        flex: 1 1 auto;
        min-width: 0;
    }

    .cf-card-complaint .cf-primary-text {
        font-size: 13px;
    }

    .cf-card-rating {
        flex: 0 0 auto;
        padding: 6px 8px;
        border-radius: 8px;
        background: #fff8e7;
        color: #a96a00;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .cf-card-details {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 12px;
        padding-top: 14px;
    }

    .cf-card-detail {
        min-width: 0;
    }

    .cf-card-label {
        display: block;
        margin-bottom: 5px;
        color: #8190a4;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .45px;
        line-height: 1.4;
        text-transform: uppercase;
    }

    .cf-card-value {
        color: #263a57;
        font-size: 12px;
        font-weight: 650;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .cf-card-action {
        display: flex;
        width: 100%;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        margin-top: 15px;
        padding: 10px 12px;
        border: 1px solid #cfe0f8;
        border-radius: 9px;
        background: #eff6ff;
        color: #1759a6;
        font-size: 13px;
        font-weight: 750;
        text-decoration: none;
    }

    .cf-card-action:hover {
        background: #dbeafe;
    }

    .cf-empty-state {
        padding: 48px 20px;
        text-align: center;
    }

    .cf-empty-icon {
        color: #a9b9cd;
        font-size: 34px;
    }

    .cf-empty-state h3 {
        margin: 12px 0 6px;
        color: #334761;
        font-size: 16px;
        font-weight: 800;
    }

    .cf-empty-state p {
        margin: 0;
        color: #8492a6;
        font-size: 13px;
        line-height: 1.6;
    }

    .cf-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding: 16px 22px;
        border-top: 1px solid #edf1f6;
    }

    .cf-pagination-info {
        color: #78869a;
        font-size: 12px;
    }

    .cf-pagination-links {
        max-width: 100%;
        overflow-x: auto;
    }

    .cf-pagination-links nav {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .cf-pagination-links nav > div:first-child {
        display: none;
    }

    .cf-pagination-links nav > div:last-child > span,
    .cf-pagination-links nav > div:last-child > a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        min-height: 34px;
        padding: 5px 9px;
        border: 1px solid #dce5ef;
        border-radius: 7px;
        color: #435771;
        background: #fff;
        text-decoration: none;
        font-size: 12px;
    }

    .cf-pagination-links nav > div:last-child span[aria-current="page"] span {
        border-color: #1769d2;
        border-radius: 5px;
        background: #1769d2;
        color: #fff;
    }

    @media (max-width: 1100px) {
        .cf-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    /* Tablet and mobile: replace table with stacked cards */
    @media (max-width: 767px) {
        .cf-page {
            padding: 14px 12px;
        }

        .cf-page-header {
            align-items: stretch;
            gap: 13px;
            margin-bottom: 17px;
        }

        .cf-page-header > div {
            flex-basis: 100%;
        }

        .cf-page-header .cf-button {
            width: 100%;
        }

        .cf-summary-grid {
            gap: 10px;
            margin-bottom: 16px;
        }

        .cf-summary-card {
            padding: 13px;
            border-radius: 12px;
        }

        .cf-summary-top {
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 10px;
        }

        .cf-summary-icon {
            flex-basis: 34px;
            width: 34px;
            height: 34px;
            border-radius: 9px;
            font-size: 16px;
        }

        .cf-summary-label {
            padding-top: 2px;
            font-size: 11px;
        }

        .cf-summary-value {
            font-size: clamp(22px, 6vw, 27px);
        }

        .cf-summary-note {
            font-size: 10px;
        }

        .cf-panel {
            margin-bottom: 16px;
            border-radius: 12px;
        }

        .cf-panel-heading,
        .cf-records-heading {
            align-items: flex-start;
            padding: 15px;
        }

        .cf-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 13px 11px;
            padding: 15px;
        }

        .cf-field-search {
            grid-column: 1 / -1;
        }

        .cf-field input,
        .cf-field select {
            min-height: 44px;
            padding: 10px;
            font-size: 12px;
        }

        .cf-filter-actions {
            padding: 0 15px 15px;
        }

        .cf-filter-actions .cf-button {
            flex: 1 1 130px;
        }

        .cf-table-wrap {
            display: none;
        }

        .cf-mobile-list {
            display: block;
            padding: 12px;
        }

        .cf-pagination {
            align-items: flex-start;
            flex-direction: column;
            padding: 14px 15px;
        }

        .cf-pagination-links {
            width: 100%;
        }

        .cf-pagination-links nav {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 380px) {
        .cf-page {
            padding: 10px 8px;
        }

        .cf-summary-grid {
            gap: 8px;
        }

        .cf-summary-card {
            padding: 10px;
        }

        .cf-summary-top {
            flex-direction: column;
            gap: 7px;
        }

        .cf-summary-label {
            font-size: 10px;
        }

        .cf-summary-value {
            font-size: 22px;
        }

        .cf-filter-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .cf-field-search {
            grid-column: auto;
        }

        .cf-filter-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .cf-filter-actions .cf-button {
            width: 100%;
            flex-basis: auto;
        }

        .cf-card-details {
            grid-template-columns: minmax(0, 1fr);
            gap: 12px;
        }

        .cf-record-count {
            white-space: normal;
        }
    }
</style>

<div class="cf-page">

    {{-- Page header --}}
    <header class="cf-page-header">
        <div>
            <div class="cf-eyebrow">ADMINISTRATION / MONITORING</div>
            <h1>Consumer Feedback</h1>
            <p>Monitor consumer ratings, feedback, and complaint outcomes.</p>
        </div>

        <a class="cf-button cf-button-primary"
           href="{{ route('admin.consumer-feedback.print', request()->query()) }}"
           target="_blank"
           rel="noopener noreferrer">
            <span aria-hidden="true">▤</span>
            Print Report
        </a>
    </header>

    {{-- Summary cards --}}
    <section class="cf-summary-grid" aria-label="Feedback summary">
        <article class="cf-summary-card">
            <div class="cf-summary-top">
                <div class="cf-summary-icon cf-icon-blue" aria-hidden="true">▤</div>
                <div class="cf-summary-label">Total Feedback</div>
            </div>
            <div class="cf-summary-value">{{ number_format($totalFeedback) }}</div>
            <div class="cf-summary-note">All submitted records</div>
        </article>

        <article class="cf-summary-card">
            <div class="cf-summary-top">
                <div class="cf-summary-icon cf-icon-purple" aria-hidden="true">◷</div>
                <div class="cf-summary-label">New Feedback</div>
            </div>
            <div class="cf-summary-value">{{ number_format($newCount) }}</div>
            <div class="cf-summary-note">Awaiting initial review</div>
        </article>

        <article class="cf-summary-card">
            <div class="cf-summary-top">
                <div class="cf-summary-icon cf-icon-amber" aria-hidden="true">!</div>
                <div class="cf-summary-label">Needs Attention</div>
            </div>
            <div class="cf-summary-value">{{ number_format($needsAttentionCount) }}</div>
            <div class="cf-summary-note">Follow-up statuses</div>
        </article>

        <article class="cf-summary-card">
            <div class="cf-summary-top">
                <div class="cf-summary-icon cf-icon-green" aria-hidden="true">★</div>
                <div class="cf-summary-label">Average Rating</div>
            </div>
            <div class="cf-summary-value">
                {{ $averageRating !== null ? number_format($averageRating, 1) : 'N/A' }}
                @if ($averageRating !== null)
                    <span class="cf-rating-denominator">/ 5</span>
                @endif
            </div>
            <div class="cf-summary-note">{{ number_format($resolvedCount) }} resolved records</div>
        </article>
    </section>

    {{-- Search and filters --}}
    <section class="cf-panel" aria-labelledby="cf-filter-heading">
        <div class="cf-panel-heading">
            <div>
                <h2 id="cf-filter-heading">Search and Filters</h2>
                <p>Find feedback by consumer, rating, status, or submission date.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.consumer-feedback.index') }}">
            <div class="cf-filter-grid">
                <div class="cf-field cf-field-search">
                    <label for="cf-search">Search</label>
                    <input
                        id="cf-search"
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Consumer, complaint number, category..."
                        autocomplete="off">
                </div>

                <div class="cf-field">
                    <label for="cf-rating">Rating</label>
                    <select name="rating" id="cf-rating">
                        <option value="">All ratings</option>
                        @for ($rating = 5; $rating >= 1; $rating--)
                            <option value="{{ $rating }}" @selected((string) request('rating') === (string) $rating)>
                                {{ $rating }} {{ $rating === 1 ? 'star' : 'stars' }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="cf-field">
                    <label for="cf-resolution">Consumer Resolution</label>
                    <select name="resolution_status" id="cf-resolution">
                        <option value="">All resolutions</option>
                        @foreach (['Resolved', 'Partially Resolved', 'Not Resolved'] as $status)
                            <option value="{{ $status }}" @selected(request('resolution_status') === $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="cf-field">
                    <label for="cf-handling">Handling Status</label>
                    <select name="handling_status" id="cf-handling">
                        <option value="">All handling statuses</option>
                        @foreach (['New', 'Reviewed', 'Follow-up Required', 'Follow-up In Progress', 'Resolved'] as $status)
                            <option value="{{ $status }}" @selected(request('handling_status') === $status)>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="cf-field">
                    <label for="cf-from">Submitted From</label>
                    <input id="cf-from" type="date" name="from" value="{{ request('from') }}">
                </div>

                <div class="cf-field">
                    <label for="cf-to">Submitted To</label>
                    <input id="cf-to" type="date" name="to" value="{{ request('to') }}">
                </div>
            </div>

            <div class="cf-filter-actions">
                <button class="cf-button cf-button-primary" type="submit">
                    <span aria-hidden="true">⌕</span>
                    Apply Filters
                </button>

                @if ($hasFilters)
                    <a class="cf-button cf-button-secondary"
                       href="{{ route('admin.consumer-feedback.index') }}">
                        Clear Filters
                    </a>
                @endif
            </div>
        </form>
    </section>

    {{-- Feedback records --}}
    <section class="cf-panel cf-records-panel" aria-labelledby="cf-records-heading">
        <div class="cf-records-heading">
            <div>
                <h2 id="cf-records-heading">Feedback Records</h2>
                <p>Review consumer submissions and their complaint outcomes.</p>
            </div>

            <div class="cf-record-count">
                <strong>{{ number_format($feedback->total()) }}</strong>
                {{ $feedback->total() === 1 ? 'record' : 'records' }}
            </div>
        </div>

        @if ($feedback->count())

            {{-- Desktop and large tablet table --}}
            <div class="cf-table-wrap">
                <table class="cf-table">
                    <thead>
                        <tr>
                            <th scope="col">Complaint / Service</th>
                            <th scope="col">Consumer</th>
                            <th scope="col">Rating</th>
                            <th scope="col">Consumer Resolution</th>
                            <th scope="col">Handling Status</th>
                            <th scope="col">Submitted</th>
                            <th scope="col" class="cf-action-column">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($feedback as $item)
                            @php
                                $consumer = $item->consumer;
                                $complaint = $item->complaint;

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
                                <td>
                                    <div class="cf-primary-text">
                                        {{ data_get($complaint, 'complaint_no', 'No complaint number') }}
                                    </div>
                                    <div class="cf-secondary-text">
                                        {{ data_get($complaint, 'category.name', 'Uncategorized') }}
                                        <span class="cf-dot">·</span>
                                        {{ data_get($complaint, 'division.name', 'No division') }}
                                    </div>
                                </td>

                                <td>
                                    <div class="cf-primary-text">{{ $consumerName }}</div>
                                    <div class="cf-secondary-text">
                                        Account: {{ $consumer->account_number ?? '—' }}
                                    </div>
                                </td>

                                <td>
                                    @if ($item->overall_rating !== null)
                                        <span class="cf-rating">
                                            <span aria-hidden="true">★</span>
                                            {{ $item->overall_rating }}<span class="cf-rating-muted">/5</span>
                                        </span>
                                    @else
                                        <span class="cf-muted">Not rated</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="cf-status {{ $resolutionClass($item->resolution_status) }}">
                                        {{ $item->resolution_status ?: 'Not specified' }}
                                    </span>
                                </td>

                                <td>
                                    <span class="cf-status {{ $handlingClass($item->handling_status) }}">
                                        {{ $item->handling_status ?: 'Not specified' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="cf-primary-text">
                                        {{ $item->created_at?->format('M d, Y') ?? '—' }}
                                    </div>
                                    <div class="cf-secondary-text">
                                        {{ $item->created_at?->format('h:i A') ?? '' }}
                                    </div>
                                </td>

                                <td class="cf-action-column">
                                    <a class="cf-view-link"
                                       href="{{ route('admin.consumer-feedback.show', $item->id) }}">
                                        View Details <span aria-hidden="true">→</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards: visible on screens 767px and below --}}
            <div class="cf-mobile-list">
                @foreach ($feedback as $item)
                    @php
                        $consumer = $item->consumer;
                        $complaint = $item->complaint;

                        $consumerName = $consumer
                            ? trim(implode(' ', array_filter([
                                $consumer->first_name ?? null,
                                $consumer->middle_name ?? null,
                                $consumer->last_name ?? null,
                            ])))
                            : '';

                        $consumerName = $consumerName ?: 'Unknown consumer';
                    @endphp

                    <article class="cf-feedback-card">
                        <div class="cf-card-top">
                            <div class="cf-card-complaint">
                                <div class="cf-card-label">Complaint / Service</div>
                                <div class="cf-primary-text">
                                    {{ data_get($complaint, 'complaint_no', 'No complaint number') }}
                                </div>
                                <div class="cf-secondary-text">
                                    {{ data_get($complaint, 'category.name', 'Uncategorized') }}
                                    <span class="cf-dot">·</span>
                                    {{ data_get($complaint, 'division.name', 'No division') }}
                                </div>
                            </div>

                            <div class="cf-card-rating">
                                @if ($item->overall_rating !== null)
                                    <span aria-hidden="true">★</span>
                                    {{ $item->overall_rating }}/5
                                @else
                                    Not rated
                                @endif
                            </div>
                        </div>

                        <div class="cf-card-details">
                            <div class="cf-card-detail">
                                <span class="cf-card-label">Consumer</span>
                                <div class="cf-card-value">{{ $consumerName }}</div>
                                <div class="cf-secondary-text">
                                    Account: {{ $consumer->account_number ?? '—' }}
                                </div>
                            </div>

                            <div class="cf-card-detail">
                                <span class="cf-card-label">Submitted</span>
                                <div class="cf-card-value">
                                    {{ $item->created_at?->format('M d, Y') ?? '—' }}
                                </div>
                                <div class="cf-secondary-text">
                                    {{ $item->created_at?->format('h:i A') ?? '' }}
                                </div>
                            </div>

                            <div class="cf-card-detail">
                                <span class="cf-card-label">Consumer Resolution</span>
                                <span class="cf-status {{ $resolutionClass($item->resolution_status) }}">
                                    {{ $item->resolution_status ?: 'Not specified' }}
                                </span>
                            </div>

                            <div class="cf-card-detail">
                                <span class="cf-card-label">Handling Status</span>
                                <span class="cf-status {{ $handlingClass($item->handling_status) }}">
                                    {{ $item->handling_status ?: 'Not specified' }}
                                </span>
                            </div>
                        </div>

                        <a class="cf-card-action"
                           href="{{ route('admin.consumer-feedback.show', $item->id) }}">
                            View Feedback Details
                            <span aria-hidden="true">→</span>
                        </a>
                    </article>
                @endforeach
            </div>

        @else
            <div class="cf-empty-state">
                <div class="cf-empty-icon" aria-hidden="true">☷</div>
                <h3>No feedback records found</h3>
                <p>
                    @if ($hasFilters)
                        Try changing your search or clearing the selected filters.
                    @else
                        Consumer feedback submissions will appear here when available.
                    @endif
                </p>

                @if ($hasFilters)
                    <a class="cf-button cf-button-secondary"
                       style="margin-top:16px"
                       href="{{ route('admin.consumer-feedback.index') }}">
                        Clear Filters
                    </a>
                @endif
            </div>
        @endif

        {{-- Pagination --}}
        @if ($feedback->hasPages())
            <div class="cf-pagination">
                <div class="cf-pagination-info">
                    Showing {{ $feedback->firstItem() }}–{{ $feedback->lastItem() }}
                    of {{ number_format($feedback->total()) }} records
                </div>

                <div class="cf-pagination-links">
                    {{ $feedback->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </section>
</div>
@endsection
```
