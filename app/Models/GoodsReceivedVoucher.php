<?php
// Create model: php artisan make:model GoodsReceivedVoucher

// filepath: app/Models/GoodsReceivedVoucher.php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceivedVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'grv_number',
        'purchase_order_id',
        'received_date',
        'received_time',
        'received_by',
        'checked_by',
        'delivery_note_number',
        'vehicle_registration',
        'driver_name',
        'delivery_company',
        'overall_status',
        'general_notes',
        'discrepancies',
        'quality_check_passed',
        'quality_notes',
        'delivery_note_received',
        'invoice_received',
        'photos'
    ];

    protected $casts = [
        'received_date' => 'date',
        'received_time' => 'datetime:H:i',
        'quality_check_passed' => 'boolean',
        'delivery_note_received' => 'boolean',
        'invoice_received' => 'boolean',
        'photos' => 'array',
    ];

    // Generate unique GRV number
    public static function generateGrvNumber()
    {
        $year = now()->year;
        $month = now()->format('m');
        
        $latest = self::where('grv_number', 'like', "GRV-{$year}{$month}-%")
            ->orderBy('grv_number', 'desc')
            ->first();
        
        if ($latest) {
            $lastSequence = (int) substr($latest->grv_number, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }
        
        return sprintf('GRV-%s%s-%04d', $year, $month, $newSequence);
    }

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items()
    {
        return $this->hasMany(GrvItem::class, 'grv_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    // Check if GRV can be approved
    public function canBeApproved()
    {
        // Can be approved if not already checked and has items
        return !$this->checked_by && $this->items->count() > 0;
    }

    // Update purchase order status based on received quantities
    public function updatePurchaseOrderStatus()
    {
        $po = $this->purchaseOrder;
        $allItemsReceived = true;
        
        foreach ($po->items as $poItem) {
            $totalReceived = $this->items->where('purchase_order_item_id', $poItem->id)->sum('quantity_received');
            
            if ($totalReceived < $poItem->quantity_ordered) {
                $allItemsReceived = false;
            }
        }
        
        if ($allItemsReceived) {
            $po->update(['status' => 'fully_received']);
        } else {
            $po->update(['status' => 'partially_received']);
        }
    }

    // Calculate completion percentage
    public function getCompletionPercentage()
    {
        $totalOrdered = $this->items->sum('quantity_ordered');
        $totalReceived = $this->items->sum('quantity_received');
        
        return $totalOrdered > 0 ? round(($totalReceived / $totalOrdered) * 100) : 0;
    }

    // Get overall quality status
    public function getQualityStatus()
    {
        if (!$this->quality_check_passed) {
            return 'failed';
        }
        
        $hasRejected = $this->items->where('quantity_rejected', '>', 0)->count() > 0;
        $hasDamaged = $this->items->where('quantity_damaged', '>', 0)->count() > 0;
        
        if ($hasRejected || $hasDamaged) {
            return 'partial';
        }
        
        return 'passed';
    }
}
