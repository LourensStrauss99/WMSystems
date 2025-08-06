<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait TenantDatabaseSwitch
{
    /**
     * Switch to tenant database if user is a tenant user
     */
    protected function switchToTenantDatabase()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return;
        }

        // Check if we have tenant database info in session
        $tenantDatabase = session('tenant_database');
        if ($tenantDatabase) {
            Log::info("TenantDatabaseSwitch: Switching to tenant database: {$tenantDatabase} for user: " . Auth::user()->email);
            Config::set('database.connections.mysql.database', $tenantDatabase);
            DB::purge('mysql'); // Add purge to clear connection
            DB::reconnect('mysql');
            return;
        }

        Log::info("TenantDatabaseSwitch: No tenant database in session for user: " . Auth::user()->email);
    }

    /**
     * Switch back to main database
     */
    protected function switchToMainDatabase()
    {
        Config::set('database.connections.mysql.database', env('DB_DATABASE'));
        DB::reconnect('mysql');
    }
}
