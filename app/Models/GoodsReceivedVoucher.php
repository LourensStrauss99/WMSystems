<?php
// Create model: php artisan make:model GoodsReceivedVoucher

// filepath: app/Models/GoodsReceivedVoucher.php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceivedVoucher extends Model
{
    protected $fillable = [
        'grv_number', 'purchase_order_id', 'received_date', 'received_time',
        'received_by', 'checked_by', 'delivery_note_number', 'vehicle_registration',
        'driver_name', 'delivery_company', 'overall_status', 'general_notes',
        'discrepancies', 'quality_check_passed', 'quality_notes',
        'delivery_note_received', 'invoice_received', 'photos'
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
        $latest = self::where('grv_number', 'like', "GRV-{$year}-%")->latest()->first();
        
        if ($latest) {
            $lastNumber = (int) substr($latest->grv_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return "GRV-{$year}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
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
}
