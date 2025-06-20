<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 10); // default to 10

        // Only allow 10, 25, or 50
        if (!in_array($perPage, [10, 25, 50])) {
            $perPage = 10; 
        }

        $customers = Client::query()
            ->when($search, function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends(['search' => $search, 'perPage' => $perPage]); // keep params in pagination

        return view('customers', [
            'customers' => $customers,
            'search' => $search,
            'perPage' => $perPage,
        ]);
    }
    
    public function show($id)
    {
        $customer = Client::findOrFail($id);
        
        // Get work history
        $workHistory = collect(); // Replace with actual jobcard data later
        
        // Get invoice history
        $invoiceHistory = collect(); // Replace with actual invoice data later
        
        // Get payment history
        $paymentHistory = Payment::where('client_id', $id)
                            ->orderBy('payment_date', 'desc')
                            ->get();
    
        // Calculate payment summary
        $paymentSummary = [
            'total_payments' => $paymentHistory->sum('amount'),
            'cash_payments' => $paymentHistory->where('payment_method', 'cash')->sum('amount'),
            'card_payments' => $paymentHistory->where('payment_method', 'card')->sum('amount'),
            'eft_payments' => $paymentHistory->where('payment_method', 'eft')->sum('amount'),
            'recent_payment' => $paymentHistory->first()
        ];
        
        return view('customer-show', compact('customer', 'workHistory', 'invoiceHistory', 'paymentHistory', 'paymentSummary'));
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
}




