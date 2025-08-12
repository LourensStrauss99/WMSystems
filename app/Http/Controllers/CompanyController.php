<?php
// filepath: app/Http/Controllers/CompanyController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Traits\TenantDatabaseSwitch;

class CompanyController extends Controller
{
    use TenantDatabaseSwitch;
    /**
     * Switch to the correct database for the current user
     */
    private function switchToCorrectDatabase()
    {
        $user = Auth::user();
        
        // Check if we have tenant database info in session
        $tenantDatabase = session('tenant_database');
        if ($tenantDatabase) {
            Config::set('database.connections.mysql.database', $tenantDatabase);
            DB::reconnect('mysql');
            return;
        }

        // Find tenant by user email
        if ($user && $user->email) {
            // Ensure we start on main database
            Config::set('database.connections.mysql.database', env('DB_DATABASE'));
            DB::reconnect('mysql');
            
            $tenant = Tenant::where('owner_email', $user->email)
                          ->where('status', 'active')
                          ->first();
            
            if ($tenant) {
                Config::set('database.connections.mysql.database', $tenant->database_name);
                DB::reconnect('mysql');
                session(['tenant_database' => $tenant->database_name]);
            }
        }
    }

    /**
     * Show company details (redirects to edit form)
     */
    public function show()
    {
        return $this->edit();
    }

    /**
     * Show company details form
     */
    public function edit()
    {
        // Switch to correct database first
        $this->switchToTenantDatabase();
        
        // Check if user has access to company settings
        if (!Auth::user()->admin_level || Auth::user()->admin_level < 3) {
            abort(403, 'Access denied. Administrator privileges required to access company settings.');
        }

        // Get existing company details or create default
        $company = CompanyDetail::first();
        
        if (!$company) {
            $company = CompanyDetail::createDefault();
        }

        return view('company-details', compact('company'));
    }

    /**
     * Update company details
     */
    public function update(Request $request)
    {
        // Switch to correct database first
        $this->switchToTenantDatabase();
        
        // Check permissions
        if (!Auth::user()->admin_level || Auth::user()->admin_level < 3) {
            abort(403, 'Access denied. Administrator privileges required.');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_telephone' => 'required|string|max:20',
            'physical_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'labour_rate' => 'required|numeric|min:0',
            'vat_percent' => 'required|numeric|min:0|max:100',
            'markup_percentage' => 'required|numeric|min:0',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $company = CompanyDetail::first();
        if (!$company) {
            $company = new CompanyDetail();
        }

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            if ($company->company_logo && Storage::exists('public/' . $company->company_logo)) {
                Storage::delete('public/' . $company->company_logo);
            }
            
            $logoPath = $request->file('company_logo')->store('company', 'public');
            $company->company_logo = $logoPath;
        }

        // Update all fields
        $company->fill($request->except(['company_logo', '_token', '_method']));
        $company->save();

        return back()->with('success', 'Company details updated successfully!');
    }

    /**
     * Remove company logo
     */
    public function removeLogo()
    {
        if (!Auth::user()->admin_level || Auth::user()->admin_level < 3) {
            abort(403, 'Access denied. Administrator privileges required to access company settings.');
        }

        $company = CompanyDetail::first();
        if ($company && $company->company_logo) {
            // Delete file
            if (Storage::exists('public/' . $company->company_logo)) {
                Storage::delete('public/' . $company->company_logo);
            }
            
            // Clear from database
            $company->company_logo = null;
            $company->save();
        }

        return back()->with('success', 'Company logo removed successfully!');
    }

    /**
     * Check if company setup is complete
     */
    public function checkSetup()
    {
        $company = CompanyDetail::first();
        
        if (!$company) {
            return response()->json(['complete' => false, 'message' => 'Company details not set up']);
        }

        return response()->json([
            'complete' => $company->isSetupComplete(),
            'company' => $company->getDocumentHeader()
        ]);
    }
}