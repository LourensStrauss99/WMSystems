<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MasterSettingsController extends Controller
{
    // Debug route to check tenancy and connection info
    public function debug(Request $request)
    {
        return response()->json([
            'tenant_id' => tenant('id'),
            'default_connection' => DB::getDefaultConnection(),
            'database_name' => DB::getDatabaseName(),
            'request_host' => $request->getHost(),
            'route_name' => $request->route() ? $request->route()->getName() : null,
        ]);
    }
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

        $inventory = Inventory::orderBy('description', 'asc')->get();

        return view('master-settings', compact('users', 'employees', 'inventory'));
    }

    public function store(Request $request)
    {
        // Debug: Check which database connection is being used
        Log::info('MasterSettingsController@store - Database info', [
            'default_connection' => DB::getDefaultConnection(),
            'database_name' => DB::getDatabaseName(),
            'tenant_id' => tenant('id'),
            'request_host' => $request->getHost(),
            'route_name' => $request->route() ? $request->route()->getName() : null,
            'middleware' => method_exists($request, 'route') ? $request->route()->gatherMiddleware() : [],
        ]);

        // You can branch logic based on account_type if needed
        if ($request->account_type === 'employee') {
            // Validate and create employee
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email',
                'department' => 'nullable|string|max:100',
                'position' => 'nullable|string|max:100',
                'employee_id' => 'required|string|max:50|unique:employees,employee_id',
                'telephone' => 'nullable|string|max:20',
                'password' => 'required|string|confirmed|min:8',
            ]);
            $validated['password'] = Hash::make($validated['password']);
            $validated['created_by'] = auth()->id();
            
            // Create employee - the tenancy package should handle database switching automatically
            \App\Models\Employee::create($validated);

            return redirect()->route(tenant('id') ? 'settings.index' : 'master.settings')->with('success', 'Employee created successfully!');
        } else {
            // Validate and create user
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'role' => 'required|string',
                'admin_level' => 'nullable|integer|between:0,5',
                'user_id' => 'required|string|max:50|unique:users,user_id',
                'department' => 'nullable|string|max:100',
                'position' => 'nullable|string|max:100',
                'telephone' => 'nullable|string|max:20',
                'password' => 'required|string|confirmed|min:8',
            ]);
            $validated['password'] = Hash::make($validated['password']);
            $validated['created_by'] = auth()->id();
            $validated['is_active'] = true;
            
            // Create user - the tenancy package should handle database switching automatically
            \App\Models\User::create($validated);

            return redirect()->route(tenant('id') ? 'settings.index' : 'master.settings')->with('success', 'User created successfully!');
        }
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