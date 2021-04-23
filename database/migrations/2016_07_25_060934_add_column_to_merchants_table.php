<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->boolean('change_type')->default('0')->after('due_date')->comment('0 = Default, 1 = Custom');
            $table->dropColumn(['percentage_of_cod_charge', 'maximum_cod_charge']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn('change_type');
        });
    }
}
