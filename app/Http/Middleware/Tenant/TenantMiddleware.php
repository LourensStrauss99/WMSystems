<?php

namespace App\Http\Middleware\Tenant;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info("TenantMiddleware: STARTING - Processing request to " . $request->url());
        Log::info("TenantMiddleware: Auth check - " . (Auth::check() ? 'authenticated' : 'not authenticated'));
        if (Auth::check()) {
            Log::info("TenantMiddleware: Current user - " . Auth::user()->email);
        }
        Log::info("TenantMiddleware: Session tenant_database - " . session('tenant_database', 'not set'));
        
        // Skip tenant switching for certain routes
        if ($this->shouldSkipTenantSwitching($request)) {
            Log::info("TenantMiddleware: Skipping tenant switching for route: " . $request->route()?->getName());
            return $next($request);
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            Log::info("TenantMiddleware: User not authenticated, skipping tenant switching");
            return $next($request);
        }

        $user = Auth::user();
        Log::info("TenantMiddleware: Processing for user: " . $user->email);

        // First check if we have tenant database info in session (set during login)
        $tenantDatabase = session('tenant_database');
        if ($tenantDatabase) {
            $this->switchToTenantDatabase($tenantDatabase);
            Log::info("TenantMiddleware: Switched to tenant database from session: {$tenantDatabase} for user: {$user->email}");
            return $next($request);
        }

        Log::info("TenantMiddleware: No tenant database in session, searching for tenant by email");
        
        // For logged-in users, we need to determine which tenant database they belong to
        if ($user->email) {
            $tenant = $this->findTenantByUserEmail($user->email);
            
            if ($tenant && $tenant->isActive()) {
                $this->switchToTenantDatabase($tenant->database_name);
                // Store in session for future requests
                session(['tenant_database' => $tenant->database_name]);
                Log::info("Switched to tenant database: {$tenant->database_name} for user: {$user->email}");
            } else {
                Log::info("TenantMiddleware: No active tenant found for user: {$user->email}");
            }
        }

        return $next($request);
    }

    /**
     * Find tenant by user email
     */
    private function findTenantByUserEmail($email)
    {
        try {
            // Ensure we start on main database
            $this->switchToMainDatabase();
            
            // First check if this is the tenant owner
            $tenant = Tenant::where('owner_email', $email)->first();
            
            if ($tenant) {
                return $tenant;
            }

            // If not owner, we need to check each tenant database for this user
            // This is more complex and should be optimized in production
            $tenants = Tenant::where('status', 'active')->get();
            
            foreach ($tenants as $tenant) {
                try {
                    $this->switchToTenantDatabase($tenant->database_name);
                    
                    // Check if user exists in this tenant database
                    $userExists = DB::table('users')->where('email', $email)->exists();
                    
                    if ($userExists) {
                        $this->switchToMainDatabase();
                        return $tenant;
                    }
                } catch (\Exception $e) {
                    Log::error("Error checking tenant {$tenant->database_name} for user {$email}: " . $e->getMessage());
                    // Continue to next tenant
                }
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error("Error finding tenant for user {$email}: " . $e->getMessage());
            return null;
        } finally {
            // Always ensure we're back on main database
            $this->switchToMainDatabase();
        }
    }

    /**
     * Switch to tenant database
     */
    private function switchToTenantDatabase($databaseName)
    {
        Config::set('database.connections.mysql.database', $databaseName);
        DB::reconnect('mysql');
    }

    /**
     * Switch back to main database
     */
    private function switchToMainDatabase()
    {
        Config::set('database.connections.mysql.database', env('DB_DATABASE'));
        DB::reconnect('mysql');
    }

    /**
     * Check if tenant switching should be skipped
     */
    private function shouldSkipTenantSwitching(Request $request)
    {
        $skipRoutes = [
            'tenant.register',
            'tenant.show-registration',
            'login',
            'register',
            'password.*',
            'verification.*',
            'admin.*',
            'super-admin.*',
        ];

        $currentRoute = $request->route()?->getName();
        
        // Always skip tenant registration routes
        if (in_array($currentRoute, ['tenant.register', 'tenant.show-registration'])) {
            return true;
        }
        
        foreach ($skipRoutes as $pattern) {
            if (fnmatch($pattern, $currentRoute)) {
                return true;
            }
        }

        // Skip for tenant registration and admin routes
        if ($request->is('tenant/*') || $request->is('admin/*') || $request->is('super-admin/*')) {
            return true;
        }

        return false;
    }
}
