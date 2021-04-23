<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64);
            $table->string('photo', 120)->nullable();
            $table->string('contact_email', 80)->nullable();
            $table->string('contact_msisdn', 32);
            $table->string('contact_alt_msisdn', 32)->nullable();
            $table->integer('vehicle_type_id')->unsigned()->index();
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onDelete('cascade');
            $table->string('license_no')->nullable();
            $table->string('brand', 80)->nullable();
            $table->string('model', 80)->nullable();
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
        Schema::drop('vehicles');
    }
}
