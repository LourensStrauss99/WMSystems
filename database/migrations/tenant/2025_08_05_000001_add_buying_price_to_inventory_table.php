<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->decimal('buying_price', 12, 2)->nullable()->after('nett_price');
        });
    }

    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn('buying_price');
        });
    }
};
