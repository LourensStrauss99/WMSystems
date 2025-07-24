<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('users', 'user_id')) {
                $table->string('user_id', 255);
            }
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name', 255);
            }
            if (!Schema::hasColumn('users', 'surname')) {
                $table->string('surname', 255);
            }
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email', 255);
            }
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password', 255);
            }
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->string('remember_token', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['super_admin','admin','manager','supervisor','artisan','staff','user'])->default('user');
            }
            if (!Schema::hasColumn('users', 'is_superuser')) {
                $table->tinyInteger('is_superuser')->default(0);
            }
            if (!Schema::hasColumn('users', 'admin_level')) {
                $table->integer('admin_level')->default(0);
            }
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id', 255);
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department', 255);
            }
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position', 255);
            }
            if (!Schema::hasColumn('users', 'telephone')) {
                $table->string('telephone', 255);
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->tinyInteger('is_active')->default(1);
            }
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->bigInteger('created_by')->unsigned();
            }
            if (!Schema::hasColumn('users', 'last_login')) {
                $table->timestamp('last_login')->nullable();
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 255);
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address');
            }
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'verification_code')) {
                $table->string('verification_code', 6);
            }
            if (!Schema::hasColumn('users', 'bypass_verification')) {
                $table->tinyInteger('bypass_verification')->default(0);
            }
            if (!Schema::hasColumn('users', 'is_first_user')) {
                $table->tinyInteger('is_first_user')->default(0);
            }
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo', 255);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drops columns if they exist. Safe to run multiple times, but may fail if columns are missing.
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'id', 'user_id', 'name', 'surname', 'email', 'email_verified_at', 'password',
                'remember_token', 'created_at', 'updated_at', 'role', 'is_superuser', 'admin_level',
                'employee_id', 'department', 'position', 'telephone', 'is_active', 'created_by',
                'last_login', 'phone', 'address', 'phone_verified_at', 'verification_code',
                'bypass_verification', 'is_first_user', 'photo'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 