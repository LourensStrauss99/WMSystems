<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('jobcards')) {
            Schema::create('jobcards', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('inventory_id')->nullable();
                $table->integer('quantity')->default(1);
                $table->string('jobcard_number', 255)->unique();
                $table->date('job_date');
                $table->unsignedBigInteger('client_id');
                $table->string('category', 255);
                $table->text('work_request');
                $table->text('special_request')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->string('status', 255)->default('in process');
                $table->decimal('amount', 10, 2)->nullable();
                $table->date('payment_date')->nullable();
                $table->string('invoice_number', 255)->nullable();
                $table->text('work_done')->nullable();
                $table->integer('time_spent')->nullable();
                $table->text('progress_note')->nullable();
                $table->decimal('normal_hours', 8, 2)->default(0.00);
                $table->decimal('overtime_hours', 8, 2)->default(0.00);
                $table->decimal('weekend_hours', 8, 2)->default(0.00);
                $table->decimal('public_holiday_hours', 8, 2)->default(0.00);
                $table->decimal('call_out_fee', 8, 2)->default(0.00);
                $table->decimal('mileage_km', 8, 2)->default(0.00);
                $table->decimal('mileage_cost', 8, 2)->default(0.00);
                $table->decimal('total_labour_cost', 10, 2)->default(0.00);
                $table->index('client_id');
                $table->foreign('client_id')->references('id')->on('clients');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('jobcards');
    }
}; 