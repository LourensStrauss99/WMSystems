<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('jobcards_completed')) {
            Schema::create('jobcards_completed', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('jobcard_id');
                $table->timestamp('completed_at');
                $table->text('completion_note')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index('jobcard_id');
                $table->foreign('jobcard_id')->references('id')->on('jobcards');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('jobcards_completed');
    }
}; 