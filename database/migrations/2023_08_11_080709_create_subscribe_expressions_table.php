<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribeExpressionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribe_expressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->nullable()->index('fk_subscribe_expressions_to_devices');
            $table->foreignId('status_type_id')->nullable()->index('fk_subscribe_expressions_to_status_types');
            $table->string('expression');
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
        Schema::dropIfExists('subscribe_expressions');
    }
}
