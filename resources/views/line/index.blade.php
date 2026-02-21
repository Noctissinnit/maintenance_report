@extends('layouts.app')

@section('title', 'Data Line - Sistem Laporan Maintenance')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Data Line Produksi</h4>
        <div>
            <a href="{{ route('lines.import-form') }}" class="btn btn-info btn-sm">
                <i class="bi bi-upload"></i> Import Excel
            </a>
            <a href="{{ route('lines.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah Line
            </a>
            <a href="{{ route('lines.template') }}" class="btn btn-primary btn-sm" title="Download template untuk import data">
                <i class="bi bi-file-earmark-spreadsheet"></i> Template
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($lines->isEmpty())
            <div class="alert alert-info">Belum ada data line</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Line</th>
                            <th>Kode</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lines as $line)
                            <tr>
                                <td>{{ ($lines->currentPage() - 1) * $lines->perPage() + $loop->iteration }}</td>
                                <td><strong>{{ $line->name }}</strong></td>
                                <td>{{ $line->code ?? '-' }}</td>
                                <td>{{ Str::limit($line->description, 40) ?? '-' }}</td>
                                <td>
                                    @if($line->status === 'active')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $line->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('lines.edit', $line->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('lines.destroy', $line->id) }}" method="POST" style="display: inline;">
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
                {{ $lines->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
