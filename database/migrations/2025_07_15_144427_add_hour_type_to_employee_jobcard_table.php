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
        Schema::table('employee_jobcard', function (Blueprint $table) {
            // Add hour_type column to track different types of hours
            $table->enum('hour_type', [
                'normal', 
                'overtime', 
                'weekend', 
                'public_holiday', 
                'call_out'
            ])->default('normal')->after('hours_worked');
            
            // Add hourly rate for this specific entry
            $table->decimal('hourly_rate', 8, 2)->default(0)->after('hour_type');
            
            // Add total cost for this employee's work
            $table->decimal('total_cost', 10, 2)->default(0)->after('hourly_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_jobcard', function (Blueprint $table) {
            $table->dropColumn(['hour_type', 'hourly_rate', 'total_cost']);
        });
    }
};
