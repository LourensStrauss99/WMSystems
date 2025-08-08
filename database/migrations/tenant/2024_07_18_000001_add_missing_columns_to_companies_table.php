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
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('companies', 'name')) {
                $table->string('name', 255);
            }
            if (!Schema::hasColumn('companies', 'standard_labour_rate')) {
                $table->decimal('standard_labour_rate', 8, 2)->default(750.00);
            }
            if (!Schema::hasColumn('companies', 'call_out_rate')) {
                $table->decimal('call_out_rate', 8, 2)->default(1000.00);
            }
            if (!Schema::hasColumn('companies', 'vat_percentage')) {
                $table->decimal('vat_percentage', 5, 2)->default(15.00);
            }
            if (!Schema::hasColumn('companies', 'overtime_multiplier')) {
                $table->decimal('overtime_multiplier', 3, 2)->default(1.50);
            }
            if (!Schema::hasColumn('companies', 'weekend_multiplier')) {
                $table->decimal('weekend_multiplier', 3, 2)->default(2.00);
            }
            if (!Schema::hasColumn('companies', 'public_holiday_multiplier')) {
                $table->decimal('public_holiday_multiplier', 3, 2)->default(2.50);
            }
            if (!Schema::hasColumn('companies', 'mileage_rate')) {
                $table->decimal('mileage_rate', 8, 2)->default(7.50);
            }
            if (!Schema::hasColumn('companies', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('companies', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
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
        // Drops columns if they exist. Safe to run multiple times, but may fail if columns are missing.
        Schema::table('companies', function (Blueprint $table) {
            $columns = [
                'id', 'name', 'standard_labour_rate', 'call_out_rate', 'vat_percentage', 'overtime_multiplier',
                'weekend_multiplier', 'public_holiday_multiplier', 'mileage_rate', 'created_at', 'updated_at'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('companies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 