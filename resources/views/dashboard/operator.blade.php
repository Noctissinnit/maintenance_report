@extends('layouts.app')

@section('title', 'Operator Dashboard - Sistem Laporan Maintenance')

@section('content')
<h2 class="mb-4">Dashboard Operator</h2>

<!-- KPI Cards -->
<div class="row mb-4">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="kpi-card">
            <div class="kpi-label">Total Laporan</div>
            <div class="kpi-value">{{ $totalLaporan }}</div>
            <small>Laporan Saya</small>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="kpi-card" style="background: linear-gradient(135deg, #ff9800, #ffb74d);">
            <div class="kpi-label">Laporan Harian</div>
            <div class="kpi-value">{{ $totalLaporanHarian }}</div>
            <small>Tipe Harian</small>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="kpi-card" style="background: linear-gradient(135deg, #2196f3, #64b5f6);">
            <div class="kpi-label">Laporan Mingguan</div>
            <div class="kpi-value">{{ $totalLaporanMingguan }}</div>
            <small>Tipe Mingguan</small>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="kpi-card" style="background: linear-gradient(135deg, #f44336, #ef5350);">
            <div class="kpi-label">Total Downtime</div>
            <div class="kpi-value">{{ number_format($totalDowntime, 0) }}</div>
            <small>Menit</small>
        </div>
    </div>
</div>

<!-- Statistics Row -->
<div class="row mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Rata-rata Downtime</h5>
                <h3 style="color: #2d5016;">{{ number_format($avgDowntime, 2) }}</h3>
                <small class="text-muted">Menit</small>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Total Jam Downtime</h5>
                <h3 style="color: #ff9800;">{{ number_format($totalDowntime / 60, 2) }}</h3>
                <small class="text-muted">Jam</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Tables Row -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Laporan per Mesin</h5>
            </div>
            <div class="card-body">
                <canvas id="laporanPerMesinChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Laporan per Tipe</h5>
            </div>
            <div class="card-body">
                <canvas id="laporanPerTipeChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Tables -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Detail Laporan per Mesin</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Mesin</th>
                                <th class="text-end">Laporan</th>
                                <th class="text-end">Downtime</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporanPerMesin as $item)
                                <tr>
                                    <td><strong>{{ $item->mesin_name }}</strong></td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ $item->count }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-warning">{{ $item->total_downtime ?? 0 }} min</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Belum ada laporan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Laporan 5 Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead class="table-light">
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
                                        <small>{{ $laporan->tanggal_laporan->format('d-m-Y') }}</small>
                                    </td>
                                    <td>
                                        <small><strong>{{ $laporan->mesin_name }}</strong></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info" style="font-size: 0.7rem;">{{ ucfirst($laporan->tipe_laporan) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3 small">Belum ada laporan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Button -->
<div class="row">
    <div class="col-md-12">
        <a href="{{ route('laporan.create') }}" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-circle"></i> Buat Laporan Baru
        </a>
        <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-lg">
            <i class="bi bi-list"></i> Lihat Semua Laporan
        </a>
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
                backgroundColor: 'rgba(45, 80, 22, 0.6)',
                borderColor: 'rgba(45, 80, 22, 1)',
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
                    'rgba(45, 80, 22, 0.8)',
                    'rgba(124, 179, 66, 0.8)',
                    'rgba(76, 175, 80, 0.8)'
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
