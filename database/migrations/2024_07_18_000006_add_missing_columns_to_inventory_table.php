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
        Schema::table('inventory', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('inventory', 'description')) {
                $table->string('description', 255);
            }
            if (!Schema::hasColumn('inventory', 'short_code')) {
                $table->string('short_code', 255);
            }
            if (!Schema::hasColumn('inventory', 'vendor')) {
                $table->string('vendor', 255);
            }
            if (!Schema::hasColumn('inventory', 'nett_price')) {
                $table->decimal('nett_price', 10, 2);
            }
            if (!Schema::hasColumn('inventory', 'sell_price')) {
                $table->decimal('sell_price', 10, 2);
            }
            if (!Schema::hasColumn('inventory', 'quantity')) {
                $table->integer('quantity');
            }
            if (!Schema::hasColumn('inventory', 'min_quantity')) {
                $table->integer('min_quantity');
            }
            if (!Schema::hasColumn('inventory', 'invoice_number')) {
                $table->string('invoice_number', 255);
            }
            if (!Schema::hasColumn('inventory', 'receipt_number')) {
                $table->string('receipt_number', 255);
            }
            if (!Schema::hasColumn('inventory', 'purchase_date')) {
                $table->date('purchase_date');
            }
            if (!Schema::hasColumn('inventory', 'purchase_order_number')) {
                $table->string('purchase_order_number', 255);
            }
            if (!Schema::hasColumn('inventory', 'purchase_notes')) {
                $table->text('purchase_notes');
            }
            if (!Schema::hasColumn('inventory', 'last_stock_update')) {
                $table->date('last_stock_update');
            }
            if (!Schema::hasColumn('inventory', 'stock_added')) {
                $table->integer('stock_added')->default(0);
            }
            if (!Schema::hasColumn('inventory', 'stock_update_reason')) {
                $table->string('stock_update_reason', 255);
            }
            if (!Schema::hasColumn('inventory', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('inventory', 'updated_at')) {
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
        Schema::table('inventory', function (Blueprint $table) {
            $columns = [
                'id', 'description', 'short_code', 'vendor', 'nett_price', 'sell_price', 'quantity',
                'min_quantity', 'invoice_number', 'receipt_number', 'purchase_date', 'purchase_order_number',
                'purchase_notes', 'last_stock_update', 'stock_added', 'stock_update_reason', 'created_at', 'updated_at'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('inventory', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 