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
        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('admin_level');
            $table->boolean('is_superuser')->default(false)->after('is_active');
            $table->string('employee_id')->nullable()->after('is_superuser');
            $table->string('department')->nullable()->after('employee_id');
            $table->string('position')->nullable()->after('department');
            $table->unsignedBigInteger('created_by')->nullable()->after('position');
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'is_superuser', 
                'employee_id',
                'department',
                'position',
                'created_by',
                'email_verified_at'
            ]);
        });
    }
};
