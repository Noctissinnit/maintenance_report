<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Head Dashboard Report</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,300&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #0f1923;
            --ink-light: #3a4a5c;
            --ink-muted: #8899aa;
            --cream: #f7f5f0;
            --cream-dark: #edeae2;
            --accent: #c8511b;
            --accent-light: #f0864a;
            --accent-muted: #f5dece;
            --blue: #1d4a8a;
            --blue-light: #4472c4;
            --blue-muted: #dce8f8;
            --green: #1a7a4a;
            --green-muted: #d4eddf;
            --amber: #b85c00;
            --amber-muted: #faecd8;
            --red: #9b1c1c;
            --red-muted: #fde8e8;
            --border: #d8d3c8;
            --shadow: 0 2px 20px rgba(15,25,35,0.08);
            --shadow-md: 0 4px 40px rgba(15,25,35,0.12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--ink);
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ─── PAGE WRAPPER ─────────────────────────── */
        .page {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            box-shadow: var(--shadow-md);
        }

        /* ─── MASTHEAD ──────────────────────────────── */
        .masthead {
            background: var(--ink);
            color: white;
            padding: 0;
            position: relative;
            overflow: hidden;
        }

        .masthead-bg {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 80% at 90% 20%, rgba(200,81,27,0.18) 0%, transparent 70%),
                radial-gradient(ellipse 40% 60% at 10% 80%, rgba(29,74,138,0.15) 0%, transparent 60%);
        }

        .masthead-rule {
            height: 4px;
            background: linear-gradient(90deg, var(--accent) 0%, var(--accent-light) 40%, var(--blue-light) 70%, var(--blue) 100%);
        }

        .masthead-inner {
            position: relative;
            z-index: 1;
            padding: 44px 48px 36px;
        }

        .masthead-eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .eyebrow-line {
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.15);
        }

        .eyebrow-text {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.5);
        }

        .masthead-title {
            font-family: 'DM Serif Display', serif;
            font-size: 42px;
            line-height: 1.05;
            letter-spacing: -1px;
            margin-bottom: 6px;
        }

        .masthead-title em {
            font-style: italic;
            color: var(--accent-light);
        }

        .masthead-subtitle {
            font-size: 13px;
            font-weight: 300;
            color: rgba(255,255,255,0.55);
            letter-spacing: 0.5px;
            margin-bottom: 32px;
        }

        .masthead-meta {
            display: flex;
            gap: 0;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
        }

        .meta-block {
            flex: 1;
            padding-right: 24px;
        }

        .meta-block + .meta-block {
            border-left: 1px solid rgba(255,255,255,0.1);
            padding-left: 24px;
        }

        .meta-block .label {
            font-size: 8px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.35);
            margin-bottom: 4px;
        }

        .meta-block .value {
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,0.9);
        }

        /* ─── FILTER BAR ────────────────────────────── */
        .filter-bar {
            background: var(--ink);
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .filter-bar-inner {
            padding: 14px 48px;
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .filter-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.25);
        }

        .filter-chips {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 2px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            font-size: 11px;
            color: rgba(255,255,255,0.75);
        }

        .chip-key {
            font-size: 8px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--accent-light);
            font-weight: 600;
        }

        /* ─── BODY ──────────────────────────────────── */
        .body {
            padding: 0 48px 48px;
        }

        /* ─── SECTION ───────────────────────────────── */
        .section {
            margin-top: 48px;
        }

        .section-head {
            display: flex;
            align-items: baseline;
            gap: 14px;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border);
            position: relative;
        }

        .section-head::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 48px;
            height: 2px;
            background: var(--accent);
        }

        .section-num {
            font-family: 'DM Serif Display', serif;
            font-size: 11px;
            color: var(--ink-muted);
            letter-spacing: 1px;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--ink);
        }

        /* ─── KPI CARDS ─────────────────────────────── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: var(--border);
            border: 1px solid var(--border);
        }

        .kpi-card {
            background: white;
            padding: 24px 20px;
            position: relative;
            overflow: hidden;
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
        }

        .kpi-card:nth-child(1)::before { background: var(--green); }
        .kpi-card:nth-child(2)::before { background: var(--red); }
        .kpi-card:nth-child(3)::before { background: var(--amber); }
        .kpi-card:nth-child(4)::before { background: var(--blue); }

        .kpi-card .ghost {
            position: absolute;
            bottom: -10px;
            right: -4px;
            font-family: 'DM Serif Display', serif;
            font-size: 64px;
            line-height: 1;
            opacity: 0.04;
            color: var(--ink);
            pointer-events: none;
            user-select: none;
        }

        .kpi-label {
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--ink-muted);
            margin-bottom: 10px;
        }

        .kpi-value {
            font-family: 'DM Serif Display', serif;
            font-size: 36px;
            line-height: 1;
            color: var(--ink);
            margin-bottom: 4px;
        }

        .kpi-unit {
            font-size: 10px;
            color: var(--ink-muted);
            font-weight: 400;
        }

        /* ─── METRIC GRID ───────────────────────────── */
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 12px;
        }

        .metric-card {
            background: var(--cream);
            border: 1px solid var(--border);
            padding: 18px 16px;
            position: relative;
        }

        .metric-label {
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--ink-muted);
            margin-bottom: 8px;
        }

        .metric-value {
            font-family: 'DM Serif Display', serif;
            font-size: 28px;
            line-height: 1;
            color: var(--ink);
            margin-bottom: 2px;
        }

        .metric-unit {
            font-size: 9px;
            color: var(--ink-muted);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* ─── TABLES ────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        thead tr {
            background: var(--ink);
            color: white;
        }

        th {
            padding: 11px 14px;
            text-align: left;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        th.center { text-align: center; }

        td {
            padding: 11px 14px;
            border-bottom: 1px solid var(--cream-dark);
            color: var(--ink);
            vertical-align: middle;
        }

        td.center { text-align: center; }
        td.right { text-align: right; }

        tbody tr:nth-child(even) { background: var(--cream); }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: var(--blue-muted); }

        .row-num {
            display: inline-block;
            width: 22px;
            height: 22px;
            line-height: 22px;
            text-align: center;
            background: var(--cream-dark);
            border-radius: 50%;
            font-size: 9px;
            font-weight: 700;
            color: var(--ink-muted);
        }

        td strong { font-weight: 600; color: var(--ink); }

        /* ─── BADGES ────────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border-radius: 2px;
        }

        .badge-success { background: var(--green-muted); color: var(--green); }
        .badge-info    { background: var(--blue-muted);  color: var(--blue); }
        .badge-warning { background: var(--amber-muted); color: var(--amber); }
        .badge-danger  { background: var(--red-muted);   color: var(--red); }
        .badge-excellent { background: var(--green-muted); color: var(--green); }
        .badge-good   { background: var(--blue-muted);  color: var(--blue); }
        .badge-fair   { background: var(--amber-muted); color: var(--amber); }
        .badge-poor   { background: var(--red-muted);   color: var(--red); }

        /* ─── TWO COL ───────────────────────────────── */
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .col-title {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--accent-muted);
        }

        /* ─── NO DATA ───────────────────────────────── */
        .no-data {
            text-align: center;
            color: var(--ink-muted);
            padding: 32px;
            background: var(--cream);
            border: 1px dashed var(--border);
            font-size: 12px;
            font-style: italic;
        }

        /* ─── DIVIDER ───────────────────────────────── */
        .divider {
            border: none;
            border-top: 1px solid var(--cream-dark);
            margin: 48px 0;
        }

        /* ─── SUMMARY ───────────────────────────────── */
        .summary-box {
            background: var(--ink);
            color: white;
            padding: 28px 32px;
            margin-top: 48px;
            position: relative;
            overflow: hidden;
        }

        .summary-box::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--accent), var(--accent-light));
        }

        .summary-box-bg {
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 50% 80% at 100% 50%, rgba(200,81,27,0.1) 0%, transparent 70%);
        }

        .summary-inner { position: relative; z-index: 1; }

        .summary-title {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--accent-light);
            margin-bottom: 16px;
        }

        .summary-stats {
            display: flex;
            gap: 36px;
            flex-wrap: wrap;
        }

        .summary-stat .sl { font-size: 9px; color: rgba(255,255,255,0.4); letter-spacing: 1px; text-transform: uppercase; margin-bottom: 3px; }
        .summary-stat .sv { font-family: 'DM Serif Display', serif; font-size: 24px; color: white; line-height: 1; margin-bottom: 2px; }
        .summary-stat .su { font-size: 10px; color: rgba(255,255,255,0.45); }

        /* ─── FOOTER ────────────────────────────────── */
        .footer {
            background: var(--cream-dark);
            border-top: 1px solid var(--border);
            padding: 24px 48px;
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .footer-brand {
            font-family: 'DM Serif Display', serif;
            font-size: 13px;
            color: var(--ink);
        }

        .footer-brand span {
            color: var(--accent);
        }

        .footer-meta-list {
            display: flex;
            gap: 24px;
        }

        .footer-meta-item .fl { font-size: 8px; letter-spacing: 1px; text-transform: uppercase; color: var(--ink-muted); margin-bottom: 2px; }
        .footer-meta-item .fv { font-size: 11px; font-weight: 500; color: var(--ink); }

        /* ─── PAGE BREAK ────────────────────────────── */
        .page-break {
            page-break-after: always;
            height: 0;
        }

        /* ─── PRINT ─────────────────────────────────── */
        @media print {
            body { background: white; }
            .page { box-shadow: none; max-width: 100%; }
            .section { page-break-inside: avoid; }
            table { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
<div class="page">

    <!-- MASTHEAD -->
    <div class="masthead">
        <div class="masthead-rule"></div>
        <div class="masthead-bg"></div>
        <div class="masthead-inner">
            <div class="masthead-eyebrow">
                <div class="eyebrow-line"></div>
                <div class="eyebrow-text">Laporan Resmi &nbsp;·&nbsp; Sistem Monitoring Maintenance</div>
                <div class="eyebrow-line"></div>
            </div>
            <div class="masthead-title">
                Department Head<br><em>Dashboard</em>
            </div>
            <div class="masthead-subtitle">Laporan Monitoring Maintenance &amp; Performa Mesin Produksi</div>
            <div class="masthead-meta">
                <div class="meta-block">
                    <div class="label">Periode Laporan</div>
                    <div class="value">{{ \Carbon\Carbon::createFromFormat('n', $bulan)->format('F') }} {{ $tahun }}</div>
                </div>
                <div class="meta-block">
                    <div class="label">Tanggal Generate</div>
                    <div class="value">{{ now()->format('d F Y') }}</div>
                </div>
                <div class="meta-block">
                    <div class="label">Waktu Generate</div>
                    <div class="value">{{ now()->format('H:i:s') }} WIB</div>
                </div>
                <div class="meta-block">
                    <div class="label">Status</div>
                    <div class="value">Official Report</div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER BAR -->
    @php
        $filterItems = [];
        $filterItems['Bulan'] = \Carbon\Carbon::createFromFormat('n', $bulan)->format('F');
        $filterItems['Tahun'] = $tahun;
        if ($mesin) $filterItems['Mesin'] = $mesin;
        if ($line) $filterItems['Line'] = $line;
    @endphp
    <div class="filter-bar">
        <div class="filter-bar-inner">
            <span class="filter-label">Filter Aktif</span>
            <div class="filter-chips">
                @foreach($filterItems as $key => $value)
                    <div class="chip">
                        <span class="chip-key">{{ $key }}</span>
                        {{ $value }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- BODY -->
    <div class="body">

        <!-- § 1 KPI -->
        <div class="section">
            <div class="section-head">
                <span class="section-num">01</span>
                <span class="section-title">Key Performance Indicators</span>
            </div>
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="ghost">A</div>
                    <div class="kpi-label">Availability</div>
                    <div class="kpi-value">{{ number_format($availability, 1) }}<small style="font-size:18px">%</small></div>
                    <div class="kpi-unit">Waktu Operasional</div>
                </div>
                <div class="kpi-card">
                    <div class="ghost">D</div>
                    <div class="kpi-label">Downtime</div>
                    <div class="kpi-value">{{ number_format($downtimePercent, 1) }}<small style="font-size:18px">%</small></div>
                    <div class="kpi-unit">Tidak Operasional</div>
                </div>
                <div class="kpi-card">
                    <div class="ghost">R</div>
                    <div class="kpi-label">Avg MTTR</div>
                    <div class="kpi-value">{{ number_format($avgMTTR, 0) }}</div>
                    <div class="kpi-unit">Menit Repair</div>
                </div>
                <div class="kpi-card">
                    <div class="ghost">F</div>
                    <div class="kpi-label">Avg MTBF</div>
                    <div class="kpi-value">{{ number_format($avgMTBFHours, 0) }}</div>
                    <div class="kpi-unit">Jam Operasi</div>
                </div>
            </div>
        </div>

        <!-- § 2 Machine Performance -->
        <div class="section">
            <div class="section-head">
                <span class="section-num">02</span>
                <span class="section-title">Machine Performance Metrics</span>
            </div>
            <div class="metric-grid">
                <div class="metric-card">
                    <div class="metric-label">Planned Time</div>
                    <div class="metric-value">{{ number_format(($totalPlannedTime ?? 0) / 60, 1) }}</div>
                    <div class="metric-unit">Jam</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Down Time</div>
                    <div class="metric-value">{{ number_format(($totalDowntimeMinutes ?? 0) / 60, 1) }}</div>
                    <div class="metric-unit">Jam</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Operation Time</div>
                    <div class="metric-value">{{ number_format((($totalPlannedTime ?? 0) - ($totalDowntimeMinutes ?? 0)) / 60, 1) }}</div>
                    <div class="metric-unit">Jam</div>
                </div>
            </div>
            <div class="metric-grid">
                <div class="metric-card">
                    <div class="metric-label">Total Laporan</div>
                    <div class="metric-value">{{ $totalLaporan }}</div>
                    <div class="metric-unit">Records</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Total Breakdown</div>
                    <div class="metric-value">{{ $totalBreakdown }}</div>
                    <div class="metric-unit">Kejadian</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Total Downtime</div>
                    <div class="metric-value">{{ number_format($totalDowntime / 60, 1) }}</div>
                    <div class="metric-unit">Jam</div>
                </div>
            </div>
        </div>

        <!-- § 3 Maintenance Type -->
        <div class="section">
            <div class="section-head">
                <span class="section-num">03</span>
                <span class="section-title">Maintenance Type Analysis</span>
            </div>
            <div class="metric-grid">
                <div class="metric-card">
                    <div class="metric-label">Corrective Maintenance</div>
                    <div class="metric-value">{{ number_format($totalCorrectiveMaint ?? 0, 1) }}</div>
                    <div class="metric-unit">Jam</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Preventive Maintenance</div>
                    <div class="metric-value">{{ number_format($totalPreventiveMaint ?? 0, 1) }}</div>
                    <div class="metric-unit">Jam</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Change Over Product</div>
                    <div class="metric-value">{{ number_format($totalChangeOver ?? 0, 1) }}</div>
                    <div class="metric-unit">Jam</div>
                </div>
            </div>
        </div>

        <!-- § 4 Top Downtime Mesin -->
        <div class="section">
            <div class="section-head">
                <span class="section-num">04</span>
                <span class="section-title">Top 10 Mesin — Downtime Tertinggi</span>
            </div>
            @if($topDowntimeMesin->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width:5%">#</th>
                            <th>Nama Mesin</th>
                            <th class="center" style="width:22%">Downtime (Jam)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topDowntimeMesin as $index => $item)
                            <tr>
                                <td><span class="row-num">{{ $index + 1 }}</span></td>
                                <td><strong>{{ $item->mesin_name }}</strong></td>
                                <td class="center"><strong>{{ number_format($item->total_downtime / 60, 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">Tidak ada data downtime mesin</div>
            @endif
        </div>

        <!-- § 5 Breakdown Analysis -->
        <div class="section">
            <div class="section-head">
                <span class="section-num">05</span>
                <span class="section-title">Breakdown Analysis</span>
            </div>
            <div class="two-col">
                <div>
                    <div class="col-title">Top 7 Breakdown — Per Line</div>
                    @if($topBreakdownLine->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:10%">#</th>
                                    <th>Line</th>
                                    <th class="center" style="width:25%">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topBreakdownLine as $index => $item)
                                    <tr>
                                        <td><span class="row-num">{{ $index + 1 }}</span></td>
                                        <td>{{ $item->line }}</td>
                                        <td class="center"><span class="badge badge-danger">{{ $item->breakdown_count }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="no-data">Tidak ada data</div>
                    @endif
                </div>
                <div>
                    <div class="col-title">Top 7 Jenis Kerusakan</div>
                    @if($topBreakdownCatatan->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:10%">#</th>
                                    <th>Kerusakan</th>
                                    <th class="center" style="width:25%">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topBreakdownCatatan as $index => $item)
                                    <tr>
                                        <td><span class="row-num">{{ $index + 1 }}</span></td>
                                        <td>{{ $item->catatan ?? '—' }}</td>
                                        <td class="center"><span class="badge badge-danger">{{ $item->breakdown_count }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="no-data">Tidak ada data</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- § 6 Spare Part -->
        <div class="section">
            <div class="section-head">
                <span class="section-num">06</span>
                <span class="section-title">Spare Part Monitoring</span>
            </div>
            @if($spareParts->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width:5%">#</th>
                            <th>Nama Spare Part</th>
                            <th class="center" style="width:20%">Total Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($spareParts as $index => $item)
                            <tr>
                                <td><span class="row-num">{{ $index + 1 }}</span></td>
                                <td>{{ $item->sparepart ?? '—' }}</td>
                                <td class="center"><span class="badge badge-info">{{ $item->total_qty }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">Tidak ada data spare part</div>
            @endif
        </div>

        <div class="page-break"></div>

        <!-- § 7 Most Reliable -->
        <div class="section">
            <div class="section-head">
                <span class="section-num">07</span>
                <span class="section-title">Machine Reliability — Top 5 Most Reliable</span>
            </div>
            @if(count($topReliableMachines) > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width:5%">#</th>
                            <th>Nama Mesin</th>
                            <th class="center" style="width:16%">MTBF (hrs)</th>
                            <th class="center" style="width:14%">Failures</th>
                            <th class="center" style="width:16%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topReliableMachines as $index => $machine)
                            @php
                                $failureCount = $machine['failure_count'] ?? 0;
                                $downtimeHours = $machine['total_downtime_hours'] ?? 0;
                                if ($failureCount == 0 || ($failureCount == 1 && $downtimeHours < 1)) {
                                    $badgeClass = 'badge-excellent'; $status = 'Excellent';
                                } elseif ($failureCount <= 2 && $downtimeHours < 4) {
                                    $badgeClass = 'badge-good'; $status = 'Good';
                                } else {
                                    $badgeClass = 'badge-fair'; $status = 'Fair';
                                }
                            @endphp
                            <tr>
                                <td><span class="row-num">{{ $index + 1 }}</span></td>
                                <td><strong>{{ $machine['machine_name'] }}</strong></td>
                                <td class="center">{{ number_format($machine['mtbf_hours'], 1) }}</td>
                                <td class="center"><span class="badge badge-danger">{{ $machine['failure_count'] }}</span></td>
                                <td class="center"><span class="badge {{ $badgeClass }}">{{ $status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">Tidak ada data MTBF untuk mesin</div>
            @endif
        </div>

        <!-- § 8 Worst Machines -->
        <div class="section">
            <div class="section-head">
                <span class="section-num">08</span>
                <span class="section-title">Machine Reliability — Bottom 5 Worst Performing</span>
            </div>
            @if(count($worstMachines) > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width:5%">#</th>
                            <th>Nama Mesin</th>
                            <th class="center" style="width:16%">MTBF (hrs)</th>
                            <th class="center" style="width:14%">Failures</th>
                            <th class="center" style="width:16%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($worstMachines as $index => $machine)
                            @php
                                $failureCount = $machine['failure_count'] ?? 0;
                                $downtimeHours = $machine['total_downtime_hours'] ?? 0;
                                if ($failureCount <= 2 && $downtimeHours < 4) {
                                    $badgeClass = 'badge-good'; $status = 'Good';
                                } elseif ($failureCount <= 5 && $downtimeHours < 12) {
                                    $badgeClass = 'badge-fair'; $status = 'Fair';
                                } else {
                                    $badgeClass = 'badge-poor'; $status = 'Poor';
                                }
                            @endphp
                            <tr>
                                <td><span class="row-num">{{ $index + 1 }}</span></td>
                                <td><strong>{{ $machine['machine_name'] }}</strong></td>
                                <td class="center">{{ number_format($machine['mtbf_hours'], 1) }}</td>
                                <td class="center"><span class="badge badge-danger">{{ $machine['failure_count'] }}</span></td>
                                <td class="center"><span class="badge {{ $badgeClass }}">{{ $status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">Tidak ada data MTBF untuk mesin</div>
            @endif
        </div>

        <!-- SUMMARY -->
        <div class="summary-box">
            <div class="summary-box-bg"></div>
            <div class="summary-inner">
                <div class="summary-title">Summary &amp; Insights</div>
                <div class="summary-stats">
                    <div class="summary-stat">
                        <div class="sl">Total Reports</div>
                        <div class="sv">{{ $totalLaporan }}</div>
                        <div class="su">Records</div>
                    </div>
                    <div class="summary-stat">
                        <div class="sl">Total Downtime</div>
                        <div class="sv">{{ number_format($totalDowntime / 60, 1) }}</div>
                        <div class="su">Jam</div>
                    </div>
                    <div class="summary-stat">
                        <div class="sl">Availability</div>
                        <div class="sv">{{ number_format($availability, 1) }}%</div>
                        <div class="su">Rate</div>
                    </div>
                    <div class="summary-stat">
                        <div class="sl">Total Breakdown</div>
                        <div class="sv">{{ $totalBreakdown }}</div>
                        <div class="su">Kejadian</div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /body -->

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-inner">
            <div class="footer-brand">Dept. Head <span>Dashboard</span></div>
            <div class="footer-meta-list">
                <div class="footer-meta-item">
                    <div class="fl">Generated</div>
                    <div class="fv">{{ now()->format('d F Y, H:i') }}</div>
                </div>
                <div class="footer-meta-item">
                    <div class="fl">Periode</div>
                    <div class="fv">{{ \Carbon\Carbon::createFromFormat('n', $bulan)->format('F') }} {{ $tahun }}</div>
                </div>
                <div class="footer-meta-item">
                    <div class="fl">Dokumen</div>
                    <div class="fv">Laporan Resmi</div>
                </div>
            </div>
        </div>
    </div>

</div><!-- /page -->
</body>
</html>