<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function details($id)
    {
        $po = PurchaseOrder::with(['supplier', 'items'])->find($id);
        if (!$po) {
            return response()->json(['error' => 'Purchase order not found'], 404);
        }
        return response()->json([
            'id' => $po->id,
            'po_number' => $po->po_number,
            'supplier' => $po->supplier ? $po->supplier->name : null,
            'items' => $po->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->item_name,
                    'code' => $item->item_code,
                    'quantity_ordered' => $item->quantity_ordered,
                    'outstanding' => $item->quantity_ordered - $item->quantity_received,
                ];
            }),
            'total' => $po->items->sum(function ($item) {
                return $item->unit_price * $item->quantity_ordered;
            }),
        ]);
    }
}
