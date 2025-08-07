<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use App\Traits\TenantDatabaseSwitch;

class MobileAuthController extends Controller
{
    use TenantDatabaseSwitch;
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Get tenant context from URL parameter or session
        $tenantDatabase = $request->get('tenant') ?? session('tenant_database');
        
        if (!$tenantDatabase) {
            return back()->withErrors(['email' => 'Invalid login link. Please request a new mobile login link from your administrator.'])->withInput();
        }
        
        // Set the tenant session
        $request->session()->put('tenant_database', $tenantDatabase);
        
        // Switch to the correct tenant database
        $this->switchToTenantDatabase();
        
        // Find employee in the specific tenant database
        $employee = Employee::where('email', $request->email)->first();
        
        if ($employee && Hash::check($request->password, $employee->password)) {
            // Store employee ID in session
            $request->session()->put('mobile_employee_id', $employee->id);
            return redirect()->route('mobile.jobcards.index');
        }
        
        return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
    }
    
    /**
     * Show mobile login form
     */
    public function showLoginForm(Request $request)
    {
        // Store tenant context from URL if provided
        if ($request->has('tenant')) {
            $request->session()->put('tenant_database', $request->get('tenant'));
        }
        
        return view('mobile.login', [
            'email' => $request->get('email', '')
        ]);
    }
} 