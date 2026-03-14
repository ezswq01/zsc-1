<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('absent_device_id')->nullable();
            $table->unsignedBigInteger('device_id')->nullable();
            $table->enum('notif_type', ['absent_device', 'dynamic_device']);
            $table->enum('notif_status', ['unread', 'read']);
            $table->string('message');
            $table->foreign('absent_device_id')->references('id')->on('absent_devices')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
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
        Schema::dropIfExists('notifs');
    }
}
