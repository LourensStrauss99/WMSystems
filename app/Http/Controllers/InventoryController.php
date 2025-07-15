<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::query();

        // Handle the new 'filter' parameter from clickable cards
        if ($request->has('filter')) {
            switch ($request->get('filter')) {
                case 'low_stock':
                    $query->whereRaw('quantity <= min_quantity');
                    break;
                case 'critical':
                    $query->whereRaw('quantity < (min_quantity * 0.5)');
                    break;
                case 'out_of_stock':
                    $query->where('quantity', 0);
                    break;
                case 'all':
                default:
                    // Show all items - no additional filter
                    break;
            }
        }

        // Handle existing stock_filter (from dropdown)
        if ($request->has('stock_filter') && $request->get('stock_filter') !== '') {
            switch ($request->get('stock_filter')) {
                case 'low':
                    $query->whereRaw('quantity <= min_quantity');
                    break;
                case 'critical':
                    $query->whereRaw('quantity < (min_quantity * 0.5)');
                    break;
                case 'out_of_stock':
                    $query->where('quantity', 0);
                    break;
            }
        }

        // Handle search
        if ($request->has('search') && $request->get('search') !== '') {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('description', 'LIKE', "%{$search}%")
                  ->orWhere('short_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('vendor', 'LIKE', "%{$search}%");
            });
        }

        // Get filtered items
        $items = $query->orderBy('description')->get();

        // Get summary statistics (always show full stats)
        $stats = [
            'total_items' => Inventory::count(),
            'low_stock_items' => Inventory::whereRaw('quantity <= min_quantity')->count(),
            'critical_items' => Inventory::whereRaw('quantity < (min_quantity * 0.5)')->count(),
            'out_of_stock' => Inventory::where('quantity', 0)->count(),
        ];

        return view('inventory.index', compact('items', 'stats'));
    }

    /**
     * Check stock availability for jobcard
     */
    public function checkStock(Request $request)
    {
        $itemId = $request->input('item_id');
        $requestedQuantity = (int) $request->input('quantity', 1);
        
        $item = Inventory::find($itemId);
        
        if (!$item) {
            return response()->json([
                'available' => false,
                'message' => 'Item not found',
                'status' => 'error'
            ]);
        }
        
        // Check if we have enough stock - Use correct column names
        $available = $item->quantity >= $requestedQuantity;  // Changed from 'stock_level'
        
        return response()->json([
            'available' => $available,
            'current_stock' => $item->quantity,              // Changed from 'stock_level'
            'requested_quantity' => $requestedQuantity,
            'remaining_after' => max(0, $item->quantity - $requestedQuantity), // Changed column
            'is_min_level' => $item->quantity <= $item->min_quantity,          // Changed columns
            'item_name' => $item->description,               // Changed from 'name'
            'message' => $available 
                ? "✅ Available: {$item->quantity} in stock"
                : "❌ Out of Stock: Only {$item->quantity} available, you requested {$requestedQuantity}",
            'warning' => ($item->quantity - $requestedQuantity) <= $item->min_quantity && $available
                ? "⚠️ This will bring stock below minimum level ({$item->min_quantity})"
                : null
        ]);
    }

    /**
     * Get low stock items for dashboard alerts
     */
    public function getLowStockAlerts()
    {
        $lowStock = Inventory::whereRaw('stock_level <= min_level')
                            ->orderBy('stock_level')
                            ->get()
                            ->map(function($item) {
                                return [
                                    'id' => $item->id,
                                    'name' => $item->name,
                                    'short_code' => $item->short_code,
                                    'current_stock' => $item->stock_level,
                                    'min_level' => $item->min_level,
                                    'status' => $item->getStockStatus()
                                ];
                            });

        return response()->json($lowStock);
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:255',
            'short_code' => 'required|string|max:50|unique:inventory,short_code',
            'vendor' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'purchase_order_number' => 'nullable|string|max:255',
            'purchase_notes' => 'nullable|string',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'goods_received_voucher' => 'nullable|string|max:255',
            'stock_level' => 'required|integer|min:0',
            'min_level' => 'required|integer|min:0',
            'stock_update_reason' => 'nullable|string|max:255',
            'is_replenishment' => 'nullable|boolean',
            'original_item_id' => 'nullable|integer',
        ]);

        // Set default values for nullable fields
        $data['nett_price'] = $data['buying_price'];
        $data['sell_price'] = $data['selling_price'];
        $data['quantity'] = $data['stock_level'];
        $data['min_quantity'] = $data['min_level'];
        $data['stock_added'] = $data['stock_level'];
        $data['last_stock_update'] = $data['purchase_date'] ?? now()->toDateString();

        // Get company markup percentage
        $companyDetails = \App\Models\CompanyDetail::first();
        $markupPercent = $companyDetails ? $companyDetails->markup_percentage : 25;
    
        // Calculate selling price if not provided
        if (empty($data['sell_price']) && !empty($data['nett_price'])) {
            $data['sell_price'] = $data['nett_price'] * (1 + ($markupPercent / 100));
        }

        try {
            $item = Inventory::create($data);
            
            // If this is a replenishment, also update the original item's stock
            if ($request->input('is_replenishment') && $request->input('original_item_id')) {
                $originalItem = Inventory::find($request->input('original_item_id'));
                if ($originalItem) {
                    $originalItem->quantity += $data['stock_level'];           // Use quantity, not stock_level
                    $originalItem->last_stock_update = now()->toDateString();
                    $originalItem->stock_added = $data['stock_level'];
                    $originalItem->stock_update_reason = 'Stock replenished - linked to ' . $item->short_code;
                    $originalItem->save();
                    
                    $successMessage = "Stock replenishment successful! Added {$data['stock_level']} units to '{$originalItem->description}'. New total stock: {$originalItem->quantity}. Replenishment record: {$item->short_code}";
                } else {
                    $successMessage = "New inventory item added (original item not found for replenishment): {$item->short_code}";
                }
            } else {
                $successMessage = "New inventory item '{$item->description}' added successfully! Stock: {$item->quantity}, Code: {$item->short_code}";
            }
            
            return redirect()->back()->with('success', $successMessage);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error adding inventory: ' . $e->getMessage())->withInput();
        }
    }

    public function adminPanel()
    {
        $items = Inventory::orderBy('description')->get();
        return view('master-settings', compact('items')); // Changed from 'admin-panel'
    }

    public function edit($id)
    {
        $item = Inventory::findOrFail($id);
        return view('inventory.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Inventory::findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:255',
            'short_code' => 'required|string|max:50|unique:inventory,short_code,' . $id,
            'vendor' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_level' => 'required|integer|min:0',
            'min_level' => 'required|integer|min:0',
        ]);

        // Update derived fields
        $data['nett_price'] = $data['buying_price'];
        $data['sell_price'] = $data['selling_price'];
        $data['quantity'] = $data['stock_level'];
        $data['min_quantity'] = $data['min_level'];

        try {
            $item->update($data);
            return redirect('/inventory')->with('success', 'Inventory item updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating inventory: ' . $e->getMessage())->withInput();
        }
    }
    /**
     * Display the specified inventory item
     */
    public function show($id)
    {
        $item = Inventory::findOrFail($id);
        return view('inventory.view', compact('item'));
    }
}