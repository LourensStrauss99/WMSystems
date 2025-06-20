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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_reference')->after('id');
            $table->unsignedBigInteger('client_id')->after('payment_reference');
            $table->string('invoice_jobcard_number')->nullable()->after('client_id');
            $table->decimal('amount', 10, 2)->after('invoice_jobcard_number');
            $table->enum('payment_method', ['cash', 'card', 'eft', 'cheque', 'payfast', 'other'])->after('amount');
            $table->date('payment_date')->after('payment_method');
            $table->string('reference_number')->nullable()->after('payment_date');
            $table->text('notes')->nullable()->after('reference_number');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed')->after('notes');
            $table->string('receipt_number')->unique()->after('status');
            
            // Add foreign key and indexes
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->index(['client_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropIndex(['client_id', 'payment_date']);
            $table->dropColumn([
                'payment_reference',
                'client_id', 
                'invoice_jobcard_number',
                'amount',
                'payment_method',
                'payment_date',
                'reference_number',
                'notes',
                'status',
                'receipt_number'
            ]);
        });
    }
};
