<?php

namespace App\Http\Controllers;

use App\Models\PlannedTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlannedTimeController extends Controller
{
    /**
     * Check if user has permission to manage planned times
     */
    private function checkAdminAuthorization()
    {
        if (!Auth::user()->can('manage_planned_times')) {
            abort(403, 'Unauthorized. Only admins and department heads can manage planned times.');
        }
    }

    /**
     * Display a listing of planned times with filtering and pagination
     */
    public function index()
    {
        $this->checkAdminAuthorization();
        
        // Get filter params
        $yearFilter = request('year');
        $monthFilter = request('month');
        
        $query = PlannedTime::with('creator')
            ->orderBy('start_date', 'desc');

        if ($yearFilter) {
            $query->where('year', $yearFilter);
        }
        
        if ($monthFilter) {
            $query->where('month', $monthFilter);
        }

        // Clone query for total before pagination
        $totalPlannedMinutes = (clone $query)->sum('planned_time_minutes');

        $plannedTimes = $query->paginate(20);

        // Get list of years for filter dropdown
        $years = PlannedTime::distinct()
            ->pluck('year')
            ->sort()
            ->reverse()
            ->values();

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March',
            4 => 'April', 5 => 'May', 6 => 'June',
            7 => 'July', 8 => 'August', 9 => 'September',
            10 => 'October', 11 => 'November', 12 => 'December'
        ];

        return view('admin.planned-times.index', compact('plannedTimes', 'years', 'yearFilter', 'monthFilter', 'months', 'totalPlannedMinutes'));
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
        $activeMachinesCount = \App\Models\Machine::where('status', 'active')->count();

        return view('admin.planned-times.create', compact('months', 'activeMachinesCount'));
    }

    /**
     * Store a newly created planned time in storage
     */
    public function store(Request $request)
    {
        $this->checkAdminAuthorization();
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'planned_time_minutes' => 'required|integer|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        // Extract year and month from start_date
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $validated['created_by'] = Auth::id();
        $validated['year'] = $startDate->year;
        $validated['month'] = $startDate->month;

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
        $activeMachinesCount = \App\Models\Machine::where('status', 'active')->count();

        return view('admin.planned-times.edit', compact('plannedTime', 'months', 'activeMachinesCount'));
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, PlannedTime $plannedTime)
    {
        $this->checkAdminAuthorization();
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'planned_time_minutes' => 'required|integer|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        // Extract year and month from start_date
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $validated['year'] = $startDate->year;
        $validated['month'] = $startDate->month;

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
