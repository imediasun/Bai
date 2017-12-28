<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFastFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fast_filters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_type')->comment('credit, deposit, auto_credit, credit_card, debit_card, loan, mortgage');
            $table->string('name_ru')->null();
            $table->string('name_kz')->null();
            $table->string('alt_name_ru')->null();
            $table->string('alt_name_kz')->null();

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
        Schema::dropIfExists('fast_filters');
    }
}
