@extends('layouts.app')

@section('title', 'Detail Command - Sistem Laporan Maintenance')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Detail Command</h4>
        <div>
            @if(auth()->id() === $command->supervisor_id)
                <a href="{{ route('commands.edit-status', $command->id) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil"></i> Update Status
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <p class="mb-2"><strong>Judul Command:</strong></p>
                <p class="text-muted">{{ $command->title }}</p>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Status:</strong></p>
                <p>
                    @if($command->status === 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @elseif($command->status === 'in_progress')
                        <span class="badge bg-info">In Progress</span>
                    @elseif($command->status === 'completed')
                        <span class="badge bg-success">Completed</span>
                    @else
                        <span class="badge bg-danger">Cancelled</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <p class="mb-2"><strong>Department Head:</strong></p>
                <p class="text-muted">{{ $command->departmentHead->name }}</p>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Supervisor:</strong></p>
                <p class="text-muted">{{ $command->supervisor->name }}</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <p class="mb-2"><strong>Tanggal Dibuat:</strong></p>
                <p class="text-muted">{{ $command->created_at->format('d M Y H:i') }}</p>
            </div>
            <div class="col-md-6">
                <p class="mb-2"><strong>Tanggal Jatuh Tempo:</strong></p>
                <p class="text-muted">
                    @if($command->due_date)
                        {{ $command->due_date->format('d M Y') }}
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>

        <hr>

        <div class="mb-4">
            <p class="mb-2"><strong>Command (Instruksi):</strong></p>
            <div class="bg-light p-3 rounded">
                {!! nl2br(e($command->command_text)) !!}
            </div>
        </div>

        <div class="mb-4">
            <p class="mb-2"><strong>Action Plan:</strong></p>
            <div class="bg-light p-3 rounded">
                {!! nl2br(e($command->action_plan)) !!}
            </div>
        </div>

        @if($command->supervisor_notes)
            <div class="mb-4">
                <p class="mb-2"><strong>Catatan dari Supervisor:</strong></p>
                <div class="bg-light p-3 rounded supervisor-notes-content">
                    {!! $command->supervisor_notes !!}
                </div>
            </div>
        @endif

        <hr>

        <div class="d-flex gap-2">
            @if(auth()->id() === $command->department_head_id && $command->status === 'pending')
                <a href="{{ route('commands.edit', $command->id) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit Command
                </a>
                <form method="POST" action="{{ route('commands.destroy', $command->id) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Anda yakin ingin menghapus command ini?')">
                        <i class="bi bi-trash"></i> Hapus Command
                    </button>
                </form>
            @endif
            
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
