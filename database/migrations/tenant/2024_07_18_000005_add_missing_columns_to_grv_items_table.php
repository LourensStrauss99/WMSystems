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
        Schema::table('grv_items', function (Blueprint $table) {
            if (!Schema::hasColumn('grv_items', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('grv_items', 'grv_id')) {
                $table->bigInteger('grv_id')->unsigned();
            }
            if (!Schema::hasColumn('grv_items', 'purchase_order_item_id')) {
                $table->bigInteger('purchase_order_item_id')->unsigned();
            }
            if (!Schema::hasColumn('grv_items', 'quantity_ordered')) {
                $table->integer('quantity_ordered');
            }
            if (!Schema::hasColumn('grv_items', 'quantity_received')) {
                $table->integer('quantity_received');
            }
            if (!Schema::hasColumn('grv_items', 'quantity_rejected')) {
                $table->integer('quantity_rejected')->default(0);
            }
            if (!Schema::hasColumn('grv_items', 'quantity_damaged')) {
                $table->integer('quantity_damaged')->default(0);
            }
            if (!Schema::hasColumn('grv_items', 'condition')) {
                $table->enum('condition', ['good','damaged','defective','expired'])->default('good');
            }
            if (!Schema::hasColumn('grv_items', 'item_notes')) {
                $table->text('item_notes');
            }
            if (!Schema::hasColumn('grv_items', 'rejection_reason')) {
                $table->text('rejection_reason');
            }
            if (!Schema::hasColumn('grv_items', 'storage_location')) {
                $table->string('storage_location', 255);
            }
            if (!Schema::hasColumn('grv_items', 'batch_number')) {
                $table->string('batch_number', 255);
            }
            if (!Schema::hasColumn('grv_items', 'expiry_date')) {
                $table->date('expiry_date');
            }
            if (!Schema::hasColumn('grv_items', 'inventory_id')) {
                $table->bigInteger('inventory_id')->unsigned();
            }
            if (!Schema::hasColumn('grv_items', 'stock_updated')) {
                $table->tinyInteger('stock_updated')->default(0);
            }
            if (!Schema::hasColumn('grv_items', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('grv_items', 'updated_at')) {
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
        Schema::table('grv_items', function (Blueprint $table) {
            $columns = [
                'id', 'grv_id', 'purchase_order_item_id', 'quantity_ordered', 'quantity_received',
                'quantity_rejected', 'quantity_damaged', 'condition', 'item_notes', 'rejection_reason',
                'storage_location', 'batch_number', 'expiry_date', 'inventory_id', 'stock_updated',
                'created_at', 'updated_at'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('grv_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 