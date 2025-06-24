<?php
// Create migration: php artisan make:migration create_goods_received_vouchers_table

// filepath: database/migrations/xxxx_xx_xx_create_goods_received_vouchers_table.php


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
        Schema::create('goods_received_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('grv_number')->unique(); // GRV-2025-001
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            
            // Receipt Details
            $table->date('received_date');
            $table->time('received_time');
            $table->foreignId('received_by')->constrained('users'); // Who received
            $table->foreignId('checked_by')->nullable()->constrained('users'); // Who checked
            
            // Delivery Information
            $table->string('delivery_note_number')->nullable();
            $table->string('vehicle_registration')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('delivery_company')->nullable();
            
            // Status and Quality
            $table->enum('overall_status', ['complete', 'partial', 'damaged', 'rejected'])->default('complete');
            $table->text('general_notes')->nullable();
            $table->text('discrepancies')->nullable(); // Any issues found
            
            // Quality Check
            $table->boolean('quality_check_passed')->default(true);
            $table->text('quality_notes')->nullable();
            
            // Documentation
            $table->boolean('delivery_note_received')->default(false);
            $table->boolean('invoice_received')->default(false);
            $table->json('photos')->nullable(); // Store photo paths
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_received_vouchers');
    }
};
