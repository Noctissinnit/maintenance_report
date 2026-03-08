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
    public $catatan = '';
    public $spare_part_id = '';
    public $qty_sparepart = 0;
    public $komentar_sparepart = '';
    public $jenis_pekerjaan = '';
    public $scope = '';
    public $start_time = '';
    public $end_time = '';
    public $downtime_min = 0;
    public $tipe_laporan = '';
    public $tanggal_laporan = '';

    public $machines = [];
    public $lines = [];
    public $spareParts = [];

    protected $rules = [
        'machine_id' => 'integer|exists:machines,id',
        'line_id' => 'required|integer|exists:lines,id',
        'catatan' => 'nullable|string',
        'spare_part_id' => 'nullable|integer|exists:spare_parts,id',
        'qty_sparepart' => 'integer|min:0',
        'komentar_sparepart' => 'nullable|string',
        'jenis_pekerjaan' => 'required|in:corrective,preventive,change over product,modifikasi,utility',
        'scope' => 'required|in:Electrik,Mekanik,Utility,Building',
        'downtime_min' => 'integer|min:0',
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
