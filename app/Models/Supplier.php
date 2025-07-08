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
        'country',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => true,
    ];

    // Relationships
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(Inventory::class, 'supplier', 'name');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
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

    // Static Methods
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
        $array['status_text'] = $this->status ? 'Active' : 'Inactive';
        $array['total_purchase_orders'] = $this->getTotalPurchaseOrders();
        $array['total_purchase_value'] = $this->getTotalPurchaseValue();
        $array['active_purchase_orders'] = $this->getActivePurchaseOrders();
        
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
            'country' => 'nullable|string|max:100',
            'status' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }

    public static function validationMessages()
    {
        return [
            'name.required' => 'Supplier name is required.',
            'name.unique' => 'A supplier with this name already exists.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered to another supplier.',
        ];
    }
}
