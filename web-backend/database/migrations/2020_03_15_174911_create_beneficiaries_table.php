<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeneficiariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_type');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('nickname');
            $table->string('othernames')->nullable();
            $table->string('msisdn')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_routing_number')->nullable();
            $table->integer("user_id")->unsigned()->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade');
            $table->integer("country_id")->unsigned()->foreign('country_id')->references('id')
                ->on('countries')->onDelete('cascade');
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
        Schema::dropIfExists('beneficiaries');
    }
}
