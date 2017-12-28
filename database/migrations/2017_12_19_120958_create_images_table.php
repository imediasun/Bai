<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->string('product_type');
            $table->string('type')->comment('PHOTO, LOGO');
            $table->string('name')->nullable();
            $table->string('path')->nullable();
            $table->string('alt_ru')->nullable();
            $table->string('alt_kz')->nullable();
            $table->string('title_ru')->nullable();
            $table->string('title_kz')->nullable();
            $table->string('url')->nullable();


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
        Schema::dropIfExists('images');
    }
}
