<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFastFilterPropsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fast_filter_props', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fast_filter_id');
            $table->string('product_field');
            $table->string('sign')->default('=');
            $table->string('value');

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
        Schema::dropIfExists('fast_filter_props');
    }
}
