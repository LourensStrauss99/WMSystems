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
            $table->unsignedBigInteger('amended_by')->nullable()->after('sent_by');
            $table->timestamp('amended_at')->nullable()->after('amended_by');
            
            // Add foreign key constraint
            $table->foreign('amended_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['amended_by']);
            $table->dropColumn(['amended_by', 'amended_at']);
        });
    }
};
