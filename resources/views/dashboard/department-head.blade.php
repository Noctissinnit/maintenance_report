@extends('layouts.app')

@section('title', 'Department Head Dashboard - Sistem Laporan Maintenance')

@section('extra-css')
<style>
    .chart-container {
        position: relative;
        height: 400px;
        margin-bottom: 30px;
    }
    
    .filter-section {
        background: linear-gradient(135deg, rgba(67, 97, 238, 0.05), rgba(107, 140, 255, 0.05));
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 30px;
        border: 1px solid rgba(67, 97, 238, 0.2);
    }
    
    .filter-section .form-label {
        color: #333;
        font-weight: 600;
        font-size: 0.95rem;
    }
    
    .filter-section select, .filter-section input {
        border-color: #d1d9e8;
        border-radius: 0.625rem;
        font-size: 0.95rem;
    }
    
    .filter-section select:focus, .filter-section input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
    }
    
    .filter-btn {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        border: none;
        color: white;
        font-weight: 600;
        border-radius: 0.625rem;
        transition: all 0.3s ease;
    }
    
    .filter-btn:hover {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        color: white;
    }

    .performance-card {
        border: 1px solid #e8ecf1;
        border-radius: 0.75rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
    }

    .performance-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .performance-card:hover {
        box-shadow: 0 8px 16px rgba(67, 97, 238, 0.15);
        transform: translateY(-4px);
        border-color: var(--primary-color);
    }

    .performance-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
    }

    .performance-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        line-height: 1;
    }

    .performance-unit {
        font-size: 0.75rem;
        color: #999;
        margin-top: 0.5rem;
    }

    .performance-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        display: inline-block;
        opacity: 0.9;
        transition: all 0.3s ease;
    }

    .performance-card:hover .performance-icon {
        transform: scale(1.15) translateY(-2px);
        opacity: 1;
    }

    .performance-card:nth-child(2) .performance-icon {
        color: #4361ee;
    }

    .performance-card:nth-child(3) .performance-icon {
        color: #dc3545;
    }

    .performance-card:nth-child(4) .performance-icon {
        color: #ffc107;
    }

    .performance-card:nth-child(5) .performance-icon {
        color: #e83e8c;
    }

    .performance-card:nth-child(6) .performance-icon {
        color: #17a2b8;
    }

    .performance-card:nth-child(7) .performance-icon {
        color: #28a745;
    }

    .performance-card:nth-child(8) .performance-icon {
        color: #6610f2;
    }

    .performance-card:nth-child(9) .performance-icon {
        color: #fd7e14;
    }
</style>
@endsection

@section('content')
<h2 class="mb-4">Department Head Dashboard - Monitoring Semua Aktivitas</h2>

<!-- Filter Section -->
<div class="filter-section">
    <form method="GET" action="{{ route('dashboard') }}" class="row g-3">
        <div class="col-md-2">
            <label for="bulan" class="form-label">Bulan</label>
            <select name="bulan" id="bulan" class="form-select">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @if($bulan == $m) selected @endif>
                        {{ \Carbon\Carbon::createFromFormat('n', $m)->format('F') }}
                    </option>
                @endfor
            </select>
        </div>
        
        <div class="col-md-2">
            <label for="tahun" class="form-label">Tahun</label>
            <select name="tahun" id="tahun" class="form-select">
                @for($y = 2024; $y <= 2026; $y++)
                    <option value="{{ $y }}" @if($tahun == $y) selected @endif>{{ $y }}</option>
                @endfor
            </select>
        </div>
        
        <div class="col-md-3">
            <label for="mesin" class="form-label">Mesin</label>
            <select name="mesin" id="mesin" class="form-select">
                <option value="">-- Semua Mesin --</option>
                @foreach($allMesins as $m)
                    <option value="{{ $m }}" @if($mesin == $m) selected @endif>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-2">
            <label for="line" class="form-label">Line</label>
            <select name="line" id="line" class="form-select">
                <option value="">-- Semua Line --</option>
                @foreach($allLines as $l)
                    <option value="{{ $l }}" @if($line == $l) selected @endif>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn filter-btn w-100">
                <i class="bi bi-funnel"></i> Filter Data
            </button>
        </div>
    </form>
</div>

<!-- Alert Box -->
<div class="alert alert-info mb-4" role="alert">
    <i class="bi bi-info-circle"></i> 
    <strong>Department Head</strong> - Anda memiliki akses monitoring untuk melihat semua dashboard kegiatan laporan harian dan supervisor.
</div>

<!-- KPI Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Availability</div>
            <div class="kpi-value">{{ number_format($availability, 2) }}%</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Downtime</div>
            <div class="kpi-value">{{ number_format($downtimePercent, 2) }}%</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Rata-rata MTTR</div>
            <div class="kpi-value">{{ number_format($avgMTTR, 2) }}</div>
            <div class="kpi-label">menit</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <div class="kpi-label">Rata-rata MTBF</div>
            <div class="kpi-value">{{ number_format($avgMTBF, 2) }}</div>
            <div class="kpi-label">menit</div>
        </div>
    </div>
</div>

