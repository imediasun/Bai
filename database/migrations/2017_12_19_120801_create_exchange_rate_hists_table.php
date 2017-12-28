<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangeRateHistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_rate_hists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hist_id')->default(0);
            $table->double('usd_buy')->nullable();
            $table->double('usd_sell')->nullable();
            $table->double('eur_buy')->nullable();
            $table->double('eur_sell')->nullable();
            $table->double('rub_buy')->nullable();
            $table->double('rub_sell')->nullable();
            $table->double('gbp_buy')->nullable();
            $table->double('gbp_sell')->nullable();
            $table->double('chf_buy')->nullable();
            $table->double('chf_sell')->nullable();
            $table->double('jpy_buy')->nullable();
            $table->double('jpy_sell')->nullable();
            $table->double('cny_buy')->nullable();
            $table->double('cny_sell')->nullable();
            $table->double('inr_buy')->nullable();
            $table->double('inr_sell')->nullable();
            $table->double('kgs_buy')->nullable();
            $table->double('kgs_sell')->nullable();
            $table->double('try_buy')->nullable();
            $table->double('try_sell')->nullable();

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
        Schema::dropIfExists('exchange_rate_hists');
    }
}
