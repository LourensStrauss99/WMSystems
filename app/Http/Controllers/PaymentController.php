<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Jobcard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Show payment form for a specific client
     */
    public function create($clientId)
    {
        $client = Client::findOrFail($clientId);
        
        // Get unpaid invoices and completed jobcards for this client
        $unpaidInvoices = Invoice::where('client_id', $clientId)
                                ->where('status', 'unpaid')
                                ->get();
        
        $completedJobcards = Jobcard::where('client_id', $clientId)
                                   ->where('status', 'completed')
                                   ->get();
        
        return view('payments.create', compact('client', 'unpaidInvoices', 'completedJobcards'));
    }

    /**
     * Process payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,eft,cheque,payfast,other',
            'payment_date' => 'required|date',
            'invoice_jobcard_number' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            $client = Client::findOrFail($request->client_id);
            
            // Create payment record
            $payment = Payment::create([
                'payment_reference' => $client->payment_reference,
                'client_id' => $request->client_id,
                'invoice_jobcard_number' => $request->invoice_jobcard_number,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date' => $request->payment_date,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'status' => 'completed',
                'receipt_number' => Payment::generateReceiptNumber()
            ]);

            // Update invoice status if paying for specific invoice
            if ($request->invoice_jobcard_number) {
                $invoice = Invoice::where('invoice_number', $request->invoice_jobcard_number)->first();
                if ($invoice && $invoice->status === 'unpaid') {
                    $invoice->update([
                        'status' => 'paid',
                        'payment_date' => $request->payment_date
                    ]);
                }

                // Update jobcard status if paying for jobcard
                $jobcard = Jobcard::where('jobcard_number', $request->invoice_jobcard_number)->first();
                if ($jobcard && $jobcard->status === 'completed') {
                    $jobcard->update(['status' => 'invoiced']);
                }
            }
        });

        return redirect()->route('payments.receipt', Payment::latest()->first()->id)
                        ->with('success', 'Payment processed successfully!');
    }

    /**
     * Show payment receipt
     */
    public function receipt($paymentId)
    {
        $payment = Payment::with('client')->findOrFail($paymentId);
        return view('payments.receipt', compact('payment'));
    }

    /**
     * Get invoice/jobcard details for payment form
     */
    public function getInvoiceDetails(Request $request)
    {
        $number = $request->invoice_jobcard_number;
        $clientId = $request->client_id;
        
        // Check if it's an invoice
        $invoice = Invoice::where('invoice_number', $number)
                         ->where('client_id', $clientId)
                         ->first();
        
        if ($invoice) {
            return response()->json([
                'found' => true,
                'type' => 'invoice',
                'amount' => $invoice->amount,
                'status' => $invoice->status,
                'date' => $invoice->invoice_date
            ]);
        }
        
        // Check if it's a jobcard
        $jobcard = Jobcard::where('jobcard_number', $number)
                          ->where('client_id', $clientId)
                          ->first();
        
        if ($jobcard) {
            return response()->json([
                'found' => true,
                'type' => 'jobcard',
                'amount' => $jobcard->amount ?? 0,
                'status' => $jobcard->status,
                'date' => $jobcard->job_date
            ]);
        }
        
        return response()->json(['found' => false]);
    }
}
