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
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', [
                    'super_admin',
                    'admin', 
                    'manager',
                    'supervisor',
                    'artisan',
                    'staff'
                ])->default('staff')->after('email');
            }
            
            if (!Schema::hasColumn('users', 'is_superuser')) {
                $table->boolean('is_superuser')->default(false)->after('role');
            }
            
            if (!Schema::hasColumn('users', 'admin_level')) {
                $table->integer('admin_level')->default(0)->after('is_superuser');
            }
            
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('admin_level');
            }
            
            // Additional useful fields
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('photo');
            }
            
            if (!Schema::hasColumn('users', 'last_login')) {
                $table->timestamp('last_login')->nullable()->after('is_active');
            }
            
            if (!Schema::hasColumn('users', 'telephone')) {
                $table->string('telephone')->nullable()->after('last_login');
            }
            
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('telephone');
            }
            
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('department');
            }
        });
        
        // Update existing user to proper super admin
        DB::table('users')->where('id', 1)->update([
            'role' => 'super_admin',
            'is_superuser' => true,
            'admin_level' => 5, // Highest level
            'is_active' => true
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_active', 'last_login', 'telephone', 
                'department', 'position'
            ]);
        });
    }
};
