<?php

namespace App\Http\Controllers;

use App\Models\PlannedTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlannedTimeController extends Controller
{
    /**
     * Check if user is admin
     */
    private function checkAdminAuthorization()
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized. Only admins can manage planned times.');
        }
    }

    /**
     * Display a listing of planned times with filtering and pagination
     */
    public function index()
    {
        $this->checkAdminAuthorization();
        
        // Get all planned times, optionally filtered by year
        $yearFilter = request('year');
        $query = PlannedTime::with('creator')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        if ($yearFilter) {
            $query->where('year', $yearFilter);
        }

        $plannedTimes = $query->paginate(20);

        // Get list of years for filter dropdown
        $years = PlannedTime::distinct()
            ->pluck('year')
            ->sort()
            ->reverse()
            ->values();

        return view('admin.planned-times.index', compact('plannedTimes', 'years', 'yearFilter'));
    }

    /**
     * Show the form for creating a new planned time
     */
    public function create()
    {
        $this->checkAdminAuthorization();
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        return view('admin.planned-times.create', compact('months'));
    }

    /**
     * Store a newly created planned time in storage
     */
    public function store(Request $request)
    {
        $this->checkAdminAuthorization();
        $validated = $request->validate([
            'year' => 'required|integer|min:2024|max:2099',
            'month' => 'required|integer|min:1|max:12',
            'planned_time_minutes' => 'required|integer|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        // Check if planned time for this month/year already exists
        $existing = PlannedTime::where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->first();

        if ($existing) {
            return back()
                ->withInput()
                ->withErrors(['duplicate' => 'Planned time for this month/year already exists. Please edit it instead.']);
        }

        $validated['created_by'] = Auth::id();

        PlannedTime::create($validated);

        return redirect()->route('planned-times.index')
            ->with('success', 'Planned time created successfully.');
    }

    /**
     * Display the specified resource
     */
    public function show(PlannedTime $plannedTime)
    {
        $this->checkAdminAuthorization();
        return view('admin.planned-times.show', compact('plannedTime'));
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(PlannedTime $plannedTime)
    {
        $this->checkAdminAuthorization();
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        return view('admin.planned-times.edit', compact('plannedTime', 'months'));
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, PlannedTime $plannedTime)
    {
        $this->checkAdminAuthorization();
        $validated = $request->validate([
            'planned_time_minutes' => 'required|integer|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $plannedTime->update($validated);

        return redirect()->route('planned-times.index')
            ->with('success', 'Planned time updated successfully.');
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(PlannedTime $plannedTime)
    {
        $this->checkAdminAuthorization();
        $plannedTime->delete();

        return redirect()->route('planned-times.index')
            ->with('success', 'Planned time deleted successfully.');
    }
}
