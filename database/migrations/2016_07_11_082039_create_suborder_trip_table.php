<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuborderTripTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suborder_trip', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sub_order_id')->unsigned()->index();
            $table->foreign('sub_order_id')->references('id')->on('sub_orders')->onDelete('cascade');
            $table->integer('trip_id')->unsigned()->index();
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->string('remarks', 120)->nullable();
            $table->boolean('status')->default(true);
            $table->integer('created_by')->unsigned()->index()->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->integer('updated_by')->unsigned()->index()->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
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
        Schema::drop('suborder_trip');
    }
}
