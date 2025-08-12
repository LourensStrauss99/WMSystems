<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandlordInvoice extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; // Always use central database

    protected $fillable = [
        'invoice_number',
        'tenant_id',
        'amount',
        'tax_amount',
        'total_amount',
        'currency',
        'status',
        'invoice_date',
        'due_date',
        'paid_date',
        'billing_period',
        'description',
        'line_items',
    ];

    protected $casts = [
        'line_items' => 'array',
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
        'paid_date' => 'datetime',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LandlordPayment::class, 'invoice_id');
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date->lt(now());
    }

    public function generateInvoiceNumber(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $lastInvoice = static::where('invoice_number', 'like', "INV-{$year}{$month}%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "INV-{$year}{$month}{$newNumber}";
    }

    public function markAsPaid($paymentDate = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => $paymentDate ?? now(),
        ]);
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    public function getBalanceDueAttribute(): float
    {
        return $this->total_amount - $this->total_paid;
    }
}
