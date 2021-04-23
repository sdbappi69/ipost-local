<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_category_id')->unsigned()->index()->nullable();
            $table->foreign('product_category_id')->references('id')->on('product_categories')->onDelete('set null');
            $table->integer('order_id')->unsigned()->index()->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->integer('sub_order_id')->unsigned()->index()->nullable();
            $table->foreign('sub_order_id')->references('id')->on('sub_orders')->onDelete('set null');
            $table->integer('pickup_location_id')->unsigned()->index()->nullable();
            $table->foreign('pickup_location_id')->references('id')->on('pickup_locations')->onDelete('set null');

            $table->date('picking_date');
            $table->integer('picking_time_slot_id')->unsigned()->index()->nullable();
            $table->foreign('picking_time_slot_id')->references('id')->on('picking_time_slots')->onDelete('set null');
            $table->tinyInteger('picking_attempts')->default(0);
            $table->string('picking_remarks', 120)->nullable();

            $table->string('title', 120);
            $table->string('image', 120)->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->smallInteger('quantity');
            $table->decimal('sub_total', 14, 2);
            $table->string('width', 20);
            $table->string('height', 20);
            $table->string('length', 20);
            $table->string('url')->nullable();

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
        Schema::drop('order_product');
    }
}
