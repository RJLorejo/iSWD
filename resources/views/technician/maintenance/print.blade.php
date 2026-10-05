<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Service Accomplishment Report - {{ $complaint->complaint_no }}
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #1f2937;
            margin: 36px;
            line-height: 1.5;
            font-size: 14px;
        }

        .actions {
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
            text-align: center;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 18px;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 23px;
            letter-spacing: 0.5px;
        }

        .header p {
            margin: 6px 0 0;
            color: #6b7280;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 7px;
            margin-top: 26px;
            margin-bottom: 15px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 25px;
        }

        .field {
            margin-bottom: 16px;
        }

        .label {
            font-weight: 700;
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .value {
            white-space: pre-line;
            margin-top: 5px;
            color: #111827;
        }

        .box {
            margin-top: 6px;
            padding: 12px 14px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            min-height: 55px;
            white-space: pre-line;
        }

        .photos {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .photo-box {
            break-inside: avoid;
        }

        .photos img {
            width: 100%;
            max-height: 320px;
            object-fit: contain;
            border: 1px solid #d1d5db;
            margin-top: 8px;
        }

        .no-photo {
            height: 180px;
            margin-top: 8px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border: 1px solid #d1d5db;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .footer {
            margin-top: 35px;
            border-top: 1px solid #d1d5db;
            padding-top: 15px;
            font-size: 12px;
            color: #4b5563;
        }

        .footer p {
            margin: 6px 0;
        }

        @media print {

            body {
                margin: 18px;
            }

            .no-print {
                display: none !important;
            }

            .section-title,
            .field,
            .photo-box,
            .footer {
                break-inside: avoid;
            }
        }

        @media (max-width: 700px) {

            .grid,
            .photos {
                grid-template-columns: 1fr;
            }
        }
    </style>

</head>

<body>

    @php
        $report = $complaint->maintenanceReport;

        $displayStatus = $complaint->status === 'Completed' ? 'Accomplished' : $complaint->status;
    @endphp


    <div class="actions no-print">

        <button type="button" onclick="window.print()" class="print-button">
            Print Report
        </button>

    </div>


    <div class="header">

        <h1>
            SERVICE ACCOMPLISHMENT REPORT
        </h1>

        <p>
            Sagay Water District
        </p>

    </div>


    <div class="section-title">
        Complaint Information
    </div>


    <div class="grid">

        <div class="field">

            <div class="label">
                Complaint Number
            </div>

            <div class="value">
                {{ $complaint->complaint_no }}
            </div>

        </div>


        <div class="field">

            <div class="label">
                Status
            </div>

            <div class="value">

                <span class="status">
                    {{ $displayStatus }}
                </span>

            </div>

        </div>


        <div class="field">

            <div class="label">
                Division
            </div>

            <div class="value">
                {{ $complaint->division?->name ?? 'N/A' }}
            </div>

        </div>


        <div class="field">

            <div class="label">
                Complaint Type
            </div>

            <div class="value">
                {{ $complaint->category?->name ?? 'N/A' }}
            </div>

        </div>

    </div>


    <div class="field">

        <div class="label">
            Consumer
        </div>

        <div class="value">
            {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'N/A') }}
        </div>

    </div>


    <div class="field">

        <div class="label">
            Service Location
        </div>

        <div class="value">
            {{ $complaint->address ?: 'N/A' }}
        </div>

    </div>


    @if ($complaint->landmark)
        <div class="field">

            <div class="label">
                Landmark
            </div>

            <div class="value">
                {{ $complaint->landmark }}
            </div>

        </div>
    @endif


    <div class="section-title">
        Diagnosis / Findings
    </div>


    <div class="field">

        <div class="box">
            {{ $report->diagnosis ?: 'No diagnosis or findings recorded.' }}
        </div>

    </div>


    <div class="section-title">
        Root Cause
    </div>


    <div class="field">

        <div class="box">
            {{ $report->root_cause ?: 'No root cause recorded.' }}
        </div>

    </div>


    <div class="section-title">
        Materials / Parts
    </div>


    <div class="field">

        <div class="box">
            {{ $report->materials_parts ?: 'None recorded.' }}
        </div>

    </div>


    <div class="section-title">
        Plumber Notes
    </div>


    <div class="field">

        <div class="box">
            {{ $report->technician_notes ?: 'No additional notes.' }}
        </div>

    </div>


    <div class="section-title">
        Before & After Photos
    </div>


    <div class="photos">


        <div class="photo-box">

            <div class="label">
                Before Maintenance
            </div>

            @if ($report->before_photo)
                <img src="{{ asset('storage/' . $report->before_photo) }}" alt="Before maintenance photo">
            @else
                <div class="no-photo">
                    No before photo available
                </div>
            @endif

        </div>


        <div class="photo-box">

            <div class="label">
                After Maintenance
            </div>

            @if ($report->after_photo)
                <img src="{{ asset('storage/' . $report->after_photo) }}" alt="After maintenance photo">
            @else
                <div class="no-photo">
                    No after photo available
                </div>
            @endif

        </div>


    </div>


    <div class="section-title">
        Report Information
    </div>


    <div class="grid">

        <div class="field">

            <div class="label">
                Report Submitted By
            </div>

            <div class="value">
                {{ $report->technician?->full_name ?? 'N/A' }}
            </div>

        </div>


        <div class="field">

            <div class="label">
                Review Status
            </div>

            <div class="value">
                {{ $report->review_status ?? 'N/A' }}
            </div>

        </div>


        <div class="field">

            <div class="label">
                Maintenance Started
            </div>

            <div class="value">
                {{ $report->started_at?->format('M d, Y h:i A') ?? 'N/A' }}
            </div>

        </div>


        <div class="field">

            <div class="label">
                Report Submitted
            </div>

            <div class="value">
                {{ $report->submitted_at?->format('M d, Y h:i A') ?? 'N/A' }}
            </div>

        </div>

    </div>


    @if ($report->reviewed_at || $report->review_remarks)

        <div class="section-title">
            Management Review
        </div>


        <div class="grid">

            @if ($report->reviewed_at)
                <div class="field">

                    <div class="label">
                        Reviewed
                    </div>

                    <div class="value">
                        {{ $report->reviewed_at->format('M d, Y h:i A') }}
                    </div>

                </div>
            @endif


            @if ($report->review_status)
                <div class="field">

                    <div class="label">
                        Review Result
                    </div>

                    <div class="value">
                        {{ $report->review_status }}
                    </div>

                </div>
            @endif

        </div>


        @if ($report->review_remarks)
            <div class="field">

                <div class="label">
                    Manager Remarks
                </div>

                <div class="box">
                    {{ $report->review_remarks }}
                </div>

            </div>
        @endif

    @endif


    <div class="footer">

        <p>
            <strong>Maintenance Team:</strong>
            {{ $complaint->technicians->pluck('full_name')->join(', ') ?: 'N/A' }}
        </p>

        <p>
            <strong>Report Submitted By:</strong>
            {{ $report->technician?->full_name ?? 'N/A' }}
        </p>

        <p>
            <strong>Started:</strong>
            {{ $report->started_at?->format('M d, Y h:i A') ?? 'N/A' }}
        </p>

        <p>
            <strong>Submitted:</strong>
            {{ $report->submitted_at?->format('M d, Y h:i A') ?? 'N/A' }}
        </p>

    </div>


</body>

</html>
