@extends('layouts.app')

@section('title', 'Daftar Laporan - Sistem Laporan Maintenance')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-gradient d-flex justify-content-between align-items-center border-0" style="background: linear-gradient(135deg, #2c5f2d 0%, #1e3f1f 100%);">
        <h4 class="mb-0 text-white">Daftar Laporan Anda</h4>
        <div>
            <a href="{{ route('laporan.import-form') }}" class="btn btn-light btn-sm fw-semibold">
                <i class="bi bi-upload"></i> Import Excel
            </a>
            <a href="{{ route('laporan.create') }}" class="btn btn-light btn-sm fw-semibold">
                <i class="bi bi-plus-circle"></i> Input Laporan Baru
            </a>
        </div>
    </div>
    
    <!-- Search & Filter Section -->
    <div class="card-body pb-3">
        <form method="GET" action="{{ route('laporan.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari mesin, line, atau catatan..." value="{{ $search }}" />
            </div>
            <div class="col-md-2">
                <input type="text" name="mesin" class="form-control" placeholder="Filter mesin" value="{{ $mesin_filter }}" />
            </div>
            <div class="col-md-2">
                <input type="text" name="line" class="form-control" placeholder="Filter line" value="{{ $line_filter }}" />
            </div>
            <div class="col-md-2">
                <select name="jenis_pekerjaan" class="form-select">
                    <option value="">-- Jenis Pekerjaan --</option>
                    <option value="corrective" {{ $jenis_filter === 'corrective' ? 'selected' : '' }}>Corrective</option>
                    <option value="preventive" {{ $jenis_filter === 'preventive' ? 'selected' : '' }}>Preventive</option>
                    <option value="modifikasi" {{ $jenis_filter === 'modifikasi' ? 'selected' : '' }}>Modifikasi</option>
                    <option value="utility" {{ $jenis_filter === 'utility' ? 'selected' : '' }}>Utility</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="tipe_laporan" class="form-select">
                    <option value="">-- Tipe Laporan --</option>
                    <option value="harian" {{ $tipe_filter === 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="mingguan" {{ $tipe_filter === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulanan" {{ $tipe_filter === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                </select>
            </div>
            <div class="col-md-12 border-top pt-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $start_date }}" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $end_date }}" />
                    </div>
                    <div class="col-md-6 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Result Info -->
    @if(request()->hasAny(['search', 'mesin', 'line', 'jenis_pekerjaan', 'tipe_laporan', 'start_date', 'end_date']))
        <div class="card-body pb-2 pt-0">
            <small class="text-muted">
                <i class="bi bi-info-circle"></i>
                Menampilkan {{ $laporan->total() }} hasil dari pencarian
            </small>
        </div>
    @endif
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 60px;" class="text-center">No</th>
                        <th style="width: 120px;">Tanggal</th>
                        <th>Mesin</th>
                        <th style="width: 130px;">Line</th>
                        <th style="width: 150px;">Tipe Laporan</th>
                        <th style="width: 120px;">Jenis Pekerjaan</th>
                        <th style="width: 120px;" class="text-center">Downtime</th>
                        <th style="width: 180px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $item)
                        <tr class="align-middle">
                            <td class="text-center fw-semibold text-muted">{{ ($laporan->currentPage() - 1) * $laporan->perPage() + $loop->iteration }}</td>
                            <td><span class="fw-semibold">{{ $item->tanggal_laporan->format('d-m-Y') }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $item->mesin_name }}</div>
                                <small class="text-muted">Mesin</small>
                            </td>
                            <td>
                                @if($item->line)
                                    <span class="badge" style="background: linear-gradient(135deg, #27ae60 0%, #229954 100%); color: white;">{{ $item->line }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $tipeLaporanColor = match($item->tipe_laporan) {
                                        'harian' => '#3498db',      // Blue
                                        'mingguan' => '#9b59b6',    // Purple
                                        'bulanan' => '#e74c3c',     // Red
                                        default => '#95a5a6'        // Gray
                                    };
                                @endphp
                                <span class="badge text-white" style="background-color: {{ $tipeLaporanColor }};">{{ ucfirst($item->tipe_laporan) }}</span>
                            </td>
                            <td>
                                @php
                                    $jenisPekerjaanStyle = match($item->jenis_pekerjaan) {
                                        'corrective' => 'background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);',      // Red
                                        'preventive' => 'background: linear-gradient(135deg, #f39c12 0%, #d68910 100%);',     // Orange
                                        'modifikasi' => 'background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);',     // Blue
                                        'utility' => 'background: linear-gradient(135deg, #16a085 0%, #138d75 100%);',        // Teal
                                        default => 'background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);'           // Gray
                                    };
                                @endphp
                                <span class="badge text-white" style="{{ $jenisPekerjaanStyle }}">{{ ucfirst($item->jenis_pekerjaan) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $item->downtime_min }} min</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('laporan.edit', $item->id) }}" class="btn btn-sm btn-outline-warning" title="Edit laporan">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('laporan.destroy', $item->id) }}" method="POST" style="display:inline;" class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus laporan" onclick="return confirm('Yakin ingin menghapus laporan ini?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mt-2">Belum ada laporan</p>
                                <a href="{{ route('laporan.create') }}" class="btn btn-sm btn-success">Buat Laporan Pertama</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($laporan->hasPages())
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    {{-- Previous Page Link --}}
                    @if ($laporan->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">&lsaquo; Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $laporan->previousPageUrl() }}">&lsaquo; Previous</a></li>
                    @endif

                    {{-- Pagination Elements with intelligent range --}}
                    @php
                        $currentPage = $laporan->currentPage();
                        $lastPage = $laporan->lastPage();
                        $start = max(1, $currentPage - 2);
                        $end = min($lastPage, $start + 4);
                        if ($end - $start < 4) {
                            $start = max(1, $end - 4);
                        }
                    @endphp

                    @if ($start > 1)
                        <li class="page-item"><a class="page-link" href="{{ $laporan->url(1) }}">1</a></li>
                        @if ($start > 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                    @endif

                    @foreach ($laporan->getUrlRange($start, $end) as $page => $url)
                        @if ($page == $laporan->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                        <li class="page-item"><a class="page-link" href="{{ $laporan->url($lastPage) }}">{{ $lastPage }}</a></li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($laporan->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $laporan->nextPageUrl() }}">Next &rsaquo;</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next &rsaquo;</span></li>
                    @endif
                </ul>
            </nav>
            <div class="text-center mt-2 text-muted small">
                Halaman {{ $laporan->currentPage() }} dari {{ $laporan->lastPage() }} | Total: {{ $laporan->total() }} laporan
            </div>
        @endif
    </div>
</div>
@endsection
