<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            // Tax and compliance fields
            if (!Schema::hasColumn('company_details', 'paye_number')) {
                $table->string('paye_number')->nullable()->after('vat_reg_number');
            }
            if (!Schema::hasColumn('company_details', 'uif_number')) {
                $table->string('uif_number')->nullable()->after('paye_number');
            }
            if (!Schema::hasColumn('company_details', 'bee_level')) {
                $table->string('bee_level')->nullable()->after('uif_number');
            }
            
            // Additional contact fields
            if (!Schema::hasColumn('company_details', 'company_fax')) {
                $table->string('company_fax')->nullable()->after('company_telephone');
            }
            if (!Schema::hasColumn('company_details', 'company_cell')) {
                $table->string('company_cell')->nullable()->after('company_fax');
            }
            if (!Schema::hasColumn('company_details', 'accounts_email')) {
                $table->string('accounts_email')->nullable()->after('company_email');
            }
            if (!Schema::hasColumn('company_details', 'orders_email')) {
                $table->string('orders_email')->nullable()->after('accounts_email');
            }
            if (!Schema::hasColumn('company_details', 'support_email')) {
                $table->string('support_email')->nullable()->after('orders_email');
            }
            
            // Additional address fields
            if (!Schema::hasColumn('company_details', 'physical_address')) {
                $table->text('physical_address')->nullable()->after('address');
            }
            if (!Schema::hasColumn('company_details', 'postal_address')) {
                $table->text('postal_address')->nullable()->after('physical_address');
            }
            
            // Banking field
            if (!Schema::hasColumn('company_details', 'branch_name')) {
                $table->string('branch_name')->nullable()->after('branch_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drops columns if they exist. Safe to run multiple times, but may fail if columns are missing.
        Schema::table('company_details', function (Blueprint $table) {
            $columns = [
                'paye_number',
                'uif_number',
                'bee_level',
                'company_fax',
                'company_cell',
                'accounts_email',
                'orders_email',
                'support_email',
                'physical_address',
                'postal_address',
                'branch_name'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('company_details', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
