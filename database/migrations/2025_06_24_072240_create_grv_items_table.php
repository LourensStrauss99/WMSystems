<?php
// Create migration: php artisan make:migration create_grv_items_table

// filepath: database/migrations/xxxx_xx_xx_create_grv_items_table.php


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
        Schema::create('grv_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grv_id')->constrained('goods_received_vouchers')->onDelete('cascade');
            $table->foreignId('purchase_order_item_id')->constrained()->onDelete('cascade');
            
            // Received quantities
            $table->integer('quantity_ordered');
            $table->integer('quantity_received');
            $table->integer('quantity_rejected')->default(0);
            $table->integer('quantity_damaged')->default(0);
            
            // Quality and condition
            $table->enum('condition', ['good', 'damaged', 'defective', 'expired'])->default('good');
            $table->text('item_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Storage location
            $table->string('storage_location')->nullable();
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            
            // Link to inventory (for stock updates)
            $table->foreignId('inventory_id')->nullable()->constrained('inventory');
            $table->boolean('stock_updated')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grv_items');
    }
};
