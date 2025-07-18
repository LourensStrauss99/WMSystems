<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mobile_jobcards', function (Blueprint $table) {
            $table->id();
            $table->string('jobcard_number')->nullable();
            $table->date('job_date')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('category')->nullable();
            $table->text('work_request')->nullable();
            $table->text('special_request')->nullable();
            $table->string('status')->nullable();
            $table->text('work_done')->nullable();
            $table->decimal('time_spent', 5, 2)->nullable();
            $table->text('progress_note')->nullable();
            $table->decimal('normal_hours', 5, 2)->nullable();
            $table->decimal('overtime_hours', 5, 2)->nullable();
            $table->decimal('weekend_hours', 5, 2)->nullable();
            $table->decimal('public_holiday_hours', 5, 2)->nullable();
            $table->decimal('call_out_fee', 10, 2)->nullable();
            $table->decimal('mileage_km', 8, 2)->nullable();
            $table->decimal('mileage_cost', 10, 2)->nullable();
            $table->decimal('total_labour_cost', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_jobcards');
    }
};
