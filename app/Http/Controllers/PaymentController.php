<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Jobcard;
use Illuminate\Http\Request;
use App\Traits\TenantDatabaseSwitch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    use TenantDatabaseSwitch;
    /**
     * Show payment form for a specific client
     */
    public function create($clientId)
    {
        $this->switchToTenantDatabase();
        
        $client = Client::findOrFail($clientId);
        
        // Get unpaid and partially paid invoices for this client
        $outstandingInvoices = collect(); // Start with empty collection for now
        $unpaidInvoices = collect(); // Add this line to fix the immediate error
        
        // Safely try to get invoices if Invoice model exists
        try {
            if (class_exists(Invoice::class)) {
                $outstandingInvoices = Invoice::where('client_id', $clientId)
                                            ->whereIn('status', ['unpaid', 'partial'])
                                            ->get()
                                            ->map(function($invoice) {
                                                // Calculate totals safely
                                                $totalPaid = 0;
                                                if (method_exists($invoice, 'getTotalPaid')) {
                                                    try {
                                                        $totalPaid = $invoice->getTotalPaid();
                                                    } catch (\Exception $e) {
                                                        $totalPaid = $invoice->paid_amount ?? 0;
                                                    }
                                                }
                                                
                                                $invoice->total_paid = $totalPaid;
                                                $invoice->balance_due = ($invoice->amount ?? 0) - $totalPaid;
                                                return $invoice;
                                            });
                
                // Also set unpaidInvoices to the same data to fix the template error
                $unpaidInvoices = $outstandingInvoices;
            }
        } catch (\Exception $e) {
            Log::warning("Could not load invoices for payment: " . $e->getMessage());
            $outstandingInvoices = collect();
            $unpaidInvoices = collect();
        }
        
        // Get completed jobcards safely
        $completedJobcards = collect();
        try {
            if (class_exists(Jobcard::class)) {
                $completedJobcards = Jobcard::where('client_id', $clientId)
                                           ->where('status', 'completed')
                                           ->get() ?? collect();
            }
        } catch (\Exception $e) {
            Log::warning("Could not load jobcards for payment: " . $e->getMessage());
            $completedJobcards = collect();
        }
        
        return view('payments.create', compact('client', 'outstandingInvoices', 'unpaidInvoices', 'completedJobcards'));
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

            // The invoice payment status will be automatically updated via the Payment model's booted method
        });

        $latestPayment = Payment::latest()->first();
        return redirect()->route('payments.receipt', $latestPayment->id)
                        ->with('success', 'Payment processed successfully!');
    }

    /**
     * Show payment receipt with remittance advice
     */
    public function receipt($paymentId)
    {
        $payment = Payment::with(['client', 'invoice'])->findOrFail($paymentId);
        
        // Get payment history for this invoice if applicable
        $relatedPayments = collect();
        if ($payment->invoice_jobcard_number) {
            $relatedPayments = Payment::where('invoice_jobcard_number', $payment->invoice_jobcard_number)
                                    ->where('id', '!=', $payment->id)
                                    ->orderBy('payment_date', 'desc')
                                    ->get();
        }
        
        return view('payments.receipt', compact('payment', 'relatedPayments'));
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
            $invoice->updatePaymentStatus(); // Ensure status is current
            
            return response()->json([
                'found' => true,
                'type' => 'invoice',
                'amount' => $invoice->amount,
                'outstanding_amount' => $invoice->getOutstandingAmount(),
                'paid_amount' => $invoice->getTotalPaid(),
                'status' => $invoice->status,
                'date' => $invoice->invoice_date,
                'due_date' => $invoice->due_date,
                'age_days' => $invoice->getPaymentAge(),
                'age_category' => $invoice->getAgeCategory()
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
                'outstanding_amount' => $jobcard->amount ?? 0,
                'paid_amount' => 0,
                'status' => $jobcard->status,
                'date' => $jobcard->job_date
            ]);
        }
        
        return response()->json(['found' => false]);
    }

    /**
     * Generate aging report for client
     */
    public function agingReport($clientId)
    {
        $client = Client::findOrFail($clientId);
        
        $invoices = Invoice::where('client_id', $clientId)
                          ->with('payments')
                          ->orderBy('invoice_date', 'desc')
                          ->get()
                          ->map(function($invoice) {
                              $invoice->updatePaymentStatus();
                              return $invoice;
                          });
        
        $aging = [
            'current' => $invoices->where('age_category', 'current')->sum('outstanding_amount'),
            '30_days' => $invoices->where('age_category', '30_days')->sum('outstanding_amount'),
            '60_days' => $invoices->where('age_category', '60_days')->sum('outstanding_amount'),
            '90_days' => $invoices->where('age_category', '90_days')->sum('outstanding_amount'),
            '120_days' => $invoices->where('age_category', '120_days')->sum('outstanding_amount'),
        ];
        
        return view('payments.aging-report', compact('client', 'invoices', 'aging'));
    }
}
