<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Only add columns that don't exist
            if (!Schema::hasColumn('users', 'is_first_user')) {
                $table->boolean('is_first_user')->default(false)->after('admin_level');
            }
            if (!Schema::hasColumn('users', 'last_login')) {
                $table->timestamp('last_login')->nullable()->after('is_first_user');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('last_login');
            }
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('employee_id');
            }
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('department');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('position');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
        });

        // Update existing role column if needed - modify the enum values
        if (Schema::hasColumn('users', 'role')) {
            // Check if we need to modify the role enum to include our values
            $currentRoles = DB::select("SHOW COLUMNS FROM users WHERE Field = 'role'")[0];
            
            // If the role column is not already an enum with our values, modify it
            if (!str_contains($currentRoles->Type, 'super_admin')) {
                DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'manager', 'supervisor', 'artisan', 'staff', 'user') DEFAULT 'user'");
            }
        }

        // Ensure first user is set as superuser (using your existing schema)
        $firstUser = \App\Models\User::oldest()->first();
        if ($firstUser) {
            $firstUser->update([
                'is_superuser' => true,
                'admin_level' => 5,
                'role' => 'super_admin',
                'is_first_user' => true,
                'is_active' => true
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToCheck = [
                'is_first_user', 'last_login', 'is_active', 'created_by',
                'employee_id', 'department', 'position', 'phone', 'address'
            ];

            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
