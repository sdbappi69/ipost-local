<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('merchant_id')->unsigned()->index();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');

            $table->string('store_id', 120);
            $table->string('store_password');
            $table->string('store_url');

            $table->integer('store_type_id')->unsigned()->index();
            $table->foreign('store_type_id')->references('id')->on('store_types')->onDelete('cascade');

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
        Schema::drop('stores');
    }
}
