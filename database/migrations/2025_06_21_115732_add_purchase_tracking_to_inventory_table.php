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
        Schema::table('inventory', function (Blueprint $table) {
            // Enhanced purchase tracking
            $table->string('invoice_number')->nullable()->after('goods_received_voucher');
            $table->string('receipt_number')->nullable()->after('invoice_number');
            $table->date('purchase_date')->nullable()->after('receipt_number');
            $table->string('purchase_order_number')->nullable()->after('purchase_date');
            $table->text('purchase_notes')->nullable()->after('purchase_order_number');
            
            // Stock tracking
            $table->date('last_stock_update')->nullable()->after('purchase_notes');
            $table->integer('stock_added')->default(0)->after('last_stock_update');
            $table->string('stock_update_reason')->nullable()->after('stock_added');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_number',
                'receipt_number', 
                'purchase_date',
                'purchase_order_number',
                'purchase_notes',
                'last_stock_update',
                'stock_added',
                'stock_update_reason'
            ]);
        });
    }
};
