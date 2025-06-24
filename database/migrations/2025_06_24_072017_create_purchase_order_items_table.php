<?php
// Create migration: php artisan make:migration create_purchase_order_items_table

// filepath: database/migrations/xxxx_xx_xx_create_purchase_order_items_table.php


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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            
            // Item details (can be existing inventory or new items)
            $table->string('item_name');
            $table->string('item_code')->nullable();
            $table->text('item_description');
            $table->string('item_category')->nullable();
            
            // Quantities
            $table->integer('quantity_ordered');
            $table->integer('quantity_received')->default(0);
            $table->integer('quantity_outstanding')->virtualAs('quantity_ordered - quantity_received');
            
            // Pricing
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 12, 2);
            
            // Units
            $table->string('unit_of_measure')->default('each'); // each, kg, liter, etc.
            
            // Status tracking
            $table->enum('status', ['pending', 'partially_received', 'fully_received'])->default('pending');
            
            // Link to inventory (if exists)
            $table->foreignId('inventory_id')->nullable()->constrained('inventory');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
