<?php
// filepath: app/Http/Controllers/CompanyController.php

namespace App\Http\Controllers;

use App\Models\CompanyDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for editing company details
     */
    public function edit()
    {
        $company = CompanyDetail::first();
        
        // If no company details exist, create default ones
        if (!$company) {
            $company = CompanyDetail::create([
                'company_name' => 'Your Company Name',
                'trading_name' => 'Your Trading Name',
                'registration_number' => '',
                'vat_number' => '',
                'address_line_1' => '',
                'address_line_2' => '',
                'city' => '',
                'state_province' => '',
                'postal_code' => '',
                'country' => 'South Africa',
                'phone' => '',
                'email' => '',
                'website' => '',
                'bank_name' => '',
                'bank_account_number' => '',
                'bank_branch_code' => '',
                'bank_account_type' => '',
            ]);
        }

        return view('company.edit', compact('company'));
    }

    /**
     * Update company details
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'trading_name' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'vat_number' => 'nullable|string|max:100',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state_province' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_branch_code' => 'nullable|string|max:20',
            'bank_account_type' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $company = CompanyDetail::first();
        
        if (!$company) {
            $company = new CompanyDetail();
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo && Storage::exists('public/' . $company->logo)) {
                Storage::delete('public/' . $company->logo);
            }

            // Store new logo
            $logoPath = $request->file('logo')->store('company', 'public');
            $validated['logo'] = $logoPath;
        }

        $company->fill($validated);
        $company->save();

        return redirect()->route('company.details')
            ->with('success', 'Company details updated successfully!');
    }

    /**
     * Show company details (view only)
     */
    public function show()
    {
        $company = CompanyDetail::first();
        
        return view('company.show', compact('company'));
    }

    /**
     * Get company details for API
     */
    public function getDetails()
    {
        $company = CompanyDetail::first();
        
        return response()->json($company);
    }

    /**
     * Remove company logo
     */
    public function removeLogo()
    {
        $company = CompanyDetail::first();
        
        if ($company && $company->logo) {
            // Delete logo file
            if (Storage::exists('public/' . $company->logo)) {
                Storage::delete('public/' . $company->logo);
            }
            
            // Remove logo from database
            $company->update(['logo' => null]);
            
            return back()->with('success', 'Company logo removed successfully!');
        }
        
        return back()->with('error', 'No logo to remove.');
    }

    /**
     * Validate company setup
     */
    public function validateSetup()
    {
        $company = CompanyDetail::first();
        
        if (!$company) {
            return response()->json([
                'valid' => false,
                'message' => 'Company details not configured'
            ]);
        }

        $required_fields = [
            'company_name',
            'address_line_1',
            'city',
            'postal_code',
            'country'
        ];

        $missing_fields = [];
        foreach ($required_fields as $field) {
            if (empty($company->$field)) {
                $missing_fields[] = str_replace('_', ' ', ucfirst($field));
            }
        }

        if (!empty($missing_fields)) {
            return response()->json([
                'valid' => false,
                'message' => 'Missing required fields: ' . implode(', ', $missing_fields)
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Company setup is complete'
        ]);
    }
}