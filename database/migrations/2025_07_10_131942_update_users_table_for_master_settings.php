<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableForMasterSettings extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('users', 'surname')) {
                $table->string('surname')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id')->unique()->nullable()->after('admin_level');
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('employee_id');
            }
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('department');
            }
            if (!Schema::hasColumn('users', 'telephone')) {
                $table->string('telephone')->nullable()->after('position');
            }
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['surname', 'employee_id', 'department', 'position', 'telephone', 'created_by']);
        });
    }
};
