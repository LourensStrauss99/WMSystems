<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LandlordPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'landlord_invoice_id',
        'payment_reference',
        'amount',
        'payment_method',
        'payment_date',
        'transaction_id',
        'status',
        'currency',
        'exchange_rate',
        'fees',
        'net_amount',
        'processor_response',
        'notes'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'fees' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'processor_response' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($payment) {
            if (empty($payment->payment_reference)) {
                $payment->payment_reference = self::generatePaymentReference();
            }
        });
    }

    public static function generatePaymentReference(): string
    {
        do {
            $reference = 'PAY-' . strtoupper(uniqid());
        } while (self::where('payment_reference', $reference)->exists());
        
        return $reference;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function landlordInvoice(): BelongsTo
    {
        return $this->belongsTo(LandlordInvoice::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format((float)$this->amount, 2);
    }

    public function getNetAmountAfterFeesAttribute(): float
    {
        return $this->amount - ($this->fees ?? 0);
    }
}
