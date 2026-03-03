@extends('layouts.app')

@section('title', 'Update Status Command - Sistem Laporan Maintenance')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Update Status Command - {{ $command->title }}</h4>
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

        <!-- Read Only Command Details -->
        <div class="mb-4 p-3 bg-light rounded">
            <div class="row mb-2">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Judul:</strong></p>
                    <p class="text-muted">{{ $command->title }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Department Head:</strong></p>
                    <p class="text-muted">{{ $command->departmentHead->name }}</p>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Command:</strong></p>
                    <p class="text-muted">{{ $command->command_text }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Action Plan:</strong></p>
                    <p class="text-muted">{{ $command->action_plan }}</p>
                </div>
            </div>

            @if($command->due_date)
                <p class="mb-1"><strong>Tanggal Jatuh Tempo:</strong></p>
                <p class="text-muted">{{ $command->due_date->format('d M Y') }}</p>
            @endif
        </div>

        <hr>

        <form method="POST" action="{{ route('commands.update-status', $command->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="pending" {{ $command->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ $command->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ $command->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $command->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="supervisor_notes" class="form-label">Catatan / Progress Update</label>
                
                <!-- Quill Editor Container -->
                <div class="quill-editor">
                    <div class="editor" style="height: 300px; background-color: white; border: 1px solid #ddd; border-radius: 4px;"></div>
                    <textarea class="editor-content d-none" id="supervisor_notes" name="supervisor_notes" placeholder="Tambahkan catatan atau progress update mengenai command ini">{{ old('supervisor_notes', $command->supervisor_notes) }}</textarea>
                </div>
                
                <small class="form-text text-muted d-block mt-2">Jelaskan progres atau catatan penting terkait eksekusi command ini. Anda bisa menambahkan foto bukti/screenshot langsung dari editor dengan mengklik tombol image.</small>
                @error('supervisor_notes')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Tips Summernote Editor:</strong>
                <ul class="mb-0 mt-2">
                    <li>Klik tombol <strong>Insert Image</strong> atau drag-drop gambar untuk menambahkan foto/screenshot bukti</li>
                    <li>Gunakan formatting tools (Bold, Italic, List, dll) untuk membuat catatan lebih jelas dan terstruktur</li>
                    <li>Gunakan tombol <strong>Fullscreen</strong> untuk ruang kerja yang lebih luas</li>
                    <li>Klik <strong>Code View</strong> jika ingin melihat atau edit HTML langsung</li>
                </ul>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Update Status
                </button>
                <a href="{{ route('commands.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Include Quill initialization script -->
@include('components.quill-init')

@endsection
