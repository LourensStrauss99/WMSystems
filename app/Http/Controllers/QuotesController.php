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
        return view('quotes', compact('company'));
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

    public function edit($id)
    {
        $quote = Quote::findOrFail($id);
        return view('quotes_edit', compact('quote'));
    }

    public function update(Request $request, $id)
    {
        $quote = Quote::findOrFail($id);
        $data = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_address' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_telephone' => 'nullable|string|max:255',
            'quote_date' => 'required|date',
            'items' => 'nullable|array',
            'items.*.description' => 'nullable|string',
            'items.*.qty' => 'nullable|numeric',
            'items.*.unit_price' => 'nullable|numeric',
            'items.*.total' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);
        $quote->update([
            'client_name' => $data['client_name'],
            'client_address' => $data['client_address'] ?? '',
            'client_email' => $data['client_email'] ?? '',
            'client_telephone' => $data['client_telephone'] ?? '',
            'quote_date' => $data['quote_date'],
            'items' => $data['items'] ?? [],
            'notes' => $data['notes'] ?? '',
        ]);
        return redirect()->route('quotes.show', $quote->id)->with('success', 'Quote updated successfully!');
    }

    public function mobileIndex()
    {
        $quotes = \App\Models\Quote::orderBy('created_at', 'desc')->paginate(15);
        return view('mobile.quote-list', compact('quotes'));
    }

    public function showMobile($id)
    {
        $quote = \App\Models\Quote::findOrFail($id);
        return view('mobile.quote-view', compact('quote'));
    }

    public function editMobile($id)
    {
        $quote = \App\Models\Quote::findOrFail($id);
        return view('mobile.quote-edit', compact('quote'));
    }
}