<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmployeesTableForMasterSettings extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('employees', 'is_superuser')) {
                $table->boolean('is_superuser')->default(0)->after('admin_level');
            }
            if (!Schema::hasColumn('employees', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            if (!Schema::hasColumn('employees', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
            if (!Schema::hasColumn('employees', 'last_login')) {
                $table->timestamp('last_login')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('employees', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['is_superuser', 'email_verified_at', 'remember_token', 'last_login', 'created_by']);
        });
    }
};
