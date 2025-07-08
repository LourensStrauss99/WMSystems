<?php

namespace App\Http\Controllers;

use App\Models\Employee; // <-- Use Employee model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
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
}