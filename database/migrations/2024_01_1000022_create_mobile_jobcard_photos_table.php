<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('mobile_jobcard_photos')) {
            Schema::create('mobile_jobcard_photos', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('jobcard_id');
                $table->string('file_path', 255);
                $table->timestamp('uploaded_at');
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->string('caption', 255)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index('jobcard_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('mobile_jobcard_photos');
    }
}; 