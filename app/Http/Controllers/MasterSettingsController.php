<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class MasterSettingsController extends Controller
{
    public function index()
    {
        $users = User::orderBy('is_superuser', 'desc')
                    ->orderBy('admin_level', 'desc')
                    ->orderBy('created_at', 'asc')
                    ->get();

        // Add employees to the data
        $employees = Employee::orderBy('admin_level', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->get();

        $inventory = Inventory::orderBy('name', 'asc')->get();

        return view('master-settings', compact('users', 'employees', 'inventory'));
    }

    public function storeEmployee(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string',
            'admin_level' => 'nullable|integer|between:0,5',
            'employee_id' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
        ]);

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('password'), // Default password
            'role' => $validated['role'],
            'admin_level' => $validated['admin_level'] ?? 0,
            'employee_id' => $validated['employee_id'],
            'department' => $validated['department'],
            'position' => $validated['position'],
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('master.settings')
            ->with('success', 'User created successfully! Default password is "password".');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Check permissions
        if (!Auth::user()->canManageUsers()) {
            abort(403, 'Access denied. User management privileges required.');
        }

        // Prevent editing superusers unless you are one
        if ($user->is_superuser && !Auth::user()->isSuperUser()) {
            abort(403, 'Only superusers can edit superuser accounts.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,artisan,staff,manager,supervisor',
            'admin_level' => 'required|integer|min:0|max:5',
            'is_active' => 'boolean',
        ]);

        // Prevent privilege escalation
        if (!Auth::user()->isSuperUser() && $request->admin_level >= Auth::user()->admin_level) {
            return back()->withErrors(['admin_level' => 'You cannot set admin level equal or higher than your own.']);
        }

        $user->update($request->only([
            'name', 'email', 'role', 'admin_level', 'is_active', 
            'department', 'position', 'phone'
        ]));

        return back()->with('success', 'User updated successfully!');
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        
        if (!Auth::user()->canManageUsers()) {
            abort(403, 'Access denied.');
        }

        // Prevent deactivating superusers
        if ($user->is_superuser && !Auth::user()->isSuperUser()) {
            return back()->withErrors(['error' => 'Cannot deactivate superuser accounts.']);
        }

        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully!");
    }
}