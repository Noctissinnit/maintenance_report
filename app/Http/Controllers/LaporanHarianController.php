<?php

namespace App\Http\Controllers;

use App\Models\LaporanHarian;
use App\Models\Machine;
use App\Models\SparePart;
use App\Models\Line;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class LaporanHarianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Cek permission
        if (!Auth::user()->can('view_own_laporan')) {
            abort(403, 'Unauthorized');
        }

        // Base query
        $query = LaporanHarian::select(
                'id',
                'user_id',
                'machine_id',
                'line_id',
                'mesin_name',
                'line',
                'jenis_pekerjaan',
                'tipe_laporan',
                'downtime_min',
                'tanggal_laporan'
            )
            ->with([
                'machine:id,name',
                'line:id,name'
            ])
            ->orderBy('tanggal_laporan', 'desc');

        // Jika bukan admin → hanya lihat laporan sendiri
        if (!Auth::user()->hasRole('admin')) {
            $query->where('user_id', Auth::id());
        }

        // Eksekusi query
        $laporan = $query->paginate(10);

        return view('laporan.index', compact('laporan'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check permission
        if (!Auth::user()->can('create_laporan')) {
            abort(403, 'Unauthorized');
        }

        // Get list of lines
        $lines = Line::where('status', 'active')->get();

        // Get list of machines
        $machines = Machine::where('status', 'active')->get();
        
        // Get list of spare parts
        $spareParts = SparePart::where('status', 'active')->get();

        return view('laporan.create', compact('lines', 'machines', 'spareParts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check permission
        if (!Auth::user()->can('create_laporan')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'machine_id' => 'integer|exists:machines,id',
            'line_id' => 'required|integer|exists:lines,id',
            'catatan' => 'nullable|string',
            'spare_part_id' => 'nullable|integer|exists:spare_parts,id',
            'qty_sparepart' => 'integer|min:0',
            'komentar_sparepart' => 'nullable|string',
            'jenis_pekerjaan' => 'required|in:corrective,preventive,modifikasi,utility',
            'scope' => 'required|in:Electrik,Mekanik,Utility,Building',
            'start_time' => 'nullable|date_format:Y-m-d\TH:i',
            'end_time' => 'nullable|date_format:Y-m-d\TH:i|after:start_time',
            'downtime_min' => 'integer|min:0',
            'tipe_laporan' => 'in:harian,mingguan,bulanan',
            'tanggal_laporan' => 'required|date',
        ]);

        $validated['user_id'] = Auth::id();
        
        // Get machine name from selected machine and line name from line
        if ($validated['machine_id']) {
            $machine = Machine::find($validated['machine_id']);
            $validated['mesin_name'] = $machine->name;
        }
        
        // Get line name from selected line
        if ($validated['line_id']) {
            $line = Line::find($validated['line_id']);
            $validated['line'] = $line->name;
        }

        // Calculate downtime for corrective and preventive types
        if (($validated['jenis_pekerjaan'] === 'corrective' || $validated['jenis_pekerjaan'] === 'preventive') && isset($validated['start_time']) && isset($validated['end_time'])) {
            $start = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['start_time']);
            $end = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['end_time']);
            $validated['downtime_min'] = (int) $start->diffInMinutes($end);
        } else {
            $validated['downtime_min'] = 0;
        }
        
        LaporanHarian::create($validated);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $laporan = LaporanHarian::findOrFail($id);
        
        // Check permission
        if ($laporan->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        if (!Auth::user()->can('edit_laporan')) {
            abort(403, 'Unauthorized');
        }

        // Get list of lines
        $lines = Line::where('status', 'active')->get();

        // Get list of machines
        $machines = Machine::where('status', 'active')->get();

        // Get list of spare parts
        $spareParts = SparePart::where('status', 'active')->get();

        return view('laporan.edit', compact('laporan', 'lines', 'machines', 'spareParts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $laporan = LaporanHarian::findOrFail($id);

        // Check permission
        if ($laporan->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        if (!Auth::user()->can('edit_laporan')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'machine_id' => 'integer|exists:machines,id',
            'line_id' => 'required|integer|exists:lines,id',
            'catatan' => 'nullable|string',
            'spare_part_id' => 'nullable|integer|exists:spare_parts,id',
            'qty_sparepart' => 'integer|min:0',
            'komentar_sparepart' => 'nullable|string',
            'jenis_pekerjaan' => 'required|in:corrective,preventive,modifikasi,utility',
            'scope' => 'required|in:Electrik,Mekanik,Utility,Building',
            'start_time' => 'nullable|date_format:Y-m-d\TH:i',
            'end_time' => 'nullable|date_format:Y-m-d\TH:i|after:start_time',
            'downtime_min' => 'integer|min:0',
            'tipe_laporan' => 'in:harian,mingguan,bulanan',
            'tanggal_laporan' => 'required|date',
        ]);

        // Get machine name from selected machine
        if ($validated['machine_id']) {
            $machine = Machine::find($validated['machine_id']);
            $validated['mesin_name'] = $machine->name;
        }

        // Get line name from selected line
        if ($validated['line_id']) {
            $line = Line::find($validated['line_id']);
            $validated['line'] = $line->name;
        }

        // Calculate downtime for corrective and preventive types
        if (($validated['jenis_pekerjaan'] === 'corrective' || $validated['jenis_pekerjaan'] === 'preventive') && isset($validated['start_time']) && isset($validated['end_time'])) {
            $start = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['start_time']);
            $end = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['end_time']);
            $validated['downtime_min'] = (int) $start->diffInMinutes($end);
        } else {
            $validated['downtime_min'] = 0;
        }

        $laporan->update($validated);

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $laporan = LaporanHarian::findOrFail($id);

        // Check permission
        if ($laporan->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }

        if (!Auth::user()->can('delete_laporan')) {
            abort(403, 'Unauthorized');
        }

        $laporan->delete();

        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dihapus!');
    }
}
