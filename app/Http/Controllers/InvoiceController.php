<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobcard;
use App\Models\CompanyDetail;
use Mail;

class InvoiceController extends Controller
{
    public function index()
    {
        $jobcards = \App\Models\Jobcard::with('client')
            ->where('status', 'completed')
            ->orderByDesc('job_date')
            ->get();

        return view('invoice', compact('jobcards'));
    }

    public function show($jobcardId)
    {
        $jobcard = \App\Models\Jobcard::with(['client', 'inventory'])->findOrFail($jobcardId);
        $company = \App\Models\CompanyDetail::first();
        return view('invoice_view', compact('jobcard', 'company'));
    }

    public function email($jobcardId)
    {
        $jobcard = Jobcard::with(['client', 'inventory'])->findOrFail($jobcardId);
        $company = CompanyDetail::first();

        // Send email logic here (use Laravel Mailable)
        Mail::to($jobcard->client->email)->send(new \App\Mail\InvoiceMailable($jobcard, $company));

        return back()->with('success', 'Invoice emailed to client!');
    }
}