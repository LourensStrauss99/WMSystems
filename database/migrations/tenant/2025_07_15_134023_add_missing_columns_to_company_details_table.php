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
            // Rate columns
            if (!Schema::hasColumn('company_details', 'call_out_rate')) {
                $table->decimal('call_out_rate', 8, 2)->default(850.00)->after('labour_rate');
            }
            if (!Schema::hasColumn('company_details', 'overtime_multiplier')) {
                $table->decimal('overtime_multiplier', 4, 2)->default(1.5)->after('call_out_rate');
            }
            if (!Schema::hasColumn('company_details', 'weekend_multiplier')) {
                $table->decimal('weekend_multiplier', 4, 2)->default(2.0)->after('overtime_multiplier');
            }
            if (!Schema::hasColumn('company_details', 'public_holiday_multiplier')) {
                $table->decimal('public_holiday_multiplier', 4, 2)->default(2.5)->after('weekend_multiplier');
            }
            if (!Schema::hasColumn('company_details', 'mileage_rate')) {
                $table->decimal('mileage_rate', 6, 2)->default(3.50)->after('public_holiday_multiplier');
            }
            // Business terms
            if (!Schema::hasColumn('company_details', 'warranty_period_months')) {
                $table->integer('warranty_period_months')->default(12)->after('quote_validity_days');
            }
            if (!Schema::hasColumn('company_details', 'late_payment_fee_percent')) {
                $table->decimal('late_payment_fee_percent', 5, 2)->default(2.0)->after('late_payment_fee');
            }
            if (!Schema::hasColumn('company_details', 'minimum_invoice_amount')) {
                $table->decimal('minimum_invoice_amount', 10, 2)->default(500.00)->after('late_payment_fee_percent');
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
                'call_out_rate',
                'overtime_multiplier',
                'weekend_multiplier',
                'public_holiday_multiplier',
                'mileage_rate',
                'warranty_period_months',
                'late_payment_fee_percent',
                'minimum_invoice_amount'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('company_details', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
