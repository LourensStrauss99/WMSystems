<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('jobcards_progress')) {
            Schema::create('jobcards_progress', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('jobcard_id');
                $table->text('progress_note');
                $table->timestamp('progress_date');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index('jobcard_id');
                $table->foreign('jobcard_id')->references('id')->on('jobcards');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('jobcards_progress');
    }
}; 