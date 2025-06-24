<?php
// Create model: php artisan make:model PurchaseOrder

// filepath: app/Models/PurchaseOrder.php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number', 'supplier_id', 'supplier_name', 'order_date', 'expected_delivery_date',
        'status', 'total_amount', 'vat_amount', 'grand_total',
        'notes', 'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'total_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    // Generate unique PO number
    public static function generatePoNumber()
    {
        $year = now()->year;
        $latest = self::where('po_number', 'like', "PO-{$year}-%")->latest()->first();
        
        if ($latest) {
            $lastNumber = (int) substr($latest->po_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return "PO-{$year}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get the supplier for this purchase order
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the items for this purchase order
     */
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Get the user who created this purchase order
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'draft' => 'secondary',
            'sent' => 'primary',
            'confirmed' => 'info',
            'partially_received' => 'warning',
            'fully_received' => 'success',
            'cancelled' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }
}
