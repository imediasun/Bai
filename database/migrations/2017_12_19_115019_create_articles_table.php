<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->text('meta_title_ru')->nullable();
            $table->text('meta_title_kz')->nullable();
            $table->text('meta_description_ru')->nullable();
            $table->text('meta_description_kz')->nullable();
            $table->string('name_ru');
            $table->string('name_kz');
            $table->string('alt_name_ru')->nullable();
            $table->string('alt_name_kz')->nullable();
            $table->string('h1_ru')->nullable();
            $table->string('h1_kz')->nullable();
            $table->text('short_description_ru')->nullable();
            $table->text('short_description_kz')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_kz')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->integer('sort_order')->default(10);
            $table->string('product_type')->nullable();
            $table->integer('product_id')->nullable();

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
        Schema::dropIfExists('articles');
    }
}
