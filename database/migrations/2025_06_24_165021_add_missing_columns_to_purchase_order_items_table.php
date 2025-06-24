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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('purchase_order_items', 'item_code')) {
                $table->string('item_code')->nullable()->after('item_name');
            }
            
            if (!Schema::hasColumn('purchase_order_items', 'description')) {
                $table->text('description')->nullable()->after('item_code');
            }
            
            if (!Schema::hasColumn('purchase_order_items', 'quantity_received')) {
                $table->decimal('quantity_received', 8, 3)->default(0)->after('quantity_ordered');
            }
            
            if (!Schema::hasColumn('purchase_order_items', 'line_total')) {
                $table->decimal('line_total', 10, 2)->after('unit_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['item_code', 'description', 'quantity_received', 'line_total']);
        });
    }
};
