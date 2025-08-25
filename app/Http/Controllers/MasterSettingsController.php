<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Employee;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\GoodsReceivedVoucher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MasterSettingsController extends Controller
{
    // Debug route removed: tenant logic

    public function index()
    {
        // Removed tenant database switching logic
        $users = User::orderBy('created_at', 'desc')->get();
        $employees = Employee::orderBy('created_at', 'desc')->get();
        $purchaseOrders = class_exists('App\Models\PurchaseOrder') ? PurchaseOrder::orderBy('created_at', 'desc')->get() : collect();
        $suppliers = class_exists('App\Models\Supplier') ? Supplier::orderBy('created_at', 'desc')->get() : collect();
    return view('master-settings', compact('users', 'employees', 'purchaseOrders', 'suppliers'));
    }

    public function store(Request $request)
    {

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
            $validated['created_by'] = Auth::id();
            Employee::create($validated);

            // Validate and create user
            $userValidated = $request->validate([
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
            $userValidated['password'] = Hash::make($userValidated['password']);
            $userValidated['is_active'] = true;
            User::create($userValidated);

            // Removed tenant route logic
            return redirect()->route('master.settings')->with('success', 'User created successfully!');
        }
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        // Check permissions
        // Basic permission check: only allow admins (admin_level >= 1) to manage users
        if (!Auth::user() || Auth::user()->admin_level < 1) {
            abort(403, 'Access denied. User management privileges required.');
        }
        // Only allow users with admin_level >= 5 to edit superuser accounts
        if ($user->is_superuser && (!Auth::user() || Auth::user()->admin_level < 5)) {
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
        // Prevent privilege escalation
        if ($request->admin_level >= Auth::user()->admin_level) {
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
        // Basic permission check: only allow admins (admin_level >= 1) to manage users
        if (!Auth::user() || Auth::user()->admin_level < 1) {
            abort(403, 'Access denied.');
        }
        // Prevent deactivating superusers unless current user is admin_level >= 5
        if ($user->is_superuser && (!Auth::user() || Auth::user()->admin_level < 5)) {
            return back()->withErrors(['error' => 'Cannot deactivate superuser accounts.']);
        }
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully!");
    }
}