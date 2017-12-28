<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchangers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('entry_id')->default(0);
            $table->double('latitude', 8, 2)->comment('широта');
            $table->double('longitude', 8, 2)->comment('долгота');
            $table->string('address_ru')->nullable();
            $table->string('address_kz')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('site_url')->nullable();
            $table->decimal('rating_client', 3, 2)->default(0.00);
            $table->text('meta_title_ru')->nullable();
            $table->text('meta_title_kz')->nullable();
            $table->text('meta_description_ru')->nullable();
            $table->text('meta_description_kz')->nullable();
            $table->string('alt_name_ru')->nullable();
            $table->string('alt_name_kz')->nullable();
            $table->string('name_ru')->nullable();
            $table->string('name_kz')->nullable();
            $table->string('locative_ru')->nullable();
            $table->string('locative_kz')->nullable();
            $table->string('genitive_ru')->nullable();
            $table->string('genitive_kz')->nullable();
            $table->string('h1_ru')->nullable();
            $table->string('h1_kz')->nullable();
            $table->text('short_description_ru')->nullable();
            $table->text('short_description_kz')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_premium')->default(false);
            $table->string('breadcrumbs_ru')->nullable();
            $table->string('breadcrumbs_kz')->nullable();
            $table->integer('sort_order')->default(10);

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
        Schema::dropIfExists('exchangers');
    }
}
