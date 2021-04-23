<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnNameOfChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->integer('zone_genre_id')->unsigned()->nullable()->after('charge_model_id');
            $table->foreign('zone_genre_id')->references('id')->on('zones')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('charges', function (Blueprint $table) {
            //
        });
    }
}
