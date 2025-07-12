<?php
// Create app/Traits/HasPermissions.php

namespace App\Traits;

trait HasPermissions
{
    /**
     * Determine if the user can access company settings.
     * Only superusers or admin level 5+ can access company details.
     */
    public function canAccessCompanySettings()
    {
        return $this->is_superuser || $this->admin_level >= 5;
    }

    /**
     * Check if user is superuser
     */
    public function isSuperUser()
    {
        return $this->is_superuser;
    }

    /**
     * Check if user has high-level admin privileges
     */
    public function isHighLevelAdmin()
    {
        return $this->admin_level >= 5;
    }

    /**
     * Check if user can manage company settings
     */
    public function canManageCompany()
    {
        return $this->is_superuser || $this->admin_level >= 5;
    }

    /**
     * Check if user can approve purchase orders
     */
    public function canApprove()
    {
        return $this->admin_level >= 3;
    }

    /**
     * Check if user can manage inventory
     */
    public function canManageInventory()
    {
        return $this->admin_level >= 2;
    }
}