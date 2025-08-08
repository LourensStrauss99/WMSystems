<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('inventory')) {
            Schema::create('inventory', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('description', 255);
                $table->string('short_code', 255);
                $table->string('vendor', 255);
                $table->decimal('nett_price', 10, 2);
                $table->decimal('sell_price', 10, 2);
                $table->integer('quantity');
                $table->integer('min_quantity');
                $table->string('invoice_number', 255)->nullable();
                $table->string('receipt_number', 255)->nullable();
                $table->date('purchase_date')->nullable();
                $table->string('purchase_order_number', 255)->nullable();
                $table->text('purchase_notes')->nullable();
                $table->date('last_stock_update')->nullable();
                $table->integer('stock_added')->default(0);
                $table->string('stock_update_reason', 255)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('inventory');
    }
}; 