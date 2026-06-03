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
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'planned_time_minutes' => 'required|integer|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        // Extract year and month from start_date
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $year = $startDate->year;
        $month = $startDate->month;

        // Check if planned time for this month/year already exists
        $existing = PlannedTime::where('year', $year)
            ->where('month', $month)
            ->first();

        if ($existing) {
            return back()
                ->withInput()
                ->withErrors(['duplicate' => 'Planned time for this month/year already exists. Please edit it instead.']);
        }

        $validated['created_by'] = Auth::id();
        $validated['year'] = $year;
        $validated['month'] = $month;

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
