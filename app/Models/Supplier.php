<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'vat_number',
        'account_number',
        'credit_limit',
        'payment_terms',
        'active'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'active' => true,
        'credit_limit' => 0.00,
        'payment_terms' => '30_days',
    ];

    // Relationships
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(Inventory::class, 'supplier_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }

    public function scopeByPaymentTerms($query, $terms)
    {
        return $query->where('payment_terms', $terms);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('contact_person', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%");
        });
    }

    // Accessors & Mutators
    public function getFullAddressAttribute()
    {
        $address = $this->address;
        if ($this->city) {
            $address .= "\n" . $this->city;
        }
        if ($this->postal_code) {
            $address .= " " . $this->postal_code;
        }
        return $address;
    }

    public function getFormattedCreditLimitAttribute()
    {
        return 'R ' . number_format((float) $this->credit_limit, 2);
    }

    public function getPaymentTermsTextAttribute()
    {
        return match($this->payment_terms) {
            'cash' => 'Cash on Delivery',
            '30_days' => '30 Days',
            '60_days' => '60 Days',
            '90_days' => '90 Days',
            default => 'Unknown'
        };
    }

    public function getStatusTextAttribute()
    {
        return $this->active ? 'Active' : 'Inactive';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->active 
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
    }

    // Business Logic Methods
    public function getTotalPurchaseOrders()
    {
        return $this->purchaseOrders()->count();
    }

    public function getTotalPurchaseValue()
    {
        return $this->purchaseOrders()->sum('grand_total');
    }

    public function getActivePurchaseOrders()
    {
        return $this->purchaseOrders()
            ->whereIn('status', ['draft', 'sent', 'confirmed', 'partially_received'])
            ->count();
    }

    public function getPendingOrderValue()
    {
        return $this->purchaseOrders()
            ->whereIn('status', ['draft', 'sent', 'confirmed', 'partially_received'])
            ->sum('grand_total');
    }

    public function getLastOrderDate()
    {
        $lastOrder = $this->purchaseOrders()
            ->orderBy('order_date', 'desc')
            ->first();
        
        return $lastOrder ? $lastOrder->order_date : null;
    }

    public function getAverageOrderValue()
    {
        $totalOrders = $this->purchaseOrders()->count();
        if ($totalOrders === 0) {
            return 0;
        }
        
        return $this->getTotalPurchaseValue() / $totalOrders;
    }

    public function hasActiveOrders()
    {
        return $this->getActivePurchaseOrders() > 0;
    }

    public function canBeDeleted()
    {
        // Can't delete if has purchase orders or inventory items
        return $this->purchaseOrders()->count() === 0 && 
               $this->inventoryItems()->count() === 0;
    }

    public function isWithinCreditLimit($amount)
    {
        if ($this->credit_limit <= 0) {
            return true; // No credit limit set
        }
        
        $pendingValue = $this->getPendingOrderValue();
        return ($pendingValue + $amount) <= $this->credit_limit;
    }

    public function getRemainingCredit()
    {
        if ($this->credit_limit <= 0) {
            return null; // No credit limit
        }
        
        return max(0, $this->credit_limit - $this->getPendingOrderValue());
    }

    // Static Methods
    public static function getPaymentTermsOptions()
    {
        return [
            'cash' => 'Cash on Delivery',
            '30_days' => '30 Days',
            '60_days' => '60 Days',
            '90_days' => '90 Days'
        ];
    }

    public static function getTopSuppliers($limit = 5)
    {
        return static::withCount('purchaseOrders')
            ->having('purchase_orders_count', '>', 0)
            ->orderBy('purchase_orders_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getActiveSuppliers()
    {
        return static::active()->orderBy('name')->get();
    }

    // Model Events
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($supplier) {
            // Ensure name is properly formatted
            $supplier->name = ucwords(strtolower($supplier->name));
            
            // Format contact person name
            if ($supplier->contact_person) {
                $supplier->contact_person = ucwords(strtolower($supplier->contact_person));
            }
            
            // Ensure email is lowercase
            if ($supplier->email) {
                $supplier->email = strtolower($supplier->email);
            }
        });
        
        static::updating(function ($supplier) {
            // Same formatting rules for updates
            $supplier->name = ucwords(strtolower($supplier->name));
            
            if ($supplier->contact_person) {
                $supplier->contact_person = ucwords(strtolower($supplier->contact_person));
            }
            
            if ($supplier->email) {
                $supplier->email = strtolower($supplier->email);
            }
        });
        
        static::deleting(function ($supplier) {
            // Prevent deletion if has related records
            if (!$supplier->canBeDeleted()) {
                throw new \Exception('Cannot delete supplier with existing purchase orders or inventory items.');
            }
        });
    }

    // JSON Serialization
    public function toArray()
    {
        $array = parent::toArray();
        
        // Add computed attributes
        $array['full_address'] = $this->full_address;
        $array['formatted_credit_limit'] = $this->formatted_credit_limit;
        $array['payment_terms_text'] = $this->payment_terms_text;
        $array['status_text'] = $this->status_text;
        $array['total_purchase_orders'] = $this->getTotalPurchaseOrders();
        $array['total_purchase_value'] = $this->getTotalPurchaseValue();
        $array['active_purchase_orders'] = $this->getActivePurchaseOrders();
        $array['remaining_credit'] = $this->getRemainingCredit();
        
        return $array;
    }

    // String Representation
    public function __toString()
    {
        return $this->name;
    }

    // Validation Rules (for use in controllers)
    public static function validationRules($id = null)
    {
        return [
            'name' => 'required|string|max:255|unique:suppliers,name' . ($id ? ",{$id}" : ''),
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email' . ($id ? ",{$id}" : ''),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'vat_number' => 'nullable|string|max:50',
            'account_number' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0|max:999999999.99',
            'payment_terms' => 'required|in:cash,30_days,60_days,90_days',
            'active' => 'boolean'
        ];
    }

    public static function validationMessages()
    {
        return [
            'name.required' => 'Supplier name is required.',
            'name.unique' => 'A supplier with this name already exists.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered to another supplier.',
            'credit_limit.numeric' => 'Credit limit must be a valid number.',
            'credit_limit.min' => 'Credit limit cannot be negative.',
            'payment_terms.required' => 'Payment terms are required.',
            'payment_terms.in' => 'Please select valid payment terms.'
        ];
    }
}
