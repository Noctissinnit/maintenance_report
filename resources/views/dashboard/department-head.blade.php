@extends('layouts.app')

@section('title', 'Department Head Dashboard - Sistem Laporan Maintenance')

@section('extra-css')
<style>
    * { box-sizing: border-box; }

    .dash-page { padding: 1.5rem 0; }

    /* ── Page Header ── */
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 12px;
    }
    .page-title { font-size: 20px; font-weight: 500; color: #1a1a2e; margin: 0; }
    .page-subtitle { font-size: 13px; color: #6b7280; margin-top: 4px; }
    .header-actions { display: flex; gap: 8px; align-items: center; }
    .badge-live {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 11px; font-weight: 500; padding: 4px 10px;
        border-radius: 20px; background: #dcfce7; color: #166534;
        border: 0.5px solid #bbf7d0;
    }
    .dot-pulse {
        width: 6px; height: 6px; border-radius: 50%; background: #22c55e;
        animation: dot-pulse 2s infinite;
    }
    @keyframes dot-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

    /* ── Filter Bar ── */
    .filter-bar {
        background: #fff;
        border: 0.5px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }
    .filter-group { display: flex; flex-direction: column; gap: 4px; min-width: 120px; }
    .filter-label {
        font-size: 11px; font-weight: 500; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .filter-bar select,
    .filter-bar input[type="date"] {
        font-size: 13px; padding: 6px 10px;
        border: 0.5px solid #d1d5db; border-radius: 8px;
        background: #f9fafb; color: #111827; height: 34px;
        transition: border-color 0.15s;
    }
    .filter-bar select:focus,
    .filter-bar input[type="date"]:focus {
        outline: none; border-color: #3266ad;
        box-shadow: 0 0 0 3px rgba(50, 102, 173, 0.1);
    }
    .filter-btn-apply {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 500; padding: 6px 16px;
        border-radius: 8px; border: none;
        background: #3266ad; color: #fff; cursor: pointer;
        height: 34px; transition: background 0.15s;
    }
    .filter-btn-apply:hover { background: #285a9a; color: #fff; }
    .filter-btn-pdf {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 500; padding: 6px 14px;
        border-radius: 8px; border: none;
        background: #E24B4A; color: #fff; cursor: pointer;
        height: 34px; transition: background 0.15s; text-decoration: none;
    }
    .filter-btn-pdf:hover { background: #c43d3c; color: #fff; }
    .filter-mode-group {
        display: flex; gap: 2px; padding: 3px;
        background: #f3f4f6; border-radius: 8px; align-self: flex-end; height: 34px;
    }
    .filter-mode-group input[type="radio"] { display: none; }
    .filter-mode-group label {
        display: flex; align-items: center;
        font-size: 12px; font-weight: 500; padding: 0 12px;
        border-radius: 6px; cursor: pointer; color: #6b7280;
        transition: all 0.15s; white-space: nowrap;
    }
    .filter-mode-group input[type="radio"]:checked + label {
        background: #fff; color: #111827;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    #date_range_wrap, #month_year_wrap { display: contents; }

    /* ── Info Banner ── */
    .info-banner {
        background: #eff6ff; border: 0.5px solid #bfdbfe;
        border-radius: 8px; padding: 10px 14px;
        font-size: 13px; color: #1d4ed8;
        display: flex; align-items: center; gap: 8px;
        margin-bottom: 1.5rem;
    }

    /* ── Section Title ── */
    .section-title {
        font-size: 11px; font-weight: 500; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.6px;
        margin-bottom: 10px; display: flex; align-items: center; gap: 6px;
    }

    /* ── KPI Cards ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 10px;
        margin-bottom: 1.5rem;
    }
    .kpi-card {
        background: #fff; border: 0.5px solid #e5e7eb;
        border-radius: 12px; padding: 1rem 1.25rem;
        position: relative; overflow: hidden;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .kpi-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.07); transform: translateY(-1px); }
    .kpi-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
    }
    .kpi-card.blue::before   { background: #3266ad; }
    .kpi-card.red::before    { background: #A32D2D; }
    .kpi-card.amber::before  { background: #BA7517; }
    .kpi-card.green::before  { background: #3B6D11; }
    .kpi-card.teal::before   { background: #0F6E56; }
    .kpi-card.purple::before { background: #534AB7; }

    .kpi-icon {
        width: 32px; height: 32px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; margin-bottom: 10px;
    }
    .kpi-icon.blue   { background: #E6F1FB; color: #185FA5; }
    .kpi-icon.red    { background: #FCEBEB; color: #A32D2D; }
    .kpi-icon.amber  { background: #FAEEDA; color: #854F0B; }
    .kpi-icon.green  { background: #EAF3DE; color: #3B6D11; }
    .kpi-icon.teal   { background: #E1F5EE; color: #0F6E56; }
    .kpi-icon.purple { background: #EEEDFE; color: #534AB7; }

    .kpi-label { font-size: 11px; color: #6b7280; margin-bottom: 4px; }
    .kpi-value { font-size: 22px; font-weight: 500; color: #111827; line-height: 1.1; }
    .kpi-unit  { font-size: 11px; color: #9ca3af; margin-top: 2px; }

    /* ── Performance Cards ── */
    .perf-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
        margin-bottom: 1.5rem;
    }
    .perf-card {
        background: #f9fafb; border: 0.5px solid #e5e7eb;
        border-radius: 8px; padding: 14px; text-align: center;
        transition: background 0.15s;
    }
    .perf-card:hover { background: #f3f4f6; }
    .perf-icon-wrap { font-size: 22px; margin-bottom: 6px; }
    .perf-val  { font-size: 20px; font-weight: 500; color: #111827; }
    .perf-lbl  { font-size: 11px; color: #6b7280; margin-top: 4px; }

    /* ── Generic Cards ── */
    .dash-card {
        background: #fff; border: 0.5px solid #e5e7eb;
        border-radius: 12px; overflow: hidden;
    }
    .dash-card-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; border-bottom: 0.5px solid #e5e7eb;
        font-size: 13px; font-weight: 500; color: #111827;
    }
    .dash-card-body { padding: 12px 16px; }
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 1.5rem; }
    @media (max-width: 768px) { .two-col { grid-template-columns: 1fr; } }

    /* ── Tables ── */
    .dash-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .dash-table th {
        font-size: 11px; font-weight: 500; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.4px;
        padding: 6px 8px; border-bottom: 0.5px solid #e5e7eb; text-align: left;
    }
    .dash-table td {
        padding: 8px 8px; border-bottom: 0.5px solid #f3f4f6;
        color: #111827; vertical-align: middle;
    }
    .dash-table tr:last-child td { border-bottom: none; }
    .dash-table tbody tr:hover td { background: #f9fafb; }
    .td-center { text-align: center; }
    .td-name   { font-weight: 500; }
    .td-sub    { font-size: 11px; color: #9ca3af; }

    /* Inline bar chart in table */
    .bar-wrap  { display: flex; align-items: center; gap: 8px; }
    .bar-bg    { flex: 1; height: 4px; background: #f3f4f6; border-radius: 4px; overflow: hidden; }
    .bar-fill  { height: 100%; border-radius: 4px; background: #3266ad; }
    .bar-val   { font-size: 12px; color: #6b7280; min-width: 36px; text-align: right; }

    /* ── Badges ── */
    .dash-badge {
        display: inline-flex; align-items: center;
        font-size: 11px; font-weight: 500; padding: 2px 8px;
        border-radius: 20px; white-space: nowrap;
    }
    .badge-green  { background: #dcfce7; color: #166534; }
    .badge-red    { background: #fee2e2; color: #991b1b; }
    .badge-amber  { background: #fef3c7; color: #92400e; }
    .badge-blue   { background: #dbeafe; color: #1e40af; }

    /* ── Status Pills ── */
    .status-pill {
        display: inline-flex; align-items: center;
        font-size: 11px; font-weight: 500;
        padding: 2px 8px; border-radius: 20px;
    }
    .pill-excellent { background: #dcfce7; color: #166534; }
    .pill-good      { background: #dbeafe; color: #1e40af; }
    .pill-fair      { background: #fef3c7; color: #92400e; }
    .pill-poor      { background: #fee2e2; color: #991b1b; }

    /* ── Charts ── */
    .chart-wrap { position: relative; width: 100%; }
</style>
@endsection

@section('content')
<div class="dash-page">

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div>
            <h2 class="page-title">Maintenance Dashboard</h2>
            <div class="page-subtitle">Department Head &middot; Monitoring Semua Aktivitas</div>
        </div>
        <div class="header-actions">
            <span class="badge-live"><span class="dot-pulse"></span> Live</span>
            <a href="{{ route('dashboard.download-pdf', array_merge(request()->query(), ['bulan' => $bulan, 'tahun' => $tahun, 'mesin' => $mesin, 'line' => $line])) }}"
               class="filter-btn-pdf">
                <i class="bi bi-download"></i> Download PDF
            </a>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('dashboard') }}" style="display:contents">

            {{-- Mode toggle --}}
            <div class="filter-group">
                <span class="filter-label">Mode</span>
                <div class="filter-mode-group">
                    <input type="radio" name="filter_mode" id="fm_month" value="month"
                        @if(request('filter_mode') != 'date_range') checked @endif
                        onchange="toggleFilterMode()">
                    <label for="fm_month">Bulan/Tahun</label>

                    <input type="radio" name="filter_mode" id="fm_range" value="date_range"
                        @if(request('filter_mode') == 'date_range') checked @endif
                        onchange="toggleFilterMode()">
                    <label for="fm_range">Range Tanggal</label>
                </div>
            </div>

            {{-- Month/Year fields --}}
            <div id="month_year_wrap" style="display: @if(request('filter_mode') == 'date_range') none @else contents @endif;">
                <div class="filter-group">
                    <span class="filter-label">Bulan</span>
                    <select name="bulan" id="bulan">
                        @php $bulanList = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December']; @endphp
                        @foreach($bulanList as $m => $bulanName)
                            <option value="{{ $m }}" @if($bulan == $m) selected @endif>{{ $bulanName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <span class="filter-label">Tahun</span>
                    <select name="tahun" id="tahun">
                        @for($y = 2024; $y <= 2026; $y++)
                            <option value="{{ $y }}" @if($tahun == $y) selected @endif>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            {{-- Date range fields --}}
            <div id="date_range_wrap" style="display: @if(request('filter_mode') == 'date_range') contents @else none @endif;">
                <div class="filter-group">
                    <span class="filter-label">Dari Tanggal</span>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal', date('Y-m-01')) }}">
                </div>
                <div class="filter-group">
                    <span class="filter-label">Sampai Tanggal</span>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal', date('Y-m-d')) }}">
                </div>
            </div>

            {{-- Mesin --}}
            <div class="filter-group">
                <span class="filter-label">Mesin</span>
                <select name="mesin">
                    <option value="">-- Semua Mesin --</option>
                    @foreach($allMesins as $m)
                        <option value="{{ $m }}" @if($mesin == $m) selected @endif>{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Line --}}
            <div class="filter-group">
                <span class="filter-label">Line</span>
                <select name="line">
                    <option value="">-- Semua Line --</option>
                    @foreach($allLines as $l)
                        <option value="{{ $l }}" @if($line == $l) selected @endif>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            {{-- All time toggle --}}
            <div class="filter-group" style="justify-content:flex-end">
                <span class="filter-label">Semua Data</span>
                <div style="display:flex;align-items:center;height:34px">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="all_time" id="all_time" value="1"
                            @if(request('all_time') == '1') checked @endif>
                    </div>
                </div>
            </div>

            <button type="submit" class="filter-btn-apply">
                <i class="bi bi-funnel"></i> Terapkan
            </button>
        </form>
    </div>

    {{-- ── Info Banner ── --}}
    <div class="info-banner">
        <i class="bi bi-info-circle-fill"></i>
        <span><strong>Department Head</strong> — Anda memiliki akses monitoring untuk melihat semua laporan harian dan aktivitas supervisor.</span>
    </div>

    {{-- ── KPI Cards ── --}}
    <div class="section-title"><i class="bi bi-bar-chart-line"></i> KPI Utama</div>
    <div class="kpi-grid">
        <div class="kpi-card blue">
            <div class="kpi-icon blue"><i class="bi bi-activity"></i></div>
            <div class="kpi-label">Availability</div>
            <div class="kpi-value">{{ number_format($availability, 2) }}%</div>
            <div class="kpi-unit">bulan ini</div>
        </div>
        <div class="kpi-card red">
            <div class="kpi-icon red"><i class="bi bi-exclamation-circle"></i></div>
            <div class="kpi-label">Downtime</div>
            <div class="kpi-value">{{ number_format($downtimePercent, 2) }}%</div>
            <div class="kpi-unit">dari planned time</div>
        </div>
        <div class="kpi-card amber">
            <div class="kpi-icon amber"><i class="bi bi-clock"></i></div>
            <div class="kpi-label">Rata-rata MTTR</div>
            <div class="kpi-value">{{ number_format($avgMTTR, 2) }}</div>
            <div class="kpi-unit">menit</div>
        </div>
        <div class="kpi-card green">
            <div class="kpi-icon green"><i class="bi bi-arrow-repeat"></i></div>
            <div class="kpi-label">Rata-rata MTBF</div>
            <div class="kpi-value">{{ number_format($avgMTBFHours, 2) }}</div>
            <div class="kpi-unit">jam</div>
        </div>
        <div class="kpi-card teal">
            <div class="kpi-icon teal"><i class="bi bi-file-text"></i></div>
            <div class="kpi-label">Total Laporan</div>
            <div class="kpi-value">{{ $totalLaporan }}</div>
            <div class="kpi-unit">laporan</div>
        </div>
        <div class="kpi-card purple">
            <div class="kpi-icon purple"><i class="bi bi-bug"></i></div>
            <div class="kpi-label">Total Breakdown</div>
            <div class="kpi-value">{{ $totalBreakdown ?? 0 }}</div>
            <div class="kpi-unit">kejadian</div>
        </div>
    </div>

    {{-- ── Machine Performance ── --}}
    <div class="section-title"><i class="bi bi-cpu"></i> Machine Performance</div>
    <div class="perf-grid">
        <div class="perf-card">
            <div class="perf-icon-wrap" style="color:#185FA5"><i class="bi bi-calendar-check"></i></div>
            <div class="perf-val">{{ number_format(($totalPlannedTime ?? 0) / 60, 2) }}</div>
            <div class="perf-lbl">Planned Time (jam)</div>
        </div>
        <div class="perf-card">
            <div class="perf-icon-wrap" style="color:#A32D2D"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="perf-val">{{ number_format(($totalDowntimeMinutes ?? 0) / 60, 2) }}</div>
            <div class="perf-lbl">Down Time (jam)</div>
        </div>
        <div class="perf-card">
            <div class="perf-icon-wrap" style="color:#3B6D11"><i class="bi bi-play-circle"></i></div>
            <div class="perf-val">{{ number_format((($totalPlannedTime ?? 0) - ($totalDowntimeMinutes ?? 0)) / 60, 2) }}</div>
            <div class="perf-lbl">Operation Time (jam)</div>
        </div>
        <div class="perf-card">
            <div class="perf-icon-wrap" style="color:#854F0B"><i class="bi bi-wrench"></i></div>
            <div class="perf-val">{{ number_format($totalCorrectiveMaint ?? 0, 2) }}</div>
            <div class="perf-lbl">Corrective Maint. (jam)</div>
        </div>
        <div class="perf-card">
            <div class="perf-icon-wrap" style="color:#534AB7"><i class="bi bi-shield-check"></i></div>
            <div class="perf-val">{{ number_format($totalPreventiveMaint ?? 0, 2) }}</div>
            <div class="perf-lbl">Preventive Maint. (jam)</div>
        </div>
        <div class="perf-card">
            <div class="perf-icon-wrap" style="color:#0F6E56"><i class="bi bi-arrow-left-right"></i></div>
            <div class="perf-val">{{ number_format($totalChangeOver ?? 0, 2) }}</div>
            <div class="perf-lbl">Change Over (jam)</div>
        </div>
    </div>

    {{-- ── Top Downtime + Breakdown per Line ── --}}
    <div class="two-col">
        <div class="dash-card">
            <div class="dash-card-header">Top 10 Mesin — Downtime Tertinggi</div>
            <div class="dash-card-body">
                @php
                    $maxDT = $topDowntimeMesin->max('total_downtime') ?: 1;
                @endphp
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Mesin</th>
                            <th>Downtime (jam)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topDowntimeMesin as $item)
                            <tr>
                                <td class="td-name">{{ $item->mesin_name }}</td>
                                <td>
                                    <div class="bar-wrap">
                                        <div class="bar-bg">
                                            <div class="bar-fill"
                                                 style="width: {{ number_format($item->total_downtime / $maxDT * 100, 1) }}%">
                                            </div>
                                        </div>
                                        <div class="bar-val">{{ number_format($item->total_downtime / 60, 2) }}</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="text-align:center;color:#9ca3af">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-header">Top 7 Breakdown per Line</div>
            <div class="dash-card-body">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Line</th>
                            <th class="td-center">Downtime</th>
                            <th class="td-center">Breakdown</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topBreakdownLine as $item)
                            <tr>
                                <td class="td-name">{{ $item->line }}</td>
                                <td class="td-center">
                                    <span class="dash-badge badge-amber">
                                        {{ number_format(($item->total_downtime_min ?? 0) / 60, 2) }} jam
                                    </span>
                                </td>
                                <td class="td-center">
                                    <span class="dash-badge badge-red">{{ $item->breakdown_count }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" style="text-align:center;color:#9ca3af">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Top Reliable + Worst Machines ── --}}
    <div class="two-col">
        <div class="dash-card">
            <div class="dash-card-header">
                <span>Top Reliable Machines</span>
                <span class="dash-badge badge-green">Top 5</span>
            </div>
            <div class="dash-card-body">
                @if(count($topReliableMachines) > 0)
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Mesin</th>
                                <th class="td-center">MTBF (hrs)</th>
                                <th class="td-center">Failures</th>
                                <th class="td-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topReliableMachines as $machine)
                                @php
                                    $fc = $machine['failure_count'] ?? 0;
                                    $dh = $machine['total_downtime_hours'] ?? 0;
                                    if ($fc == 0 || ($fc == 1 && $dh < 1)) {
                                        $pillClass = 'pill-excellent'; $pillLabel = 'Excellent';
                                    } elseif ($fc <= 2 && $dh < 4) {
                                        $pillClass = 'pill-good'; $pillLabel = 'Good';
                                    } else {
                                        $pillClass = 'pill-fair'; $pillLabel = 'Fair';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="td-name">{{ $machine['machine_name'] }}</div>
                                        <div class="td-sub">{{ $machine['line_name'] ?? '—' }}</div>
                                    </td>
                                    <td class="td-center">{{ number_format($machine['mtbf_hours'], 2) }}</td>
                                    <td class="td-center">
                                        <span class="dash-badge badge-red">{{ $fc }}</span>
                                    </td>
                                    <td class="td-center">
                                        <span class="status-pill {{ $pillClass }}">{{ $pillLabel }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="color:#9ca3af;font-size:13px;text-align:center;padding:1rem 0">Tidak ada data MTBF</p>
                @endif
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-header">
                <span>Worst Performing Machines</span>
                <span class="dash-badge badge-red">Bottom 5</span>
            </div>
            <div class="dash-card-body">
                @if(count($worstMachines) > 0)
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Mesin</th>
                                <th class="td-center">MTBF (hrs)</th>
                                <th class="td-center">Failures</th>
                                <th class="td-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($worstMachines as $machine)
                                @php
                                    $fc = $machine['failure_count'] ?? 0;
                                    $dh = $machine['total_downtime_hours'] ?? 0;
                                    if ($fc <= 2 && $dh < 4) {
                                        $pillClass = 'pill-good'; $pillLabel = 'Good';
                                    } elseif ($fc <= 5 && $dh < 12) {
                                        $pillClass = 'pill-fair'; $pillLabel = 'Fair';
                                    } else {
                                        $pillClass = 'pill-poor'; $pillLabel = 'Poor';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="td-name">{{ $machine['machine_name'] }}</div>
                                        <div class="td-sub">{{ $machine['line_name'] ?? '—' }}</div>
                                    </td>
                                    <td class="td-center">{{ number_format($machine['mtbf_hours'], 2) }}</td>
                                    <td class="td-center">
                                        <span class="dash-badge badge-red">{{ $fc }}</span>
                                    </td>
                                    <td class="td-center">
                                        <span class="status-pill {{ $pillClass }}">{{ $pillLabel }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="color:#9ca3af;font-size:13px;text-align:center;padding:1rem 0">Tidak ada data MTBF</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Jenis Kerusakan + Spare Part ── --}}
    <div class="two-col">
        <div class="dash-card">
            <div class="dash-card-header">Top 7 Jenis Kerusakan</div>
            <div class="dash-card-body">
                <table class="dash-table">
                    <thead>
                        <tr><th>Kerusakan</th><th class="td-center">Total</th></tr>
                    </thead>
                    <tbody>
                        @forelse($topBreakdownCatatan as $item)
                            <tr>
                                <td>{{ $item->catatan ?? '—' }}</td>
                                <td class="td-center">
                                    <span class="dash-badge badge-red">{{ $item->breakdown_count }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="text-align:center;color:#9ca3af">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-header">
                Monitoring Spare Part —
                {{ \Carbon\Carbon::createFromFormat('n', $bulan)->format('F') }} {{ $tahun }}
            </div>
            <div class="dash-card-body">
                <table class="dash-table">
                    <thead>
                        <tr><th>Spare Part</th><th class="td-center">Qty</th></tr>
                    </thead>
                    <tbody>
                        @forelse($spareParts as $item)
                            <tr>
                                <td>{{ $item->sparepart ?? '—' }}</td>
                                <td class="td-center">
                                    <span class="dash-badge badge-blue">{{ $item->total_qty }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="text-align:center;color:#9ca3af">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Downtime by Scope Chart ── --}}
    <div class="dash-card" style="margin-bottom:1.5rem">
        <div class="dash-card-header">
            <span>Downtime by Scope</span>
            <span style="font-size:11px;color:#9ca3af">Electrical · Mechanical · Utility · Building</span>
        </div>
        <div class="dash-card-body">
            <div class="chart-wrap" style="height:240px">
                <canvas id="downtimeByScopeChart"
                    role="img"
                    aria-label="Bar chart of downtime by scope: Electrical, Mechanical, Utility, Building">
                    Downtime by scope data
                </canvas>
            </div>
        </div>
    </div>

    {{-- ── Bottom Charts ── --}}
    <div class="two-col">
        <div class="dash-card">
            <div class="dash-card-header">Top 10 Downtime per Mesin</div>
            <div class="dash-card-body">
                @if(count($topDowntimeMesin) > 0)
                    <div class="chart-wrap" style="height: {{ max(count($topDowntimeMesin) * 40 + 60, 200) }}px">
                        <canvas id="topDowntimeChart"
                            role="img"
                            aria-label="Horizontal bar chart of top 10 machines by downtime hours">
                            Top machine downtime data
                        </canvas>
                    </div>
                @else
                    <p style="color:#9ca3af;font-size:13px;text-align:center;padding:2rem 0">Tidak ada data</p>
                @endif
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-header">Machine Performance Distribution</div>
            <div class="dash-card-body">
                @if(count($machinePerformance) > 0)
                    <div id="donutLegend" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px;font-size:11px;color:#6b7280"></div>
                    <div class="chart-wrap" style="height:220px">
                        <canvas id="machinePerformanceChart"
                            role="img"
                            aria-label="Donut chart showing machine performance distribution">
                            Machine distribution data
                        </canvas>
                    </div>
                @else
                    <p style="color:#9ca3af;font-size:13px;text-align:center;padding:2rem 0">Tidak ada data</p>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

@section('extra-js')
<script>
    // ── Filter mode toggle ──
    function toggleFilterMode() {
        const mode = document.querySelector('input[name="filter_mode"]:checked').value;
        document.getElementById('month_year_wrap').style.display = mode === 'date_range' ? 'none' : 'contents';
        document.getElementById('date_range_wrap').style.display = mode === 'date_range' ? 'contents' : 'none';
    }

    // ── Charts ──
    function initCharts() {
        // Raw data from controller
        const topDTRaw    = {!! json_encode($topDowntimeMesin->pluck('total_downtime')->toArray()) !!};
        const topDTHours  = topDTRaw.map(x => parseFloat((x / 60).toFixed(2)));
        const topDTLabels = {!! json_encode($topDowntimeMesin->pluck('mesin_name')->toArray()) !!};

        const perfData   = {!! json_encode($machinePerformance->pluck('count')->toArray()) !!};
        const perfLabels = {!! json_encode($machinePerformance->pluck('mesin_name')->toArray()) !!};

        const scopeRaw   = {!! json_encode($downtimeByScope) !!};
        const scopeLabels = scopeRaw.map(i => i.scope);
        const scopeHours  = scopeRaw.map(i => parseFloat(parseFloat(i.downtime_hours).toFixed(2)));

        const chartColors = ['#3266ad', '#E24B4A', '#8B5CF6', '#BA7517',
                             '#0F6E56', '#D85A30', '#3B6D11', '#185FA5',
                             '#534AB7', '#854F0B'];

        // Scope bar chart
        if (scopeLabels.length > 0) {
            new Chart(document.getElementById('downtimeByScopeChart'), {
                type: 'bar',
                data: {
                    labels: scopeLabels,
                    datasets: [{
                        label: 'Downtime (jam)',
                        data: scopeHours,
                        backgroundColor: ['#3266ad', '#E24B4A', '#8B5CF6', '#BA7517'],
                        borderWidth: 0,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 11 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 12 } } }
                    }
                }
            });
        }

        // Horizontal top downtime chart
        if (topDTHours.length > 0) {
            new Chart(document.getElementById('topDowntimeChart'), {
                type: 'bar',
                data: {
                    labels: topDTLabels,
                    datasets: [{
                        label: 'Downtime (jam)',
                        data: topDTHours,
                        backgroundColor: '#3266ad',
                        borderWidth: 0,
                        borderRadius: 3
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 11 } } },
                        y: { grid: { display: false }, ticks: { font: { size: 11 } } }
                    }
                }
            });
        }

        // Donut + custom legend
        if (perfData.length > 0) {
            const total = perfData.reduce((a, b) => a + b, 0);
            const legendEl = document.getElementById('donutLegend');
            if (legendEl) {
                legendEl.innerHTML = perfLabels.map((lbl, i) => {
                    const pct = total > 0 ? Math.round(perfData[i] / total * 100) : 0;
                    return `<span style="display:flex;align-items:center;gap:4px">
                        <span style="width:8px;height:8px;border-radius:2px;background:${chartColors[i % chartColors.length]}"></span>
                        ${lbl} ${pct}%
                    </span>`;
                }).join('');
            }

            new Chart(document.getElementById('machinePerformanceChart'), {
                type: 'doughnut',
                data: {
                    labels: perfLabels,
                    datasets: [{
                        data: perfData,
                        backgroundColor: chartColors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: { legend: { display: false } }
                }
            });
        }
    }

    // Wait for Chart.js to load
    (function waitForChart() {
        if (typeof Chart !== 'undefined') initCharts();
        else setTimeout(waitForChart, 100);
    })();
</script>
@endsection