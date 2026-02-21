@extends('layouts.app')

@section('title', 'Import Line - Sistem Laporan Maintenance')

@section('content')
<h2 class="mb-4">Import Data Line dari Excel</h2>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Format File Excel</h5>
            </div>
            <div class="card-body">
                <p>File Excel harus memiliki kolom dengan header berikut (baris pertama):</p>
                <ul>
                    <li><strong>name</strong> - Nama line (wajib, harus unik)</li>
                    <li><strong>code</strong> - Kode line (opsional, harus unik jika diisi)</li>
                    <li><strong>description</strong> - Deskripsi line (opsional)</li>
                    <li><strong>status</strong> - Status line (opsional: active/inactive, default: active)</li>
                </ul>
                <p class="text-muted mt-3"><strong>Contoh:</strong></p>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>name</th>
                            <th>code</th>
                            <th>description</th>
                            <th>status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Line A</td>
                            <td>LA001</td>
                            <td>Lini Produksi A</td>
                            <td>active</td>
                        </tr>
                        <tr>
                            <td>Line B</td>
                            <td>LB001</td>
                            <td>Lini Produksi B</td>
                            <td>active</td>
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
                <form method="POST" action="{{ route('lines.import') }}" enctype="multipart/form-data">
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
                        <strong>Perhatian!</strong> Data yang sudah ada tidak akan dihapus. Nama dan kode line harus unik.
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload"></i> Import Data
                        </button>
                        <a href="{{ route('lines.index') }}" class="btn btn-secondary">
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
                <a href="{{ route('lines.template') }}" class="btn btn-primary">
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
                        <strong>✓ Gunakan Excel atau CSV</strong>
                        <p class="small text-muted mb-0">File format yang didukung</p>
                    </li>
                    <li class="mb-3">
                        <strong>✓ Header di baris pertama</strong>
                        <p class="small text-muted mb-0">Jangan lupa header columns</p>
                    </li>
                    <li class="mb-3">
                        <strong>✓ Nama unik</strong>
                        <p class="small text-muted mb-0">Setiap nama line harus berbeda</p>
                    </li>
                    <li>
                        <strong>✓ Data lengkap</strong>
                        <p class="small text-muted mb-0">Name wajib ada</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
