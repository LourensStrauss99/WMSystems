<?php
// filepath: app/Http/Controllers/GrvController.php

namespace App\Http\Controllers;

use App\Models\GoodsReceivedVoucher;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem; // Add this missing import
use App\Models\Inventory;         // Add this missing import
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
        // Remove any other middleware that might be redirecting
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
        // Add debug logging
        Log::info('GRV Create accessed', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user' => Auth::id(),
            'request_data' => $request->all()
        ]);
        
        $purchaseOrderId = $request->get('purchase_order_id');
        $purchaseOrder = null;
        
        if ($purchaseOrderId) {
            $purchaseOrder = PurchaseOrder::with(['supplier', 'items'])->find($purchaseOrderId);
        }

        // Get available POs for selection
        $availablePOs = PurchaseOrder::with('supplier')
            ->whereIn('status', ['sent', 'approved', 'partially_received', 'pending_approval']) // Add more statuses
            ->orderBy('created_at', 'desc')
            ->get();

        // ✅ ALWAYS return the view - don't redirect
        return view('grv.create', compact('purchaseOrder', 'availablePOs'));
    }

    /**
     * Store a newly created GRV
     */
    public function store(Request $request)
    {
        file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Store method called\n", FILE_APPEND);
        file_put_contents(storage_path('logs/debug.log'), "Request data: " . json_encode($request->all()) . "\n", FILE_APPEND);
        
        // Debug: log all incoming request data
        file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - GRV store called\n", FILE_APPEND);
        file_put_contents(storage_path('logs/debug.log'), "Request data: " . json_encode($request->all()) . "\n", FILE_APPEND);

        try {
            $validated = $request->validate([
                'purchase_order_id' => 'required|exists:purchase_orders,id',
                'received_date' => 'required|date',
                'received_time' => 'required',
                'delivery_note_number' => 'nullable|string|max:255',
                'vehicle_registration' => 'nullable|string|max:255',
                'driver_name' => 'nullable|string|max:255',
                'delivery_company' => 'nullable|string|max:255',
                'overall_status' => 'required|string',
                'quality_check_passed' => 'nullable|boolean',
                'delivery_note_received' => 'nullable|boolean',
                'invoice_received' => 'nullable|boolean',
                'general_notes' => 'nullable|string',
                'discrepancies' => 'nullable|string',
                'quality_notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
                'items.*.quantity_received' => 'required|integer|min:0',
                'items.*.quantity_rejected' => 'nullable|integer|min:0',
                'items.*.quantity_damaged' => 'nullable|integer|min:0',
                'items.*.condition' => 'required|string',
                'items.*.batch_number' => 'nullable|string',
                'items.*.expiry_date' => 'nullable|date',
                'items.*.storage_location' => 'nullable|string',
                'items.*.item_notes' => 'nullable|string',
                'items.*.rejection_reason' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Validation error: " . json_encode($e->errors()) . "\n", FILE_APPEND);
            throw $e;
        }

        file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Validation passed\n", FILE_APPEND);

        $grv = null;

        try {
            DB::transaction(function() use ($validated, &$grv) {
                file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Creating GRV...\n", FILE_APPEND);
                
                // Create GRV
                $grv = GoodsReceivedVoucher::create([
                    'grv_number' => $this->generateGrvNumber(),
                    'purchase_order_id' => $validated['purchase_order_id'],
                    'received_date' => $validated['received_date'],
                    'received_time' => $validated['received_time'],
                    'received_by' => Auth::id(),
                    'delivery_note_number' => $validated['delivery_note_number'],
                    'vehicle_registration' => $validated['vehicle_registration'],
                    'driver_name' => $validated['driver_name'],
                    'delivery_company' => $validated['delivery_company'],
                    'overall_status' => $validated['overall_status'],
                    'general_notes' => $validated['general_notes'],
                    'discrepancies' => $validated['discrepancies'],
                    'quality_notes' => $validated['quality_notes'],
                    'quality_check_passed' => $validated['quality_check_passed'] ?? false,
                    'delivery_note_received' => $validated['delivery_note_received'] ?? false,
                    'invoice_received' => $validated['invoice_received'] ?? false,
                ]);

                file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - GRV created with ID: {$grv->id}\n", FILE_APPEND);

                // Create GRV items
                foreach ($validated['items'] as $itemData) {
                    $poItem = PurchaseOrderItem::findOrFail($itemData['purchase_order_item_id']);
                    
                    file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Processing PO item: {$poItem->id} - {$poItem->item_name}\n", FILE_APPEND);
                    
                    // Find inventory by item code first, then by name
                    $inventory = Inventory::where('short_code', $poItem->item_code)->first();
                    
                    if (!$inventory) {
                        $inventory = Inventory::where('description', 'LIKE', '%' . $poItem->item_name . '%')->first();
                    }
                    
                    if (!$inventory) {
                        file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Creating new inventory item for: {$poItem->item_name}\n", FILE_APPEND);
                        
                        // Determine department from item name or default to General
                        $department = $this->determineDepartmentFromItemName($poItem->item_name);
                        
                        // Generate proper department-based inventory code
                        $inventoryCode = Inventory::generateInventoryCode($department);
                        
                        $inventory = Inventory::create([
                            'description' => $poItem->item_name,
                            'short_code' => $inventoryCode, // Use proper department-based code
                            'department' => $department, // Add department field
                            'vendor' => $grv->purchaseOrder->supplier->name ?? 'Unknown',
                            'nett_price' => $poItem->unit_price,
                            'buying_price' => $poItem->unit_price, // <-- Set buying_price from PO item
                            'sell_price' => $poItem->unit_price * 1.3, // 30% markup
                            'quantity' => 0, // Will be updated when stock is received
                            'min_quantity' => 5, // Default minimum
                            'invoice_number' => null,
                            'receipt_number' => null,
                            'purchase_date' => now(),
                            'purchase_order_number' => $grv->purchaseOrder->po_number,
                            'purchase_notes' => "Created from GRV #{$grv->id}",
                            'last_stock_update' => now(),
                            'stock_added' => 0,
                            'stock_update_reason' => 'Initial creation from GRV',
                        ]);
                        
                        Log::info("Created new inventory item with department code", [
                            'id' => $inventory->id,
                            'description' => $inventory->description,
                            'short_code' => $inventory->short_code,
                            'department' => $inventory->department
                        ]);
                    }

                    // Create GRV item with CORRECT field name
                    $grvItem = GrvItem::create([
                        'grv_id' => $grv->id,
                        'purchase_order_item_id' => $itemData['purchase_order_item_id'],
                        'inventory_id' => $inventory->id,
                        'quantity_ordered' => $poItem->quantity_ordered, // ✅ FIXED!
                        'quantity_received' => $itemData['quantity_received'],
                        'quantity_rejected' => $itemData['quantity_rejected'] ?? 0,
                        'quantity_damaged' => $itemData['quantity_damaged'] ?? 0,
                        'condition' => $itemData['condition'],
                        'batch_number' => $itemData['batch_number'],
                        'expiry_date' => $itemData['expiry_date'],
                        'storage_location' => $itemData['storage_location'],
                        'notes' => $itemData['item_notes'],
                        'rejection_reason' => $itemData['rejection_reason'],
                        'stock_updated' => false,
                    ]);
                    
                    file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Created GRV item with ID: {$grvItem->id}\n", FILE_APPEND);
                }
                
                // Update purchase order status
                $po = PurchaseOrder::findOrFail($validated['purchase_order_id']);
                $po->update(['status' => 'received']);
                
                file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Updated PO status to 'received'\n", FILE_APPEND);
            });

            file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Transaction completed successfully\n", FILE_APPEND);

            // ✅ SUCCESS MESSAGE AND REDIRECT
            if ($grv) {
                return redirect()->route('grv.show', $grv->id)
                    ->with('success', "🎉 GRV {$grv->grv_number} created successfully! {$grv->items->count()} items received.");
            } else {
                return back()->with('error', '❌ Error: GRV could not be created.');
            }
                
        } catch (\Exception $e) {
            file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n", FILE_APPEND);
            
            return back()->withInput()
                ->with('error', "❌ Error creating GRV: " . $e->getMessage());
        }
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
        file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Approve method called for GRV ID: {$id}\n", FILE_APPEND);
        
        try {
            $grv = GoodsReceivedVoucher::with(['items.inventory'])->findOrFail($id);
            file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - GRV found: {$grv->grv_number}, checked_by: " . ($grv->checked_by ?? 'null') . "\n", FILE_APPEND);
            
            $updatedItems = 0;
            
            file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Processing " . $grv->items->count() . " items\n", FILE_APPEND);
            
            DB::transaction(function() use ($grv, &$updatedItems) {
                foreach ($grv->items as $item) {
                    file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Processing item {$item->id}, inventory_id: {$item->inventory_id}, quantity: {$item->getAcceptedQuantity()}\n", FILE_APPEND);
                    
                    if ($item->inventory_id && $item->getAcceptedQuantity() > 0) {
                        $inventory = $item->inventory;
                        if ($inventory) {
                            $oldStock = $inventory->quantity; // Use correct column name
                            
                            // Complete database update with correct syntax
                            $affected = DB::table('inventory')
                                ->where('id', $item->inventory_id)
                                ->update([
                                    'quantity' => DB::raw('quantity + ' . $item->getAcceptedQuantity()),  // Make sure this uses 'quantity'
                                    'last_stock_update' => now(),
                                    'stock_added' => $item->getAcceptedQuantity(),
                                    'stock_update_reason' => "GRV #{$grv->grv_number} - Stock received",
                                ]);
                    
                            file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Database update result: {$affected} rows affected\n", FILE_APPEND);
                    
                            // Mark as updated
                            $item->stock_updated = true;
                            $item->save();
                        
                            $updatedItems++;
                        
                            // Verify the update
                            $newStock = DB::table('inventory')->where('id', $item->inventory_id)->value('quantity');
                            file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Stock updated from {$oldStock} to {$newStock}\n", FILE_APPEND);
                        }
                    }
                }
                
                // Mark GRV as approved
                $grv->checked_by = Auth::id();
                $grv->save();
                
                file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - GRV marked as approved\n", FILE_APPEND);
            });
            
            file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Transaction completed successfully, {$updatedItems} items updated\n", FILE_APPEND);
            
            return back()->with('success', "GRV approved successfully! {$updatedItems} inventory items updated.");
            
        } catch (\Exception $e) {
            file_put_contents(storage_path('logs/debug.log'), date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n", FILE_APPEND);
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
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

    /**
     * Debug GRV approval process
     */
    public function debugApproval($id)
    {
        $grv = GoodsReceivedVoucher::with(['items.inventory', 'items.purchaseOrderItem'])->findOrFail($id);
        
        $debug = [];
        
        foreach ($grv->items as $item) {
            $debug[] = [
                'grv_item_id' => $item->id,
                'inventory_id' => $item->inventory_id,
                'inventory_exists' => $item->inventory ? 'YES' : 'NO',
                'current_inventory_stock' => $item->inventory ? $item->inventory->stock_level : 'N/A',
                'quantity_received' => $item->quantity_received,
                'quantity_rejected' => $item->quantity_rejected,
                'quantity_damaged' => $item->quantity_damaged,
                'accepted_quantity' => $item->getAcceptedQuantity(),
                'stock_updated' => $item->stock_updated ? 'YES' : 'NO',
                'inventory_short_code' => $item->inventory ? $item->inventory->short_code : 'N/A'
            ];
        }
        
        return response()->json([
            'grv_number' => $grv->grv_number,
            'checked_by' => $grv->checked_by,
            'items' => $debug
        ], 200, [], JSON_PRETTY_PRINT);
    }

    // Add the missing generateGrvNumber method
    private function generateGrvNumber()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastGrv = GoodsReceivedVoucher::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastGrv ? (int)substr($lastGrv->grv_number, -4) + 1 : 1;
        
        return "GRV-{$year}{$month}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Force update inventory (temporary debug method)
     */
    public function forceUpdateInventory($id)
    {
        try {
            $grv = GoodsReceivedVoucher::with(['items.inventory'])->findOrFail($id);
            
            $results = [];
            
            foreach ($grv->items as $item) {
                if ($item->inventory_id && $item->getAcceptedQuantity() > 0) {
                    
                    // Direct database update - FIXED TABLE NAME
                    $affected = DB::table('inventory')
                        ->where('id', $item->inventory_id)
                        ->update([
                            'quantity' => DB::raw('quantity + ' . $item->getAcceptedQuantity()),
                            'last_stock_update' => now(),
                            'stock_added' => $item->getAcceptedQuantity(),
                            'stock_update_reason' => "Force update via GRV: {$grv->grv_number}"
                        ]);
                    
                    // Mark item as updated
                    $item->update(['stock_updated' => true]);
                    
                    $results[] = [
                        'grv_item_id' => $item->id,
                        'inventory_id' => $item->inventory_id,
                        'quantity_added' => $item->getAcceptedQuantity(),
                        'rows_affected' => $affected,
                        'new_stock' => $item->inventory->fresh()->stock_level
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Force update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Determine department from item name using keywords
     */
    private function determineDepartmentFromItemName($itemName)
    {
        $itemNameLower = strtolower($itemName);
        
        // Department keyword mapping
        $departmentKeywords = [
            'EL' => ['electric', 'electrical', 'wire', 'cable', 'switch', 'socket', 'plug', 'led', 'bulb', 'light', 'amp', 'volt'],
            'PL' => ['plumb', 'pipe', 'tap', 'valve', 'drain', 'toilet', 'sink', 'basin', 'shower', 'bath', 'water'],
            'MC' => ['mechanical', 'gear', 'bearing', 'shaft', 'motor', 'engine', 'pump', 'compressor'],
            'TL' => ['tool', 'hammer', 'screwdriver', 'wrench', 'drill', 'saw', 'spanner'],
            'HV' => ['hvac', 'air conditioning', 'heating', 'ventilation', 'fan', 'duct'],
            'HW' => ['screw', 'bolt', 'nut', 'washer', 'nail', 'fastener', 'hardware'],
            'CH' => ['chemical', 'acid', 'solvent', 'cleaner', 'oil', 'grease'],
            'SF' => ['safety', 'helmet', 'glove', 'goggle', 'mask', 'harness'],
            'CL' => ['clean', 'detergent', 'soap', 'sanitizer', 'disinfectant'],
            'PP' => ['fitting', 'elbow', 'joint', 'coupling', 'adapter'],
        ];
        
        // Check for keyword matches
        foreach ($departmentKeywords as $dept => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($itemNameLower, $keyword) !== false) {
                    return $dept;
                }
            }
        }
        
        // Default to General if no match found
        return 'GE';
    }
}