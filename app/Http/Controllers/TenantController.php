<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class TenantController extends Controller
{
    /**
     * Show the company registration form
     */
    public function showRegistration()
    {
        return view('tenant.register');
    }

    /**
     * Handle company registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255|unique:tenants,name',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255',
            'owner_password' => ['required', 'confirmed', Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Generate unique tenant ID
            $tenantId = strtolower(Str::slug($request->company_name, '_'));
            
            // Ensure uniqueness
            $counter = 1;
            $originalTenantId = $tenantId;
            while (Tenant::where('id', $tenantId)->exists()) {
                $tenantId = $originalTenantId . '_' . $counter;
                $counter++;
            }

            // Create the tenant
            $tenant = Tenant::create([
                'id' => $tenantId,
                'name' => $request->company_name,
                'slug' => Str::slug($request->company_name),
                'database_name' => 'tenant_' . $tenantId,
                'owner_name' => $request->owner_name,
                'owner_email' => $request->owner_email,
                'owner_phone' => $request->phone,
                'address' => $request->address,
                'status' => 'active',
                'subscription_plan' => 'trial',
                'subscription_expires_at' => now()->addDays(30), // 30-day trial
            ]);

            // Create domain for the tenant
            $domain = $tenantId . '.workflow-management.test';
            $tenant->domains()->create(['domain' => $domain]);

            DB::commit();

            // Redirect to the landlord portal to show success
            return redirect()->route('workflow-management.test')
                ->with('success', "Company '{$request->company_name}' registered successfully! You can now create a tenant owner account.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])
                        ->withInput($request->except('owner_password', 'owner_password_confirmation'));
        }
    }

    /**
     * Display tenant listings (admin only)
     */
    public function index()
    {
        $tenants = Tenant::with('domains')->get();
        return view('tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant
     */
    public function create()
    {
        return view('tenants.create');
    }

    /**
     * Store a newly created tenant
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tenants,name',
            'domain' => 'required|string|max:255|unique:tenants,domain',
        ]);

        $tenant = Tenant::create([
            'name' => $request->name,
            'domain' => $request->domain,
            'database' => 'tenant_' . Str::slug($request->name, '_'),
        ]);

        return redirect()->route('tenants.index')->with('success', 'Tenant created successfully!');
    }

    /**
     * Display the specified tenant
     */
    public function show(Tenant $tenant)
    {
        return view('tenants.show', compact('tenant'));
    }

    /**
     * Remove the specified tenant
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('tenants.index')->with('success', 'Tenant deleted successfully!');
    }
}
