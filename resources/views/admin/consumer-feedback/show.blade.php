@extends('admin.layouts.app')

@section('content')

<style>
        .cfd-page {
            --cfd-border: #e3eaf3;
            --cfd-muted: #77869b;
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
            padding: 26px;
            color: #1d304d;
            font-size: 14px;
        }

        .cfd-page,
        .cfd-page * {
            box-sizing: border-box;
        }

        .cfd-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 18px;
            margin-bottom: 20px;
        }

        .cfd-header h1 {
            margin: 5px 0 7px;
            font-size: 27px;
            font-weight: 750;
            color: #182b49;
        }

        .cfd-header p {
            margin: 0;
            color: var(--cfd-muted);
            line-height: 1.5;
        }

        .cfd-back-link {
            display: inline-block;
            margin-bottom: 15px;
            color: #1769d2;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
        }

        .cfd-back-link:hover {
            text-decoration: underline;
        }

        .cfd-eyebrow {
            color: #3275c9;
            font-size: 10px;
            letter-spacing: 1.4px;
            font-weight: 800;
        }

        .cfd-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 40px;
            padding: 10px 15px;
            border: 1px solid #d6dfeb;
            border-radius: 8px;
            background: #fff;
            color: #34445d;
            font: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .cfd-button:hover {
            background: #f5f8fc;
        }

        .cfd-notice {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 20px;
            padding: 15px 17px;
            border: 1px solid #cfe3ff;
            border-radius: 10px;
            background: #f1f7ff;
            color: #244d80;
        }

        .cfd-notice-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 23px;
            width: 23px;
            height: 23px;
            border-radius: 50%;
            background: #d9eaff;
            font-weight: 800;
        }

        .cfd-notice strong {
            font-size: 12px;
        }

        .cfd-notice p {
            margin: 4px 0 0;
            font-size: 12px;
            line-height: 1.5;
        }

        .cfd-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(280px, .9fr);
            gap: 20px;
            align-items: start;
        }

        .cfd-main,
        .cfd-aside {
            display: grid;
            gap: 20px;
            min-width: 0;
        }

        .cfd-card {
            min-width: 0;
            overflow: hidden;
            border: 1px solid var(--cfd-border);
            border-radius: 13px;
            background: #fff;
            box-shadow: 0 3px 12px rgba(24, 48, 82, .045);
        }

        .cfd-card-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 19px 21px;
            border-bottom: 1px solid #edf1f6;
        }

        .cfd-card-heading h2 {
            margin: 0 0 5px;
            color: #203452;
            font-size: 16px;
            font-weight: 750;
        }

        .cfd-card-heading p {
            margin: 0;
            color: var(--cfd-muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .cfd-record-id {
            padding: 6px 9px;
            border-radius: 6px;
            background: #f0f5fb;
            color: #536780;
            font-size: 11px;
            font-weight: 700;
        }

        .cfd-card-body {
            padding: 21px;
        }

        .cfd-metrics {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .cfd-metric {
            min-width: 0;
        }

        .cfd-field-label {
            display: block;
            margin-bottom: 7px;
            color: #7b899d;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .cfd-metric-value {
            color: #243953;
            font-size: 14px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .cfd-rating-large {
            color: #a96a00;
            font-size: 27px;
            font-weight: 800;
        }

        .cfd-rating-large small {
            margin-left: 3px;
            color: #98a4b5;
            font-size: 13px;
        }

        .cfd-muted {
            color: var(--cfd-muted);
            font-size: 11px;
        }

        .cfd-status {
            display: inline-block;
            max-width: 100%;
            padding: 5px 9px;
            border-radius: 6px;
            font-size: 10px;
            line-height: 1.4;
            font-weight: 750;
        }

        .cfd-status-new {
            color: #1559b7;
            background: #e9f2ff;
        }

        .cfd-status-reviewed {
            color: #475b78;
            background: #edf1f6;
        }

        .cfd-status-attention {
            color: #986000;
            background: #fff3d5;
        }

        .cfd-status-resolved {
            color: #14764f;
            background: #e3f7ed;
        }

        .cfd-status-danger {
            color: #b42332;
            background: #ffeaec;
        }

        .cfd-status-neutral {
            color: #59677b;
            background: #eef1f5;
        }

        .cfd-divider {
            height: 1px;
            margin: 22px 0;
            background: #edf1f6;
        }

        .cfd-section+.cfd-section {
            margin-top: 22px;
        }

        .cfd-section h3 {
            margin: 0 0 10px;
            color: #2a3e59;
            font-size: 13px;
            font-weight: 750;
        }

        .cfd-text-block {
            min-height: 65px;
            padding: 14px;
            border: 1px solid #e3eaf3;
            border-radius: 8px;
            background: #f8fafd;
            color: #465973;
            font-size: 13px;
            line-height: 1.7;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .cfd-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px 18px;
        }

        .cfd-info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 0;
        }

        .cfd-full-width {
            grid-column: 1 / -1;
        }

        .cfd-info-value {
            color: #2d415d;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.65;
            overflow-wrap: anywhere;
        }

        .cfd-description {
            color: #465973;
            font-size: 13px;
            line-height: 1.7;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .cfd-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .cfd-chip {
            padding: 6px 9px;
            border: 1px solid #e0e8f2;
            border-radius: 7px;
            background: #f7f9fc;
            color: #465973;
            font-size: 11px;
        }

        .cfd-consumer-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 18px;
            margin-bottom: 18px;
            border-bottom: 1px solid #edf1f6;
        }

        .cfd-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 43px;
            width: 43px;
            height: 43px;
            border-radius: 12px;
            background: #e8f2ff;
            color: #1769d2;
            font-size: 18px;
            font-weight: 800;
        }

        .cfd-consumer-name {
            color: #243953;
            font-size: 13px;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .cfd-detail-list {
            display: grid;
            gap: 18px;
        }

        .cfd-detail-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .cfd-detail-item .cfd-field-label {
            margin-bottom: 0;
        }

        .cfd-break {
            word-break: break-word;
        }

        .cfd-preserve {
            white-space: pre-wrap;
        }

        .cfd-empty {
            padding: 25px 10px;
            text-align: center;
            color: var(--cfd-muted);
            font-size: 13px;
        }

        @media (max-width:1000px) {
            .cfd-layout {
                grid-template-columns: minmax(0, 1fr);
            }

            .cfd-aside {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                align-items: start;
            }
        }

        @media (max-width:650px) {
            .cfd-page {
                padding: 16px 12px;
            }

            .cfd-header h1 {
                font-size: 23px;
            }

            .cfd-aside {
                grid-template-columns: minmax(0, 1fr);
            }

            .cfd-card-body {
                padding: 16px;
            }

            .cfd-card-heading {
                padding: 16px;
            }

            .cfd-metrics,
            .cfd-info-grid {
                gap: 18px 12px;
            }
        }

        @media print {
            .cfd-page {
                max-width: none;
                padding: 0;
            }

            .cfd-header .cfd-button,
            .cfd-back-link {
                display: none;
            }

            .cfd-layout {
                grid-template-columns: 1.5fr 1fr;
                gap: 10px;
            }

            .cfd-main,
            .cfd-aside {
                gap: 10px;
            }

            .cfd-card {
                box-shadow: none;
                break-inside: avoid;
            }

            .cfd-notice {
                break-inside: avoid;
            }
        }
    </style>
    @php
        $consumer = $feedback->consumer;
        $complaint = $feedback->complaint;
        $address = $consumer?->address;

        $consumerName = $consumer
            ? trim(
                implode(
                    ' ',
                    array_filter([
                        $consumer->first_name ?? null,
                        $consumer->middle_name ?? null,
                        $consumer->last_name ?? null,
                    ]),
                ),
            )
            : '';

        $consumerName = $consumerName ?: 'Unknown consumer';

        $handlingClass = match ($feedback->handling_status) {
            'New' => 'cfd-status-new',
            'Reviewed' => 'cfd-status-reviewed',
            'Follow-up Required', 'Follow-up In Progress' => 'cfd-status-attention',
            'Resolved' => 'cfd-status-resolved',
            default => 'cfd-status-neutral',
        };

        $resolutionClass = match ($feedback->resolution_status) {
            'Resolved' => 'cfd-status-resolved',
            'Partially Resolved' => 'cfd-status-attention',
            'Not Resolved' => 'cfd-status-danger',
            default => 'cfd-status-neutral',
        };

        $feedbackMessage =
            $feedback->comments ??
            ($feedback->feedback ?? ($feedback->message ?? ($feedback->feedback_comments ?? null)));

        $consumerResponse = $feedback->consumer_response ?? ($feedback->suggestions ?? null);
    @endphp

    <div class="cfd-page">
        <div class="cfd-header">
            <div>
                <a class="cfd-back-link" href="{{ route('admin.consumer-feedback.index') }}">
                    <span aria-hidden="true">←</span> Back to Consumer Feedback
                </a>
                <div class="cfd-eyebrow">ADMINISTRATION / FEEDBACK DETAILS</div>
                <h1>Feedback Details</h1>
                <p>Review the consumer submission and its related complaint record.</p>
            </div>


        </div>

        <div class="cfd-notice">
            <span class="cfd-notice-icon">i</span>
            <div>
                <strong>Read-only monitoring</strong>
                <p>This Admin page does not change feedback statuses or send replies. Those actions remain in Customer
                    Service.</p>
            </div>
        </div>

        <div class="cfd-layout">
            <main class="cfd-main">
                <section class="cfd-card">
                    <div class="cfd-card-heading">
                        <div>
                            <h2>Feedback Summary</h2>
                            <p>Consumer rating and submitted comments</p>
                        </div>
                        <span class="cfd-record-id">Feedback #{{ $feedback->id }}</span>
                    </div>

                    <div class="cfd-card-body">
                        <div class="cfd-metrics">
                            <div class="cfd-metric">
                                <span class="cfd-field-label">Overall Rating</span>
                                @if ($feedback->overall_rating !== null)
                                    <div class="cfd-rating-large">
                                        <span>★</span> {{ $feedback->overall_rating }}<small>/ 5</small>
                                    </div>
                                @else
                                    <div class="cfd-metric-value">Not rated</div>
                                @endif
                            </div>

                            <div class="cfd-metric">
                                <span class="cfd-field-label">Submitted On</span>
                                <div class="cfd-metric-value">
                                    {{ $feedback->created_at?->format('M d, Y') ?? '—' }}
                                </div>
                                <div class="cfd-muted">
                                    {{ $feedback->created_at?->format('h:i A') ?? '' }}
                                </div>
                            </div>

                            <div class="cfd-metric">
                                <span class="cfd-field-label">Consumer Resolution</span>
                                <div class="cfd-metric-value">
                                    <span class="cfd-status {{ $resolutionClass }}">
                                        {{ $feedback->resolution_status ?: 'Not specified' }}
                                    </span>
                                </div>
                            </div>

                            <div class="cfd-metric">
                                <span class="cfd-field-label">Handling Status</span>
                                <div class="cfd-metric-value">
                                    <span class="cfd-status {{ $handlingClass }}">
                                        {{ $feedback->handling_status ?: 'Not specified' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="cfd-divider"></div>

                        <div class="cfd-section">
                            <h3>Feedback Comments</h3>
                            <div class="cfd-text-block">{{ $feedbackMessage ?: 'No written feedback was recorded.' }}</div>
                        </div>

                        <div class="cfd-section">
                            <h3>Additional Consumer Response</h3>
                            <div class="cfd-text-block">{{ $consumerResponse ?: 'No additional response was recorded.' }}
                            </div>
                        </div>
                    </div>
                </section>

                <section class="cfd-card">
                    <div class="cfd-card-heading">
                        <div>
                            <h2>Related Complaint</h2>
                            <p>Complaint and service information associated with this feedback</p>
                        </div>
                    </div>

                    <div class="cfd-card-body">
                        @if ($complaint)
                            <div class="cfd-info-grid">
                                <div class="cfd-info-item">
                                    <span class="cfd-field-label">Complaint Number</span>
                                    <span class="cfd-info-value">{{ $complaint->complaint_no ?? '—' }}</span>
                                </div>

                                <div class="cfd-info-item">
                                    <span class="cfd-field-label">Complaint Status</span>
                                    <span class="cfd-info-value">{{ $complaint->status ?? '—' }}</span>
                                </div>

                                <div class="cfd-info-item">
                                    <span class="cfd-field-label">Service Category</span>
                                    <span class="cfd-info-value">{{ data_get($complaint, 'category.name', '—') }}</span>
                                </div>

                                <div class="cfd-info-item">
                                    <span class="cfd-field-label">Division</span>
                                    <span class="cfd-info-value">{{ data_get($complaint, 'division.name', '—') }}</span>
                                </div>

                                <div class="cfd-info-item cfd-full-width">
                                    <span class="cfd-field-label">Complaint Description</span>
                                    <div class="cfd-description">
                                        {{ $complaint->description ?: 'No description recorded.' }}</div>
                                </div>

                                @if ($complaint->technicians && $complaint->technicians->isNotEmpty())
                                    <div class="cfd-info-item cfd-full-width">
                                        <span class="cfd-field-label">Assigned Technicians</span>
                                        <div class="cfd-chips">
                                            @foreach ($complaint->technicians as $technician)
                                                <span class="cfd-chip">
                                                    {{ $technician->name ?? trim(($technician->first_name ?? '') . ' ' . ($technician->last_name ?? '')) ?:
                                                        'Technician' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="cfd-empty">No related complaint record was found.</div>
                        @endif
                    </div>
                </section>
            </main>

            <aside class="cfd-aside">
                <section class="cfd-card">
                    <div class="cfd-card-heading">
                        <div>
                            <h2>Consumer Information</h2>
                            <p>Account holder details</p>
                        </div>
                    </div>

                    <div class="cfd-card-body">
                        <div class="cfd-consumer-profile">
                            <div class="cfd-avatar">
                                {{ strtoupper(substr($consumerName, 0, 1)) }}
                            </div>
                            <div>
                                <div class="cfd-consumer-name">{{ $consumerName }}</div>
                                <div class="cfd-muted">Consumer</div>
                            </div>
                        </div>

                        <div class="cfd-detail-list">
                            <div class="cfd-detail-item">
                                <span class="cfd-field-label">Account Number</span>
                                <span class="cfd-info-value">{{ $consumer->account_number ?? '—' }}</span>
                            </div>

                            <div class="cfd-detail-item">
                                <span class="cfd-field-label">Email</span>
                                <span class="cfd-info-value cfd-break">{{ $consumer->email ?? '—' }}</span>
                            </div>

                            <div class="cfd-detail-item">
                                <span class="cfd-field-label">Contact Number</span>
                                <span
                                    class="cfd-info-value">{{ $consumer->phone ?? ($consumer->contact_number ?? '—') }}</span>
                            </div>

                            <div class="cfd-detail-item">
                                <span class="cfd-field-label">Address</span>
                                <span class="cfd-info-value">
                                    @if ($address)
                                        {{ implode(
                                            ', ',
                                            array_filter([
                                                $address->house_number ?? null,
                                                $address->street ?? null,
                                                $address->purok ?? null,
                                                $address->barangay ?? null,
                                                $address->municipality ?? null,
                                            ]),
                                        ) ?:
                                            'Not available' }}
                                    @else
                                        Not available
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="cfd-card">
                    <div class="cfd-card-heading">
                        <div>
                            <h2>Handling Information</h2>
                            <p>Recorded Customer Service activity</p>
                        </div>
                    </div>

                    <div class="cfd-card-body">
                        <div class="cfd-detail-list">
                            <div class="cfd-detail-item">
                                <span class="cfd-field-label">Reviewed By</span>
                                <span class="cfd-info-value">
                                    @if ($feedback->reviewer)
                                        {{ $feedback->reviewer->name ??
                                        trim(($feedback->reviewer->first_name ?? '') . ' ' . ($feedback->reviewer->last_name ?? '')) ?:
                                            'Staff member' }}
                                    @else
                                        Not recorded
                                    @endif
                                </span>
                            </div>

                            <div class="cfd-detail-item">
                                <span class="cfd-field-label">Reviewed At</span>
                                <span class="cfd-info-value">
                                    {{ $feedback->reviewed_at
                                        ? \Illuminate\Support\Carbon::parse($feedback->reviewed_at)->format('M d, Y h:i A')
                                        : 'Not recorded' }}
                                </span>
                            </div>

                            <div class="cfd-detail-item">
                                <span class="cfd-field-label">Follow-up Date</span>
                                <span class="cfd-info-value">
                                    {{ $feedback->follow_up_at
                                        ? \Illuminate\Support\Carbon::parse($feedback->follow_up_at)->format('M d, Y h:i A')
                                        : 'Not recorded' }}
                                </span>
                            </div>

                            <div class="cfd-detail-item">
                                <span class="cfd-field-label">Follow-up Notes</span>
                                <span class="cfd-info-value cfd-preserve">
                                    {{ $feedback->follow_up_notes ?: 'No follow-up notes recorded.' }}
                                </span>
                            </div>

                            <div class="cfd-detail-item">
                                <span class="cfd-field-label">Resolved At</span>
                                <span class="cfd-info-value">
                                    {{ $feedback->resolved_at
                                        ? \Illuminate\Support\Carbon::parse($feedback->resolved_at)->format('M d, Y h:i A')
                                        : 'Not recorded' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>


@endsection
