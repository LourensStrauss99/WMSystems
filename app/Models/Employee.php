<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'employees';

    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'role',
        'admin_level',
        'is_superuser',
        'employee_id',
        'department',
        'position',
        'telephone',
        'is_active',
        'created_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_superuser' => 'boolean',
        'admin_level' => 'integer',
    ];

    // Update the jobcards relationship
    public function jobcards()
    {
        return $this->belongsToMany(Jobcard::class, 'employee_jobcard')
                    ->withPivot([
                        'hours_worked', 
                        'hour_type',
                        'hourly_rate',
                        'total_cost',
                        'travel_km' // Ensure travel_km is included
                    ])
                    ->withTimestamps();
    }

    /**
     * Check if employee can access company settings
     */
    public function canAccessCompanySettings()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 2 || 
               in_array($this->role, ['admin', 'manager']);
    }

    /**
     * Check if employee can manage purchase orders
     */
    public function canManagePurchaseOrders()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 2 || 
               in_array($this->role, ['admin', 'manager', 'supervisor']);
    }

    /**
     * Check if employee can manage inventory
     */
    public function canManageInventory()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 1 || 
               in_array($this->role, ['admin', 'manager', 'supervisor', 'artisan']);
    }

    /**
     * Check if employee can manage users
     */
    public function canManageUsers()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 3 || 
               $this->role === 'admin';
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->role));
    }

    /**
     * Get admin level display name
     */
    public function getAdminLevelNameAttribute()
    {
        $levels = [
            0 => 'No Admin Rights',
            1 => 'Basic Access',
            2 => 'Company Settings',
            3 => 'User Management',
            4 => 'System Admin',
            5 => 'Master Admin'
        ];
        
        return $levels[$this->admin_level] ?? 'Unknown';
    }

    /**
     * Get full name (name + surname)
     */
    public function getFullNameAttribute()
    {
        return trim($this->name . ' ' . $this->surname);
    }

    /**
     * Get employee's hourly rate by type
     */
    public function getHourlyRate($hourType = 'normal')
    {
        $companyDetails = \App\Models\CompanyDetail::first();
        $baseRate = $companyDetails->labour_rate ?? 450;
        
        return match($hourType) {
            'normal' => $baseRate,
            'overtime' => $baseRate * ($companyDetails->overtime_multiplier ?? 1.5),
            'weekend' => $baseRate * ($companyDetails->weekend_multiplier ?? 2.0),
            'public_holiday' => $baseRate * ($companyDetails->public_holiday_multiplier ?? 2.5),
            'call_out' => $companyDetails->call_out_rate ?? 850,
            default => $baseRate,
        };
    }

    /**
     * Get the FCM token for the employee.
     */
    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }

    protected static function booted()
    {
        static::created(function ($employee) {
            Log::info('Employee created', [
                'connection' => $employee->getConnectionName(),
                'database' => $employee->getConnection()->getDatabaseName(),
                'id' => $employee->id,
            ]);
        });
    }
}