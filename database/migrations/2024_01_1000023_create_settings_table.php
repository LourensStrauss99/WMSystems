<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->decimal('labour_rate', 10, 2)->nullable();
                $table->decimal('vat_percent', 5, 2)->nullable();
                $table->string('company_name', 255)->nullable();
                $table->string('company_reg_number', 255)->nullable();
                $table->string('vat_reg_number', 255)->nullable();
                $table->string('bank_name', 255)->nullable();
                $table->string('account_holder', 255)->nullable();
                $table->string('account_number', 255)->nullable();
                $table->string('branch_code', 255)->nullable();
                $table->string('swift_code', 255)->nullable();
                $table->string('address', 255)->nullable();
                $table->string('city', 255)->nullable();
                $table->string('province', 255)->nullable();
                $table->string('postal_code', 255)->nullable();
                $table->string('country', 255)->nullable();
                $table->string('company_telephone', 255)->nullable();
                $table->string('company_email', 255)->nullable();
                $table->string('company_website', 255)->nullable();
                $table->string('invoice_terms', 255)->nullable();
                $table->text('invoice_footer')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}; 