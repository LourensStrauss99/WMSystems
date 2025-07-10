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
        'active',
        'notes',
    ];

    protected $casts = [
        'active' => 'boolean',
        'credit_limit' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'active' => true,
        'credit_limit' => 0,
    ];

    // Add the missing purchaseOrders relationship
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    // Add inventory relationship (items supplied by this supplier)
    public function inventoryItems()
    {
        return $this->hasMany(Inventory::class, 'supplier_id');
    }

    // Add status badge accessor
    public function getStatusBadgeAttribute()
    {
        return $this->active 
            ? '<span class="badge bg-success">Active</span>' 
            : '<span class="badge bg-danger">Inactive</span>';
    }

    // Add payment terms text accessor
    public function getPaymentTermsTextAttribute()
    {
        return self::getPaymentTermsOptions()[$this->payment_terms] ?? $this->payment_terms;
    }

    // Add formatted credit limit accessor
    public function getFormattedCreditLimitAttribute()
    {
        return 'R' . number_format($this->credit_limit, 2);
    }

    // Payment terms options
    public static function getPaymentTermsOptions()
    {
        return [
            'cash' => 'Cash on Delivery',
            '30_days' => '30 Days',
            '60_days' => '60 Days',
            '90_days' => '90 Days',
        ];
    }

    // Validation rules
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
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'required|in:cash,30_days,60_days,90_days',
            'active' => 'boolean',
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
            'payment_terms.required' => 'Payment terms are required.',
            'payment_terms.in' => 'Please select valid payment terms.',
        ];
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

    public function scopeByPaymentTerms($query, $terms)
    {
        return $query->where('payment_terms', $terms);
    }
}
