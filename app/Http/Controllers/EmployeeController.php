<?php

namespace App\Http\Controllers;

use App\Models\Employee; // <-- Use Employee model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email|unique:employees', // unique in employees table
            'password' => 'required|min:6',
            'role' => 'required|in:admin,artisan,staff',
            'admin_level' => 'nullable|integer|min:0|max:3',
            'telephone' => 'required',
        ]);

        $data['password'] = Hash::make($data['password']);

        $employee = Employee::create($data); // <-- Save to employees table

        
        return redirect()->back()->with('success', 'Employee added!');
    }
}