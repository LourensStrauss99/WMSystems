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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_items', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('purchase_order_items', 'purchase_order_id')) {
                $table->bigInteger('purchase_order_id')->unsigned();
            }
            if (!Schema::hasColumn('purchase_order_items', 'item_name')) {
                $table->string('item_name', 255);
            }
            if (!Schema::hasColumn('purchase_order_items', 'item_code')) {
                $table->string('item_code', 255);
            }
            if (!Schema::hasColumn('purchase_order_items', 'item_description')) {
                $table->text('item_description');
            }
            if (!Schema::hasColumn('purchase_order_items', 'item_category')) {
                $table->string('item_category', 255);
            }
            if (!Schema::hasColumn('purchase_order_items', 'quantity_ordered')) {
                $table->integer('quantity_ordered');
            }
            if (!Schema::hasColumn('purchase_order_items', 'quantity_received')) {
                $table->integer('quantity_received')->default(0);
            }
            if (!Schema::hasColumn('purchase_order_items', 'quantity_outstanding')) {
                $table->integer('quantity_outstanding');
            }
            if (!Schema::hasColumn('purchase_order_items', 'unit_price')) {
                $table->decimal('unit_price', 10, 2);
            }
            if (!Schema::hasColumn('purchase_order_items', 'line_total')) {
                $table->decimal('line_total', 12, 2);
            }
            if (!Schema::hasColumn('purchase_order_items', 'unit_of_measure')) {
                $table->string('unit_of_measure', 255)->default('each');
            }
            if (!Schema::hasColumn('purchase_order_items', 'status')) {
                $table->enum('status', ['pending','partially_received','fully_received'])->default('pending');
            }
            if (!Schema::hasColumn('purchase_order_items', 'inventory_id')) {
                $table->bigInteger('inventory_id')->unsigned();
            }
            if (!Schema::hasColumn('purchase_order_items', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_order_items', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_order_items', 'description')) {
                $table->text('description');
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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $columns = [
                'id', 'purchase_order_id', 'item_name', 'item_code', 'item_description', 'item_category',
                'quantity_ordered', 'quantity_received', 'quantity_outstanding', 'unit_price', 'line_total',
                'unit_of_measure', 'status', 'inventory_id', 'created_at', 'updated_at', 'description'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('purchase_order_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 