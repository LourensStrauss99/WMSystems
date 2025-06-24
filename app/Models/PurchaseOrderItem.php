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
        'item_description', // Changed from 'description'
        'item_category',
        'quantity_ordered',
        'quantity_received',
        'unit_price',
        'line_total',
        'unit_of_measure',
        'status',
        'inventory_id',
    ];

    protected $casts = [
        'quantity_ordered' => 'integer',
        'quantity_received' => 'integer',
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
     * Get remaining quantity to receive
     */
    public function getRemainingQuantityAttribute()
    {
        return $this->quantity_ordered - ($this->quantity_received ?? 0);
    }

    /**
     * Check if item is fully received
     */
    public function getIsFullyReceivedAttribute()
    {
        return $this->quantity_received >= $this->quantity_ordered;
    }

    /**
     * Get the outstanding quantity (calculated field)
     */
    public function getQuantityOutstandingAttribute()
    {
        return $this->quantity_ordered - $this->quantity_received;
    }
}
