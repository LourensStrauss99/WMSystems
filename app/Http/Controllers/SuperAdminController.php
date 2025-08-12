<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isSuperAdmin()) {
                abort(403, 'Access denied. Super Admin privileges required.');
            }
            return $next($request);
        });
    }

    /**
     * Show the super admin dashboard
     */
    public function dashboard()
    {
        $this->ensureMainDatabase();
        
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'trial_tenants' => Tenant::where('subscription_plan', 'trial')->count(),
            'expired_trials' => Tenant::where('subscription_plan', 'trial')
                                     ->where('subscription_expires_at', '<', now())
                                     ->count(),
        ];

        $recent_tenants = Tenant::orderBy('created_at', 'desc')
                                ->limit(10)
                                ->get();

        return view('super-admin.dashboard', compact('stats', 'recent_tenants'));
    }

    /**
     * Show all tenants
     */
    public function tenants()
    {
        $this->ensureMainDatabase();
        
        $tenants = Tenant::orderBy('created_at', 'desc')
                         ->paginate(20);

        return view('super-admin.tenants.index', compact('tenants'));
    }

    /**
     * Show tenant details
     */
    public function showTenant(Tenant $tenant)
    {
        $this->ensureMainDatabase();
        
        // Get tenant statistics by switching to tenant database
        $tenantStats = $this->getTenantStatistics($tenant);
        
        return view('super-admin.tenants.show', compact('tenant', 'tenantStats'));
    }

    /**
     * Suspend a tenant
     */
    public function suspendTenant(Tenant $tenant)
    {
        $this->ensureMainDatabase();
        
        $tenant->update(['status' => 'suspended']);
        
        return back()->with('success', "Tenant '{$tenant->name}' has been suspended.");
    }

    /**
     * Activate a tenant
     */
    public function activateTenant(Tenant $tenant)
    {
        $this->ensureMainDatabase();
        
        $tenant->update(['status' => 'active']);
        
        return back()->with('success', "Tenant '{$tenant->name}' has been activated.");
    }

    /**
     * Delete a tenant and its database
     */
    public function deleteTenant(Tenant $tenant)
    {
        $this->ensureMainDatabase();
        
        DB::beginTransaction();
        
        try {
            // Drop the tenant database
            DB::statement("DROP DATABASE IF EXISTS `{$tenant->database_name}`");
            
            // Delete the tenant record
            $tenant->delete();
            
            DB::commit();
            
            return back()->with('success', "Tenant '{$tenant->name}' and its database have been permanently deleted.");
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withErrors(['error' => 'Failed to delete tenant: ' . $e->getMessage()]);
        }
    }

    /**
     * Get tenant statistics
     */
    private function getTenantStatistics(Tenant $tenant)
    {
        try {
            // Switch to tenant database
            Config::set('database.connections.mysql.database', $tenant->database_name);
            DB::reconnect('mysql');
            
            $stats = [
                'total_users' => DB::table('users')->count(),
                'total_customers' => DB::table('customers')->count(),
                'total_jobcards' => DB::table('jobcards')->count(),
                'pending_jobcards' => DB::table('jobcards')->where('status', 'pending')->count(),
                'total_inventory' => DB::table('inventory')->count(),
            ];
            
            // Switch back to main database
            $this->ensureMainDatabase();
            
            return $stats;
        } catch (\Exception $e) {
            $this->ensureMainDatabase();
            
            return [
                'error' => 'Could not retrieve tenant statistics: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Ensure we're using the main database
     */
    private function ensureMainDatabase()
    {
        Config::set('database.connections.mysql.database', env('DB_DATABASE'));
        DB::reconnect('mysql');
    }

    /**
     * Login as tenant user (for debugging)
     */
    public function loginAsTenant(Tenant $tenant)
    {
        $this->ensureMainDatabase();
        
        try {
            // Switch to tenant database
            Config::set('database.connections.mysql.database', $tenant->database_name);
            DB::reconnect('mysql');
            
            // Find the tenant owner
            $tenantUser = User::where('email', $tenant->owner_email)->first();
            
            if (!$tenantUser) {
                $this->ensureMainDatabase();
                return back()->withErrors(['error' => 'Tenant owner not found in tenant database.']);
            }
            
            // Log in as the tenant user
            Auth::login($tenantUser);
            
            return redirect()->route('dashboard')->with('success', "Logged in as {$tenant->name} owner: {$tenantUser->name}");
            
        } catch (\Exception $e) {
            $this->ensureMainDatabase();
            return back()->withErrors(['error' => 'Failed to login as tenant: ' . $e->getMessage()]);
        }
    }
}
