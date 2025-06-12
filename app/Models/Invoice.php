<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'client_id',
        'jobcard_id', // <-- add this
        'invoice_number',
        'amount',
        'invoice_date',
        'payment_date',
        'status',
        'created_at', // optional, for mass assignment
        'updated_at', // optional, for mass assignment
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function jobcard()
    {
        return $this->belongsTo(Jobcard::class, 'jobcard_id');
    }

    public static function generateInvoiceNumber()
    {
        $date = now()->format('Ymd');
        $lastInvoice = self::whereDate('created_at', now()->toDateString())->orderBy('id', 'desc')->first();
        $nextNumber = $lastInvoice ? ((int)substr($lastInvoice->invoice_number, -4)) + 1 : 1;
        return 'INV-' . $date . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}