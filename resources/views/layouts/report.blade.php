<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Sagay Water District Report')
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            background: #f3f4f6;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
        }

        .report-toolbar {
            width: 210mm;
            margin: 14px auto 10px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .report-button {
            appearance: none;
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #374151;
            border-radius: 6px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .report-button-primary {
            background: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
        }

        .report-paper {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 25px;
            padding: 10mm 13mm 12mm;
            background: #ffffff;
            box-shadow: 0 1px 8px rgba(0, 0, 0, 0.08);
        }

        .report-header {
            text-align: center;
        }

        .report-logo {
            display: block;
            width: 11mm;
            height: 11mm;
            object-fit: contain;
            margin: 0 auto 1.5mm;
        }

        .report-agency {
            margin: 0;
            color: #111827;
            font-size: 12.5pt;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: 0.15px;
            text-transform: uppercase;
        }

        .report-agency-address {
            margin-top: 0.8mm;
            color: #4b5563;
            font-size: 7.5pt;
            line-height: 1.2;
        }

        .report-header-rule {
            border: 0;
            border-top: 1px solid #374151;
            margin: 3mm 0 3.5mm;
        }

        .report-document-heading {
            text-align: center;
            margin-bottom: 4mm;
        }

        .report-title {
            margin: 0;
            color: #111827;
            font-size: 11.5pt;
            font-weight: 700;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .report-subtitle {
            margin: 1mm 0 0;
            color: #4b5563;
            font-size: 8pt;
        }

        .report-period {
            margin: 1mm 0 0;
            color: #111827;
            font-size: 8pt;
            font-weight: 600;
        }

        .report-content {
            width: 100%;
        }

        .report-section {
            margin-top: 5mm;
        }

        .report-section:first-child {
            margin-top: 0;
        }

        .report-section-title {
            margin: 0 0 2.5mm;
            padding-bottom: 1.2mm;
            border-bottom: 1px solid #d1d5db;
            color: #111827;
            font-size: 9pt;
            font-weight: 700;
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .report-section-heading-row {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 5mm;
            margin-bottom: 2.5mm;
            padding-bottom: 1.2mm;
            border-bottom: 1px solid #d1d5db;
        }

        .report-section-heading-row .report-section-title {
            margin: 0;
            padding: 0;
            border: 0;
        }

        .report-record-count {
            color: #4b5563;
            font-size: 8pt;
            white-space: nowrap;
        }

        .report-record-count strong {
            color: #111827;
        }

        .report-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 2.5mm 7mm;
        }

        .report-field {
            min-width: 0;
        }

        .report-field-label {
            margin-bottom: 0.7mm;
            color: #6b7280;
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
        }

        .report-field-value {
            color: #111827;
            font-size: 9pt;
            font-weight: 500;
            overflow-wrap: anywhere;
        }

        .report-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 7.5pt;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #d1d5db;
            padding: 1.8mm 1.5mm;
            vertical-align: top;
            text-align: left;
            overflow-wrap: anywhere;
            word-break: normal;
        }

        .report-table th {
            background: #f3f4f6;
            color: #374151;
            font-size: 7pt;
            font-weight: 700;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .report-table td {
            color: #111827;
            line-height: 1.3;
        }

        .report-text-block {
            border: 1px solid #d1d5db;
            padding: 2.5mm 3mm;
            color: #111827;
            font-size: 8.5pt;
            line-height: 1.45;
            white-space: pre-line;
        }

        .report-image-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 4mm;
        }

        .report-image-box {
            border: 1px solid #d1d5db;
            padding: 2.5mm;
        }

        .report-image-label {
            margin-bottom: 1.5mm;
            color: #4b5563;
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
        }

        .report-image {
            display: block;
            width: 100%;
            max-height: 55mm;
            object-fit: contain;
        }

        .report-empty-image {
            min-height: 35mm;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9fafb;
            color: #9ca3af;
            font-size: 8pt;
            text-align: center;
        }

        .report-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15mm;
            margin-top: 5mm;
            padding-top: 3mm;
            border-top: 1px solid #9ca3af;
        }

        .report-meta>div:last-child {
            text-align: right;
        }

        .report-meta-title {
            margin-bottom: 1mm;
            color: #6b7280;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
        }

        .report-meta-primary {
            color: #111827;
            font-size: 8.5pt;
            font-weight: 700;
            line-height: 1.3;
        }

        .report-meta-secondary {
            margin-top: 0.3mm;
            color: #4b5563;
            font-size: 7.5pt;
            line-height: 1.3;
        }

        .report-disclaimer {
            margin-top: 3.5mm;
            padding-top: 2.5mm;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 6.8pt;
            line-height: 1.4;
            text-align: justify;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .nowrap {
            white-space: nowrap;
        }

        .avoid-break {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .page-break {
            break-before: page;
            page-break-before: always;
        }

        @media (max-width: 900px) {

            body {
                background: #ffffff;
            }

            .report-toolbar {
                width: 100%;
                padding: 0 16px;
            }

            .report-paper {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 20px 16px;
                box-shadow: none;
            }

            .report-grid,
            .report-meta,
            .report-image-grid {
                grid-template-columns: 1fr;
            }

            .report-meta>div:last-child {
                text-align: left;
            }
        }

        @page {
            size: A4 portrait;
            margin: 9mm;
        }

        @media print {

            html,
            body {
                width: auto !important;
                min-width: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print,
            .report-toolbar {
                display: none !important;
            }

            .report-paper {
                width: 100% !important;
                max-width: none !important;
                min-height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }

            .report-header {
                text-align: center !important;
            }

            .report-logo {
                width: 10mm !important;
                height: 10mm !important;
                margin: 0 auto 1mm !important;
            }

            .report-agency {
                font-size: 11.5pt !important;
                line-height: 1.1 !important;
            }

            .report-agency-address {
                margin-top: 0.7mm !important;
                font-size: 7pt !important;
            }

            .report-header-rule {
                margin: 2.5mm 0 3mm !important;
            }

            .report-document-heading {
                margin-bottom: 3.5mm !important;
            }

            .report-title {
                font-size: 10.5pt !important;
            }

            .report-subtitle {
                font-size: 7.5pt !important;
            }

            .report-section {
                margin-top: 4mm !important;
            }

            /*
    |--------------------------------------------------------------------------
    | IMPORTANT: Force two columns in actual print
    |--------------------------------------------------------------------------
    */

            .report-grid {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                column-gap: 7mm !important;
                row-gap: 2.5mm !important;
            }

            .report-image-grid {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 4mm !important;
            }

            /*
    |--------------------------------------------------------------------------
    | Footer information
    |--------------------------------------------------------------------------
    */

            .report-meta {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 15mm !important;
                margin-top: 4mm !important;
                padding-top: 2.5mm !important;
            }

            .report-meta>div:first-child {
                text-align: left !important;
            }

            .report-meta>div:last-child {
                text-align: right !important;
            }

            /*
    |--------------------------------------------------------------------------
    | Tables
    |--------------------------------------------------------------------------
    */

            .report-table-wrapper {
                width: 100% !important;
                overflow: visible !important;
            }

            .report-table {
                width: 100% !important;
                table-layout: fixed !important;
            }

            .report-table thead {
                display: table-header-group !important;
            }

            /*
    |--------------------------------------------------------------------------
    | Page breaking
    |--------------------------------------------------------------------------
    */

            .report-field,
            .report-image-box,
            .report-meta,
            .avoid-break {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            .report-table tr {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            /*
    |--------------------------------------------------------------------------
    | Prevent responsive/mobile CSS from changing print layout
    |--------------------------------------------------------------------------
    */

            .report-grid>.report-field {
                min-width: 0 !important;
            }

            .report-image-grid>.report-image-box {
                min-width: 0 !important;
            }

            /*
    |--------------------------------------------------------------------------
    | Footer disclaimer
    |--------------------------------------------------------------------------
    */

            .report-disclaimer {
                margin-top: 3mm !important;
                padding-top: 2mm !important;
            }

            a {
                color: inherit !important;
                text-decoration: none !important;
            }
        }
    </style>

    @stack('styles')

</head>

<body>

    @php

        $reportGeneratedAt = $reportGeneratedAt ?? now();

        $reportPreparedBy = $reportPreparedBy ?? auth()->user();

        $preparedByName = trim($__env->yieldContent('prepared-by-name'));

        if (!$preparedByName) {
            $preparedByName =
                $reportPreparedBy?->full_name ??
                trim(($reportPreparedBy?->first_name ?? '') . ' ' . ($reportPreparedBy?->last_name ?? ''));
        }

        if (!$preparedByName) {
            $preparedByName = 'System User';
        }

        $preparedByRole = trim($__env->yieldContent('prepared-by-role'));

        if (!$preparedByRole) {
            $preparedByRole =
                $reportPreparedBy && method_exists($reportPreparedBy, 'getRoleNames')
                    ? $reportPreparedBy->getRoleNames()->first()
                    : null;
        }

        $preparedByRole = $preparedByRole ?: 'Authorized Personnel';
    @endphp


    <div class="report-toolbar no-print">

        @hasSection('back-url')
            <a href="@yield('back-url')" class="report-button">
                Back
            </a>
        @endif


        <button type="button" class="report-button report-button-primary" onclick="window.print()">
            Print Report
        </button>

    </div>


    <main class="report-paper">

        <header class="report-header">

            <img src="{{ asset('images/logo/logo.png') }}" alt="Sagay Water District Logo" class="report-logo">

            <h1 class="report-agency">
                Sagay Water District
            </h1>

            <div class="report-agency-address">
                Sagay City, Negros Occidental
            </div>

        </header>


        <hr class="report-header-rule">


        @hasSection('report-title')

            <section class="report-document-heading">

                <h2 class="report-title">
                    @yield('report-title')
                </h2>

                @hasSection('report-subtitle')
                    <p class="report-subtitle">
                        @yield('report-subtitle')
                    </p>
                @endif

                @hasSection('report-period')
                    <p class="report-period">
                        @yield('report-period')
                    </p>
                @endif

            </section>

        @endif


        <div class="report-content">

            @yield('report-content')

        </div>


        <section class="report-meta avoid-break">

            <div>

                <div class="report-meta-title">
                    Report Generated
                </div>

                <div class="report-meta-primary">
                    {{ $reportGeneratedAt->format('F d, Y') }}
                </div>

                <div class="report-meta-secondary">
                    {{ $reportGeneratedAt->format('h:i A') }}
                </div>

            </div>


            <div>

                <div class="report-meta-title">
                    Prepared By
                </div>

                <div class="report-meta-primary">
                    {{ $preparedByName }}
                </div>

                <div class="report-meta-secondary">
                    {{ $preparedByRole }}
                </div>

            </div>

        </section>


        <footer>

            <div class="report-disclaimer">
                This is a system-generated report of Sagay Water District.
                The information presented is based on records maintained
                in the complaint and maintenance management system.
            </div>

        </footer>

    </main>


    @stack('scripts')

</body>

</html>
