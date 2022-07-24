<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("type")->default(null);
            $table->decimal('amount');
            $table->decimal('balance_before');
            $table->decimal('balance_after');
            $table->string("description");
            $table->json('properties')->nullable();
            $table->integer("user_id")->unsigned()->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer("payment_id")->unsigned()->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade')->default(0);
            $table->integer("transaction_id")->unsigned()->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade')->default(0);
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
        Schema::dropIfExists('funds');
    }
}
