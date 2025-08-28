<?php

namespace App\Http\Controllers;

use App\Models\Employee; // <-- Use Employee model
use Illuminate\Http\Request;
// Removed: use App\Traits\TenantDatabaseSwitch;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    // Removed: use TenantDatabaseSwitch
    
    public function index()
    {
        // Switch to tenant database
    // Removed: $this->switchToTenantDatabase();
        
        $employees = \App\Models\Employee::paginate(15);
        return view('admin.employees.index', compact('employees'));
    }

    public function store(Request $request)
    {
    // Removed: $this->switchToTenantDatabase();
        
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
    // Removed: $this->switchToTenantDatabase();
        
        $employee = \App\Models\Employee::findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }
    
    public function update(Request $request, $id)
    {
    // Removed: $this->switchToTenantDatabase();
        
        $employee = \App\Models\Employee::findOrFail($id);

        $request->validate([
            // ...other validation...
        ]);

        $employee->update($request->all());

        return redirect()->route('users.index')->with('success', 'Employee updated successfully.');
    }

    public function toggleStatus($id)
    {
    // Removed: $this->switchToTenantDatabase();
        
        $employee = \App\Models\Employee::findOrFail($id);
        $employee->is_active = !$employee->is_active;
        $employee->save();

        return response()->json([
            'message' => 'Employee status updated successfully.',
        ]);
    }

    public function destroy($id)
    {
    // Removed: $this->switchToTenantDatabase();
        
        $employee = \App\Models\Employee::findOrFail($id);
        $employee->delete();

        return response()->json([
            'message' => 'Employee deleted successfully.',
        ]);
    }
}