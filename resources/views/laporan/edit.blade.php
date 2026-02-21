@extends('layouts.app')

@section('title', 'Edit Laporan - Sistem Laporan Maintenance')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Edit Laporan</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('laporan.update', $laporan->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="machine_id" class="form-label">Nama Mesin <span class="text-danger">*</span></label>
                    <select class="form-select select2 @error('machine_id') is-invalid @enderror" 
                        id="machine_id" name="machine_id" style="width: 100%;">
                        <option value="">-- Pilih Mesin --</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}" @selected(old('machine_id', $laporan->machine_id) == $machine->id)>{{ $machine->name }}</option>
                        @endforeach
                    </select>
                    @error('machine_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="line_id" class="form-label">Line/Departemen <span class="text-danger">*</span></label>
                    <select class="form-select select2 @error('line_id') is-invalid @enderror" 
                        id="line_id" name="line_id" style="width: 100%;" required>
                        <option value="">-- Pilih Line --</option>
                        @foreach($lines as $line)
                            <option value="{{ $line->id }}" @selected(old('line_id', $laporan->machine?->line_id) == $line->id)>{{ $line->name }}</option>
                        @endforeach
                    </select>
                    @error('line_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan/Deskripsi Masalah</label>
                <textarea class="form-control @error('catatan') is-invalid @enderror" 
                    id="catatan" name="catatan" rows="3">{{ old('catatan', $laporan->catatan) }}</textarea>
                @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="spare_part_id" class="form-label">Nama Spare Part</label>
                    <select class="form-select select2 @error('spare_part_id') is-invalid @enderror" 
                        id="spare_part_id" name="spare_part_id" style="width: 100%;">
                        <option value="">-- Pilih Spare Part --</option>
                        @foreach($spareParts as $part)
                            <option value="{{ $part->id }}" @selected(old('spare_part_id', $laporan->spare_part_id) == $part->id)>{{ $part->name }}</option>
                        @endforeach
                    </select>
                    @error('spare_part_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="qty_sparepart" class="form-label">Jumlah Spare Part</label>
                    <input type="number" class="form-control @error('qty_sparepart') is-invalid @enderror" 
                        id="qty_sparepart" name="qty_sparepart" value="{{ old('qty_sparepart', $laporan->qty_sparepart) }}" min="0">
                    @error('qty_sparepart')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="komentar_sparepart" class="form-label">Komentar Spare Part</label>
                <textarea class="form-control @error('komentar_sparepart') is-invalid @enderror" 
                    id="komentar_sparepart" name="komentar_sparepart" rows="2">{{ old('komentar_sparepart', $laporan->komentar_sparepart) }}</textarea>
                @error('komentar_sparepart')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="tanggal_laporan" class="form-label">Tanggal Laporan <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('tanggal_laporan') is-invalid @enderror" 
                    id="tanggal_laporan" name="tanggal_laporan" value="{{ old('tanggal_laporan', $laporan->tanggal_laporan->format('Y-m-d')) }}" required>
                @error('tanggal_laporan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jenis_pekerjaan" class="form-label">Jenis Pekerjaan <span class="text-danger">*</span></label>
                    <select class="form-select @error('jenis_pekerjaan') is-invalid @enderror" 
                        id="jenis_pekerjaan" name="jenis_pekerjaan" required onchange="toggleTimeFields()">
                        <option value="">-- Pilih Jenis Pekerjaan --</option>
                        <option value="corrective" @selected(old('jenis_pekerjaan', $laporan->jenis_pekerjaan) === 'corrective')>Corrective</option>
                        <option value="preventive" @selected(old('jenis_pekerjaan', $laporan->jenis_pekerjaan) === 'preventive')>Preventive</option>
                        <option value="modifikasi" @selected(old('jenis_pekerjaan', $laporan->jenis_pekerjaan) === 'modifikasi')>Modifikasi</option>
                        <option value="utility" @selected(old('jenis_pekerjaan', $laporan->jenis_pekerjaan) === 'utility')>Utility</option>
                    </select>
                    @error('jenis_pekerjaan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tipe_laporan" class="form-label">Tipe Laporan <span class="text-danger">*</span></label>
                    <select class="form-select @error('tipe_laporan') is-invalid @enderror" 
                        id="tipe_laporan" name="tipe_laporan" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="harian" @selected(old('tipe_laporan', $laporan->tipe_laporan) === 'harian')>Harian</option>
                        <option value="mingguan" @selected(old('tipe_laporan', $laporan->tipe_laporan) === 'mingguan')>Mingguan</option>
                        <option value="bulanan" @selected(old('tipe_laporan', $laporan->tipe_laporan) === 'bulanan')>Bulanan</option>
                    </select>
                    @error('tipe_laporan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div id="timeFieldsContainer" style="display: none;">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_time" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" 
                            id="start_time" name="start_time" value="{{ old('start_time', $laporan->start_time?->format('Y-m-d\TH:i')) }}" onchange="calculateDowntime()">
                        @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_time" class="form-label">Waktu Selesai</label>
                        <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" 
                            id="end_time" name="end_time" value="{{ old('end_time', $laporan->end_time?->format('Y-m-d\TH:i')) }}" onchange="calculateDowntime()">
                        @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="downtime_min" class="form-label">Downtime (Menit)</label>
                    <input type="number" class="form-control @error('downtime_min') is-invalid @enderror" 
                        id="downtime_min" name="downtime_min" value="{{ old('downtime_min', $laporan->downtime_min) }}" min="0" readonly>
                    @error('downtime_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleTimeFields() {
    const jenisPekerjaan = document.getElementById('jenis_pekerjaan').value;
    const timeFieldsContainer = document.getElementById('timeFieldsContainer');
    const startTimeInput = document.getElementById('start_time');
    
    if (jenisPekerjaan === 'corrective') {
        timeFieldsContainer.style.display = 'contents';
        startTimeInput.required = true;
    } else {
        timeFieldsContainer.style.display = 'none';
        startTimeInput.required = false;
        document.getElementById('downtime_min').value = 0;
    }
}

function calculateDowntime() {
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    if (startTime && endTime) {
        const start = new Date(startTime);
        const end = new Date(endTime);
        const diffMinutes = Math.floor((end - start) / (1000 * 60));
        document.getElementById('downtime_min').value = Math.max(0, diffMinutes);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleTimeFields();
});
</script>
@endsection
