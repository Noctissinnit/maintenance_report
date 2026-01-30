@extends('layouts.app')

@section('title', 'Data Spare Part - Sistem Laporan Maintenance')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Data Spare Part</h4>
        <div>
            <a href="{{ route('spare-parts.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah Spare Part
            </a>
            <a href="{{ route('templates.download-spare-part') }}" class="btn btn-primary btn-sm" title="Download template untuk import data">
                <i class="bi bi-file-earmark-spreadsheet"></i> Template
            </a>
            <a href="{{ route('spare-parts.export') }}" class="btn btn-info btn-sm">
                <i class="bi bi-download"></i> Export
            </a>
            <a href="{{ route('spare-parts.import-form') }}" class="btn btn-warning btn-sm">
                <i class="bi bi-upload"></i> Import
            </a>
            <a href="{{ route('spare-parts.monitoring') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-graph-up"></i> Monitoring Penggunaan
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($spareParts->isEmpty())
            <div class="alert alert-info">Belum ada data spare part</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Spare Part</th>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($spareParts as $part)
                            <tr>
                                <td>{{ ($spareParts->currentPage() - 1) * $spareParts->perPage() + $loop->iteration }}</td>
                                <td><strong>{{ $part->name }}</strong></td>
                                <td>{{ $part->code ?? '-' }}</td>
                                <td>{{ $part->category ?? '-' }}</td>
                                <td>{{ Str::limit($part->description, 30) ?? '-' }}</td>
                                <td>
                                    @if($part->status === 'active')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $part->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('spare-parts.edit', $part->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('spare-parts.destroy', $part->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $spareParts->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
