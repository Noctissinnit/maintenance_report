@extends('layouts.app')

@section('title', 'Planned Time Management - Sistem Laporan Maintenance')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-clock-history"></i> Planned Time Management</h2>
            <p class="text-muted">Manage production scheduled times for each month</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('planned-times.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Planned Time
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('planned-times.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="year" class="form-label">Filter by Year</label>
                    <select name="year" id="year" class="form-select">
                        <option value="">-- All Years --</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" @if($yearFilter == $year) selected @endif>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('planned-times.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Year</th>
                        <th>Month</th>
                        <th>Planned Time</th>
                        <th>Hours</th>
                        <th>Description</th>
                        <th>Created By</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plannedTimes as $record)
                        <tr>
                            <td>
                                <strong>{{ $record->year }}</strong>
                            </td>
                            <td>
                                @php
                                    $months = [
                                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                                    ];
                                @endphp
                                {{ $months[$record->month] }}
                            </td>
                            <td>
                                <strong class="text-primary">{{ number_format($record->planned_time_minutes) }}</strong>
                                <small class="text-muted">minutes</small>
                            </td>
                            <td>
                                {{ number_format($record->planned_time_minutes / 60, 2) }}
                            </td>
                            <td>
                                @if($record->description)
                                    <span title="{{ $record->description }}" class="d-inline-block text-truncate" style="max-width: 150px;">
                                        {{ $record->description }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $record->creator->name ?? 'System' }}</small>
                            </td>
                            <td>
                                <small>{{ $record->updated_at->format('Y-m-d H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('planned-times.edit', $record->id) }}" class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('planned-times.destroy', $record->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> No planned times found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($plannedTimes->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $plannedTimes->links() }}
        </div>
    @endif
</div>
@endsection
