<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email', 255)->primary();
                $table->string('token', 255);
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('password_reset_tokens');
    }
}; 