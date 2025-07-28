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
        $inventory = Inventory::orderBy('description')->get();
        
        return view('purchase-orders.create', compact('suppliers', 'inventory'));
    }

    /**
     * Store a newly created purchase order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.inventory_id' => 'nullable|exists:inventory,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_code' => 'nullable|string|max:50',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity_ordered' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function() use ($validated) {
            // Get supplier information
            $supplier = Supplier::findOrFail($validated['supplier_id']);
            
            // Generate PO number
            $poNumber = $this->generatePONumber();
            
            // Calculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity_ordered'] * $item['unit_price'];
            }
            
            $companyDetails = \App\Models\CompanyDetail::first();
            $vatPercent = $companyDetails ? $companyDetails->vat_percent : 15;
            $vatAmount = $subtotal * ($vatPercent / 100);
            $grandTotal = $subtotal + $vatAmount;
            
            // Create purchase order with CORRECT column names
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $validated['supplier_id'],
                'supplier_name' => $supplier->name,
                'supplier_contact' => $supplier->contact_person ?? '',
                'supplier_email' => $supplier->email ?? '',
                'supplier_phone' => $supplier->phone ?? '',
                'supplier_address' => $supplier->address ?? '',
                'order_date' => $validated['order_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'draft',
                'total_amount' => $subtotal,        // Use total_amount instead of subtotal
                'vat_amount' => $vatAmount,
                'grand_total' => $grandTotal,
                'created_by' => auth()->id(),
                'terms_conditions' => '',
                'payment_terms' => $companyDetails ? $companyDetails->default_payment_terms : 30,
            ]);

            // Create Purchase Order Items
            foreach ($validated['items'] as $itemData) {
                $purchaseOrder->items()->create([
                    'inventory_id' => $itemData['inventory_id'],
                    'item_name' => $itemData['item_name'],
                    'item_code' => $itemData['item_code'],
                    'item_description' => $itemData['item_description'],
                    'quantity_ordered' => $itemData['quantity_ordered'],
                    'unit_price' => $itemData['unit_price'],
                    'line_total' => $itemData['quantity_ordered'] * $itemData['unit_price'],
                ]);
            }
            // Ensure totals are correct
            $purchaseOrder->calculateTotals();
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
    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        
        // Only allow editing draft and rejected POs
        if (!in_array($purchaseOrder->status, ['draft', 'rejected'])) {
            return redirect()->route('purchase-orders.show', $id)
                ->with('error', 'Only draft or rejected purchase orders can be edited.');
        }
        
        $suppliers = Supplier::where('active', true)->orderBy('name')->get();
        $inventory = Inventory::orderBy('description')->get(); // Remove the active filter
        
        // Load existing items for pre-population
        $purchaseOrder->load(['supplier', 'items']);
        
        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'inventory'));
    }

    /**
     * Update the specified purchase order
     */
    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        
        // Only allow updating draft and rejected POs
        if (!in_array($purchaseOrder->status, ['draft', 'rejected'])) {
            return redirect()->route('purchase-orders.show', $id)
                ->with('error', 'Only draft or rejected purchase orders can be updated.');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.inventory_id' => 'nullable|exists:inventory,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_code' => 'nullable|string|max:50',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity_ordered' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function() use ($validated, $purchaseOrder) {
            // Calculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity_ordered'] * $item['unit_price'];
            }
            
            $vatAmount = $subtotal * 0.15;
            $grandTotal = $subtotal + $vatAmount;

            // Prepare update data
            $updateData = [
                'supplier_id' => $validated['supplier_id'],
                'order_date' => $validated['order_date'],
                'vat_amount' => $vatAmount,
                'grand_total' => $grandTotal,
                'amended_by' => auth()->id(),
                'amended_at' => now(),
            ];

            // If the order was rejected, reset it to draft status
            if ($purchaseOrder->status === 'rejected') {
                $updateData['status'] = 'draft';
                // Clear current rejection data but keep history
                $updateData['rejected_by'] = null;
                $updateData['rejected_at'] = null;
                $updateData['rejection_reason'] = null;
                // rejection_history is preserved
            }

            // Update purchase order
            $purchaseOrder->update($updateData);

            // Delete existing items
            $purchaseOrder->items()->delete();

            // Add new items
            foreach ($validated['items'] as $item) {
                $purchaseOrder->items()->create([
                    'inventory_id' => $item['inventory_id'],
                    'item_name' => $item['item_name'],
                    'item_code' => $item['item_code'],
                    'item_description' => $item['item_description'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['quantity_ordered'] * $item['unit_price'],
                ]);
            }
            // Ensure totals are correct
            $purchaseOrder->calculateTotals();
        });

        $message = $purchaseOrder->status === 'draft' 
            ? 'Purchase order updated and reset to draft status. You can now submit it for approval again.'
            : 'Purchase order updated successfully.';

        return redirect()->route('purchase-orders.show', $id)
            ->with('success', $message);
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
    public function reject(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending_approval') {
            return back()->with('error', 'Only orders pending approval can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        // Add to rejection history
        $purchaseOrder->addRejectionToHistory($request->rejection_reason, Auth::id());

        $purchaseOrder->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Purchase Order rejected with reason provided.');
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
