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
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('purchase_orders', 'supplier_id')) {
                $table->foreignId('supplier_id')->constrained()->after('po_number');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'order_date')) {
                $table->date('order_date')->after('supplier_id');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'expected_delivery_date')) {
                $table->date('expected_delivery_date')->nullable()->after('order_date');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'status')) {
                $table->enum('status', ['draft', 'sent', 'confirmed', 'partially_received', 'fully_received', 'cancelled'])
                      ->default('draft')->after('expected_delivery_date');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('status');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'vat_amount')) {
                $table->decimal('vat_amount', 10, 2)->default(0)->after('total_amount');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'grand_total')) {
                $table->decimal('grand_total', 10, 2)->default(0)->after('vat_amount');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'notes')) {
                $table->text('notes')->nullable()->after('grand_total');
            }
            
            if (!Schema::hasColumn('purchase_orders', 'created_by')) {
                $table->foreignId('created_by')->constrained('users')->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['supplier_id', 'order_date', 'expected_delivery_date', 'status', 'total_amount', 'vat_amount', 'grand_total', 'notes']);
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
