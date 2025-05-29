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
        Schema::table('jobcards', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_id')->nullable()->after('id');
            $table->integer('quantity')->default(1)->after('inventory_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobcards', function (Blueprint $table) {
            $table->dropColumn(['inventory_id', 'quantity']);
        });
    }
};
