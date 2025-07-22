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
        Schema::table('goods_received_vouchers', function (Blueprint $table) {
            if (!Schema::hasColumn('goods_received_vouchers', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'grv_number')) {
                $table->string('grv_number', 255);
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'purchase_order_id')) {
                $table->bigInteger('purchase_order_id')->unsigned();
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'received_date')) {
                $table->date('received_date');
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'received_time')) {
                $table->time('received_time');
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'received_by')) {
                $table->bigInteger('received_by')->unsigned();
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'checked_by')) {
                $table->bigInteger('checked_by')->unsigned();
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'delivery_note_number')) {
                $table->string('delivery_note_number', 255);
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'vehicle_registration')) {
                $table->string('vehicle_registration', 255);
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'driver_name')) {
                $table->string('driver_name', 255);
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'delivery_company')) {
                $table->string('delivery_company', 255);
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'overall_status')) {
                $table->enum('overall_status', ['complete','partial','damaged','rejected'])->default('complete');
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'general_notes')) {
                $table->text('general_notes');
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'discrepancies')) {
                $table->text('discrepancies');
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'quality_check_passed')) {
                $table->tinyInteger('quality_check_passed')->default(1);
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'quality_notes')) {
                $table->text('quality_notes');
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'delivery_note_received')) {
                $table->tinyInteger('delivery_note_received')->default(0);
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'invoice_received')) {
                $table->tinyInteger('invoice_received')->default(0);
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'photos')) {
                $table->json('photos');
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('goods_received_vouchers', 'updated_at')) {
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