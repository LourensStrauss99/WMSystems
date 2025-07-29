<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('jobcards', function (Blueprint $table) {
            $table->boolean('is_quote')->default(false)->after('id');
            $table->timestamp('quote_accepted_at')->nullable()->after('is_quote');
            $table->unsignedBigInteger('accepted_by')->nullable()->after('quote_accepted_at');
            $table->text('accepted_signature')->nullable()->after('accepted_by');
        });
    }

    public function down(): void
    {
        Schema::table('jobcards', function (Blueprint $table) {
            $table->dropColumn(['is_quote', 'quote_accepted_at', 'accepted_by', 'accepted_signature']);
        });
    }
}; 