@extends('layouts.app')

@section('title', 'Planned Time Management - Sistem Laporan Maintenance')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-clock-history"></i> Planned Time Management</h2>
            <p class="text-muted">Kelola jadwal planned time produksi (bisa per minggu, akumulasi otomatis)</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('planned-times.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Planned Time
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Accumulation Summary Card -->
    <div class="card mb-4 border-primary">
        <div class="card-body d-flex align-items-center justify-content-between py-3">
            <div>
                <h6 class="mb-0 text-primary">
                    <i class="bi bi-calculator"></i> Total Akumulasi Planned Time
                    @if($yearFilter && $monthFilter)
                        <span class="text-muted fw-normal">({{ $months[$monthFilter] ?? '' }} {{ $yearFilter }})</span>
                    @elseif($yearFilter)
                        <span class="text-muted fw-normal">(Tahun {{ $yearFilter }})</span>
                    @else
                        <span class="text-muted fw-normal">(Semua Data)</span>
                    @endif
                </h6>
            </div>
            <div class="text-end">
                <span class="fs-4 fw-bold text-primary">{{ number_format($totalPlannedMinutes) }}</span>
                <small class="text-muted">menit</small>
                <span class="mx-2 text-muted">|</span>
                <span class="fs-4 fw-bold text-success">{{ number_format($totalPlannedMinutes / 60, 2) }}</span>
                <small class="text-muted">jam</small>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('planned-times.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="year" class="form-label">Filter Tahun</label>
                    <select name="year" id="year" class="form-select">
                        <option value="">-- Semua Tahun --</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" @if($yearFilter == $year) selected @endif>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="month" class="form-label">Filter Bulan</label>
                    <select name="month" id="month" class="form-select">
                        <option value="">-- Semua Bulan --</option>
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" @if($monthFilter == $num) selected @endif>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('planned-times.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Periode</th>
                        <th>Planned Time</th>
                        <th>Jam</th>
                        <th>Keterangan</th>
                        <th>Dibuat Oleh</th>
                        <th>Terakhir Update</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plannedTimes as $index => $record)
                        <tr>
                            <td>{{ $plannedTimes->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $record->start_date?->format('d M Y') ?? '-' }}</strong>
                                <span class="text-muted">s/d</span>
                                <strong>{{ $record->end_date?->format('d M Y') ?? '-' }}</strong>
                            </td>
                            <td>
                                <strong class="text-primary">{{ number_format($record->planned_time_minutes) }}</strong>
                                <small class="text-muted">menit</small>
                            </td>
                            <td>
                                {{ number_format($record->planned_time_minutes / 60, 2) }}
                            </td>
                            <td>
                                @if($record->description)
                                    <span title="{{ $record->description }}" class="d-inline-block text-truncate" style="max-width: 150px;">
                                        {{ $record->description }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $record->creator->name ?? 'System' }}</small>
                            </td>
                            <td>
                                <small>{{ $record->updated_at->format('Y-m-d H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('planned-times.edit', $record->id) }}" class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('planned-times.destroy', $record->id) }}" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> Belum ada data planned time
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($plannedTimes->count() > 0)
                <tfoot class="table-light">
                    <tr>
                        <td colspan="2" class="text-end"><strong>Total Akumulasi:</strong></td>
                        <td>
                            <strong class="text-success">{{ number_format($totalPlannedMinutes) }}</strong>
                            <small class="text-muted">menit</small>
                        </td>
                        <td>
                            <strong class="text-success">{{ number_format($totalPlannedMinutes / 60, 2) }}</strong>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($plannedTimes->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $plannedTimes->appends(request()->query())->links() }}
        </div>
    @endif

    <!-- Info Card -->
    <div class="card mt-4 border-info">
        <div class="card-body">
            <h6 class="text-info"><i class="bi bi-info-circle"></i> Cara Kerja Akumulasi</h6>
            <ul class="mb-0 small text-muted">
                <li>Anda bisa membuat <strong>beberapa record planned time</strong> untuk bulan yang sama (misalnya per minggu).</li>
                <li>Pada dashboard, semua planned time yang berada dalam periode filter akan <strong>dijumlahkan otomatis</strong>.</li>
                <li>Contoh: Minggu 1 = 10.080 menit + Minggu 2 = 10.080 menit → Dashboard menampilkan <strong>20.160 menit</strong>.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
