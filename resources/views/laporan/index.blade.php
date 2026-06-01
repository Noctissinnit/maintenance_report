@extends('layouts.app')

@section('title', 'Dashboard Laporan - Sistem Laporan Maintenance')

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

    /* ── Filter Bar ── */
    .filter-section {
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
    .form-label {
        font-size: 11px; font-weight: 500; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .filter-section select,
    .filter-section input[type="date"],
    .filter-section input[type="text"] {
        font-size: 13px; padding: 6px 10px;
        border: 0.5px solid #d1d5db; border-radius: 8px;
        background: #f9fafb; color: #111827; height: 34px;
        transition: border-color 0.15s;
    }
    .filter-section select:focus,
    .filter-section input[type="date"]:focus,
    .filter-section input[type="text"]:focus {
        outline: none; border-color: #3266ad;
        box-shadow: 0 0 0 3px rgba(50, 102, 173, 0.1);
    }
    .filter-btn {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; font-weight: 500; padding: 6px 16px;
        border-radius: 8px; border: none;
        background: #3266ad; color: #fff; cursor: pointer;
        height: 34px; transition: background 0.15s;
    }
    .filter-btn:hover { background: #285a9a; color: #fff; }
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

    .kpi-icon {
        width: 32px; height: 32px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; margin-bottom: 10px;
    }
    .kpi-icon.blue   { background: #E6F1FB; color: #185FA5; }
    .kpi-icon.red    { background: #FCEBEB; color: #A32D2D; }
    .kpi-icon.amber  { background: #FAEEDA; color: #854F0B; }
    .kpi-icon.green  { background: #EAF3DE; color: #3B6D11; }

    .kpi-label { font-size: 11px; color: #6b7280; margin-bottom: 4px; }
    .kpi-value { font-size: 22px; font-weight: 500; color: #111827; line-height: 1.1; }
    .kpi-unit  { font-size: 11px; color: #9ca3af; margin-top: 2px; }

    /* ── Performance Cards ── */
    .performance-card {
        background: #f9fafb; border: 0.5px solid #e5e7eb;
        border-radius: 8px; padding: 14px; text-align: center;
        transition: background 0.15s;
    }
    .performance-card:hover { background: #f3f4f6; }
    .performance-icon { font-size: 22px; margin-bottom: 6px; }
    .performance-label { font-size: 11px; color: #6b7280; margin-bottom: 4px; text-transform: uppercase; }
    .performance-value { font-size: 20px; font-weight: 500; color: #111827; }
    .performance-unit { font-size: 11px; color: #6b7280; margin-top: 4px; }

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
    .chart-container {
        position: relative;
        height: 400px;
        margin-bottom: 30px;
    }

    /* ── Grid Layout ── */
    .perf-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
        margin-bottom: 1.5rem;
    }
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 10px;
        margin-bottom: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="dash-page">
    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div>
            <h2 class="page-title">Dashboard Laporan</h2>
            <div class="page-subtitle">Monitoring Aktivitas Laporan Harian</div>
        </div>
        <div class="header-actions">
            <a href="{{ route('laporan.import-form') }}" class="filter-btn">
                <i class="bi bi-upload"></i> Import Excel
            </a>
            <a href="{{ route('laporan.create') }}" class="filter-btn" style="background: #3B6D11;">
                <i class="bi bi-plus-circle"></i> Laporan Baru
            </a>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="filter-section">
        <form method="GET" action="{{ route('laporan.index') }}" style="display:contents">
            {{-- Mode toggle --}}
            <div class="filter-group">
                <span class="form-label">Mode</span>
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
                    <span class="form-label">Bulan</span>
                    <select name="bulan" id="bulan">
                        @php
                            $bulanList = [
                                1 => 'January',
                                2 => 'February',
                                3 => 'March',
                                4 => 'April',
                                5 => 'May',
                                6 => 'June',
                                7 => 'July',
                                8 => 'August',
                                9 => 'September',
                                10 => 'October',
                                11 => 'November',
                                12 => 'December'
                            ];
                        @endphp
                        @foreach($bulanList as $m => $bulanName)
                            <option value="{{ $m }}" @if($bulan == $m) selected @endif>
                                {{ $bulanName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group">
                    <span class="form-label">Tahun</span>
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
                    <span class="form-label">Dari Tanggal</span>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal', date('Y-m-01')) }}">
                </div>
                <div class="filter-group">
                    <span class="form-label">Sampai Tanggal</span>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal', date('Y-m-d')) }}">
                </div>
            </div>
            
            <div class="filter-group">
                <span class="form-label">Mesin</span>
                <select name="mesin" id="mesin">
                    <option value="">-- Semua Mesin --</option>
                    @foreach($allMesins as $m)
                        <option value="{{ $m }}" @if($mesin == $m) selected @endif>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-group">
                <span class="form-label">Line</span>
                <select name="line" id="line">
                    <option value="">-- Semua Line --</option>
                    @foreach($allLines as $l)
                        <option value="{{ $l }}" @if($line == $l) selected @endif>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group" style="justify-content:flex-end">
                <span class="form-label">Semua Data</span>
                <div style="display:flex;align-items:center;height:34px">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="all_time" id="all_time" value="1"
                            @if(request('all_time') == '1') checked @endif>
                    </div>
                </div>
            </div>

            <button type="submit" class="filter-btn">
                <i class="bi bi-funnel"></i> Terapkan
            </button>
        </form>
</div>

    {{-- ── Info Banner ── --}}
    <div class="info-banner">
        <i class="bi bi-info-circle-fill"></i>
        <span><strong>Dashboard Laporan</strong> — Monitoring komprehensif semua aktivitas laporan harian Anda.</span>
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
    </div>

    {{-- ── Machine Performance ── --}}
    <div class="section-title"><i class="bi bi-cpu"></i> Machine Performance</div>
    <div class="perf-grid">
        <div class="performance-card">
            <div class="performance-icon" style="color:#185FA5"><i class="bi bi-calendar-check"></i></div>
            <div class="performance-label">Planned Time</div>
            <div class="performance-value">{{ number_format(($totalPlannedTime ?? 0) / 60, 2) }}</div>
            <div class="performance-unit">jam</div>
        </div>
        <div class="performance-card">
            <div class="performance-icon" style="color:#A32D2D"><i class="bi bi-exclamation-circle"></i></div>
            <div class="performance-label">Down Time</div>
            <div class="performance-value">{{ number_format(($totalDowntimeMinutes ?? 0) / 60, 2) }}</div>
            <div class="performance-unit">jam</div>
        </div>
        <div class="performance-card">
            <div class="performance-icon" style="color:#3B6D11"><i class="bi bi-play-circle"></i></div>
            <div class="performance-label">Operation Time</div>
            <div class="performance-value">{{ number_format((($totalPlannedTime ?? 0) - ($totalDowntimeMinutes ?? 0)) / 60, 2) }}</div>
            <div class="performance-unit">jam</div>
        </div>
        <div class="performance-card">
            <div class="performance-icon" style="color:#A32D2D"><i class="bi bi-bug"></i></div>
            <div class="performance-label">Breakdown</div>
            <div class="performance-value">{{ $totalBreakdown ?? 0 }}</div>
            <div class="performance-unit">kejadian</div>
        </div>
        <div class="performance-card">
            <div class="performance-icon" style="color:#854F0B"><i class="bi bi-wrench"></i></div>
            <div class="performance-label">Corrective Maint.</div>
            <div class="performance-value">{{ number_format($totalCorrectiveMaint ?? 0, 2) }}</div>
            <div class="performance-unit">jam</div>
        </div>
        <div class="performance-card">
            <div class="performance-icon" style="color:#534AB7"><i class="bi bi-shield-check"></i></div>
            <div class="performance-label">Preventive Maint.</div>
            <div class="performance-value">{{ number_format($totalPreventiveMaint ?? 0, 2) }}</div>
            <div class="performance-unit">jam</div>
        </div>
        <div class="performance-card">
            <div class="performance-icon" style="color:#0F6E56"><i class="bi bi-arrow-repeat"></i></div>
            <div class="performance-label">Change Over</div>
            <div class="performance-value">{{ number_format($totalChangeOver ?? 0, 2) }}</div>
            <div class="performance-unit">jam</div>
        </div>
    </div>

    {{-- ── Summary Cards ── --}}
    <div class="section-title"><i class="bi bi-file-earmark-text"></i> Ringkasan Data</div>
    <div class="kpi-grid">
        <div class="kpi-card blue">
            <div class="kpi-icon blue"><i class="bi bi-file-text"></i></div>
            <div class="kpi-label">Total Laporan</div>
            <div class="kpi-value">{{ $totalLaporan }}</div>
            <div class="kpi-unit">laporan</div>
        </div>
        <div class="kpi-card red">
            <div class="kpi-icon red"><i class="bi bi-clock"></i></div>
            <div class="kpi-label">Total Downtime</div>
            <div class="kpi-value">{{ number_format($totalDowntime) }}</div>
            <div class="kpi-unit">menit</div>
        </div>
        <div class="kpi-card amber">
            <div class="kpi-icon amber"><i class="bi bi-hourglass"></i></div>
            <div class="kpi-label">Jam Downtime</div>
            <div class="kpi-value">{{ number_format($totalDowntime / 60, 2) }}</div>
            <div class="kpi-unit">jam</div>
        </div>
    </div>

<!-- MTBF Metrics Section -->
<div style="margin-bottom: 1.5rem;">
    <div class="section-title"><i class="bi bi-speedometer2"></i> MTBF (Mean Time Between Failures) Analysis</div>
    <div class="perf-grid">
        <div class="performance-card">
            <div class="performance-icon" style="color: #185FA5"><i class="bi bi-graph-up"></i></div>
            <div class="performance-label">Average MTBF</div>
            <div class="performance-value">{{ number_format($avgMTBFHours, 2) }}</div>
            <div class="performance-unit">jam</div>
        </div>
        <div class="performance-card">
            <div class="performance-icon" style="color: #3B6D11"><i class="bi bi-check-circle"></i></div>
            <div class="performance-label">Machines with Data</div>
            <div class="performance-value">{{ count($mtbfData) }}</div>
            <div class="performance-unit">mesin</div>
        </div>
        <div class="performance-card">
            <div class="performance-icon" style="color: #854F0B"><i class="bi bi-table"></i></div>
            <div class="performance-label">View Details</div>
            <a href="{{ route('laporan.index') }}" class="filter-btn" style="margin-top: 10px; width: 100%; text-align: center;">
                <i class="bi bi-arrow-repeat"></i> Refresh
            </a>
        </div>
    </div>
</div>

<!-- Top Reliable & Worst Machines -->
<div class="two-col">
    <div class="dash-card">
        <div class="dash-card-header">
            <span><i class="bi bi-trophy"></i> Top 5 Most Reliable Machines</span>
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
                                $failureCount = $machine['failure_count'] ?? 0;
                                $mtbfHours = $machine['mtbf_hours'] ?? 0;
                                $downtimeHours = $machine['total_downtime_hours'] ?? 0;
                                
                                if ($failureCount == 0) {
                                    $pillClass = 'pill-excellent'; $pillLabel = 'Excellent';
                                } elseif ($failureCount == 1 && $downtimeHours < 1) {
                                    $pillClass = 'pill-excellent'; $pillLabel = 'Excellent';
                                } elseif ($failureCount <= 2 && $downtimeHours < 4) {
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
                                    <span class="dash-badge badge-red">{{ $failureCount }}</span>
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
            <span><i class="bi bi-exclamation-triangle"></i> Bottom 5 Worst Performing Machines</span>
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
                                $failureCount = $machine['failure_count'] ?? 0;
                                $mtbfHours = $machine['mtbf_hours'] ?? 0;
                                $downtimeHours = $machine['total_downtime_hours'] ?? 0;
                                
                                if ($failureCount <= 2 && $downtimeHours < 4) {
                                    $pillClass = 'pill-good'; $pillLabel = 'Good';
                                } elseif ($failureCount <= 5 && $downtimeHours < 12) {
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
                                    <span class="dash-badge badge-red">{{ $failureCount }}</span>
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

<!-- Top 10 & Top 7 Tables -->
<div class="two-col">
    <div class="dash-card">
        <div class="dash-card-header">Top 10 Mesin — Downtime Tertinggi</div>
        <div class="dash-card-body">
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
                            <td>{{ number_format($item->total_downtime / 60, 2) }}</td>
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

<!-- Top 7 Breakdown Catatan + Spare Part -->
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

<!-- Charts Row -->
<div class="two-col">
    <div class="dash-card">
        <div class="dash-card-header">Top 10 Mesin dengan Downtime Tertinggi (Chart)</div>
        <div class="dash-card-body">
            @if(count($topDowntimeMesin) > 0)
            <div class="chart-wrap" style="height: {{ max(count($topDowntimeMesin) * 40 + 60, 200) }}px">
                <canvas id="topDowntimeChart"></canvas>
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
                <canvas id="machinePerformanceChart"></canvas>
            </div>
            @else
            <p style="color:#9ca3af;font-size:13px;text-align:center;padding:2rem 0">Tidak ada data</p>
            @endif
        </div>
    </div>
</div>

@endsection

@section('extra-js')
<script>
    function initCharts() {
        const topDowntimeMesinDataRaw = {!! json_encode($topDowntimeMesin->pluck('total_downtime')->toArray()) !!};
        const topDowntimeMesinData = topDowntimeMesinDataRaw.map(x => (x / 60).toFixed(2));
        const topDowntimeMesinLabels = {!! json_encode($topDowntimeMesin->pluck('mesin_name')->toArray()) !!};
        
        const machinePerformanceData = {!! json_encode($machinePerformance->pluck('count')->toArray()) !!};
        const machinePerformanceLabels = {!! json_encode($machinePerformance->pluck('mesin_name')->toArray()) !!};

        if (topDowntimeMesinData.length > 0) {
            const topDowntimeCtx = document.getElementById('topDowntimeChart');
            if (topDowntimeCtx) {
                new Chart(topDowntimeCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: topDowntimeMesinLabels,
                        datasets: [{
                            label: 'Downtime (Jam)',
                            data: topDowntimeMesinData,
                            backgroundColor: 'rgba(67, 97, 238, 0.8)',
                            borderColor: '#4361ee',
                            borderWidth: 2,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }
        }

        if (machinePerformanceData.length > 0) {
            const machinePerformanceCtx = document.getElementById('machinePerformanceChart');
            if (machinePerformanceCtx) {
                new Chart(machinePerformanceCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: machinePerformanceLabels,
                        datasets: [{
                            data: machinePerformanceData,
                            backgroundColor: [
                                'rgba(67, 97, 238, 0.9)',
                                'rgba(107, 140, 255, 0.9)',
                                'rgba(52, 211, 153, 0.9)',
                                'rgba(244, 63, 94, 0.9)',
                                'rgba(255, 159, 28, 0.9)',
                                'rgba(99, 102, 241, 0.9)',
                                'rgba(139, 92, 246, 0.9)',
                                'rgba(6, 182, 212, 0.9)',
                                'rgba(34, 197, 94, 0.9)',
                                'rgba(59, 130, 246, 0.9)'
                            ],
                            borderWidth: 2,
                            borderColor: 'rgba(255, 255, 255, 0.8)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }
        }
    }

    function waitForChart() {
        if (typeof Chart !== 'undefined') {
            initCharts();
        } else {
            setTimeout(waitForChart, 100);
        }
    }

    waitForChart();

    // ── Filter mode toggle ──
    function toggleFilterMode() {
        const mode = document.querySelector('input[name="filter_mode"]:checked').value;
        document.getElementById('month_year_wrap').style.display = mode === 'date_range' ? 'none' : 'contents';
        document.getElementById('date_range_wrap').style.display = mode === 'date_range' ? 'contents' : 'none';
    }
</script>
@endsection
