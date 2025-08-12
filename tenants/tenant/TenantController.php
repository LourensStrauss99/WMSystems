<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class TenantController extends Controller
{
    /**
     * Show the company registration form
     */
    public function showRegistration()
    {
        return view('tenant.register');
    }

    /**
     * Handle company registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255|unique:tenants,name',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_password' => ['required', 'confirmed', Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Create the tenant
            $tenant = Tenant::create([
                'name' => $request->company_name,
                'slug' => Str::slug($request->company_name),
                'database_name' => $this->generateDatabaseName($request->company_name),
                'owner_name' => $request->owner_name,
                'owner_email' => $request->owner_email,
                'owner_phone' => $request->phone,
                'address' => $request->address,
                'status' => 'active',
                'subscription_plan' => 'trial',
                'subscription_expires_at' => now()->addDays(30), // 30-day trial
            ]);

            // Create the tenant database with complete schema
            $this->createTenantDatabase($tenant->database_name);
            $this->createTenantSchema($tenant->database_name);

            // Create the super user in tenant database
            $this->createTenantSuperUser($tenant, $request);

            DB::commit();

            // Log the user in by switching to tenant database
            $this->switchToTenantDatabase($tenant->database_name);
            $superUser = User::where('email', $request->owner_email)->first();
            Auth::login($superUser);

            return redirect()->route('dashboard')->with('success', 
                'Company registered successfully! Welcome to your 30-day trial.');

        } catch (\Exception $e) {
            // Only rollback if transaction is active
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            // Clean up database if it was created
            if (isset($tenant) && isset($tenant->database_name)) {
                $this->dropTenantDatabase($tenant->database_name);
            }

            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])
                        ->withInput($request->except('owner_password', 'owner_password_confirmation'));
        }
    }

    /**
     * Generate a unique database name
     */
    private function generateDatabaseName($companyName)
    {
        $base = 'wf_' . Str::slug($companyName, '_');
        $base = preg_replace('/[^a-zA-Z0-9_]/', '', $base);
        $base = substr($base, 0, 50); // MySQL database name limit
        
        $counter = 1;
        $databaseName = $base;
        
        while (Tenant::where('database_name', $databaseName)->exists()) {
            $databaseName = $base . '_' . $counter;
            $counter++;
        }
        
        return $databaseName;
    }

    /**
     * Create tenant database
     */
    private function createTenantDatabase($databaseName)
    {
        $charset = config('database.connections.mysql.charset', 'utf8mb4');
        $collation = config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');
        
        DB::statement("CREATE DATABASE `{$databaseName}` CHARACTER SET {$charset} COLLATE {$collation}");
    }

    /**
     * Drop tenant database
     */
    private function dropTenantDatabase($databaseName)
    {
        try {
            DB::statement("DROP DATABASE IF EXISTS `{$databaseName}`");
        } catch (\Exception $e) {
            // Log error but don't throw
            Log::error("Failed to drop tenant database {$databaseName}: " . $e->getMessage());
        }
    }

    /**
     * Run migrations on tenant database
     */
    private function runTenantMigrations($databaseName)
    {
        // Switch to tenant database temporarily
        $defaultConnection = config('database.default');
        
        config(['database.connections.tenant' => array_merge(
            config('database.connections.mysql'),
            ['database' => $databaseName]
        )]);
        
        // Purge existing connections to avoid conflicts
        DB::purge('tenant');
        config(['database.default' => 'tenant']);
        
        try {
            // Run only essential migrations for tenant database
            $essentialMigrations = [
                '2024_01_1000001_create_clients_table.php',
                '2024_01_1000025_create_users_table.php', // Users first
                '2024_01_1000022_create_sessions_table.php',
                '2024_01_1000017_create_password_reset_tokens_table.php',
                '2024_01_1000011_create_jobcards_table.php',
                '2024_01_1000012_create_jobcards_completed_table.php',
                '2024_01_1000013_create_jobcards_progress_table.php',
                '2024_01_1000008_create_inventory_table.php',
                '2024_01_1000010_create_invoices_table.php',
                '2024_01_1000023_create_settings_table.php',
            ];
            
            foreach ($essentialMigrations as $migration) {
                $migrationPath = database_path('migrations/' . $migration);
                if (file_exists($migrationPath)) {
                    try {
                        // Run each migration individually with fresh connection
                        DB::purge('tenant');
                        Artisan::call('migrate', [
                            '--database' => 'tenant',
                            '--path' => 'database/migrations/' . $migration,
                            '--force' => true,
                        ]);
                        Log::info("Successfully migrated {$migration} for tenant {$databaseName}");
                    } catch (\Exception $e) {
                        // Log but continue - some migrations may fail due to dependencies
                        Log::warning("Migration {$migration} failed for tenant {$databaseName}: " . $e->getMessage());
                    }
                }
            }
            
            // Run basic seeder for tenant (optional)
            try {
                Artisan::call('db:seed', [
                    '--database' => 'tenant',
                    '--force' => true,
                    '--class' => 'BasicDataSeeder',
                ]);
                Log::info("BasicDataSeeder completed for tenant {$databaseName}");
            } catch (\Exception $e) {
                // Seeder is optional
                Log::info("BasicDataSeeder skipped for tenant {$databaseName}: " . $e->getMessage());
            }
            
        } finally {
            // Clean up and switch back to default connection
            DB::purge('tenant');
            config(['database.default' => $defaultConnection]);
            DB::reconnect($defaultConnection);
        }
    }

    /**
     * Show tenant dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // For the new system, create a mock tenant object from current database
        $currentDb = DB::connection()->getDatabaseName();
        
        // Find tenant by database name
        $originalDb = env('DB_DATABASE', 'workflow_management');
        config(['database.connections.mysql.database' => $originalDb]);
        DB::reconnect('mysql');
        
        $tenant = Tenant::where('database_name', $currentDb)->first();
        
        // Switch back to tenant database
        config(['database.connections.mysql.database' => $currentDb]);
        DB::reconnect('mysql');
        
        if (!$tenant) {
            // Fallback: create a mock tenant object for display
            $tenant = (object) [
                'name' => 'Your Company',
                'status' => 'active',
                'subscription_plan' => 'trial',
                'subscription_expires_at' => now()->addDays(30),
                'database_name' => $currentDb,
                'owner_name' => $user->name,
                'owner_phone' => $user->telephone,
                'address' => null,
                'created_at' => $user->created_at ?? now(),
                'jobcards_count' => 0,
                'customers_count' => 0,
                'pending_jobs_count' => 0,
                'users_count' => 1,
            ];
        }
        
        return view('tenant.dashboard', compact('tenant'));
    }

    /**
     * Show tenant settings
     */
    public function settings()
    {
        $this->authorize('manage-company');
        
        $tenant = Auth::user()->tenant;
        
        return view('tenant.settings', compact('tenant'));
    }

    /**
     * Update tenant settings
     */
    public function updateSettings(Request $request)
    {
        $this->authorize('manage-company');
        
        $tenant = Auth::user()->tenant;
        
        $request->validate([
            'company_name' => 'required|string|max:255|unique:tenants,company_name,' . $tenant->id,
            'owner_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $tenant->update([
            'company_name' => $request->company_name,
            'owner_name' => $request->owner_name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return back()->with('success', 'Company settings updated successfully.');
    }

    /**
     * Show all tenants (super admin only)
     */
    public function index()
    {
        // Only allow access from main application (not tenant-specific)
        if (!Auth::user()->is_superuser || Auth::user()->tenant_id !== null) {
            abort(403, 'Unauthorized access.');
        }

        $tenants = Tenant::with('superUser')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Suspend a tenant
     */
    public function suspend(Tenant $tenant)
    {
        // Only allow access from main application
        if (!Auth::user()->is_superuser || Auth::user()->tenant_id !== null) {
            abort(403, 'Unauthorized access.');
        }

        $tenant->update(['status' => 'suspended']);

        return back()->with('success', 'Tenant suspended successfully.');
    }

    /**
     * Activate a tenant
     */
    public function activate(Tenant $tenant)
    {
        // Only allow access from main application
        if (!Auth::user()->is_superuser || Auth::user()->tenant_id !== null) {
            abort(403, 'Unauthorized access.');
        }

        $tenant->update(['status' => 'active']);

        return back()->with('success', 'Tenant activated successfully.');
    }

    /**
     * Create complete tenant schema with your provided structure
     */
    private function createTenantSchema($databaseName)
    {
        // Switch to tenant database
        $this->switchToTenantDatabase($databaseName);

        // Execute the complete schema creation
        $sqlStatements = $this->getTenantSchemaSql();
        
        foreach ($sqlStatements as $statement) {
            if (!empty(trim($statement))) {
                DB::statement($statement);
            }
        }
    }

    /**
     * Switch database connection to tenant database
     */
    private function switchToTenantDatabase($databaseName)
    {
        config(['database.connections.mysql.database' => $databaseName]);
        DB::reconnect('mysql');
    }

    /**
     * Create super user in tenant database
     */
    private function createTenantSuperUser($tenant, $request)
    {
        // Switch to tenant database
        $this->switchToTenantDatabase($tenant->database_name);

        // Create user in tenant database
        User::create([
            'name' => $request->owner_name,
            'email' => $request->owner_email,
            'password' => Hash::make($request->owner_password),
            'email_verified_at' => now(),
            'role' => 'super_admin',
            'is_superuser' => true,
            'admin_level' => 5,
            'telephone' => $request->phone,
            'is_active' => true,
            'is_first_user' => true,
        ]);
    }

    /**
     * Get the SQL statements for tenant schema creation
     */
    private function getTenantSchemaSql()
    {
        return [
            "SET NAMES utf8mb4",
            "SET foreign_key_checks = 0",
            "SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'",
            
            "CREATE TABLE `clients` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `surname` varchar(255) NOT NULL,
                `telephone` varchar(255) NOT NULL,
                `address` varchar(255) NOT NULL,
                `notes` text,
                `is_active` tinyint(1) NOT NULL DEFAULT '1',
                `last_activity` timestamp NULL DEFAULT NULL,
                `inactive_reason` varchar(255) DEFAULT NULL,
                `email` varchar(255) NOT NULL,
                `payment_reference` varchar(255) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `clients_email_unique` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `companies` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `standard_labour_rate` decimal(8,2) NOT NULL DEFAULT '750.00',
                `call_out_rate` decimal(8,2) NOT NULL DEFAULT '1000.00',
                `vat_percentage` decimal(5,2) NOT NULL DEFAULT '15.00',
                `overtime_multiplier` decimal(3,2) NOT NULL DEFAULT '1.50',
                `weekend_multiplier` decimal(3,2) NOT NULL DEFAULT '2.00',
                `public_holiday_multiplier` decimal(3,2) NOT NULL DEFAULT '2.50',
                `mileage_rate` decimal(8,2) NOT NULL DEFAULT '7.50',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `users` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `user_id` varchar(255) DEFAULT NULL,
                `name` varchar(255) NOT NULL,
                `surname` varchar(255) DEFAULT NULL,
                `email` varchar(255) NOT NULL,
                `email_verified_at` timestamp NULL DEFAULT NULL,
                `password` varchar(255) NOT NULL,
                `remember_token` varchar(100) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `role` enum('super_admin','admin','manager','supervisor','artisan','staff','user') DEFAULT 'user',
                `is_superuser` tinyint(1) NOT NULL DEFAULT '0',
                `admin_level` int NOT NULL DEFAULT '0',
                `employee_id` varchar(255) DEFAULT NULL,
                `department` varchar(255) DEFAULT NULL,
                `position` varchar(255) DEFAULT NULL,
                `telephone` varchar(255) DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT '1',
                `created_by` bigint unsigned DEFAULT NULL,
                `last_login` timestamp NULL DEFAULT NULL,
                `phone` varchar(255) DEFAULT NULL,
                `address` text,
                `phone_verified_at` timestamp NULL DEFAULT NULL,
                `verification_code` varchar(6) DEFAULT NULL,
                `bypass_verification` tinyint(1) NOT NULL DEFAULT '0',
                `is_first_user` tinyint(1) NOT NULL DEFAULT '0',
                `photo` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `users_email_unique` (`email`),
                UNIQUE KEY `users_user_id_unique` (`user_id`),
                UNIQUE KEY `users_employee_id_unique` (`employee_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `sessions` (
                `id` varchar(255) NOT NULL,
                `user_id` bigint unsigned DEFAULT NULL,
                `ip_address` varchar(45) DEFAULT NULL,
                `user_agent` text,
                `payload` longtext NOT NULL,
                `last_activity` int NOT NULL,
                PRIMARY KEY (`id`),
                KEY `sessions_user_id_index` (`user_id`),
                KEY `sessions_last_activity_index` (`last_activity`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `password_reset_tokens` (
                `email` varchar(255) NOT NULL,
                `token` varchar(255) NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "INSERT INTO `companies` (`id`, `name`, `standard_labour_rate`, `call_out_rate`, `vat_percentage`, `overtime_multiplier`, `weekend_multiplier`, `public_holiday_multiplier`, `mileage_rate`, `created_at`, `updated_at`) VALUES
            (1, 'Your Company Name', 750.00, 1000.00, 15.00, 1.50, 2.00, 2.50, 7.50, NOW(), NOW())",

            "SET foreign_key_checks = 1"
        ];
    }
}
