<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToStatusTypeWidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('status_type_widgets', function (Blueprint $table) {
            $table->foreign('setting_id', 'fk_status_type_widgets_to_settings')
                ->references('id')
                ->on('settings')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->foreign('status_type_id', 'fk_status_type_widgets_to_status_types')
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
        Schema::table('status_type_widgets', function (Blueprint $table) {
            $table->dropForeign('fk_status_type_widgets_to_settings');
            $table->dropForeign('fk_status_type_widgets_to_status_types');
        });
    }
}
