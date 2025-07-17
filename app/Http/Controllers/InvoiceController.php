<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jobcard;
use App\Models\CompanyDetail; // <- CHANGE THIS (not Company)
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMailable;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoice; // <- ADD THIS
use Illuminate\Support\Facades\DB; // <- ADD THIS
use App\Mail\InvoiceReminderMailable;

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
        $company = CompanyDetail::first(); // <- FIXED
        return view('invoice_view', compact('jobcard', 'company'));
    }

    public function email($jobcardId)
    {
        $jobcard = Jobcard::with(['client', 'inventory'])->findOrFail($jobcardId);
        $company = CompanyDetail::first(); // <- FIXED

        // Send email using a Mailable (see step 4)
        Mail::to($jobcard->client->email)->send(new InvoiceMailable($jobcard, $company));

        return back()->with('success', 'Invoice emailed successfully!');
    }

    public function generatePDF($id)
    {
        try {
            $jobcard = Jobcard::with(['client', 'inventory', 'employees'])->findOrFail($id);
            $company = CompanyDetail::first(); // <- FIXED

            // Calculate totals
            $inventoryTotal = $jobcard->inventory->sum(function($item) {
                $quantity = $item->pivot->quantity ?? 0;
                $sellingPrice = $item->selling_price ?? $item->sell_price ?? 0;
                return $quantity * $sellingPrice;
            });

            $labourHours = $jobcard->employees->sum(function($employee) {
                return $employee->pivot->hours_worked ?? 0;
            });

            $labourTotal = $labourHours * ($company->labour_rate ?? 0); // <- ADD NULL CHECK
            $subtotal = $inventoryTotal + $labourTotal;
            $vat = $subtotal * (($company->vat_percent ?? 15) / 100); // <- ADD NULL CHECK
            $grandTotal = $subtotal + $vat;

            $data = [
                'jobcard' => $jobcard,
                'company' => $company,
                'inventoryTotal' => $inventoryTotal,
                'labourHours' => $labourHours,
                'labourTotal' => $labourTotal,
                'subtotal' => $subtotal,
                'vat' => $vat,
                'grandTotal' => $grandTotal
            ];

            // Generate PDF
            $pdf = PDF::loadView('invoice_pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            
            // Set options for better rendering
            $pdf->setOptions([
                'dpi' => 150,
                'defaultFont' => 'sans-serif',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true
            ]);

            $filename = 'Invoice-' . $jobcard->jobcard_number . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jobcard_id' => 'required|exists:jobcards,id',
            'due_date' => 'nullable|date',
        ]);

        try {
            DB::transaction(function() use ($validated) {
                $jobcard = Jobcard::with(['client', 'inventory', 'employees'])->findOrFail($validated['jobcard_id']);
                $company = CompanyDetail::first();

                            // Calculate totals (same logic as your PDF generation)
            $inventoryTotal = $jobcard->inventory->sum(function($item) {
                $quantity = $item->pivot->quantity ?? 0;
                $sellingPrice = $item->selling_price ?? $item->sell_price ?? 0;
                return $quantity * $sellingPrice;
            });

                $labourHours = $jobcard->employees->sum(function($employee) {
                    return $employee->pivot->hours_worked ?? 0;
                });

                $labourTotal = $labourHours * ($company->labour_rate ?? 0);
                $subtotal = $inventoryTotal + $labourTotal;
                $vat = $subtotal * (($company->vat_percent ?? 15) / 100);
                $grandTotal = $subtotal + $vat;

                // ✅ CREATE ACTUAL INVOICE RECORD
                $invoice = Invoice::create([
                    'invoice_number' => Invoice::generateInvoiceNumber(),
                    'client_id' => $jobcard->client_id,
                    'jobcard_id' => $jobcard->id,
                    'amount' => $grandTotal,
                    'invoice_date' => now(),
                    'due_date' => $validated['due_date'] ?? now()->addDays(30),
                    'status' => 'unpaid',
                    'paid_amount' => 0,
                    'outstanding_amount' => $grandTotal,
                ]);

                // Update jobcard status
                $jobcard->update(['status' => 'invoiced']);

                return redirect()->route('invoices.show', $jobcard->id)
                    ->with('success', "Invoice {$invoice->invoice_number} created successfully!");
            });

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    public function sendReminder($invoiceId)
    {
        $invoice = \App\Models\Invoice::with(['client', 'jobcard'])->findOrFail($invoiceId);
        $company = \App\Models\CompanyDetail::first();
        // Send reminder email
        Mail::to($invoice->client->email)
            ->send(new InvoiceReminderMailable($invoice, $company));
        return response()->json(['success' => true]);
    }
}