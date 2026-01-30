@extends('layouts.app')

@section('title', 'Admin Dashboard - Sistem Laporan Maintenance')

@section('content')
<h2 class="mb-4">Admin Dashboard</h2>

<!-- KPI Cards -->
<div class="row mb-4">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="kpi-card" style="background: linear-gradient(135deg, #2d5016, #558b2f);">
            <div class="kpi-label">Total Users</div>
            <div class="kpi-value">{{ $totalUsers }}</div>
            <small>Pengguna Sistem</small>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="kpi-card" style="background: linear-gradient(135deg, #7cb342, #9ccc65);">
            <div class="kpi-label">Total Laporan</div>
            <div class="kpi-value">{{ $totalLaporan }}</div>
            <small>Laporan Masuk</small>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="kpi-card" style="background: linear-gradient(135deg, #ff9800, #ffb74d);">
            <div class="kpi-label">Total Downtime</div>
            <div class="kpi-value">{{ number_format($totalDowntime, 0) }}</div>
            <small>Menit</small>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="kpi-card" style="background: linear-gradient(135deg, #f44336, #ef5350);">
            <div class="kpi-label">Downtime Jam</div>
            <div class="kpi-value">{{ number_format($totalDowntime / 60, 1) }}</div>
            <small>Jam</small>
        </div>
    </div>
</div>

<!-- User Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Admin Users</h5>
            </div>
            <div class="card-body text-center">
                <h2 style="color: #2d5016;">{{ $adminCount }}</h2>
                <small class="text-muted">Pengguna Admin</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Department Head Users</h5>
            </div>
            <div class="card-body text-center">
                <h2 style="color: #7cb342;">{{ $departmentHeadCount }}</h2>
                <small class="text-muted">Pengguna Department Head</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Operator Users</h5>
            </div>
            <div class="card-body text-center">
                <h2 style="color: #ff9800;">{{ $operatorCount }}</h2>
                <small class="text-muted">Pengguna Operator</small>
            </div>
        </div>
    </div>
</div>

<!-- Latest Reports -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Laporan Terbaru (10 Laporan)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>User</th>
                                <th>Mesin</th>
                                <th>Tipe</th>
                                <th>Downtime</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestLaporan as $laporan)
                                <tr>
                                    <td>{{ $laporan->tanggal_laporan->format('d-m-Y') }}</td>
                                    <td><strong>{{ $laporan->user->name }}</strong></td>
                                    <td>{{ $laporan->mesin_name }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($laporan->tipe_laporan) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $laporan->downtime_min }} min</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Belum ada laporan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top 10 Operator (Laporan Terbanyak)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th class="text-end">Laporan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporanPerUser as $item)
                                <tr>
                                    <td>
                                        <small><strong>{{ $item->user->name }}</strong></small>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ $item->count }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3 small">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
