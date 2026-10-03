<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Complaint Management Report</title>


    <style>
        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            padding: 30px;
            background: #ffffff;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }


        .report-container {
            max-width: 1400px;
            margin: 0 auto;
        }


        .header {
            text-align: center;
            margin-bottom: 24px;
        }


        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #111827;
        }


        .header h2 {
            margin: 6px 0 0;
            font-size: 16px;
            font-weight: 600;
            color: #374151;
        }


        .header p {
            margin: 5px 0 0;
            color: #6b7280;
        }


        .filters {
            margin-bottom: 20px;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #f9fafb;
        }


        .filters-title {
            margin-bottom: 8px;
            font-weight: bold;
            color: #111827;
        }


        .filter-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 7px 20px;
        }


        .filter-label {
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
            padding: 8px 7px;
            border: 1px solid #9ca3af;
            background: #f3f4f6;
            color: #374151;
            font-size: 10px;
            text-align: left;
            text-transform: uppercase;
        }


        td {
            padding: 8px 7px;
            border: 1px solid #d1d5db;
            vertical-align: top;
            font-size: 10px;
        }


        tr {
            page-break-inside: avoid;
        }


        .complaint-no {
            font-weight: bold;
            color: #1d4ed8;
        }


        .subtext {
            display: block;
            margin-top: 3px;
            color: #6b7280;
            font-size: 9px;
        }


        .urgency {
            display: inline-block;
            margin-top: 4px;
            font-weight: bold;
            font-size: 9px;
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


        .summary {
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
            padding-top: 12px;
            border-top: 1px solid #d1d5db;
        }


        .summary strong {
            color: #111827;
        }


        .print-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-bottom: 20px;
        }


        .button {
            display: inline-block;
            padding: 9px 14px;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
        }


        .print-button {
            background: #1d4ed8;
            color: white;
        }


        .close-button {
            background: #e5e7eb;
            color: #374151;
        }


        .empty {
            padding: 40px;
            border: 1px solid #d1d5db;
            text-align: center;
            color: #6b7280;
        }


        @page {
            size: landscape;
            margin: 12mm;
        }


        @media print {

            body {
                padding: 0;
            }


            .print-actions {
                display: none !important;
            }


            .report-container {
                max-width: none;
            }

        }
    </style>

</head>


<body>

    <div class="report-container">


        {{-- ========================================================= --}}
        {{-- ACTIONS --}}
        {{-- ========================================================= --}}

        <div class="print-actions">

            <button type="button" onclick="window.print()" class="button print-button">

                Print Report

            </button>


            <button type="button" onclick="window.close()" class="button close-button">

                Close

            </button>

        </div>



        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="header">

            <h1>
                SAGAY WATER DISTRICT
            </h1>

            <h2>
                Complaint Management Report
            </h2>

            <p>
                Maintenance Manager
            </p>

        </div>



        {{-- ========================================================= --}}
        {{-- APPLIED FILTERS --}}
        {{-- ========================================================= --}}

        <div class="filters">

            <div class="filters-title">
                Report Criteria
            </div>


            <div class="filter-grid">


                <div>

                    <span class="filter-label">
                        Division:
                    </span>

                    {{ $selectedDivision?->name ?? 'All Divisions' }}

                </div>


                <div>

                    <span class="filter-label">
                        Status:
                    </span>

                    {{ request('status') ?: 'All Statuses' }}

                </div>


                <div>

                    <span class="filter-label">
                        Urgency:
                    </span>

                    @if (request('urgency') === 'not_assessed')
                        Not Assessed
                    @else
                        {{ request('urgency') ?: 'All Urgency Levels' }}
                    @endif

                </div>


                <div>

                    <span class="filter-label">
                        Date From:
                    </span>

                    {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') : 'Beginning' }}

                </div>


                <div>

                    <span class="filter-label">
                        Date To:
                    </span>

                    {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') : 'Present' }}

                </div>


                <div>

                    <span class="filter-label">
                        Search:
                    </span>

                    {{ request('search') ?: 'None' }}

                </div>

            </div>

        </div>



        {{-- ========================================================= --}}
        {{-- REPORT TABLE --}}
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
                            Division
                        </th>

                        <th>
                            Complaint Type
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Responsible Unit
                        </th>

                        <th>
                            Reported
                        </th>

                        <th>
                            Completed
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

                            $divisionName = $complaint->division?->name ?? '—';

                            $isCommercial = strcasecmp(trim($divisionName), 'Commercial') === 0;

                        @endphp


                        <tr>


                            {{-- COMPLAINT --}}

                            <td>

                                <span class="complaint-no">

                                    {{ $complaint->complaint_no }}

                                </span>


                                @if ($urgency)
                                    <span class="urgency {{ $urgencyClass }}">

                                        {{ strtoupper($urgency) }}

                                    </span>
                                @else
                                    <span class="subtext">
                                        Urgency not assessed
                                    </span>
                                @endif

                            </td>



                            {{-- CONSUMER --}}

                            <td>

                                {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? '—') }}


                                @if ($complaint->consumer?->account_number)
                                    <span class="subtext">

                                        Account:
                                        {{ $complaint->consumer->account_number }}

                                    </span>
                                @endif

                            </td>



                            {{-- DIVISION --}}

                            <td>

                                {{ $divisionName }}

                            </td>



                            {{-- TYPE --}}

                            <td>

                                {{ $complaint->category?->name ?? 'Uncategorized' }}

                            </td>



                            {{-- STATUS --}}

                            <td>

                                {{ $complaint->status }}

                            </td>



                            {{-- RESPONSIBLE UNIT --}}

                            <td>

                                @if ($isCommercial)
                                    Customer Service
                                @elseif ($complaint->technicians->count())
                                    @foreach ($complaint->technicians as $technician)
                                        {{ $technician->full_name }}

                                        @if (!$loop->last)
                                            <br>
                                        @endif
                                    @endforeach
                                @else
                                    Not assigned
                                @endif

                            </td>



                            {{-- REPORTED --}}

                            <td>

                                {{ $complaint->created_at?->format('M d, Y h:i A') ?? '—' }}

                            </td>



                            {{-- COMPLETED --}}

                            <td>

                                {{ $complaint->completed_at?->format('M d, Y h:i A') ?? '—' }}

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>
        @else
            <div class="empty">

                No complaints matched the selected report criteria.

            </div>

        @endif



        {{-- ========================================================= --}}
        {{-- SUMMARY --}}
        {{-- ========================================================= --}}

        <div class="summary">

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
