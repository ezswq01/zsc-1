<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSubscribeExpressionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscribe_expressions', function (Blueprint $table) {
            $table->foreign('device_id', 'fk_subscribe_expressions_to_devices')
                ->references('id')
                ->on('devices')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('status_type_id', 'fk_subscribe_expressions_to_status_types')
                ->references('id')
                ->on('status_types')
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
        Schema::table('subscribe_expressions', function (Blueprint $table) {
            $table->dropForeign('fk_subscribe_expressions_to_devices');
            $table->dropForeign('fk_subscribe_expressions_to_status_types');
        });
    }
}
