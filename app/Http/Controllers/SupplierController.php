<?php
// Create: php artisan make:controller SupplierController


namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of suppliers
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
            }
        }

        // Filter by payment terms
        if ($request->filled('payment_terms')) {
            $query->byPaymentTerms($request->payment_terms);
        }

        $suppliers = $query->orderBy('name')->paginate(15);

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new supplier
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created supplier
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            Supplier::validationRules(),
            Supplier::validationMessages()
        );

        try {
            $supplier = Supplier::create($validated);
            
            return redirect()->route('suppliers.show', $supplier)
                ->with('success', 'Supplier created successfully!');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create supplier: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified supplier
     */
    public function show(Supplier $supplier)
    {
        $supplier->load(['purchaseOrders' => function($query) {
            $query->orderBy('order_date', 'desc')->limit(10);
        }]);
        
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified supplier
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate(
            Supplier::validationRules($supplier->id),
            Supplier::validationMessages()
        );

        try {
            $supplier->update($validated);
            
            return redirect()->route('suppliers.show', $supplier)
                ->with('success', 'Supplier updated successfully!');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update supplier: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified supplier
     */
    public function destroy(Supplier $supplier)
    {
        try {
            if (!$supplier->canBeDeleted()) {
                return back()->with('error', 'Cannot delete supplier with existing purchase orders or inventory items.');
            }
            
            $supplierName = $supplier->name;
            $supplier->delete();
            
            return redirect()->route('suppliers.index')
                ->with('success', "Supplier '{$supplierName}' deleted successfully!");
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete supplier: ' . $e->getMessage());
        }
    }

    /**
     * Toggle supplier active status
     */
    public function toggleStatus(Supplier $supplier)
    {
        $supplier->update(['active' => !$supplier->active]);
        
        $status = $supplier->active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Supplier {$supplier->name} has been {$status}.");
    }

    /**
     * Get suppliers for API (used in dropdowns)
     */
    public function getActive()
    {
        $suppliers = Supplier::active()
            ->select(['id', 'name', 'contact_person', 'email', 'phone', 'payment_terms'])
            ->orderBy('name')
            ->get();

        return response()->json($suppliers);
    }
}
