<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Line;
use Illuminate\Http\Request;
use Auth;

class MachineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $machines = Machine::with('line')->paginate(10);
        return view('machine.index', compact('machines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lines = Line::where('status', 'active')->get();
        return view('machine.create', compact('lines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:machines',
            'code' => 'nullable|string|max:100',
            'line_id' => 'required|integer|exists:lines,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Machine::create($validated);

        return redirect()->route('machines.index')->with('success', 'Mesin berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Machine $machine)
    {
        $lines = Line::where('status', 'active')->get();
        return view('machine.edit', compact('machine', 'lines'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Machine $machine)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:machines,name,' . $machine->id,
            'code' => 'nullable|string|max:100',
            'line_id' => 'required|integer|exists:lines,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $machine->update($validated);

        return redirect()->route('machines.index')->with('success', 'Mesin berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Machine $machine)
    {
        $machine->delete();

        return redirect()->route('machines.index')->with('success', 'Mesin berhasil dihapus!');
    }

    /**
     * Import machines from file
     */
    public function importForm()
    {
        return view('machine.import');
    }

    /**
     * Export machines
     */
    public function export()
    {
        $machines = Machine::all();
        
        $filename = 'machines_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($machines) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['ID', 'Name', 'Code', 'Line', 'Description', 'Status', 'Created At']);
            
            // Data
            foreach ($machines as $machine) {
                fputcsv($file, [
                    $machine->id,
                    $machine->name,
                    $machine->code,
                    $machine->line,
                    $machine->description,
                    $machine->status,
                    $machine->created_at,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import machines
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->path(), 'r');
        $header = fgetcsv($handle);
        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 2 && !empty($row[1])) {
                Machine::updateOrCreate(
                    ['name' => $row[1]],
                    [
                        'code' => $row[2] ?? null,
                        'line' => $row[3] ?? null,
                        'description' => $row[4] ?? null,
                        'status' => $row[5] ?? 'active',
                    ]
                );
                $count++;
            }
        }
        fclose($handle);

        return redirect()->route('machines.index')->with('success', "$count mesin berhasil diimport!");
    }
}
