<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->string('api_reference')->default("");
            $table->string('reference')->default("");
            $table->string('gateway_id')->default("");
            $table->string('status')->default("Pending");
//            $table->string('payment_status')->default("Pending");
//            $table->string('payment_message')->default("Pending Payment");
            $table->string('status_message')->default("Pending Validation");
            $table->string('source_currency');
            $table->string('destination_currency');
            $table->float('source_amount')->default(0);
            $table->float('destination_amount')->default(0);
            $table->integer("user_id")->unsigned()->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer("rate_id")->unsigned()->foreign('rate_id')->references('id')->on('rates')->onDelete('cascade');
            $table->integer("beneficiary_id")->unsigned()->foreign('beneficiary_id')->references('id')->on('beneficiaries')->onDelete('cascade');
            $table->json('properties')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('transactions');
    }
}
