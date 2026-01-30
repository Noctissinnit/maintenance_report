@extends('layouts.app')

@section('title', 'Import Spare Part - Sistem Laporan Maintenance')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Import Data Spare Part</h4>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>Petunjuk Import:</strong>
            <ol>
                <li><a href="{{ route('templates.download-spare-part') }}" class="alert-link" download>Download template XLSX</a> terlebih dahulu</li>
                <li>Buka file dengan Excel dan isi data sesuai format (Nama Spare Part, Kode, Deskripsi, Stok, Status)</li>
                <li>Jangan ubah header kolom</li>
                <li>Pastikan format data sudah benar sebelum upload</li>
                <li>Simpan file sebagai XLSX</li>
                <li>Upload file XLSX yang sudah diisi</li>
            </ol>
        </div>

        <form action="{{ route('spare-parts.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="file" class="form-label">Pilih File XLSX <span class="text-danger">*</span></label>
                <input type="file" class="form-control @error('file') is-invalid @enderror" 
                    id="file" name="file" accept=".xlsx" required>
                <small class="form-text text-muted">Format file: Excel (.xlsx)</small>
                @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Import</button>
                <a href="{{ route('spare-parts.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
