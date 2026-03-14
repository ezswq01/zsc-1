<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDeviceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_logs', function (Blueprint $table) {
            $table->foreign('device_id', 'fk_device_logs_to_devices')
                ->references('id')
                ->on('devices')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            // $table->foreign('user_id', 'fk_device_logs_to_users')
            //     ->references('id')
            //     ->on('users')
            //     ->onUpdate('CASCADE')
            //     ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_logs', function (Blueprint $table) {
            $table->dropForeign('fk_device_logs_to_devices');
            // $table->dropForeign('fk_device_logs_to_users');
        });
    }
}
