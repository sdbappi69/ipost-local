<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_histories', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('order_product_id')->unsigned()->index()->nullable();
            $table->foreign('order_product_id')->references('id')->on('order_product')->onDelete('set null');

            $table->string('from_type', 32);
            $table->integer('from_hub_id')->unsigned()->index()->nullable();
            $table->foreign('from_hub_id')->references('id')->on('hubs')->onDelete('set null');
            $table->string('to_type', 32);
            $table->integer('to_hub_id')->unsigned()->index()->nullable();
            $table->foreign('to_hub_id')->references('id')->on('hubs')->onDelete('set null');

            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

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
        Schema::drop('product_histories');
    }
}
