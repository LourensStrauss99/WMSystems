<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_reference',
        'client_id',
        'invoice_jobcard_number',
        'amount',
        'payment_method',
        'payment_date',
        'reference_number',
        'notes',
        'status',
        'receipt_number'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2'
    ];

    /**
     * Relationship with Client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Generate a unique receipt number
     */
    public static function generateReceiptNumber()
    {
        $date = Carbon::now()->format('Ymd');
        $count = self::whereDate('created_at', Carbon::today())->count() + 1;
        return 'RCT-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique payment reference
     */
    public static function generatePaymentReference()
    {
        $date = Carbon::now()->format('Ymd');
        $count = self::whereDate('created_at', Carbon::today())->count() + 1;
        return 'PAY-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}