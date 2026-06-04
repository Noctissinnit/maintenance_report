<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Department Head Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            color: #1f2937;
            font-size: 10px;
            line-height: 1.4;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #3266ad 0%, #4a7fc1 100%);
            color: white;
            padding: 18px 20px;
            text-align: center;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .header p {
            font-size: 9px;
            opacity: 0.95;
        }

        /* Alert */
        .alert {
            background: #e0f2fe;
            border-left: 4px solid #0284c7;
            color: #0c4a6e;
            padding: 9px 12px;
            margin-bottom: 10px;
            font-size: 9px;
            border-radius: 3px;
        }

        /* Section Title */
        .section-title {
            background: #3266ad;
            color: white;
            padding: 8px 12px;
            margin: 12px 0 8px 0;
            font-size: 10px;
            font-weight: 700;
            border-radius: 3px;
        }

        /* Layout */
        .row {
            display: block;
            margin-bottom: 3px;
        }

        .col-25 {
            display: inline-block;
            width: 24%;
            margin-right: 1%;
            vertical-align: top;
        }

        .col-25:nth-child(4n) {
            margin-right: 0;
        }

        .col-33 {
            display: inline-block;
            width: 32%;
            margin-right: 1.5%;
            vertical-align: top;
        }

        .col-33:nth-child(3n) {
            margin-right: 0;
        }

        .col-50 {
            display: inline-block;
            width: 49%;
            margin-right: 1%;
            vertical-align: top;
        }

        .col-50:nth-child(even) {
            margin-right: 0;
        }

        /* KPI Card */
        .kpi-card {
            background: linear-gradient(135deg, #3266ad, #4a7fc1);
            color: white;
            padding: 18px 12px;
            border-radius: 4px;
            text-align: center;
            min-height: 85px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px 4px 0 0;
        }

        .kpi-card .label {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: 700;
            opacity: 0.95;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .kpi-card .value {
            font-size: 24px;
            font-weight: 700;
            line-height: 1.1;
        }

        /* Color variants for KPI Cards */
        .kpi-card.red {
            background: linear-gradient(135deg, #A32D2D, #b84343);
        }

        .kpi-card.amber {
            background: linear-gradient(135deg, #BA7517, #cc8a2f);
        }

        .kpi-card.green {
            background: linear-gradient(135deg, #3B6D11, #4d8a1a);
        }

        .kpi-card.teal {
            background: linear-gradient(135deg, #0F6E56, #2a8b73);
        }

        .kpi-card.purple {
            background: linear-gradient(135deg, #534AB7, #6d5fd1);
        }

        /* Performance Card */
        .perf-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-top: 3px solid #3266ad;
            padding: 14px 10px;
            border-radius: 3px;
            text-align: center;
            min-height: 90px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .perf-card.red { border-top-color: #A32D2D; }
        .perf-card.amber { border-top-color: #BA7517; }
        .perf-card.green { border-top-color: #3B6D11; }
        .perf-card.teal { border-top-color: #0F6E56; }
        .perf-card.purple { border-top-color: #534AB7; }

        .perf-card .label {
            font-size: 8px;
            text-transform: uppercase;
            color: #666;
            font-weight: 700;
            margin-bottom: 6px;
            letter-spacing: 0.4px;
        }

        .perf-card .value {
            font-size: 20px;
            font-weight: 700;
            color: #3266ad;
            line-height: 1.1;
            margin-bottom: 2px;
        }

        .perf-card .unit {
            font-size: 8px;
            color: #888;
        }

        .perf-card.red .value { color: #A32D2D; }
        .perf-card.amber .value { color: #BA7517; }
        .perf-card.green .value { color: #3B6D11; }
        .perf-card.teal .value { color: #0F6E56; }
        .perf-card.purple .value { color: #534AB7; }

        /* Summary Box */
        .summary-box {
            background: linear-gradient(135deg, #3266ad, #4a7fc1);
            color: white;
            padding: 16px 12px;
            border-radius: 4px;
            text-align: center;
            min-height: 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .summary-box.red { background: linear-gradient(135deg, #A32D2D, #b84343); }
        .summary-box.amber { background: linear-gradient(135deg, #BA7517, #cc8a2f); }

        .summary-box .title {
            font-size: 9px;
            font-weight: 700;
            opacity: 0.95;
            margin-bottom: 6px;
        }

        .summary-box .number {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 3px;
        }

        .summary-box .unit {
            font-size: 8px;
            opacity: 0.9;
        }

        /* Card */
        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .card-header {
            background: #3266ad;
            color: white;
            padding: 8px 12px;
            font-weight: 700;
            font-size: 9px;
        }

        .card-header.success {
            background: #3B6D11;
        }

        .card-header.danger {
            background: #A32D2D;
        }

        .card-header.warning {
            background: #BA7517;
        }

        .card-body {
            padding: 8px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            line-height: 1.2;
        }

        table th {
            background: #3266ad;
            color: white;
            padding: 4px 6px;
            border: none;
            font-weight: 700;
            text-align: left;
            font-size: 8px;
        }

        table td {
            padding: 4px 6px;
            border-bottom: 1px solid #e5e7eb;
        }

        table tr:last-child td {
            border-bottom: 1px solid #3266ad;
        }

        table tr:nth-child(even) {
            background: #f9fafb;
        }

        .text-center {
            text-align: center;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            color: white;
            font-size: 7px;
            font-weight: 700;
            min-width: 22px;
            text-align: center;
        }

        .badge-danger {
            background: #A32D2D;
        }

        .badge-success {
            background: #3B6D11;
        }

        .badge-info {
            background: #0F6E56;
        }

        .badge-warning {
            background: #BA7517;
            color: #fff;
        }

        .badge-red { background: #A32D2D; }
        .badge-blue { background: #3266ad; }

        /* Utilities */
        .small {
            font-size: 7px;
            color: #777;
        }

        .no-data {
            text-align: center;
            color: #999;
            padding: 10px;
            font-size: 8px;
            font-style: italic;
        }

        .page-break {
            page-break-after: always;
        }

        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
            color: #999;
        }

        /* Machine Info */
        .machine-info {
            background: linear-gradient(to right, #e0ecff, #eff3ff);
            border-left: 4px solid #3266ad;
            color: #1e3a8a;
            padding: 10px 12px;
            margin-bottom: 3px;
            font-size: 9px;
            border-radius: 3px;
        }

        .machine-info strong {
            display: block;
            margin-bottom: 3px;
            font-size: 10px;
            color: #3266ad;
        }

        /* Info Banner */
        .info-banner {
            background: linear-gradient(to right, #e0ecff, #eff3ff);
            border-left: 4px solid #3266ad;
            padding: 8px 12px;
            margin-bottom: 3px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            color: #1e3a8a;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <h1>Department Head Dashboard</h1>
        <p>Monitoring Laporan Maintenance - {{ \Carbon\Carbon::createFromFormat('n', $bulan)->format('F') }} {{ $tahun }}</p>
    </div>
  

    @if($mesin)
        <!-- MACHINE INFO -->
        <div style="background: linear-gradient(to right, #e8f0ff, #f5f8ff); border-left: 4px solid #4361ee; padding: 8px 12px; margin-bottom: 3px; border-radius: 3px; font-size: 10px; font-weight: 600; color: #2d3748;">
            Mesin: <span style="color: #4361ee; font-weight: 700;">{{ $mesin }}</span>
            <br><span style="font-size: 8px; color: #666; font-weight: 400; margin-top: 2px; display: block;">
                @if($all_time ?? false)
                    Periode: Semua Waktu
                @else
                    Periode: {{ \Carbon\Carbon::createFromFormat('n', $bulan)->format('F') }} {{ $tahun }}
                @endif
            </span>
        </div>
    @elseif($line)
        <!-- LINE INFO -->
        <div style="background: linear-gradient(to right, #e8f0ff, #f5f8ff); border-left: 4px solid #4361ee; padding: 8px 12px; margin-bottom: 3px; border-radius: 3px; font-size: 10px; font-weight: 600; color: #2d3748;">
            Line: <span style="color: #4361ee; font-weight: 700;">{{ $line }}</span>
            <br><span style="font-size: 8px; color: #666; font-weight: 400; margin-top: 2px; display: block;">
                @if($all_time ?? false)
                    Periode: Semua Waktu
                @else
                    Periode: {{ \Carbon\Carbon::createFromFormat('n', $bulan)->format('F') }} {{ $tahun }}
                @endif
            </span>
        </div>
    @else
        <!-- DEFAULT INFO -->
        <div style="background: linear-gradient(to right, #e8f0ff, #f5f8ff); border-left: 4px solid #4361ee; padding: 8px 12px; margin-bottom: 3px; border-radius: 3px; font-size: 10px; font-weight: 600; color: #2d3748;">
            Laporan Maintenance Dashboard
            <br><span style="font-size: 8px; color: #666; font-weight: 400; margin-top: 2px; display: block;">
                @if($all_time ?? false)
                    Periode: Semua Waktu
                @else
                    Periode: {{ \Carbon\Carbon::createFromFormat('n', $bulan)->format('F') }} {{ $tahun }}
                @endif
            </span>
        </div>
    @endif

    <!-- KPI CARDS -->
    <div class="row">
        <div class="col-25">
            <div class="kpi-card">
                <div class="label">Availability</div>
                <div class="value">{{ number_format($availability ?? 0, 2) }}%</div>
            </div>
        </div>
        <div class="col-25">
            <div class="kpi-card">
                <div class="label">Downtime</div>
                <div class="value">{{ number_format($downtimePercent ?? 0, 2) }}%</div>
            </div>
        </div>
        <div class="col-25">
            <div class="kpi-card">
                <div class="label">Rata-rata MTTR</div>
                <div class="value">{{ number_format($avgMTTR ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-25">
            <div class="kpi-card">
                <div class="label">Rata-rata MTBF</div>
                <div class="value">{{ number_format($avgMTBFHours ?? 0, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- MACHINE PERFORMANCE SECTION -->
    <div class="section-title">Machine Performance</div>

    <div class="row">
        <div class="col-25">
            <div class="perf-card">
                <div class="label">Planned Time</div>
                <div class="value">{{ number_format(($totalPlannedTime ?? 0) / 60, 2) }}</div>
                <div class="unit">jam</div>
            </div>
        </div>
        <div class="col-25">
            <div class="perf-card">
                <div class="label">Down Time</div>
                <div class="value">{{ number_format(($totalDowntimeMinutes ?? 0) / 60, 2) }}</div>
                <div class="unit">jam</div>
            </div>
        </div>
        <div class="col-25">
            <div class="perf-card">
                <div class="label">Operation Time</div>
                <div class="value">{{ number_format((($totalPlannedTime ?? 0) - ($totalDowntimeMinutes ?? 0)) / 60, 2) }}</div>
                <div class="unit">jam</div>
            </div>
        </div>
        <div class="col-25">
            <div class="perf-card">
                <div class="label">Breakdown</div>
                <div class="value">{{ $totalBreakdown ?? 0 }}</div>
                <div class="unit">kejadian</div>
            </div>
        </div>
    </div>

    <!-- MAINTENANCE TYPE SECTION -->
    <div class="section-title">Jenis Maintenance</div>

    <div class="row">
        <div class="col-33">
            <div class="perf-card">
                <div class="label">Corrective Maintenance</div>
                <div class="value">{{ number_format($totalCorrectiveMaint ?? 0, 2) }}</div>
                <div class="unit">jam</div>
            </div>
        </div>
        <div class="col-33">
            <div class="perf-card">
                <div class="label">Preventive Maintenance</div>
                <div class="value">{{ number_format($totalPreventiveMaint ?? 0, 2) }}</div>
                <div class="unit">jam</div>
            </div>
        </div>
        <div class="col-33">
            <div class="perf-card">
                <div class="label">Change Over Product</div>
                <div class="value">{{ number_format($totalChangeOver ?? 0, 2) }}</div>
                <div class="unit">jam</div>
            </div>
        </div>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="row">
        <div class="col-33">
            <div class="summary-box">
                <div class="title">Total Laporan</div>
                <div class="number">{{ $totalLaporan ?? 0 }}</div>
            </div>
        </div>
        <div class="col-33">
            <div class="summary-box">
                <div class="title">Total Downtime</div>
                <div class="number">{{ number_format($totalDowntime ?? 0) }}</div>
                <div class="unit">menit</div>
            </div>
        </div>
        <div class="col-33">
            <div class="summary-box">
                <div class="title">Jam Downtime</div>
                <div class="number">{{ number_format(($totalDowntime ?? 0) / 60, 2) }}</div>
                <div class="unit">jam</div>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- CHARTS SECTION -->
    <div class="section-title">Analisis Visual</div>

    <div class="row">
        <div class="col-50">
            <div class="card">
                <div class="card-header">Top 10 Mesin dengan Downtime Tertinggi</div>
                <div class="card-body">
                    @php
                        $chartData = $topDowntimeMesin ?? [];
                        $maxValue = 0;
                        foreach($chartData as $item) {
                            $val = ($item->total_downtime ?? 0) / 60;
                            if($val > $maxValue) $maxValue = $val;
                        }
                        $maxValue = $maxValue > 0 ? $maxValue : 1;
                    @endphp
                    
                    @if(count($chartData) > 0)
                        <table style="width: 100%; border: none; margin-top: 5px;">
                            @foreach($chartData->take(10) as $item)
                                @php
                                    $downtime = ($item->total_downtime ?? 0) / 60;
                                    $percentage = ($downtime / $maxValue) * 100;
                                @endphp
                                <tr style="border: none;">
                                    <td style="border: none; padding: 3px 5px; width: 50px; font-size: 7px;">{{ substr($item->mesin_name, 0, 12) }}</td>
                                    <td style="border: none; padding: 3px 5px; width: 60%;">
                                        <div style="background: #e0e0e0; height: 12px; border-radius: 2px; overflow: hidden;">
                                            <div style="background: #4361ee; height: 100%; width: {{ $percentage }}%; display: inline-block;"></div>
                                        </div>
                                    </td>
                                    <td style="border: none; padding: 3px 5px; text-align: right; font-size: 7px; width: 40px;">{{ number_format($downtime, 2) }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        <div class="no-data">Tidak ada data</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-50">
            <div class="card">
                <div class="card-header">Perbandingan Machine Performance</div>
                <div class="card-body">
                    @php
                        $plannedHours = ($totalPlannedTime ?? 0) / 60;
                        $downHours = ($totalDowntimeMinutes ?? 0) / 60;
                        $operationHours = $plannedHours - $downHours;
                        $total = $plannedHours > 0 ? $plannedHours : 1;
                    @endphp
                    
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <th style="background: #f1f5f9; color: #4361ee; padding: 6px 8px; border: 1px solid #ddd; font-size: 8px; font-weight: 700;">Status</th>
                            <th style="background: #f1f5f9; color: #4361ee; padding: 6px 8px; border: 1px solid #ddd; font-size: 8px; font-weight: 700; text-align: right;">Jam</th>
                            <th style="background: #f1f5f9; color: #4361ee; padding: 6px 8px; border: 1px solid #ddd; font-size: 8px; font-weight: 700; text-align: right;">%</th>
                        </tr>
                        <tr>
                            <td style="padding: 6px 8px; border: 1px solid #ddd; font-size: 8px;">
                                <span style="display: inline-block; width: 8px; height: 8px; background: #4361ee; border-radius: 2px; margin-right: 4px;"></span>
                                Operation
                            </td>
                            <td style="padding: 6px 8px; border: 1px solid #ddd; font-size: 8px; text-align: right;">{{ number_format($operationHours, 2) }}</td>
                            <td style="padding: 6px 8px; border: 1px solid #ddd; font-size: 8px; text-align: right;">{{ number_format(($operationHours / $total) * 100, 1) }}%</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 8px; border: 1px solid #ddd; font-size: 8px; background: #f9fafb;">
                                <span style="display: inline-block; width: 8px; height: 8px; background: #dc3545; border-radius: 2px; margin-right: 4px;"></span>
                                Downtime
                            </td>
                            <td style="padding: 6px 8px; border: 1px solid #ddd; font-size: 8px; text-align: right; background: #f9fafb;">{{ number_format($downHours, 2) }}</td>
                            <td style="padding: 6px 8px; border: 1px solid #ddd; font-size: 8px; text-align: right; background: #f9fafb;">{{ number_format(($downHours / $total) * 100, 1) }}%</td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 8px; border: 1px solid #ddd; font-size: 8px;">
                                <span style="display: inline-block; width: 8px; height: 8px; background: #28a745; border-radius: 2px; margin-right: 4px;"></span>
                                Idle Time
                            </td>
                            <td style="padding: 6px 8px; border: 1px solid #ddd; font-size: 8px; text-align: right;">{{ number_format(max(0, $plannedHours - $operationHours - $downHours), 2) }}</td>
                            <td style="padding: 6px 8px; border: 1px solid #ddd; font-size: 8px; text-align: right;">{{ number_format((max(0, $plannedHours - $operationHours - $downHours) / $total) * 100, 1) }}%</td>
                        </tr>
                        <tr style="font-weight: 700; background: #e8eef7;">
                            <td style="padding: 6px 8px; border: 1px solid #ddd; font-size: 8px;">Total Planned Time</td>
                            <td style="padding: 6px 8px; border: 1px solid #ddd; font-size: 8px; text-align: right;">{{ number_format($plannedHours, 2) }}</td>
                            <td style="padding: 6px 8px; border: 1px solid #ddd; font-size: 8px; text-align: right;">100%</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MTBF SECTION -->
    <div class="section-title">MTBF (Mean Time Between Failures)</div>

    <!-- TOP 5 RELIABLE & WORST MACHINES -->
    <div class="row">
        <div class="col-50">
            <div class="card">
                <div class="card-header success">Top 5 Most Reliable Machines</div>
                <div class="card-body">
                    @if(count($topReliableMachines ?? []) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Mesin</th>
                                    <th class="text-center">MTBF (hrs)</th>
                                    <th class="text-center">Failures</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topReliableMachines as $machine)
                                    @php
                                        $failureCount = $machine['failure_count'] ?? 0;
                                        $downtimeHours = $machine['total_downtime_hours'] ?? 0;
                                        
                                        if ($failureCount == 0) {
                                            $badgeClass = 'badge-success';
                                            $status = 'Excellent';
                                        } elseif ($failureCount <= 2 && $downtimeHours < 4) {
                                            $badgeClass = 'badge-info';
                                            $status = 'Good';
                                        } else {
                                            $badgeClass = 'badge-warning';
                                            $status = 'Fair';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $machine['machine_name'] ?? '-' }}</strong>
                                            <br><span class="small">{{ $machine['line_name'] ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">{{ number_format($machine['mtbf_hours'] ?? 0, 2) }}</td>
                                        <td class="text-center"><span class="badge badge-danger">{{ $machine['failure_count'] ?? 0 }}</span></td>
                                        <td class="text-center"><span class="badge {{ $badgeClass }}">{{ $status }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="no-data">Tidak ada data</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-50">
            <div class="card">
                <div class="card-header danger">Bottom 5 Worst Performing Machines</div>
                <div class="card-body">
                    @if(count($worstMachines ?? []) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Mesin</th>
                                    <th class="text-center">MTBF (hrs)</th>
                                    <th class="text-center">Failures</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($worstMachines as $machine)
                                    @php
                                        $failureCount = $machine['failure_count'] ?? 0;
                                        
                                        if ($failureCount <= 2) {
                                            $badgeClass = 'badge-info';
                                            $status = 'Good';
                                        } elseif ($failureCount <= 5) {
                                            $badgeClass = 'badge-warning';
                                            $status = 'Fair';
                                        } else {
                                            $badgeClass = 'badge-danger';
                                            $status = 'Poor';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $machine['machine_name'] ?? '-' }}</strong>
                                            <br><span class="small">{{ $machine['line_name'] ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">{{ number_format($machine['mtbf_hours'] ?? 0, 2) }}</td>
                                        <td class="text-center"><span class="badge badge-danger">{{ $machine['failure_count'] ?? 0 }}</span></td>
                                        <td class="text-center"><span class="badge {{ $badgeClass }}">{{ $status }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="no-data">Tidak ada data</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- DETAIL TABLES ROW 1 -->
    <div class="row">
        <div class="col-50">
            <div class="card">
                <div class="card-header">Top 10 Mesin dengan Downtime Tertinggi</div>
                <div class="card-body">
                    @if(count($topDowntimeMesin ?? []) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Mesin</th>
                                    <th class="text-center">Downtime (jam)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topDowntimeMesin as $item)
                                    <tr>
                                        <td>{{ $item->mesin_name ?? '-' }}</td>
                                        <td class="text-center">{{ number_format(($item->total_downtime ?? 0) / 60, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="no-data">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        <div class="no-data">Tidak ada data</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-50">
            <div class="card">
                <div class="card-header">Daftar Breakdown per Line</div>
                <div class="card-body">
                    @if(count($topBreakdownLine ?? []) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Line</th>
                                    <th class="text-center">Downtime</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topBreakdownLine as $item)
                                    <tr>
                                        <td>{{ $item->line ?? '-' }}</td>
                                        <td class="text-center"><span class="badge badge-warning">{{ number_format(($item->total_downtime_min ?? 0) / 60, 2) }} jam</span></td>
                                        <td class="text-center"><span class="badge badge-danger">{{ $item->breakdown_count ?? 0 }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="no-data">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        <div class="no-data">Tidak ada data</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- DETAIL TABLES ROW 2 -->
    <div class="row">
        <div class="col-50">
            <div class="card">
                <div class="card-header">Top 7 Breakdown - Jenis Kerusakan</div>
                <div class="card-body">
                    @if(count($topBreakdownCatatan ?? []) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Kerusakan</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topBreakdownCatatan as $item)
                                    <tr>
                                        <td>{{ $item->catatan ?? '-' }}</td>
                                        <td class="text-center"><span class="badge badge-danger">{{ $item->breakdown_count ?? 0 }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="no-data">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        <div class="no-data">Tidak ada data</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-50">
            <div class="card">
                <div class="card-header">Monitoring Spare Part</div>
                <div class="card-body">
                    @if(count($spareParts ?? []) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Spare Part</th>
                                    <th class="text-center">Total Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($spareParts as $item)
                                    <tr>
                                        <td>{{ $item->sparepart ?? '-' }}</td>
                                        <td class="text-center"><span class="badge badge-info">{{ $item->total_qty ?? 0 }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="no-data">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        <div class="no-data">Tidak ada data</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p>Generated: {{ now()->format('d F Y H:i:s') }}</p>
        <p>Department Head Dashboard - Sistem Laporan Maintenance</p>
    </div>

</body>
</html>
