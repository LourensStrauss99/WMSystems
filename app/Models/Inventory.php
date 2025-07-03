<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'description',
        'name',
        'code', // Add this
        'item_code', // Add this  
        'short_code',
        'short_description',
        'vendor',
        'supplier',
        'goods_received_voucher',
        'invoice_number',
        'receipt_number',
        'purchase_date',
        'purchase_order_number',
        'purchase_notes',
        'last_stock_update',
        'stock_added',
        'stock_update_reason',
        'nett_price',
        'buying_price',
        'sell_price',
        'selling_price',
        'unit_price', // Add this
        'price', // Add this
        'quantity',
        'stock_level',
        'min_quantity',
        'min_level',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'last_stock_update' => 'datetime',
        'nett_price' => 'decimal:2',
        'buying_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'quantity' => 'integer',
        'stock_level' => 'integer',
        'min_quantity' => 'integer',
        'min_level' => 'integer',
        'stock_added' => 'integer',
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

    // Update your existing addStock method (around line 143)
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
        
        Log::info("Stock added to inventory", [
            'inventory_id' => $this->id,
            'item_name' => $this->name,
            'old_stock' => $oldStock,
            'quantity_added' => $quantity,
            'new_stock' => $this->stock_level,
            'reason' => $reason,
            'purchase_data' => $purchaseData
        ]);
        
        return [
            'old_stock' => $oldStock,
            'new_stock' => $this->stock_level,
            'added' => $quantity
        ];
    }

    // Add these relationships if not already present
    public function purchaseOrderItems()
    {
        return $this->hasMany(\App\Models\PurchaseOrderItem::class);
    }

    public function grvItems()
    {
        return $this->hasMany(\App\Models\GrvItem::class);
    }
}