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
        return $this->quantity_received - ($this->quantity_rejected ?? 0) - ($this->quantity_damaged ?? 0);
    }

    // Update inventory stock when item is received
    public function updateInventoryStock()
    {
        Log::info("updateInventoryStock called", [
            'grv_item_id' => $this->id,
            'inventory_id' => $this->inventory_id,
            'stock_updated' => $this->stock_updated,
            'accepted_quantity' => $this->getAcceptedQuantity()
        ]);

        if ($this->inventory_id && !$this->stock_updated && $this->getAcceptedQuantity() > 0) {
            $inventory = $this->inventory;
            if ($inventory) {
                Log::info("Updating inventory stock", [
                    'grv_item_id' => $this->id,
                    'inventory_id' => $this->inventory_id,
                    'current_stock' => $inventory->stock_level,
                    'adding_quantity' => $this->getAcceptedQuantity(),
                    'new_stock_will_be' => $inventory->stock_level + $this->getAcceptedQuantity()
                ]);
                
                // Update inventory stock directly
                $oldStock = $inventory->stock_level;
                $inventory->stock_level += $this->getAcceptedQuantity();
                $inventory->quantity = $inventory->stock_level; // Update quantity field too
                $inventory->last_stock_update = now();
                $inventory->stock_added = $this->getAcceptedQuantity();
                $inventory->stock_update_reason = "Stock received via GRV: {$this->grv->grv_number}";
                
                // Update purchase information
                $inventory->goods_received_voucher = $this->grv->grv_number;
                $inventory->purchase_date = $this->grv->received_date;
                $inventory->purchase_notes = "Received via GRV {$this->grv->grv_number}";
                
                // Save inventory
                if ($inventory->save()) {
                    Log::info("Inventory saved successfully", [
                        'inventory_id' => $inventory->id,
                        'old_stock' => $oldStock,
                        'new_stock' => $inventory->fresh()->stock_level, // Fresh from DB
                        'quantity_added' => $this->getAcceptedQuantity()
                    ]);
                    
                    // Update PO item received quantity
                    $poItem = $this->purchaseOrderItem;
                    if ($poItem) {
                        $poItem->quantity_received = ($poItem->quantity_received ?? 0) + $this->getAcceptedQuantity();
                        $poItem->save();
                        
                        Log::info("PO item updated", [
                            'po_item_id' => $poItem->id,
                            'new_quantity_received' => $poItem->quantity_received
                        ]);
                    }
                    
                    // Mark this item as stock updated
                    $this->stock_updated = true;
                    $this->save();
                    
                    Log::info("GRV item marked as stock updated", [
                        'grv_item_id' => $this->id,
                        'stock_updated' => $this->fresh()->stock_updated
                    ]);
                    
                    return true;
                } else {
                    Log::error("Failed to save inventory", [
                        'inventory_id' => $inventory->id,
                        'validation_errors' => $inventory->getErrors() ?? 'No validation errors'
                    ]);
                    return false;
                }
            } else {
                Log::error("Inventory not found", [
                    'inventory_id' => $this->inventory_id,
                    'grv_item_id' => $this->id
                ]);
                return false;
            }
        } else {
            Log::info("Skipping inventory update", [
                'grv_item_id' => $this->id,
                'inventory_id' => $this->inventory_id,
                'stock_updated' => $this->stock_updated,
                'accepted_quantity' => $this->getAcceptedQuantity(),
                'reason' => !$this->inventory_id ? 'No inventory_id' : 
                           ($this->stock_updated ? 'Already updated' : 
                           ($this->getAcceptedQuantity() <= 0 ? 'No quantity to add' : 'Unknown'))
            ]);
            return false;
        }
    }
}
