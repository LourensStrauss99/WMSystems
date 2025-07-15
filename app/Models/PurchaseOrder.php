<?php
// Create model: php artisan make:model PurchaseOrder

// filepath: app/Models/PurchaseOrder.php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'status',
        'supplier_name',
        'supplier_contact',
        'supplier_email',
        'supplier_phone',
        'supplier_address',
        'order_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'total_amount',          // Use this instead of subtotal
        'vat_amount',
        'grand_total',
        'created_by',
        'approved_by',
        'approved_at',
        'notes',
        'terms_conditions',
        'payment_terms',
        'supplier_id',
        'submitted_for_approval_at',
        'submitted_by',
        'rejected_at',
        'rejection_reason',
        'rejection_history',
        'rejected_by',
        'sent_at',
        'sent_by',
        'amended_by',
        'amended_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
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

    /**
     * Update PO status based on item statuses
     */
    public function updateStatusBasedOnItems()
    {
        $totalOrdered = $this->items->sum('quantity_ordered');
        $totalReceived = $this->items->sum('quantity_received');
        
        Log::info("Updating PO status", [
            'po_id' => $this->id,
            'po_number' => $this->po_number,
            'total_ordered' => $totalOrdered,
            'total_received' => $totalReceived,
            'current_status' => $this->status
        ]);
        
        $newStatus = $this->status; // Keep current status as default
        
        if ($totalReceived >= $totalOrdered && $totalOrdered > 0) {
            $newStatus = 'fully_received';
        } elseif ($totalReceived > 0) {
            $newStatus = 'partially_received';
        }
        
        if ($newStatus !== $this->status) {
            $this->update(['status' => $newStatus]);
            Log::info("PO status updated", [
                'po_id' => $this->id,
                'old_status' => $this->status,
                'new_status' => $newStatus
            ]);
        }
    }

    /**
     * Get total received quantity across all items
     */
    public function getTotalReceivedQuantity()
    {
        return $this->items->sum('quantity_received');
    }

    /**
     * Get total ordered quantity across all items
     */
    public function getTotalOrderedQuantity()
    {
        return $this->items->sum('quantity_ordered');
    }

    /**
     * Check if PO is fully received
     */
    public function isFullyReceived()
    {
        return $this->getTotalReceivedQuantity() >= $this->getTotalOrderedQuantity();
    }

    /**
     * Check if PO can create GRV
     */
    public function canCreateGrv()
    {
        return in_array($this->status, ['approved', 'sent', 'partially_received']) && 
               $this->getTotalReceivedQuantity() < $this->getTotalOrderedQuantity();
    }

    /**
     * Get GRVs for this PO
     */
    public function grvs()
    {
        return $this->hasMany(GoodsReceivedVoucher::class);
    }

    /**
     * Get the user who created this purchase order
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this PO
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Add these helper methods for backwards compatibility:
    public function getSubtotalAttribute()
    {
        return $this->total_amount;  // Alias for subtotal
    }

    public function getVatPercentAttribute()
    {
        // Calculate VAT percentage from amounts
        if ($this->total_amount > 0) {
            return ($this->vat_amount / $this->total_amount) * 100;
        }
        return 15; // Default VAT percent
    }

    public function calculateTotals()
    {
        $subtotal = $this->items->sum(function($item) {
            return $item->quantity_ordered * $item->unit_price;
        });
        
        $companyDetails = \App\Models\CompanyDetail::first();
        $vatPercent = $companyDetails ? $companyDetails->vat_percent : 15;
        $vatAmount = $subtotal * ($vatPercent / 100);
        $grandTotal = $subtotal + $vatAmount;
        
        $this->update([
            'total_amount' => $subtotal,
            'vat_amount' => $vatAmount,
            'grand_total' => $grandTotal,
        ]);
        
        return $this;
    }
}
