@extends('layouts.app')

@section('title', 'Import Laporan - Sistem Laporan Maintenance')

@section('content')
<h2 class="mb-4">Import Data Laporan dari Excel</h2>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Format File Excel</h5>
            </div>
            <div class="card-body">
                <p>File Excel harus memiliki kolom dengan header berikut (baris pertama):</p>
                <ul>
                    <li><strong>tanggal_laporan</strong> - Tanggal laporan (format: dd/mm/yyyy)</li>
                    <li><strong>machine_name</strong> - Nama mesin (wajib, harus sudah ada di database)</li>
                    <li><strong>line_name</strong> - Nama line (opsional)</li>
                    <li><strong>jenis_pekerjaan</strong> - Jenis pekerjaan (preventive/corrective)</li>
                    <li><strong>scope</strong> - Scope pekerjaan</li>
                    <li><strong>notes</strong> - Catatan umum</li>
                    <li><strong>spare_part_name</strong> - Nama spare part yang digunakan (opsional)</li>
                    <li><strong>qty_spare_part</strong> - Jumlah spare part (opsional)</li>
                    <li><strong>spare_part_notes</strong> - Catatan spare part (opsional)</li>
                    <li><strong>start_time</strong> - Waktu mulai (format: HH:mm, untuk corrective)</li>
                    <li><strong>end_time</strong> - Waktu selesai (format: HH:mm, untuk corrective)</li>
                    <li><strong>downtime_min</strong> - Downtime dalam menit (opsional)</li>
                    <li><strong>status</strong> - Status (completed/pending, default: completed)</li>
                    <li><strong>report_type</strong> - Tipe laporan (daily/weekly/monthly, default: daily)</li>
                </ul>
                <p class="text-muted mt-3"><strong>Contoh:</strong></p>
                <table class="table table-sm table-bordered" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>tanggal_laporan</th>
                            <th>machine_name</th>
                            <th>jenis_pekerjaan</th>
                            <th>scope</th>
                            <th>notes</th>
                            <th>start_time</th>
                            <th>end_time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>15/02/2026</td>
                            <td>Mesin Produksi A1</td>
                            <td>preventive</td>
                            <td>Pembersihan dan pelumasan</td>
                            <td>Rutin harian</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>14/02/2026</td>
                            <td>Mesin Produksi B1</td>
                            <td>corrective</td>
                            <td>Perbaikan bearing</td>
                            <td>Bearing aus, diganti</td>
                            <td>08:30</td>
                            <td>10:15</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Upload File</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('laporan.import') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="file" class="form-label">Pilih File Excel *</label>
                        <input 
                            type="file" 
                            class="form-control @error('file') is-invalid @enderror" 
                            id="file" 
                            name="file" 
                            accept=".xlsx,.xls,.csv"
                            required
                        >
                        <small class="form-text text-muted">
                            Format: .xlsx, .xls, atau .csv (Max 10MB)
                        </small>
                        @error('file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-info-circle"></i>
                        <strong>Perhatian!</strong> Nama mesin harus sudah terdaftar. Laporan akan dicatat atas nama Anda ({{ Auth::user()->name }}).
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload"></i> Import Data
                        </button>
                        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Download Template</h5>
            </div>
            <div class="card-body">
                <p>Anda dapat mengunduh template Excel untuk memudahkan proses input data:</p>
                <a href="{{ route('laporan.template') }}" class="btn btn-primary">
                    <i class="bi bi-download"></i> Download Template Excel
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Tips</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <strong>✓ Mesin harus ada</strong>
                        <p class="small text-muted mb-0">Pastikan mesin sudah terdaftar</p>
                    </li>
                    <li class="mb-3">
                        <strong>✓ Format tanggal: dd/mm/yyyy</strong>
                        <p class="small text-muted mb-0">Contoh: 15/02/2026</p>
                    </li>
                    <li class="mb-3">
                        <strong>✓ Format waktu: HH:mm</strong>
                        <p class="small text-muted mb-0">Contoh: 08:30 atau 14:45</p>
                    </li>
                    <li class="mb-3">
                        <strong>✓ Untuk corrective</strong>
                        <p class="small text-muted mb-0">Isikan start_time dan end_time</p>
                    </li>
                    <li>
                        <strong>✓ Header wajib ada</strong>
                        <p class="small text-muted mb-0">Gunakan template yang tersedia</p>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Catatan</h5>
            </div>
            <div class="card-body">
                <p class="small mb-0">Laporan yang diimport akan dicatat dengan:</p>
                <ul class="small list-unstyled mt-2">
                    <li>👤 User: Anda ({{ Auth::user()->name }})</li>
                    <li>📅 Tanggal: Sesuai di file</li>
                    <li>⚙️ Status: Completed (default)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
