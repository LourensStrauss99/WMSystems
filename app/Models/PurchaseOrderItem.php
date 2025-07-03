<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'inventory_id',
        'item_name',
        'item_code',
        'item_description',
        'quantity_ordered',
        'quantity_received',
        'unit_price',
        'line_total',
        'status'
    ];

    protected $casts = [
        'quantity_ordered' => 'integer',
        'quantity_received' => 'integer',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    // Get outstanding quantity
    public function getOutstandingQuantity()
    {
        return $this->quantity_ordered - ($this->quantity_received ?? 0);
    }

    // Update received quantity
    public function updateReceivedQuantity($quantity)
    {
        $this->quantity_received = ($this->quantity_received ?? 0) + $quantity;
        
        // Update status based on received quantity
        if ($this->quantity_received >= $this->quantity_ordered) {
            $this->status = 'fully_received';
        } elseif ($this->quantity_received > 0) {
            $this->status = 'partially_received';
        } else {
            $this->status = 'pending';
        }
        
        $this->save();
        
        Log::info("Purchase order item updated", [
            'po_item_id' => $this->id,
            'quantity_received' => $this->quantity_received,
            'quantity_ordered' => $this->quantity_ordered,
            'status' => $this->status
        ]);
        
        // Update the main PO status
        $this->purchaseOrder->updateStatusBasedOnItems();
    }
}
