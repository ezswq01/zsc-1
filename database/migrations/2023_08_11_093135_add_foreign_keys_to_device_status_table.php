<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDeviceStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_status', function (Blueprint $table) {
            $table->foreign('device_id', 'fk_device_status_to_devices')
                ->references('id')
                ->on('devices')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('status_type_id', 'fk_device_status_to_status_types')
                ->references('id')
                ->on('status_types')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('device_log_id', 'fk_device_status_to_device_logs')
                ->references('id')
                ->on('device_logs')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('user_id', 'fk_device_status_to_users')
                ->references('id')
                ->on('users')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_status', function (Blueprint $table) {
            $table->dropForeign('fk_device_status_to_devices');
            $table->dropForeign('fk_device_status_to_status_types');
            $table->dropForeign('fk_device_status_to_device_logs');
            $table->dropForeign('fk_device_status_to_users');
        });
    }
}
