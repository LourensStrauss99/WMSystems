<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of purchase orders
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'createdBy'])
            ->when(request('search'), function ($query) {
                $query->where('po_number', 'like', '%' . request('search') . '%')
                      ->orWhereHas('supplier', function ($q) {
                          $q->where('name', 'like', '%' . request('search') . '%');
                      });
            })
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(request('date_from'), function ($query) {
                $query->whereDate('order_date', '>=', request('date_from'));
            })
            ->when(request('date_to'), function ($query) {
                $query->whereDate('order_date', '<=', request('date_to'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new purchase order
     */
    public function create()
    {
        $suppliers = Supplier::where('active', true)->orderBy('name')->get();
        
        return view('purchase-orders.create', compact('suppliers'));
    }

    /**
     * Store a newly created purchase order
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after:order_date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_code' => 'nullable|string|max:100',
            'items.*.description' => 'nullable|string|max:500',
            'items.*.quantity_ordered' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            // Get supplier information
            $supplier = Supplier::findOrFail($request->supplier_id);
            
            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity_ordered'] * $item['unit_price'];
            }
            
            $vatAmount = $subtotal * 0.15; // 15% VAT
            $grandTotal = $subtotal + $vatAmount;

            // Create Purchase Order
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $this->generatePONumber(),
                'supplier_id' => $request->supplier_id,
                'supplier_name' => $supplier->name,
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'status' => 'draft',
                'total_amount' => $subtotal,
                'vat_amount' => $vatAmount,
                'grand_total' => $grandTotal,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            // Create Purchase Order Items - Remove quantity_outstanding
            foreach ($request->items as $itemData) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_name' => $itemData['item_name'],
                    'item_code' => $itemData['item_code'] ?? null,
                    'item_description' => $itemData['description'] ?? null,
                    'item_category' => null,
                    'quantity_ordered' => (int) $itemData['quantity_ordered'],
                    'quantity_received' => 0,
                    'unit_price' => $itemData['unit_price'],
                    'line_total' => $itemData['quantity_ordered'] * $itemData['unit_price'],
                    'unit_of_measure' => 'each',
                    'status' => 'pending',
                    'inventory_id' => null,
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order created successfully!');
    }

    /**
     * Display the specified purchase order
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items', 'createdBy']);
        
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified purchase order
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only draft purchase orders can be edited.');
        }

        $suppliers = Supplier::where('active', true)->orderBy('name')->get();
        $purchaseOrder->load(['supplier', 'items']);
        
        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers'));
    }

    /**
     * Update the specified purchase order
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only draft purchase orders can be updated.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after:order_date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_code' => 'nullable|string|max:100',
            'items.*.description' => 'nullable|string|max:500',
            'items.*.quantity_ordered' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchaseOrder) {
            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity_ordered'] * $item['unit_price'];
            }
            
            $vatAmount = $subtotal * 0.15;
            $grandTotal = $subtotal + $vatAmount;

            // Update Purchase Order
            $purchaseOrder->update([
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'total_amount' => $subtotal,
                'vat_amount' => $vatAmount,
                'grand_total' => $grandTotal,
                'notes' => $request->notes,
            ]);

            // Delete existing items and create new ones
            $purchaseOrder->items()->delete();
            
            foreach ($request->items as $itemData) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_name' => $itemData['item_name'],
                    'item_code' => $itemData['item_code'] ?? null,
                    'description' => $itemData['description'] ?? null,
                    'quantity_ordered' => $itemData['quantity_ordered'],
                    'unit_price' => $itemData['unit_price'],
                    'line_total' => $itemData['quantity_ordered'] * $itemData['unit_price'],
                ]);
            }
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order updated successfully!');
    }

    /**
     * Remove the specified purchase order
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft purchase orders can be deleted.');
        }

        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order deleted successfully!');
    }

    /**
     * Update purchase order status
     */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'status' => 'required|in:draft,sent,confirmed,partially_received,fully_received,cancelled'
        ]);

        $oldStatus = $purchaseOrder->status;
        
        $purchaseOrder->update([
            'status' => $request->status
        ]);

        // Log the status change (optional)
        Log::info("Purchase Order {$purchaseOrder->po_number} status changed from {$oldStatus} to {$request->status} by user " . auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Purchase order status updated successfully',
            'old_status' => $oldStatus,
            'new_status' => $request->status
        ]);
    }

    /**
     * Show receive goods form
     */
    public function receive(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['sent', 'confirmed', 'partially_received'])) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot receive goods for this purchase order status.');
        }

        $purchaseOrder->load(['supplier', 'items']);
        
        return view('purchase-orders.receive', compact('purchaseOrder'));
    }

    /**
     * Generate PDF
     */
    public function generatePdf(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items', 'createdBy']);
        
        // For now, return a simple response
        return response()->json([
            'message' => 'PDF generation feature coming soon!',
            'po_number' => $purchaseOrder->po_number
        ]);
    }

    /**
     * Create PO from low stock items
     */
    public function createFromLowStock()
    {
        // This would integrate with inventory management
        return view('purchase-orders.create-from-low-stock');
    }

    /**
     * Get low stock items for AJAX
     */
    public function getLowStockItems()
    {
        // This would return low stock items
        return response()->json([]);
    }

    /**
     * Generate unique PO number
     */
    private function generatePONumber()
    {
        $year = date('Y');
        $month = date('m');
        
        try {
            // Find the last PO number for this month
            $lastPO = PurchaseOrder::where('po_number', 'like', "PO-{$year}{$month}-%")
                ->orderBy('po_number', 'desc')
                ->first();

            if ($lastPO) {
                // Extract the sequence number and increment
                $lastSequence = (int) substr($lastPO->po_number, -4);
                $newSequence = $lastSequence + 1;
            } else {
                // First PO of the month
                $newSequence = 1;
            }

            return sprintf('PO-%s%s-%04d', $year, $month, $newSequence);
            
        } catch (\Exception $e) {
            // Fallback to timestamp-based number if query fails
            return 'PO-' . date('YmdHis');
        }
    }
}
