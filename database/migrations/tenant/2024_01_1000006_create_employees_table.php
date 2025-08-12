<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 255);
                $table->string('surname', 255);
                $table->string('telephone', 255);
                $table->string('email', 255)->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('role', 255)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->string('password', 255);
                $table->string('remember_token', 100)->nullable();
                $table->integer('admin_level')->default(0);
                $table->boolean('is_superuser')->default(0);
                $table->string('employee_id', 255)->nullable();
                $table->string('department', 255)->nullable();
                $table->string('position', 255)->nullable();
                $table->boolean('is_active')->default(1);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamp('last_login')->nullable();
                $table->index('created_by');
                // Temporarily disabled to fix migration order
                // $table->foreign('created_by')->references('id')->on('users');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
}; 