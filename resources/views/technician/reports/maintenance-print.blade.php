<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Technician Work Summary</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            color: #111827;
            margin: 36px;
            font-size: 12px;
            line-height: 1.5;
        }

        h1,
        h2,
        p {
            margin-top: 0;
        }

        .no-print {
            margin-bottom: 20px;
        }

        .print-button {
            padding: 10px 18px;
            background: #2563eb;
            color: #ffffff;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        .header {
            border-bottom: 2px solid #111827;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .header-top {
            display: table;
            width: 100%;
        }

        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: top;
        }

        .header-right {
            text-align: right;
        }

        .header h1 {
            margin-bottom: 5px;
            font-size: 23px;
        }

        .organization {
            font-weight: 700;
            margin-bottom: 2px;
        }

        .muted {
            color: #6b7280;
        }

        .technician {
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 3px;
        }

        .summary {
            display: table;
            width: 100%;
            table-layout: fixed;
            border-spacing: 10px 0;
            margin: 22px -10px;
        }

        .summary-box {
            display: table-cell;
            border: 1px solid #d1d5db;
            padding: 14px;
            vertical-align: top;
        }

        .summary-label {
            color: #6b7280;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .summary-value {
            font-size: 22px;
            font-weight: bold;
            margin-top: 4px;
        }

        .section-title {
            margin-top: 28px;
            margin-bottom: 4px;
            font-size: 16px;
        }

        .section-description {
            color: #6b7280;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .complaint-number {
            font-weight: 700;
        }

        .small {
            font-size: 10px;
        }

        .urgency {
            font-weight: 700;
        }

        .footer {
            margin-top: 35px;
            padding-top: 12px;
            border-top: 1px solid #d1d5db;
            font-size: 10px;
            color: #6b7280;
        }

        @media print {
            body {
                margin: 18px;
            }

            .no-print {
                display: none;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>

</head>

<body>

    <div class="no-print">

        <button
            onclick="window.print()"
            class="print-button"
        >
            Print Summary
        </button>

    </div>


    <div class="header">

        <div class="header-top">

            <div class="header-left">

                <h1>
                    Technician Work Summary
                </h1>

                <p class="organization">
                    iSWD — Sagay Water District
                </p>

                <p class="muted">
                    Field Maintenance Operations
                </p>

            </div>


            <div class="header-right">

                <p class="technician">
                    {{ auth()->user()->full_name }}
                </p>

                <p class="muted">
                    Maintenance Technician
                </p>

                <p class="muted">
                    {{ $from->format('M d, Y') }}
                    —
                    {{ $to->format('M d, Y') }}
                </p>

            </div>

        </div>

    </div>


    <div class="summary">

        <div class="summary-box">

            <div class="summary-label">
                Accomplished Work
            </div>

            <div class="summary-value">
                {{ $total }}
            </div>

        </div>


        <div class="summary-box">

            <div class="summary-label">
                Average Completion Time
            </div>

            <div class="summary-value">
                {{ $averageCompletionHours }} hrs
            </div>

        </div>

    </div>


    <h2 class="section-title">
        Accomplished Work
    </h2>

    <p class="section-description">
        Maintenance work accomplished during the selected reporting period.
    </p>


    <table>

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

            @forelse ($complaints as $complaint)

                @php
                    $urgency = strtoupper(
                        trim(
                            $complaint->aiAnalysis?->urgency_level ?? ''
                        )
                    );
                @endphp

                <tr>

                    <td>

                        <div class="complaint-number">
                            {{ $complaint->complaint_no }}
                        </div>

                        <div class="small muted">
                            Submitted:
                            {{ $complaint->created_at?->format('M d, Y h:i A') ?? '—' }}
                        </div>

                    </td>


                    <td>

                        @if ($complaint->consumer)

                            <div>
                                {{ $complaint->consumer->full_name }}
                            </div>

                            @if ($complaint->consumer->account_number)

                                <div class="small muted">
                                    Account:
                                    {{ $complaint->consumer->account_number }}
                                </div>

                            @endif

                        @elseif ($complaint->complainant_name)

                            <div>
                                {{ $complaint->complainant_name }}
                            </div>

                            @if ($complaint->complainant_phone)

                                <div class="small muted">
                                    {{ $complaint->complainant_phone }}
                                </div>

                            @endif

                        @else

                            <span class="muted">
                                No complainant information
                            </span>

                        @endif

                    </td>


                    <td>

                        {{ $complaint->category?->name ?? 'Uncategorized' }}

                    </td>


                    <td>

                        <span class="urgency">

                            {{ $urgency !== ''
                                ? $urgency
                                : 'NOT ASSESSED' }}

                        </span>

                    </td>


                    <td>

                        {{ $complaint->completed_at?->format('M d, Y h:i A') ?? '—' }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="5"
                        style="text-align: center; padding: 25px;"
                    >
                        No accomplished maintenance work found for this reporting period.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <div class="footer">

        Generated by iSWD Technician Work Summary.

        <br>

        Generated on:
        {{ now()->format('M d, Y h:i A') }}

    </div>


    <script>
        window.onload = function () {
            window.print();
        };
    </script>

</body>

</html>
