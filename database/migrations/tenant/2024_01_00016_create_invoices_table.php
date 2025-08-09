<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('jobcard_id');
                $table->unsignedBigInteger('client_id');
                $table->string('invoice_number', 255)->unique();
                $table->decimal('amount', 10, 2)->nullable();
                $table->decimal('paid_amount', 10, 2)->default(0.00);
                $table->decimal('outstanding_amount', 10, 2)->default(0.00);
                $table->date('invoice_date');
                $table->date('due_date')->nullable();
                $table->date('payment_date')->nullable();
                $table->enum('status', ['unpaid','partial','paid','overdue'])->default('unpaid');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index('client_id');
                $table->index('jobcard_id');
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
                $table->foreign('jobcard_id')->references('id')->on('jobcards')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}; 