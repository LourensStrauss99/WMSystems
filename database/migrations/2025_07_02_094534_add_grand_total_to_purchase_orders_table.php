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
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'vat_amount')) {
                $table->decimal('vat_amount', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'grand_total')) {
                $table->decimal('grand_total', 15, 2)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('grand_total');
            if (Schema::hasColumn('purchase_orders', 'vat_amount')) {
                $table->dropColumn('vat_amount');
            }
        });
    }
};
