<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickupLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pickup_locations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('merchant_id')->unsigned()->index();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');

            $table->string('title', 120);
            $table->string('email', 64);
            $table->string('photo', 120);
            $table->string('msisdn', 32);
            $table->string('alt_msisdn', 32)->nullable();
            $table->string('address1');
            $table->string('address2')->nullable();

            $table->integer('zone_id')->unsigned()->index();
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade');

            $table->integer('city_id')->unsigned()->index();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');

            $table->integer('state_id')->unsigned()->index();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');

            $table->integer('country_id')->unsigned()->index();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');

            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
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
        Schema::drop('pickup_locations');
    }
}
