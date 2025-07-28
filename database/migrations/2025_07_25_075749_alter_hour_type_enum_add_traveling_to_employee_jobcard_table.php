<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE employee_jobcard MODIFY hour_type ENUM('normal','overtime','weekend','public_holiday','call_out','traveling') DEFAULT 'normal'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE employee_jobcard MODIFY hour_type ENUM('normal','overtime','weekend','public_holiday','call_out') DEFAULT 'normal'");
    }
};
