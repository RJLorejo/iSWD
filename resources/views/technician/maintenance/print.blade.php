<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>
        Maintenance Report - {{ $complaint->complaint_no }}
    </title>

    <style>
        body {
            font-family: Arial, sans-serif;
            color: #222;
            margin: 40px;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }

        h2 {
            font-size: 17px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 6px;
            margin-top: 25px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .field {
            margin-bottom: 15px;
        }

        .label {
            font-weight: bold;
            font-size: 13px;
            color: #555;
        }

        .value {
            white-space: pre-line;
            margin-top: 4px;
        }

        .photos {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .photos img {
            width: 100%;
            max-height: 350px;
            object-fit: contain;
            border: 1px solid #ddd;
        }

        .footer {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            font-size: 12px;
            color: #666;
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
            MAINTENANCE REPORT
        </h1>

        <p>
            {{ $complaint->complaint_no }}
        </p>

    </div>


    <h2>Complaint Information</h2>

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
                Priority
            </div>

            <div class="value">
                {{ $complaint->priority }}
            </div>

        </div>


        <div class="field">

            <div class="label">
                Subject
            </div>

            <div class="value">
                {{ $complaint->subject }}
            </div>

        </div>


        <div class="field">

            <div class="label">
                Category
            </div>

            <div class="value">
                {{ $complaint->category?->name ?? 'N/A' }}
            </div>

        </div>

    </div>


    <div class="field">

        <div class="label">
            Location
        </div>

        <div class="value">
            {{ $complaint->address }}
        </div>

    </div>


    <h2>Diagnosis & Root Cause</h2>

    <div class="field">

        <div class="label">
            Diagnosis
        </div>

        <div class="value">
            {{ $complaint->maintenanceReport->diagnosis }}
        </div>

    </div>


    <div class="field">

        <div class="label">
            Root Cause
        </div>

        <div class="value">
            {{ $complaint->maintenanceReport->root_cause }}
        </div>

    </div>


    <h2>Work & Repair</h2>

    <div class="field">

        <div class="label">
            Work Performed
        </div>

        <div class="value">
            {{ $complaint->maintenanceReport->work_performed }}
        </div>

    </div>


    <div class="field">

        <div class="label">
            Repair Procedure
        </div>

        <div class="value">
            {{ $complaint->maintenanceReport->repair_procedure }}
        </div>

    </div>


    <h2>Materials, Parts & Tools</h2>

    <div class="grid">

        <div class="field">

            <div class="label">
                Materials Used
            </div>

            <div class="value">
                {{ $complaint->maintenanceReport->materials_used ?: 'None recorded' }}
            </div>

        </div>


        <div class="field">

            <div class="label">
                Parts Replaced
            </div>

            <div class="value">
                {{ $complaint->maintenanceReport->parts_replaced ?: 'None recorded' }}
            </div>

        </div>

    </div>


    <div class="field">

        <div class="label">
            Tools Used
        </div>

        <div class="value">
            {{ $complaint->maintenanceReport->tools_used ?: 'None recorded' }}
        </div>

    </div>


    <h2>Technician Notes & Completion</h2>

    <div class="field">

        <div class="label">
            Technician Notes
        </div>

        <div class="value">
            {{ $complaint->maintenanceReport->technician_notes ?: 'None recorded' }}
        </div>

    </div>


    <div class="field">

        <div class="label">
            Completion Remarks
        </div>

        <div class="value">
            {{ $complaint->maintenanceReport->completion_remarks }}
        </div>

    </div>


    <h2>Before & After Photos</h2>

    <div class="photos">

        <div>

            <div class="label">
                Before Maintenance
            </div>

            @if ($complaint->maintenanceReport->before_photo)
                <img src="{{ asset('storage/' . $complaint->maintenanceReport->before_photo) }}">
            @else
                <p>No photo available.</p>
            @endif

        </div>


        <div>

            <div class="label">
                After Maintenance
            </div>

            @if ($complaint->maintenanceReport->after_photo)
                <img src="{{ asset('storage/' . $complaint->maintenanceReport->after_photo) }}">
            @else
                <p>No photo available.</p>
            @endif

        </div>

    </div>


    <div class="footer">

        <p>
            Technician:
            {{ $complaint->maintenanceReport->technician?->full_name ?? 'N/A' }}
        </p>

        <p>
            Started:
            {{ $complaint->maintenanceReport->started_at?->format('M d, Y h:i A') ?? 'N/A' }}
        </p>

        <p>
            Submitted:
            {{ $complaint->maintenanceReport->submitted_at?->format('M d, Y h:i A') ?? 'N/A' }}
        </p>

    </div>


</body>

</html>
