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
        Schema::table('quotes', function (Blueprint $table) {
            if (!Schema::hasColumn('quotes', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('quotes', 'client_name')) {
                $table->string('client_name', 255);
            }
            if (!Schema::hasColumn('quotes', 'client_address')) {
                $table->string('client_address', 255);
            }
            if (!Schema::hasColumn('quotes', 'client_email')) {
                $table->string('client_email', 255);
            }
            if (!Schema::hasColumn('quotes', 'client_telephone')) {
                $table->string('client_telephone', 255);
            }
            if (!Schema::hasColumn('quotes', 'quote_number')) {
                $table->string('quote_number', 255);
            }
            if (!Schema::hasColumn('quotes', 'quote_date')) {
                $table->date('quote_date');
            }
            if (!Schema::hasColumn('quotes', 'items')) {
                $table->json('items');
            }
            if (!Schema::hasColumn('quotes', 'notes')) {
                $table->text('notes');
            }
            if (!Schema::hasColumn('quotes', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('quotes', 'updated_at')) {
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
        Schema::table('quotes', function (Blueprint $table) {
            $columns = [
                'id', 'client_name', 'client_address', 'client_email', 'client_telephone',
                'quote_number', 'quote_date', 'items', 'notes', 'created_at', 'updated_at'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('quotes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 