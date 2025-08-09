<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('clients')) {
            Schema::create('clients', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 255);
                $table->string('surname', 255);
                $table->string('telephone', 255);
                $table->string('address', 255);
                $table->text('notes')->nullable();
                $table->boolean('is_active')->default(1);
                $table->timestamp('last_activity')->nullable();
                $table->string('inactive_reason', 255)->nullable();
                $table->string('email', 255)->unique();
                $table->string('payment_reference', 255)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
}; 