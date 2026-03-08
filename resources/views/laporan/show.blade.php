@extends('layouts.app')

@section('title', 'Detail Laporan - Sistem Laporan Maintenance')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Detail Laporan Maintenance</h4>
        <div>
            <a href="{{ route('laporan.edit', $laporan->id) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="text-muted">Informasi Umum</h5>
                <table class="table table-sm">
                    <tr>
                        <td class="fw-bold" style="width: 40%;">No. Laporan</td>
                        <td>#{{ $laporan->id }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tanggal Laporan</td>
                        <td>{{ $laporan->tanggal_laporan->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tipe Laporan</td>
                        <td>
                            <span class="badge bg-info">
                                @if($laporan->tipe_laporan === 'harian')
                                    Harian
                                @elseif($laporan->tipe_laporan === 'mingguan')
                                    Mingguan
                                @else
                                    Bulanan
                                @endif
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dibuat Oleh</td>
                        <td>{{ $laporan->user->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Waktu Dibuat</td>
                        <td>{{ $laporan->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <h5 class="text-muted">Informasi Mesin & Line</h5>
                <table class="table table-sm">
                    <tr>
                        <td class="fw-bold" style="width: 40%;">Nama Mesin</td>
                        <td>{{ $laporan->machine->name ?? $laporan->mesin_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Line/Departemen</td>
                        <td>{{ $laporan->line->name ?? $laporan->line ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Jenis Pekerjaan</td>
                        <td>
                            <span class="badge bg-warning">{{ ucfirst($laporan->jenis_pekerjaan) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Scope</td>
                        <td>{{ $laporan->scope ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Status</td>
                        <td>
                            @if($laporan->status === 'open')
                                <span class="badge bg-success">Open</span>
                            @elseif($laporan->status === 'closed')
                                <span class="badge bg-secondary">Closed</span>
                            @else
                                <span class="badge bg-info">{{ ucfirst($laporan->status) }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>

        <div class="row mb-4">
            <div class="col-12">
                <h5 class="text-muted">Deskripsi Masalah</h5>
                <div class="alert alert-light border">
                    {{ $laporan->catatan ?? '-' }}
                </div>
            </div>
        </div>

        @if(in_array($laporan->jenis_pekerjaan, ['corrective', 'preventive', 'change over product']))
        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="text-muted">Waktu Downtime</h5>
                <table class="table table-sm">
                    <tr>
                        <td class="fw-bold" style="width: 40%;">Waktu Mulai</td>
                        <td>{{ $laporan->start_time?->format('d/m/Y H:i') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Waktu Selesai</td>
                        <td>{{ $laporan->end_time?->format('d/m/Y H:i') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Total Downtime</td>
                        <td>
                            <strong>{{ $laporan->downtime_min ?? 0 }} Menit</strong>
                            <small class="text-muted">({{ ceil(($laporan->downtime_min ?? 0) / 60) }} Jam)</small>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @endif

        <div class="row mb-4">
            <div class="col-12">
                <h5 class="text-muted">Spare Part yang Digunakan</h5>
                @if($laporan->spare_part_id && $laporan->qty_sparepart)
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50%;">Nama Spare Part</th>
                            <th style="width: 25%;">Jumlah</th>
                            <th>Kode</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>{{ $laporan->sparePart->name ?? '-' }}</strong></td>
                            <td>{{ $laporan->qty_sparepart }} {{ $laporan->sparePart->unit ?? 'pcs' }}</td>
                            <td>{{ $laporan->sparePart->code ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
                @if($laporan->komentar_sparepart)
                <div class="alert alert-light border">
                    <strong>Komentar:</strong> {{ $laporan->komentar_sparepart }}
                </div>
                @endif
                @else
                <div class="alert alert-info">
                    Tidak ada spare part yang digunakan dalam laporan ini
                </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Informasi Tambahan</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 50%;">Terakhir Diubah</td>
                                <td>{{ $laporan->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Quantity Spare Part</td>
                                <td>{{ $laporan->qty_sparepart ?? 0 }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Aksi</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('laporan.edit', $laporan->id) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil"></i> Edit Laporan
                            </a>
                            <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-list"></i> Daftar Laporan
                            </a>
                            <form action="{{ route('laporan.destroy', $laporan->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Yakin ingin menghapus laporan ini?')">
                                    <i class="bi bi-trash"></i> Hapus Laporan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
