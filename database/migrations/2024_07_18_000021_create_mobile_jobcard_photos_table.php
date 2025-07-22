<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_jobcard_photos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('jobcard_id')->index();
            $table->string('file_path', 255);
            $table->timestamp('uploaded_at');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->string('caption', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobile_jobcard_photos');
    }
}; 