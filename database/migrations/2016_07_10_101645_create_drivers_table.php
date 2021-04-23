<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64);
            $table->string('photo', 120)->nullable();
            $table->string('contact_email', 80)->nullable();
            $table->string('contact_msisdn', 32);
            $table->string('contact_alt_msisdn', 32)->nullable();
            $table->date('date_of_birth');
            $table->string('driving_license_no')->nullable();
            $table->string('reference_name', 64);
            $table->string('reference_email', 80)->nullable();
            $table->string('reference_msisdn', 32);
            $table->string('reference_alt_msisdn', 32)->nullable();
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
        Schema::drop('drivers');
    }
}
