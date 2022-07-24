<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('processor');
            $table->string('status')->default("Pending");
            $table->string('status_message')->default("Pending Gateway Response");
            $table->string('source_currency');
            $table->string('destination_currency');
            $table->float('source_amount')->default(0);
            $table->float('destination_amount')->default(0);
            $table->string('reference')->default("");
            $table->string('gateway_id')->default("");
            $table->integer("user_id")->unsigned()->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer("rate_id")->unsigned()->foreign('rate_id')->references('id')->on('rates')->onDelete('cascade');
            $table->json('properties')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
