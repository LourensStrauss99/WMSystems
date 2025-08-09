<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Landlord Invoices (separate from tenant invoices)
        Schema::create('landlord_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('tenant_id'); // Back to unsignedBigInteger
            $table->decimal('amount', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('ZAR');
            $table->string('status')->default('pending'); // pending, paid, overdue, cancelled
            $table->date('invoice_date');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->string('billing_period')->nullable(); // monthly, yearly, etc.
            $table->text('description')->nullable();
            $table->json('line_items')->nullable(); // subscription details, add-ons, etc.
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['status', 'due_date']);
        });

        // Landlord Payments (separate from tenant payments)
        Schema::create('landlord_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_reference')->unique();
            $table->unsignedBigInteger('tenant_id'); // Back to unsignedBigInteger
            $table->unsignedBigInteger('landlord_invoice_id')->nullable(); // Updated column name
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('ZAR');
            $table->string('payment_method'); // bank_transfer, credit_card, cash, etc.
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->date('payment_date');
            $table->string('transaction_id')->nullable();
            $table->decimal('exchange_rate', 8, 4)->default(1.0000);
            $table->decimal('fees', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->nullable();
            $table->json('processor_response')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('landlord_invoice_id')->references('id')->on('landlord_invoices')->onDelete('set null');
        });

        // Subscription Packages
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('monthly_price', 10, 2);
            $table->decimal('yearly_price', 10, 2)->nullable();
            $table->integer('max_users')->default(5);
            $table->bigInteger('storage_limit_mb')->default(1000); // 1GB default
            $table->json('features'); // list of included features
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Tenant-Landlord Communications
        Schema::create('tenant_communications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id'); // Back to unsignedBigInteger
            $table->unsignedBigInteger('initiated_by_user_id'); // landlord user who started
            $table->unsignedBigInteger('assigned_to_user_id')->nullable(); // landlord user assigned
            $table->string('subject');
            $table->string('category')->default('general'); // support, billing, announcement, maintenance, general
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('open'); // open, pending, resolved, closed
            $table->json('tags')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('initiated_by_user_id')->references('id')->on('users');
            $table->foreign('assigned_to_user_id')->references('id')->on('users');
        });

        // Communication Messages (replies)
        Schema::create('communication_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('communication_id');
            $table->unsignedBigInteger('user_id'); // who sent the message
            $table->boolean('is_tenant_user')->default(false); // true if sent by tenant user
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->foreign('communication_id')->references('id')->on('tenant_communications')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('communication_messages');
        Schema::dropIfExists('tenant_communications');
        Schema::dropIfExists('subscription_packages');
        Schema::dropIfExists('landlord_payments');
        Schema::dropIfExists('landlord_invoices');
    }
};
