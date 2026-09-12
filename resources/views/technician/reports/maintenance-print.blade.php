<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Technician Maintenance Report
    </title>

    <style>
        body {
            font-family: Arial, sans-serif;
            color: #111827;
            margin: 40px;
            font-size: 13px;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }

        h2 {
            margin-top: 30px;
            font-size: 17px;
        }

        .header {
            border-bottom: 2px solid #111827;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .muted {
            color: #6b7280;
        }

        .summary {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }

        .summary-box {
            border: 1px solid #d1d5db;
            padding: 15px;
            flex: 1;
        }

        .summary-label {
            color: #6b7280;
            font-size: 12px;
        }

        .summary-value {
            font-size: 22px;
            font-weight: bold;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 9px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #d1d5db;
            font-size: 11px;
            color: #6b7280;
        }

        @media print {

            body {
                margin: 20px;
            }

            .no-print {
                display: none;
            }

        }
    </style>

</head>

<body>


    <div class="no-print" style="margin-bottom:20px;">

        <button onclick="window.print()"
            style="
            padding:10px 18px;
            background:#2563eb;
            color:white;
            border:0;
            border-radius:6px;
            cursor:pointer;
        ">
            Print
        </button>

    </div>

    <div class="header">

        <h1>
            Technician Maintenance Report
        </h1>

        <p class="font-semibold">
            {{ auth()->user()->full_name }}
        </p>


        <p class="muted">
            iSWD — Sagay Water District
        </p>

        <p class="muted">
            Reporting Period:
            {{ $from->format('M d, Y') }}
            —
            {{ $to->format('M d, Y') }}
        </p>

    </div>


    <div class="summary">

        <div class="summary-box">

            <div class="summary-label">
                Completed Maintenance
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


    <h2>
        Completed Maintenance Work
    </h2>


    <table>

        <thead>

            <tr>

                <th>
                    Complaint No.
                </th>

                <th>
                    Subject
                </th>

                <th>
                    Category
                </th>

                <th>
                    Consumer
                </th>

                <th>
                    Priority
                </th>

                <th>
                    Completed
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($complaints as $complaint)
                <tr>

                    <td>
                        {{ $complaint->complaint_no }}

                        <div class="text-xs text-gray-400 mt-1">

                            <i class="far fa-clock mr-1"></i>

                            {{ $complaint->created_at?->format('M d, Y h:i A') }}

                        </div>
                    </td>

                    <td>
                        {{ $complaint->subject }}
                    </td>

                    <td>
                        {{ $complaint->category?->name ?? '—' }}
                    </td>

                    <td>
                        @if ($complaint->consumer)
                            <div class="font-medium text-gray-900">
                                {{ $complaint->consumer->full_name }}
                            </div>

                            <div class="text-xs text-gray-500">
                                {{ $complaint->consumer->consumer_no }}
                            </div>
                        @elseif ($complaint->complainant_name)
                            <div class="font-medium text-gray-900">
                                {{ $complaint->complainant_name }}
                            </div>

                            @if ($complaint->complainant_phone)
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-phone mr-1"></i>
                                    {{ $complaint->complainant_phone }}
                                </div>
                            @endif
                        @else
                            <span class="text-gray-400">
                                No complainant information
                            </span>
                        @endif
                        <div class="text-xs text-gray-500 mt-1">

                            {{ $complaint->address }}

                        </div>

                    </td>

                    <td>
                        {{ $complaint->priority }}
                    </td>

                    <td>
                        {{ $complaint->completed_at?->format('M d, Y h:i A') }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6" style="text-align:center;">
                        No completed maintenance work found.
                    </td>

                </tr>
            @endforelse

        </tbody>

    </table>


    <div class="footer">

        Generated by iSWD Technician Maintenance Reporting.

    </div>


    <script>
        window.onload = function() {
            window.print();
        };
    </script>

</body>

</html>
