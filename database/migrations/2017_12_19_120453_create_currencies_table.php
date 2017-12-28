<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_common')->default(0);
            $table->integer('amount');
            $table->string('name_ru');
            $table->string('name_kz');
            $table->string('locative_ru')->nullable();
            $table->string('locative_kz')->nullable();
            $table->string('genitive_ru')->nullable();
            $table->string('genitive_kz')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_kz')->nullable();
            $table->string('code');
            $table->integer('number_code');
            $table->double('rate');
            $table->double('prev_rate');
            $table->string('date');
            $table->string('symbol');

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
        Schema::dropIfExists('currencies');
    }
}
