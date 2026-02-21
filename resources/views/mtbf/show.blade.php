@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">MTBF Analysis: {{ $machine->nama_mesin }}</h1>
                    <small class="text-muted">Machine ID: {{ $machine->id }} | Line: {{ $machine->line->name ?? 'N/A' }}</small>
                </div>
                <a href="{{ route('mtbf.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to MTBF List
                </a>
            </div>
        </div>
    </div>

    <!-- MTBF Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <h6 class="card-title text-uppercase small">Total Failures</h6>
                    <h3 class="text-danger mb-0">{{ $mtbfData['failure_count'] }}</h3>
                    <small class="text-muted">(Corrective Maintenance)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="card-title text-uppercase small">Total Downtime</h6>
                    <h3 class="text-warning mb-0">{{ number_format($mtbfData['total_downtime_hours'], 2) }} hrs</h3>
                    <small class="text-muted">{{ number_format($mtbfData['total_downtime_minutes'], 0) }} min</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="card-title text-uppercase small">MTBF (Hours)</h6>
                    <h3 class="text-primary mb-0">{{ number_format($mtbfData['mtbf_hours'], 2) }} hrs</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="card-title text-uppercase small">MTBF (Days)</h6>
                    <h3 class="text-success mb-0">{{ number_format($mtbfData['mtbf_days'], 2) }} days</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Summary by Type -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Maintenance Summary by Type</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3 border-end">
                                <div class="h2 text-danger">{{ $maintenanceSummary['corrective'] ?? 0 }}</div>
                                <div class="text-muted small">Corrective (Perbaikan)</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border-end">
                                <div class="h2 text-info">{{ $maintenanceSummary['preventive'] ?? 0 }}</div>
                                <div class="text-muted small">Preventive (Pencegahan)</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border-end">
                                <div class="h2 text-warning">{{ $maintenanceSummary['modifikasi'] ?? 0 }}</div>
                                <div class="text-muted small">Modifikasi (Improvement)</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3">
                                <div class="h2 text-secondary">{{ $maintenanceSummary['utility'] ?? 0 }}</div>
                                <div class="text-muted small">Utility (Utility Work)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Corrective Maintenance History -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Corrective Maintenance History (Used for MTBF Calculation)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="15%">Date</th>
                            <th width="12%">Time Start</th>
                            <th width="12%">Time End</th>
                            <th width="12%" class="text-center">Downtime</th>
                            <th width="50%">Notes (Catatan)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($correctiveMaintenance as $report)
                            <tr>
                                <td>{{ $report->tanggal_laporan->format('d M Y') }}</td>
                                <td>
                                    @if($report->start_time)
                                        <small>{{ $report->start_time->format('H:i') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($report->end_time)
                                        <small>{{ $report->end_time->format('H:i') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($report->downtime_min)
                                        <span class="badge bg-danger">{{ $report->downtime_min }} min</span>
                                        <br>
                                        <small class="text-muted">{{ number_format($report->downtime_min / 60, 2) }} hrs</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ Str::limit($report->catatan, 50) }}
                                    @if(strlen($report->catatan) > 50)
                                        <br>
                                        <button type="button" class="btn btn-link btn-sm p-0" data-bs-toggle="modal" data-bs-target="#noteModal{{ $report->id }}">
                                            View full note
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            <!-- Note Modal -->
                            @if(strlen($report->catatan) > 50)
                                <div class="modal fade" id="noteModal{{ $report->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Maintenance Notes - {{ $report->tanggal_laporan->format('d M Y') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>{{ $report->catatan }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox"></i> No corrective maintenance records found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $correctiveMaintenance->links() }}
        </div>
    </div>

    <!-- MTBF Calculation Info -->
    <div class="alert alert-info">
        <h6 class="alert-heading mb-2"><i class="bi bi-info-circle"></i> MTBF Calculation Formula</h6>
        <p class="mb-1"><strong>MTBF = Total Downtime (hours) ÷ Number of Failures</strong></p>
        <p class="mb-0 small text-muted">
            For machine <strong>{{ $machine->nama_mesin }}</strong>: 
            {{ number_format($mtbfData['total_downtime_hours'], 2) }} hours ÷ {{ $mtbfData['failure_count'] }} failures 
            = {{ number_format($mtbfData['mtbf_hours'], 2) }} hours ({{ number_format($mtbfData['mtbf_days'], 2) }} days)
        </p>
    </div>

</div>
@endsection
