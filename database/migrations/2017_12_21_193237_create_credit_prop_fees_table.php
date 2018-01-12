<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditPropFeesTable_2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('credit_prop_fees');

        Schema::create('credit_prop_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('credit_id');
            $table->integer('credit_prop_id');
            $table->integer('fee_type_id');
            $table->integer('fee_value_id');
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
        Schema::dropIfExists('credit_prop_fees');
    }
}
