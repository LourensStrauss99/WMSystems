<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Align tenants table with required columns (non-destructive, only add if missing)
        if (Schema::hasTable('tenants')) {
            Schema::table('tenants', function (Blueprint $table) {
                if (!Schema::hasColumn('tenants', 'data')) {
                    $table->json('data')->nullable();
                }
                if (!Schema::hasColumn('tenants', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (!Schema::hasColumn('tenants', 'owner_password')) {
                    $table->string('owner_password')->nullable();
                }
                if (!Schema::hasColumn('tenants', 'email_verified_at')) {
                    $table->timestamp('email_verified_at')->nullable();
                }
                if (!Schema::hasColumn('tenants', 'verification_token')) {
                    $table->string('verification_token')->nullable();
                }
                if (!Schema::hasColumn('tenants', 'payment_status')) {
                    $table->string('payment_status')->default('pending');
                }
                if (!Schema::hasColumn('tenants', 'monthly_fee')) {
                    $table->decimal('monthly_fee', 10, 2)->default(0);
                }
                if (!Schema::hasColumn('tenants', 'next_payment_due')) {
                    $table->date('next_payment_due')->nullable();
                }
                if (!Schema::hasColumn('tenants', 'last_payment_date')) {
                    $table->timestamp('last_payment_date')->nullable();
                }
            });
        }

        // Payment history table for tenants
        if (!Schema::hasTable('tenant_payments')) {
            Schema::create('tenant_payments', function (Blueprint $table) {
                $table->bigIncrements('id');
                // Using unsignedInteger to better match typical tenants.id increments()
                $table->unsignedInteger('tenant_id');
                $table->decimal('amount', 10, 2);
                $table->string('reference')->nullable();
                $table->string('method')->nullable(); // e.g. eft, card, cash
                $table->string('status')->default('paid'); // paid, pending, failed, refunded
                $table->timestamp('paid_at')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                // Add FK if possible; if it fails due to type mismatch, it's still fine without FK
                // We'll wrap in a try/catch once executed by the framework
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tenant_payments')) {
            Schema::dropIfExists('tenant_payments');
        }

        if (Schema::hasTable('tenants')) {
            Schema::table('tenants', function (Blueprint $table) {
                $columns = [
                    'data', 'is_active', 'owner_password', 'email_verified_at', 'verification_token',
                    'payment_status', 'monthly_fee', 'next_payment_due', 'last_payment_date',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('tenants', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
