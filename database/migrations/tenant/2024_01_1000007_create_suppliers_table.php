<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 255);
                $table->string('contact_person', 255)->nullable();
                $table->string('email', 255)->nullable();
                $table->string('phone', 255)->nullable();
                $table->text('address')->nullable();
                $table->string('city', 255)->nullable();
                $table->string('postal_code', 255)->nullable();
                $table->string('vat_number', 255)->nullable();
                $table->string('account_number', 255)->nullable();
                $table->decimal('credit_limit', 12, 2)->default(0.00);
                $table->enum('payment_terms', ['cash','30_days','60_days','90_days'])->default('30_days');
                $table->boolean('active')->default(1);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
                $table->index(['name', 'active']);
                $table->index('email');
                $table->index('phone');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}; 