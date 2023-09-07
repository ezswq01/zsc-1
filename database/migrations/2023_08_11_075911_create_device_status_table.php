<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_status', function (Blueprint $table) {
            $table->id();
            $table->boolean('marked_as_read')->default(false);
            $table->string('notes')->nullable()->default(null);
            $table->foreignId('device_id')->nullable()->index('fk_device_status_to_devices');
            $table->foreignId('status_type_id')->nullable()->index('fk_device_status_to_status_types');
            $table->foreignId('device_log_id')->nullable()->index('fk_device_status_to_device_logs');
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
        Schema::dropIfExists('device_status');
    }
}
