<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Only add columns if they don't exist
            if (!Schema::hasColumn('invoices', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0)->after('amount');
            }
            
            if (!Schema::hasColumn('invoices', 'outstanding_amount')) {
                $table->decimal('outstanding_amount', 10, 2)->default(0)->after('paid_amount');
            }
            
            if (!Schema::hasColumn('invoices', 'due_date')) {
                $table->date('due_date')->nullable()->after('invoice_date');
            }
        });
        
        // Update status enum to include 'partial' if needed
        $statusColumn = DB::select("SHOW COLUMNS FROM invoices WHERE Field = 'status'")[0] ?? null;
        if ($statusColumn && !str_contains($statusColumn->Type, 'partial')) {
            DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('unpaid', 'partial', 'paid', 'overdue') DEFAULT 'unpaid'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
            if (Schema::hasColumn('invoices', 'outstanding_amount')) {
                $table->dropColumn('outstanding_amount');
            }
            if (Schema::hasColumn('invoices', 'due_date')) {
                $table->dropColumn('due_date');
            }
        });
        
        // Revert status enum
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('unpaid', 'paid') DEFAULT 'unpaid'");
    }
};
