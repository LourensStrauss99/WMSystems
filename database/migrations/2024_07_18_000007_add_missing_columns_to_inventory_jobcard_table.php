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
        Schema::table('inventory_jobcard', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_jobcard', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('inventory_jobcard', 'inventory_id')) {
                $table->bigInteger('inventory_id')->unsigned();
            }
            if (!Schema::hasColumn('inventory_jobcard', 'jobcard_id')) {
                $table->bigInteger('jobcard_id')->unsigned();
            }
            if (!Schema::hasColumn('inventory_jobcard', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('inventory_jobcard', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
            if (!Schema::hasColumn('inventory_jobcard', 'quantity')) {
                $table->integer('quantity')->default(1);
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