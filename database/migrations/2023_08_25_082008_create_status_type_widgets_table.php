<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusTypeWidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_type_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->nullable()->index('fk_status_type_widgets_to_settings');
            $table->foreignId('status_type_id')->nullable()->index('fk_status_type_widgets_to_status_types');
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
        Schema::dropIfExists('status_type_widgets');
    }
}
