{{-- Shared print template matching resources/views/layouts/report.blade.php --}}
<script>
function openMaintenanceReport({title, subtitle = '', period = '', tableHtml = '', summary = ''}) {
    const escapeReport = value => String(value ?? '').replace(/&/g, '&amp;')
        .replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    const logo = @js(asset('images/logo/logo.png'));
    const preparedBy = @js(auth()->user()?->full_name ?? auth()->user()?->email ?? 'Maintenance Manager');
    const generated = @js(now('Asia/Manila')->format('F d, Y h:i A'));
    const popup = window.open('', '_blank', 'width=1000,height=750');
    if (!popup) { alert('Please allow pop-ups to print this report.'); return; }
    const html = `<!doctype html><html lang="en"><head><meta charset="utf-8">
        <title>${escapeReport(title)} - Sagay Water District</title>
        <style>
        * {box-sizing:border-box} html,body{margin:0;padding:0}
        body{background:#f3f4f6;color:#111827;font:10pt/1.4 Arial,Helvetica,sans-serif}
        .report-paper{width:210mm;min-height:297mm;margin:20px auto;padding:10mm 13mm 12mm;background:#fff;box-shadow:0 1px 8px #0002}
        .report-header{text-align:center}
        .report-logo{display:block;width:11mm;height:11mm;object-fit:contain;margin:0 auto 1.5mm}
        .report-agency{margin:0;color:#111827;font-size:12.5pt;font-weight:700;line-height:1.1;letter-spacing:.15px;text-transform:uppercase}
        .report-agency-address{margin-top:.8mm;color:#4b5563;font-size:7.5pt;line-height:1.2}
        .report-header-rule{border:0;border-top:1px solid #374151;margin:3mm 0 3.5mm}
        .report-document-heading{text-align:center;margin-bottom:4mm}
        .report-title{margin:0;color:#111827;font-size:11.5pt;font-weight:700;line-height:1.2;text-transform:uppercase}
        .report-subtitle{margin:1mm 0 0;color:#4b5563;font-size:8pt}
        .report-period{margin:1mm 0 0;color:#111827;font-size:8pt;font-weight:600}
        .report-section{margin-top:5mm}
        .report-section-title{margin:0 0 2.5mm;padding-bottom:1.2mm;border-bottom:1px solid #d1d5db;color:#111827;font-size:9pt;font-weight:700;text-transform:uppercase;letter-spacing:.2px}
        .report-table{width:100%;border-collapse:collapse;table-layout:fixed;font-size:7.5pt}
        .report-table th,.report-table td{border:1px solid #d1d5db;padding:1.8mm 1.5mm;vertical-align:top;text-align:left;overflow-wrap:anywhere}
        .report-table th{background:#f3f4f6;color:#374151;font-size:7pt;font-weight:700;line-height:1.2;text-transform:uppercase}
        .report-table td{color:#111827;line-height:1.3}
        .report-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15mm;margin-top:5mm;padding-top:3mm;border-top:1px solid #9ca3af;break-inside:avoid}
        .report-meta>div:last-child{text-align:right}
        .report-meta-title{margin-bottom:1mm;color:#6b7280;font-size:7pt;font-weight:700;text-transform:uppercase}
        .report-meta-primary{color:#111827;font-size:8.5pt;font-weight:700;line-height:1.3}
        .report-meta-secondary{margin-top:.3mm;color:#4b5563;font-size:7.5pt;line-height:1.3}
        .report-disclaimer{margin-top:3.5mm;padding-top:2.5mm;border-top:1px solid #e5e7eb;color:#6b7280;font-size:6.8pt;line-height:1.4;text-align:justify}
        .report-summary{margin-top:3mm;color:#4b5563;font-size:7.5pt}
        .report-table thead{display:table-header-group}
        .report-table tr{break-inside:avoid;page-break-inside:avoid}
        @page{size:A4 portrait;margin:9mm}
        @media print{
            html,body{width:auto!important;margin:0!important;padding:0!important;background:#fff!important;-webkit-print-color-adjust:exact;print-color-adjust:exact}
            .report-paper{width:100%!important;min-height:0!important;margin:0!important;padding:0!important;box-shadow:none!important}
            .report-logo{width:10mm!important;height:10mm!important;margin:0 auto 1mm!important}
            .report-agency{font-size:11.5pt!important}.report-agency-address{font-size:7pt!important}
            .report-header-rule{margin:2.5mm 0 3mm!important}.report-document-heading{margin-bottom:3.5mm!important}
            .report-title{font-size:10.5pt!important}.report-subtitle{font-size:7.5pt!important}
            .report-section{margin-top:4mm!important}.report-meta{margin-top:4mm!important;padding-top:2.5mm!important}
            .report-disclaimer{margin-top:3mm!important;padding-top:2mm!important}
        }
        </style></head><body><main class="report-paper">
        <header class="report-header"><img class="report-logo" src="${escapeReport(logo)}" alt="Sagay Water District Logo">
        <h1 class="report-agency">Sagay Water District</h1>
        <div class="report-agency-address">Sagay City, Negros Occidental</div></header>
        <hr class="report-header-rule">
        <section class="report-document-heading"><h2 class="report-title">${escapeReport(title)}</h2>
        <p class="report-subtitle">${escapeReport(subtitle)}</p><p class="report-period">${escapeReport(period)}</p></section>
        <div class="report-content"><section class="report-section">
        <h3 class="report-section-title">Maintenance Work Orders</h3>
        <table class="report-table">${tableHtml}</table>
        <div class="report-summary">${escapeReport(summary)}</div></section></div>
        <section class="report-meta"><div><div class="report-meta-title">Report Generated</div>
        <div class="report-meta-primary">${escapeReport(generated.split(' ').slice(0,3).join(' '))}</div>
        <div class="report-meta-secondary">${escapeReport(generated)}</div></div>
        <div><div class="report-meta-title">Prepared By</div>
        <div class="report-meta-primary">${escapeReport(preparedBy)}</div>
        <div class="report-meta-secondary">Maintenance Manager</div></div></section>
        <footer><div class="report-disclaimer">This is a system-generated report of Sagay Water District.
        The information presented is based on records maintained in the complaint and maintenance management system.</div></footer>
        </main></body></html>`;
    popup.document.open(); popup.document.write(html); popup.document.close();
    popup.onload = () => { popup.focus(); popup.print(); };
}
</script>
