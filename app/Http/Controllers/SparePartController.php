<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use App\Models\LaporanHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SparePartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $spareParts = SparePart::paginate(10);
        return view('sparepart.index', compact('spareParts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sparepart.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:spare_parts',
            'code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        SparePart::create($validated);

        return redirect()->route('spare-parts.index')->with('success', 'Spare Part berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SparePart $sparePart)
    {
        return view('sparepart.edit', compact('sparePart'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SparePart $sparePart)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:spare_parts,name,' . $sparePart->id,
            'code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $sparePart->update($validated);

        return redirect()->route('spare-parts.index')->with('success', 'Spare Part berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SparePart $sparePart)
    {
        $sparePart->delete();

        return redirect()->route('spare-parts.index')->with('success', 'Spare Part berhasil dihapus!');
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('sparepart.import');
    }

    /**
     * Export spare parts
     */
    public function export()
    {
        $spareParts = SparePart::all();
        
        $filename = 'spare_parts_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($spareParts) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['ID', 'Name', 'Code', 'Description', 'Category', 'Stock', 'Unit', 'Status', 'Created At']);
            
            // Data
            foreach ($spareParts as $part) {
                fputcsv($file, [
                    $part->id,
                    $part->name,
                    $part->code,
                    $part->description,
                    $part->category,
                    $part->stock,
                    $part->unit,
                    $part->status,
                    $part->created_at,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Monitoring usage
     */
    public function monitoring(Request $request)
    {
        // Get filter parameters
        $bulan = $request->get('bulan') ?? now()->month;
        $tahun = $request->get('tahun') ?? now()->year;

        // Get spare parts usage for selected month
        $usage = LaporanHarian::select('sparepart', DB::raw('SUM(qty_sparepart) as total_qty'))
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->whereNotNull('sparepart')
            ->where('sparepart', '<>', '')
            ->groupBy('sparepart')
            ->orderByDesc('total_qty')
            ->get();

        // Get total spare parts count
        $totalUsage = $usage->sum('total_qty');

        // Get all available months with data
        $availableMonths = LaporanHarian::select(DB::raw('MONTH(created_at) as bulan'))
            ->distinct()
            ->orderBy('bulan')
            ->pluck('bulan');

        return view('sparepart.monitoring', compact(
            'usage',
            'bulan',
            'tahun',
            'totalUsage',
            'availableMonths'
        ));
    }

    /**
     * Import spare parts
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
                SparePart::updateOrCreate(
                    ['name' => $row[1]],
                    [
                        'code' => $row[2] ?? null,
                        'description' => $row[3] ?? null,
                        'category' => $row[4] ?? null,
                        'stock' => $row[5] ?? 0,
                        'unit' => $row[6] ?? 'pcs',
                        'status' => $row[7] ?? 'active',
                    ]
                );
                $count++;
            }
        }
        fclose($handle);

        return redirect()->route('spare-parts.index')->with('success', "$count spare part berhasil diimport!");
    }
}   

