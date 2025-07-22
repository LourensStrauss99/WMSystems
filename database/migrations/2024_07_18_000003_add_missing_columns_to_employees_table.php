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
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('employees', 'name')) {
                $table->string('name', 255);
            }
            if (!Schema::hasColumn('employees', 'surname')) {
                $table->string('surname', 255);
            }
            if (!Schema::hasColumn('employees', 'telephone')) {
                $table->string('telephone', 255);
            }
            if (!Schema::hasColumn('employees', 'email')) {
                $table->string('email', 255);
            }
            if (!Schema::hasColumn('employees', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
            if (!Schema::hasColumn('employees', 'role')) {
                $table->string('role', 255)->nullable();
            }
            if (!Schema::hasColumn('employees', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('employees', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
            if (!Schema::hasColumn('employees', 'password')) {
                $table->string('password', 255);
            }
            if (!Schema::hasColumn('employees', 'remember_token')) {
                $table->string('remember_token', 100)->nullable();
            }
            if (!Schema::hasColumn('employees', 'admin_level')) {
                $table->integer('admin_level')->default(0);
            }
            if (!Schema::hasColumn('employees', 'is_superuser')) {
                $table->tinyInteger('is_superuser')->default(0);
            }
            if (!Schema::hasColumn('employees', 'employee_id')) {
                $table->string('employee_id', 255);
            }
            if (!Schema::hasColumn('employees', 'department')) {
                $table->string('department', 255)->nullable();
            }
            if (!Schema::hasColumn('employees', 'position')) {
                $table->string('position', 255)->nullable();
            }
            if (!Schema::hasColumn('employees', 'is_active')) {
                $table->tinyInteger('is_active')->default(1);
            }
            if (!Schema::hasColumn('employees', 'created_by')) {
                $table->bigInteger('created_by')->unsigned();
            }
            if (!Schema::hasColumn('employees', 'last_login')) {
                $table->timestamp('last_login')->nullable();
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
        // No columns are dropped in down() to avoid data loss.
    }
}; 