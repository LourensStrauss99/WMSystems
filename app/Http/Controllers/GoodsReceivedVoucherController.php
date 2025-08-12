<?php
// Create controller: php artisan make:controller GoodsReceivedVoucherController

// filepath: app/Http/Controllers/GoodsReceivedVoucherController.php


namespace App\Http\Controllers;

use App\Models\GoodsReceivedVoucher;
use App\Models\GrvItem;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\TenantDatabaseSwitch;

class GoodsReceivedVoucherController extends Controller
{
    use TenantDatabaseSwitch;
    
    public function index()
    {
        $this->switchToTenantDatabase();
        
        $grvs = GoodsReceivedVoucher::with(['purchaseOrder', 'receivedBy', 'checkedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('grv.index', compact('grvs'));
    }

    public function create(Request $request)
    {
        $this->switchToTenantDatabase();
        
        $poId = $request->get('po_id');
        $purchaseOrders = PurchaseOrder::where('status', '!=', 'completed')
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $selectedPo = null;
        
        if ($poId) {
            $selectedPo = PurchaseOrder::with('items')->find($poId);
        }

        return view('grv.create', compact('purchaseOrders', 'selectedPo'));
    }

    public function store(Request $request)
    {
        $this->switchToTenantDatabase();
        
        $request->validate([
            'po_id' => 'required|exists:purchase_orders,id',
            'received_by' => 'required|exists:users,id',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:grv_items,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $grv = GoodsReceivedVoucher::create([
                'po_id' => $request->po_id,
                'received_by' => $request->received_by,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                GrvItem::create([
                    'grv_id' => $grv->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                ]);
            }
        });

        return redirect()->route('grv.index')->with('success', 'Goods Received Voucher created successfully.');
    }

    public function show(GoodsReceivedVoucher $grv)
    {
        $this->switchToTenantDatabase();
        
        $grv->load(['purchaseOrder', 'receivedBy', 'checkedBy', 'items.item']);
        
        return view('grv.show', compact('grv'));
    }

    public function edit(GoodsReceivedVoucher $grv)
    {
        $this->switchToTenantDatabase();
        
        $grv->load(['purchaseOrder', 'receivedBy', 'checkedBy', 'items.item']);
        
        $purchaseOrders = PurchaseOrder::where('status', '!=', 'completed')
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('grv.edit', compact('grv', 'purchaseOrders'));
    }

    public function update(Request $request, GoodsReceivedVoucher $grv)
    {
        $this->switchToTenantDatabase();
        
        $request->validate([
            'po_id' => 'required|exists:purchase_orders,id',
            'received_by' => 'required|exists:users,id',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:grv_items,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request, $grv) {
            $grv->update([
                'po_id' => $request->po_id,
                'received_by' => $request->received_by,
                'status' => 'pending',
                'updated_by' => Auth::id(),
            ]);

            $existingItemIds = $grv->items->pluck('id')->toArray();
            $updatedItemIds = collect($request->items)->pluck('id')->toArray();

            // Update existing items
            foreach ($request->items as $item) {
                GrvItem::where('id', $item['id'])->update([
                    'quantity' => $item['quantity'],
                ]);
            }

            // Delete removed items
            $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
            GrvItem::whereIn('id', $itemsToDelete)->delete();
        });

        return redirect()->route('grv.index')->with('success', 'Goods Received Voucher updated successfully.');
    }

    public function destroy(GoodsReceivedVoucher $grv)
    {
        $this->switchToTenantDatabase();
        
        $grv->delete();
        
        return redirect()->route('grv.index')->with('success', 'Goods Received Voucher deleted successfully.');
    }
}
