<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLatlongColumnToCamPayloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cam_payloads', function (Blueprint $table) {
            $table->string('latlong')->after('file_name')->nullable()->comment('Latitude and Longitude of the image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cam_payloads', function (Blueprint $table) {
            $table->dropColumn('latlong');
        });
    }
}
