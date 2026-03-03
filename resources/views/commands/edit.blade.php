@extends('layouts.app')

@section('title', 'Edit Command - Sistem Laporan Maintenance')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Edit Command</h4>
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

        <form method="POST" action="{{ route('commands.update', $command->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="title" class="form-label">Judul Command <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $command->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="command_text" class="form-label">Command (Instruksi) <span class="text-danger">*</span></label>
                <textarea class="form-control @error('command_text') is-invalid @enderror" id="command_text" name="command_text" rows="4" required>{{ old('command_text', $command->command_text) }}</textarea>
                <small class="form-text text-muted">Deskripsikan instruksi atau command yang ingin diberikan kepada supervisor</small>
                @error('command_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="action_plan" class="form-label">Action Plan <span class="text-danger">*</span></label>
                <textarea class="form-control @error('action_plan') is-invalid @enderror" id="action_plan" name="action_plan" rows="4" required>{{ old('action_plan', $command->action_plan) }}</textarea>
                <small class="form-text text-muted">Jelaskan rencana aksi atau langkah-langkah yang harus diambil untuk menyelesaikan command ini</small>
                @error('action_plan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="supervisor_id" class="form-label">Pilih Supervisor <span class="text-danger">*</span></label>
                <select class="form-select @error('supervisor_id') is-invalid @enderror" id="supervisor_id" name="supervisor_id" required>
                    <option value="">-- Pilih Supervisor --</option>
                    @foreach($supervisors as $id => $name)
                        <option value="{{ $id }}" {{ old('supervisor_id', $command->supervisor_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @error('supervisor_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="due_date" class="form-label">Tanggal Jatuh Tempo</label>
                <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date', $command->due_date?->format('Y-m-d')) }}">
                @error('due_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Update Command
                </button>
                <a href="{{ route('commands.list-department-head') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
