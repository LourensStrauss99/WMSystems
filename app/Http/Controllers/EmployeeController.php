<?php

namespace App\Http\Controllers;

use App\Models\Employee; // <-- Use Employee model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = \App\Models\Employee::paginate(15);
        return view('admin.employees.index', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'telephone' => [
                'required',
                'regex:/^\+?[0-9]{7,20}$/'
            ],
            'email' => 'required|email|unique:employees,email',
            'password' => 'required|string|min:6|confirmed', // Add confirmation
            'role' => 'required|in:admin,manager,supervisor,artisan,staff,user',
            'admin_level' => 'required|integer|min:0|max:5',
            // Add these optional fields if they don't exist
            'employee_id' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $employee = Employee::create($validated);

        return redirect()->back()->with('success', "Employee '{$employee->name} {$employee->surname}' added successfully!");
    }

    public function edit($id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }
    
    public function update(Request $request, $id)
    {
        $employee = \App\Models\Employee::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email,' . $employee->id,
            // ...other validation...
        ]);

        $employee->update($request->all());

        return redirect()->route('users.index')->with('success', 'Employee updated successfully.');
    }

    public function toggleStatus($id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $employee->is_active = !$employee->is_active;
        $employee->save();

        return response()->json([
            'success' => true,
            'message' => 'Employee status updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $employee = \App\Models\Employee::findOrFail($id);
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully.',
        ]);
    }
}