<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bank_id')->nullable();
            $table->string('meta_title_ru')->nullable();
            $table->string('meta_title_kz')->nullable();
            $table->text('meta_description_ru')->nullable();
            $table->text('meta_description_kz')->nullable();
            $table->string('alt_name_ru')->nullable();
            $table->string('alt_name_kz')->nullable();
            $table->string('breadcrumbs_ru')->nullable();
            $table->string('breadcrumbs_kz')->nullable();
            $table->string('name_ru')->nullable();
            $table->string('name_kz')->nullable();
            $table->string('h1_ru')->nullable();
            $table->string('h1_kz')->nullable();
            $table->text('short_description_ru')->nullable();
            $table->text('short_description_kz')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_kz')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->integer('sort_order')->default(10);
            $table->string('promo')->nullable();
            $table->string('online_url')->nullable();
            $table->integer('changed_by')->nullable();

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
        Schema::dropIfExists('credits');
    }
}
