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
            'email' => 'required|email|unique:employees,email', // or users table if applicable
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,artisan,staff',
            'admin_level' => 'required|integer|min:0|max:3',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $employee = Employee::create($validated); // <-- Save to employees table

        
        return redirect()->back()->with('success', 'Employee added!');
    }
}