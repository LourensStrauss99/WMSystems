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
        Schema::table('jobcards_completed', function (Blueprint $table) {
            if (!Schema::hasColumn('jobcards_completed', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('jobcards_completed', 'jobcard_id')) {
                $table->bigInteger('jobcard_id')->unsigned();
            }
            if (!Schema::hasColumn('jobcards_completed', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
            if (!Schema::hasColumn('jobcards_completed', 'completion_note')) {
                $table->text('completion_note');
            }
            if (!Schema::hasColumn('jobcards_completed', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('jobcards_completed', 'updated_at')) {
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
        // Drops columns if they exist. Safe to run multiple times, but may fail if columns are missing.
        Schema::table('jobcards_completed', function (Blueprint $table) {
            $columns = ['id', 'jobcard_id', 'completed_at', 'completion_note', 'created_at', 'updated_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('jobcards_completed', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 