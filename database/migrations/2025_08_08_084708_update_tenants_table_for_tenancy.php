<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Add only missing columns for our requirements
            if (!Schema::hasColumn('tenants', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('status');
            }
            if (!Schema::hasColumn('tenants', 'owner_password')) {
                $table->string('owner_password')->nullable()->after('owner_email');
            }
            if (!Schema::hasColumn('tenants', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('owner_password');
            }
            if (!Schema::hasColumn('tenants', 'verification_token')) {
                $table->string('verification_token')->nullable()->after('email_verified_at');
            }
            if (!Schema::hasColumn('tenants', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('verification_token');
            }
            if (!Schema::hasColumn('tenants', 'monthly_fee')) {
                $table->decimal('monthly_fee', 8, 2)->default(0.00)->after('payment_status');
            }
            if (!Schema::hasColumn('tenants', 'next_payment_due')) {
                $table->date('next_payment_due')->nullable()->after('monthly_fee');
            }
            if (!Schema::hasColumn('tenants', 'last_payment_date')) {
                $table->timestamp('last_payment_date')->nullable()->after('next_payment_due');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $columns = ['is_active', 'owner_password', 'email_verified_at', 'verification_token', 
                       'payment_status', 'monthly_fee', 'next_payment_due', 'last_payment_date'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('tenants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
