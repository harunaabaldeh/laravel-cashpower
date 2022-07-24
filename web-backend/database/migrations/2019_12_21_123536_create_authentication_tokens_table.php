<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthenticationTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authentication_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->boolean('used')->default(false);
            $table->integer("user_id")->unsigned()->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->json('properties')->nullable();
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
        Schema::dropIfExists('authentication_tokens');
    }
}
