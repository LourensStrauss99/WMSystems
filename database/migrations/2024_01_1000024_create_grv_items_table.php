<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('grv_items')) {
            Schema::create('grv_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('grv_id');
                $table->unsignedBigInteger('purchase_order_item_id');
                $table->integer('quantity_ordered');
                $table->integer('quantity_received');
                $table->integer('quantity_rejected')->default(0);
                $table->integer('quantity_damaged')->default(0);
                $table->enum('condition', ['good','damaged','defective','expired'])->default('good');
                $table->text('item_notes')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->string('storage_location', 255)->nullable();
                $table->string('batch_number', 255)->nullable();
                $table->date('expiry_date')->nullable();
                $table->unsignedBigInteger('inventory_id')->nullable();
                $table->boolean('stock_updated')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index('grv_id');
                $table->index('purchase_order_item_id');
                $table->index('inventory_id');
                $table->foreign('grv_id')->references('id')->on('goods_received_vouchers')->onDelete('cascade');
                $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items')->onDelete('cascade');
                $table->foreign('inventory_id')->references('id')->on('inventory');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('grv_items');
    }
}; 