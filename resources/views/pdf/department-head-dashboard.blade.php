<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Head Dashboard Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Geist:wght@300;400;500;600;700&family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
:root {
  --p900: #1A1640;
  --p800: #241E5C;
  --p700: #2F2778;
  --p600: #3E32A0;
  --p500: #5448C8;
  --p400: #7B72E0;
  --p300: #A9A4EE;
  --p200: #D0CDFB;
  --p100: #ECEAFA;
  --p50:  #F5F4FE;

  --ink:      #0E0C24;
  --ink2:     #3A3660;
  --ink3:     #7A779C;
  --ink4:     #ABA9C8;
  --line:     #E4E2F4;
  --surface:  #FFFFFF;
  --bg:       #F8F7FE;

  --green:    #1A7A50;
  --green-bg: #E8F5EF;
  --red:      #B5180E;
  --red-bg:   #FEF0EF;
  --amber:    #92530A;
  --amber-bg: #FEF3E2;
  --blue:     #1248A0;
  --blue-bg:  #EAF1FE;
}

* { margin:0; padding:0; box-sizing:border-box; }

body {
  font-family: 'Geist', sans-serif;
  background: var(--bg);
  color: var(--ink);
  font-size: 12px;
  line-height: 1.5;
  -webkit-font-smoothing: antialiased;
}

/* ── PAGE ── */
.page {
  max-width: 900px;
  margin: 0 auto;
  background: var(--surface);
  box-shadow: 0 0 0 1px rgba(83,72,200,0.08), 0 24px 64px rgba(30,22,80,0.12);
}

/* ── COVER ── */
.cover {
  background: var(--p900);
  padding: 52px 56px 44px;
  position: relative;
  overflow: hidden;
}

.cover-grid {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(84,72,200,0.08) 1px, transparent 1px),
    linear-gradient(90deg, rgba(84,72,200,0.08) 1px, transparent 1px);
  background-size: 40px 40px;
}

.cover-glow {
  position: absolute;
  top: -60px; right: -60px;
  width: 360px; height: 360px;
  background: radial-gradient(circle, rgba(84,72,200,0.25) 0%, transparent 70%);
  pointer-events: none;
}

.cover-glow2 {
  position: absolute;
  bottom: -40px; left: 80px;
  width: 200px; height: 200px;
  background: radial-gradient(circle, rgba(123,114,224,0.15) 0%, transparent 70%);
  pointer-events: none;
}

.cover-inner { position: relative; z-index: 2; }

.cover-eyebrow {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
}

.eyebrow-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: var(--p400);
}

.eyebrow-text {
  font-size: 9px;
  font-weight: 600;
  letter-spacing: 2.5px;
  text-transform: uppercase;
  color: var(--p300);
}

.eyebrow-line {
  flex: 1;
  height: 1px;
  background: linear-gradient(90deg, var(--p700), transparent);
}

.cover-heading {
  font-family: 'Instrument Serif', serif;
  font-size: 48px;
  line-height: 1.0;
  color: #fff;
  letter-spacing: -1px;
  margin-bottom: 6px;
}

.cover-heading em {
  font-style: italic;
  color: var(--p300);
}

.cover-tagline {
  font-size: 11px;
  color: rgba(255,255,255,0.35);
  font-weight: 300;
  letter-spacing: 0.3px;
  margin-bottom: 36px;
}

.cover-divider {
  height: 1px;
  background: linear-gradient(90deg, var(--p600), transparent);
  margin-bottom: 28px;
}

.cover-meta {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0;
}

.cover-meta-item {
  padding-right: 24px;
}

.cover-meta-item + .cover-meta-item {
  border-left: 1px solid rgba(255,255,255,0.07);
  padding-left: 24px;
}

.cover-meta-label {
  font-size: 8px;
  font-weight: 600;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: rgba(255,255,255,0.25);
  margin-bottom: 5px;
}

.cover-meta-value {
  font-size: 12px;
  font-weight: 500;
  color: rgba(255,255,255,0.82);
}

