<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_trip_id', 32)->unique();
            $table->integer('vehicle_id')->unsigned()->index()->nullable();
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
            $table->integer('source_hub_id')->unsigned()->index()->nullable();
            $table->foreign('source_hub_id')->references('id')->on('hubs')->onDelete('set null');
            $table->integer('destination_hub_id')->unsigned()->index()->nullable();
            $table->foreign('destination_hub_id')->references('id')->on('hubs')->onDelete('set null');
            $table->integer('responsible_user_id')->unsigned()->index()->nullable();
            $table->foreign('responsible_user_id')->references('id')->on('users')->onDelete('set null');
            $table->integer('alt_responsible_user_id')->unsigned()->index()->nullable();
            $table->foreign('alt_responsible_user_id')->references('id')->on('users')->onDelete('set null');
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
        Schema::drop('trips');
    }
}
