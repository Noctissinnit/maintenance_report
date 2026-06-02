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

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status Line <span class="text-danger">*</span></label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="line_status" id="line_on" value="on" 
                            wire:model="line_status" checked>
                        <label class="btn btn-outline-success" for="line_on" style="cursor: pointer;">
                            <i class="bi bi-check-circle"></i> ON
                        </label>

                        <input type="radio" class="btn-check" name="line_status" id="line_off" value="off" 
                            wire:model="line_status">
                        <label class="btn btn-outline-danger" for="line_off" style="cursor: pointer;">
                            <i class="bi bi-x-circle"></i> OFF
                        </label>
                    </div>
                    <small class="text-muted d-block mt-2">ON = Downtime dihitung | OFF = Downtime tidak dihitung</small>
                </div>
            </div>

            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan/Deskripsi Masalah</label>
                <textarea class="form-control @error('catatan') is-invalid @enderror" 
                    id="catatan" wire:model="catatan" rows="3"></textarea>
                @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Spare Parts Section - Optional (Hidden by Default) -->
            <div class="mb-3">
                <button type="button" class="btn btn-outline-success" 
                    wire:click="toggleSparePartsUsage">
                    <i class="bi bi-plus-lg"></i> Tambah Spare Part
                </button>
            </div>

            @if($use_spare_parts)
            <div class="card mb-3 border-success">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Form Spare Part</h6>
                    <button type="button" class="btn btn-sm btn-light" 
                        wire:click="toggleSparePartsUsage">
                        <i class="bi bi-x-lg"></i> Tutup
                    </button>
                </div>
                <div class="card-body">
                    <!-- Form Tambah Spare Part -->
                    <div class="row mb-3">
                        <div class="col-md-5 mb-3">
                            <label for="temp_spare_part_id" class="form-label">Pilih Spare Part</label>
                            <select class="form-select select2" 
                                id="temp_spare_part_id" wire:model="temp_spare_part_id" style="width: 100%;">
                                <option value="">-- Pilih Spare Part --</option>
                                @foreach($spareParts as $part)
                                    <option value="{{ $part->id }}">
                                        {{ $part->name }} (Stok: {{ $part->stock }} {{ $part->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="temp_qty_sparepart" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" 
                                id="temp_qty_sparepart" wire:model="temp_qty_sparepart" min="1" placeholder="0">
                        </div>

                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" wire:click="addSparePart">
                                <i class="bi bi-plus-lg"></i> Tambah
                            </button>
                        </div>
                    </div>

                    <!-- Daftar Spare Part yang Ditambahkan -->
                    @if(!empty($spare_parts_list))
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Spare Part</th>
                                    <th class="text-center">Jumlah</th>
                                    <th>Komentar</th>
                                    <th class="text-center" style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($spare_parts_list as $index => $part)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $part['name'] }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $part['qty'] }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $part['komentar'] ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger" 
                                            wire:click="removeSparePart({{ $index }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info small mb-0">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Catatan:</strong> Stok spare part akan berkurang otomatis sesuai jumlah yang digunakan saat laporan disimpan.
                    </div>
                    @else
                    <div class="alert alert-secondary small mb-0">
                        <i class="bi bi-info-circle"></i> Belum ada spare part yang ditambahkan. Pilih spare part di atas untuk menambahkannya.
                    </div>
                    @endif
                </div>
            </div>
            @endif

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
    // Function to initialize Select2
    function initSelect2() {
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            jQuery('.select2').select2({
                allowClear: true,
                width: '100%'
            });
        }
    }

    // Re-initialize Select2 after Livewire updates
    document.addEventListener('livewire:updated', function() {
        initSelect2();
    });

    document.addEventListener('livewire:navigated', function() {
        initSelect2();
    });

    // Initial Select2 initialization
    Livewire.on('select2:reinit', function() {
        initSelect2();
    });

    // Call on page load
    initSelect2();
</script>
