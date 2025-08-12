<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'settings'
    ];

    protected $casts = [
        'subscription_expires_at' => 'datetime',
        'settings' => 'array',
    ];

    /**
     * Generate database name from company name
     */
    public static function generateDatabaseName($companyName)
    {
        $slug = Str::slug($companyName, '_');
        $slug = preg_replace('/[^a-zA-Z0-9_]/', '', $slug);
        return 'wms_' . $slug;
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
     * Get the users belonging to this tenant
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the super user for this tenant
     */
    public function superUser()
    {
        return $this->belongsTo(User::class, 'super_user_id');
    }

    /**
     * Check if tenant is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get tenant's database connection
     */
    public function getDatabaseConnection()
    {
        return "tenant_{$this->id}";
    }
}
