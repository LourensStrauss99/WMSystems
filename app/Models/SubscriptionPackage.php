<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'yearly_price',
        'max_users',
        'storage_limit_mb',
        'features',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean'
    ];

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'subscription_plan', 'slug');
    }

    public function activeTenants(): HasMany
    {
        return $this->tenants()->where('is_active', true);
    }

    public function getFormattedMonthlyPriceAttribute(): string
    {
        return 'R ' . number_format((float)$this->monthly_price, 2);
    }

    public function getFormattedYearlyPriceAttribute(): string
    {
        return 'R ' . number_format((float)$this->yearly_price, 2);
    }

    public function getStorageInGbAttribute(): float
    {
        return $this->storage_limit_mb / 1024;
    }

    public function getFormattedStorageAttribute(): string
    {
        $gb = $this->storage_in_gb;
        if ($gb >= 1024) {
            return number_format($gb / 1024, 1) . ' TB';
        }
        return number_format($gb, 0) . ' GB';
    }

    public function getYearlySavingsAttribute(): float
    {
        $monthlyTotal = (float)$this->monthly_price * 12;
        return $monthlyTotal - (float)$this->yearly_price;
    }

    public function getYearlySavingsPercentageAttribute(): int
    {
        $monthlyTotal = (float)$this->monthly_price * 12;
        if ($monthlyTotal == 0) return 0;
        
        return round(($this->yearly_savings / $monthlyTotal) * 100);
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
