<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $credentials = $request->only('email', 'password');

    // First try to authenticate with main database (for super admins)
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        
        // Authentication passed...
        $request->session()->regenerate();
        
        // Redirect super admins to super admin dashboard
        if ($user->admin_level >= 4) {
            return redirect()->route('super-admin.dashboard');
        }
        
        return redirect()->intended($this->redirectTo);
    }

    // If main database authentication failed, try tenant databases
    Log::info('Attempting tenant login for: ' . $credentials['email']);
    
    $tenantLoginResult = $this->attemptTenantLogin($credentials);
    Log::info('Tenant login result: ' . json_encode($tenantLoginResult ? 'success' : 'failed'));
    
    if ($tenantLoginResult && isset($tenantLoginResult['user']) && isset($tenantLoginResult['tenant_database'])) {
        Log::info('Logging in tenant user: ' . $tenantLoginResult['user']->email . ' with database: ' . $tenantLoginResult['tenant_database']);
        
        // IMPORTANT: We need to ensure we're on the correct database when logging in the user
        // because Auth::login() might re-query the user by ID
        $this->switchToTenantDatabase($tenantLoginResult['tenant_database']);
        
        // Log in the tenant user
        Auth::login($tenantLoginResult['user']);
        
        // DO NOT switch back to main database - stay on tenant database
        // so that Auth::user() queries work correctly
        
        $request->session()->regenerate();
        
        // Set the tenant database session after regeneration
        session(['tenant_database' => $tenantLoginResult['tenant_database']]);
        
        Log::info('Session set - tenant_database: ' . session('tenant_database'));
        Log::info('Logged in user after tenant login: ' . Auth::user()->email);
        
        return redirect()->intended($this->redirectTo);
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
}
public function showLoginForm()
{
    $superuserExists = \App\Models\User::where('admin_level', 4)->exists();
    return view('auth.login', compact('superuserExists'));
}

/**
 * Attempt to log in user from tenant databases
 */
private function attemptTenantLogin($credentials)
{
    $email = $credentials['email'];
    $password = $credentials['password'];
    
    try {
        // First, switch to main database to get tenant info
        $this->switchToMainDatabase();
        Log::info('Attempting tenant login - switched to main database');
        
        // Find which tenant this user belongs to
        $tenant = Tenant::where('owner_email', $email)
                       ->where('status', 'active')
                       ->first();
        
        Log::info('Tenant search for owner_email ' . $email . ': ' . ($tenant ? $tenant->name : 'not found'));
        
        if ($tenant) {
            // Switch to tenant database
            $this->switchToTenantDatabase($tenant->database_name);
            Log::info('Switched to tenant database: ' . $tenant->database_name);
            
            // Try to find and authenticate the user
            $user = User::where('email', $email)->first();
            Log::info('User found in tenant database: ' . ($user ? 'yes' : 'no'));
            
            if ($user && Hash::check($password, $user->password)) {
                Log::info('Password check passed for tenant owner');
                // Switch back to main database before returning
                $this->switchToMainDatabase();                // Return both user and tenant database
                return [
                    'user' => $user,
                    'tenant_database' => $tenant->database_name
                ];
            }
        }
        
        // If not tenant owner, check all tenant databases for the user
        $tenants = Tenant::where('status', 'active')->get();
        
        foreach ($tenants as $tenant) {
            try {
                $this->switchToTenantDatabase($tenant->database_name);
                
                $user = User::where('email', $email)->first();
                
                if ($user && Hash::check($password, $user->password)) {
                    // Switch back to main database before returning
                    $this->switchToMainDatabase();
                    
                    // Return both user and tenant database
                    return [
                        'user' => $user,
                        'tenant_database' => $tenant->database_name
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Error checking tenant {$tenant->database_name} for login: " . $e->getMessage());
                continue;
            }
        }
        
        return null;
        
    } catch (\Exception $e) {
        Log::error("Tenant login attempt failed: " . $e->getMessage());
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
}
