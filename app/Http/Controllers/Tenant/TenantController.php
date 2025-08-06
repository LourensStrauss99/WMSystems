<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

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
            'owner_email' => 'required|email|unique:tenants,owner_email',
            'owner_password' => ['required', 'confirmed', Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Generate unique database name using company name and current date
            $databaseName = Tenant::generateDatabaseName($request->company_name);
            $slug = Tenant::generateSlug($request->company_name);

            // Create the tenant record
            $tenant = Tenant::create([
                'name' => $request->company_name,
                'slug' => $slug,
                'database_name' => $databaseName,
                'owner_name' => $request->owner_name,
                'owner_email' => $request->owner_email,
                'owner_phone' => $request->phone,
                'address' => $request->address,
                'status' => 'active',
                'subscription_plan' => 'trial',
                'subscription_expires_at' => now()->addDays(30), // 30-day trial
            ]);

            Log::info("Creating tenant database: {$databaseName} for company: {$request->company_name}");

            // Create the tenant database
            $this->createTenantDatabase($databaseName);

            // Create the tenant schema with all tables
            $this->createTenantSchema($databaseName);

            // Create the super user in tenant database
            $this->createTenantSuperUser($tenant, $request);

            DB::commit();

            Log::info("Tenant registration completed successfully for: {$request->company_name}");

            // Ensure we're back on main database before redirect
            $this->switchToMainDatabase();

            // Redirect to login page with success message
            return redirect()->route('login')->with('success', 
                'Company registration successful! Your account has been created. Please log in with your credentials.');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Ensure we're back on main database
            $this->switchToMainDatabase();
            
            // Clean up database if it was created
            if (isset($databaseName)) {
                $this->dropTenantDatabase($databaseName);
            }

            Log::error("Tenant registration failed: " . $e->getMessage());

            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])
                        ->withInput();
        } finally {
            // Final fallback to ensure we're on main database
            $this->switchToMainDatabase();
            
            // Ensure session database is correctly set
            $this->ensureSessionDatabaseConnection();
        }
    }

    /**
     * Create tenant database
     */
    private function createTenantDatabase($databaseName)
    {
        $charset = config('database.connections.mysql.charset', 'utf8mb4');
        $collation = config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');
        
        DB::statement("CREATE DATABASE `{$databaseName}` CHARACTER SET {$charset} COLLATE {$collation}");
        Log::info("Database created: {$databaseName}");
    }

    /**
     * Drop tenant database
     */
    private function dropTenantDatabase($databaseName)
    {
        try {
            DB::statement("DROP DATABASE IF EXISTS `{$databaseName}`");
            Log::info("Database dropped: {$databaseName}");
        } catch (\Exception $e) {
            Log::error("Failed to drop tenant database {$databaseName}: " . $e->getMessage());
        }
    }

    /**
     * Create complete tenant schema
     */
    private function createTenantSchema($databaseName)
    {
        // Switch to tenant database
        $this->switchToTenantDatabase($databaseName);

        try {
            // Execute the complete schema creation
            $sqlStatements = $this->getTenantSchemaSql();
            
            foreach ($sqlStatements as $statement) {
                if (trim($statement)) {
                    DB::statement($statement);
                }
            }

            Log::info("Schema created for tenant database: {$databaseName}");
        } finally {
            // Switch back to main database
            $this->switchToMainDatabase();
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
     * Switch back to main database
     */
    private function switchToMainDatabase()
    {
        config(['database.connections.mysql.database' => env('DB_DATABASE')]);
        DB::reconnect('mysql');
    }

    /**
     * Create super user in tenant database
     */
    private function createTenantSuperUser($tenant, $request)
    {
        // Switch to tenant database
        $this->switchToTenantDatabase($tenant->database_name);

        try {
            // Create super user with level 5 admin rights
            $user = User::create([
                'name' => $request->owner_name,
                'email' => $request->owner_email,
                'password' => Hash::make($request->owner_password),
                'role' => 'admin',
                'admin_level' => 5,
                'is_superuser' => true,
                'is_active' => true,
                'email_verified_at' => now(), // Auto-verify tenant owner
                'created_at' => now()
            ]);

            Log::info("Super user created for tenant: {$tenant->name}, User ID: {$user->id}");
        } finally {
            // Switch back to main database
            $this->switchToMainDatabase();
        }
    }

    /**
     * Get the SQL statements for tenant schema creation
     */
    private function getTenantSchemaSql()
    {
        return [
            // Cache tables
            "CREATE TABLE `cache` (
                `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
                `expiration` int NOT NULL,
                PRIMARY KEY (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `cache_locks` (
                `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `expiration` int NOT NULL,
                PRIMARY KEY (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Users table
            "CREATE TABLE `users` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `surname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `email_verified_at` timestamp NULL DEFAULT NULL,
                `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `role` enum('super_admin','admin','manager','supervisor','artisan','staff','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'user',
                `is_superuser` tinyint(1) NOT NULL DEFAULT '0',
                `admin_level` int NOT NULL DEFAULT '0',
                `employee_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `telephone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT '1',
                `created_by` bigint unsigned DEFAULT NULL,
                `last_login` timestamp NULL DEFAULT NULL,
                `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `phone_verified_at` timestamp NULL DEFAULT NULL,
                `verification_code` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `bypass_verification` tinyint(1) NOT NULL DEFAULT '0',
                `is_first_user` tinyint(1) NOT NULL DEFAULT '0',
                `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `users_email_unique` (`email`),
                UNIQUE KEY `users_user_id_unique` (`user_id`),
                UNIQUE KEY `users_employee_id_unique` (`employee_id`),
                KEY `users_created_by_foreign` (`created_by`),
                CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Clients table
            "CREATE TABLE `clients` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `surname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `telephone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `is_active` tinyint(1) NOT NULL DEFAULT '1',
                `last_activity` timestamp NULL DEFAULT NULL,
                `inactive_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `payment_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `clients_email_unique` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Companies table
            "CREATE TABLE `companies` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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

            // Company details table (most comprehensive)
            "CREATE TABLE `company_details` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `labour_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
                `call_out_rate` decimal(8,2) NOT NULL DEFAULT '850.00',
                `overtime_multiplier` decimal(4,2) NOT NULL DEFAULT '1.50',
                `weekend_multiplier` decimal(4,2) NOT NULL DEFAULT '2.00',
                `public_holiday_multiplier` decimal(4,2) NOT NULL DEFAULT '2.50',
                `mileage_rate` decimal(6,2) NOT NULL DEFAULT '3.50',
                `vat_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
                `markup_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
                `discount_threshold` decimal(10,2) NOT NULL DEFAULT '0.00',
                `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `trading_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `company_reg_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `vat_reg_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `paye_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `uif_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `bee_level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `account_holder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `account_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `branch_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `branch_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `swift_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `account_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `reference_format` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INV-{YYYY}{MM}-{0000}',
                `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `physical_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `postal_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `company_telephone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `company_fax` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `company_cell` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `company_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `accounts_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `orders_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `support_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `company_website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `invoice_terms` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `invoice_footer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `quote_terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `po_terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `warranty_terms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `company_slogan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `company_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `letterhead_template` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `default_payment_terms` int NOT NULL DEFAULT '30',
                `late_payment_fee` decimal(5,2) NOT NULL DEFAULT '0.00',
                `late_payment_fee_percent` decimal(5,2) NOT NULL DEFAULT '2.00',
                `minimum_invoice_amount` decimal(10,2) NOT NULL DEFAULT '500.00',
                `quote_validity_days` int NOT NULL DEFAULT '30',
                `warranty_period_months` int NOT NULL DEFAULT '12',
                `po_auto_approval_limit` decimal(12,2) NOT NULL DEFAULT '0.00',
                `hourly_rate_categories` json DEFAULT NULL,
                `business_sectors` json DEFAULT NULL,
                `certification_numbers` json DEFAULT NULL,
                `insurance_details` json DEFAULT NULL,
                `safety_certifications` json DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `company_logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Employees table
            "CREATE TABLE `employees` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `surname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `telephone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `email_verified_at` timestamp NULL DEFAULT NULL,
                `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `admin_level` int DEFAULT '0',
                `is_superuser` tinyint(1) NOT NULL DEFAULT '0',
                `employee_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT '1',
                `created_by` bigint unsigned DEFAULT NULL,
                `last_login` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `employees_email_unique` (`email`),
                KEY `employees_created_by_foreign` (`created_by`),
                CONSTRAINT `employees_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Suppliers table
            "CREATE TABLE `suppliers` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `contact_person` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `vat_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `account_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `credit_limit` decimal(12,2) NOT NULL DEFAULT '0.00',
                `payment_terms` enum('cash','30_days','60_days','90_days') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '30_days',
                `active` tinyint(1) NOT NULL DEFAULT '1',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `deleted_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `suppliers_name_active_index` (`name`,`active`),
                KEY `suppliers_email_index` (`email`),
                KEY `suppliers_phone_index` (`phone`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Inventory table
            "CREATE TABLE `inventory` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `short_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `vendor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `department` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Department prefix for inventory code (EL, PL, SU, etc.)',
                `nett_price` decimal(10,2) NOT NULL,
                `buying_price` decimal(12,2) DEFAULT NULL,
                `sell_price` decimal(10,2) NOT NULL,
                `quantity` int NOT NULL,
                `stock_level` int NOT NULL DEFAULT '0',
                `min_quantity` int NOT NULL,
                `invoice_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `receipt_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `purchase_date` date DEFAULT NULL,
                `purchase_order_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `purchase_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `last_stock_update` date DEFAULT NULL,
                `stock_added` int NOT NULL DEFAULT '0',
                `stock_update_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Jobcards table
            "CREATE TABLE `jobcards` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `is_quote` tinyint(1) NOT NULL DEFAULT '0',
                `quote_accepted_at` timestamp NULL DEFAULT NULL,
                `accepted_by` bigint unsigned DEFAULT NULL,
                `accepted_signature` text COLLATE utf8mb4_unicode_ci,
                `inventory_id` bigint unsigned DEFAULT NULL,
                `quantity` int NOT NULL DEFAULT '1',
                `jobcard_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `job_date` date NOT NULL,
                `client_id` bigint unsigned NOT NULL,
                `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `work_request` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `special_request` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in process',
                `amount` decimal(10,2) DEFAULT NULL,
                `payment_date` date DEFAULT NULL,
                `invoice_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `work_done` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `time_spent` int DEFAULT NULL,
                `progress_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `normal_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
                `overtime_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
                `weekend_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
                `public_holiday_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
                `call_out_fee` decimal(8,2) NOT NULL DEFAULT '0.00',
                `mileage_km` decimal(8,2) NOT NULL DEFAULT '0.00',
                `mileage_cost` decimal(8,2) NOT NULL DEFAULT '0.00',
                `total_labour_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
                `visible_on_mobile` tinyint(1) NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`),
                UNIQUE KEY `jobcards_jobcard_number_unique` (`jobcard_number`),
                KEY `jobcards_client_id_foreign` (`client_id`),
                CONSTRAINT `jobcards_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Employee Jobcard pivot table
            "CREATE TABLE `employee_jobcard` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `employee_id` bigint unsigned NOT NULL,
                `jobcard_id` bigint unsigned NOT NULL,
                `hours` decimal(5,2) NOT NULL DEFAULT '0.00',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `hours_worked` int NOT NULL DEFAULT '0',
                `hour_type` enum('normal','overtime','weekend','public_holiday','call_out','traveling') COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
                `travel_km` decimal(8,2) DEFAULT NULL,
                `hourly_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
                `total_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
                PRIMARY KEY (`id`),
                KEY `employee_jobcard_employee_id_foreign` (`employee_id`),
                KEY `employee_jobcard_jobcard_id_foreign` (`jobcard_id`),
                CONSTRAINT `employee_jobcard_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
                CONSTRAINT `employee_jobcard_jobcard_id_foreign` FOREIGN KEY (`jobcard_id`) REFERENCES `jobcards` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Inventory Jobcard pivot table
            "CREATE TABLE `inventory_jobcard` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `inventory_id` bigint unsigned NOT NULL,
                `jobcard_id` bigint unsigned NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `quantity` int NOT NULL DEFAULT '1',
                `buying_price` decimal(10,2) DEFAULT NULL,
                `selling_price` decimal(10,2) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `inventory_jobcard_inventory_id_foreign` (`inventory_id`),
                KEY `inventory_jobcard_jobcard_id_foreign` (`jobcard_id`),
                CONSTRAINT `inventory_jobcard_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE,
                CONSTRAINT `inventory_jobcard_jobcard_id_foreign` FOREIGN KEY (`jobcard_id`) REFERENCES `jobcards` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Invoices table
            "CREATE TABLE `invoices` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `jobcard_id` bigint unsigned NOT NULL,
                `client_id` bigint unsigned NOT NULL,
                `invoice_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `amount` decimal(10,2) DEFAULT NULL,
                `paid_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
                `outstanding_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
                `invoice_date` date NOT NULL,
                `due_date` date DEFAULT NULL,
                `payment_date` date DEFAULT NULL,
                `status` enum('unpaid','partial','paid','overdue') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'unpaid',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
                KEY `invoices_client_id_foreign` (`client_id`),
                KEY `invoices_jobcard_id_foreign` (`jobcard_id`),
                CONSTRAINT `invoices_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
                CONSTRAINT `invoices_jobcard_id_foreign` FOREIGN KEY (`jobcard_id`) REFERENCES `jobcards` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Payments table
            "CREATE TABLE `payments` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `payment_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `client_id` bigint unsigned NOT NULL,
                `invoice_jobcard_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `amount` decimal(10,2) NOT NULL,
                `payment_method` enum('cash','card','eft','cheque','payfast','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `payment_date` date NOT NULL,
                `reference_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `status` enum('pending','completed','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
                `receipt_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `payments_receipt_number_unique` (`receipt_number`),
                KEY `payments_client_id_payment_date_index` (`client_id`,`payment_date`),
                CONSTRAINT `payments_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Purchase Orders table
            "CREATE TABLE `purchase_orders` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `po_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `supplier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `supplier_contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `supplier_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `supplier_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `supplier_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `order_date` date NOT NULL,
                `expected_delivery_date` date DEFAULT NULL,
                `actual_delivery_date` date DEFAULT NULL,
                `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
                `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
                `vat_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
                `vat_percent` decimal(5,2) NOT NULL DEFAULT '15.00',
                `grand_total` decimal(12,2) NOT NULL DEFAULT '0.00',
                `created_by` bigint unsigned DEFAULT NULL,
                `approved_by` bigint unsigned DEFAULT NULL,
                `approved_at` timestamp NULL DEFAULT NULL,
                `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `terms_conditions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `payment_terms` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `supplier_id` bigint unsigned NOT NULL,
                `submitted_for_approval_at` timestamp NULL DEFAULT NULL,
                `submitted_by` bigint unsigned DEFAULT NULL,
                `rejected_at` timestamp NULL DEFAULT NULL,
                `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `rejection_history` json DEFAULT NULL,
                `rejected_by` bigint unsigned DEFAULT NULL,
                `sent_at` timestamp NULL DEFAULT NULL,
                `sent_by` bigint unsigned DEFAULT NULL,
                `amended_by` bigint unsigned DEFAULT NULL,
                `amended_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`),
                KEY `purchase_orders_created_by_foreign` (`created_by`),
                KEY `purchase_orders_approved_by_foreign` (`approved_by`),
                KEY `purchase_orders_supplier_id_foreign` (`supplier_id`),
                KEY `purchase_orders_amended_by_foreign` (`amended_by`),
                CONSTRAINT `purchase_orders_amended_by_foreign` FOREIGN KEY (`amended_by`) REFERENCES `users` (`id`),
                CONSTRAINT `purchase_orders_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
                CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
                CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Purchase Order Items table
            "CREATE TABLE `purchase_order_items` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `purchase_order_id` bigint unsigned NOT NULL,
                `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `item_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `item_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `item_category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `quantity_ordered` int NOT NULL,
                `quantity_received` int NOT NULL DEFAULT '0',
                `quantity_outstanding` int GENERATED ALWAYS AS ((`quantity_ordered` - `quantity_received`)) VIRTUAL,
                `unit_price` decimal(10,2) NOT NULL,
                `line_total` decimal(12,2) NOT NULL,
                `unit_of_measure` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'each',
                `status` enum('pending','partially_received','fully_received') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
                `inventory_id` bigint unsigned DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                PRIMARY KEY (`id`),
                KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
                KEY `purchase_order_items_inventory_id_foreign` (`inventory_id`),
                CONSTRAINT `purchase_order_items_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`),
                CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Goods Received Vouchers table
            "CREATE TABLE `goods_received_vouchers` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `grv_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `purchase_order_id` bigint unsigned NOT NULL,
                `received_date` date NOT NULL,
                `received_time` time NOT NULL,
                `received_by` bigint unsigned NOT NULL,
                `checked_by` bigint unsigned DEFAULT NULL,
                `delivery_note_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `vehicle_registration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `driver_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `delivery_company` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `overall_status` enum('complete','partial','damaged','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'complete',
                `general_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `discrepancies` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `quality_check_passed` tinyint(1) NOT NULL DEFAULT '1',
                `quality_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `delivery_note_received` tinyint(1) NOT NULL DEFAULT '0',
                `invoice_received` tinyint(1) NOT NULL DEFAULT '0',
                `photos` json DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `goods_received_vouchers_grv_number_unique` (`grv_number`),
                KEY `goods_received_vouchers_purchase_order_id_foreign` (`purchase_order_id`),
                KEY `goods_received_vouchers_received_by_foreign` (`received_by`),
                KEY `goods_received_vouchers_checked_by_foreign` (`checked_by`),
                CONSTRAINT `goods_received_vouchers_checked_by_foreign` FOREIGN KEY (`checked_by`) REFERENCES `users` (`id`),
                CONSTRAINT `goods_received_vouchers_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
                CONSTRAINT `goods_received_vouchers_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // GRV Items table
            "CREATE TABLE `grv_items` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `grv_id` bigint unsigned NOT NULL,
                `purchase_order_item_id` bigint unsigned NOT NULL,
                `quantity_ordered` int NOT NULL,
                `quantity_received` int NOT NULL,
                `quantity_rejected` int NOT NULL DEFAULT '0',
                `quantity_damaged` int NOT NULL DEFAULT '0',
                `condition` enum('good','damaged','defective','expired') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'good',
                `item_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `storage_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `batch_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `expiry_date` date DEFAULT NULL,
                `inventory_id` bigint unsigned DEFAULT NULL,
                `stock_updated` tinyint(1) NOT NULL DEFAULT '0',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `grv_items_grv_id_foreign` (`grv_id`),
                KEY `grv_items_purchase_order_item_id_foreign` (`purchase_order_item_id`),
                KEY `grv_items_inventory_id_foreign` (`inventory_id`),
                CONSTRAINT `grv_items_grv_id_foreign` FOREIGN KEY (`grv_id`) REFERENCES `goods_received_vouchers` (`id`) ON DELETE CASCADE,
                CONSTRAINT `grv_items_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`),
                CONSTRAINT `grv_items_purchase_order_item_id_foreign` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Additional supporting tables
            "CREATE TABLE `jobcards_completed` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `jobcard_id` bigint unsigned NOT NULL,
                `completed_at` timestamp NOT NULL,
                `completion_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `jobcards_completed_jobcard_id_foreign` (`jobcard_id`),
                CONSTRAINT `jobcards_completed_jobcard_id_foreign` FOREIGN KEY (`jobcard_id`) REFERENCES `jobcards` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `jobcards_progress` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `jobcard_id` bigint unsigned NOT NULL,
                `progress_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `progress_date` timestamp NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `jobcards_progress_jobcard_id_foreign` (`jobcard_id`),
                CONSTRAINT `jobcards_progress_jobcard_id_foreign` FOREIGN KEY (`jobcard_id`) REFERENCES `jobcards` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `quotes` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `client_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `client_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `client_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `client_telephone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `quote_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `quote_date` date NOT NULL,
                `items` json NOT NULL,
                `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `quotes_quote_number_unique` (`quote_number`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `mobile_jobcards` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `mobile_jobcard_photos` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `jobcard_id` bigint unsigned NOT NULL,
                `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `uploaded_at` timestamp NOT NULL,
                `uploaded_by` bigint unsigned DEFAULT NULL,
                `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `mobile_jobcard_photos_jobcard_id_index` (`jobcard_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `settings` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `labour_rate` decimal(10,2) DEFAULT NULL,
                `vat_percent` decimal(5,2) DEFAULT NULL,
                `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `company_reg_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `vat_reg_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `account_holder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `account_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `branch_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `swift_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `company_telephone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `company_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `company_website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `invoice_terms` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `invoice_footer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            "CREATE TABLE `password_reset_tokens` (
                `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            // Insert default company data for tenant
            "INSERT INTO `companies` (`name`, `standard_labour_rate`, `call_out_rate`, `vat_percentage`, `overtime_multiplier`, `weekend_multiplier`, `public_holiday_multiplier`, `mileage_rate`, `created_at`, `updated_at`) VALUES ('Default Company', 750.00, 1000.00, 15.00, 1.50, 2.00, 2.50, 7.50, NOW(), NOW())",

            "INSERT INTO `company_details` (`company_name`, `labour_rate`, `call_out_rate`, `vat_percent`, `created_at`, `updated_at`) VALUES ('Default Company', 750.00, 850.00, 15.00, NOW(), NOW())"
        ];
    }

    /**
     * Ensure session database connection is properly set
     */
    private function ensureSessionDatabaseConnection()
    {
        // Force reconnection to main database for sessions
        Config::set('database.connections.mysql.database', env('DB_DATABASE'));
        DB::reconnect('mysql');
        
        // Clear any cached database connections
        DB::purge('mysql');
        
        // Reconnect with main database
        Config::set('database.connections.mysql.database', env('DB_DATABASE'));
        DB::reconnect('mysql');
    }
}
