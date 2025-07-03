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
        'supplier_name',
        'supplier_contact',
        'supplier_email',
        'supplier_phone',
        'supplier_address',
        'actual_delivery_date',
        'approved_by',
        'approved_at',
        'terms_conditions',
        'payment_terms',
        'supplier_id',
        'vat_amount',
        'grand_total',
        'status',
        'order_date',
        'submitted_for_approval_at',
        'submitted_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'rejection_history',
        'sent_at',
        'sent_by',
        'amended_by',
        'amended_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'actual_delivery_date' => 'date',
        'submitted_for_approval_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'sent_at' => 'datetime',
        'amended_at' => 'datetime',
        'rejection_history' => 'array',
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

    /**
     * Get the user who last amended this PO
     */
    public function amendedBy()
    {
        return $this->belongsTo(User::class, 'amended_by');
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

    // Add method to calculate subtotal
    public function calculateSubtotal()
    {
        return $this->items->sum('line_total');
    }

    public function calculateVat()
    {
        return $this->calculateSubtotal() * 0.15;
    }

    public function calculateGrandTotal()
    {
        return $this->calculateSubtotal() + $this->calculateVat();
    }

    /**
     * Add rejection to history
     */
    public function addRejectionToHistory($reason, $rejectedBy)
    {
        $history = $this->rejection_history ?? [];
        
        $history[] = [
            'reason' => $reason,
            'rejected_by' => $rejectedBy,
            'rejected_at' => now()->toDateTimeString(),
            'po_version' => count($history) + 1,
        ];
        
        $this->rejection_history = $history;
        $this->save();
    }

    /**
     * Get latest rejection reason - BETTER APPROACH
     */
    public function getLatestRejectionReason()
    {
        if (empty($this->rejection_history)) {
            return $this->rejection_reason;
        }
        
        // Get the last element by index instead of using end()
        $history = $this->rejection_history;
        $lastIndex = count($history) - 1;
        
        return $history[$lastIndex]['reason'] ?? $this->rejection_reason;
    }

    /**
     * Get rejection count
     */
    public function getRejectionCount()
    {
        return count($this->rejection_history ?? []);
    }

    /**
     * Get all rejection history
     */
    public function getRejectionHistory()
    {
        return $this->rejection_history ?? [];
    }
}
