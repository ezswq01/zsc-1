<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPublishActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('publish_actions', function (Blueprint $table) {
            $table->foreign('device_id', 'fk_publish_actions_to_devices')
                ->references('id')
                ->on('devices')
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
        Schema::table('publish_actions', function (Blueprint $table) {
            $table->dropForeign('fk_publish_actions_to_devices');
        });
    }
}
