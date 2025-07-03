<?php
// Create model: php artisan make:model GrvItem

// filepath: app/Models/GrvItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class GrvItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'grv_id',
        'purchase_order_item_id',
        'inventory_id',
        'quantity_ordered',
        'quantity_received',
        'quantity_rejected',
        'quantity_damaged',
        'condition',
        'item_notes',
        'rejection_reason',
        'storage_location',
        'batch_number',
        'expiry_date',
        'stock_updated'
    ];

    protected $casts = [
        'quantity_ordered' => 'integer',
        'quantity_received' => 'integer',
        'quantity_rejected' => 'integer',
        'quantity_damaged' => 'integer',
        'stock_updated' => 'boolean',
        'expiry_date' => 'date',
    ];

    // Relationships
    public function grv()
    {
        return $this->belongsTo(GoodsReceivedVoucher::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    // Get accepted quantity (received - rejected - damaged)
    public function getAcceptedQuantity()
    {
        return $this->quantity_received - $this->quantity_rejected - $this->quantity_damaged;
    }

    // Update inventory stock when item is received
    public function updateInventoryStock()
    {
        if ($this->inventory_id && !$this->stock_updated && $this->getAcceptedQuantity() > 0) {
            $inventory = $this->inventory;
            if ($inventory) {
                // Prepare purchase data for inventory update
                $purchaseData = [
                    'goods_received_voucher' => $this->grv->grv_number,
                    'purchase_date' => $this->grv->received_date,
                    'purchase_notes' => "Received via GRV {$this->grv->grv_number}, PO {$this->grv->purchaseOrder->po_number}",
                ];
                
                // Add batch number if available
                if ($this->batch_number) {
                    $purchaseData['batch_number'] = $this->batch_number;
                }
                
                // Add stock to inventory
                $result = $inventory->addStock(
                    $this->getAcceptedQuantity(),
                    "Stock received via GRV: {$this->grv->grv_number}",
                    $purchaseData
                );
                
                // Update purchase order item
                $this->purchaseOrderItem->updateReceivedQuantity($this->getAcceptedQuantity());
                
                // Mark as stock updated
                $this->stock_updated = true;
                $this->save();
                
                Log::info("Inventory updated from GRV", [
                    'grv_number' => $this->grv->grv_number,
                    'inventory_id' => $this->inventory_id,
                    'item_name' => $inventory->name,
                    'quantity_added' => $this->getAcceptedQuantity(),
                    'result' => $result
                ]);
                
                return $result;
            }
        }
        return false;
    }
}
