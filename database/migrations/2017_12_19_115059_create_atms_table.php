<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAtmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bank_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->double('latitude', 8, 2)->comment('широта');
            $table->double('longitude', 8, 2)->comment('долгота');
            $table->string('address_ru')->nullable();
            $table->string('address_kz')->nullable();
            $table->string('notes_ru')->nullable();
            $table->string('notes_kz')->nullable();
            $table->string('service')->nullable();
            $table->boolean('is_cashin')->default(false);
            $table->boolean('is_multicurrency')->default(false);


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
        Schema::dropIfExists('atms');
    }
}
