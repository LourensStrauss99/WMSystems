<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('po_number', 255)->unique();
                $table->string('status', 50);
                $table->string('supplier_name', 255);
                $table->string('supplier_contact', 255)->nullable();
                $table->string('supplier_email', 255)->nullable();
                $table->string('supplier_phone', 255)->nullable();
                $table->text('supplier_address')->nullable();
                $table->date('order_date');
                $table->date('expected_delivery_date')->nullable();
                $table->date('actual_delivery_date')->nullable();
                $table->decimal('total_amount', 12, 2)->default(0.00);
                $table->decimal('subtotal', 12, 2)->default(0.00);
                $table->decimal('vat_amount', 12, 2)->default(0.00);
                $table->decimal('vat_percent', 5, 2)->default(15.00);
                $table->decimal('grand_total', 12, 2)->default(0.00);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('notes')->nullable();
                $table->text('terms_conditions')->nullable();
                $table->string('payment_terms', 255)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unsignedBigInteger('supplier_id');
                $table->timestamp('submitted_for_approval_at')->nullable();
                $table->unsignedBigInteger('submitted_by')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->json('rejection_history')->nullable();
                $table->unsignedBigInteger('rejected_by')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->unsignedBigInteger('sent_by')->nullable();
                $table->unsignedBigInteger('amended_by')->nullable();
                $table->timestamp('amended_at')->nullable();
                $table->index('created_by');
                $table->index('approved_by');
                $table->index('supplier_id');
                $table->index('amended_by');
                $table->foreign('created_by')->references('id')->on('users');
                $table->foreign('approved_by')->references('id')->on('users');
                $table->foreign('supplier_id')->references('id')->on('suppliers');
                $table->foreign('amended_by')->references('id')->on('users');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}; 