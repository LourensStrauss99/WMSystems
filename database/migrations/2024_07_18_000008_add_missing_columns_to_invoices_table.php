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
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('invoices', 'jobcard_id')) {
                $table->bigInteger('jobcard_id')->unsigned();
            }
            if (!Schema::hasColumn('invoices', 'client_id')) {
                $table->bigInteger('client_id')->unsigned();
            }
            if (!Schema::hasColumn('invoices', 'invoice_number')) {
                $table->string('invoice_number', 255);
            }
            if (!Schema::hasColumn('invoices', 'amount')) {
                $table->decimal('amount', 10, 2);
            }
            if (!Schema::hasColumn('invoices', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('invoices', 'outstanding_amount')) {
                $table->decimal('outstanding_amount', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('invoices', 'invoice_date')) {
                $table->date('invoice_date');
            }
            if (!Schema::hasColumn('invoices', 'due_date')) {
                $table->date('due_date');
            }
            if (!Schema::hasColumn('invoices', 'payment_date')) {
                $table->date('payment_date');
            }
            if (!Schema::hasColumn('invoices', 'status')) {
                $table->enum('status', ['unpaid','partial','paid','overdue'])->default('unpaid');
            }
            if (!Schema::hasColumn('invoices', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('invoices', 'updated_at')) {
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