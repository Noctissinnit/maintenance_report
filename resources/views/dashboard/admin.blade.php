@extends('layouts.app')

@section('title', 'Admin Dashboard - Sistem Laporan Maintenance')

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/dashboard-styles.css') }}">
@endsection

@section('content')
<div class="dash-page">

    {{-- Page Header --}}
    <x-dashboard.page-header title="Admin Dashboard" subtitle="Administrator · System Overview">
        <span class="badge-live"><span class="dot-pulse"></span> Live</span>
    </x-dashboard.page-header>

    {{-- Overview Stats --}}
    <x-dashboard.section title="System Overview" icon="bi-speedometer2">
        <div class="kpi-grid">
            <x-dashboard.stat-card label="Total Users" value="{{ $totalUsers }}" unit="users" />
            <x-dashboard.stat-card label="Total Laporan" value="{{ $totalLaporan }}" unit="reports" />
            <x-dashboard.stat-card label="Total Downtime" value="{{ number_format($totalDowntime/60, 1) }}" unit="jam" />
            <x-dashboard.stat-card label="Machines" value="{{ $totalMachines ?? 0 }}" unit="active" />
        </div>
    </x-dashboard.section>

    {{-- User Distribution --}}
    <x-dashboard.section title="User Distribution" icon="bi-people">
        <div class="kpi-grid">
            <x-dashboard.stat-card label="Admin Users" value="{{ $adminCount }}" subtitle="Administrator" />
            <x-dashboard.stat-card label="Department Head" value="{{ $departmentHeadCount }}" subtitle="Manager" />
            <x-dashboard.stat-card label="Supervisor" value="{{ $supervisorCount ?? 0 }}" subtitle="Supervisor" />
            <x-dashboard.stat-card label="Operator Users" value="{{ $operatorCount }}" subtitle="Technician" />
        </div>
    </x-dashboard.section>

    {{-- Latest Reports --}}
    <x-dashboard.section title="Latest Reports (10)" icon="bi-file-earmark-text">
        <x-dashboard.data-table :headers="['Date', 'User', 'Downtime', 'Status']">
            @forelse($latestLaporan ?? [] as $report)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($report->created_at)->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ $report->user->name ?? 'N/A' }}</strong></td>
                    <td><span class="badge-blue">{{ number_format(($report->downtime_min ?? 0)/60, 1) }} jam</span></td>
                    <td><span class="badge-green">Recorded</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-3">No reports available</td>
                </tr>
            @endforelse
        </x-dashboard.data-table>
    </x-dashboard.section>

    {{-- Reports by User --}}
    <x-dashboard.section title="Reports by User" icon="bi-people">
        <x-dashboard.data-table :headers="['User', 'Total Reports', 'Percentage']">
            @php $totalReports = collect($laporanPerUser ?? [])->sum('count'); @endphp
            @forelse($laporanPerUser ?? [] as $userReport)
                <tr>
                    <td><strong>{{ $userReport->user->name ?? 'N/A' }}</strong></td>
                    <td>{{ $userReport->count }}</td>
                    <td>
                        <div class="bar-wrap">
                            <div class="bar-bg">
                                <div class="bar-fill" style="width: {{ $totalReports > 0 ? ($userReport->count / $totalReports * 100) : 0 }}%"></div>
                            </div>
                            <span class="bar-val">{{ number_format($totalReports > 0 ? ($userReport->count / $totalReports * 100) : 0, 0) }}%</span>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted py-3">No user data available</td>
                </tr>
            @endforelse
        </x-dashboard.data-table>
    </x-dashboard.section>

</div>
@endsection
