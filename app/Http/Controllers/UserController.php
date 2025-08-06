<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Traits\TenantDatabaseSwitch;

class UserController extends Controller
{
    use TenantDatabaseSwitch;
    
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
        $this->switchToTenantDatabase();
        
        $users = \App\Models\User::paginate(15);
        $employees = \App\Models\Employee::paginate(15);
        return view('admin.users.index', compact('users', 'employees'));
    }

    public function store(Request $request)
    {
        $this->switchToTenantDatabase();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,manager,supervisor,artisan,staff,user',
            'admin_level' => 'nullable|integer|min:0|max:5',
            'employee_id' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:15',
        ]);

        // Use provided password or default to 'password'
        $password = $request->password ?: 'password';

        // Prevent non-superusers from creating superusers
        if (!Auth::user()->isSuperAdmin() && ($request->admin_level ?? 0) >= 5) {
            return back()->withErrors(['admin_level' => 'You cannot create users with Super Admin privileges.']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => $request->role,
            'admin_level' => $request->admin_level ?? 0,
            'is_superuser' => ($request->admin_level ?? 0) >= 5,
            'employee_id' => $request->employee_id,
            'department' => $request->department,
            'position' => $request->position,
            'telephone' => $request->telephone,
            'is_active' => true,
            'created_by' => auth()->id(),
            // For testing - auto-verify high-level users or set bypass
            'bypass_verification' => ($request->admin_level ?? 0) >= 3 || app()->environment('local'),
            'email_verified_at' => ($request->admin_level ?? 0) >= 3 ? now() : null,
        ]);

        // Send verification email only if not bypassed
        if (!$user->canBypassVerification()) {
            $user->sendEmailVerificationNotification();
        }

        return back()->with('success', "User '{$user->name}' created successfully. " . 
            ($user->needsEmailVerification() ? 'Verification email sent.' : 'User verified automatically.'));
    }

    public function edit(User $user)
    {
        $this->switchToTenantDatabase();
        
        // Check if user can edit this user
        if (!Auth::user() || !Auth::user()->canManageUsers()) {
            abort(403, 'You do not have permission to edit users.');
        }

        // Prevent editing higher-level users unless you're a superuser
        if ($user->admin_level >= auth()->user()->admin_level && !auth()->user()->isSuperAdmin() && $user->id !== auth()->id()) {
            abort(403, 'You cannot edit users with equal or higher admin level.');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->switchToTenantDatabase();
        
        // Check permissions
        if (!Auth::user()->canManageUsers()) {
            abort(403, 'You do not have permission to edit users.');
        }

        // Prevent editing superuser unless you are superuser
        if ($user->is_superuser && !Auth::user()->isSuperAdmin()) {
            return back()->withErrors(['error' => 'You cannot edit Super Administrator accounts.']);
        }

        // Prevent editing higher-level users
        if ($user->admin_level >= auth()->user()->admin_level && !auth()->user()->isSuperAdmin() && $user->id !== auth()->id()) {
            return back()->withErrors(['error' => 'You cannot edit users with equal or higher admin level.']);
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

        // Prevent escalating admin level beyond current user's level
        if (($request->admin_level ?? 0) > auth()->user()->admin_level && !auth()->user()->isSuperAdmin()) {
            return back()->withErrors(['admin_level' => 'You cannot set admin level higher than your own.']);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'admin_level' => $request->admin_level ?? 0,
            'is_superuser' => ($request->admin_level ?? 0) >= 5,
            'employee_id' => $request->employee_id,
            'department' => $request->department,
            'position' => $request->position,
            'is_active' => $request->boolean('is_active', $user->id === auth()->id() ? true : false), // Prevent self-deactivation
        ]);

        return back()->with('success', "User '{$user->name}' updated successfully.");
    }

    public function changePassword(Request $request, User $user)
    {
        $this->switchToTenantDatabase();
        
        // Check permissions
        if (!auth()->user()->canManageUsers() && $user->id !== auth()->id()) {
            abort(403, 'You can only change your own password or you need user management permissions.');
        }

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus(User $user)
    {
        $this->switchToTenantDatabase();
        
        // Check permissions
        if (!auth()->user()->canManageUsers()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Prevent self-deactivation
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You cannot deactivate your own account'], 400);
        }

        // Prevent deactivating superusers unless you are one
        if ($user->is_superuser && !auth()->user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'You cannot modify super administrator accounts'], 403);
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success' => true, 
            'message' => "User {$user->name} " . ($user->is_active ? 'activated' : 'deactivated') . " successfully",
            'is_active' => $user->is_active
        ]);
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        $this->switchToTenantDatabase();
        
        // Check permissions
        if (!auth()->user()->canManageUsers()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You cannot delete your own account'], 400);
        }

        // Prevent deleting superusers unless you are one
        if ($user->is_superuser && !auth()->user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'You cannot delete super administrator accounts'], 403);
        }

        // Prevent deleting higher-level users
        if ($user->admin_level >= auth()->user()->admin_level && !auth()->user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'You cannot delete users with equal or higher admin level'], 403);
        }

        $userName = $user->name;
        $user->delete();

        return response()->json([
            'success' => true, 
            'message' => "User {$userName} deleted successfully"
        ]);
    }

    /**
     * Show user data for editing (AJAX endpoint)
     */
    public function show(User $user)
    {
        $this->switchToTenantDatabase();
        
        // Check permissions
        if (!auth()->user()->canManageUsers()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json($user);
    }
}
