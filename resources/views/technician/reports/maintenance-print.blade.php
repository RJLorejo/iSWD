@extends('layouts.report')

@section('title', 'Technician Work Summary')

@section('report-title', 'Technician Work Summary')

@section(
    'report-subtitle',
    'Field Maintenance Accomplishment Summary'
)

@section('report-period')
    Reporting Period:
    {{ $from->format('F d, Y') }}
    —
    {{ $to->format('F d, Y') }}
@endsection

@section('prepared-by-name')
    {{ auth()->user()->full_name
        ?? auth()->user()->name
        ?? 'N/A' }}
@endsection

@section('prepared-by-role', 'Maintenance Technician')


@push('styles')

    <style>

        .work-summary-cards {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 4mm;
            margin-bottom: 5mm;
        }

        .work-summary-card {
            border: 1px solid #d1d5db;
            padding: 3mm 4mm;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .work-summary-label {
            color: #6b7280;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .work-summary-value {
            margin-top: 1mm;
            color: #111827;
            font-size: 14pt;
            font-weight: 700;
            line-height: 1.2;
        }

        .technician-work-table th:nth-child(1),
        .technician-work-table td:nth-child(1) {
            width: 19%;
        }

        .technician-work-table th:nth-child(2),
        .technician-work-table td:nth-child(2) {
            width: 22%;
        }

        .technician-work-table th:nth-child(3),
        .technician-work-table td:nth-child(3) {
            width: 24%;
        }

        .technician-work-table th:nth-child(4),
        .technician-work-table td:nth-child(4) {
            width: 13%;
        }

        .technician-work-table th:nth-child(5),
        .technician-work-table td:nth-child(5) {
            width: 22%;
        }

        .complaint-number {
            font-weight: 700;
        }

        .table-secondary {
            margin-top: 0.8mm;
            color: #6b7280;
            font-size: 6.5pt;
            line-height: 1.3;
        }

        .urgency-value {
            font-weight: 700;
        }

        @page {
            size: A4 landscape;
            margin: 9mm;
        }

        @media print {

            .work-summary-cards {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 4mm !important;
            }

            .work-summary-card {
                padding: 2.5mm 3mm !important;
            }

            .work-summary-value {
                font-size: 12pt !important;
            }

            .technician-work-table {
                width: 100% !important;
                table-layout: fixed !important;
                font-size: 7pt !important;
            }

            .technician-work-table th {
                font-size: 6.5pt !important;
            }

            .technician-work-table th,
            .technician-work-table td {
                padding: 1.7mm 1.5mm !important;
            }

        }

    </style>

@endpush


@section('report-content')

    <section class="report-section avoid-break">

        <div class="work-summary-cards">

            <div class="work-summary-card">

                <div class="work-summary-label">
                    Accomplished Work
                </div>

                <div class="work-summary-value">
                    {{ $total }}
                </div>

            </div>


            <div class="work-summary-card">

                <div class="work-summary-label">
                    Average Completion Time
                </div>

                <div class="work-summary-value">
                    {{ $averageCompletionHours }} hrs
                </div>

            </div>

        </div>

    </section>


    <section class="report-section">

        <div class="report-section-heading-row">

            <h3 class="report-section-title">
                Accomplished Work
            </h3>

            <div class="report-record-count">

                Total Records:

                <strong>
                    {{ $complaints->count() }}
                </strong>

            </div>

        </div>


        @if ($complaints->count())

            <div class="report-table-wrapper">

                <table class="report-table technician-work-table">

                    <thead>

                        <tr>

                            <th>
                                Complaint
                            </th>

                            <th>
                                Consumer
                            </th>

                            <th>
                                Complaint Type
                            </th>

                            <th>
                                AI Urgency
                            </th>

                            <th>
                                Accomplished
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach ($complaints as $complaint)

                            @php

                                $urgency =
                                    strtoupper(
                                        trim(
                                            $complaint
                                                ->aiAnalysis
                                                ?->urgency_level
                                            ?? ''
                                        )
                                    );

                            @endphp


                            <tr>

                                <td>

                                    <div class="complaint-number">
                                        {{ $complaint->complaint_no }}
                                    </div>

                                    <div class="table-secondary">

                                        Submitted:

                                        {{ $complaint->created_at
                                            ?->format('M d, Y h:i A')
                                            ?? '—' }}

                                    </div>

                                </td>


                                <td>

                                    @if ($complaint->consumer)

                                        <div>
                                            {{ $complaint->consumer->full_name }}
                                        </div>

                                        @if ($complaint->consumer->account_number)

                                            <div class="table-secondary">

                                                Account:
                                                {{ $complaint->consumer->account_number }}

                                            </div>

                                        @endif

                                    @elseif ($complaint->complainant_name)

                                        <div>
                                            {{ $complaint->complainant_name }}
                                        </div>

                                        @if ($complaint->complainant_phone)

                                            <div class="table-secondary">
                                                {{ $complaint->complainant_phone }}
                                            </div>

                                        @endif

                                    @else

                                        <span class="table-secondary">
                                            No complainant information
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    {{ $complaint->category?->name
                                        ?? 'Uncategorized' }}

                                </td>


                                <td>

                                    <span class="urgency-value">

                                        {{ $urgency !== ''
                                            ? $urgency
                                            : 'NOT ASSESSED' }}

                                    </span>

                                </td>


                                <td>

                                    {{ $complaint->completed_at
                                        ?->format('M d, Y h:i A')
                                        ?? '—' }}

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="report-text-block text-center">

                No accomplished maintenance work was found
                for the selected reporting period.

            </div>

        @endif

    </section>

@endsection

