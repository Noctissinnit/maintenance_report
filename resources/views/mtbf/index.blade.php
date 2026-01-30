@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">MTBF Analysis (Mean Time Between Failures)</h1>
                <a href="{{ route('machines.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to Machines
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title text-uppercase small">Total Machines</h6>
                    <h3 class="mb-0">{{ $statistics['total_machines'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title text-uppercase small">Total Failures</h6>
                    <h3 class="mb-0">{{ $statistics['total_failures'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title text-uppercase small">Total Downtime</h6>
                    <h3 class="mb-0">{{ number_format($statistics['total_downtime_hours'], 2) }} hrs</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title text-uppercase small">Average MTBF</h6>
                    <h3 class="mb-0">{{ number_format($statistics['average_mtbf_hours'], 2) }} hrs</h3>
                    <small>{{ number_format($statistics['average_mtbf_days'], 2) }} days</small>
                </div>
            </div>
        </div>
    </div>

    <!-- MTBF Table -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Machine Reliability (Sorted by MTBF - Best First)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="25%">Machine Name</th>
                            <th width="15%">Line</th>
                            <th width="10%" class="text-center">Failures</th>
                            <th width="15%" class="text-center">Total Downtime</th>
                            <th width="15%" class="text-center">MTBF</th>
                            <th width="10%" class="text-center">Reliability</th>
                            <th width="10%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mtbfData as $machine)
                            @php
                                $mtbfHours = $machine['mtbf_hours'] ?? 0;
                                // Determine reliability status
                                if ($mtbfHours >= 168) { // >= 1 week
                                    $badgeClass = 'bg-success';
                                    $statusText = 'Excellent';
                                } elseif ($mtbfHours >= 72) { // >= 3 days
                                    $badgeClass = 'bg-info';
                                    $statusText = 'Good';
                                } elseif ($mtbfHours >= 24) { // >= 1 day
                                    $badgeClass = 'bg-warning';
                                    $statusText = 'Fair';
                                } else {
                                    $badgeClass = 'bg-danger';
                                    $statusText = 'Poor';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $machine['machine_name'] }}</strong>
                                    <br>
                                    <small class="text-muted">ID: {{ $machine['machine_id'] }}</small>
                                </td>
                                <td>{{ $machine['line_name'] ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if($machine['failure_count'] > 0)
                                        <span class="badge bg-danger">{{ $machine['failure_count'] }}</span>
                                    @else
                                        <span class="badge bg-success">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ number_format($machine['total_downtime_hours'], 2) }} hrs
                                    <br>
                                    <small class="text-muted">{{ number_format($machine['total_downtime_minutes'], 0) }} min</small>
                                </td>
                                <td class="text-center">
                                    @if($machine['failure_count'] > 0)
                                        <strong>{{ number_format($machine['mtbf_hours'], 2) }} hrs</strong>
                                        <br>
                                        <small class="text-muted">{{ number_format($machine['mtbf_days'], 2) }} days</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('mtbf.show', $machine['machine_id']) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox"></i> No machines found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-4">
        <div class="alert alert-info alert-sm">
            <h6 class="alert-heading mb-2"><i class="bi bi-info-circle"></i> Reliability Status Explanation</h6>
            <ul class="mb-0 small">
                <li><span class="badge bg-success">Excellent</span> - MTBF ≥ 168 hours (1 week) - Highly reliable machine</li>
                <li><span class="badge bg-info">Good</span> - MTBF ≥ 72 hours (3 days) - Reliable machine</li>
                <li><span class="badge bg-warning">Fair</span> - MTBF ≥ 24 hours (1 day) - Machine requires attention</li>
                <li><span class="badge bg-danger">Poor</span> - MTBF < 24 hours - Machine needs frequent maintenance</li>
            </ul>
        </div>
    </div>

</div>
@endsection
