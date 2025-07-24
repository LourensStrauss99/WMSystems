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
        Schema::table('jobcards', function (Blueprint $table) {
            if (!Schema::hasColumn('jobcards', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('jobcards', 'inventory_id')) {
                $table->bigInteger('inventory_id')->unsigned();
            }
            if (!Schema::hasColumn('jobcards', 'quantity')) {
                $table->integer('quantity')->default(1);
            }
            if (!Schema::hasColumn('jobcards', 'jobcard_number')) {
                $table->string('jobcard_number', 255);
            }
            if (!Schema::hasColumn('jobcards', 'job_date')) {
                $table->date('job_date');
            }
            if (!Schema::hasColumn('jobcards', 'client_id')) {
                $table->bigInteger('client_id')->unsigned();
            }
            if (!Schema::hasColumn('jobcards', 'category')) {
                $table->string('category', 255);
            }
            if (!Schema::hasColumn('jobcards', 'work_request')) {
                $table->text('work_request');
            }
            if (!Schema::hasColumn('jobcards', 'special_request')) {
                $table->text('special_request');
            }
            if (!Schema::hasColumn('jobcards', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('jobcards', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
            if (!Schema::hasColumn('jobcards', 'status')) {
                $table->string('status', 255)->default('in process');
            }
            if (!Schema::hasColumn('jobcards', 'amount')) {
                $table->decimal('amount', 10, 2);
            }
            if (!Schema::hasColumn('jobcards', 'payment_date')) {
                $table->date('payment_date');
            }
            if (!Schema::hasColumn('jobcards', 'invoice_number')) {
                $table->string('invoice_number', 255);
            }
            if (!Schema::hasColumn('jobcards', 'work_done')) {
                $table->text('work_done');
            }
            if (!Schema::hasColumn('jobcards', 'time_spent')) {
                $table->integer('time_spent');
            }
            if (!Schema::hasColumn('jobcards', 'progress_note')) {
                $table->text('progress_note');
            }
            if (!Schema::hasColumn('jobcards', 'normal_hours')) {
                $table->decimal('normal_hours', 8, 2)->default(0.00);
            }
            if (!Schema::hasColumn('jobcards', 'overtime_hours')) {
                $table->decimal('overtime_hours', 8, 2)->default(0.00);
            }
            if (!Schema::hasColumn('jobcards', 'weekend_hours')) {
                $table->decimal('weekend_hours', 8, 2)->default(0.00);
            }
            if (!Schema::hasColumn('jobcards', 'public_holiday_hours')) {
                $table->decimal('public_holiday_hours', 8, 2)->default(0.00);
            }
            if (!Schema::hasColumn('jobcards', 'call_out_fee')) {
                $table->decimal('call_out_fee', 8, 2)->default(0.00);
            }
            if (!Schema::hasColumn('jobcards', 'mileage_km')) {
                $table->decimal('mileage_km', 8, 2)->default(0.00);
            }
            if (!Schema::hasColumn('jobcards', 'mileage_cost')) {
                $table->decimal('mileage_cost', 8, 2)->default(0.00);
            }
            if (!Schema::hasColumn('jobcards', 'total_labour_cost')) {
                $table->decimal('total_labour_cost', 10, 2)->default(0.00);
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
        // Drops columns if they exist. Safe to run multiple times, but may fail if columns are missing.
        Schema::table('jobcards', function (Blueprint $table) {
            $columns = [
                'id', 'inventory_id', 'quantity', 'jobcard_number', 'job_date', 'client_id', 'category',
                'work_request', 'special_request', 'created_at', 'updated_at', 'status', 'amount',
                'payment_date', 'invoice_number', 'work_done', 'time_spent', 'progress_note',
                'normal_hours', 'overtime_hours', 'weekend_hours', 'public_holiday_hours',
                'call_out_fee', 'mileage_km', 'mileage_cost', 'total_labour_cost'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('jobcards', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 