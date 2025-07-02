<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\User;
use App\Mail\PurchaseOrderMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $purchaseOrders = PurchaseOrder::with(['supplier', 'submittedBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new purchase order
     */
    public function create()
    {
        // Get active suppliers
        $suppliers = Supplier::where('active', true)
            ->orderBy('name')
            ->get();
        
        // Get all inventory items (adjust the query based on your needs)
        $inventory = Inventory::orderBy('name')->get();
        
        return view('purchase-orders.create', compact('suppliers', 'inventory'));
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
                    'item_code' => $itemData['item_code'] ?? '',
                    'item_description' => $itemData['description'] ?? '',
                    'item_category' => $itemData['item_category'] ?? '',
                    'quantity_ordered' => $itemData['quantity_ordered'],
                    'quantity_received' => 0,
                    'unit_price' => $itemData['unit_price'],
                    'line_total' => $itemData['quantity_ordered'] * $itemData['unit_price'],
                    'unit_of_measure' => $itemData['unit_of_measure'] ?? 'each',
                    'status' => 'pending',
                    'inventory_id' => $itemData['inventory_id'] ?? null,
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
        // Load the relationships
        $purchaseOrder->load(['supplier', 'items']);
        
        // Debug: Check if data is loaded
        // dd($purchaseOrder->toArray()); // Uncomment this line to debug
        
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified purchase order
     */
    public function edit($id)  // Change parameter from PurchaseOrder $purchaseOrder to $id
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        
        if (!in_array($purchaseOrder->status, ['draft', 'pending_approval'])) {
            return redirect()->route('purchase-orders.show', $id)  // Use $id instead of $purchaseOrder
                ->with('error', 'Only draft or pending approval purchase orders can be edited.');
        }

        $suppliers = Supplier::where('active', true)->orderBy('name')->get();
        $purchaseOrder->load(['supplier', 'items']);
        
        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers'));
    }

    /**
     * Update the specified purchase order
     */
    public function update(Request $request, $id)  // Change parameter from PurchaseOrder $purchaseOrder to $id
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        
        if (!in_array($purchaseOrder->status, ['draft', 'pending_approval'])) {
            return redirect()->route('purchase-orders.show', $id)  // Use $id instead of $purchaseOrder
                ->with('error', 'Only draft or pending approval purchase orders can be updated.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_code' => 'nullable|string|max:100',
            'items.*.item_description' => 'nullable|string|max:500',
            'items.*.quantity_ordered' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $purchaseOrder) {
            // Get supplier information
            $supplier = Supplier::findOrFail($request->supplier_id);
            
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
                'supplier_name' => $supplier->name,
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
                    'item_code' => $itemData['item_code'] ?? '',
                    'item_description' => $itemData['item_description'] ?? '',
                    'quantity_ordered' => $itemData['quantity_ordered'],
                    'quantity_received' => 0,
                    'unit_price' => $itemData['unit_price'],
                    'line_total' => $itemData['quantity_ordered'] * $itemData['unit_price'],
                    'unit_of_measure' => 'each',
                    'status' => 'pending',
                    'inventory_id' => $itemData['inventory_id'] ?? null,
                ]);
            }
        });

        return redirect()->route('purchase-orders.show', $id)  // Use $id instead of $purchaseOrder
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
        Log::info("Purchase Order {$purchaseOrder->po_number} status changed from {$oldStatus} to {$request->status} by user " . Auth::id());

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
            return redirect()->route('purchase-orders.show', $purchaseOrder->id)
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

    /**
     * Submit purchase order for approval
     */
    public function submitForApproval(PurchaseOrder $purchaseOrder)
    {
        // Check if PO is in draft status
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft orders can be submitted for approval.');
        }

        // Update status to pending approval
        $purchaseOrder->update([
            'status' => 'pending_approval',
            'submitted_for_approval_at' => now(),
            'submitted_by' => Auth::id(),
        ]);

        // Optional: Notify approvers via email
        // Uncomment these lines if you want email notifications:
        /*
        $approvers = User::where('role', 'manager')->get(); // Adjust query as needed
        foreach ($approvers as $approver) {
            // You'll need to create this Mail class
            Mail::to($approver)->send(new PurchaseOrderPendingApproval($purchaseOrder));
        }
        */

        return back()->with('success', 'Purchase Order submitted for approval successfully!');
    }

    /**
     * Approve purchase order
     */
    public function approve(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending_approval') {
            return back()->with('error', 'Only orders pending approval can be approved.');
        }

        $purchaseOrder->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Purchase Order approved successfully!');
    }

    /**
     * Reject purchase order
     */
    public function reject(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending_approval') {
            return back()->with('error', 'Only orders pending approval can be rejected.');
        }

        $purchaseOrder->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
        ]);

        return back()->with('success', 'Purchase Order rejected.');
    }

    /**
     * Send to supplier
     */
    public function sendToSupplier(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'approved') {
            return back()->with('error', 'Only approved orders can be sent to suppliers.');
        }

        // Load relationships for email
        $purchaseOrder->load(['supplier', 'items']);

        // Check if supplier has email
        if (!$purchaseOrder->supplier || !$purchaseOrder->supplier->email) {
            return back()->with('error', 'Supplier email address is required to send purchase order.');
        }

        try {
            // Send email to supplier
            Mail::to($purchaseOrder->supplier->email)->send(new PurchaseOrderMail($purchaseOrder));

            // Update status
            $purchaseOrder->update([
                'status' => 'sent',
                'sent_at' => now(),
                'sent_by' => Auth::id(),
            ]);

            return back()->with('success', "Purchase Order sent to {$purchaseOrder->supplier->name} ({$purchaseOrder->supplier->email}) successfully!");

        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to send purchase order email', [
                'po_id' => $purchaseOrder->id,
                'supplier_email' => $purchaseOrder->supplier->email,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to send email to supplier. Please try again or contact them directly.');
        }
    }

    /**
     * Show orders pending approval
     */
    public function approvals()
    {
        // Check if user can approve (optional - remove if you want all users to see this page)
        if (!auth()->user()->canApprove()) {
            abort(403, 'Unauthorized to view approvals.');
        }

        $pendingOrders = PurchaseOrder::with(['supplier', 'submittedBy'])
            ->where('status', 'pending_approval')
            ->orderBy('submitted_for_approval_at', 'desc')
            ->paginate(20);

        return view('approvals.index', compact('pendingOrders'));
    }
}
