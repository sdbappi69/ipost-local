<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merchant_id')->unsigned()->index()->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('set null');
            $table->integer('product_category_id')->unsigned()->index()->nullable();
            $table->foreign('product_category_id')->references('id')->on('product_categories')->onDelete('set null');
            // $table->integer('city_genre_id')->unsigned()->index()->nullable();
            // $table->foreign('city_genre_id')->references('id')->on('city_genres')->onDelete('set null');
            $table->integer('charge_model_id')->unsigned()->index()->nullable();
            $table->foreign('charge_model_id')->references('id')->on('charge_models')->onDelete('set null');
            $table->float('percentage_range_start')->default('0');
            $table->float('percentage_range_end')->default('0');
            $table->float('percentage_value')->default('0');
            $table->float('additional_range_per_slot')->default('0');
            $table->float('additional_charge_per_slot')->default('0');
            $table->boolean('additional_charge_type')->default('1')->comment('0 = Normal, 1 = Flat');
            $table->float('fixed_charge')->default('0');
            $table->boolean('status')->default('1');
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
        Schema::drop('charges');
    }
}
