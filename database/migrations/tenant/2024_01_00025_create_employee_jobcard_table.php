<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('employee_jobcard')) {
            Schema::create('employee_jobcard', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('jobcard_id');
                $table->decimal('hours', 5, 2)->default(0.00);
                $table->timestamps();
                $table->integer('hours_worked')->default(0);
                $table->decimal('hourly_rate', 8, 2)->default(0.00);
                $table->decimal('total_cost', 10, 2)->default(0.00);
                
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('jobcard_id')->references('id')->on('jobcards')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('employee_jobcard');
    }
};
