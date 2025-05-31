<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\CompanyDetail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotesController extends Controller
{
    // Show the main quotes page (form + search)
    public function index(Request $request)
    {
        $company = CompanyDetail::first();
        $quotes = collect();
        $quote = null;
        $nextQuoteNumber = Quote::max('quote_number') + 1 ?? 1;

        // Search by client name
        if ($request->filled('client')) {
            $quotes = Quote::where('client_name', 'like', '%' . $request->client . '%')->get();
        }

        return view('quotes', compact('company', 'quotes', 'quote', 'nextQuoteNumber'));
    }

    // Save a new or edited quote
    public function save(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_address' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_telephone' => 'nullable|string|max:255',
            'quote_number' => 'required|string|max:255',
            'quote_date' => 'required|date',
            'items' => 'required|array',
            'items.*.description' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $quote = Quote::updateOrCreate(
            ['quote_number' => $validated['quote_number']],
            [
                'client_name' => $validated['client_name'],
                'client_address' => $validated['client_address'],
                'client_email' => $validated['client_email'],
                'client_telephone' => $validated['client_telephone'],
                'quote_date' => $validated['quote_date'],
                'items' => $validated['items'],
                'notes' => $validated['notes'],
            ]
        );

        return redirect()->route('quotes.show', $quote->id)
            ->with('success', 'Quote saved successfully!');
    }

    // Show a single quote (PDF-like view)
    public function show($id)
    {
        $quote = Quote::findOrFail($id);
        $company = CompanyDetail::first();
        return view('quotes_view', compact('quote', 'company'));
    }

    // Download quote as PDF
    public function download($id)
    {
        $quote = Quote::findOrFail($id);
        $company = CompanyDetail::first();
        $pdf = Pdf::loadView('quotes.pdf', compact('quote', 'company'));
        return $pdf->download('quote_'.$quote->quote_number.'.pdf');
    }

    // (Optional) Email quote as PDF
    public function email($id)
    {
        $quote = Quote::findOrFail($id);
        $company = CompanyDetail::first();
        $pdf = Pdf::loadView('quotes_pdf', compact('quote', 'company'))->output();

        \Mail::to($quote->client_email)->send(new QuoteMailable($quote, $company, $pdf->output()));

        return back()->with('success', 'Quote emailed successfully!');
    }
}