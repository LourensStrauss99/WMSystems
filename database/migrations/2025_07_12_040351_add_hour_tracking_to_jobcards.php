<?php
// Create migration: php artisan make:migration add_hour_tracking_to_jobcards


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
        Schema::table('jobcards', function (Blueprint $table) {
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
     */
    public function down(): void
    {
        Schema::table('jobcards', function (Blueprint $table) {
            $table->dropColumn([
                'normal_hours', 'overtime_hours', 'weekend_hours', 
                'public_holiday_hours', 'call_out_fee', 'mileage_km', 
                'mileage_cost', 'total_labour_cost'
            ]);
        });
    }
};
