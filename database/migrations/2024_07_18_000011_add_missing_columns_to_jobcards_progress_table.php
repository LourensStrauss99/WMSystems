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
        Schema::table('jobcards_progress', function (Blueprint $table) {
            if (!Schema::hasColumn('jobcards_progress', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('jobcards_progress', 'jobcard_id')) {
                $table->bigInteger('jobcard_id')->unsigned();
            }
            if (!Schema::hasColumn('jobcards_progress', 'progress_note')) {
                $table->text('progress_note');
            }
            if (!Schema::hasColumn('jobcards_progress', 'progress_date')) {
                $table->timestamp('progress_date')->nullable();
            }
            if (!Schema::hasColumn('jobcards_progress', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('jobcards_progress', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
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