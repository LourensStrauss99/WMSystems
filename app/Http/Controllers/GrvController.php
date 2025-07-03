<?php
// filepath: app/Http/Controllers/GrvController.php

namespace App\Http\Controllers;

use App\Models\GoodsReceivedVoucher;
use App\Models\PurchaseOrder;
use App\Models\GrvItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GrvController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of GRVs
     */
    public function index()
    {
        $grvs = GoodsReceivedVoucher::with(['purchaseOrder.supplier', 'receivedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('grv.index', compact('grvs'));
    }

    /**
     * Show the form for creating a new GRV
     */
    public function create(Request $request)
    {
        $purchaseOrderId = $request->get('purchase_order_id');
        $purchaseOrder = null;
        
        if ($purchaseOrderId) {
            $purchaseOrder = PurchaseOrder::with(['supplier', 'items'])->findOrFail($purchaseOrderId);
            
            if (!$purchaseOrder->canCreateGrv()) {
                return redirect()->route('grv.index')
                    ->with('error', 'Cannot create GRV for this purchase order status.');
            }
        }

        $availablePOs = PurchaseOrder::with('supplier')
            ->whereIn('status', ['sent', 'approved', 'partially_received'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('grv.create', compact('purchaseOrder', 'availablePOs'));
    }

    /**
     * Store a newly created GRV
     */
    public function store(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'received_date' => 'required|date',
            'received_time' => 'required',
            'delivery_note_number' => 'nullable|string|max:255',
            'vehicle_registration' => 'nullable|string|max:255',
            'driver_name' => 'nullable|string|max:255',
            'delivery_company' => 'nullable|string|max:255',
            'overall_status' => 'required|in:complete,partial,damaged,rejected',
            'general_notes' => 'nullable|string',
            'discrepancies' => 'nullable|string',
            'quality_check_passed' => 'boolean',
            'quality_notes' => 'nullable|string',
            'delivery_note_received' => 'boolean',
            'invoice_received' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.quantity_rejected' => 'required|integer|min:0',
            'items.*.quantity_damaged' => 'required|integer|min:0',
            'items.*.condition' => 'required|in:good,damaged,defective,expired',
            'items.*.item_notes' => 'nullable|string',
            'items.*.rejection_reason' => 'nullable|string',
            'items.*.storage_location' => 'nullable|string|max:255',
            'items.*.batch_number' => 'nullable|string|max:255',
            'items.*.expiry_date' => 'nullable|date',
        ]);

        DB::transaction(function() use ($request) {
            // Create GRV
            $grv = GoodsReceivedVoucher::create([
                'grv_number' => GoodsReceivedVoucher::generateGrvNumber(),
                'purchase_order_id' => $request->purchase_order_id,
                'received_date' => $request->received_date,
                'received_time' => $request->received_time,
                'received_by' => Auth::id(),
                'delivery_note_number' => $request->delivery_note_number,
                'vehicle_registration' => $request->vehicle_registration,
                'driver_name' => $request->driver_name,
                'delivery_company' => $request->delivery_company,
                'overall_status' => $request->overall_status,
                'general_notes' => $request->general_notes,
                'discrepancies' => $request->discrepancies,
                'quality_check_passed' => $request->boolean('quality_check_passed'),
                'quality_notes' => $request->quality_notes,
                'delivery_note_received' => $request->boolean('delivery_note_received'),
                'invoice_received' => $request->boolean('invoice_received'),
            ]);

            // Create GRV items
            foreach ($request->items as $itemData) {
                $poItem = \App\Models\PurchaseOrderItem::find($itemData['purchase_order_item_id']);
                
                GrvItem::create([
                    'grv_id' => $grv->id,
                    'purchase_order_item_id' => $itemData['purchase_order_item_id'],
                    'inventory_id' => $poItem->inventory_id,
                    'quantity_ordered' => $poItem->quantity_ordered,
                    'quantity_received' => $itemData['quantity_received'],
                    'quantity_rejected' => $itemData['quantity_rejected'],
                    'quantity_damaged' => $itemData['quantity_damaged'] ?? 0,
                    'condition' => $itemData['condition'],
                    'item_notes' => $itemData['item_notes'] ?? null,
                    'rejection_reason' => $itemData['rejection_reason'] ?? null,
                    'storage_location' => $itemData['storage_location'] ?? null,
                    'batch_number' => $itemData['batch_number'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                ]);
            }
        });

        return redirect()->route('grv.index')
            ->with('success', 'GRV created successfully! Please proceed with quality check.');
    }

    /**
     * Display the specified GRV
     */
    public function show($id)
    {
        $grv = GoodsReceivedVoucher::with([
            'purchaseOrder.supplier',
            'items.purchaseOrderItem',
            'receivedBy',
            'checkedBy'
        ])->findOrFail($id);

        return view('grv.show', compact('grv'));
    }

    /**
     * Approve GRV and update inventory
     */
    public function approve($id)
    {
        $grv = GoodsReceivedVoucher::findOrFail($id);
        
        if ($grv->checked_by) {
            return back()->with('error', 'This GRV has already been approved.');
        }
        
        if (!$grv->quality_check_passed) {
            return back()->with('error', 'Quality check must pass before approval.');
        }

        DB::transaction(function() use ($grv) {
            Log::info("Starting GRV approval process", [
                'grv_id' => $grv->id,
                'grv_number' => $grv->grv_number,
                'po_number' => $grv->purchaseOrder->po_number
            ]);
            
            $updatedItems = 0;
            
            // Update inventory for each item
            foreach ($grv->items as $item) {
                Log::info("Processing GRV item", [
                    'grv_item_id' => $item->id,
                    'inventory_id' => $item->inventory_id,
                    'accepted_quantity' => $item->getAcceptedQuantity(),
                    'stock_updated' => $item->stock_updated
                ]);
                
                if ($item->updateInventoryStock()) {
                    $updatedItems++;
                }
            }

            // Update GRV as checked
            $grv->update([
                'checked_by' => Auth::id(),
            ]);

            // Update purchase order status
            $grv->purchaseOrder->updateStatusBasedOnItems();
            
            Log::info("GRV approval completed", [
                'grv_id' => $grv->id,
                'grv_number' => $grv->grv_number,
                'items_updated' => $updatedItems,
                'po_status' => $grv->purchaseOrder->fresh()->status
            ]);
        });

        return back()->with('success', 'GRV approved successfully! Inventory has been updated.');
    }

    /**
     * Mark GRV as quality check passed
     */
    public function passQualityCheck($id)
    {
        $grv = GoodsReceivedVoucher::findOrFail($id);
        
        $grv->update([
            'quality_check_passed' => true,
            'checked_by' => Auth::id(),
        ]);

        return back()->with('success', 'Quality check passed! GRV can now be approved.');
    }

    /**
     * Mark GRV as quality check failed
     */
    public function failQualityCheck(Request $request, $id)
    {
        $request->validate([
            'quality_notes' => 'required|string',
        ]);

        $grv = GoodsReceivedVoucher::findOrFail($id);
        
        $grv->update([
            'quality_check_passed' => false,
            'quality_notes' => $request->quality_notes,
            'checked_by' => Auth::id(),
        ]);

        return back()->with('error', 'Quality check failed. Please review the items and notes.');
    }

    /**
     * Get purchase order details for GRV creation
     */
    public function getPurchaseOrderDetails($id)
    {
        $po = PurchaseOrder::with(['supplier', 'items'])->findOrFail($id);
        
        return response()->json([
            'po_number' => $po->po_number,
            'supplier' => $po->supplier->name,
            'items' => $po->items->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->item_name,
                    'code' => $item->item_code,
                    'description' => $item->item_description,
                    'quantity_ordered' => $item->quantity_ordered,
                    'quantity_received' => $item->quantity_received ?? 0,
                    'outstanding' => $item->quantity_ordered - ($item->quantity_received ?? 0),
                ];
            })
        ]);
    }
}