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
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('clients', 'name')) {
                $table->string('name', 255);
            }
            if (!Schema::hasColumn('clients', 'surname')) {
                $table->string('surname', 255);
            }
            if (!Schema::hasColumn('clients', 'telephone')) {
                $table->string('telephone', 255);
            }
            if (!Schema::hasColumn('clients', 'address')) {
                $table->string('address', 255);
            }
            if (!Schema::hasColumn('clients', 'notes')) {
                $table->text('notes');
            }
            if (!Schema::hasColumn('clients', 'is_active')) {
                $table->tinyInteger('is_active')->default(1);
            }
            if (!Schema::hasColumn('clients', 'last_activity')) {
                $table->timestamp('last_activity')->nullable();
            }
            if (!Schema::hasColumn('clients', 'inactive_reason')) {
                $table->string('inactive_reason', 255)->nullable();
            }
            if (!Schema::hasColumn('clients', 'email')) {
                $table->string('email', 255);
            }
            if (!Schema::hasColumn('clients', 'payment_reference')) {
                $table->string('payment_reference', 255);
            }
            if (!Schema::hasColumn('clients', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('clients', 'updated_at')) {
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