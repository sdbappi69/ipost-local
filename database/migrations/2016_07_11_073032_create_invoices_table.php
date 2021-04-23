<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_unique_id', 32)->unique();
            $table->string('invoice_type', 32);
            $table->integer('order_id')->unsigned()->index()->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->integer('merchant_id')->unsigned()->index()->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('set null');
            $table->decimal('order_amount', 12, 2);
            $table->decimal('delivery_charge', 8, 2);
            $table->decimal('cod_charge', 8, 2);
            $table->decimal('amount_payable', 12, 2);
            $table->string('status', 20);
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
        Schema::drop('invoices');
    }
}
