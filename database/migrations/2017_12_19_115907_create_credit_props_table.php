<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditPropsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_props', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('credit_id');

            $table->integer('min_amount')->nullable();
            $table->integer('max_amount')->nullable();

            $table->integer('min_period')->nullable();
            $table->integer('max_period')->nullable();

            $table->double('percent_rate', 8, 2)->nullable();
            $table->string('currency')->nullable();
            $table->boolean('income_confirmation')->nullable();
            $table->string('repayment_structure')->nullable()->comment('схема погашения');
            $table->boolean('gesv')->nullable();


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
        Schema::dropIfExists('credit_props');
    }
}