/* ── FILTER STRIP ── */
.filter-strip {
  background: var(--p800);
  padding: 11px 56px;
  display: flex;
  align-items: center;
  gap: 14px;
  border-bottom: 1px solid rgba(255,255,255,0.05);
}

.filter-strip-label {
  font-size: 8px;
  font-weight: 600;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: rgba(255,255,255,0.2);
  white-space: nowrap;
  flex-shrink: 0;
}

.filter-strip-sep {
  width: 1px; height: 14px;
  background: rgba(255,255,255,0.1);
  flex-shrink: 0;
}

.chips { display: flex; gap: 6px; flex-wrap: wrap; }

.chip {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 3px;
  padding: 3px 9px;
  font-size: 10px;
  color: rgba(255,255,255,0.65);
}

.chip-k {
  font-size: 8px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: var(--p300);
}

/* ── BODY ── */
.body { padding: 44px 56px 60px; }

/* ── SECTION ── */
.section { margin-bottom: 44px; }

.sec-head {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 18px;
}

.sec-n {
  font-family: 'Geist Mono', monospace;
  font-size: 10px;
  font-weight: 500;
  color: var(--p400);
  background: var(--p50);
  border: 1px solid var(--p200);
  border-radius: 4px;
  padding: 3px 8px;
  letter-spacing: 0.5px;
  flex-shrink: 0;
}

.sec-title {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: var(--ink2);
  flex: 1;
}

.sec-rule {
  flex: 1;
  height: 1px;
  background: var(--line);
}

/* ── KPI ── */
.kpi-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1px;
  background: var(--line);
  border: 1px solid var(--line);
  border-radius: 10px;
  overflow: hidden;
}

.kpi {
  background: var(--surface);
  padding: 22px 20px 18px;
  position: relative;
}

.kpi::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
}

.kpi:nth-child(1)::before { background: var(--green); }
.kpi:nth-child(2)::before { background: var(--red); }
.kpi:nth-child(3)::before { background: var(--amber); }
.kpi:nth-child(4)::before { background: var(--p500); }

.kpi-l {
  font-size: 8px;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--ink4);
  margin-bottom: 10px;
}

.kpi-v {
  font-family: 'Instrument Serif', serif;
  font-size: 38px;
  line-height: 1;
  color: var(--ink);
  letter-spacing: -1px;
  margin-bottom: 5px;
}

.kpi-v sup {
  font-family: 'Geist', sans-serif;
  font-size: 15px;
  font-weight: 500;
  letter-spacing: 0;
  vertical-align: top;
  margin-top: 6px;
  display: inline-block;
}

.kpi-u {
  font-size: 9px;
  color: var(--ink4);
}

/* ── METRICS ── */
.metric-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  margin-bottom: 10px;
}

.metric {
  background: var(--p50);
  border: 1px solid var(--p100);
  border-radius: 8px;
  padding: 18px 16px 14px;
  position: relative;
  overflow: hidden;
}

.metric::after {
  content: '';
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 1px;
  background: linear-gradient(90deg, var(--p200), transparent);
}

.metric-l {
  font-size: 8px;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--p400);
  margin-bottom: 8px;
}

.metric-v {
  font-family: 'Instrument Serif', serif;
  font-size: 30px;
  line-height: 1;
  color: var(--p800);
  letter-spacing: -0.5px;
}

.metric-u {
  font-size: 9px;
  color: var(--ink4);
  margin-top: 3px;
  letter-spacing: 0.5px;
}

/* ── TABLE ── */
.tbl-wrap {
  border: 1px solid var(--line);
  border-radius: 8px;
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12px;
}

thead tr {
  background: var(--p900);
}

th {
  padding: 10px 14px;
  text-align: left;
  font-family: 'Geist Mono', monospace;
  font-size: 8px;
  font-weight: 500;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: rgba(255,255,255,0.45);
  white-space: nowrap;
}

th.c { text-align: center; }

