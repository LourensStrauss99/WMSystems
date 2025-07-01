<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'name',
        'description',
        'short_description',
        'short_code',
        'vendor',
        'supplier',
        'goods_received_voucher',
        'invoice_number',        // New
        'receipt_number',        // New
        'purchase_date',         // New
        'purchase_order_number', // New
        'purchase_notes',        // New
        'last_stock_update',     // New
        'stock_added',           // New
        'stock_update_reason',   // New
        'nett_price',
        'buying_price',
        'sell_price',
        'selling_price',
        'quantity',
        'stock_level',
        'min_quantity',
        'min_level',
    ];

    protected $casts = [
        'buying_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'nett_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'stock_level' => 'integer',
        'quantity' => 'integer',
        'min_level' => 'integer',
        'min_quantity' => 'integer',
        'stock_added' => 'integer',
        'purchase_date' => 'date',
        'last_stock_update' => 'date',
    ];

    /**
     * Check if inventory is at or below minimum level
     */
    public function isAtMinLevel()
    {
        return $this->stock_level <= $this->min_level;
    }

    /**
     * Check if inventory is critically low (50% below min level)
     */
    public function isCriticallyLow()
    {
        return $this->stock_level < ($this->min_level * 0.5);
    }

    /**
     * Check if inventory is out of stock
     */
    public function isOutOfStock()
    {
        return $this->stock_level == 0;
    }

    /**
     * Check if requested quantity is available
     */
    public function hasStock($requestedQuantity)
    {
        return $this->stock_level >= $requestedQuantity;
    }

    /**
     * Get available stock after considering requested quantity
     */
    public function getAvailableStock($requestedQuantity = 0)
    {
        return max(0, $this->stock_level - $requestedQuantity);
    }

    /**
     * Get stock status with color coding
     */
    public function getStockStatus()
    {
        if ($this->stock_level == 0) {
            return [
                'status' => 'Out of Stock',
                'icon' => '❌',
                'class' => 'bg-dark'
            ];
        } elseif ($this->isCriticallyLow()) {
            return [
                'status' => 'Critical',
                'icon' => '🚨',
                'class' => 'bg-danger'
            ];
        } elseif ($this->isAtMinLevel()) {
            return [
                'status' => 'Low Stock',
                'icon' => '⚠️',
                'class' => 'bg-warning'
            ];
        } else {
            return [
                'status' => 'In Stock',
                'icon' => '✅',
                'class' => 'bg-success'
            ];
        }
    }

    /**
     * Reduce stock level (when used in jobcard)
     */
    public function reduceStock($quantity)
    {
        if ($this->hasStock($quantity)) {
            $this->stock_level -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Get jobcards using this inventory item
     */
    public function jobcards()
    {
        return $this->belongsToMany(Jobcard::class)
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    // Add this method to track stock additions
    public function addStock($quantity, $reason = 'Stock replenishment', $purchaseData = [])
    {
        $oldStock = $this->stock_level;
        $this->stock_level += $quantity;
        $this->quantity = $this->stock_level;
        $this->stock_added = $quantity;
        $this->last_stock_update = now();
        $this->stock_update_reason = $reason;
        
        // Update purchase info if provided
        if (!empty($purchaseData)) {
            foreach ($purchaseData as $field => $value) {
                if (in_array($field, $this->fillable) && $value !== null) {
                    $this->{$field} = $value;
                }
            }
        }
        
        $this->save();
        
        return [
            'old_stock' => $oldStock,
            'new_stock' => $this->stock_level,
            'added' => $quantity
        ];
    }
}