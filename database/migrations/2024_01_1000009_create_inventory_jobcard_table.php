<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('inventory_jobcard')) {
            Schema::create('inventory_jobcard', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('inventory_id');
                $table->unsignedBigInteger('jobcard_id');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->integer('quantity')->default(1);
                $table->index('inventory_id');
                $table->index('jobcard_id');
                $table->foreign('inventory_id')->references('id')->on('inventory')->onDelete('cascade');
                $table->foreign('jobcard_id')->references('id')->on('jobcards')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('inventory_jobcard');
    }
}; 