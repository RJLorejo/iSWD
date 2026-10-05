@extends('layouts.report')

@section('title', 'Maintenance Technician Complaint Report')

@section('prepared-by-name')
    {{ auth()->user()->full_name ?? auth()->user()->name ?? 'N/A' }}
@endsection

@section('prepared-by-role', 'Maintenance Technician')


@push('styles')

    <style>

        .technician-complaint-report {
            width: 100%;
        }

        .technician-complaint-report .report-table th:nth-child(1),
        .technician-complaint-report .report-table td:nth-child(1) {
            width: 12%;
        }

        .technician-complaint-report .report-table th:nth-child(2),
        .technician-complaint-report .report-table td:nth-child(2) {
            width: 15%;
        }

        .technician-complaint-report .report-table th:nth-child(3),
        .technician-complaint-report .report-table td:nth-child(3) {
            width: 15%;
        }

        .technician-complaint-report .report-table th:nth-child(4),
        .technician-complaint-report .report-table td:nth-child(4) {
            width: 25%;
        }

        .technician-complaint-report .report-table th:nth-child(5),
        .technician-complaint-report .report-table td:nth-child(5) {
            width: 11%;
        }

        .technician-complaint-report .report-table th:nth-child(6),
        .technician-complaint-report .report-table td:nth-child(6) {
            width: 11%;
        }

        .technician-complaint-report .report-table th:nth-child(7),
        .technician-complaint-report .report-table td:nth-child(7) {
            width: 11%;
        }

        @page {
            size: A4 landscape;
            margin: 9mm;
        }

        @media print {

            .technician-complaint-report .report-table {
                font-size: 7pt;
            }

            .technician-complaint-report .report-table th {
                font-size: 6.5pt;
            }

            .technician-complaint-report .report-table th,
            .technician-complaint-report .report-table td {
                padding: 1.5mm 1.3mm;
            }

        }

    </style>

@endpush


@section('report-content')

    <div class="technician-complaint-report">

        <section class="report-section">

            <div class="report-section-heading-row">

                <h3 class="report-section-title">
                    Complaint Records
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

                    <table class="report-table">

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
                                    Location
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Reported
                                </th>

                                <th>
                                    Updated
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

                                    $displayStatus =
                                        $complaint->status === 'Completed'
                                            ? 'Accomplished'
                                            : $complaint->status;

                                @endphp


                                <tr>

                                    <td>

                                        <strong>
                                            {{ $complaint->complaint_no }}
                                        </strong>

                                        <div
                                            style="
                                                margin-top: 1mm;
                                                font-size: 6.5pt;
                                                font-weight: 700;
                                                color: #4b5563;
                                            "
                                        >
                                            {{ $urgency ?: 'NOT ASSESSED' }}
                                        </div>

                                    </td>


                                    <td>

                                        {{ $complaint->consumer?->full_name
                                            ?? (
                                                $complaint->complainant_name
                                                ?? 'Unknown'
                                            ) }}

                                        @if ($complaint->consumer?->account_number)

                                            <div
                                                style="
                                                    margin-top: 1mm;
                                                    color: #6b7280;
                                                    font-size: 6.5pt;
                                                "
                                            >
                                                {{ $complaint->consumer->account_number }}
                                            </div>

                                        @endif

                                    </td>


                                    <td>
                                        {{ $complaint->category?->name
                                            ?? 'Uncategorized' }}
                                    </td>


                                    <td>
                                        {{ $complaint->address ?: '—' }}
                                    </td>


                                    <td>
                                        <strong>
                                            {{ $displayStatus }}
                                        </strong>
                                    </td>


                                    <td>

                                        {{ $complaint->created_at
                                            ?->format('M d, Y')
                                            ?? '—' }}

                                        @if ($complaint->created_at)

                                            <div
                                                style="
                                                    margin-top: 0.5mm;
                                                    color: #6b7280;
                                                    font-size: 6.5pt;
                                                "
                                            >
                                                {{ $complaint->created_at->format('h:i A') }}
                                            </div>

                                        @endif

                                    </td>


                                    <td>

                                        {{ $complaint->updated_at
                                            ?->format('M d, Y')
                                            ?? '—' }}

                                        @if ($complaint->updated_at)

                                            <div
                                                style="
                                                    margin-top: 0.5mm;
                                                    color: #6b7280;
                                                    font-size: 6.5pt;
                                                "
                                            >
                                                {{ $complaint->updated_at->format('h:i A') }}
                                            </div>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="report-text-block text-center">
                    No complaints matched the selected filters.
                </div>

            @endif

        </section>

    </div>

@endsection
