<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\CompanyDetail;
use App\Models\Jobcard;

class MasterSettingsController extends Controller
{
    public function index()
    {
        $companyDetails = \App\Models\CompanyDetail::first();
        return view('master-settings', compact('companyDetails'));
    }

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

        // Only one row, so update or create
        CompanyDetail::updateOrCreate(['id' => 1], $data);

        return back()->with('success', 'Company details updated!');
    }

    public function showInvoice($jobcardId)
    {
        $jobcard = \App\Models\Jobcard::with(['client', 'inventory'])->findOrFail($jobcardId);
        $company = \App\Models\CompanyDetail::first();
        return view('invoice', compact('jobcard', 'company'));
    }
    public function email($jobcardId)
    {
        $jobcard = \App\Models\Jobcard::with(['client', 'inventory'])->findOrFail($jobcardId);
        $company = \App\Models\CompanyDetail::first();

        // Send email logic here (use Laravel Mailable)
        \Mail::to($jobcard->client->email)->send(new \App\Mail\InvoiceMailable($jobcard, $company));

        return back()->with('success', 'Invoice emailed to client!');
    }
}