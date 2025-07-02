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
        'po_number',
        'supplier_id',
        'supplier_name',
        'supplier_contact',
        'supplier_email',
        'supplier_phone',
        'supplier_address',
        'order_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'total_amount',
        'vat_amount',
        'grand_total',
        'notes',
        'terms_conditions',
        'payment_terms',
        'status',
        'submitted_for_approval_at',
        'submitted_by',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'sent_at',
        'sent_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'submitted_for_approval_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'sent_at' => 'datetime',
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
     * Get the user who submitted this PO for approval
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user who approved this PO
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who created this purchase order
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Add calculated attributes for totals
    public function getTotalAmountAttribute()
    {
        return $this->items->sum('line_total');
    }

    public function getVatAmountAttribute()
    {
        return $this->total_amount * 0.15;
    }

    public function getGrandTotalAttribute()
    {
        return $this->total_amount + $this->vat_amount;
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
