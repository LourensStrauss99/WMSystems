<?php
// Create: php artisan make:controller SupplierController


namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Convert active checkbox to boolean
        $validated['active'] = $request->has('active') ? true : false;
        
        // Ensure credit_limit is numeric
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;

        try {
            $supplier = Supplier::create($validated);
            
            return redirect()->route('suppliers.index')
                ->with('success', "Supplier '{$supplier->name}' created successfully!");
                
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
        // Only load purchase orders if the model exists
        if (class_exists('App\Models\PurchaseOrder')) {
            $supplier->load(['purchaseOrders' => function($query) {
                $query->orderBy('order_date', 'desc')->limit(10);
            }]);
        }
        
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
     * Remove the specified supplier from storage.
     */
    public function destroy(Supplier $supplier)
    {
        try {
            // Check if supplier is used in any inventory items
            $inventoryCount = DB::table('inventory')
                               ->where('supplier', $supplier->name)
                               ->orWhere('vendor', $supplier->name)
                               ->count();
            
            if ($inventoryCount > 0) {
                return redirect()->route('suppliers.index')
                               ->with('error', "Cannot delete supplier '{$supplier->name}' because it's linked to {$inventoryCount} inventory item(s).");
            }
            
            // Delete the supplier
            $supplierName = $supplier->name;
            $supplier->delete();
            
            return redirect()->route('suppliers.index')
                           ->with('success', "Supplier '{$supplierName}' has been deleted successfully.");
            
        } catch (\Exception $e) {
            return redirect()->route('suppliers.index')
                           ->with('error', 'An error occurred while deleting the supplier: ' . $e->getMessage());
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
