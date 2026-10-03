<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Consumer Report</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 28px;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            background: #ffffff;
            font-size: 12px;
        }

        .report {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }

        .header h2 {
            margin: 5px 0 0;
            font-size: 15px;
            font-weight: 600;
        }

        .header p {
            margin: 6px 0 0;
            color: #6b7280;
            font-size: 11px;
        }

        .filter-summary {
            margin-bottom: 16px;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .filter-summary strong {
            color: #374151;
        }

        .filter-item {
            display: inline-block;
            margin-right: 20px;
        }

        .record-count {
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 8px;
            border: 1px solid #d1d5db;
            background: #f3f4f6;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }

        td {
            padding: 8px;
            border: 1px solid #d1d5db;
            vertical-align: top;
            font-size: 11px;
        }

        .consumer-name {
            font-weight: 600;
        }

        .secondary {
            margin-top: 3px;
            color: #6b7280;
            font-size: 10px;
        }

        .status {
            font-weight: 600;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
            padding-top: 12px;
            border-top: 1px solid #d1d5db;
        }


        .footer strong {
            color: #111827;
        }

        .no-records {
            padding: 30px;
            text-align: center;
            color: #6b7280;
        }

        .print-actions {
            margin-bottom: 18px;
            text-align: right;
        }

        .print-button {
            border: 0;
            border-radius: 6px;
            background: #2563eb;
            color: white;
            padding: 9px 14px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }


        .close-button {
            border: 0;
            border-radius: 6px;
            background: #e5e7eb;
            color: #374151;
            padding: 9px 14px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }


        @media print {

            body {
                padding: 0;
            }

            .print-actions {
                display: none;
            }

            @page {
                size: landscape;
                margin: 12mm;
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

    <div class="report">

        {{-- Print Button --}}
        <div class="print-actions">

            <button type="button" class="print-button" onclick="window.print()">
                Print Report
            </button>

            <button type="button" onclick="window.close()" class="button close-button">

                Close

            </button>

        </div>


        {{-- Header --}}
        <div class="header">

            <h1>
                Sagay Water District
            </h1>

            <h2>
                Consumer Report
            </h2>
            <p>
                Customer Service Assistant
            </p>
        </div>


        {{-- Applied Filters --}}
        @if (request()->filled('search') || request()->filled('status'))

            <div class="filter-summary">

                <strong>Applied Filters:</strong>

                @if (request()->filled('search'))
                    <span class="filter-item">

                        Search:
                        <strong>
                            {{ request('search') }}
                        </strong>

                    </span>
                @endif


                @if (request()->filled('status'))
                    <span class="filter-item">

                        Status:

                        <strong>
                            {{ request('status') === '1' ? 'Active' : 'Inactive' }}
                        </strong>

                    </span>
                @endif

            </div>

        @endif


        {{-- Count --}}
        <div class="record-count">

            Total Records:
            {{ number_format($consumers->count()) }}

        </div>


        {{-- Table --}}
        <table>

            <thead>

                <tr>

                    <th>
                        Consumer
                    </th>

                    <th>
                        Account Number
                    </th>

                    <th>
                        Contact
                    </th>

                    <th>
                        Service Address
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Registered
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse ($consumers as $consumer)
                    <tr>

                        <td>

                            <div class="consumer-name">
                                {{ $consumer->full_name }}
                            </div>

                            @if ($consumer->email)
                                <div class="secondary">
                                    {{ $consumer->email }}
                                </div>
                            @endif


                            <div class="secondary">

                                @if ($consumer->user && $consumer->user->is_active)
                                    Portal: Active
                                @elseif ($consumer->user)
                                    Portal: Inactive
                                @else
                                    Portal: Missing
                                @endif

                            </div>

                        </td>


                        <td>
                            {{ $consumer->account_number }}
                        </td>


                        {{-- Contact --}}
                        <td>
                            {{ $consumer->phone ?: '—' }}
                        </td>


                        {{-- Service Address --}}
                        <td>

                            @if ($consumer->address)
                                {{ collect([
                                    $consumer->address->house_no,
                                    $consumer->address->street,
                                    $consumer->address->purok,
                                    $consumer->address->barangay,
                                    $consumer->address->municipality,
                                    $consumer->address->province,
                                ])->filter()->implode(', ') ?:
                                    '—' }}
                            @else
                                —
                            @endif

                        </td>

                        {{-- Consumer Status --}}
                        <td>

                            <span class="status">

                                {{ $consumer->is_active ? 'Active' : 'Inactive' }}

                            </span>

                        </td>


                        {{-- Registered --}}
                        <td>

                            {{ optional($consumer->created_at)->format('M d, Y') }}

                            <div class="secondary">

                                {{ optional($consumer->created_at)->format('h:i A') }}

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="no-records">
                            No consumers found for the selected filters.
                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>


        <div class="footer col-2">

            <p>
                <strong>
                    Total Records:
                </strong>

                {{ $consumer->count() }}
            </p>


            <p>
                <strong>Generated:</strong>
                {{ now()->format('F d, Y h:i A') }}
            </p>

        </div>

    </div>

</body>

</html>
