<?php
// Create model: php artisan make:model GrvItem

// filepath: app/Models/GrvItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrvItem extends Model
{
    protected $fillable = [
        'grv_id', 'purchase_order_item_id', 'quantity_ordered', 'quantity_received',
        'quantity_rejected', 'quantity_damaged', 'condition', 'item_notes',
        'rejection_reason', 'storage_location', 'batch_number', 'expiry_date',
        'inventory_id', 'stock_updated'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'stock_updated' => 'boolean',
        'quantity_ordered' => 'integer',
        'quantity_received' => 'integer',
        'quantity_rejected' => 'integer',
        'quantity_damaged' => 'integer',
    ];

    // Relationships
    public function grv()
    {
        return $this->belongsTo(GoodsReceivedVoucher::class, 'grv_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    // Update inventory stock when item is received
    public function updateInventoryStock()
    {
        if ($this->inventory_id && !$this->stock_updated && $this->quantity_received > 0) {
            $inventory = $this->inventory;
            if ($inventory) {
                $inventory->addStock(
                    $this->quantity_received,
                    "Stock received via GRV: {$this->grv->grv_number}",
                    [
                        'purchase_date' => $this->grv->received_date,
                        'purchase_notes' => "Received via GRV {$this->grv->grv_number}, PO {$this->grv->purchaseOrder->po_number}"
                    ]
                );
                
                $this->stock_updated = true;
                $this->save();
            }
        }
    }
}
