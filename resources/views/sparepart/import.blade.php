@extends('layouts.app')

@section('title', 'Import Spare Part - Sistem Laporan Maintenance')

@section('content')
<h2 class="mb-4">Import Data Spare Part dari Excel</h2>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Format File Excel</h5>
            </div>
            <div class="card-body">
                <p>File Excel harus memiliki kolom dengan header berikut (baris pertama):</p>
                <ul>
                    <li><strong>name</strong> - Nama spare part (wajib, harus unik)</li>
                    <li><strong>code</strong> - Kode spare part (opsional, harus unik jika diisi)</li>
                    <li><strong>description</strong> - Deskripsi spare part (opsional)</li>
                    <li><strong>category</strong> - Kategori spare part (opsional)</li>
                    <li><strong>status</strong> - Status spare part (opsional: active/inactive, default: active)</li>
                </ul>
                <p class="text-muted mt-3"><strong>Contoh:</strong></p>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>name</th>
                            <th>code</th>
                            <th>description</th>
                            <th>category</th>
                            <th>status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Bearing 6203</td>
                            <td>BP001</td>
                            <td>Bearing standard untuk motor</td>
                            <td>Bearing</td>
                            <td>active</td>
                        </tr>
                        <tr>
                            <td>Belt Konveyor</td>
                            <td>BK001</td>
                            <td>Belt konveyor ukuran standar</td>
                            <td>Belt</td>
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
                <form method="POST" action="{{ route('spare-parts.import') }}" enctype="multipart/form-data">
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
                        <strong>Perhatian!</strong> Data yang sudah ada tidak akan dihapus. Nama dan kode harus unik.
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload"></i> Import Data
                        </button>
                        <a href="{{ route('spare-parts.index') }}" class="btn btn-secondary">
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
                <a href="{{ route('spare-parts.template') }}" class="btn btn-primary">
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
                        <p class="small text-muted mb-0">Setiap nama spare part harus berbeda</p>
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
