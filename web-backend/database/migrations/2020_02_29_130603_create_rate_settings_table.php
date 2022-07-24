<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('source_currency',10);
            $table->string('destination_currency',10);
            $table->bigInteger('markup_fixed')->default(0);
            $table->bigInteger('markup_percentage')->default(0);
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
        Schema::dropIfExists('rate_settings');
    }
}
