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
        Schema::table('mobile_jobcards', function (Blueprint $table) {
            if (!Schema::hasColumn('mobile_jobcards', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('mobile_jobcards', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('mobile_jobcards', 'updated_at')) {
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
        Schema::table('mobile_jobcards', function (Blueprint $table) {
            $columns = ['id', 'created_at', 'updated_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('mobile_jobcards', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 