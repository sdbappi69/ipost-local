<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickingTimeSlots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('picking_time_slots', function (Blueprint $table) {
            $table->increments('id');
            $table->string('day', 16);
            $table->time('start_time');
            $table->time('end_time');
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
        Schema::drop('picking_time_slots');
    }
}
