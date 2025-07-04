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
            // Add only the missing columns based on your current structure
            
            // Missing business fields
            if (!Schema::hasColumn('company_details', 'trading_name')) {
                $table->string('trading_name')->nullable()->after('company_name');
            }
            
            if (!Schema::hasColumn('company_details', 'account_type')) {
                $table->string('account_type')->nullable()->after('swift_code');
            }
            
            if (!Schema::hasColumn('company_details', 'reference_format')) {
                $table->string('reference_format')->default('INV-{YYYY}{MM}-{0000}')->after('account_type');
            }
            
            // Document terms
            if (!Schema::hasColumn('company_details', 'quote_terms')) {
                $table->text('quote_terms')->nullable()->after('invoice_footer');
            }
            
            if (!Schema::hasColumn('company_details', 'po_terms')) {
                $table->text('po_terms')->nullable()->after('quote_terms');
            }
            
            if (!Schema::hasColumn('company_details', 'warranty_terms')) {
                $table->text('warranty_terms')->nullable()->after('po_terms');
            }
            
            if (!Schema::hasColumn('company_details', 'company_slogan')) {
                $table->string('company_slogan')->nullable()->after('warranty_terms');
            }
            
            if (!Schema::hasColumn('company_details', 'company_description')) {
                $table->text('company_description')->nullable()->after('company_slogan');
            }
            
            if (!Schema::hasColumn('company_details', 'letterhead_template')) {
                $table->string('letterhead_template')->nullable()->after('company_description');
            }
            
            // Industry specific
            if (!Schema::hasColumn('company_details', 'certification_numbers')) {
                $table->json('certification_numbers')->nullable()->after('business_sectors');
            }
            
            if (!Schema::hasColumn('company_details', 'insurance_details')) {
                $table->json('insurance_details')->nullable()->after('certification_numbers');
            }
            
            if (!Schema::hasColumn('company_details', 'safety_certifications')) {
                $table->json('safety_certifications')->nullable()->after('insurance_details');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            $columnsToRemove = [
                'trading_name', 'account_type', 'reference_format',
                'quote_terms', 'po_terms', 'warranty_terms',
                'company_slogan', 'company_description', 'letterhead_template',
                'certification_numbers', 'insurance_details', 'safety_certifications'
            ];

            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('company_details', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
