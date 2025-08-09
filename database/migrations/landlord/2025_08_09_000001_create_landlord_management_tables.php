<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Subscription Packages
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('monthly_price', 10, 2);
            $table->decimal('yearly_price', 10, 2)->nullable();
            $table->integer('max_users')->default(5);
            $table->bigInteger('storage_limit_mb')->default(1000);
            $table->json('features');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Landlord Invoices (separate from tenant invoices)
        Schema::create('landlord_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('ZAR');
            $table->string('status')->default('pending'); // pending, paid, overdue, cancelled
            $table->date('invoice_date');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->string('billing_period'); // monthly, yearly, etc.
            $table->text('description')->nullable();
            $table->json('line_items');
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['status', 'due_date']);
        });

        // Landlord Payments (separate from tenant payments)
        Schema::create('landlord_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_reference')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('ZAR');
            $table->string('payment_method'); // bank_transfer, credit_card, cash, etc.
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('landlord_invoices')->onDelete('set null');
        });

        // Tenant-Landlord Communications
        Schema::create('tenant_communications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('type'); // support, billing, announcement, maintenance
            $table->string('subject');
            $table->text('message');
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->string('status')->default('open'); // open, in_progress, resolved, closed
            $table->unsignedBigInteger('initiated_by_user_id');
            $table->unsignedBigInteger('assigned_to_user_id')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('initiated_by_user_id')->references('id')->on('users');
            $table->foreign('assigned_to_user_id')->references('id')->on('users');
        });

        // Communication Messages (replies)
        Schema::create('communication_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('communication_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_tenant_user')->default(false);
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->foreign('communication_id')->references('id')->on('tenant_communications')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });

        // Service Usage Tracking
        Schema::create('tenant_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('metric_type'); // storage, users, api_calls, etc.
            $table->bigInteger('metric_value');
            $table->string('metric_unit'); // mb, count, etc.
            $table->date('log_date');
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'metric_type', 'log_date']);
        });

        // Tenant Status History
        Schema::create('tenant_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('previous_status');
            $table->string('new_status');
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('changed_by_user_id');
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('changed_by_user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_status_history');
        Schema::dropIfExists('tenant_usage_logs');
        Schema::dropIfExists('communication_messages');
        Schema::dropIfExists('tenant_communications');
        Schema::dropIfExists('landlord_payments');
        Schema::dropIfExists('landlord_invoices');
        Schema::dropIfExists('subscription_packages');
    }
};
