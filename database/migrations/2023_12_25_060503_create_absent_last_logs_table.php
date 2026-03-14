<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsentLastLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absent_last_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('absent_device_id');
            $table->unsignedBigInteger('absent_log_id');
            $table->string('value');
            $table->string('status');
            $table->timestamps();

            $table->foreign('absent_log_id')->references('id')->on('absent_logs')->onDelete('cascade');
            $table->foreign('absent_device_id')->references('id')->on('absent_devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absent_last_logs');
    }
}
