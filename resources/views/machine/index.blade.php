@extends('layouts.app')

@section('title', 'Data Mesin - Sistem Laporan Maintenance')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Data Mesin</h4>
        <div>
            <a href="{{ route('machines.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah Mesin
            </a>
            <a href="{{ route('templates.download-machine') }}" class="btn btn-primary btn-sm" title="Download template untuk import data">
                <i class="bi bi-file-earmark-spreadsheet"></i> Template
            </a>
            <a href="{{ route('machines.export') }}" class="btn btn-info btn-sm">
                <i class="bi bi-download"></i> Export
            </a>
            <a href="{{ route('machines.import-form') }}" class="btn btn-warning btn-sm">
                <i class="bi bi-upload"></i> Import
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($machines->isEmpty())
            <div class="alert alert-info">Belum ada data mesin</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Mesin</th>
                            <th>Kode</th>
                            <th>Line</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($machines as $machine)
                            <tr>
                                <td>{{ ($machines->currentPage() - 1) * $machines->perPage() + $loop->iteration }}</td>
                                <td><strong>{{ $machine->name }}</strong></td>
                                <td>{{ $machine->code ?? '-' }}</td>
                                <td>{{ $machine->line->name ?? '-' }}</td>
                                <td>{{ Str::limit($machine->description, 30) ?? '-' }}</td>
                                <td>
                                    @if($machine->status === 'active')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $machine->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('mtbf.show', $machine->id) }}" class="btn btn-sm btn-info" title="View MTBF Analysis">
                                        <i class="bi bi-graph-up"></i>
                                    </a>
                                    <a href="{{ route('machines.edit', $machine->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('machines.destroy', $machine->id) }}" method="POST" style="display: inline;">
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
                {{ $machines->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
