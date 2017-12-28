<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangeRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bank_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('type')->comment('CASH, NO_CASH');
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
        Schema::dropIfExists('exchange_rates');
    }
}
