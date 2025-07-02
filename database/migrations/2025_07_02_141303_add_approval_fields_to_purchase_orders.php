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
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'submitted_for_approval_at')) {
                $table->timestamp('submitted_for_approval_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'submitted_by')) {
                $table->unsignedBigInteger('submitted_by')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'sent_at')) {
                $table->timestamp('sent_at')->nullable();
            }
            if (!Schema::hasColumn('purchase_orders', 'sent_by')) {
                $table->unsignedBigInteger('sent_by')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $columnsToCheck = [
                'submitted_for_approval_at',
                'submitted_by',
                'approved_at',
                'approved_by',
                'rejected_at',
                'rejected_by',
                'sent_at',
                'sent_by',
            ];

            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('purchase_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
