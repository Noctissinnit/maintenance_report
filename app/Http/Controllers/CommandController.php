<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\User;
use Illuminate\Http\Request;

class CommandController extends Controller
{
    /**
     * Display a listing of commands (for supervisors)
     */
    public function index()
    {
        // Check if user is supervisor or admin
        if (!auth()->user()->hasRole(['supervisor', 'admin'])) {
            abort(403, 'Unauthorized');
        }

        $status = request('status');
        $query = Command::with(['departmentHead', 'supervisor']);

        if ($status) {
            $query->where('status', $status);
        }

        $commands = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('commands.index', [
            'commands' => $commands,
            'status' => $status,
        ]);
    }

    /**
     * Show the form for creating a new command (for department heads)
     */
    public function create()
    {
        // Check if user is department head
        if (!auth()->user()->hasRole('department_head')) {
            abort(403, 'Unauthorized');
        }

        $supervisors = User::role('supervisor')->pluck('name', 'id');

        return view('commands.create', [
            'supervisors' => $supervisors,
        ]);
    }

    /**
     * Store a newly created command in storage
     */
    public function store(Request $request)
    {
        // Check if user is department head
        if (!auth()->user()->hasRole('department_head')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'command_text' => 'required|string',
            'action_plan' => 'required|string',
            'supervisor_id' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $validated['department_head_id'] = auth()->id();
        $validated['created_date'] = now();
        $validated['status'] = 'pending';

        Command::create($validated);

        return redirect()->route('commands.list-department-head')
            ->with('success', 'Command berhasil dibuat');
    }

    /**
     * Display a listing of commands created by the department head
     */
    public function listDepartmentHead()
    {
        // Check if user is department head
        if (!auth()->user()->hasRole('department_head')) {
            abort(403, 'Unauthorized');
        }

        $status = request('status');
        $query = Command::where('department_head_id', auth()->id())
            ->with(['departmentHead', 'supervisor']);

        if ($status) {
            $query->where('status', $status);
        }

        $commands = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('commands.department-head-list', [
            'commands' => $commands,
            'status' => $status,
        ]);
    }

    /**
     * Display the specified command
     */
    public function show(Command $command)
    {
        // Check authorization - can view if department head created it or if supervisor is assigned
        if (auth()->id() !== $command->department_head_id && auth()->id() !== $command->supervisor_id) {
            abort(403, 'Unauthorized');
        }

        return view('commands.show', [
            'command' => $command,
        ]);
    }

    /**
     * Show the form for editing command status (for supervisors)
     */
    public function editStatus(Command $command)
    {
        // Check if user is the assigned supervisor
        if (auth()->id() !== $command->supervisor_id) {
            abort(403, 'Unauthorized');
        }

        return view('commands.edit-status', [
            'command' => $command,
        ]);
    }

    /**
     * Update the command status
     */
    public function updateStatus(Request $request, Command $command)
    {
        // Check if user is the assigned supervisor
        if (auth()->id() !== $command->supervisor_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'supervisor_notes' => 'nullable|string',
        ]);

        $command->update($validated);

        return redirect()->route('commands.index')
            ->with('success', 'Status command berhasil diperbarui');
    }

    /**
     * Show the form for editing a command (for department heads)
     */
    public function edit(Command $command)
    {
        // Check if user is the department head who created the command
        if (auth()->id() !== $command->department_head_id) {
            abort(403, 'Unauthorized');
        }

        // Can only edit if status is still pending
        if ($command->status !== 'pending') {
            abort(403, 'Cannot edit command that is not pending');
        }

        $supervisors = User::role('supervisor')->pluck('name', 'id');

        return view('commands.edit', [
            'command' => $command,
            'supervisors' => $supervisors,
        ]);
    }

    /**
     * Update the command
     */
    public function update(Request $request, Command $command)
    {
        // Check if user is the department head who created the command
        if (auth()->id() !== $command->department_head_id) {
            abort(403, 'Unauthorized');
        }

        // Can only edit if status is still pending
        if ($command->status !== 'pending') {
            abort(403, 'Cannot edit command that is not pending');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'command_text' => 'required|string',
            'action_plan' => 'required|string',
            'supervisor_id' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $command->update($validated);

        return redirect()->route('commands.list-department-head')
            ->with('success', 'Command berhasil diperbarui');
    }

    /**
     * Remove the specified command from storage
     */
    public function destroy(Command $command)
    {
        // Check if user is the department head who created the command
        if (auth()->id() !== $command->department_head_id) {
            abort(403, 'Unauthorized');
        }

        // Can only delete if status is still pending
        if ($command->status !== 'pending') {
            abort(403, 'Cannot delete command that is not pending');
        }

        $command->delete();

        return redirect()->route('commands.list-department-head')
            ->with('success', 'Command berhasil dihapus');
    }

    /**
     * Handle image upload for Summernote
     */
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // Validate file
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
            ]);

            // Create filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Store image in public/images/commands directory
            $path = $file->storeAs('images/commands', $filename, 'public');
            
            // Return URL
            return response()->json([
                'url' => asset('storage/' . $path),
                'path' => $path,
            ]);
        }

        return response()->json([
            'message' => 'Tidak ada file yang diunggah'
        ], 400);
    }
}
