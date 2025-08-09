<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 255);
                $table->decimal('standard_labour_rate', 8, 2)->default(750.00);
                $table->decimal('call_out_rate', 8, 2)->default(1000.00);
                $table->decimal('vat_percentage', 5, 2)->default(15.00);
                $table->decimal('overtime_multiplier', 3, 2)->default(1.50);
                $table->decimal('weekend_multiplier', 3, 2)->default(2.00);
                $table->decimal('public_holiday_multiplier', 3, 2)->default(2.50);
                $table->decimal('mileage_rate', 8, 2)->default(7.50);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}; 