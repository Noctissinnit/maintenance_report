@extends('layouts.app')

@section('title', 'Operator Dashboard - Sistem Laporan Maintenance')

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

    /* ── Grid Layouts ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 10px;
        margin-bottom: 1.5rem;
    }
    .perf-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
        margin-bottom: 1.5rem;
    }

    .table-wrap { max-height: 400px; overflow-y: auto; }
</style>
@endsection

@section('content')
<div class="dash-page">
    <div class="page-header">
        <div>
            <h2 class="page-title">Dashboard Operator</h2>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card blue">
            <div class="kpi-icon blue"><i class="bi bi-file-text"></i></div>
            <div class="kpi-label">Total Laporan</div>
            <div class="kpi-value">{{ $totalLaporan }}</div>
            <div class="kpi-unit">Laporan Saya</div>
        </div>

        <div class="kpi-card amber">
            <div class="kpi-icon amber"><i class="bi bi-calendar-check"></i></div>
            <div class="kpi-label">Laporan Harian</div>
            <div class="kpi-value">{{ $totalLaporanHarian }}</div>
            <div class="kpi-unit">Tipe Harian</div>
        </div>

        <div class="kpi-card blue">
            <div class="kpi-icon blue"><i class="bi bi-calendar3"></i></div>
            <div class="kpi-label">Laporan Mingguan</div>
            <div class="kpi-value">{{ $totalLaporanMingguan }}</div>
            <div class="kpi-unit">Tipe Mingguan</div>
        </div>

        <div class="kpi-card red">
            <div class="kpi-icon red"><i class="bi bi-exclamation-circle"></i></div>
            <div class="kpi-label">Total Downtime</div>
            <div class="kpi-value">{{ number_format($totalDowntime, 0) }}</div>
            <div class="kpi-unit">Menit</div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="perf-grid">
        <div class="performance-card">
            <div class="performance-icon"><i class="bi bi-clock-history"></i></div>
            <div class="performance-label">Rata-rata Downtime</div>
            <div class="performance-value">{{ number_format($avgDowntime, 2) }}</div>
            <div class="performance-unit">Menit</div>
        </div>

        <div class="performance-card">
            <div class="performance-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="performance-label">Total Jam Downtime</div>
            <div class="performance-value">{{ number_format($totalDowntime / 60, 2) }}</div>
            <div class="performance-unit">Jam</div>
        </div>
    </div>

    <!-- Charts and Tables Row -->
    <div class="two-col">
        <div class="dash-card">
            <div class="dash-card-header">
                <span>Laporan per Mesin</span>
            </div>
            <div class="dash-card-body">
                <canvas id="laporanPerMesinChart"></canvas>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-header">
                <span>Laporan per Tipe</span>
            </div>
            <div class="dash-card-body">
                <canvas id="laporanPerTipeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Tables -->
    <div class="two-col">
        <div class="dash-card">
            <div class="dash-card-header">
                <span>Detail Laporan per Mesin</span>
            </div>
            <div class="dash-card-body">
                <div class="table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Mesin</th>
                                <th class="td-center">Laporan</th>
                                <th class="td-center">Downtime</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporanPerMesin as $item)
                                <tr>
                                    <td class="td-name">{{ $item->mesin_name }}</td>
                                    <td class="td-center">
                                        <span class="dash-badge badge-blue">{{ $item->count }}</span>
                                    </td>
                                    <td class="td-center">
                                        <span class="dash-badge badge-amber">{{ $item->total_downtime ?? 0 }} min</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="td-center" style="color: #999; padding: 12px;">Belum ada laporan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-header">
                <span>Laporan 5 Terbaru</span>
            </div>
            <div class="dash-card-body">
                <div class="table-wrap">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Mesin</th>
                                <th>Tipe</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestLaporan as $laporan)
                                <tr>
                                    <td>
                                        <small class="td-sub">{{ $laporan->tanggal_laporan->format('d-m-Y') }}</small>
                                    </td>
                                    <td class="td-name">
                                        <small>{{ $laporan->mesin_name }}</small>
                                    </td>
                                    <td>
                                        <span class="dash-badge badge-blue">{{ ucfirst($laporan->tipe_laporan) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="td-center" style="color: #999; padding: 12px;">Belum ada laporan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('extra-js')
<script>
    // Laporan per Mesin Chart
    const laporanPerMesinCtx = document.getElementById('laporanPerMesinChart').getContext('2d');
    new Chart(laporanPerMesinCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($laporanPerMesin as $item)
                    '{{ $item->mesin_name }}',
                @endforeach
            ],
            datasets: [{
                label: 'Jumlah Laporan',
                data: [
                    @foreach($laporanPerMesin as $item)
                        {{ $item->count }},
                    @endforeach
                ],
                backgroundColor: 'rgba(50, 102, 173, 0.6)',
                borderColor: 'rgba(50, 102, 173, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Laporan per Tipe Chart
    const laporanPerTipeCtx = document.getElementById('laporanPerTipeChart').getContext('2d');
    new Chart(laporanPerTipeCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($laporanPerTipe as $item)
                    '{{ ucfirst($item->tipe_laporan) }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($laporanPerTipe as $item)
                        {{ $item->count }},
                    @endforeach
                ],
                backgroundColor: [
                    'rgba(50, 102, 173, 0.8)',
                    'rgba(163, 45, 45, 0.8)',
                    'rgba(186, 117, 23, 0.8)',
                    'rgba(59, 109, 17, 0.8)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection
