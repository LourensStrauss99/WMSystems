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
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'po_number')) {
                $table->string('po_number', 255);
            }
            if (!Schema::hasColumn('purchase_orders', 'status')) {
                $table->string('status', 255);
            }
            if (!Schema::hasColumn('purchase_orders', 'supplier_name')) {
                $table->string('supplier_name', 255);
            }
            if (!Schema::hasColumn('purchase_orders', 'supplier_contact')) {
                $table->string('supplier_contact', 255);
            }
            if (!Schema::hasColumn('purchase_orders', 'supplier_email')) {
                $table->string('supplier_email', 255);
            }
            if (!Schema::hasColumn('purchase_orders', 'supplier_phone')) {
                $table->string('supplier_phone', 255);
            }
            if (!Schema::hasColumn('purchase_orders', 'supplier_address')) {
                $table->string('supplier_address', 255);
            }
            if (!Schema::hasColumn('purchase_orders', 'order_date')) {
                $table->date('order_date');
            }
            if (!Schema::hasColumn('purchase_orders', 'expected_delivery_date')) {
                $table->date('expected_delivery_date')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'actual_delivery_date')) {
                $table->date('actual_delivery_date')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'total_amount')) {
                $table->decimal('total_amount', 10, 2);
            }
            if (!Schema::hasColumn('purchase_orders', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('purchase_orders', 'vat_amount')) {
                $table->decimal('vat_amount', 10, 2);
            }
            if (!Schema::hasColumn('purchase_orders', 'vat_percent')) {
                $table->decimal('vat_percent', 5, 2);
            }
            if (!Schema::hasColumn('purchase_orders', 'grand_total')) {
                $table->decimal('grand_total', 10, 2);
            }
            if (!Schema::hasColumn('purchase_orders', 'created_by')) {
                $table->bigInteger('created_by')->unsigned();
            }
            if (!Schema::hasColumn('purchase_orders', 'approved_by')) {
                $table->bigInteger('approved_by')->unsigned();
            }
            if (!Schema::hasColumn('purchase_orders', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'payment_terms')) {
                $table->integer('payment_terms')->default(30);
            }
            if (!Schema::hasColumn('purchase_orders', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'supplier_id')) {
                $table->bigInteger('supplier_id')->unsigned();
            }
            if (!Schema::hasColumn('purchase_orders', 'submitted_for_approval_at')) {
                $table->timestamp('submitted_for_approval_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'submitted_by')) {
                $table->bigInteger('submitted_by')->unsigned();
            }
            if (!Schema::hasColumn('purchase_orders', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'rejection_history')) {
                $table->json('rejection_history')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'rejected_by')) {
                $table->bigInteger('rejected_by')->unsigned();
            }
            if (!Schema::hasColumn('purchase_orders', 'sent_at')) {
                $table->timestamp('sent_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'sent_by')) {
                $table->bigInteger('sent_by')->unsigned();
            }
            if (!Schema::hasColumn('purchase_orders', 'amended_by')) {
                $table->bigInteger('amended_by')->unsigned();
            }
            if (!Schema::hasColumn('purchase_orders', 'amended_at')) {
                $table->timestamp('amended_at')->nullable();
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