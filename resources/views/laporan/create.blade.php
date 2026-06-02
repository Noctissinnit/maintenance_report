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

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status Line <span class="text-danger">*</span></label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="line_status" id="line_on" value="on" 
                            @checked(old('line_status', 'on') === 'on')>
                        <label class="btn btn-outline-success" for="line_on" style="cursor: pointer;">
                            <i class="bi bi-check-circle"></i> Line ON
                        </label>

                        <input type="radio" class="btn-check" name="line_status" id="line_off" value="off" 
                            @checked(old('line_status') === 'off')>
                        <label class="btn btn-outline-danger" for="line_off" style="cursor: pointer;">
                            <i class="bi bi-x-circle"></i> Line OFF
                        </label>
                    </div>
                    <small class="text-muted d-block mt-2">ON = Downtime dihitung | OFF = Downtime tidak dihitung</small>
                </div>
            </div>

            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan/Deskripsi Masalah</label>
                <textarea class="form-control @error('catatan') is-invalid @enderror" 
                    id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
                @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Spare Parts Section - Optional (Hidden by Default) -->
            <div class="mb-3">
                <button type="button" class="btn btn-outline-success" 
                    onclick="toggleSparePartsForm()">
                    <i class="bi bi-plus-lg"></i> Tambah Spare Part
                </button>
            </div>

            <div id="sparePartsFormContainer" style="display: none;">
                <div class="card mb-3 border-success">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Form Spare Part</h6>
                        <button type="button" class="btn btn-sm btn-light" 
                            onclick="toggleSparePartsForm()">
                            <i class="bi bi-x-lg"></i> Tutup
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Form Tambah Spare Part -->
                        <div class="row mb-3">
                            <div class="col-md-5 mb-3">
                                <label for="temp_spare_part_id" class="form-label">Pilih Spare Part</label>
                                <select class="form-select select2" 
                                    id="temp_spare_part_id" style="width: 100%;" onchange="updateStockDisplay()">
                                    <option value="">-- Pilih Spare Part --</option>
                                    @foreach($spareParts as $part)
                                        @if($part->stock > 0)
                                            <option value="{{ $part->id }}" data-name="{{ $part->name }}" data-stock="{{ $part->stock }}" data-unit="{{ $part->unit }}">
                                                {{ $part->name }} (Stok: {{ $part->stock }} {{ $part->unit }})
                                            </option>
                                        @else
                                            <option value="{{ $part->id }}" data-name="{{ $part->name }}" data-stock="{{ $part->stock }}" data-unit="{{ $part->unit }}" disabled style="color: #ccc;">
                                                {{ $part->name }} (Stok: 0 - HABIS)
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1">Spare part dengan stok 0 tidak bisa dipilih</small>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="temp_stock_display" class="form-label">Stok Tersedia</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light" 
                                        id="temp_stock_display" readonly value="0">
                                    <span class="input-group-text" id="temp_unit_display">pcs</span>
                                </div>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label for="temp_qty_sparepart" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" 
                                    id="temp_qty_sparepart" min="1" placeholder="0">
                            </div>

                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="button" class="btn btn-primary w-100" onclick="addSparePart()">
                                    <i class="bi bi-plus-lg"></i> Tambah
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="temp_komentar_sparepart" class="form-label">Komentar</label>
                            <textarea class="form-control" 
                                id="temp_komentar_sparepart" rows="2" placeholder="Komentar spare part (opsional)"></textarea>
                        </div>

                        <!-- Daftar Spare Part yang Ditambahkan -->
                        <div id="sparePartsTableContainer" style="display: none;">
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
                                    <tbody id="sparePartsTable">
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-info small mb-0">
                                <i class="bi bi-info-circle"></i> 
                                <strong>Catatan:</strong> Stok spare part akan berkurang otomatis sesuai jumlah yang digunakan saat laporan disimpan.
                            </div>
                        </div>

                        <div id="sparePartsEmpty" class="alert alert-secondary small mb-0">
                            <i class="bi bi-info-circle"></i> Belum ada spare part yang ditambahkan. Pilih spare part di atas untuk menambahkannya.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden field untuk store multiple spare parts -->
            <input type="hidden" id="spare_parts_used" name="spare_parts_used" value="">

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

                <div class="col-md-6 mb-3">
                    <label for="planned_time_minutes" class="form-label">Planned Time (Menit) <small class="text-muted">(dari PPIC)</small></label>
                    <input type="number" class="form-control @error('planned_time_minutes') is-invalid @enderror" 
                        id="planned_time_minutes" name="planned_time_minutes" value="{{ old('planned_time_minutes', 0) }}" min="0" placeholder="Masukkan planned time dari jadwal PPIC">
                    <small class="text-muted d-block mt-1">Diisi manual berdasarkan jadwal yang ditetapkan departemen PPIC</small>
                    @error('planned_time_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

            <!-- DEBUG: Show spare parts data yang akan dikirim -->
            <div class="alert alert-dark small mt-3" id="debugSparePartsData" style="display: none;">
                <strong>DEBUG - Data Spare Parts yang akan dikirim:</strong>
                <pre id="debugSparePartsContent" style="background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto;"></pre>
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
        
        // Initialize Select2 for temp spare part dropdown
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            jQuery('#temp_spare_part_id').select2();
            
            // Add event listener for temp spare part selection
            jQuery('#temp_spare_part_id').on('select2:select', function() {
                updateStockDisplay();
            }).on('select2:unselecting', function() {
                updateStockDisplay();
            });
        }
        
        // Initialize stock display on load
        updateStockDisplay();
        
        // Form submit handler - ensure line_id is filled and serialize spare parts
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // DEBUG
                console.log('=== FORM SUBMIT DEBUG ===');
                console.log('sparePartsList global:', sparePartsList);
                
                const lineDisplaySelect = document.getElementById('line_id_display');
                const lineHiddenInput = document.getElementById('line_id_hidden');
                
                if (lineDisplaySelect && lineDisplaySelect.value) {
                    lineHiddenInput.value = lineDisplaySelect.value;
                }

                // Serialize spare parts list to JSON
                const hiddenField = document.getElementById('spare_parts_used');
                console.log('Hidden field current value:', hiddenField.value);
                console.log('Spare Parts List length:', sparePartsList ? sparePartsList.length : 'undefined');
                
                if (sparePartsList && sparePartsList.length > 0) {
                    const jsonData = JSON.stringify(sparePartsList);
                    console.log('JSON being sent:', jsonData);
                    hiddenField.value = jsonData;
                    
                    // Show debug info on page
                    const debugDiv = document.getElementById('debugSparePartsData');
                    const debugContent = document.getElementById('debugSparePartsContent');
                    debugDiv.style.display = 'block';
                    debugContent.innerText = jsonData;
                } else {
                    console.log('No spare parts in list - clearing hidden field');
                    hiddenField.value = '';
                    document.getElementById('debugSparePartsData').style.display = 'none';
                }
                
                console.log('=== END DEBUG ===');
            });
        }
    });

    // ===== MULTIPLE SPARE PARTS FUNCTIONALITY =====
    let sparePartsList = [];

    function updateStockDisplay() {
        const sparePartSelect = document.getElementById('temp_spare_part_id');
        const stockDisplay = document.getElementById('temp_stock_display');
        const unitDisplay = document.getElementById('temp_unit_display');
        const addButton = document.querySelector('button[onclick="addSparePart()"]');
        
        if (sparePartSelect.value) {
            const selectedOption = sparePartSelect.options[sparePartSelect.selectedIndex];
            const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
            const unit = selectedOption.getAttribute('data-unit') || 'pcs';
            
            stockDisplay.value = stock;
            unitDisplay.textContent = unit;

            // Disable add button if stock is 0
            if (stock === 0) {
                addButton.disabled = true;
                addButton.classList.add('btn-warning');
                addButton.classList.remove('btn-primary');
                addButton.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Stok Habis';
            } else {
                addButton.disabled = false;
                addButton.classList.remove('btn-warning');
                addButton.classList.add('btn-primary');
                addButton.innerHTML = '<i class="bi bi-plus-lg"></i> Tambah';
            }
        } else {
            stockDisplay.value = '0';
            unitDisplay.textContent = 'pcs';
            addButton.disabled = false;
            addButton.classList.remove('btn-warning');
            addButton.classList.add('btn-primary');
            addButton.innerHTML = '<i class="bi bi-plus-lg"></i> Tambah';
        }
    }

    function toggleSparePartsForm() {
        const container = document.getElementById('sparePartsFormContainer');
        container.style.display = container.style.display === 'none' ? 'block' : 'none';
        
        // Reset form when opened
        if (container.style.display === 'block') {
            document.getElementById('temp_spare_part_id').value = '';
            document.getElementById('temp_qty_sparepart').value = '';
            document.getElementById('temp_komentar_sparepart').value = '';
            updateStockDisplay();
            if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                jQuery('#temp_spare_part_id').val('').trigger('change');
            }
        }
    }

    function addSparePart() {
        const tempSparePartId = document.getElementById('temp_spare_part_id').value;
        const tempQty = document.getElementById('temp_qty_sparepart').value;
        const tempKomentar = document.getElementById('temp_komentar_sparepart').value;
        const selectedOption = document.getElementById('temp_spare_part_id').options[document.getElementById('temp_spare_part_id').selectedIndex];
        const availableStock = parseInt(selectedOption.getAttribute('data-stock')) || 0;

        // Validation
        if (!tempSparePartId) {
            alert('Silakan pilih spare part');
            return;
        }

        // Check if stock is 0
        if (availableStock === 0) {
            alert('❌ Spare part ini tidak memiliki stok. Silakan pilih spare part lain.');
            return;
        }

        if (!tempQty || parseInt(tempQty) <= 0) {
            alert('Silakan masukkan jumlah yang valid (minimal 1)');
            return;
        }

        // Check if quantity exceeds available stock
        if (parseInt(tempQty) > availableStock) {
            alert(`❌ Jumlah melebihi stok tersedia!\n\nStok tersedia: ${availableStock}\nJumlah diminta: ${tempQty}`);
            return;
        }

        // Check if spare part already exists
        if (sparePartsList.some(item => item.id == tempSparePartId)) {
            alert('⚠️ Spare part ini sudah ditambahkan di list');
            return;
        }

        // Get spare part name
        const sparePartName = selectedOption.getAttribute('data-name');

        // Add to list
        sparePartsList.push({
            id: tempSparePartId,
            name: sparePartName,
            qty: parseInt(tempQty),
            komentar: tempKomentar,
            availableStock: availableStock
        });

        // Update display
        updateSparePartsDisplay();

        // Reset form
        document.getElementById('temp_spare_part_id').value = '';
        document.getElementById('temp_qty_sparepart').value = '';
        document.getElementById('temp_komentar_sparepart').value = '';
        updateStockDisplay();
        if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
            jQuery('#temp_spare_part_id').val('').trigger('change');
        }
    }

    function removeSparePart(index) {
        sparePartsList.splice(index, 1);
        updateSparePartsDisplay();
    }

    function updateSparePartsDisplay() {
        const tableContainer = document.getElementById('sparePartsTableContainer');
        const emptyMessage = document.getElementById('sparePartsEmpty');
        const tableBody = document.getElementById('sparePartsTable');
        const hiddenField = document.getElementById('spare_parts_used');

        if (sparePartsList.length === 0) {
            tableContainer.style.display = 'none';
            emptyMessage.style.display = 'block';
            hiddenField.value = '';
        } else {
            tableContainer.style.display = 'block';
            emptyMessage.style.display = 'none';

            // Build table rows
            tableBody.innerHTML = sparePartsList.map((item, index) => `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${item.name}</strong></td>
                    <td class="text-center"><span class="badge bg-info">${item.qty}</span></td>
                    <td><small class="text-muted">${item.komentar || '-'}</small></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeSparePart(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');

            // Update hidden field with JSON data (remove availableStock before storing)
            const dataToStore = sparePartsList.map(item => ({
                id: item.id,
                name: item.name,
                qty: item.qty,
                komentar: item.komentar
            }));
            hiddenField.value = JSON.stringify(dataToStore);
            console.log('Hidden field updated with:', hiddenField.value);
        }
    }
</script>
@endsection

