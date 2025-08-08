<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('goods_received_vouchers')) {
            Schema::create('goods_received_vouchers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('grv_number', 255)->unique();
                $table->unsignedBigInteger('purchase_order_id');
                $table->date('received_date');
                $table->time('received_time');
                $table->unsignedBigInteger('received_by');
                $table->unsignedBigInteger('checked_by')->nullable();
                $table->string('delivery_note_number', 255)->nullable();
                $table->string('vehicle_registration', 255)->nullable();
                $table->string('driver_name', 255)->nullable();
                $table->string('delivery_company', 255)->nullable();
                $table->enum('overall_status', ['complete','partial','damaged','rejected'])->default('complete');
                $table->text('general_notes')->nullable();
                $table->text('discrepancies')->nullable();
                $table->boolean('quality_check_passed')->default(1);
                $table->text('quality_notes')->nullable();
                $table->boolean('delivery_note_received')->default(0);
                $table->boolean('invoice_received')->default(0);
                $table->json('photos')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index('purchase_order_id');
                $table->index('received_by');
                $table->index('checked_by');
                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
                $table->foreign('received_by')->references('id')->on('users');
                $table->foreign('checked_by')->references('id')->on('users');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('goods_received_vouchers');
    }
}; 