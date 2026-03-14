<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('status_types', function (Blueprint $table) {
            // Adds the 'category' column right after the 'name' column. 
            // Defaults to 'info' just in case.
            $table->string('category')->default('info')->after('name'); 
        });
    }

    public function down()
    {
        Schema::table('status_types', function (Blueprint $table) {
            // Removes the column if you ever need to rollback
            $table->dropColumn('category');
        });
    }
};