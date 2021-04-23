<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 80);
            $table->string('photo', 120);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('msisdn', 32);
            $table->string('alt_msisdn', 32)->nullable();
            $table->integer('user_type_id')->unsigned()->index();
            $table->foreign('user_type_id')->references('id')->on('user_types')->onDelete('cascade');
            $table->integer('reference_id')->unsigned()->index()->nullable();
            $table->integer('country_id')->unsigned()->index();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->integer('state_id')->unsigned()->index();
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->integer('city_id')->unsigned()->index();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->integer('zone_id')->unsigned()->index();
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade');
            $table->string('address1', 120);
            $table->string('address2', 120)->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->boolean('status')->default(true);
            $table->integer('created_by')->unsigned()->index()->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->integer('updated_by')->unsigned()->index()->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
