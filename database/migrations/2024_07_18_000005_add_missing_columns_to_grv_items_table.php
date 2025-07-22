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
        // No columns are dropped in down() to avoid data loss.
    }
}; 