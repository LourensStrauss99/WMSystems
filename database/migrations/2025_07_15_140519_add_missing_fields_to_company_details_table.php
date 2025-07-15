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
            $table->string('paye_number')->nullable()->after('vat_reg_number');
            $table->string('uif_number')->nullable()->after('paye_number');
            $table->string('bee_level')->nullable()->after('uif_number');
            
            // Additional contact fields
            $table->string('company_fax')->nullable()->after('company_telephone');
            $table->string('company_cell')->nullable()->after('company_fax');
            $table->string('accounts_email')->nullable()->after('company_email');
            $table->string('orders_email')->nullable()->after('accounts_email');
            $table->string('support_email')->nullable()->after('orders_email');
            
            // Additional address fields
            $table->text('physical_address')->nullable()->after('address');
            $table->text('postal_address')->nullable()->after('physical_address');
            
            // Banking field
            $table->string('branch_name')->nullable()->after('branch_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
