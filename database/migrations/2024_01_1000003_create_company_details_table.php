<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('company_details')) {
            Schema::create('company_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->decimal('labour_rate', 10, 2)->default(0.00);
                $table->decimal('call_out_rate', 8, 2)->default(850.00);
                $table->decimal('overtime_multiplier', 4, 2)->default(1.50);
                $table->decimal('weekend_multiplier', 4, 2)->default(2.00);
                $table->decimal('public_holiday_multiplier', 4, 2)->default(2.50);
                $table->decimal('mileage_rate', 6, 2)->default(3.50);
                $table->decimal('vat_percent', 5, 2)->default(0.00);
                $table->decimal('markup_percentage', 5, 2)->default(0.00);
                $table->decimal('discount_threshold', 10, 2)->default(0.00);
                $table->string('company_name', 255);
                $table->string('trading_name', 255)->nullable();
                $table->string('company_reg_number', 255)->nullable();
                $table->string('vat_reg_number', 255)->nullable();
                $table->string('paye_number', 255)->nullable();
                $table->string('uif_number', 255)->nullable();
                $table->string('bee_level', 255)->nullable();
                $table->string('bank_name', 255)->nullable();
                $table->string('account_holder', 255)->nullable();
                $table->string('account_number', 255)->nullable();
                $table->string('branch_code', 255)->nullable();
                $table->string('branch_name', 255)->nullable();
                $table->string('swift_code', 255)->nullable();
                $table->string('account_type', 255)->nullable();
                $table->string('reference_format', 255)->default('INV-{YYYY}{MM}-{0000}');
                $table->string('address', 255)->nullable();
                $table->text('physical_address')->nullable();
                $table->text('postal_address')->nullable();
                $table->string('city', 255)->nullable();
                $table->string('province', 255)->nullable();
                $table->string('postal_code', 255)->nullable();
                $table->string('country', 255)->nullable();
                $table->string('company_telephone', 255)->nullable();
                $table->string('company_fax', 255)->nullable();
                $table->string('company_cell', 255)->nullable();
                $table->string('company_email', 255)->nullable();
                $table->string('accounts_email', 255)->nullable();
                $table->string('orders_email', 255)->nullable();
                $table->string('support_email', 255)->nullable();
                $table->string('company_website', 255)->nullable();
                $table->string('invoice_terms', 255)->nullable();
                $table->text('invoice_footer')->nullable();
                $table->text('quote_terms')->nullable();
                $table->text('po_terms')->nullable();
                $table->text('warranty_terms')->nullable();
                $table->string('company_slogan', 255)->nullable();
                $table->text('company_description')->nullable();
                $table->string('letterhead_template', 255)->nullable();
                $table->integer('default_payment_terms')->default(30);
                $table->decimal('late_payment_fee', 5, 2)->default(0.00);
                $table->decimal('late_payment_fee_percent', 5, 2)->default(2.00);
                $table->decimal('minimum_invoice_amount', 10, 2)->default(500.00);
                $table->integer('quote_validity_days')->default(30);
                $table->integer('warranty_period_months')->default(12);
                $table->decimal('po_auto_approval_limit', 12, 2)->default(0.00);
                $table->json('hourly_rate_categories')->nullable();
                $table->json('business_sectors')->nullable();
                $table->json('certification_numbers')->nullable();
                $table->json('insurance_details')->nullable();
                $table->json('safety_certifications')->nullable();
                $table->timestamps();
                $table->string('company_logo', 255)->nullable();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('company_details');
    }
}; 