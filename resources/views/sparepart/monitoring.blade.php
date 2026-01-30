@extends('layouts.app')

@section('title', 'Monitoring Penggunaan Spare Part - Sistem Laporan Maintenance')

@section('extra-css')
<style>
    .sparepart-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .sparepart-title {
        font-size: 1.5rem;
        font-weight: bold;
    }
    
    .sparepart-counter {
        background-color: rgba(255, 255, 255, 0.2);
        padding: 15px 25px;
        border-radius: 8px;
        text-align: center;
        font-size: 1.2rem;
    }
    
    .sparepart-counter .label {
        font-size: 0.9rem;
        opacity: 0.9;
        display: block;
        margin-bottom: 5px;
    }
    
    .sparepart-counter .count {
        font-size: 2rem;
        font-weight: bold;
    }
    
    .month-selector {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        overflow-x: auto;
    }
    
    .month-buttons {
        display: flex;
        gap: 10px;
        min-width: min-content;
    }
    
    .month-btn {
        padding: 8px 16px;
        border: 2px solid var(--secondary-color);
        background-color: white;
        color: var(--secondary-color);
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
        white-space: nowrap;
        text-decoration: none;
    }
    
    .month-btn:hover,
    .month-btn.active {
        background-color: var(--secondary-color);
        color: white;
    }
    
    .sparepart-table {
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .sparepart-table thead {
        background-color: var(--primary-color);
        color: white;
    }
    
    .sparepart-table thead th {
        padding: 15px;
        font-weight: 600;
        text-align: left;
    }
    
    .sparepart-table tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
    }
    
    .sparepart-table tbody tr:hover {
        background-color: #f5f5f5;
    }
    
    .sparepart-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .qty-badge {
        display: inline-block;
        background-color: var(--secondary-color);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #999;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }
</style>
@endsection

@section('content')
<!-- Header Section -->
<div class="sparepart-header">
    <div>
        <div class="sparepart-title">
            <i class="bi bi-box-seam"></i> Monitoring Penggunaan Spare Part
        </div>
        <small style="opacity: 0.9;">Dashboard Pemeliharaan Spare Part</small>
    </div>
    <div class="sparepart-counter">
        <span class="label">PEMAKAIAN SPARE PART</span>
        <span class="count">{{ $totalUsage }}</span>
    </div>
</div>

<!-- Month Selector -->
<div class="month-selector">
    <label class="form-label fw-bold mb-3">Pilih Bulan:</label>
    <div class="month-buttons">
        @php
            $months = [
                1 => 'Januari',
                2 => 'Februari', 
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];
        @endphp
        
        @foreach($months as $m => $name)
            <a href="{{ route('spare-parts.monitoring', ['bulan' => $m, 'tahun' => $tahun]) }}" 
               class="month-btn @if($bulan == $m) active @endif">
                {{ $name }}
            </a>
        @endforeach
    </div>
</div>

<!-- Spare Parts Table -->
<div class="sparepart-table">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 75%;">Nama Spare Part</th>
                <th style="width: 20%; text-align: center;">Total Pemakaian</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usage as $index => $part)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $part->sparepart }}</td>
                    <td style="text-align: center;">
                        <span class="qty-badge">{{ $part->total_qty }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>Tidak ada data spare part untuk bulan {{ $months[$bulan] ?? 'pilihan' }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Summary Card -->
@if($usage->count() > 0)
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Ringkasan</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h6 style="color: var(--secondary-color);">Total Jenis Spare Part</h6>
                            <h3 style="color: var(--primary-color);">{{ $usage->count() }}</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h6 style="color: var(--secondary-color);">Total Pemakaian</h6>
                            <h3 style="color: var(--primary-color);">{{ $totalUsage }} Unit</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h6 style="color: var(--secondary-color);">Rata-rata Pemakaian</h6>
                            <h3 style="color: var(--primary-color);">{{ number_format($totalUsage / $usage->count(), 2) }} Unit</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
