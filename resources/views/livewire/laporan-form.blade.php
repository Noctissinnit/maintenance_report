<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Input Laporan Baru</h4>
    </div>
    <div class="card-body">
        <form wire:submit="submit">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="machine_id" class="form-label">Nama Mesin <span class="text-danger">*</span></label>
                    <select class="form-select select2 @error('machine_id') is-invalid @enderror" 
                        id="machine_id" wire:model.live="machine_id" style="width: 100%;">
                        <option value="">-- Pilih Mesin --</option>
                        @foreach($machines as $machine)
                            <option value="{{ $machine->id }}">{{ $machine->name }}</option>
                        @endforeach
                    </select>
                    @error('machine_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="line_id" class="form-label">Line/Departemen <span class="text-danger">*</span></label>
                    <select class="form-select select2 @error('line_id') is-invalid @enderror" 
                        id="line_id" wire:model="line_id" style="width: 100%;" required>
                        <option value="">-- Pilih Line --</option>
                        @foreach($lines as $line)
                            <option value="{{ $line->id }}">{{ $line->name }}</option>
                        @endforeach
                    </select>
                    @error('line_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan/Deskripsi Masalah</label>
                <textarea class="form-control @error('catatan') is-invalid @enderror" 
                    id="catatan" wire:model="catatan" rows="3"></textarea>
                @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="spare_part_id" class="form-label">Nama Spare Part</label>
                    <select class="form-select select2 @error('spare_part_id') is-invalid @enderror" 
                        id="spare_part_id" wire:model="spare_part_id" style="width: 100%;">
                        <option value="">-- Pilih Spare Part --</option>
                        @foreach($spareParts as $part)
                            <option value="{{ $part->id }}">{{ $part->name }}</option>
                        @endforeach
                    </select>
                    @error('spare_part_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="qty_sparepart" class="form-label">Jumlah Spare Part</label>
                    <input type="number" class="form-control @error('qty_sparepart') is-invalid @enderror" 
                        id="qty_sparepart" wire:model="qty_sparepart" min="0">
                    @error('qty_sparepart')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="komentar_sparepart" class="form-label">Komentar Spare Part</label>
                <textarea class="form-control @error('komentar_sparepart') is-invalid @enderror" 
                    id="komentar_sparepart" wire:model="komentar_sparepart" rows="2"></textarea>
                @error('komentar_sparepart')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jenis_pekerjaan" class="form-label">Jenis Pekerjaan <span class="text-danger">*</span></label>
                    <select class="form-select @error('jenis_pekerjaan') is-invalid @enderror" 
                        id="jenis_pekerjaan" wire:model.live="jenis_pekerjaan" required>
                        <option value="">-- Pilih Jenis Pekerjaan --</option>
                        <option value="corrective">Corrective</option>
                        <option value="preventive">Preventive</option>
                        <option value="change over product">Change Over Product</option>
                        <option value="modifikasi">Modifikasi</option>
                        <option value="utility">Utility</option>
                    </select>
                    @error('jenis_pekerjaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="scope" class="form-label">Scope <span class="text-danger">*</span></label>
                    <select class="form-select @error('scope') is-invalid @enderror" 
                        id="scope" wire:model="scope" required>
                        <option value="">-- Pilih Scope --</option>
                        <option value="Electrik">Electrik</option>
                        <option value="Mekanik">Mekanik</option>
                        <option value="Utility">Utility</option>
                        <option value="Building">Building</option>
                    </select>
                    @error('scope')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            @if(in_array($jenis_pekerjaan, ['corrective', 'preventive', 'change over product']))
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_time" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" 
                        id="start_time" wire:model.live="start_time">
                    @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="end_time" class="form-label">Waktu Selesai</label>
                    <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" 
                        id="end_time" wire:model.live="end_time">
                    @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="downtime_min" class="form-label">Downtime (Menit)</label>
                    <input type="number" class="form-control" 
                        id="downtime_min" wire:model="downtime_min" min="0" readonly>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tipe_laporan" class="form-label">Tipe Laporan <span class="text-danger">*</span></label>
                    <select class="form-select @error('tipe_laporan') is-invalid @enderror" 
                        id="tipe_laporan" wire:model="tipe_laporan" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="harian">Harian</option>
                        <option value="mingguan">Mingguan</option>
                        <option value="bulanan">Bulanan</option>
                    </select>
                    @error('tipe_laporan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tanggal_laporan" class="form-label">Tanggal Laporan <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal_laporan') is-invalid @enderror" 
                        id="tanggal_laporan" wire:model="tanggal_laporan" required>
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
    // Re-initialize Select2 after Livewire updates
    document.addEventListener('livewire:navigated', function() {
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            jQuery('.select2').select2();
        }
    });

    // Initial Select2 initialization
    Livewire.on('select2:reinit', function() {
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            jQuery('.select2').select2();
        }
    });
</script>
