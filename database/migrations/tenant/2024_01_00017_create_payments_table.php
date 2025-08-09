<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('payment_reference', 255);
                $table->unsignedBigInteger('client_id');
                $table->string('invoice_jobcard_number', 255)->nullable();
                $table->decimal('amount', 10, 2);
                $table->enum('payment_method', ['cash','card','eft','cheque','payfast','other']);
                $table->date('payment_date');
                $table->string('reference_number', 255)->nullable();
                $table->text('notes')->nullable();
                $table->enum('status', ['pending','completed','failed'])->default('completed');
                $table->string('receipt_number', 255)->unique();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['client_id', 'payment_date']);
                $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}; 