td {
  padding: 10px 14px;
  border-bottom: 1px solid var(--line);
  color: var(--ink);
  vertical-align: middle;
}

td.c { text-align: center; }
td strong { font-weight: 600; }

tbody tr:last-child td { border-bottom: none; }
tbody tr:nth-child(even) { background: var(--p50); }
tbody tr:hover { background: var(--p100); }

.rn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 20px; height: 20px;
  border-radius: 50%;
  background: var(--p100);
  font-family: 'Geist Mono', monospace;
  font-size: 9px;
  font-weight: 500;
  color: var(--p600);
}

/* ── BADGES ── */
.badge {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 4px;
  font-family: 'Geist Mono', monospace;
  font-size: 9px;
  font-weight: 500;
  letter-spacing: 0.3px;
  white-space: nowrap;
}

.badge-excellent, .badge-success { background: var(--green-bg); color: var(--green); }
.badge-good,      .badge-info    { background: var(--blue-bg);  color: var(--blue); }
.badge-fair,      .badge-warning { background: var(--amber-bg); color: var(--amber); }
.badge-poor,      .badge-danger  { background: var(--red-bg);   color: var(--red); }
.badge-purple                    { background: var(--p100);     color: var(--p600); }

/* ── TWO COL ── */
.two-col {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.col-head {
  font-family: 'Geist Mono', monospace;
  font-size: 8px;
  font-weight: 500;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--p500);
  padding-bottom: 9px;
  border-bottom: 2px solid var(--p200);
  margin-bottom: 12px;
}

/* ── NO DATA ── */
.no-data {
  text-align: center;
  color: var(--ink4);
  padding: 32px 20px;
  background: var(--bg);
  border: 1px dashed var(--line);
  border-radius: 8px;
  font-size: 11px;
  font-style: italic;
}

/* ── SUMMARY BOX ── */
.summary {
  background: var(--p900);
  border-radius: 10px;
  padding: 30px 36px;
  margin-top: 44px;
  position: relative;
  overflow: hidden;
}

.summary-grid-bg {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(84,72,200,0.06) 1px, transparent 1px),
    linear-gradient(90deg, rgba(84,72,200,0.06) 1px, transparent 1px);
  background-size: 32px 32px;
}

.summary-glow {
  position: absolute;
  top: -40px; right: -40px;
  width: 240px; height: 240px;
  background: radial-gradient(circle, rgba(84,72,200,0.2) 0%, transparent 70%);
}

.summary-inner { position: relative; z-index: 1; }

.summary-label {
  font-family: 'Geist Mono', monospace;
  font-size: 8px;
  font-weight: 500;
  letter-spacing: 2.5px;
  text-transform: uppercase;
  color: var(--p300);
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.summary-label::after {
  content: '';
  flex: 1;
  height: 1px;
  background: linear-gradient(90deg, var(--p700), transparent);
}

.summary-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0;
}

.sstat { padding-right: 28px; }
.sstat + .sstat {
  border-left: 1px solid rgba(255,255,255,0.08);
  padding-left: 28px;
  padding-right: 0;
}
.sstat + .sstat + .sstat { padding-right: 28px; }
.sstat + .sstat + .sstat + .sstat { padding-right: 0; }

.sstat .sl {
  font-size: 8px;
  font-weight: 600;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: rgba(255,255,255,0.25);
  margin-bottom: 6px;
}

.sstat .sv {
  font-family: 'Instrument Serif', serif;
  font-size: 32px;
  line-height: 1;
  color: #fff;
  letter-spacing: -0.5px;
  margin-bottom: 3px;
}

.sstat .su {
  font-size: 10px;
  color: rgba(255,255,255,0.3);
}

/* ── PAGE BREAK ── */
.pg-break { page-break-after: always; height: 0; }

/* ── FOOTER ── */
.footer {
  border-top: 1px solid var(--line);
  padding: 18px 56px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: var(--p50);
}

