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
        // Core fields
        'description',
        'short_code',
        'vendor',
        
        // Pricing
        'nett_price',
        'sell_price',
        
        // Stock
        'quantity',
        'min_quantity',
        
        // Purchase tracking
        'invoice_number',
        'receipt_number',
        'purchase_date',
        'purchase_order_number',
        'purchase_notes',
        
        // Stock management
        'last_stock_update',
        'stock_added',
        'stock_update_reason',
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

    // Accessor to get the selling price (prioritize selling_price over sell_price)
    public function getSellingPriceAttribute($value)
    {
        return $value ?: $this->sell_price;
    }

    /**
     * Check if inventory is at or below minimum level
     */
    public function isAtMinLevel()
    {
        return $this->quantity <= $this->min_quantity;  // Changed from stock_level/min_level
    }

    /**
     * Check if inventory is critically low (50% below min level)
     */
    public function isCriticallyLow()
    {
        return $this->quantity < ($this->min_quantity * 0.5);  // Changed columns
    }

    /**
     * Check if inventory is out of stock
     */
    public function isOutOfStock()
    {
        return $this->quantity == 0;  // Changed from stock_level
    }

    /**
     * Check if requested quantity is available
     */
    public function hasStock($requestedQuantity)
    {
        return $this->quantity >= $requestedQuantity;  // Changed from stock_level
    }

    /**
     * Get available stock after considering requested quantity
     */
    public function getAvailableStock($requestedQuantity = 0)
    {
        return max(0, $this->quantity - $requestedQuantity);  // Changed from stock_level
    }

    /**
     * Get stock status with color coding
     */
    public function getStockStatus()
    {
        if ($this->quantity == 0) {
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
            $this->quantity -= $quantity;  // Changed from stock_level
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
        return $this->belongsToMany(\App\Models\Jobcard::class, 'inventory_jobcard')
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

    public function updateSellingPrice()
    {
        $companyDetails = \App\Models\CompanyDetail::first();
        $markupPercent = $companyDetails ? $companyDetails->markup_percentage : 25;
        
        $this->sell_price = $this->nett_price * (1 + ($markupPercent / 100));
        $this->save();
        
        return $this->sell_price;
    }

    public function getCalculatedSellingPrice()
    {
        $companyDetails = \App\Models\CompanyDetail::first();
        $markupPercent = $companyDetails ? $companyDetails->markup_percentage : 25;
        
        return $this->nett_price * (1 + ($markupPercent / 100));
    }
}