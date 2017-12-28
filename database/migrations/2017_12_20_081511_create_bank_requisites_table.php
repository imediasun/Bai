<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankRequisitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_requisites', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bank_id');
            $table->string('currency')->nullable()->default('KZT');
            $table->string('bik')->nullable();
            $table->string('bin')->nullable();
            $table->string('iik')->nullable();
            $table->string('rnn')->nullable();
            $table->string('correspondent_bank')->nullable();
            $table->string('account_number')->nullable();
            $table->string('swift')->nullable();


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
        Schema::dropIfExists('bank_requisites');
    }
}
