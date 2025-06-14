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
            $table->decimal('hours', 5, 2)->default(0)->after('jobcard_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_jobcard', function (Blueprint $table) {
            $table->dropColumn('hours');
        });
    }
};
