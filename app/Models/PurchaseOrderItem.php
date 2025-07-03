<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model 
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'item_name',
        'item_code',
        'item_description',
        'item_category',
        'quantity_ordered',
        'quantity_received',
        'quantity_outstanding',
        'unit_price',
        'line_total',
        'unit_of_measure',
        'status',
        'inventory_id',
    ];

    protected $casts = [
        'quantity_ordered' => 'decimal:3',
        'quantity_received' => 'decimal:3',
        'quantity_outstanding' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    /**
     * Get the purchase order for this item
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the inventory associated with the purchase order item
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
