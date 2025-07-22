<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_details', function (Blueprint $table) {
            if (!Schema::hasColumn('company_details', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('company_details', 'labour_rate')) {
                $table->decimal('labour_rate', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('company_details', 'call_out_rate')) {
                $table->decimal('call_out_rate', 8, 2)->default(850.00);
            }
            if (!Schema::hasColumn('company_details', 'overtime_multiplier')) {
                $table->decimal('overtime_multiplier', 4, 2)->default(1.50);
            }
            if (!Schema::hasColumn('company_details', 'weekend_multiplier')) {
                $table->decimal('weekend_multiplier', 4, 2)->default(2.00);
            }
            if (!Schema::hasColumn('company_details', 'public_holiday_multiplier')) {
                $table->decimal('public_holiday_multiplier', 4, 2)->default(2.50);
            }
            if (!Schema::hasColumn('company_details', 'mileage_rate')) {
                $table->decimal('mileage_rate', 6, 2)->default(3.50);
            }
            if (!Schema::hasColumn('company_details', 'vat_percent')) {
                $table->decimal('vat_percent', 5, 2)->default(0.00);
            }
            if (!Schema::hasColumn('company_details', 'markup_percentage')) {
                $table->decimal('markup_percentage', 5, 2)->default(0.00);
            }
            if (!Schema::hasColumn('company_details', 'discount_threshold')) {
                $table->decimal('discount_threshold', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('company_details', 'company_name')) {
                $table->string('company_name', 255);
            }
            if (!Schema::hasColumn('company_details', 'trading_name')) {
                $table->string('trading_name', 255);
            }
            if (!Schema::hasColumn('company_details', 'company_reg_number')) {
                $table->string('company_reg_number', 255);
            }
            if (!Schema::hasColumn('company_details', 'vat_reg_number')) {
                $table->string('vat_reg_number', 255);
            }
            if (!Schema::hasColumn('company_details', 'paye_number')) {
                $table->string('paye_number', 255);
            }
            if (!Schema::hasColumn('company_details', 'uif_number')) {
                $table->string('uif_number', 255);
            }
            if (!Schema::hasColumn('company_details', 'bee_level')) {
                $table->string('bee_level', 255);
            }
            if (!Schema::hasColumn('company_details', 'bank_name')) {
                $table->string('bank_name', 255);
            }
            if (!Schema::hasColumn('company_details', 'account_holder')) {
                $table->string('account_holder', 255);
            }
            if (!Schema::hasColumn('company_details', 'account_number')) {
                $table->string('account_number', 255);
            }
            if (!Schema::hasColumn('company_details', 'branch_code')) {
                $table->string('branch_code', 255);
            }
            if (!Schema::hasColumn('company_details', 'branch_name')) {
                $table->string('branch_name', 255);
            }
            if (!Schema::hasColumn('company_details', 'swift_code')) {
                $table->string('swift_code', 255);
            }
            if (!Schema::hasColumn('company_details', 'account_type')) {
                $table->string('account_type', 255);
            }
            if (!Schema::hasColumn('company_details', 'reference_format')) {
                $table->string('reference_format', 255)->default('INV-{YYYY}{MM}-{0000}');
            }
            if (!Schema::hasColumn('company_details', 'address')) {
                $table->string('address', 255);
            }
            if (!Schema::hasColumn('company_details', 'physical_address')) {
                $table->text('physical_address');
            }
            if (!Schema::hasColumn('company_details', 'postal_address')) {
                $table->text('postal_address');
            }
            if (!Schema::hasColumn('company_details', 'city')) {
                $table->string('city', 255);
            }
            if (!Schema::hasColumn('company_details', 'province')) {
                $table->string('province', 255);
            }
            if (!Schema::hasColumn('company_details', 'postal_code')) {
                $table->string('postal_code', 255);
            }
            if (!Schema::hasColumn('company_details', 'country')) {
                $table->string('country', 255);
            }
            if (!Schema::hasColumn('company_details', 'company_telephone')) {
                $table->string('company_telephone', 255);
            }
            if (!Schema::hasColumn('company_details', 'company_fax')) {
                $table->string('company_fax', 255);
            }
            if (!Schema::hasColumn('company_details', 'company_cell')) {
                $table->string('company_cell', 255);
            }
            if (!Schema::hasColumn('company_details', 'company_email')) {
                $table->string('company_email', 255);
            }
            if (!Schema::hasColumn('company_details', 'accounts_email')) {
                $table->string('accounts_email', 255);
            }
            if (!Schema::hasColumn('company_details', 'orders_email')) {
                $table->string('orders_email', 255);
            }
            if (!Schema::hasColumn('company_details', 'support_email')) {
                $table->string('support_email', 255);
            }
            if (!Schema::hasColumn('company_details', 'company_website')) {
                $table->string('company_website', 255);
            }
            if (!Schema::hasColumn('company_details', 'invoice_terms')) {
                $table->string('invoice_terms', 255);
            }
            if (!Schema::hasColumn('company_details', 'invoice_footer')) {
                $table->text('invoice_footer');
            }
            if (!Schema::hasColumn('company_details', 'quote_terms')) {
                $table->text('quote_terms');
            }
            if (!Schema::hasColumn('company_details', 'po_terms')) {
                $table->text('po_terms');
            }
            if (!Schema::hasColumn('company_details', 'warranty_terms')) {
                $table->text('warranty_terms');
            }
            if (!Schema::hasColumn('company_details', 'company_slogan')) {
                $table->string('company_slogan', 255);
            }
            if (!Schema::hasColumn('company_details', 'company_description')) {
                $table->text('company_description');
            }
            if (!Schema::hasColumn('company_details', 'letterhead_template')) {
                $table->string('letterhead_template', 255);
            }
            if (!Schema::hasColumn('company_details', 'default_payment_terms')) {
                $table->integer('default_payment_terms')->default(30);
            }
            if (!Schema::hasColumn('company_details', 'late_payment_fee')) {
                $table->decimal('late_payment_fee', 5, 2)->default(0.00);
            }
            if (!Schema::hasColumn('company_details', 'late_payment_fee_percent')) {
                $table->decimal('late_payment_fee_percent', 5, 2)->default(2.00);
            }
            if (!Schema::hasColumn('company_details', 'minimum_invoice_amount')) {
                $table->decimal('minimum_invoice_amount', 10, 2)->default(500.00);
            }
            if (!Schema::hasColumn('company_details', 'quote_validity_days')) {
                $table->integer('quote_validity_days')->default(30);
            }
            if (!Schema::hasColumn('company_details', 'warranty_period_months')) {
                $table->integer('warranty_period_months')->default(12);
            }
            if (!Schema::hasColumn('company_details', 'po_auto_approval_limit')) {
                $table->decimal('po_auto_approval_limit', 12, 2)->default(0.00);
            }
            if (!Schema::hasColumn('company_details', 'hourly_rate_categories')) {
                $table->json('hourly_rate_categories');
            }
            if (!Schema::hasColumn('company_details', 'business_sectors')) {
                $table->json('business_sectors');
            }
            if (!Schema::hasColumn('company_details', 'certification_numbers')) {
                $table->json('certification_numbers');
            }
            if (!Schema::hasColumn('company_details', 'insurance_details')) {
                $table->json('insurance_details');
            }
            if (!Schema::hasColumn('company_details', 'safety_certifications')) {
                $table->json('safety_certifications');
            }
            if (!Schema::hasColumn('company_details', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('company_details', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
            if (!Schema::hasColumn('company_details', 'company_logo')) {
                $table->string('company_logo', 255);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No columns are dropped in down() to avoid data loss.
    }
}; 