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
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (!Schema::hasColumn('password_reset_tokens', 'email')) {
                $table->string('email', 255);
            }
            if (!Schema::hasColumn('password_reset_tokens', 'token')) {
                $table->string('token', 255);
            }
            if (!Schema::hasColumn('password_reset_tokens', 'created_at')) {
                $table->timestamp('created_at')->nullable();
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
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $columns = ['email', 'token', 'created_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('password_reset_tokens', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 