<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customer Service Complaint Report</title>


    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 28px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1f2937;
            background: white;
        }

        .container {
            max-width: 1400px;
            margin: auto;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-bottom: 20px;
        }

        button {
            border: 0;
            border-radius: 6px;
            padding: 9px 14px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        .print {
            background: #2563eb;
            color: white;
        }

        .close {
            background: #e5e7eb;
            color: #374151;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 21px;
        }

        .header h2 {
            margin: 5px 0 0;
            font-size: 15px;
            font-weight: 600;
        }

        .header p {
            margin: 5px 0 0;
            color: #6b7280;
        }

        .criteria {
            border: 1px solid #d1d5db;
            background: #f9fafb;
            border-radius: 7px;
            padding: 11px 13px;
            margin-bottom: 16px;
        }

        .criteria-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 7px 18px;
        }

        .label {
            font-weight: bold;
            color: #4b5563;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: table-header-group;
        }

        th {
            border: 1px solid #9ca3af;
            background: #f3f4f6;
            padding: 8px 6px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
        }

        td {
            border: 1px solid #d1d5db;
            padding: 8px 6px;
            vertical-align: top;
            font-size: 10px;
        }

        tr {
            page-break-inside: avoid;
        }

        .complaint-number {
            font-weight: bold;
        }

        .urgency {
            display: block;
            margin-top: 4px;
            font-size: 9px;
            font-weight: bold;
        }

        .high {
            color: #b91c1c;
        }

        .moderate {
            color: #b45309;
        }

        .low {
            color: #15803d;
        }

        .muted {
            color: #6b7280;
            font-size: 9px;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 16px;
            padding-top: 10px;
            border-top: 1px solid #d1d5db;
        }

        @page {
            size: landscape;
            margin: 12mm;
        }

        @media print {

            body {
                padding: 0;
            }

            .actions {
                display: none;
            }

        }
    </style>

</head>


<body>

    <div class="container">

        <div class="actions">

            <button type="button" class="print" onclick="window.print()">

                Print Report

            </button>


            <button type="button" class="close" onclick="window.close()">

                Close

            </button>

        </div>


        <div class="header">

            <h1>
                SAGAY WATER DISTRICT
            </h1>

            <h2>
                Customer Service Complaint Report
            </h2>

            <p>
                Filtered complaint records
            </p>

        </div>


        {{-- ========================================================= --}}
        {{-- FILTER CRITERIA --}}
        {{-- ========================================================= --}}

        <div class="criteria">

            <div class="criteria-grid">

                <div>
                    <span class="label">
                        Search:
                    </span>

                    {{ request('search') ?: 'None' }}
                </div>


                <div>
                    <span class="label">
                        Status:
                    </span>

                    {{ request('status') ?: 'All' }}
                </div>


                <div>
                    <span class="label">
                        Urgency:
                    </span>

                    {{ request('urgency') === 'not_assessed' ? 'Not Assessed' : (request('urgency') ?: 'All') }}
                </div>


                <div>
                    <span class="label">
                        Complaint Type:
                    </span>

                    @php
                        $selectedType = $complaints->firstWhere('complaint_category_id', request('complaint_type'))
                            ?->category?->name;
                    @endphp

                    {{ $selectedType ?: 'All' }}
                </div>


                <div>
                    <span class="label">
                        Division:
                    </span>

                    @php
                        $selectedDivision = $complaints->firstWhere('division_id', request('division_id'))?->division
                            ?->name;
                    @endphp

                    {{ $selectedDivision ?: 'All' }}
                </div>


                <div>
                    <span class="label">
                        From:
                    </span>

                    {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') : 'Beginning' }}
                </div>


                <div>
                    <span class="label">
                        To:
                    </span>

                    {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') : 'Present' }}
                </div>


                <div>
                    <span class="label">
                        Records:
                    </span>

                    {{ $complaints->count() }}
                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- TABLE --}}
        {{-- ========================================================= --}}

        @if ($complaints->count())

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
                            Division
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Reported
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach ($complaints as $complaint)
                        @php

                            $urgency = $complaint->aiAnalysis?->urgency_level;

                            $urgencyClass = match ($urgency) {
                                'High' => 'high',

                                'Moderate' => 'moderate',

                                'Low' => 'low',

                                default => '',
                            };

                        @endphp


                        <tr>

                            <td>

                                <span class="complaint-number">

                                    {{ $complaint->complaint_no }}

                                </span>


                                @if ($urgency)
                                    <span class="urgency {{ $urgencyClass }}">

                                        {{ strtoupper($urgency) }}

                                    </span>
                                @else
                                    <span class="urgency muted">

                                        NOT ASSESSED

                                    </span>
                                @endif

                            </td>


                            <td>

                                {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? '—') }}


                                @if ($complaint->consumer?->account_number)
                                    <div class="muted">

                                        Account:
                                        {{ $complaint->consumer->account_number }}

                                    </div>
                                @endif

                            </td>


                            <td>

                                {{ $complaint->category?->name ?? 'Uncategorized' }}

                            </td>


                            <td>

                                {{ $complaint->division?->name ?? '—' }}

                            </td>


                            <td>

                                {{ $complaint->status }}

                            </td>


                            <td>

                                {{ $complaint->created_at?->format('M d, Y h:i A') ?? '—' }}

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>
        @else
            <div
                style="
                padding: 40px;
                text-align: center;
                border: 1px solid #d1d5db;
                color: #6b7280;
            ">

                No complaints matched the selected filters.

            </div>

        @endif


        <div class="footer">

            <div>

                <strong>
                    Total Records:
                </strong>

                {{ $complaints->count() }}

            </div>


            <div>

                <strong>
                    Generated:
                </strong>

                {{ now()->format('M d, Y h:i A') }}

            </div>

        </div>

    </div>

</body>

</html>
