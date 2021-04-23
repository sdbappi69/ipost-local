<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zones', function (Blueprint $table) {
            $table->integer('zone_genre_id')->after('city_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zones', function (Blueprint $table) {
             $table->dropColumn('zone_genre_id');
        });
    }
}
