<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('user_id', 255)->nullable()->unique();
                $table->string('name', 255);
                $table->string('surname', 255)->nullable();
                $table->string('email', 255)->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password', 255);
                $table->string('remember_token', 100)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->enum('role', ['super_admin','admin','manager','supervisor','artisan','staff','user'])->default('user');
                $table->boolean('is_superuser')->default(0);
                $table->integer('admin_level')->default(0);
                $table->string('employee_id', 255)->nullable()->unique();
                $table->string('department', 255)->nullable();
                $table->string('position', 255)->nullable();
                $table->string('telephone', 255)->nullable();
                $table->boolean('is_active')->default(1);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamp('last_login')->nullable();
                $table->string('phone', 255)->nullable();
                $table->text('address')->nullable();
                $table->timestamp('phone_verified_at')->nullable();
                $table->string('verification_code', 6)->nullable();
                $table->boolean('bypass_verification')->default(0);
                $table->boolean('is_first_user')->default(0);
                $table->string('photo', 255)->nullable();
                $table->index('created_by');
                $table->foreign('created_by')->references('id')->on('users');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}; 