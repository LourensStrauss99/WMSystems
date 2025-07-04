<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
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
        'is_superuser' => 'boolean',
        'admin_level' => 'integer',
        'is_active' => 'boolean',
        'last_login' => 'datetime',
    ];

    // ===== CORE PERMISSION METHODS =====

    /**
     * Check if user is super user
     */
    public function isSuperUser()
    {
        return $this->is_superuser == 1;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin() 
    {
        return $this->is_superuser == 1 || $this->admin_level >= 2;
    }

    /**
     * Check if user can access company settings
     */
    public function canAccessCompanySettings()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 2 || 
               in_array($this->role, ['admin', 'manager']);
    }

    /**
     * Check if user can access master settings
     */
    public function canAccessMasterSettings()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 3 || 
               in_array($this->role, ['admin', 'manager']);
    }

    /**
     * Check if user can manage users
     */
    public function canManageUsers()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 3 || 
               $this->role === 'admin';
    }

    /**
     * Check if user can manage inventory
     */
    public function canManageInventory()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 1 || 
               in_array($this->role, ['admin', 'artisan', 'manager']);
    }

    /**
     * Check if user can manage purchase orders
     */
    public function canManagePurchaseOrders()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 2 || 
               in_array($this->role, ['admin', 'manager']);
    }

    /**
     * Check if user can approve purchase orders
     */
    public function canApprove($amount = null)
    {
        // Super users can approve anything
        if ($this->is_superuser == 1) {
            return true;
        }

        // Admin level based approval limits
        $approvalLimits = [
            5 => PHP_INT_MAX, // Master Admin: Unlimited
            4 => 100000,      // System Admin: R100,000
            3 => 50000,       // User Management: R50,000
            2 => 25000,       // Company Settings: R25,000
            1 => 10000,       // Basic Access: R10,000
        ];

        $limit = $approvalLimits[$this->admin_level] ?? 0;

        // Role-based adjustments
        if ($this->role === 'manager' && $limit < 10000) {
            $limit = 10000;
        }

        return $amount === null || $amount <= $limit;
    }

    /**
     * Check if user can create purchase orders
     */
    public function canCreatePurchaseOrders()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 1 || 
               in_array($this->role, ['admin', 'manager', 'artisan']);
    }

    /**
     * Check if user can edit purchase orders
     */
    public function canEditPurchaseOrders()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 2 || 
               in_array($this->role, ['admin', 'manager']);
    }

    /**
     * Check if user can delete purchase orders
     */
    public function canDeletePurchaseOrders()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 3 || 
               $this->role === 'admin';
    }

    /**
     * Check if user can manage suppliers
     */
    public function canManageSuppliers()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 2 || 
               $this->role === 'admin';
    }

    /**
     * Check if user can view reports
     */
    public function canViewReports()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 1 || 
               in_array($this->role, ['admin', 'manager']);
    }

    /**
     * Check if user can manage GRVs
     */
    public function canManageGrvs()
    {
        return $this->is_superuser == 1 || 
               $this->admin_level >= 1 || 
               in_array($this->role, ['admin', 'manager', 'artisan']);
    }

    // ===== DISPLAY METHODS =====

    /**
     * Get role display name
     */
    public function getRoleDisplayAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->role));
    }

    /**
     * Get admin level name
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
     * Get user's maximum approval amount
     */
    public function getMaxApprovalAmountAttribute()
    {
        if ($this->is_superuser == 1) {
            return PHP_INT_MAX; // Unlimited
        }

        $approvalLimits = [
            5 => 100000,  // Master Admin: R100,000
            4 => 50000,   // System Admin: R50,000
            3 => 25000,   // User Management: R25,000
            2 => 10000,   // Company Settings: R10,000
            1 => 5000,    // Basic Access: R5,000
            0 => 0,       // No admin rights: R0
        ];

        $baseLimit = $approvalLimits[$this->admin_level] ?? 0;

        // Role-based adjustments
        if ($this->role === 'manager' && $baseLimit < 5000) {
            return 5000;
        }

        return $baseLimit;
    }

    /**
     * Default is_active to true if not set
     */
    public function getIsActiveAttribute($value)
    {
        return $value ?? true;
    }

    /**
     * Get user's permission level (1-5, where 1 is highest)
     */
    public function getPermissionLevelAttribute()
    {
        if ($this->is_superuser) return 1;
        if ($this->admin_level >= 4) return 2;
        if ($this->admin_level >= 3) return 3;
        if ($this->admin_level >= 2) return 4;
        return 5;
    }

    /**
     * Get full name with employee ID
     */
    public function getFullNameAttribute()
    {
        $name = $this->name;
        if ($this->employee_id) {
            $name .= " ({$this->employee_id})";
        }
        return $name;
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($roles)
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }
        
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        
        return false;
    }

    /**
     * Check if user has minimum admin level
     */
    public function hasMinimumAdminLevel($level)
    {
        return $this->is_superuser == 1 || $this->admin_level >= $level;
    }

    /**
     * Get user's capabilities as an array
     */
    public function getCapabilitiesAttribute()
    {
        return [
            'can_manage_users' => $this->canManageUsers(),
            'can_manage_inventory' => $this->canManageInventory(),
            'can_manage_purchase_orders' => $this->canManagePurchaseOrders(),
            'can_approve_orders' => $this->canApprove(),
            'can_manage_suppliers' => $this->canManageSuppliers(),
            'can_manage_grvs' => $this->canManageGrvs(),
            'can_view_reports' => $this->canViewReports(),
            'can_access_company_settings' => $this->canAccessCompanySettings(),
            'can_access_master_settings' => $this->canAccessMasterSettings(),
            'max_approval_amount' => $this->max_approval_amount,
        ];
    }
}
