<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('purchase_order_id');
                $table->string('item_name', 255);
                $table->string('item_code', 255)->nullable();
                $table->text('item_description');
                $table->string('item_category', 255)->nullable();
                $table->integer('quantity_ordered');
                $table->integer('quantity_received')->default(0);
                $table->integer('quantity_outstanding')->nullable(); // generated column, handled in app logic
                $table->decimal('unit_price', 10, 2);
                $table->decimal('line_total', 12, 2);
                $table->string('unit_of_measure', 255)->default('each');
                $table->enum('status', ['pending','partially_received','fully_received'])->default('pending');
                $table->unsignedBigInteger('inventory_id')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->text('description')->nullable();
                $table->index('purchase_order_id');
                $table->index('inventory_id');
                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
                $table->foreign('inventory_id')->references('id')->on('inventory');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('purchase_order_items');
    }
}; 