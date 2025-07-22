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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('payments', 'payment_reference')) {
                $table->string('payment_reference', 255);
            }
            if (!Schema::hasColumn('payments', 'client_id')) {
                $table->bigInteger('client_id')->unsigned();
            }
            if (!Schema::hasColumn('payments', 'invoice_jobcard_number')) {
                $table->string('invoice_jobcard_number', 255);
            }
            if (!Schema::hasColumn('payments', 'amount')) {
                $table->decimal('amount', 10, 2);
            }
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->enum('payment_method', ['cash','card','eft','cheque','payfast','other']);
            }
            if (!Schema::hasColumn('payments', 'payment_date')) {
                $table->date('payment_date');
            }
            if (!Schema::hasColumn('payments', 'reference_number')) {
                $table->string('reference_number', 255);
            }
            if (!Schema::hasColumn('payments', 'notes')) {
                $table->text('notes');
            }
            if (!Schema::hasColumn('payments', 'status')) {
                $table->enum('status', ['pending','completed','failed'])->default('completed');
            }
            if (!Schema::hasColumn('payments', 'receipt_number')) {
                $table->string('receipt_number', 255);
            }
            if (!Schema::hasColumn('payments', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('payments', 'updated_at')) {
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