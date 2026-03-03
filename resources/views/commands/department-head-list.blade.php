@extends('layouts.app')

@section('title', 'Daftar Command Saya - Sistem Laporan Maintenance')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Command yang Saya Buat</h4>
        <a href="{{ route('commands.create') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle"></i> Buat Command Baru
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter Status -->
        <div class="mb-3">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </form>
        </div>

        @if($commands->isEmpty())
            <div class="alert alert-info">Belum ada command yang dibuat</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Supervisor</th>
                            <th>Status</th>
                            <th>Tanggal Jatuh Tempo</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commands as $command)
                            <tr>
                                <td>{{ ($commands->currentPage() - 1) * $commands->perPage() + $loop->iteration }}</td>
                                <td><strong>{{ $command->title }}</strong></td>
                                <td>{{ $command->supervisor->name }}</td>
                                <td>
                                    @if($command->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($command->status === 'in_progress')
                                        <span class="badge bg-info">In Progress</span>
                                    @elseif($command->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    @if($command->due_date)
                                        {{ $command->due_date->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $command->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('commands.show', $command->id) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($command->status === 'pending')
                                        <a href="{{ route('commands.edit', $command->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('commands.destroy', $command->id) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Anda yakin ingin menghapus command ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $commands->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
