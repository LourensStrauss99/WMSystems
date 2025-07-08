<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter'); // Add this
        $perPage = $request->input('perPage', 10);

        if (!in_array($perPage, [10, 25, 50])) {
            $perPage = 10; 
        }

        $customers = Client::query()
            ->when($search, function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($filter === 'active', function($q) {
                $q->active();
            })
            ->when($filter === 'inactive', function($q) {
                $q->inactive();
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends(['search' => $search, 'perPage' => $perPage, 'filter' => $filter]);

        return view('customers', compact('customers', 'search', 'perPage', 'filter'));
    }
    
    public function show($id)
    {
        $customer = Client::findOrFail($id);
        
        // Initialize all collections as empty to prevent null errors
        $workHistory = collect();
        $invoiceHistory = collect();
        $paymentHistory = collect();
        
        // Get work history - with null safety
        try {
            if (class_exists(Jobcard::class)) {
                $workHistory = Jobcard::where('client_id', $id)
                              ->orderBy('created_at', 'desc')
                              ->take(10)
                              ->get() ?? collect();
            }
        } catch (\Exception $e) {
            Log::warning("Could not load work history: " . $e->getMessage());
            $workHistory = collect();
        }

        // Get invoice history with payment tracking - with null safety
        try {
            if (class_exists(Invoice::class)) {
                $invoiceHistory = Invoice::where('client_id', $id)
                                ->orderBy('invoice_date', 'desc')
                                ->get();
                
                if (!$invoiceHistory) {
                    $invoiceHistory = collect();
                } else {
                    $invoiceHistory = $invoiceHistory?->map(function($invoice) {
                        // Only update payment status if method exists
                        if (method_exists($invoice, 'updatePaymentStatus')) {
                            try {
                                $invoice->updatePaymentStatus();
                            } catch (\Exception $e) {
                                Log::warning("Could not update payment status for invoice {$invoice->id}: " . $e->getMessage());
                            }
                        }
                        return $invoice;
                    });
                }
            }
        } catch (\Exception $e) {
            Log::warning("Could not load invoice history: " . $e->getMessage());
            $invoiceHistory = collect();
        }

        // Get payment history - with null safety
        try {
            if (class_exists(Payment::class)) {
                $paymentHistory = Payment::where('client_id', $id)
                                ->orderBy('payment_date', 'desc')
                                ->get() ?? collect();
            }
        } catch (\Exception $e) {
            Log::warning("Could not load payment history: " . $e->getMessage());
            $paymentHistory = collect();
        }

        // Calculate enhanced payment summary with null safety
        $paymentSummary = [
            'total_payments' => $paymentHistory ? $paymentHistory->sum('amount') : 0,
            'cash_payments' => $paymentHistory ? $paymentHistory->where('payment_method', 'cash')->sum('amount') : 0,
            'card_payments' => $paymentHistory ? $paymentHistory->where('payment_method', 'card')->sum('amount') : 0,
            'eft_payments' => $paymentHistory ? $paymentHistory->where('payment_method', 'eft')->sum('amount') : 0,
            'recent_payment' => $paymentHistory && $paymentHistory->count() > 0 ? $paymentHistory->first() : null,
            'this_month_payments' => $paymentHistory ? $paymentHistory->where('payment_date', '>=', now()->startOfMonth())->sum('amount') : 0
        ];
        
        // Calculate aging summary with null safety
        $agingSummary = [
            'current' => 0,
            '30_days' => 0,
            '60_days' => 0,
            '90_days' => 0,
            '120_days' => 0,
        ];
        
        // Only calculate aging if we have invoices
        if ($invoiceHistory && $invoiceHistory->count() > 0) {
            $agingSummary = [
                'current' => $invoiceHistory->sum(function($invoice) {
                    try {
                        return method_exists($invoice, 'getAgeCategory') && $invoice->getAgeCategory() === 'current' 
                            ? ($invoice->outstanding_amount ?? 0) : 0;
                    } catch (\Exception $e) {
                        return 0;
                    }
                }),
                '30_days' => $invoiceHistory->sum(function($invoice) {
                    try {
                        return method_exists($invoice, 'getAgeCategory') && $invoice->getAgeCategory() === '30_days' 
                            ? ($invoice->outstanding_amount ?? 0) : 0;
                    } catch (\Exception $e) {
                        return 0;
                    }
                }),
                '60_days' => $invoiceHistory->sum(function($invoice) {
                    try {
                        return method_exists($invoice, 'getAgeCategory') && $invoice->getAgeCategory() === '60_days' 
                            ? ($invoice->outstanding_amount ?? 0) : 0;
                    } catch (\Exception $e) {
                        return 0;
                    }
                }),
                '90_days' => $invoiceHistory->sum(function($invoice) {
                    try {
                        return method_exists($invoice, 'getAgeCategory') && $invoice->getAgeCategory() === '90_days' 
                            ? ($invoice->outstanding_amount ?? 0) : 0;
                    } catch (\Exception $e) {
                        return 0;
                    }
                }),
                '120_days' => $invoiceHistory->sum(function($invoice) {
                    try {
                        return method_exists($invoice, 'getAgeCategory') && $invoice->getAgeCategory() === '120_days' 
                            ? ($invoice->outstanding_amount ?? 0) : 0;
                    } catch (\Exception $e) {
                        return 0;
                    }
                }),
            ];
        }
        
        return view('customer-show', compact('customer', 'workHistory', 'invoiceHistory', 'paymentHistory', 'paymentSummary', 'agingSummary'));
    }
    
    public function create()
    {
        return view('customer-create');
    }
    
    public function store(Request $request)
    {
        // Validate and save the customer
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);
        Client::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer added!');
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit($id)
    {
        $customer = Client::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, $id)
    {
        $customer = Client::findOrFail($id);
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'payment_reference' => 'nullable|string|max:8'
        ]);

        $customer->update($validatedData);

        return redirect()->route('client.show', $id)
                        ->with('success', 'Customer updated successfully!');
    }

    /**
     * Update customer notes via AJAX
     */
    public function updateNotes(Request $request, $id)
    {
        $customer = Client::findOrFail($id);
        
        $request->validate([
            'notes' => 'nullable|string'
        ]);

        $customer->update(['notes' => $request->notes]);

        return response()->json(['success' => true]);
    }

    /**
     * Regenerate payment reference for customer
     */
    public function regenerateReference($id)
    {
        $customer = Client::findOrFail($id);
        $newReference = $customer->regeneratePaymentReference();
        
        return response()->json([
            'success' => true,
            'reference' => $newReference
        ]);
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Client $customer)
    {
        try {
            // Check for related records
            $jobcardCount = DB::table('jobcards')->where('client_id', $customer->id)->count();
            
            if ($jobcardCount > 0) {
                return redirect()->route('customers.index')
                               ->with('error', "Cannot delete customer '{$customer->name}' because they have {$jobcardCount} associated jobcard(s).");
            }
            
            // Delete the customer
            $customerName = $customer->name . ' ' . ($customer->surname ?? '');
            $customer->delete();
            
            return redirect()->route('customers.index')
                           ->with('success', "Customer '{$customerName}' has been deleted successfully.");
            
        } catch (\Exception $e) {
            return redirect()->route('customers.index')
                           ->with('error', 'Error deleting customer: ' . $e->getMessage());
        }
    }
    
    /**
     * Toggle customer active status
     */
    public function toggleStatus(Request $request, Client $customer)
    {
        $newStatus = !$customer->is_active;
        $reason = $request->input('reason', 'Manual toggle');
        
        $customer->update([
            'is_active' => $newStatus,
            'inactive_reason' => $newStatus ? null : $reason,
            'last_activity' => $newStatus ? now() : $customer->last_activity
        ]);
        
        $status = $newStatus ? 'activated' : 'deactivated';
        $customerName = $customer->name . ' ' . ($customer->surname ?? '');
        
        return redirect()->route('customers.index')
                       ->with('success', "Customer '{$customerName}' has been {$status}.");
    }
}