.footer-brand {
  display: flex;
  align-items: center;
  gap: 8px;
}

.footer-logo {
  width: 24px; height: 24px;
  background: var(--p600);
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: 700;
  color: white;
  font-family: 'Geist Mono', monospace;
}

.footer-name {
  font-size: 11px;
  font-weight: 600;
  color: var(--ink2);
}

.footer-name span { color: var(--p500); }

.footer-right {
  display: flex;
  gap: 28px;
}

.f-item .fl {
  font-size: 8px;
  font-weight: 600;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--ink4);
  margin-bottom: 2px;
}

.f-item .fv {
  font-size: 10px;
  font-weight: 500;
  color: var(--ink3);
  font-family: 'Geist Mono', monospace;
}

/* ── PRINT ── */
@media print {
  body { background: white; }
  .page { box-shadow: none; max-width: 100%; }
  .section, table { page-break-inside: avoid; }
}
</style>
  </head>
  <body>
    <div class="page">
    <div class="page">

      <!-- COVER -->
      <div class="cover">
        <div class="cover-grid"></div>
        <div class="cover-glow"></div>
        <div class="cover-glow2"></div>
        <div class="cover-inner">
          <div class="cover-eyebrow">
            <div class="eyebrow-dot"></div>
            <div class="eyebrow-text">Laporan Resmi &nbsp;·&nbsp; Maintenance Monitoring System</div>
            <div class="eyebrow-line"></div>
          </div>
          <div class="cover-heading">Department<br>Head <em>Dashboard</em></div>
          <div class="cover-tagline">Laporan Monitoring Maintenance &amp; Performa Mesin Produksi</div>
          <div class="cover-divider"></div>
          <div class="cover-meta">
            <div class="cover-meta-item">
              <div class="cover-meta-label">Periode Laporan</div>
              <div class="cover-meta-value">{{ \Carbon\Carbon::createFromFormat('n', $bulan)->format('F') }} {{ $tahun }}</div>
            </div>
            <div class="cover-meta-item">
              <div class="cover-meta-label">Tanggal Generate</div>
              <div class="cover-meta-value">{{ now()->format('d F Y') }}</div>
            </div>
            <div class="cover-meta-item">
              <div class="cover-meta-label">Waktu Generate</div>
              <div class="cover-meta-value">{{ now()->format('H:i:s') }} WIB</div>
            </div>
            <div class="cover-meta-item">
              <div class="cover-meta-label">Status Dokumen</div>
              <div class="cover-meta-value">Official Report</div>
            </div>
          </div>
        </div>
      </div>

      <!-- FILTER STRIP -->
      @php
        $filterItems = [];
        $filterItems['Bulan'] = \Carbon\Carbon::createFromFormat('n', $bulan)->format('F');
        $filterItems['Tahun'] = $tahun;
        if ($mesin) $filterItems['Mesin'] = $mesin;
        if ($line)  $filterItems['Line']  = $line;
      @endphp
      <div class="filter-strip">
        <span class="filter-strip-label">Filter Aktif</span>
        <div class="filter-strip-sep"></div>
        <div class="chips">
          @foreach($filterItems as $key => $value)
            <div class="chip"><span class="chip-k">{{ $key }}</span>{{ $value }}</div>
          @endforeach
        </div>
      </div>

      <!-- BODY -->
      <div class="body">

        <!-- §01 KPI -->
        <div class="section">
          <div class="sec-head">
            <span class="sec-n">01</span>
            <span class="sec-title">Key Performance Indicators</span>
            <div class="sec-rule"></div>
          </div>
          <div class="kpi-row">
            <div class="kpi">
              <div class="kpi-l">Availability</div>
              <div class="kpi-v">{{ number_format($availability, 1) }}<sup>%</sup></div>
              <div class="kpi-u">Waktu Operasional</div>
            </div>
            <div class="kpi">
              <div class="kpi-l">Downtime Rate</div>
              <div class="kpi-v">{{ number_format($downtimePercent, 1) }}<sup>%</sup></div>
              <div class="kpi-u">Tidak Operasional</div>
            </div>
            <div class="kpi">
              <div class="kpi-l">Avg MTTR</div>
              <div class="kpi-v">{{ number_format($avgMTTR, 0) }}</div>
              <div class="kpi-u">Menit Repair</div>
            </div>
            <div class="kpi">
              <div class="kpi-l">Avg MTBF</div>
              <div class="kpi-v">{{ number_format($avgMTBFHours, 0) }}</div>
              <div class="kpi-u">Jam Operasi</div>
            </div>
          </div>
        </div>

        <!-- §02 Machine Performance -->
        <div class="section">
          <div class="sec-head">
            <span class="sec-n">02</span>
            <span class="sec-title">Machine Performance Metrics</span>
            <div class="sec-rule"></div>
          </div>
          <div class="metric-grid">
            <div class="metric">
              <div class="metric-l">Planned Time</div>
              <div class="metric-v">{{ number_format(($totalPlannedTime ?? 0) / 60, 1) }}</div>
              <div class="metric-u">Jam</div>
            </div>
            <div class="metric">
              <div class="metric-l">Down Time</div>
              <div class="metric-v">{{ number_format(($totalDowntimeMinutes ?? 0) / 60, 1) }}</div>
              <div class="metric-u">Jam</div>
            </div>
            <div class="metric">
              <div class="metric-l">Operation Time</div>
              <div class="metric-v">{{ number_format((($totalPlannedTime ?? 0) - ($totalDowntimeMinutes ?? 0)) / 60, 1) }}</div>
              <div class="metric-u">Jam</div>
            </div>
          </div>
          <div class="metric-grid">
            <div class="metric">
              <div class="metric-l">Total Laporan</div>
              <div class="metric-v">{{ $totalLaporan }}</div>
              <div class="metric-u">Records</div>
            </div>
            <div class="metric">
              <div class="metric-l">Total Breakdown</div>
              <div class="metric-v">{{ $totalBreakdown }}</div>
              <div class="metric-u">Kejadian</div>
            </div>
            <div class="metric">
              <div class="metric-l">Total Downtime</div>
              <div class="metric-v">{{ number_format($totalDowntime / 60, 1) }}</div>
              <div class="metric-u">Jam</div>
            </div>
          </div>
        </div>

        <!-- §03 Maintenance Type -->
        <div class="section">
          <div class="sec-head">
            <span class="sec-n">03</span>
            <span class="sec-title">Maintenance Type Analysis</span>
            <div class="sec-rule"></div>
          </div>
          <div class="metric-grid">
            <div class="metric">
              <div class="metric-l">Corrective Maintenance</div>
              <div class="metric-v">{{ number_format($totalCorrectiveMaint ?? 0, 1) }}</div>
              <div class="metric-u">Jam</div>
            </div>
            <div class="metric">
              <div class="metric-l">Preventive Maintenance</div>
              <div class="metric-v">{{ number_format($totalPreventiveMaint ?? 0, 1) }}</div>
              <div class="metric-u">Jam</div>
            </div>
            <div class="metric">
              <div class="metric-l">Change Over Product</div>
              <div class="metric-v">{{ number_format($totalChangeOver ?? 0, 1) }}</div>
              <div class="metric-u">Jam</div>
            </div>
          </div>
        </div>

        <!-- §04 Top Downtime Mesin -->
        <div class="section">
          <div class="sec-head">
            <span class="sec-n">04</span>
            <span class="sec-title">Top 10 Mesin — Downtime Tertinggi</span>
            <div class="sec-rule"></div>
          </div>
          @if($topDowntimeMesin->count() > 0)
            <div class="tbl-wrap">
              <table>
                <thead>
                  <tr>
                    <th style="width:46px">#</th>
                    <th>Nama Mesin</th>
                    <th class="c" style="width:150px">Downtime (Jam)</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($topDowntimeMesin as $i => $item)
                    <tr>
                      <td><span class="rn">{{ $i + 1 }}</span></td>
                      <td><strong>{{ $item->mesin_name }}</strong></td>
                      <td class="c">{{ number_format($item->total_downtime / 60, 2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="no-data">Tidak ada data downtime mesin pada periode ini.</div>
          @endif
        </div>

        <!-- §05 Breakdown Analysis -->
        <div class="section">
          <div class="sec-head">
            <span class="sec-n">05</span>
            <span class="sec-title">Breakdown Analysis</span>
            <div class="sec-rule"></div>
          </div>
          <div class="two-col">
            <div>
              <div class="col-head">Top 7 Breakdown Per Line</div>
              @if($topBreakdownLine->count() > 0)
                <div class="tbl-wrap">
                  <table>
                    <thead>
                      <tr>
                        <th style="width:36px">#</th>
                        <th>Line</th>
                        <th class="c" style="width:72px">Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($topBreakdownLine as $i => $item)
                        <tr>
                          <td><span class="rn">{{ $i + 1 }}</span></td>
                          <td>{{ $item->line }}</td>
                          <td class="c"><span class="badge badge-danger">{{ $item->breakdown_count }}</span></td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="no-data">Tidak ada data</div>
              @endif
            </div>
            <div>
              <div class="col-head">Top 7 Jenis Kerusakan</div>
              @if($topBreakdownCatatan->count() > 0)
                <div class="tbl-wrap">
                  <table>
                    <thead>
                      <tr>
                        <th style="width:36px">#</th>
                        <th>Kerusakan</th>
                        <th class="c" style="width:72px">Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($topBreakdownCatatan as $i => $item)
                        <tr>
                          <td><span class="rn">{{ $i + 1 }}</span></td>
                          <td>{{ $item->catatan ?? '—' }}</td>
                          <td class="c"><span class="badge badge-danger">{{ $item->breakdown_count }}</span></td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="no-data">Tidak ada data</div>
              @endif
            </div>
          </div>
        </div>

        <!-- §06 Spare Part -->
        <div class="section">
          <div class="sec-head">
            <span class="sec-n">06</span>
            <span class="sec-title">Spare Part Monitoring</span>
            <div class="sec-rule"></div>
          </div>
          @if($spareParts->count() > 0)
            <div class="tbl-wrap">
              <table>
                <thead>
                  <tr>
                    <th style="width:46px">#</th>
                    <th>Nama Spare Part</th>
                    <th class="c" style="width:130px">Total Qty</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($spareParts as $i => $item)
                    <tr>
                      <td><span class="rn">{{ $i + 1 }}</span></td>
                      <td>{{ $item->sparepart ?? '—' }}</td>
                      <td class="c"><span class="badge badge-purple">{{ $item->total_qty }}</span></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="no-data">Tidak ada data spare part pada periode ini.</div>
          @endif
        </div>

        <div class="pg-break"></div>

        <!-- §07 Most Reliable -->
        <div class="section">
          <div class="sec-head">
            <span class="sec-n">07</span>
            <span class="sec-title">Machine Reliability — Top 5 Most Reliable</span>
            <div class="sec-rule"></div>
          </div>
          @if(count($topReliableMachines) > 0)
            <div class="tbl-wrap">
              <table>
                <thead>
                  <tr>
                    <th style="width:46px">#</th>
                    <th>Nama Mesin</th>
                    <th class="c" style="width:110px">MTBF (hrs)</th>
                    <th class="c" style="width:90px">Failures</th>
                    <th class="c" style="width:100px">Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($topReliableMachines as $i => $machine)
                    @php
                      $fc = $machine['failure_count'] ?? 0;
                      $dh = $machine['total_downtime_hours'] ?? 0;
                      if ($fc == 0 || ($fc == 1 && $dh < 1)) { $bc = 'badge-excellent'; $st = 'Excellent'; }
                      elseif ($fc <= 2 && $dh < 4)            { $bc = 'badge-good';     $st = 'Good'; }
                      else                                    { $bc = 'badge-fair';     $st = 'Fair'; }
                    @endphp
                    <tr>
                      <td><span class="rn">{{ $i + 1 }}</span></td>
                      <td><strong>{{ $machine['machine_name'] }}</strong></td>
                      <td class="c">{{ number_format($machine['mtbf_hours'], 1) }}</td>
                      <td class="c"><span class="badge badge-danger">{{ $machine['failure_count'] }}</span></td>
                      <td class="c"><span class="badge {{ $bc }}">{{ $st }}</span></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="no-data">Tidak ada data MTBF untuk mesin.</div>
          @endif
        </div>

        <!-- §08 Worst Machines -->
        <div class="section">
          <div class="sec-head">
            <span class="sec-n">08</span>
            <span class="sec-title">Machine Reliability — Bottom 5 Worst Performing</span>
            <div class="sec-rule"></div>
          </div>
          @if(count($worstMachines) > 0)
            <div class="tbl-wrap">
              <table>
                <thead>
                  <tr>
                    <th style="width:46px">#</th>
                    <th>Nama Mesin</th>
                    <th class="c" style="width:110px">MTBF (hrs)</th>
                    <th class="c" style="width:90px">Failures</th>
                    <th class="c" style="width:100px">Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($worstMachines as $i => $machine)
                    @php
                      $fc = $machine['failure_count'] ?? 0;
                      $dh = $machine['total_downtime_hours'] ?? 0;
                      if ($fc <= 2 && $dh < 4)       { $bc = 'badge-good'; $st = 'Good'; }
                      elseif ($fc <= 5 && $dh < 12)  { $bc = 'badge-fair'; $st = 'Fair'; }
                      else                           { $bc = 'badge-poor'; $st = 'Poor'; }
                    @endphp
                    <tr>
                      <td><span class="rn">{{ $i + 1 }}</span></td>
                      <td><strong>{{ $machine['machine_name'] }}</strong></td>
                      <td class="c">{{ number_format($machine['mtbf_hours'], 1) }}</td>
                      <td class="c"><span class="badge badge-danger">{{ $machine['failure_count'] }}</span></td>
                      <td class="c"><span class="badge {{ $bc }}">{{ $st }}</span></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="no-data">Tidak ada data MTBF untuk mesin.</div>
          @endif
        </div>

        <!-- SUMMARY -->
        <div class="summary">
          <div class="summary-grid-bg"></div>
          <div class="summary-glow"></div>
          <div class="summary-inner">
            <div class="summary-label">Summary &amp; Insights</div>
            <div class="summary-stats">
              <div class="sstat">
                <div class="sl">Total Reports</div>
                <div class="sv">{{ $totalLaporan }}</div>
                <div class="su">Records</div>
              </div>
              <div class="sstat">
                <div class="sl">Total Downtime</div>
                <div class="sv">{{ number_format($totalDowntime / 60, 1) }}</div>
                <div class="su">Jam</div>
              </div>
              <div class="sstat">
                <div class="sl">Availability</div>
                <div class="sv">{{ number_format($availability, 1) }}%</div>
                <div class="su">Rate</div>
              </div>
              <div class="sstat">
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
        <div class="footer-brand">
          <div class="footer-logo">DH</div>
          <div class="footer-name">Dept. Head <span>Dashboard</span></div>
        </div>
        <div class="footer-right">
          <div class="f-item">
            <div class="fl">Generated</div>
            <div class="fv">{{ now()->format('d M Y, H:i') }}</div>
          </div>
          <div class="f-item">
            <div class="fl">Periode</div>
            <div class="fv">{{ \Carbon\Carbon::createFromFormat('n', $bulan)->format('M') }} {{ $tahun }}</div>
          </div>
          <div class="f-item">
            <div class="fl">Dokumen</div>
            <div class="fv">Laporan Resmi</div>
          </div>
        </div>
      </div>

    </div><!-- /page -->
  </body>
</html>