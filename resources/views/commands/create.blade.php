@extends('layouts.app')

@section('title', 'Buat Command Baru - Sistem Laporan Maintenance')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Buat Command Baru</h4>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Validasi Gagal!</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('commands.store') }}">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label">Judul Command <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="command_text" class="form-label">Command (Instruksi) <span class="text-danger">*</span></label>
                <textarea class="form-control @error('command_text') is-invalid @enderror" id="command_text" name="command_text" rows="4" required>{{ old('command_text') }}</textarea>
                <small class="form-text text-muted">Deskripsikan instruksi atau command yang ingin diberikan kepada supervisor</small>
                @error('command_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="action_plan" class="form-label">Action Plan <span class="text-danger">*</span></label>
                <textarea class="form-control @error('action_plan') is-invalid @enderror" id="action_plan" name="action_plan" rows="4" required>{{ old('action_plan') }}</textarea>
                <small class="form-text text-muted">Jelaskan rencana aksi atau langkah-langkah yang harus diambil untuk menyelesaikan command ini</small>
                @error('action_plan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Supervisor field is automatically assigned -->
            <input type="hidden" name="supervisor_id" value=""

            <div class="mb-3">
                <label for="due_date" class="form-label">Tanggal Jatuh Tempo</label>
                <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date') }}">
                @error('due_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Buat Command
                </button>
                <a href="{{ route('commands.list-department-head') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