<!-- Machine Performance KPI Section -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-3">
            <i class="bi bi-speedometer2" style="color: var(--primary-color);"></i> 
            <span style="color: var(--text-dark); font-weight: 600;">Machine Performance</span>
        </h5>
    </div>
    <div class="col-md-3">
        <div class="card performance-card">
            <div class="card-body text-center">
                <div class="performance-icon"><i class="bi bi-calendar-check"></i></div>
                <div class="performance-label">Planned Time</div>
                <div class="performance-value">{{ number_format($totalPlannedTime ?? 0) }}</div>
                <div class="performance-unit">jam</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card performance-card">
            <div class="card-body text-center">
                <div class="performance-icon"><i class="bi bi-exclamation-circle"></i></div>
                <div class="performance-label">Down Time</div>
                <div class="performance-value">{{ number_format(($totalDowntime ?? 0) / 60, 2) }}</div>
                <div class="performance-unit">jam</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card performance-card">
            <div class="card-body text-center">
                <div class="performance-icon"><i class="bi bi-play-circle"></i></div>
                <div class="performance-label">Operation Time</div>
                <div class="performance-value">{{ number_format(((($totalPlannedTime ?? 0) * 60) - ($totalDowntime ?? 0)) / 60, 2) }}</div>
                <div class="performance-unit">jam</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card performance-card">
            <div class="card-body text-center">
                <div class="performance-icon"><i class="bi bi-bug"></i></div>
                <div class="performance-label">Breakdown</div>
                <div class="performance-value">{{ $totalBreakdown ?? 0 }}</div>
                <div class="performance-unit">kejadian</div>
            </div>
        </div>
    </div>
</div>

<!-- More Performance Metrics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card performance-card">
            <div class="card-body text-center">
                <div class="performance-icon"><i class="bi bi-wrench"></i></div>
                <div class="performance-label">Corrective Maintenance</div>
                <div class="performance-value">{{ number_format($totalCorrectiveMaint ?? 0, 2) }}</div>
                <div class="performance-unit">jam</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card performance-card">
            <div class="card-body text-center">
                <div class="performance-icon"><i class="bi bi-shield-check"></i></div>
                <div class="performance-label">Preventive Maintenance</div>
                <div class="performance-value">{{ number_format($totalPreventiveMaint ?? 0, 2) }}</div>
                <div class="performance-unit">jam</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card performance-card">
            <div class="card-body text-center">
                <div class="performance-icon"><i class="bi bi-lightning"></i></div>
                <div class="performance-label">Predictive</div>
                <div class="performance-value">{{ number_format($totalPredictive ?? 0, 2) }}</div>
                <div class="performance-unit">jam</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card performance-card">
            <div class="card-body text-center">
                <div class="performance-icon"><i class="bi bi-arrow-repeat"></i></div>
                <div class="performance-label">Change Over Product</div>
                <div class="performance-value">{{ number_format($totalChangeOver ?? 0, 2) }}</div>
                <div class="performance-unit">jam</div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Total Laporan</h5>
                <h2 style="color: var(--primary-color);">{{ $totalLaporan }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Total Downtime</h5>
                <h2 style="color: var(--secondary-color);">{{ number_format($totalDowntime) }} menit</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Jam Downtime</h5>
                <h2 style="color: var(--warning-color);">{{ number_format($totalDowntime / 60, 2) }} jam</h2>
            </div>
        </div>
    </div>
</div>

<!-- Top 10 & Top 7 Tables Row 1 -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Top 10 Mesin dengan Downtime Tertinggi</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Mesin</th>
                                <th>Down Time (Jam)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topDowntimeMesin as $item)
                                <tr>
                                    <td>{{ $item->mesin_name }}</td>
                                    <td>{{ number_format($item->total_downtime / 60, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Top 7 Breakdown Per Line</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Line</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topBreakdownLine as $item)
                                <tr>
                                    <td>{{ $item->line }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ $item->breakdown_count }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top 7 Breakdown Catatan -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Top 7 Breakdown - Jenis Kerusakan</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Kerusakan</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topBreakdownCatatan as $item)
                                <tr>
                                    <td>{{ $item->catatan ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ $item->breakdown_count }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Monitoring Spare Part -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Monitoring Spare Part ({{ \Carbon\Carbon::createFromFormat('n', $bulan)->format('F') }} {{ $tahun }})</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Spare Part</th>
                                <th>Total Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($spareParts as $item)
                                <tr>
                                    <td>{{ $item->sparepart ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $item->total_qty }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Top 10 Mesin dengan Downtime Tertinggi (Chart)</div>
            <div class="card-body">
                @if(count($topDowntimeMesin) > 0)
                <div class="chart-container">
                    <canvas id="topDowntimeChart"></canvas>
                </div>
                @else
                <p class="text-center text-muted">Tidak ada data downtime mesin</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Machine Performance</div>
            <div class="card-body">
                @if(count($machinePerformance) > 0)
                <div class="chart-container">
                    <canvas id="machinePerformanceChart"></canvas>
                </div>
                @else
                <p class="text-center text-muted">Tidak ada data mesin</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('extra-js')
<script>
    function initCharts() {
        // Data dari controller (convert menit ke jam)
        const topDowntimeMesinDataRaw = {!! json_encode($topDowntimeMesin->pluck('total_downtime')->toArray()) !!};
        const topDowntimeMesinData = topDowntimeMesinDataRaw.map(x => (x / 60).toFixed(2));
        const topDowntimeMesinLabels = {!! json_encode($topDowntimeMesin->pluck('mesin_name')->toArray()) !!};
        
        const machinePerformanceData = {!! json_encode($machinePerformance->pluck('count')->toArray()) !!};
        const machinePerformanceLabels = {!! json_encode($machinePerformance->pluck('mesin_name')->toArray()) !!};

        // Top Downtime Chart (in hours)
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

        // Machine Performance Chart
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

    // Polling sampai Chart tersedia
    function waitForChart() {
        if (typeof Chart !== 'undefined') {
            initCharts();
        } else {
            setTimeout(waitForChart, 100);
        }
    }

    waitForChart();
</script>
@endsection
