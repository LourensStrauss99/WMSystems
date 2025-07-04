<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->canManageUsers()) {
                abort(403, 'Unauthorized access. User management privileges required.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::orderBy('admin_level', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:6', // Make password optional since you set default
            'role' => 'required|in:admin,manager,supervisor,artisan,staff,user', // Match your form options
            'admin_level' => 'nullable|integer|min:0|max:5',
            'employee_id' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
        ]);

        // Prevent non-superusers from creating superusers
        if (!Auth::user()->isSuperAdmin() && ($request->admin_level ?? 0) >= 5) {
            return back()->withErrors(['admin_level' => 'You cannot create users with Super Admin privileges.']);
        }

        // Prevent creating users with higher admin level than current user
        if (($request->admin_level ?? 0) > Auth::user()->admin_level && !Auth::user()->is_superuser) {
            return back()->withErrors(['admin_level' => 'You cannot create users with higher admin level than yourself.']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password ?? 'password'), // Default password if not provided
            'role' => $request->role,
            'admin_level' => $request->admin_level ?? 0,
            'is_superuser' => ($request->admin_level ?? 0) >= 5,
            'employee_id' => $request->employee_id,
            'department' => $request->department,
            'position' => $request->position,
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', "User '{$user->name}' created successfully with {$user->role_display} access.");
    }

    public function update(Request $request, User $user)
    {
        // Prevent editing superuser unless you are superuser
        if ($user->is_superuser && !Auth::user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'You cannot edit Super Administrator accounts.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'role' => 'required|in:admin,manager,supervisor,artisan,staff,user',
            'admin_level' => 'nullable|integer|min:0|max:5',
            'employee_id' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'admin_level' => $request->admin_level ?? 0,
            'is_superuser' => ($request->admin_level ?? 0) >= 5,
            'employee_id' => $request->employee_id,
            'department' => $request->department,
            'position' => $request->position,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "User '{$user->name}' updated successfully.");
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        
        return response()->json([
            'success' => true,
            'message' => "User status updated successfully.",
            'is_active' => $user->is_active
        ]);
    }

    public function destroy(User $user)
    {
        // Prevent deleting superusers unless you are one
        if ($user->is_superuser && !Auth::user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete superuser accounts.']);
        }
        
        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'You cannot delete your own account.']);
        }
        
        $user->delete();
        
        return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
    }
}
