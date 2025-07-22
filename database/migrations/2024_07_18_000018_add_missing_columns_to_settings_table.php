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
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'id')) {
                $table->bigIncrements('id')->unsigned();
            }
            if (!Schema::hasColumn('settings', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('settings', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
            if (!Schema::hasColumn('settings', 'labour_rate')) {
                $table->decimal('labour_rate', 10, 2);
            }
            if (!Schema::hasColumn('settings', 'vat_percent')) {
                $table->decimal('vat_percent', 5, 2);
            }
            if (!Schema::hasColumn('settings', 'company_name')) {
                $table->string('company_name', 255);
            }
            if (!Schema::hasColumn('settings', 'company_reg_number')) {
                $table->string('company_reg_number', 255);
            }
            if (!Schema::hasColumn('settings', 'vat_reg_number')) {
                $table->string('vat_reg_number', 255);
            }
            if (!Schema::hasColumn('settings', 'bank_name')) {
                $table->string('bank_name', 255);
            }
            if (!Schema::hasColumn('settings', 'account_holder')) {
                $table->string('account_holder', 255);
            }
            if (!Schema::hasColumn('settings', 'account_number')) {
                $table->string('account_number', 255);
            }
            if (!Schema::hasColumn('settings', 'branch_code')) {
                $table->string('branch_code', 255);
            }
            if (!Schema::hasColumn('settings', 'swift_code')) {
                $table->string('swift_code', 255);
            }
            if (!Schema::hasColumn('settings', 'address')) {
                $table->string('address', 255);
            }
            if (!Schema::hasColumn('settings', 'city')) {
                $table->string('city', 255);
            }
            if (!Schema::hasColumn('settings', 'province')) {
                $table->string('province', 255);
            }
            if (!Schema::hasColumn('settings', 'postal_code')) {
                $table->string('postal_code', 255);
            }
            if (!Schema::hasColumn('settings', 'country')) {
                $table->string('country', 255);
            }
            if (!Schema::hasColumn('settings', 'company_telephone')) {
                $table->string('company_telephone', 255);
            }
            if (!Schema::hasColumn('settings', 'company_email')) {
                $table->string('company_email', 255);
            }
            if (!Schema::hasColumn('settings', 'company_website')) {
                $table->string('company_website', 255);
            }
            if (!Schema::hasColumn('settings', 'invoice_terms')) {
                $table->string('invoice_terms', 255);
            }
            if (!Schema::hasColumn('settings', 'invoice_footer')) {
                $table->text('invoice_footer');
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