@extends('layouts.app')

@section('title', 'Data Karyawan - Sistem Laporan Maintenance')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>Data Karyawan</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('employees.import-form') }}" class="btn btn-info">
            <i class="bi bi-upload"></i> Import Excel
        </a>
        <a href="{{ route('employees.create') }}" class="btn btn-success">
            <i class="bi bi-plus"></i> Tambah Karyawan
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Email</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $index => $employee)
                    <tr>
                        <td>{{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ $employee->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah anda yakin akan mepecat karyawan ini?')">
                                    <i class="bi bi-trash"></i> Pecat
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Tidak ada data karyawan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $employees->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
