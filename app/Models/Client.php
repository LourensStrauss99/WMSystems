<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'surname', 
        'email',
        'telephone',
        'address',
        'notes',
        'payment_reference'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Generate payment reference when customer is created
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($client) {
            if (!$client->payment_reference) {
                $client->payment_reference = $client->generatePaymentReference();
            }
        });
    }

    /**
     * Generate a unique payment reference
     * Format: First 3 letters of surname + 5 random digits
     * Example: STR12345 (for surname "strauss")
     */
    public function generatePaymentReference()
    {
        if ($this->payment_reference) {
            return $this->payment_reference;
        }

        $surname = strtoupper(substr($this->surname ?: $this->name, 0, 3));
        $random = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
        
        $reference = $surname . $random;
        
        // Ensure uniqueness
        while (self::where('payment_reference', $reference)->exists()) {
            $random = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
            $reference = $surname . $random;
        }
        
        $this->update(['payment_reference' => $reference]);
        return $reference;
    }

    /**
     * Regenerate payment reference
     */
    public function regeneratePaymentReference()
    {
        $this->payment_reference = $this->generatePaymentReference();
        $this->save();
        return $this->payment_reference;
    }

    /**
     * Relationship with payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}