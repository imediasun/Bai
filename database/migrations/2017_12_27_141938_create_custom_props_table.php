<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomPropsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_props', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('credit_id')->nullable();
            $table->integer('deposit_id')->nullable();
            $table->integer('auto_credit_id')->nullable();
            $table->integer('credit_card_id')->nullable();
            $table->integer('debit_card_id')->nullable();
            $table->integer('mortgage_id')->nullable();
            $table->integer('loan_id')->nullable();
            $table->integer('bank_id')->nullable();
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
        Schema::dropIfExists('custom_props');
    }
}
