<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_suborder_id', 32);
            $table->integer('order_id')->unsigned()->index();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->integer('package_type_id')->unsigned()->index()->nullable();
            $table->foreign('package_type_id')->references('id')->on('package_types')->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->string('qr_image', 120)->nullable();
            $table->string('delivery_image', 120)->nullable();
            $table->tinyInteger('no_of_delivery_attempts')->default(0);
            $table->string('remarks', 120)->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            $table->integer('source_hub_id')->unsigned()->index()->nullable();
            $table->foreign('source_hub_id')->references('id')->on('hubs')->onDelete('set null');
            $table->integer('destination_hub_id')->unsigned()->index()->nullable();
            $table->foreign('destination_hub_id')->references('id')->on('hubs')->onDelete('set null');
            $table->integer('next_hub_id')->unsigned()->index()->nullable();
            $table->foreign('next_hub_id')->references('id')->on('hubs')->onDelete('set null');
            $table->integer('responsible_user_id')->unsigned()->index()->nullable();
            $table->foreign('responsible_user_id')->references('id')->on('users')->onDelete('set null');
            $table->integer('alt_responsible_user_id')->unsigned()->index()->nullable();
            $table->foreign('alt_responsible_user_id')->references('id')->on('users')->onDelete('set null');

            $table->string('current_task_status', 32);
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
        Schema::drop('sub_orders');
    }
}
