<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('quotes')) {
            Schema::create('quotes', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('client_name', 255);
                $table->string('client_address', 255)->nullable();
                $table->string('client_email', 255)->nullable();
                $table->string('client_telephone', 255)->nullable();
                $table->string('quote_number', 255)->unique();
                $table->date('quote_date');
                $table->json('items');
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('quotes');
    }
}; 