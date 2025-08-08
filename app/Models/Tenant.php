<?php
namespace App\Models;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id', 'name', 'slug', 'database_name', 'domain', 'data',
        'owner_name', 'owner_email', 'owner_phone', 'owner_password',
        'address', 'city', 'country', 'status', 'is_active',
        'email_verified_at', 'verification_token', 'payment_status',
        'monthly_fee', 'next_payment_due', 'last_payment_date',
        'subscription_plan', 'subscription_expires_at', 'settings'
    ];

    protected $casts = [
        'data' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'monthly_fee' => 'decimal:2',
        'email_verified_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'last_payment_date' => 'datetime',
        'next_payment_due' => 'date',
    ];

    protected $hidden = [
        'owner_password', 'verification_token'
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id', 'name', 'slug', 'database_name', 'domain', 'data',
            'owner_name', 'owner_email', 'owner_phone', 'owner_password',
            'address', 'city', 'country', 'status', 'is_active',
            'email_verified_at', 'verification_token', 'payment_status',
            'monthly_fee', 'next_payment_due', 'last_payment_date',
            'subscription_plan', 'subscription_expires_at', 'settings'
        ];
    }

    // Hash password when setting
    public function setOwnerPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['owner_password'] = Hash::make($value);
        }
    }

    // Check if tenant is active and payments are up to date
    public function isActiveWithPayments()
    {
        return $this->is_active && 
               $this->payment_status === 'active' && 
               ($this->next_payment_due === null || $this->next_payment_due >= now()->toDateString());
    }

    // Generate unique slug from name
    public static function generateSlug($name)
    {
        $slug = Str::slug($name);
        $count = static::where('slug', 'like', $slug . '%')->count();
        return $count > 0 ? $slug . '-' . ($count + 1) : $slug;
    }
}