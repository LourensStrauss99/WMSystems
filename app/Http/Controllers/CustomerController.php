<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
// Removed: use App\Traits\TenantDatabaseSwitch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\StatementMailable;
use Illuminate\Support\Facades\Mail;

class CustomerController extends Controller
{
    // Removed: use TenantDatabaseSwitch
    
    public function index(Request $request)
    {
        // Switch to tenant database
    // Removed: $this->switchToTenantDatabase();
        
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
        $customer = Client::with(['jobcards', 'invoices', 'payments'])->findOrFail($id);
        
        // Get work history
        $workHistory = $customer->jobcards()->orderBy('created_at', 'desc')->get();
        
        // Get invoice history
        $invoiceHistory = $customer->invoices()->orderBy('invoice_date', 'desc')->get();
        
        // Get payment history
        $paymentHistory = $customer->payments()->orderBy('payment_date', 'desc')->get();
        
        // Calculate account summary (use dynamic properties)
        $accountSummary = [
            'total_jobs' => $customer->total_jobs,
            'total_invoiced' => $customer->total_invoiced,
            'outstanding_amount' => $customer->outstanding_amount,
            'paid_invoices_count' => $customer->paid_invoices_count,
        ];
        
        return view('customer-show', compact(
            'customer', 
            'workHistory', 
            'invoiceHistory', 
            'paymentHistory', 
            'accountSummary'
        ));
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

    public function sendStatement($customerId)
    {
        $customer = \App\Models\Client::with(['invoices.jobcard'])->findOrFail($customerId);
        $company = \App\Models\CompanyDetail::first();
        // You can add more logic to filter invoices/payments as needed
        Mail::to($customer->email)
            ->send(new StatementMailable($customer, $company));
        return response()->json(['success' => true]);
    }

    public function downloadStatement($customerId)
    {
        $customer = \App\Models\Client::with(['invoices.jobcard'])->findOrFail($customerId);
        $company = \App\Models\CompanyDetail::first();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.statement', [
            'customer' => $customer,
            'company' => $company
        ]);
        return $pdf->download('statement.pdf');
    }
}




