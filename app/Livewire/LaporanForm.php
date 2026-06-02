<?php

namespace App\Livewire;

use App\Models\LaporanHarian;
use App\Models\Machine;
use App\Models\SparePart;
use App\Models\Line;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanForm extends Component
{
    public $machine_id = '';
    public $line_id = '';
    public $line_status = 'on';
    public $catatan = '';
    public $spare_part_id = '';
    public $qty_sparepart = 0;
    public $komentar_sparepart = '';
    public $jenis_pekerjaan = '';
    public $scope = '';
    public $start_time = '';
    public $end_time = '';
    public $downtime_min = 0;
    public $planned_time_minutes = 0;
    public $tipe_laporan = '';
    public $tanggal_laporan = '';
    
    // Multiple spare parts
    public $use_spare_parts = false;
    public $spare_parts_list = [];
    public $temp_spare_part_id = '';
    public $temp_qty_sparepart = '';
    public $temp_komentar_sparepart = '';

    public $machines = [];
    public $lines = [];
    public $spareParts = [];

    protected $rules = [
        'machine_id' => 'integer|exists:machines,id',
        'line_id' => 'required|integer|exists:lines,id',
        'line_status' => 'required|in:on,off',
        'catatan' => 'nullable|string',
        'spare_part_id' => 'nullable|integer|exists:spare_parts,id',
        'qty_sparepart' => 'integer|min:0',
        'komentar_sparepart' => 'nullable|string',
        'spare_parts_used' => 'nullable|string',
        'jenis_pekerjaan' => 'required|in:corrective,preventive,change over product,modifikasi,utility',
        'scope' => 'required|in:Electrik,Mekanik,Utility,Building',
        'downtime_min' => 'integer|min:0',
        'planned_time_minutes' => 'nullable|integer|min:0',
        'tipe_laporan' => 'in:harian,mingguan,bulanan',
        'tanggal_laporan' => 'required|date',
        'start_time' => 'nullable|date_format:Y-m-d\TH:i',
        'end_time' => 'nullable|date_format:Y-m-d\TH:i|after:start_time',
    ];

    public function mount()
    {
        $this->machines = Machine::where('status', 'active')->get();
        $this->lines = Line::where('status', 'active')->get();
        $this->spareParts = SparePart::where('status', 'active')->get();
        $this->tanggal_laporan = now()->format('Y-m-d');
    }

    // Auto-fill line ketika machine dipilih
    #[\Livewire\Attributes\On('update:machine_id')]
    public function updatedMachineId($value)
    {
        if ($value) {
            $machine = Machine::find($value);
            if ($machine && $machine->line_id) {
                $this->line_id = $machine->line_id;
            }
        } else {
            $this->line_id = '';
        }
    }

    // Calculate downtime ketika start_time atau end_time berubah
    #[\Livewire\Attributes\On('update:start_time')]
    #[\Livewire\Attributes\On('update:end_time')]
    public function calculateDowntime()
    {
        if ($this->start_time && $this->end_time) {
            $start = Carbon::createFromFormat('Y-m-d\TH:i', $this->start_time);
            $end = Carbon::createFromFormat('Y-m-d\TH:i', $this->end_time);
            $this->downtime_min = $start->diffInMinutes($end);
        }
    }

    // Toggle spare parts usage
    public function toggleSparePartsUsage()
    {
        $this->use_spare_parts = !$this->use_spare_parts;
        if (!$this->use_spare_parts) {
            $this->spare_parts_list = [];
            $this->resetTempFields();
        }
    }

    // Add spare part to list
    public function addSparePart()
    {
        if (!$this->temp_spare_part_id || !$this->temp_qty_sparepart) {
            session()->flash('error', 'Silakan pilih spare part dan masukkan jumlah');
            return;
        }

        $sparePart = SparePart::find($this->temp_spare_part_id);
        if (!$sparePart) {
            session()->flash('error', 'Spare part tidak ditemukan');
            return;
        }

        // Check if spare part already exists in list
        $exists = collect($this->spare_parts_list)->contains(fn($item) => $item['id'] == $this->temp_spare_part_id);
        if ($exists) {
            session()->flash('error', 'Spare part sudah ditambahkan, gunakan tombol edit untuk mengubah jumlah');
            return;
        }

        // Add to list
        $this->spare_parts_list[] = [
            'id' => $this->temp_spare_part_id,
            'name' => $sparePart->name,
            'qty' => (int)$this->temp_qty_sparepart,
            'komentar' => $this->temp_komentar_sparepart,
        ];

        $this->resetTempFields();
    }

    // Remove spare part from list
    public function removeSparePart($index)
    {
        unset($this->spare_parts_list[$index]);
        $this->spare_parts_list = array_values($this->spare_parts_list);
    }

    // Reset temporary fields
    private function resetTempFields()
    {
        $this->temp_spare_part_id = '';
        $this->temp_qty_sparepart = '';
        $this->temp_komentar_sparepart = '';
    }

    public function submit()
    {
        // Check permission
        if (!Auth::user()->can('create_laporan')) {
            abort(403, 'Unauthorized');
        }

        // Add required validation untuk corrective, preventive, dan change over product
        if (in_array($this->jenis_pekerjaan, ['corrective', 'preventive', 'change over product'])) {
            $this->rules['start_time'] = 'required|date_format:Y-m-d\TH:i';
            $this->rules['end_time'] = 'required|date_format:Y-m-d\TH:i|after:start_time';
        }

        $validated = $this->validate();
        $validated['user_id'] = Auth::id();

        // Get machine name
        if ($validated['machine_id']) {
            $machine = Machine::find($validated['machine_id']);
            $validated['mesin_name'] = $machine->name;
        }

        // Get line name
        if ($validated['line_id']) {
            $line = Line::find($validated['line_id']);
            $validated['line'] = $line->name;
        }

        // Calculate downtime for corrective, preventive dan change over product types
        if (in_array($validated['jenis_pekerjaan'], ['corrective', 'preventive', 'change over product']) && $this->start_time && $this->end_time) {
            $validated['downtime_min'] = $this->downtime_min;
        } else {
            $validated['downtime_min'] = 0;
        }

        // Store multiple spare parts data if used
        if ($this->use_spare_parts && !empty($this->spare_parts_list)) {
            $validated['spare_parts_used'] = json_encode($this->spare_parts_list);
            
            // Update stock for each spare part
            foreach ($this->spare_parts_list as $sparePartData) {
                $sparePart = SparePart::find($sparePartData['id']);
                if ($sparePart) {
                    // Reduce stock
                    $sparePart->decrement('stock', $sparePartData['qty']);
                }
            }

            // Set first spare part for backward compatibility
            if (isset($this->spare_parts_list[0])) {
                $validated['spare_part_id'] = $this->spare_parts_list[0]['id'];
                $validated['qty_sparepart'] = $this->spare_parts_list[0]['qty'];
                $validated['komentar_sparepart'] = $this->spare_parts_list[0]['komentar'];
            }
        }

        LaporanHarian::create($validated);

        session()->flash('success', 'Laporan berhasil disimpan!');
        return redirect()->route('laporan.index');
    }

    public function render()
    {
        return view('livewire.laporan-form', [
            'machines' => $this->machines,
            'lines' => $this->lines,
            'spareParts' => $this->spareParts,
        ]);
    }
}
