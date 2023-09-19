<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSubDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_districts', function (Blueprint $table) {
            $table->foreign('regency_id', 'fk_sub_districts_to_regencies')
                ->references('id')
                ->on('regencies')
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
        Schema::table('sub_districts', function (Blueprint $table) {
            $table->dropForeign('fk_sub_districts_to_regencies');
        });
    }
}
