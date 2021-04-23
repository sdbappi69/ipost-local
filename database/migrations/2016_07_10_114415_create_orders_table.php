<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_order_id', 32)->unique();

            $table->integer('store_id')->unsigned()->index();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');

            $table->integer('hub_id')->unsigned()->index()->nullable();
            $table->foreign('hub_id')->references('id')->on('hubs')->onDelete('set null');

            $table->decimal('amount', 12, 2);
            $table->decimal('delivery_payment_amount', 8, 2);
            $table->string('delivery_payment_status', 32);
            $table->decimal('total_amount', 12, 2);

            $table->string('billing_name', 32);
            $table->string('billing_email', 64)->nullable();
            $table->string('billing_msisdn', 32);
            $table->string('billing_alt_msisdn', 32)->nullable();
            $table->string('billing_address1');
            $table->string('billing_address2')->nullable();
            $table->integer('billing_zone_id')->unsigned()->index()->nullable();
            $table->foreign('billing_zone_id')->references('id')->on('zones')->onDelete('set null');
            $table->integer('billing_city_id')->unsigned()->index()->nullable();
            $table->foreign('billing_city_id')->references('id')->on('cities')->onDelete('set null');
            $table->integer('billing_state_id')->unsigned()->index()->nullable();
            $table->foreign('billing_state_id')->references('id')->on('states')->onDelete('set null');
            $table->integer('billing_country_id')->unsigned()->index()->nullable();
            $table->foreign('billing_country_id')->references('id')->on('countries')->onDelete('set null');
            $table->decimal('billing_latitude', 10, 8);
            $table->decimal('billing_longitude', 11, 8);

            $table->string('delivery_name', 32);
            $table->string('delivery_email', 64)->nullable();
            $table->string('delivery_msisdn', 32);
            $table->string('delivery_alt_msisdn', 32)->nullable();
            $table->string('delivery_address1');
            $table->string('delivery_address2')->nullable();
            $table->integer('delivery_zone_id')->unsigned()->index()->nullable();
            $table->foreign('delivery_zone_id')->references('id')->on('zones')->onDelete('set null');
            $table->integer('delivery_city_id')->unsigned()->index()->nullable();
            $table->foreign('delivery_city_id')->references('id')->on('cities')->onDelete('set null');
            $table->integer('delivery_state_id')->unsigned()->index()->nullable();
            $table->foreign('delivery_state_id')->references('id')->on('states')->onDelete('set null');
            $table->integer('delivery_country_id')->unsigned()->index()->nullable();
            $table->foreign('delivery_country_id')->references('id')->on('countries')->onDelete('set null');
            $table->decimal('delivery_latitude', 10, 8);
            $table->decimal('delivery_longitude', 11, 8);

            $table->string('order_status', 32);
            $table->string('payment_type', 32);
            $table->boolean('status')->default(true);
            $table->integer('created_by')->unsigned()->index()->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->nullable();
            $table->integer('updated_by')->unsigned()->index()->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null')->nullable();
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
        Schema::drop('orders');
    }
}
