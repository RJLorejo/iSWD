<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Maintenance Insights Report — Sagay Water District</title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#eef2f7;color:#111827;font:10pt/1.4 Arial,Helvetica,sans-serif}
        .toolbar{max-width:1000px;margin:16px auto;display:flex;justify-content:space-between;align-items:center;gap:12px;padding:0 12px}
        button,.back{border:0;border-radius:9px;background:#0369a1;color:#fff;padding:11px 18px;text-decoration:none;font-weight:700;cursor:pointer}
        .paper{width:min(100%,210mm);min-height:297mm;margin:0 auto 24px;padding:12mm 13mm;background:white;box-shadow:0 2px 15px #0002}
        .header{text-align:center}.logo{width:11mm;height:11mm;object-fit:contain;display:block;margin:0 auto 2mm}
        .agency{font-size:13pt;font-weight:700;text-transform:uppercase;margin:0}.address{font-size:8pt;color:#475569}
        hr{border:0;border-top:1px solid #334155;margin:3mm 0}.title{text-align:center;text-transform:uppercase;font-weight:700;font-size:11pt;margin:3mm 0 1mm}
        .subtitle{text-align:center;font-size:8pt;color:#475569;margin:0 0 5mm}
        h2{font-size:9pt;text-transform:uppercase;border-bottom:1px solid #cbd5e1;padding-bottom:1mm;margin:6mm 0 2mm}
        table{width:100%;border-collapse:collapse;font-size:7.4pt;table-layout:fixed}
        th,td{border:1px solid #cbd5e1;padding:1.5mm;vertical-align:top;text-align:left;overflow-wrap:anywhere}
        th{background:#f1f5f9;text-transform:uppercase;font-size:7pt}thead{display:table-header-group}tr{break-inside:avoid}
        .metrics{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:2mm}
        .metric{border:1px solid #cbd5e1;padding:3mm}.metric span{display:block;font-size:7pt;color:#64748b}.metric strong{font-size:13pt}
        .meta{display:flex;justify-content:space-between;gap:20px;margin-top:6mm;padding-top:3mm;border-top:1px solid #94a3b8;font-size:8pt}
        .muted{color:#64748b}.disclaimer{margin-top:4mm;padding-top:2mm;border-top:1px solid #e2e8f0;color:#64748b;font-size:7pt}
        @page{size:A4 portrait;margin:10mm}
        @media print{body{background:white}.toolbar{display:none}.paper{width:auto;min-height:0;margin:0;padding:0;box-shadow:none}th{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
        @media(max-width:650px){.paper{padding:15px}.metrics{grid-template-columns:repeat(2,minmax(0,1fr))}.toolbar{flex-wrap:wrap}}
    </style>
</head>
<body>
<div class="toolbar"><a class="back" href="{{ route('maintenance-manager.insights.index', request()->only(['date_from','date_to','status'])) }}">← Back to insights</a><button onclick="window.print()">Print / Save PDF</button></div>
<main class="paper">
    <header class="header">
        <img class="logo" src="{{ asset('images/logo/logo.png') }}" alt="Sagay Water District logo">
        <h1 class="agency">Sagay Water District</h1><div class="address">Sagay City, Negros Occidental</div>
    </header><hr>
    <h1 class="title">Maintenance Insights & Performance Report</h1>
    <p class="subtitle">Scheduled work dates: {{ $from->format('F d, Y') }} to {{ $to->format('F d, Y') }} · Status filter: {{ $status === 'all' ? 'All' : $status }}</p>
    <h2>Maintenance Summary</h2>
    <div class="metrics">
        <div class="metric"><span>Total visits</span><strong>{{ number_format($total) }}</strong></div>
        <div class="metric"><span>Completed visits</span><strong>{{ number_format($completed->count()) }}</strong></div>
        <div class="metric"><span>Completion rate</span><strong>{{ $eligible ? number_format($completed->count() / $eligible * 100, 1) . '%' : 'N/A' }}</strong></div>
        <div class="metric"><span>Pending workload</span><strong>{{ number_format($pending->count()) }}</strong></div>
        <div class="metric"><span>Confirmed missed</span><strong>{{ number_format($missedConfirmed->count()) }}</strong></div>
        <div class="metric"><span>Avg. duration ({{ $timedCount }} timed visits)</span><strong>{{ ($averageMinutes === null ? 'N/A' : ($averageMinutes < 60 ? number_format($averageMinutes, 1) . ' mins' : number_format($averageMinutes / 60, 2) . ' hrs')) }}</strong></div>
        <div class="metric"><span>Total recorded work time</span><strong>{{ ($totalMinutes === null ? 'N/A' : ($totalMinutes < 60 ? number_format($totalMinutes, 1) . ' mins' : number_format($totalMinutes / 60, 2) . ' hrs')) }}</strong></div>
    </div>
    @if ($monthlyChartAvailable && count($monthly))
        <h2>Monthly Trends</h2>
        <table><thead><tr><th>Month</th><th>Total</th><th>Completed</th><th>Pending</th><th>Confirmed Missed</th></tr></thead><tbody>
        @foreach ($monthly as $month)
            <tr><td>{{ $month['label'] }}</td><td>{{ $month['total'] }}</td><td>{{ $month['completed'] }}</td><td>{{ $month['pending'] }}</td><td>{{ $month['missed'] }}</td></tr>
        @endforeach
        </tbody></table>
    @endif
    <h2>Plumber Assignment Summary</h2>
    <table><thead><tr><th>Plumber</th><th>Assigned visits</th><th>Completed visits</th><th>Pending visits</th></tr></thead><tbody>
        @forelse ($plumbers as $plumber)
            <tr><td>{{ $plumber['name'] }}</td><td>{{ $plumber['assigned'] }}</td><td>{{ $plumber['completed'] }}</td><td>{{ $plumber['pending'] }}</td></tr>
        @empty<tr><td colspan="4">No assignments recorded in the selected period.</td></tr>@endforelse
    </tbody></table>
    <h2>Detailed Maintenance Work Orders ({{ $total }})</h2>
    <table><thead><tr><th style="width:17%">Work Date</th><th style="width:18%">Complaint / Visit</th><th style="width:24%">Plumber(s)</th><th style="width:17%">Status</th><th style="width:24%">Reason / Remarks</th></tr></thead><tbody>
        @forelse ($orders as $order)
            <tr>
                <td>{{ $order->schedule?->schedule_date?->format('M d, Y') ?? '—' }}</td>
                <td>{{ $order->complaint?->complaint_no ?? '#' . $order->complaint_id }}<br>Visit #{{ $order->visit_number }} / WO #{{ $order->id }}</td>
                <td>{{ $order->technicians->pluck('full_name')->filter()->join(', ') ?: 'Not recorded' }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->pending_reason ?: ($order->missed_manager_reason ?: ($order->manager_remarks ?: '—')) }}</td>
            </tr>
        @empty<tr><td colspan="5">No records match these filters.</td></tr>@endforelse
    </tbody></table>
    <div class="meta">
        <div><strong>Report Generated</strong><br>{{ now('Asia/Manila')->format('F d, Y h:i A') }}</div>
        <div style="text-align:right"><strong>Prepared By</strong><br>{{ auth()->user()?->full_name ?? auth()->user()?->email ?? 'Maintenance Manager' }}<br><span class="muted">Maintenance Manager</span></div>
    </div>
    <footer class="disclaimer">This is a system-generated report of Sagay Water District. Information is based on records maintained in the complaint and maintenance management system. Work duration is elapsed time between recorded start and completion, not summed staff-hours. Missing timestamps are excluded. Metrics represent current work-order statuses for the selected scheduled-date period, not historical snapshots. Only approved work orders on finalized schedules are included. Cancelled visits are excluded from the completion-rate denominator.</footer>
</main>
</body>
</html>
