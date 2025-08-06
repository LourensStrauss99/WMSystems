<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'database_name',
        'domain',
        'owner_name',
        'owner_email',
        'owner_phone',
        'address',
        'city',
        'country',
        'status',
        'subscription_plan',
        'subscription_expires_at',
        'settings',
        'created_at'
    ];

    protected $casts = [
        'subscription_expires_at' => 'datetime',
        'settings' => 'array',
        'created_at' => 'datetime'
    ];

    /**
     * Generate unique database name from company name and date
     */
    public static function generateDatabaseName($companyName)
    {
        $slug = Str::slug($companyName, '_');
        $slug = preg_replace('/[^a-zA-Z0-9_]/', '', $slug);
        $date = Carbon::now()->format('Ymd');
        
        $baseDbName = 'wms_' . $slug . '_' . $date;
        $databaseName = $baseDbName;
        $counter = 1;

        // Ensure uniqueness
        while (self::where('database_name', $databaseName)->exists()) {
            $databaseName = $baseDbName . '_' . $counter;
            $counter++;
        }

        return $databaseName;
    }

    /**
     * Generate unique slug from company name
     */
    public static function generateSlug($companyName)
    {
        $baseSlug = Str::slug($companyName);
        $slug = $baseSlug;
        $counter = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the super user for this tenant
     */
    public function superUser()
    {
        // This needs to query the tenant database
        return null; // Will be implemented when switching to tenant DB
    }

    /**
     * Check if tenant is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if tenant subscription is valid
     */
    public function hasValidSubscription()
    {
        return $this->subscription_expires_at && $this->subscription_expires_at->isFuture();
    }

    /**
     * Get tenant's database connection name
     */
    public function getDatabaseConnection()
    {
        return "tenant_{$this->id}";
    }

    /**
     * Get full database name with prefix
     */
    public function getFullDatabaseName()
    {
        return $this->database_name;
    }
}
