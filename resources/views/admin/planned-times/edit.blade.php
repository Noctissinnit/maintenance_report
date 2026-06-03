@extends('layouts.app')

@section('title', 'Edit Planned Time - Sistem Laporan Maintenance')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-pencil-square"></i> Edit Planned Time</h2>
            <p class="text-muted">Update production planned time for a specific month</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('planned-times.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Validation Error:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('planned-times.update', $plannedTime->id) }}" class="row g-3">
                @csrf
                @method('PUT')

                <!-- Start Date -->
                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                           id="start_date" name="start_date" 
                           value="{{ old('start_date', $plannedTime->start_date?->format('Y-m-d')) }}" required>
                    @error('start_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- End Date -->
                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                           id="end_date" name="end_date" 
                           value="{{ old('end_date', $plannedTime->end_date?->format('Y-m-d')) }}" required>
                    @error('end_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">End date must be equal to or after start date</small>
                </div>

                <!-- Planned Time in Minutes -->
                <div class="col-md-6">
                    <label for="planned_time_minutes" class="form-label">Planned Time (Minutes) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control @error('planned_time_minutes') is-invalid @enderror" 
                               id="planned_time_minutes" name="planned_time_minutes" min="0" 
                               value="{{ old('planned_time_minutes', $plannedTime->planned_time_minutes) }}" 
                               required @change="updateHours()">
                        <span class="input-group-text">min</span>
                    </div>
                    @error('planned_time_minutes')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">
                        Total planned production time in minutes. Use calculator: Days × 24 × 60 × Active Machines
                    </small>
                </div>

                <!-- Calculated Hours -->
                <div class="col-md-6">
                    <label class="form-label">Calculated Hours</label>
                    <div class="input-group">
                        <input type="text" class="form-control bg-light" id="hours_display" readonly 
                               value="{{ ($plannedTime->planned_time_minutes / 60) }}">
                        <span class="input-group-text">hours</span>
                    </div>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3" 
                              placeholder="e.g., Based on production schedule...">{{ old('description', $plannedTime->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        <strong>Created by:</strong> {{ $plannedTime->creator->name ?? 'System' }} 
                        on {{ $plannedTime->created_at->format('Y-m-d H:i:s') }}
                        <br>
                        <strong>Last updated:</strong> {{ $plannedTime->updated_at->format('Y-m-d H:i:s') }}
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Planned Time
                        </button>
                        <a href="{{ route('planned-times.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <form method="POST" action="{{ route('planned-times.destroy', $plannedTime->id) }}" style="display: inline;" 
                              onsubmit="return confirm('Are you sure you want to delete this planned time?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reference Information -->
    <div class="card mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Calculation Guide</h5>
        </div>
        <div class="card-body">
            <p><strong>Formula:</strong> Planned Time = Days in Month × 24 hours × 60 minutes × Number of Active Machines</p>
            <p class="mb-0"><strong>Current Value:</strong> {{ number_format($plannedTime->planned_time_minutes) }} minutes = {{ number_format($plannedTime->planned_time_minutes / 60, 2) }} hours</p>
        </div>
    </div>
</div>

<script>
function updateHours() {
    const minutes = parseInt(document.getElementById('planned_time_minutes').value) || 0;
    const hours = (minutes / 60).toFixed(2);
    document.getElementById('hours_display').value = hours;
}

// Update on load and on input change
document.getElementById('planned_time_minutes').addEventListener('input', updateHours);
document.addEventListener('DOMContentLoaded', updateHours);
</script>
@endsection
