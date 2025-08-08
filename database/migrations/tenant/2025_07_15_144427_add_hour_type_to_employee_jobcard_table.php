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
            if (!Schema::hasColumn('employee_jobcard', 'hour_type')) {
                $table->enum('hour_type', [
                    'normal', 
                    'overtime', 
                    'weekend', 
                    'public_holiday', 
                    'call_out'
                ])->default('normal')->after('hours_worked');
            }
            // Add hourly rate for this specific entry
            if (!Schema::hasColumn('employee_jobcard', 'hourly_rate')) {
                $table->decimal('hourly_rate', 8, 2)->default(0)->after('hour_type');
            }
            // Add total cost for this employee's work
            if (!Schema::hasColumn('employee_jobcard', 'total_cost')) {
                $table->decimal('total_cost', 10, 2)->default(0)->after('hourly_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drops columns if they exist. Safe to run multiple times, but may fail if columns are missing.
        Schema::table('employee_jobcard', function (Blueprint $table) {
            $columns = ['hour_type', 'hourly_rate', 'total_cost'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('employee_jobcard', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
