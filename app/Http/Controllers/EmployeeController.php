<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::whereHas('roles', function($q) {
            $q->where('name', 'operator');
        })->paginate(10);

        return view('employee.index', compact('employees'));
    }

    public function create()
    {
        return view('employee.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('operator');

        return redirect()->route('employees.index')->with('success', 'Operator berhasil ditambahkan!');
    }

    public function edit(User $employee)
    {
        // Cek apakah user adalah operator
        if (!$employee->hasRole('operator')) {
            abort(403, 'User ini bukan operator');
        }

        return view('employee.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        // Cek apakah user adalah operator
        if (!$employee->hasRole('operator')) {
            abort(403, 'User ini bukan operator');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $employee->name = $validated['name'];
        $employee->email = $validated['email'];

        if ($request->filled('password')) {
            $employee->password = Hash::make($validated['password']);
        }

        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Operator berhasil diperbarui!');
    }

    public function destroy(User $employee)
    {
        // Cek apakah user adalah operator
        if (!$employee->hasRole('operator')) {
            abort(403, 'User ini bukan operator');
        }

        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Operator berhasil dipecat!');
    }
}
