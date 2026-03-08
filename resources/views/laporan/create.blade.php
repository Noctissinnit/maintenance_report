@extends('layouts.app')

@section('title', 'Input Laporan Baru - Sistem Laporan Maintenance')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Input Laporan Baru</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('laporan.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="machine_id" class="form-label">Nama Mesin <span class="text-danger">*</span></label>
                    <select class="form-select select2 @error('machine_id') is-invalid @enderror" 
                        id="machine_id" name="machine_id" style="width: 100%;">
                        <option value="">-- Pilih Mesin --</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}" @selected(old('machine_id') == $machine->id)>{{ $machine->name }}</option>
                        @endforeach
                    </select>
                    @error('machine_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="line_id_display" class="form-label">Line/Departemen <span class="text-danger">*</span></label>
                    <select class="form-select select2 @error('line_id') is-invalid @enderror" 
                        id="line_id_display" name="line_id_display" style="width: 100%;" disabled>
                        <option value="">-- Pilih Line --</option>
                        @foreach($lines as $line)
                            <option value="{{ $line->id }}" @selected(old('line_id') == $line->id)>{{ $line->name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="line_id_hidden" name="line_id" value="{{ old('line_id', '') }}">
                    @error('line_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan/Deskripsi Masalah</label>
                <textarea class="form-control @error('catatan') is-invalid @enderror" 
                    id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
                @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="spare_part_id" class="form-label">Nama Spare Part</label>
                    <select class="form-select select2 @error('spare_part_id') is-invalid @enderror" 
                        id="spare_part_id" name="spare_part_id" style="width: 100%;">
                        <option value="">-- Pilih Spare Part --</option>
                        @foreach($spareParts as $part)
                            <option value="{{ $part->id }}" data-stock="{{ $part->stock }}" data-unit="{{ $part->unit }}" @selected(old('spare_part_id') == $part->id)>{{ $part->name }}</option>
                        @endforeach
                    </select>
                    @error('spare_part_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="stock_display" class="form-label">Stok Tersedia</label>
                    <div class="input-group">
                        <input type="text" class="form-control bg-light" 
                            id="stock_display" readonly value="0">
                        <span class="input-group-text" id="unit_display">pcs</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="qty_sparepart" class="form-label">Jumlah Spare Part</label>
                    <input type="number" class="form-control @error('qty_sparepart') is-invalid @enderror" 
                        id="qty_sparepart" name="qty_sparepart" value="{{ old('qty_sparepart', 0) }}" min="0" step="0.01">
                    @error('qty_sparepart')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            <div class="mb-3">
                <label for="komentar_sparepart" class="form-label">Komentar Spare Part</label>
                <textarea class="form-control @error('komentar_sparepart') is-invalid @enderror" 
                    id="komentar_sparepart" name="komentar_sparepart" rows="2">{{ old('komentar_sparepart') }}</textarea>
                @error('komentar_sparepart')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jenis_pekerjaan" class="form-label">Jenis Pekerjaan <span class="text-danger">*</span></label>
                    <select class="form-select @error('jenis_pekerjaan') is-invalid @enderror" 
                        id="jenis_pekerjaan" name="jenis_pekerjaan" required onchange="toggleTimeFields()">
                        <option value="">-- Pilih Jenis Pekerjaan --</option>
                        <option value="corrective" @selected(old('jenis_pekerjaan') === 'corrective')>Corrective</option>
                        <option value="preventive" @selected(old('jenis_pekerjaan') === 'preventive')>Preventive</option>
                        <option value="change over product" @selected(old('jenis_pekerjaan') === 'change over product')>Change Over Product</option>
                        <option value="modifikasi" @selected(old('jenis_pekerjaan') === 'modifikasi')>Modifikasi</option>
                        <option value="utility" @selected(old('jenis_pekerjaan') === 'utility')>Utility</option>
                    </select>
                    @error('jenis_pekerjaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="scope" class="form-label">Scope <span class="text-danger">*</span></label>
                    <select class="form-select @error('scope') is-invalid @enderror" 
                        id="scope" name="scope" required>
                        <option value="">-- Pilih Scope --</option>
                        <option value="Electrik" @selected(old('scope') === 'Electrik')>Electrik</option>
                        <option value="Mekanik" @selected(old('scope') === 'Mekanik')>Mekanik</option>
                        <option value="Utility" @selected(old('scope') === 'Utility')>Utility</option>
                        <option value="Building" @selected(old('scope') === 'Building')>Building</option>
                    </select>
                    @error('scope')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

             <div id="timeFieldsContainer" class="row" style="display: none;">
                <div class="col-md-6 mb-3">
                    <label for="start_time" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" 
                        id="start_time" name="start_time" value="{{ old('start_time') }}" onchange="calculateDowntime()">
                    @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="end_time" class="form-label">Waktu Selesai</label>
                    <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" 
                        id="end_time" name="end_time" value="{{ old('end_time') }}" onchange="calculateDowntime()">
                    @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="downtime_min" class="form-label">Downtime (Menit)</label>
                    <input type="number" class="form-control @error('downtime_min') is-invalid @enderror" 
                        id="downtime_min" name="downtime_min" value="{{ old('downtime_min', 0) }}" min="0" readonly>
                    @error('downtime_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tipe_laporan" class="form-label">Tipe Laporan <span class="text-danger">*</span></label>
                    <select class="form-select @error('tipe_laporan') is-invalid @enderror" 
                        id="tipe_laporan" name="tipe_laporan" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="harian" @selected(old('tipe_laporan') === 'harian')>Harian</option>
                        <option value="mingguan" @selected(old('tipe_laporan') === 'mingguan')>Mingguan</option>
                        <option value="bulanan" @selected(old('tipe_laporan') === 'bulanan')>Bulanan</option>
                    </select>
                    @error('tipe_laporan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tanggal_laporan" class="form-label">Tanggal Laporan <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal_laporan') is-invalid @enderror" 
                        id="tanggal_laporan" name="tanggal_laporan" value="{{ old('tanggal_laporan', now()->format('Y-m-d')) }}" required>
                    @error('tanggal_laporan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Kirim Laporan
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
        const endTimeInput = document.getElementById('end_time');
        
        if (jenisPekerjaan === 'corrective' || jenisPekerjaan === 'preventive' || jenisPekerjaan === 'change over product') {
            timeFieldsContainer.style.display = 'contents';
            startTimeInput.required = true;
        } else {
            timeFieldsContainer.style.display = 'none';
            startTimeInput.required = false;
            endTimeInput.required = false;
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

    // Display spare part stock
    function displaySparePartStock() {
        const sparePartSelect = document.getElementById('spare_part_id');
        const stockDisplay = document.getElementById('stock_display');
        const unitDisplay = document.getElementById('unit_display');
        
        if (sparePartSelect.value) {
            const selectedOption = sparePartSelect.options[sparePartSelect.selectedIndex];
            const stock = selectedOption.getAttribute('data-stock') || '0';
            const unit = selectedOption.getAttribute('data-unit') || 'pcs';
            
            stockDisplay.value = stock;
            unitDisplay.textContent = unit;
        } else {
            stockDisplay.value = '0';
            unitDisplay.textContent = 'pcs';
        }
    }

    // Auto-fill Line when Machine is selected - NO REFRESH!
    async function autoFillLine() {
        const machineId = document.getElementById('machine_id').value;
        const lineSelectDisplay = document.getElementById('line_id_display');
        const lineSelectHidden = document.getElementById('line_id_hidden');
        
        if (!machineId) {
            lineSelectDisplay.value = '';
            lineSelectHidden.value = '';
            if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                jQuery(lineSelectDisplay).val('').trigger('change');
            }
            return;
        }

        try {
            const response = await fetch(`/api/machine/${machineId}/line`);
            const data = await response.json();
            
            if (data.success) {
                // Set value untuk display dan hidden input
                lineSelectDisplay.value = data.line_id;
                lineSelectHidden.value = data.line_id;
                
                // Update Select2
                if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                    jQuery(lineSelectDisplay).val(data.line_id).trigger('change');
                }
                
                console.log('✓ Line terisi otomatis: ' + data.line_name);
            }
        } catch (error) {
            console.error('Error:', error);
            lineSelectDisplay.value = '';
            lineSelectHidden.value = '';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            jQuery('.select2').select2();
            
            // Add event listener for Select2 change event (machine selection)
            jQuery('#machine_id').on('select2:select', function() {
                autoFillLine();
            });
            
            // Add event listener for spare part selection to show stock
            jQuery('#spare_part_id').on('select2:select', function() {
                displaySparePartStock();
            });
        } else {
            // Fallback if Select2 not available
            document.getElementById('machine_id').addEventListener('change', autoFillLine);
            document.getElementById('spare_part_id').addEventListener('change', displaySparePartStock);
        }

        // Initialize time fields visibility
        toggleTimeFields();
        
        // Display stock for selected spare part on load
        displaySparePartStock();
        
        // Form submit handler - ensure line_id is filled
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const lineDisplaySelect = document.getElementById('line_id_display');
                const lineHiddenInput = document.getElementById('line_id_hidden');
                
                if (lineDisplaySelect && lineDisplaySelect.value) {
                    lineHiddenInput.value = lineDisplaySelect.value;
                }
            });
        }
    });
</script>
@endsection

