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
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('suppliers', 'name')) {
                $table->string('name', 255);
            }
            if (!Schema::hasColumn('suppliers', 'contact_person')) {
                $table->string('contact_person', 255);
            }
            if (!Schema::hasColumn('suppliers', 'email')) {
                $table->string('email', 255);
            }
            if (!Schema::hasColumn('suppliers', 'phone')) {
                $table->string('phone', 255);
            }
            if (!Schema::hasColumn('suppliers', 'address')) {
                $table->text('address');
            }
            if (!Schema::hasColumn('suppliers', 'city')) {
                $table->string('city', 255);
            }
            if (!Schema::hasColumn('suppliers', 'postal_code')) {
                $table->string('postal_code', 255);
            }
            if (!Schema::hasColumn('suppliers', 'vat_number')) {
                $table->string('vat_number', 255);
            }
            if (!Schema::hasColumn('suppliers', 'account_number')) {
                $table->string('account_number', 255);
            }
            if (!Schema::hasColumn('suppliers', 'credit_limit')) {
                $table->decimal('credit_limit', 12, 2)->default(0.00);
            }
            if (!Schema::hasColumn('suppliers', 'payment_terms')) {
                $table->enum('payment_terms', ['cash','30_days','60_days','90_days'])->default('30_days');
            }
            if (!Schema::hasColumn('suppliers', 'active')) {
                $table->tinyInteger('active')->default(1);
            }
            if (!Schema::hasColumn('suppliers', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
            if (!Schema::hasColumn('suppliers', 'deleted_at')) {
                $table->timestamp('deleted_at')->nullable();
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
        Schema::table('suppliers', function (Blueprint $table) {
            $columns = [
                'id', 'name', 'contact_person', 'email', 'phone', 'address', 'city', 'postal_code',
                'vat_number', 'account_number', 'credit_limit', 'payment_terms', 'active',
                'created_at', 'updated_at', 'deleted_at'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('suppliers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 