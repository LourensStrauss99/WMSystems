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
            $table->decimal('travel_km', 8, 2)->nullable()->after('hour_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_jobcard', function (Blueprint $table) {
            $table->dropColumn('travel_km');
        });
    }
};
