<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'client_id',
        'jobcard_id',
        'amount',
        'invoice_date',
        'due_date',
        'status',
        'payment_date',
        'paid_amount',
        'outstanding_amount'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2'
    ];

    /**
     * Get total payments made against this invoice
     */
    public function getTotalPaid()
    {
        return $this->payments()->sum('amount');
    }

    /**
     * Get outstanding amount
     */
    public function getOutstandingAmount()
    {
        return $this->amount - $this->getTotalPaid();
    }

    /**
     * Update payment status based on payments
     */
    public function updatePaymentStatus()
    {
        $totalPaid = $this->getTotalPaid();
        $outstanding = $this->amount - $totalPaid;

        if ($totalPaid == 0) {
            $status = 'unpaid';
        } elseif ($outstanding <= 0) {
            $status = 'paid';
        } else {
            $status = 'partial';
        }

        $this->update([
            'paid_amount' => $totalPaid,
            'outstanding_amount' => max(0, $outstanding),
            'status' => $status,
            'payment_date' => $status === 'paid' ? now() : null
        ]);

        return $this;
    }

    /**
     * Get payment age in days
     */
    public function getPaymentAge()
    {
        if ($this->status === 'paid') return 0;
        
        $dueDate = $this->due_date ?: $this->invoice_date->addDays(30);
        return Carbon::now()->diffInDays($dueDate, false);
    }

    /**
     * Get age category
     */
    public function getAgeCategory()
    {
        if ($this->status === 'paid') return 'paid';
        
        $age = $this->getPaymentAge();
        
        if ($age > 0) return 'current';
        if ($age >= -30) return '30_days';
        if ($age >= -60) return '60_days';
        if ($age >= -90) return '90_days';
        return '120_days';
    }

    /**
     * Relationships
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function jobcard()
    {
        return $this->belongsTo(Jobcard::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_jobcard_number', 'invoice_number');
    }

    public static function generateInvoiceNumber()
    {
        $date = now()->format('Ymd');
        $lastInvoice = self::whereDate('created_at', now()->toDateString())->orderBy('id', 'desc')->first();
        $nextNumber = $lastInvoice ? ((int)substr($lastInvoice->invoice_number, -4)) + 1 : 1;
        return 'INV-' . $date . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}