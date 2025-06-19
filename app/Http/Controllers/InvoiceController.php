<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobcard;
use App\Models\CompanyDetail;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMailable;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Jobcard::with('client')->where('status', 'invoiced');

        if ($request->filled('client')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->client . '%');
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('updated_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('updated_at', '<=', $request->to);
        }

        $jobcards = $query->paginate(10);

        return view('invoice', compact('jobcards'));
    }

    public function show($jobcardId)
    {
        $jobcard = Jobcard::with(['client', 'inventory'])->findOrFail($jobcardId);
        $company = CompanyDetail::first();
        return view('invoice_view', compact('jobcard', 'company'));
    }

    public function email($jobcardId)
    {
        $jobcard = Jobcard::with(['client', 'inventory'])->findOrFail($jobcardId);
        $company = CompanyDetail::first();

        // Send email using a Mailable (see step 4)
        Mail::to($jobcard->client->email)->send(new InvoiceMailable($jobcard, $company));

        return back()->with('success', 'Invoice emailed successfully!');
    }
}