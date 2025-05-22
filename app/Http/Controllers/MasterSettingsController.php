<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class MasterSettingsController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'labour_rate' => 'required|numeric',
            'vat_percent' => 'required|numeric',
            'company_name' => 'required|string',
            'company_reg_number' => 'nullable|string',
            'vat_reg_number' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_holder' => 'nullable|string',
            'account_number' => 'nullable|string',
            'branch_code' => 'nullable|string',
            'swift_code' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country' => 'nullable|string',
            'company_telephone' => 'nullable|string',
            'company_email' => 'nullable|email',
            'company_website' => 'nullable|string',
            'invoice_terms' => 'nullable|string',
            'invoice_footer' => 'nullable|string',
        ]);

        // For single row settings table (id=1)
        $settings = Setting::first() ?? new Setting();
        $settings->fill($data);
        $settings->save();

        return redirect()->back()->with('success', 'Settings updated!');
    }
}