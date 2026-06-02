@extends('layouts.app')

@section('title', 'Daftar Laporan Saya - Sistem Laporan Maintenance')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-list-ul"></i> Daftar Laporan Saya</h2>
            <p class="text-muted">Lihat semua laporan maintenance yang telah Anda inputkan</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('laporan.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Input Laporan Baru
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.list') }}" class="row g-3">
                <!-- Search -->
                <div class="col-md-3">
                    <label class="form-label">Cari</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Mesin, catatan, line..." value="{{ $search ?? '' }}">
                </div>

                <!-- Bulan -->
                <div class="col-md-2">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">-- Semua Bulan --</option>
                        @foreach($bulanList as $num => $name)
                            <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tahun -->
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="tahun" class="form-control" 
                           value="{{ $tahun }}" min="2024" max="2099">
                </div>

                <!-- Mesin -->
                <div class="col-md-2">
                    <label class="form-label">Mesin</label>
                    <select name="mesin" class="form-select">
                        <option value="">-- Semua Mesin --</option>
                        @foreach($allMesins as $m)
                            <option value="{{ $m }}" {{ $mesin == $m ? 'selected' : '' }}>
                                {{ $m }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Line -->
                <div class="col-md-2">
                    <label class="form-label">Line</label>
                    <select name="line" class="form-select">
                        <option value="">-- Semua Line --</option>
                        @foreach($allLines as $l)
                            <option value="{{ $l }}" {{ $line == $l ? 'selected' : '' }}>
                                {{ $l }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('laporan.list') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total Laporan</small>
                            <h4 class="mb-0">{{ $laporan->total() }}</h4>
                        </div>
                        <i class="bi bi-file-text fs-3 text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Preventive</small>
                            <h4 class="mb-0">
                                @php
                                    $preventiveCount = \App\Models\LaporanHarian::query()
                                        ->when(!Auth::user()->hasRole('admin'), function($q) {
                                            return $q->where('user_id', Auth::id());
                                        })
                                        ->where('jenis_pekerjaan', 'preventive')
                                        ->count();
                                @endphp
                                {{ $preventiveCount }}
                            </h4>
                        </div>
                        <i class="bi bi-check-circle fs-3 text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Corrective</small>
                            <h4 class="mb-0">
                                @php
                                    $correctiveCount = \App\Models\LaporanHarian::query()
                                        ->when(!Auth::user()->hasRole('admin'), function($q) {
                                            return $q->where('user_id', Auth::id());
                                        })
                                        ->where('jenis_pekerjaan', 'corrective')
                                        ->count();
                                @endphp
                                {{ $correctiveCount }}
                            </h4>
                        </div>
                        <i class="bi bi-exclamation-circle fs-3 text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Change Over</small>
                            <h4 class="mb-0">
                                @php
                                    $changeoverCount = \App\Models\LaporanHarian::query()
                                        ->when(!Auth::user()->hasRole('admin'), function($q) {
                                            return $q->where('user_id', Auth::id());
                                        })
                                        ->where('jenis_pekerjaan', 'change over product')
                                        ->count();
                                @endphp
                                {{ $changeoverCount }}
                            </h4>
                        </div>
                        <i class="bi bi-arrow-repeat fs-3 text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="12%">Tanggal</th>
                        <th width="15%">Mesin</th>
                        <th width="10%">Line</th>
                        <th width="12%">Jenis Pekerjaan</th>
                        <th width="8%">Downtime (menit)</th>
                        <th width="10%">Planned Time</th>
                        <th width="20%">Catatan</th>
                        <th width="13%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $item)
                        <tr>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($item->tanggal_laporan)->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <strong>{{ $item->mesin_name }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $item->line }}</span>
                            </td>
                            <td>
                                @php
                                    $jenisColor = match($item->jenis_pekerjaan) {
                                        'preventive' => 'success',
                                        'corrective' => 'danger',
                                        'change over product' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $jenisColor }}">
                                    {{ ucfirst($item->jenis_pekerjaan) }}
                                </span>
                            </td>
                            <td>
                                @if($item->downtime_min > 0)
                                    <span class="text-danger fw-bold">{{ $item->downtime_min }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->planned_time_minutes)
                                    <span class="badge bg-primary">{{ $item->planned_time_minutes }} min</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 150px;" 
                                       title="{{ $item->catatan }}">
                                    {{ $item->catatan ?? '-' }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('laporan.show', $item->id) }}" 
                                       class="btn btn-outline-info" title="Lihat">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('laporan.edit', $item->id) }}" 
                                       class="btn btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('laporan.destroy', $item->id) }}" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Hapus laporan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-inbox fs-4 text-muted d-block mb-2"></i>
                                <p class="text-muted mb-0">Belum ada laporan yang ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($laporan->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Menampilkan {{ $laporan->firstItem() }} - {{ $laporan->lastItem() }} 
                        dari {{ $laporan->total() }} laporan
                    </small>
                    {{ $laporan->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .btn-group-sm .btn {
        padding: 0.35rem 0.65rem;
        font-size: 0.875rem;
    }

    .card {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.05);
    }
</style>
@endsection
