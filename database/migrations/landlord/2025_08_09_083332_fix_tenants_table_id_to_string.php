<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, check if there are any existing tenants (there shouldn't be any yet)
        $existingTenants = DB::table('tenants')->count();
        
        if ($existingTenants > 0) {
            throw new Exception('Cannot modify tenants table ID column - there are existing tenants. Please backup and migrate them manually.');
        }

        // Drop foreign key constraints from domains table temporarily if they exist
        if (Schema::hasTable('domains')) {
            try {
                Schema::table('domains', function (Blueprint $table) {
                    $table->dropForeign(['tenant_id']);
                });
            } catch (Exception $e) {
                // Foreign key might not exist, continue
            }
        }

        // Drop any other foreign key constraints that might reference tenants.id
        // Check for tenant_payments table
        if (Schema::hasTable('tenant_payments')) {
            try {
                Schema::table('tenant_payments', function (Blueprint $table) {
                    $table->dropForeign(['tenant_id']);
                });
            } catch (Exception $e) {
                // Foreign key might not exist, continue
            }
        }

        // Modify the tenants table ID column
        Schema::table('tenants', function (Blueprint $table) {
            // Drop the auto-increment primary key
            $table->dropPrimary(['id']);
        });

        // Change the column type
        DB::statement('ALTER TABLE tenants MODIFY COLUMN id VARCHAR(255) NOT NULL');

        // Re-add the primary key
        Schema::table('tenants', function (Blueprint $table) {
            $table->primary('id');
        });

        // Re-add foreign key constraints
        if (Schema::hasTable('domains')) {
            // First, modify the domains table tenant_id column to match
            DB::statement('ALTER TABLE domains MODIFY COLUMN tenant_id VARCHAR(255) NOT NULL');
            
            Schema::table('domains', function (Blueprint $table) {
                $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            });
        }

        // Re-add tenant_payments foreign key if table exists
        if (Schema::hasTable('tenant_payments')) {
            // Modify the tenant_payments table tenant_id column to match
            DB::statement('ALTER TABLE tenant_payments MODIFY COLUMN tenant_id VARCHAR(255) NOT NULL');
            
            try {
                Schema::table('tenant_payments', function (Blueprint $table) {
                    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
                });
            } catch (Exception $e) {
                // If this fails, it's okay - the relationship can be established later
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is not easily reversible since we're changing data types
        // Would require manual intervention to convert string IDs back to integers
        throw new Exception('This migration cannot be safely reversed. Manual intervention required.');
    }
};
