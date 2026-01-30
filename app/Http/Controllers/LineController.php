<?php

namespace App\Http\Controllers;

use App\Models\Line;
use Illuminate\Http\Request;

class LineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lines = Line::paginate(10);
        return view('line.index', compact('lines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('line.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:lines',
            'code' => 'nullable|string|max:100|unique:lines',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Line::create($validated);

        return redirect()->route('lines.index')->with('success', 'Line berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Line $line)
    {
        return view('line.edit', compact('line'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Line $line)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:lines,name,' . $line->id,
            'code' => 'nullable|string|max:100|unique:lines,code,' . $line->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $line->update($validated);

        return redirect()->route('lines.index')->with('success', 'Line berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Line $line)
    {
        $line->delete();

        return redirect()->route('lines.index')->with('success', 'Line berhasil dihapus!');
    }
}
