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
            if (!Schema::hasColumn('purchase_order_items', 'quantity_received')) {
                $table->integer('quantity_received')->default(0)->after('quantity_ordered');
            }
            if (!Schema::hasColumn('purchase_order_items', 'status')) {
                $table->enum('status', ['pending', 'partially_received', 'fully_received', 'cancelled'])->default('pending')->after('line_total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['quantity_received', 'status']);
        });
    }
};
