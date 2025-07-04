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
            // Business rates and settings
            if (!Schema::hasColumn('company_details', 'labour_rate')) {
                $table->decimal('labour_rate', 8, 2)->default(0)->after('id');
            }
            if (!Schema::hasColumn('company_details', 'vat_percent')) {
                $table->decimal('vat_percent', 5, 2)->default(15.00)->after('labour_rate');
            }
            if (!Schema::hasColumn('company_details', 'markup_percentage')) {
                $table->decimal('markup_percentage', 5, 2)->default(0)->after('vat_percent');
            }
            if (!Schema::hasColumn('company_details', 'discount_threshold')) {
                $table->decimal('discount_threshold', 10, 2)->default(0)->after('markup_percentage');
            }
            
            // Company information
            if (!Schema::hasColumn('company_details', 'company_logo')) {
                $table->string('company_logo')->nullable()->after('company_website');
            }
            
            // Payment terms
            if (!Schema::hasColumn('company_details', 'default_payment_terms')) {
                $table->integer('default_payment_terms')->default(30)->after('invoice_footer'); // days
            }
            if (!Schema::hasColumn('company_details', 'late_payment_fee')) {
                $table->decimal('late_payment_fee', 5, 2)->default(0)->after('default_payment_terms'); // percentage
            }
            
            // Document settings
            if (!Schema::hasColumn('company_details', 'quote_validity_days')) {
                $table->integer('quote_validity_days')->default(30)->after('late_payment_fee');
            }
            if (!Schema::hasColumn('company_details', 'po_auto_approval_limit')) {
                $table->decimal('po_auto_approval_limit', 12, 2)->default(0)->after('quote_validity_days');
            }
            
            // Industry specific
            if (!Schema::hasColumn('company_details', 'hourly_rate_categories')) {
                $table->json('hourly_rate_categories')->nullable()->after('po_auto_approval_limit');
            }
            if (!Schema::hasColumn('company_details', 'business_sectors')) {
                $table->json('business_sectors')->nullable()->after('hourly_rate_categories');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn([
                'markup_percentage', 'discount_threshold', 'default_payment_terms', 
                'late_payment_fee', 'quote_validity_days', 'po_auto_approval_limit',
                'hourly_rate_categories', 'business_sectors'
            ]);
        });
    }
};
